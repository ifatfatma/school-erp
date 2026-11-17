<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\IdCardTemplate;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IdCardController extends Controller
{
    protected function getSchoolId(Request $request): int
    {
        return $this->resolveSchoolId($request) ?? (int) (School::first()?->id ?? 1);
    }

    protected function normalizeImageUrl(?string $raw): ?string
    {
        if (!$raw || $raw === '[]' || $raw === 'null' || $raw === '{}') {
            return null;
        }

        $raw = trim($raw);
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/(storage/.*)$#', $raw, $matches)) {
            return '/' . $matches[3];
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:')) {
            return $raw;
        }

        return '/storage/' . ltrim($raw, '/');
    }

    /**
     * Get or initialize ID Card Template for the active school
     */
    public function getTemplate(Request $request)
    {
        $schoolId = $this->getSchoolId($request);
        $school = School::find($schoolId) ?? School::first();

        if (!$school) {
            return response()->json(['success' => false, 'message' => 'School not found.'], 404);
        }

        $template = IdCardTemplate::withoutGlobalScopes()->where('school_id', $schoolId)->first();

        // Default school logo
        $schoolLogo = $this->normalizeImageUrl($school->logo);

        // Current active session
        $currentSession = AcademicSession::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->first()?->name ?? '2026 - 2027';

        if (!$template) {
            $contactInfo = '';
            if ($school->phone) {
                $contactInfo .= 'Ph: ' . $school->phone;
            }
            if ($school->email) {
                $contactInfo .= ($contactInfo ? ' | ' : '') . $school->email;
            }

            $template = IdCardTemplate::create([
                'school_id' => $schoolId,
                'name' => 'Standard Student ID Card',
                'layout' => 'vertical',
                'theme' => 'modern',
                'primary_color' => '#1d4ed8', // Vibrant modern blue
                'accent_color' => '#f59e0b',  // Warm amber gold
                'bg_color' => '#ffffff',
                'header_title' => $school->name,
                'header_subtitle' => 'STUDENT IDENTITY CARD',
                'contact_info' => $contactInfo ?: 'Ph: +91 90079 63069 | info@school.edu',
                'academic_session' => $currentSession,
                'logo_url' => $schoolLogo,
                'signature_url' => null,
                'signatory_title' => 'Principal',
                'photo_shape' => 'rounded',
                'barcode_type' => 'qr',
                'show_roll_no' => true,
                'show_admission_no' => true,
                'show_class_section' => true,
                'show_blood_group' => true,
                'show_dob' => true,
                'show_father_name' => true,
                'show_emergency_phone' => true,
                'show_address' => false,
                'show_back_side' => true,
                'back_instructions' => "1. This Identity Card is non-transferable and must be displayed while on school premises.\n2. In case of loss or mutilation, immediately inform the school administrative office.\n3. If found, kindly return to the Principal's Office or nearest police station.",
            ]);
        } else {
            // Keep logo up-to-date if template has none
            if (!$template->logo_url && $schoolLogo) {
                $template->logo_url = $schoolLogo;
            }
        }

        return response()->json([
            'success' => true,
            'template' => $template,
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code,
                'logo' => $schoolLogo,
                'phone' => $school->phone,
                'email' => $school->email,
                'address' => $school->address,
                'city' => $school->city,
                'state' => $school->state,
                'postal_code' => $school->postal_code,
            ],
            'current_session' => $currentSession,
        ]);
    }

    /**
     * Save / Update ID Card Template
     */
    public function saveTemplate(Request $request)
    {
        $schoolId = $this->getSchoolId($request);
        $school = School::find($schoolId);

        if (!$school) {
            return response()->json(['success' => false, 'message' => 'School not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:150',
            'layout' => 'required|in:vertical,horizontal',
            'theme' => 'required|in:modern,classic,minimal,tech',
            'primary_color' => 'required|string|max:30',
            'accent_color' => 'required|string|max:30',
            'bg_color' => 'required|string|max:30',
            'header_title' => 'nullable|string|max:255',
            'header_subtitle' => 'nullable|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'academic_session' => 'nullable|string|max:100',
            'logo_url' => 'nullable|string',
            'signature_url' => 'nullable|string',
            'signatory_title' => 'nullable|string|max:100',
            'photo_shape' => 'required|in:rounded,circle,square',
            'barcode_type' => 'required|in:qr,barcode,none',
            'show_roll_no' => 'boolean',
            'show_admission_no' => 'boolean',
            'show_class_section' => 'boolean',
            'show_blood_group' => 'boolean',
            'show_dob' => 'boolean',
            'show_father_name' => 'boolean',
            'show_emergency_phone' => 'boolean',
            'show_address' => 'boolean',
            'show_back_side' => 'boolean',
            'back_instructions' => 'nullable|string',
            'custom_css' => 'nullable|string',
        ]);

        $template = IdCardTemplate::withoutGlobalScopes()->where('school_id', $schoolId)->first();

        if ($template) {
            $template->update($validated);
        } else {
            $validated['school_id'] = $schoolId;
            $template = IdCardTemplate::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'ID Card Template design saved successfully!',
            'template' => $template,
        ]);
    }

    /**
     * Upload Principal / Authority Signature Image
     */
    public function uploadSignature(Request $request)
    {
        $schoolId = $this->getSchoolId($request);

        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
        ]);

        $file = $request->file('signature');
        $filename = 'sig_' . $schoolId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('id_cards/signatures', $filename, 'public');
        $url = '/storage/' . $path;

        $template = IdCardTemplate::withoutGlobalScopes()->where('school_id', $schoolId)->first();
        if ($template) {
            $template->signature_url = $url;
            $template->save();
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => 'Principal signature uploaded successfully!',
        ]);
    }

    /**
     * Upload Custom School Logo for ID Cards
     */
    public function uploadLogo(Request $request)
    {
        $schoolId = $this->getSchoolId($request);

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
        ]);

        $file = $request->file('logo');
        $filename = 'logo_' . $schoolId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('id_cards/logos', $filename, 'public');
        $url = '/storage/' . $path;

        $template = IdCardTemplate::withoutGlobalScopes()->where('school_id', $schoolId)->first();
        if ($template) {
            $template->logo_url = $url;
            $template->save();
        }

        return response()->json([
            'success' => true,
            'url' => $url,
            'message' => 'School emblem/logo uploaded successfully!',
        ]);
    }

    /**
     * Upload photo for a specific student directly from ID Card Generator
     */
    public function uploadStudentPhoto(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request);
        $student = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('photo');

        // Delete old photo if on local public storage
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $path = $file->store('students', 'public');
        $student->photo = $path;
        $student->save();

        $photoUrl = $this->normalizeImageUrl($path);

        return response()->json([
            'success' => true,
            'message' => "Photo updated for {$student->full_name}!",
            'photo_url' => $photoUrl,
            'student_id' => $student->id,
        ]);
    }

    /**
     * Get students formatted for ID card generation and batch printing
     */
    public function getStudents(Request $request)
    {
        $schoolId = $this->getSchoolId($request);
        $classId = $request->query('class_id');
        $sectionId = $request->query('section_id');
        $search = $request->query('search');

        $query = Student::withoutGlobalScopes()
            ->with([
                'class:id,name',
                'section:id,name',
                'academicSession:id,name',
                'school:id,name,code,logo,address,city,phone',
            ])
            ->where('school_id', $schoolId)
            ->where('status', 1);

        if ($classId && is_numeric($classId)) {
            $query->where('class_id', $classId);
        }

        if ($sectionId && is_numeric($sectionId)) {
            $query->where('section_id', $sectionId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('admission_no', 'like', "%{$search}%")
                    ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class_id')
            ->orderBy('section_id')
            ->orderByRaw('CAST(roll_no AS UNSIGNED) ASC')
            ->orderBy('first_name')
            ->get();

        // Format student list with normalized photos and scan data
        $formatted = $students->map(function ($s) {
            $photo = null;
            if ($s->photo) {
                $photo = $this->normalizeImageUrl($s->photo);
            }

            $dobFormatted = $s->dob ? date('d M Y', strtotime($s->dob)) : null;
            $fatherName = $s->father_name ?: ($s->guardian_name ?: null);
            $emergencyPhone = $s->father_mobile ?: ($s->guardian_mobile ?: ($s->mobile ?: null));

            return [
                'id' => $s->id,
                'admission_no' => $s->admission_no,
                'roll_no' => $s->roll_no ?: '-',
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'full_name' => $s->full_name,
                'gender' => $s->gender,
                'dob' => $dobFormatted,
                'blood_group' => $s->blood_group ?: 'O+',
                'class_name' => $s->class?->name ?? 'Class',
                'section_name' => $s->section?->name ?? 'A',
                'class_id' => $s->class_id,
                'section_id' => $s->section_id,
                'father_name' => $fatherName,
                'emergency_phone' => $emergencyPhone,
                'mobile' => $s->mobile,
                'address' => $s->address,
                'photo_url' => $photo ?: '/images/no-image.png',
                // Scanning barcode payload: Standard format combining school code + admission number
                'scan_code' => $s->admission_no,
                'barcode_text' => $s->admission_no,
            ];
        });

        // Classes and Sections for dropdown filters
        $classes = SchoolClass::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        $sections = Section::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->select('id', 'name', 'class_id')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'students' => $formatted,
            'total' => $formatted->count(),
            'classes' => $classes,
            'sections' => $sections,
        ]);
    }
}
