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

        // Non-Super Admins can only view their own school
        if (!$isSuperAdmin) {
            if (!$user || !$user->school_id) {
                return response()->json(['data' => []]);
            }
            $query->where('id', $user->school_id);
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
        if (!app(\App\Services\TenantManager::class)->isSuperAdmin($request->user())) {
            abort(403, 'Unauthorized. Only Super Admin can create schools.');
        }

        $data = $request->validated();

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

        if (isset($data['status'])) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
        }

        $school = School::create($data);

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

        return new SchoolResource($school);
    }

    public function show(School $school)
    {
        return new SchoolResource($school);
    }

    public function update(SchoolRequest $request, School $school)
    {
        $user = $request->user();
        $isSuperAdmin = app(\App\Services\TenantManager::class)->isSuperAdmin($user);

        if (!$isSuperAdmin && (int) ($user?->school_id) !== (int) $school->id) {
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
        $schoolId = $request->user()?->school_id;
        $school = $schoolId ? School::find($schoolId) : School::first();

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
