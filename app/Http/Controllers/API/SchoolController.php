<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use App\Http\Requests\SchoolRequest;
use App\Http\Resources\SchoolResource;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isSuperAdmin = app(\App\Services\TenantManager::class)->isSuperAdmin($user);

        $query = School::query();

        // Super Admin sees all schools; other users only see their assigned schools
        if (!$isSuperAdmin) {
            if (!$user) {
                return response()->json(['data' => []]);
            }
            $assignedIds = $user->schools()->pluck('schools.id')->push($user->school_id)->filter()->unique()->toArray();
            if (empty($assignedIds)) {
                return response()->json(['data' => []]);
            }
            $query->whereIn('id', $assignedIds);
        }

        // Optional search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $schools = $query->latest('id')->paginate($request->get('per_page', 15));
        return SchoolResource::collection($schools);
    }

    public function store(SchoolRequest $request)
    {
        $user = $request->user();
        $tenantManager = app(\App\Services\TenantManager::class);
        $isSuperAdmin = $tenantManager->isSuperAdmin($user);

        if (!$isSuperAdmin) {
            if (!$user || !$user->canManageMultipleSchools() || !$user->canAddMoreSchools()) {
                return response()->json([
                    'success' => false,
                    'message' => 'School limit reached for your current subscription plan. Please contact Super Admin to upgrade.',
                ], 403);
            }
        }

        $data = $request->validated();

        // Non-super admins inherit their subscription plan on newly created schools
        if (!$isSuperAdmin && $user) {
            $userSub = $user->subscription;
            $data['subscription_plan'] = $userSub?->plan?->slug ?? strtolower($user->school?->subscription_plan ?? 'starter');
            $data['subscription_status'] = 'active';
        }

        if ($request->hasFile('logo') || ($request->file('logo') instanceof \Illuminate\Http\UploadedFile)) {
            $file = $request->file('logo');
            $data['logo'] = $file->store('schools', 'public');
        } elseif (isset($data['logo'])) {
            if ($data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['logo'] = $data['logo']->store('schools', 'public');
            } elseif (is_array($data['logo'])) {
                $values = array_values($data['logo']);
                $first = $values[0] ?? null;
                $data['logo'] = is_string($first) ? trim($first) : null;
            } elseif (is_string($data['logo'])) {
                $trimmed = trim($data['logo']);
                $data['logo'] = ($trimmed === '' || $trimmed === '[]' || $trimmed === 'null' || $trimmed === '{}') ? null : $trimmed;
            } else {
                $data['logo'] = null;
            }
        }

        if ($request->hasFile('login_background') || ($request->file('login_background') instanceof \Illuminate\Http\UploadedFile)) {
            $file = $request->file('login_background');
            $data['login_background'] = $file->store('schools/backgrounds', 'public');
        } elseif (isset($data['login_background'])) {
            if ($data['login_background'] instanceof \Illuminate\Http\UploadedFile) {
                $data['login_background'] = $data['login_background']->store('schools/backgrounds', 'public');
            } elseif (is_string($data['login_background'])) {
                $trimmedBg = trim($data['login_background']);
                $data['login_background'] = ($trimmedBg === '' || $trimmedBg === '[]' || $trimmedBg === 'null' || $trimmedBg === '{}') ? null : $trimmedBg;
            }
        }

        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $data['status'] = true;
        }

        if (!isset($data['subscription_plan'])) {
            $data['subscription_plan'] = 'starter';
        }
        if (!isset($data['subscription_status'])) {
            $data['subscription_status'] = 'active';
        }

        $school = School::create($data);

        // Associate this school with the creating user in school_user
        if ($user) {
            $user->schools()->syncWithoutDetaching([
                $school->id => ['role' => 'admin', 'is_primary' => empty($user->school_id)]
            ]);

            if (empty($user->school_id)) {
                $user->update(['school_id' => $school->id]);
            }
        }

        // Auto-create current academic session for the newly registered school
        \App\Models\AcademicSession::create([
            'school_id' => $school->id,
            'name' => '2027-2028',
            'start_date' => '2027-04-01',
            'end_date' => '2028-03-31',
            'is_current' => true,
            'status' => true,
        ]);

        // Sync with WebsiteSettings if active or primary
        $this->syncWebsiteSettings($school);

        return (new SchoolResource($school))->additional([
            'success' => true,
            'message' => "School '{$school->name}' created and assigned to your account successfully."
        ]);
    }

    public function show(School $school, Request $request)
    {
        $user = $request->user();
        if ($user && !app(\App\Services\TenantManager::class)->isSuperAdmin($user) && !$user->canAccessSchool($school->id)) {
            abort(403, 'Unauthorized. You cannot view another school.');
        }
        return new SchoolResource($school);
    }

    public function update(SchoolRequest $request, School $school)
    {
        $user = $request->user();
        $isSuperAdmin = app(\App\Services\TenantManager::class)->isSuperAdmin($user);

        if (!$isSuperAdmin && !$user->canAccessSchool($school->id)) {
            abort(403, 'Unauthorized. You cannot modify another school.');
        }

        $data = $request->validated();

        // Non-Super Admins cannot change school code, status, or subscription plan
        if (!$isSuperAdmin) {
            unset($data['code'], $data['status'], $data['subscription_plan'], $data['subscription_status']);
        }

        if ($request->hasFile('logo') || ($request->file('logo') instanceof \Illuminate\Http\UploadedFile)) {
            if (is_string($school->logo) && !empty($school->logo) && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $file = $request->file('logo');
            $data['logo'] = $file->store('schools', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if (is_string($school->logo) && !empty($school->logo) && Storage::disk('public')->exists($school->logo)) {
                Storage::disk('public')->delete($school->logo);
            }
            $data['logo'] = null;
        } elseif (isset($data['logo'])) {
            if ($data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                $data['logo'] = $data['logo']->store('schools', 'public');
            } elseif (is_array($data['logo'])) {
                $values = array_values($data['logo']);
                $first = $values[0] ?? null;
                $data['logo'] = is_string($first) ? trim($first) : null;
            } elseif (is_string($data['logo'])) {
                $trimmed = trim($data['logo']);
                $data['logo'] = ($trimmed === '' || $trimmed === '[]' || $trimmed === 'null' || $trimmed === '{}') ? null : $trimmed;
            } else {
                $data['logo'] = null;
            }
        }

        if ($request->hasFile('login_background') || ($request->file('login_background') instanceof \Illuminate\Http\UploadedFile)) {
            if (is_string($school->login_background) && !empty($school->login_background) && Storage::disk('public')->exists($school->login_background)) {
                Storage::disk('public')->delete($school->login_background);
            }
            $file = $request->file('login_background');
            $data['login_background'] = $file->store('schools/backgrounds', 'public');
        } elseif ($request->boolean('remove_login_background')) {
            if (is_string($school->login_background) && !empty($school->login_background) && Storage::disk('public')->exists($school->login_background)) {
                Storage::disk('public')->delete($school->login_background);
            }
            $data['login_background'] = null;
        } elseif (isset($data['login_background'])) {
            if ($data['login_background'] instanceof \Illuminate\Http\UploadedFile) {
                $data['login_background'] = $data['login_background']->store('schools/backgrounds', 'public');
            } elseif (is_string($data['login_background'])) {
                $trimmedBg = trim($data['login_background']);
                $data['login_background'] = ($trimmedBg === '' || $trimmedBg === '[]' || $trimmedBg === 'null' || $trimmedBg === '{}') ? null : $trimmedBg;
            }
        }

        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $school->update($data);

        // Sync with WebsiteSettings if active or primary
        $this->syncWebsiteSettings($school);

        return new SchoolResource($school);
    }

    public function destroy(School $school)
    {
        if (!app(\App\Services\TenantManager::class)->isSuperAdmin(auth()->user())) {
            abort(403, 'Unauthorized. Only Super Admin can delete schools.');
        }

        $school->delete();
        return response()->json(['message' => 'School deleted successfully']);
    }

    // Optional: restore soft-deleted
    public function restore($id)
    {
        if (!app(\App\Services\TenantManager::class)->isSuperAdmin(auth()->user())) {
            abort(403, 'Unauthorized. Only Super Admin can restore schools.');
        }

        $school = School::withTrashed()->findOrFail($id);
        $school->restore();
        return new SchoolResource($school);
    }

    public function current(Request $request)
    {
        $tenantManager = app(\App\Services\TenantManager::class);
        $school = $tenantManager->getTenant();

        if (!$school) {
            $schoolId = $request->user()?->school_id;
            $school = $schoolId ? School::find($schoolId) : School::first();
        }

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'No school found.'
            ], 404);
        }

        return new SchoolResource($school);
    }

    protected function syncWebsiteSettings(School $school): void
    {
        try {
            $logoUrl = null;
            $logoVal = $school->logo;
            $raw = null;
            if (is_string($logoVal)) {
                $raw = trim($logoVal);
            } elseif (is_array($logoVal)) {
                $vals = array_values($logoVal);
                $first = $vals[0] ?? null;
                $raw = is_string($first) ? trim($first) : null;
            }
            if ($raw && $raw !== '[]' && $raw !== 'null' && $raw !== '{}') {
                $logoUrl = str_starts_with($raw, 'http')
                    ? $raw
                    : asset('storage/' . ltrim($raw, '/'));
            }

            $setting = WebsiteSetting::withoutGlobalScopes()->firstOrNew([
                'school_id'   => $school->id,
                'section_key' => 'general',
            ]);
            $content = is_array($setting->content) ? $setting->content : [];
            $content['school_name'] = $school->name;
            if ($logoUrl) {
                $content['logo'] = $logoUrl;
            }
            if ($school->email) {
                $content['email'] = $school->email;
            }
            if ($school->phone) {
                $content['phone'] = $school->phone;
            }
            if ($school->address) {
                $content['address'] = $school->address;
            }

            $setting->content = $content;
            $setting->is_active = true;
            $setting->save();
        } catch (\Throwable $e) {
            \Log::warning('Failed syncing website settings with school: ' . $e->getMessage());
        }
    }
}
