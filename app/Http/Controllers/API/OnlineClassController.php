<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\OnlineClass;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OnlineClassController extends Controller
{
    /**
     * Display a listing of online classes with filters.
     */
    public function index(Request $request)
    {
        $query = OnlineClass::with([
            'class:id,name',
            'section:id,name',
            'subject:id,name,code',
            'teacher:id,first_name,last_name,email',
            'academicSession:id,name'
        ]);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Scope filter: today, upcoming, past
        if ($request->filled('scope')) {
            $today = Carbon::today()->toDateString();
            if ($request->scope === 'today') {
                $query->whereDate('class_date', $today);
            } elseif ($request->scope === 'upcoming') {
                $query->whereDate('class_date', '>=', $today)
                      ->where('status', '!=', 'completed');
            } elseif ($request->scope === 'past') {
                $query->where(function ($q) use ($today) {
                    $q->whereDate('class_date', '<', $today)
                      ->orWhere('status', 'completed');
                });
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $sortBy = $request->get('sort_by', 'class_date');
        $sortOrder = $request->get('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder)->orderBy('start_time', 'asc');

        if ($request->boolean('all')) {
            return response()->json([
                'success' => true,
                'data' => $query->get(),
            ]);
        }

        $perPage = (int) $request->get('per_page', 15);
        $classes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $classes->items(),
            'pagination' => [
                'total' => $classes->total(),
                'per_page' => $classes->perPage(),
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created online class.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|in:google_meet,zoom,teams,jitsi,other',
            'meeting_link' => 'required|url|max:1000',
            'meeting_id' => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:100',
            'class_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration_minutes' => 'nullable|integer',
            'timezone' => 'nullable|string|max:60',
            'status' => 'nullable|in:scheduled,live,completed,cancelled',
            'join_instructions' => 'nullable|string',
            'recorded_link' => 'nullable|url|max:1000',
        ]);

        // Auto calculate duration if missing
        if (empty($validated['duration_minutes']) && !empty($validated['start_time']) && !empty($validated['end_time'])) {
            try {
                $start = Carbon::parse($validated['start_time']);
                $end = Carbon::parse($validated['end_time']);
                $validated['duration_minutes'] = abs($end->diffInMinutes($start));
            } catch (\Throwable $e) {
                $validated['duration_minutes'] = 45;
            }
        }

        // Auto resolve school_id from selected class, active tenant, or current user
        if (empty($validated['school_id'])) {
            $class = SchoolClass::withoutGlobalScopes()->find($validated['class_id']);
            if ($class && $class->school_id) {
                $validated['school_id'] = $class->school_id;
            } elseif ($activeTenantId = app(\App\Services\TenantManager::class)->getTenantId()) {
                $validated['school_id'] = $activeTenantId;
            } elseif (Auth::user() && Auth::user()->school_id) {
                $validated['school_id'] = Auth::user()->school_id;
            } else {
                $validated['school_id'] = \App\Models\School::first()?->id ?? 1;
            }
        }

        // Auto resolve active academic session
        $activeSession = AcademicSession::where('is_current', true)->first();
        if ($activeSession) {
            $validated['academic_session_id'] = $activeSession->id;
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'scheduled';

        $onlineClass = OnlineClass::create($validated);
        $onlineClass->load(['class', 'section', 'subject', 'teacher']);

        return response()->json([
            'success' => true,
            'message' => 'Online class scheduled successfully!',
            'data' => $onlineClass,
        ], 201);
    }

    /**
     * Display the specified online class.
     */
    public function show($id)
    {
        $onlineClass = OnlineClass::with([
            'class:id,name',
            'section:id,name',
            'subject:id,name,code',
            'teacher:id,first_name,last_name,email,mobile',
            'academicSession:id,name',
            'creator:id,name,email'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $onlineClass,
        ]);
    }

    /**
     * Update the specified online class.
     */
    public function update(Request $request, $id)
    {
        $onlineClass = OnlineClass::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'sometimes|required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'sometimes|required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'sometimes|required|in:google_meet,zoom,teams,jitsi,other',
            'meeting_link' => 'sometimes|required|url|max:1000',
            'meeting_id' => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:100',
            'class_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required',
            'duration_minutes' => 'nullable|integer',
            'timezone' => 'nullable|string|max:60',
            'status' => 'nullable|in:scheduled,live,completed,cancelled',
            'join_instructions' => 'nullable|string',
            'recorded_link' => 'nullable|url|max:1000',
        ]);

        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            try {
                $start = Carbon::parse($validated['start_time']);
                $end = Carbon::parse($validated['end_time']);
                $validated['duration_minutes'] = abs($end->diffInMinutes($start));
            } catch (\Throwable $e) {}
        }

        $onlineClass->update($validated);
        $onlineClass->load(['class', 'section', 'subject', 'teacher']);

        return response()->json([
            'success' => true,
            'message' => 'Online class updated successfully.',
            'data' => $onlineClass,
        ]);
    }

    /**
     * Remove the specified online class.
     */
    public function destroy($id)
    {
        $onlineClass = OnlineClass::findOrFail($id);
        $onlineClass->delete();

        return response()->json([
            'success' => true,
            'message' => 'Online class deleted successfully.',
        ]);
    }

    /**
     * Quick status update (e.g. Mark Live, Mark Completed).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,live,completed,cancelled',
            'recorded_link' => 'nullable|url|max:1000',
        ]);

        $onlineClass = OnlineClass::findOrFail($id);
        $onlineClass->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class status updated to ' . ucfirst($validated['status']),
            'data' => $onlineClass,
        ]);
    }

    /**
     * Generate a free instant Jitsi Meet room link.
     * This allows teachers to start video classes with 1 click without requiring a Zoom or Google account.
     */
    public function generateRoom(Request $request)
    {
        $user = Auth::user();
        $schoolSlug = $user->school ? Str::slug($user->school->name) : 'school';

        if ($request->filled('class_id')) {
            $class = SchoolClass::withoutGlobalScopes()->with('school')->find($request->class_id);
            if ($class && $class->school) {
                $schoolSlug = Str::slug($class->school->name);
            }
        }

        $classTitle = $request->filled('title') ? Str::slug($request->title) : 'class';
        $uniqueToken = Str::random(8);

        $roomName = "{$schoolSlug}-{$classTitle}-{$uniqueToken}";
        $meetingLink = "https://meet.jit.si/{$roomName}";

        return response()->json([
            'success' => true,
            'platform' => 'jitsi',
            'meeting_link' => $meetingLink,
            'room_name' => $roomName,
            'message' => 'Instant free meeting room generated!',
        ]);
    }
}
