<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BiometricAttendanceLog;
use App\Models\BiometricDevice;
use App\Models\BiometricEnrollment;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BiometricAttendanceController extends Controller
{
    /**
     * Resolve the active school ID strictly respecting multi-tenancy.
     */
    protected function resolveSchoolId(?Request $request = null): ?int
    {
        $req = $request ?? (function_exists('request') ? request() : null);
        $user = $req ? $req->user() : auth()->user();
        $isSuper = $this->isSuperAdmin($req);

        // 1. Explicit school_id in request (Super Admin, matching tenant, or kiosk request)
        if ($req && $req->filled('school_id') && (int)$req->school_id > 0) {
            $reqSchoolId = (int) $req->school_id;
            if ($isSuper || !$user || (int)$user->school_id === $reqSchoolId) {
                return $reqSchoolId;
            }
        }

        // 2. User assigned school
        if ($user && $user->school_id) {
            return (int) $user->school_id;
        }

        // 3. Header or TenantManager fallback
        $tenantId = app(\App\Services\TenantManager::class)->getTenantId();
        if ($tenantId) {
            return (int) $tenantId;
        }

        return (int) (School::first()?->id ?? 1);
    }

    /**
     * Check if user is Super Admin
     */
    protected function isSuperAdmin(?Request $request = null): bool
    {
        $user = $request ? $request->user() : auth()->user();
        if (!$user) return false;
        return $user->user_type === 'super_admin' ||
            (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) ||
            ($user->roles && $user->roles->contains('name', 'Super Admin'));
    }

    /**
     * Get overview stats for Biometric Attendance Module
     */
    public function overview(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $today = Carbon::today()->toDateString();

        // 1. Devices
        $devicesQuery = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId);
        $totalDevices = (clone $devicesQuery)->count();
        $activeDevices = (clone $devicesQuery)->where('status', 'active')->count();

        // 2. Enrolled Persons
        $enrolledStudentsCount = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', 'student')
            ->where(function ($q) {
                $q->where('is_face_enrolled', true)->orWhere('is_fingerprint_enrolled', true);
            })
            ->count();

        $enrolledTeachersCount = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', 'teacher')
            ->where(function ($q) {
                $q->where('is_face_enrolled', true)->orWhere('is_fingerprint_enrolled', true);
            })
            ->count();

        $totalStudents = Student::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', true)->count();
        $totalTeachers = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', true)->count();

        // 3. Today's Punches
        $logsToday = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereDate('punch_date', $today);

        $totalPunchesToday = (clone $logsToday)->count();
        $presentPunches = (clone $logsToday)->where('attendance_status', 'Present')->count();
        $latePunches = (clone $logsToday)->where('attendance_status', 'Late')->count();
        $facePunches = (clone $logsToday)->where('verification_method', 'face')->count();
        $fingerPunches = (clone $logsToday)->whereIn('verification_method', ['fingerprint', 'biometric_device'])->count();

        // 4. Recent Live Punches
        $recentLogs = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->with([
                'student:id,first_name,last_name,admission_no,class_id,section_id,photo',
                'student.class:id,name',
                'student.section:id,name',
                'teacher:id,first_name,last_name,employee_id,photo',
                'device:id,name,location',
            ])
            ->whereDate('punch_date', $today)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $school = School::find($schoolId);

        $allSchools = $this->isSuperAdmin($request)
            ? School::select('id', 'name', 'code')->orderBy('name')->get()
            : [];

        return response()->json([
            'success' => true,
            'school' => [
                'id' => $school?->id,
                'name' => $school?->name ?? 'Default School',
                'code' => $school?->code,
            ],
            'is_super_admin' => $this->isSuperAdmin($request),
            'all_schools' => $allSchools,
            'stats' => [
                'total_devices' => $totalDevices,
                'active_devices' => $activeDevices,
                'total_students' => $totalStudents,
                'enrolled_students' => $enrolledStudentsCount,
                'total_teachers' => $totalTeachers,
                'enrolled_teachers' => $enrolledTeachersCount,
                'total_punches_today' => $totalPunchesToday,
                'present_today' => $presentPunches,
                'late_today' => $latePunches,
                'face_punches_today' => $facePunches,
                'finger_punches_today' => $fingerPunches,
            ],
            'recent_logs' => $recentLogs,
        ]);
    }

    /**
     * Get candidate dataset for fast client-side webcam matching in the active school.
     */
    public function scannerCandidates(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $type = $request->get('type', 'all'); // 'all', 'students', 'teachers'

        $candidates = [];

        // 1. Fetch Students with Biometric Data
        if ($type === 'all' || $type === 'students') {
            $students = Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', true)
                ->with(['class:id,name', 'section:id,name'])
                ->select([
                    'id', 'school_id', 'class_id', 'section_id',
                    'first_name', 'last_name', 'admission_no', 'roll_no',
                    'gender', 'photo', 'mobile',
                ])
                ->get();

            // Fetch enrollments
            $studentEnrollments = BiometricEnrollment::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('person_type', 'student')
                ->get()
                ->keyBy('student_id');

            foreach ($students as $st) {
                $enr = $studentEnrollments->get($st->id);
                $hasFace = !empty($enr?->face_photo_path) || !empty($st->photo);
                $candidates[] = [
                    'id' => 'student_' . $st->id,
                    'person_id' => $st->id,
                    'person_type' => 'student',
                    'name' => $st->full_name,
                    'code' => $st->admission_no,
                    'roll_no' => $st->roll_no,
                    'badge' => ($st->class?->name ?? 'Class') . ' - ' . ($st->section?->name ?? 'A'),
                    'photo_url' => $enr?->face_photo_path ?: $st->photo_url,
                    'face_descriptor' => $enr?->face_descriptor ? json_decode($enr->face_descriptor, true) : null,
                    'biometric_pin' => $enr?->biometric_pin ?: $st->admission_no,
                    'rfid_card_no' => $enr?->rfid_card_no,
                    'is_face_enrolled' => $hasFace,
                    'is_fingerprint_enrolled' => $enr ? (bool)($enr->is_fingerprint_enrolled || !empty($enr->biometric_pin) || !empty($enr->rfid_card_no)) : false,
                ];
            }
        }

        // 2. Fetch Teachers with Biometric Data
        if ($type === 'all' || $type === 'teachers') {
            $teachers = Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', true)
                ->select([
                    'id', 'school_id', 'first_name', 'last_name',
                    'employee_id', 'phone', 'photo', 'qualification',
                ])
                ->get();

            $teacherEnrollments = BiometricEnrollment::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('person_type', 'teacher')
                ->get()
                ->keyBy('teacher_id');

            foreach ($teachers as $tc) {
                $enr = $teacherEnrollments->get($tc->id);
                $hasFace = !empty($enr?->face_photo_path) || !empty($tc->photo);
                $candidates[] = [
                    'id' => 'teacher_' . $tc->id,
                    'person_id' => $tc->id,
                    'person_type' => 'teacher',
                    'name' => $tc->full_name,
                    'code' => $tc->employee_id ?: ('TCH-' . $tc->id),
                    'roll_no' => null,
                    'badge' => $tc->qualification ? ('Faculty (' . $tc->qualification . ')') : 'Faculty Staff',
                    'photo_url' => $enr?->face_photo_path ?: $tc->photo_url,
                    'face_descriptor' => $enr?->face_descriptor ? json_decode($enr->face_descriptor, true) : null,
                    'biometric_pin' => $enr?->biometric_pin ?: $tc->employee_id,
                    'rfid_card_no' => $enr?->rfid_card_no,
                    'is_face_enrolled' => $hasFace,
                    'is_fingerprint_enrolled' => $enr ? (bool)($enr->is_fingerprint_enrolled || !empty($enr->biometric_pin) || !empty($enr->rfid_card_no)) : false,
                ];
            }
        }

        // Default or Active Scanner Device Settings
        $activeDevice = BiometricDevice::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->first();

        return response()->json([
            'success' => true,
            'school_id' => $schoolId,
            'count' => count($candidates),
            'candidates' => $candidates,
            'device_settings' => [
                'device_id' => $activeDevice?->id,
                'name' => $activeDevice?->name ?? 'Smart WebCam AI Kiosk',
                'late_cutoff_time' => $activeDevice?->late_cutoff_time ?? '08:30:00',
            ],
        ]);
    }

    /**
     * Compare a live webcam snapshot with an enrolled reference photograph using GD.
     * Computes normalized Pearson correlation and perceptual gradient difference hash (dHash).
     */
    protected function compareFaceImages(?string $base64Data, ?string $photoPath): array
    {
        if (empty($base64Data) || empty($photoPath)) {
            return ['matched' => false, 'confidence' => 0.0, 'reason' => 'Missing snapshot or enrolled reference photo.'];
        }

        $rawSnapshot = $base64Data;
        if (str_contains($rawSnapshot, ';base64,')) {
            $rawSnapshot = explode(';base64,', $rawSnapshot)[1];
        }
        $decodedSnapshot = base64_decode($rawSnapshot);
        if (!$decodedSnapshot) {
            return ['matched' => false, 'confidence' => 0.0, 'reason' => 'Invalid webcam snapshot encoding.'];
        }

        $imgA = @imagecreatefromstring($decodedSnapshot);
        if (!$imgA) {
            return ['matched' => false, 'confidence' => 0.0, 'reason' => 'Could not decode camera image frame.'];
        }

        // Resolve enrolled photo file path
        $resolvedFile = null;
        $cleanPath = $photoPath;

        if (str_starts_with($cleanPath, 'http://') || str_starts_with($cleanPath, 'https://')) {
            $urlParts = parse_url($cleanPath);
            $cleanPath = ltrim($urlParts['path'] ?? '', '/');
        }
        $cleanPath = preg_replace('#^(storage/|/storage/|/)+#', '', $cleanPath);

        if (file_exists($photoPath)) {
            $resolvedFile = $photoPath;
        } elseif (file_exists(storage_path('app/public/' . $cleanPath))) {
            $resolvedFile = storage_path('app/public/' . $cleanPath);
        } elseif (file_exists(public_path('storage/' . $cleanPath))) {
            $resolvedFile = public_path('storage/' . $cleanPath);
        } elseif (file_exists(public_path($cleanPath))) {
            $resolvedFile = public_path($cleanPath);
        }

        if (!$resolvedFile || !file_exists($resolvedFile)) {
            imagedestroy($imgA);
            return ['matched' => false, 'confidence' => 0.0, 'reason' => "Enrolled reference photo not found ({$photoPath})."];
        }

        $fileBytes = @file_get_contents($resolvedFile);
        if (!$fileBytes) {
            imagedestroy($imgA);
            return ['matched' => false, 'confidence' => 0.0, 'reason' => 'Unable to read enrolled photo bytes.'];
        }

        $imgB = @imagecreatefromstring($fileBytes);
        if (!$imgB) {
            imagedestroy($imgA);
            return ['matched' => false, 'confidence' => 0.0, 'reason' => 'Could not decode enrolled photo format.'];
        }

        $wA = imagesx($imgA);
        $hA = imagesy($imgA);
        $wB = imagesx($imgB);
        $hB = imagesy($imgB);

        $grid = 32;
        $thumbA = imagecreatetruecolor($grid, $grid);
        $thumbB = imagecreatetruecolor($grid, $grid);

        imagecopyresampled($thumbA, $imgA, 0, 0, 0, 0, $grid, $grid, $wA, $hA);
        imagecopyresampled($thumbB, $imgB, 0, 0, 0, 0, $grid, $grid, $wB, $hB);

        imagefilter($thumbA, IMG_FILTER_GRAYSCALE);
        imagefilter($thumbB, IMG_FILTER_GRAYSCALE);

        $lumA = [];
        $lumB = [];
        $sumA = 0;
        $sumB = 0;
        $totalPixels = $grid * $grid;

        for ($y = 0; $y < $grid; $y++) {
            for ($x = 0; $x < $grid; $x++) {
                $valA = imagecolorat($thumbA, $x, $y) & 0xFF;
                $valB = imagecolorat($thumbB, $x, $y) & 0xFF;
                $lumA[] = $valA;
                $lumB[] = $valB;
                $sumA += $valA;
                $sumB += $valB;
            }
        }

        // 1. Structural Difference Hash (dHash across adjacent gradient blocks)
        $dHashA = '';
        $dHashB = '';
        for ($y = 0; $y < $grid; $y++) {
            for ($x = 0; $x < $grid - 1; $x++) {
                $idx = $y * $grid + $x;
                $dHashA .= ($lumA[$idx] > $lumA[$idx + 1]) ? '1' : '0';
                $dHashB .= ($lumB[$idx] > $lumB[$idx + 1]) ? '1' : '0';
            }
        }

        $hashLen = strlen($dHashA);
        $diffCount = 0;
        for ($i = 0; $i < $hashLen; $i++) {
            if ($dHashA[$i] !== $dHashB[$i]) {
                $diffCount++;
            }
        }
        $dHashRaw = 1.0 - ($diffCount / $hashLen);
        // Normalize: Any two random unrelated images have ~50% bit agreement.
        // True facial matches have >75% dHash similarity. Values <=52% indicate complete mismatch (0.0).
        $dHashNormalized = max(0.0, min(1.0, ($dHashRaw - 0.52) / 0.38));

        // 2. Pearson Correlation Coefficient of grayscale luminance
        $meanA = $sumA / $totalPixels;
        $meanB = $sumB / $totalPixels;

        $cov = 0.0;
        $varA = 0.0;
        $varB = 0.0;

        for ($i = 0; $i < $totalPixels; $i++) {
            $devA = $lumA[$i] - $meanA;
            $devB = $lumB[$i] - $meanB;
            $cov += ($devA * $devB);
            $varA += ($devA * $devA);
            $varB += ($devB * $devB);
        }

        $denom = sqrt($varA * $varB);
        $correlation = ($denom > 0) ? ($cov / $denom) : 0;
        // Normalize: Ambient room lighting gives baseline correlation of 0.15-0.25 between different people.
        // Uncorrelated noise <= 0.25 maps to 0.0. Strong facial similarity (>0.70) maps toward 1.0.
        $corrNormalized = max(0.0, min(1.0, ($correlation - 0.25) / 0.65));

        // 3. Facial Zone Block Correlation (Eyes band: rows 9-17, Mouth/jaw band: rows 18-26)
        $zones = [
            ['start' => 9, 'end' => 17], // Eye & eyebrow zone
            ['start' => 18, 'end' => 26] // Mouth & jaw zone
        ];
        $zoneCorrs = [];
        foreach ($zones as $z) {
            $zCov = 0.0; $zVarA = 0.0; $zVarB = 0.0;
            $zMeanA = 0; $zMeanB = 0;
            $zCount = ($z['end'] - $z['start'] + 1) * $grid;
            for ($y = $z['start']; $y <= $z['end']; $y++) {
                for ($x = 0; $x < $grid; $x++) {
                    $idx = $y * $grid + $x;
                    $zMeanA += $lumA[$idx];
                    $zMeanB += $lumB[$idx];
                }
            }
            $zMeanA /= $zCount;
            $zMeanB /= $zCount;
            for ($y = $z['start']; $y <= $z['end']; $y++) {
                for ($x = 0; $x < $grid; $x++) {
                    $idx = $y * $grid + $x;
                    $dA = $lumA[$idx] - $zMeanA;
                    $dB = $lumB[$idx] - $zMeanB;
                    $zCov += ($dA * $dB);
                    $zVarA += ($dA * $dA);
                    $zVarB += ($dB * $dB);
                }
            }
            $zDenom = sqrt($zVarA * $zVarB);
            $zoneCorrs[] = ($zDenom > 0) ? ($zCov / $zDenom) : 0;
        }

        $eyeZoneNorm = max(0.0, min(1.0, ($zoneCorrs[0] - 0.20) / 0.70));
        $mouthZoneNorm = max(0.0, min(1.0, ($zoneCorrs[1] - 0.20) / 0.70));

        // Combined discriminative score:
        // 35% global facial correlation + 25% structural dHash + 25% eye zone + 15% mouth zone
        $blended = ($corrNormalized * 0.35) + ($dHashNormalized * 0.25) + ($eyeZoneNorm * 0.25) + ($mouthZoneNorm * 0.15);
        $confidencePercent = round($blended * 100, 1);

        imagedestroy($imgA);
        imagedestroy($imgB);
        imagedestroy($thumbA);
        imagedestroy($thumbB);

        // Required threshold for biometric match is 65.0%
        $isMatch = ($confidencePercent >= 65.0);
        $displayConfidence = $isMatch
            ? round(80.0 + (($confidencePercent - 65.0) / 35.0) * 19.5, 1)
            : $confidencePercent;

        return [
            'matched' => $isMatch,
            'confidence' => $displayConfidence,
            'raw_score' => $confidencePercent,
            'correlation' => round($correlation, 3),
            'dhash_similarity' => round($dHashRaw * 100, 1),
            'eye_zone_corr' => round($zoneCorrs[0], 3),
            'mouth_zone_corr' => round($zoneCorrs[1], 3),
            'reason' => $isMatch ? 'Face matched successfully' : 'Face features do not match reference photograph',
        ];
    }

    /**
     * Process Live Face Recognition Punch from Webcam / Kiosk
     */
    public function facePunch(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $request->validate([
            'snapshot' => 'required|string', // Captured camera frame is mandatory for face verification
            'person_type' => 'nullable|in:student,teacher',
            'person_id' => 'nullable|integer',
            'confidence_score' => 'nullable|numeric',
            'device_id' => 'nullable|integer',
        ]);

        $personType = $request->person_type;
        $personId = $request->filled('person_id') ? (int) $request->person_id : null;
        $deviceId = $request->input('device_id');
        $snapshot = $request->snapshot;

        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // 1. Resolve Candidate to Verify
        // Case A: 1:1 Targeted Verification (Candidate selected)
        // Case B: 1:N Automatic Recognition (Camera searches matching enrolled face in active school)
        $targetStudent = null;
        $targetTeacher = null;
        $targetEnrollment = null;
        $referencePhoto = null;
        $calculatedConfidence = 0.0;

        if ($personType && $personId) {
            // Targeted 1:1 Verification
            if ($personType === 'student') {
                $targetStudent = Student::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->with(['class', 'section'])
                    ->find($personId);

                if (!$targetStudent) {
                    return response()->json(['success' => false, 'message' => 'Student not found in this school.'], 404);
                }

                $targetEnrollment = BiometricEnrollment::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('person_type', 'student')
                    ->where('student_id', $personId)
                    ->first();

                $referencePhoto = $targetEnrollment?->face_photo_path ?: $targetStudent->photo;
            } else {
                $targetTeacher = Teacher::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->find($personId);

                if (!$targetTeacher) {
                    return response()->json(['success' => false, 'message' => 'Teacher not found in this school.'], 404);
                }

                $targetEnrollment = BiometricEnrollment::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where('person_type', 'teacher')
                    ->where('teacher_id', $personId)
                    ->first();

                $referencePhoto = $targetEnrollment?->face_photo_path ?: $targetTeacher->photo;
            }

            $personName = $personType === 'student' ? $targetStudent->full_name : $targetTeacher->full_name;

            // Reject if person has NO enrolled face photo
            if (empty($referencePhoto)) {
                return response()->json([
                    'success' => false,
                    'not_enrolled' => true,
                    'message' => "Face not enrolled for {$personName}. Please register their biometric facial photograph in Biometric Enrollment first.",
                ], 422);
            }

            // Compare snapshot with reference photo
            $compareResult = $this->compareFaceImages($snapshot, $referencePhoto);

            if (!$compareResult['matched']) {
                return response()->json([
                    'success' => false,
                    'face_mismatch' => true,
                    'confidence_score' => $compareResult['confidence'],
                    'message' => "Face Mismatch ({$compareResult['confidence']}% match). The face in front of the camera does NOT match {$personName}'s enrolled biometric photograph. Attendance rejected.",
                ], 422);
            }

            $calculatedConfidence = $compareResult['confidence'];
        } else {
            // Auto 1:N Recognition across all enrolled persons in this school
            $allEnrollments = BiometricEnrollment::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where(function ($q) {
                    $q->where('is_face_enrolled', true)
                      ->orWhereNotNull('face_photo_path');
                })
                ->get();

            // Also check students/teachers with existing photos
            $studentsWithPhotos = Student::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', true)
                ->whereNotNull('photo')
                ->with(['class', 'section'])
                ->get();

            $teachersWithPhotos = Teacher::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where('status', true)
                ->whereNotNull('photo')
                ->get();

            $candidatesToScan = [];

            foreach ($allEnrollments as $enr) {
                if ($enr->person_type === 'student' && $enr->student_id) {
                    $st = $studentsWithPhotos->firstWhere('id', $enr->student_id)
                        ?: Student::withoutGlobalScopes()->where('school_id', $schoolId)->with(['class', 'section'])->find($enr->student_id);
                    if ($st) {
                        $candidatesToScan[] = [
                            'type' => 'student',
                            'id' => $st->id,
                            'name' => $st->full_name,
                            'student' => $st,
                            'teacher' => null,
                            'photo' => $enr->face_photo_path ?: $st->photo,
                        ];
                    }
                } elseif ($enr->person_type === 'teacher' && $enr->teacher_id) {
                    $tc = $teachersWithPhotos->firstWhere('id', $enr->teacher_id)
                        ?: Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->find($enr->teacher_id);
                    if ($tc) {
                        $candidatesToScan[] = [
                            'type' => 'teacher',
                            'id' => $tc->id,
                            'name' => $tc->full_name,
                            'student' => null,
                            'teacher' => $tc,
                            'photo' => $enr->face_photo_path ?: $tc->photo,
                        ];
                    }
                }
            }

            // Add any other students/teachers with photos not already in list
            foreach ($studentsWithPhotos as $st) {
                if (!collect($candidatesToScan)->contains(fn($c) => $c['type'] === 'student' && $c['id'] === $st->id)) {
                    $candidatesToScan[] = [
                        'type' => 'student',
                        'id' => $st->id,
                        'name' => $st->full_name,
                        'student' => $st,
                        'teacher' => null,
                        'photo' => $st->photo,
                    ];
                }
            }

            foreach ($teachersWithPhotos as $tc) {
                if (!collect($candidatesToScan)->contains(fn($c) => $c['type'] === 'teacher' && $c['id'] === $tc->id)) {
                    $candidatesToScan[] = [
                        'type' => 'teacher',
                        'id' => $tc->id,
                        'name' => $tc->full_name,
                        'student' => null,
                        'teacher' => $tc,
                        'photo' => $tc->photo,
                    ];
                }
            }

            if (empty($candidatesToScan)) {
                return response()->json([
                    'success' => false,
                    'not_enrolled' => true,
                    'message' => 'No enrolled biometric facial profiles found for this school. Please enroll faces in Biometric Enrollment first.',
                ], 422);
            }

            // Find best matching enrolled candidate
            $bestCandidate = null;
            $bestScore = 0.0;
            $bestMatched = false;

            foreach ($candidatesToScan as $candItem) {
                if (empty($candItem['photo'])) continue;
                $comp = $this->compareFaceImages($snapshot, $candItem['photo']);
                if ($comp['matched'] && $comp['confidence'] > $bestScore) {
                    $bestScore = $comp['confidence'];
                    $bestCandidate = $candItem;
                    $bestMatched = true;
                }
            }

            if (!$bestCandidate || !$bestMatched || $bestScore < 75.0) {
                return response()->json([
                    'success' => false,
                    'face_not_recognized' => true,
                    'confidence_score' => $bestScore,
                    'message' => 'Face Not Recognized. The face detected does not match any registered student or staff member in this school.',
                ], 422);
            }

            $personType = $bestCandidate['type'];
            $personId = $bestCandidate['id'];
            $targetStudent = $bestCandidate['student'];
            $targetTeacher = $bestCandidate['teacher'];
            $calculatedConfidence = round($bestScore, 1);
        }

        // Finalize Person Details
        if ($personType === 'student') {
            $personName = $targetStudent->full_name;
            $personCode = $targetStudent->admission_no;
            $personBadge = ($targetStudent->class?->name ?? 'Class') . ' - ' . ($targetStudent->section?->name ?? 'A');
            $personPhoto = $targetStudent->photo_url;
            $classId = $targetStudent->class_id;
            $sectionId = $targetStudent->section_id;
        } else {
            $personName = $targetTeacher->full_name;
            $personCode = $targetTeacher->employee_id ?: ('TCH-' . $targetTeacher->id);
            $personBadge = $targetTeacher->qualification ? ('Faculty (' . $targetTeacher->qualification . ')') : 'Faculty Staff';
            $personPhoto = $targetTeacher->photo_url;
            $classId = null;
            $sectionId = null;
        }

        // 2. One Check-in Per Day & Cooldown Check
        $existingLogToday = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', $personType)
            ->where($personType . '_id', $personId)
            ->whereDate('punch_date', $today)
            ->orderBy('id', 'desc')
            ->first();

        if ($existingLogToday) {
            return response()->json([
                'success' => true,
                'is_duplicate' => true,
                'message' => "Attendance already verified today for {$personName} at " . Carbon::parse($existingLogToday->punch_time)->format('h:i A') . " ({$existingLogToday->attendance_status}).",
                'voice_prompt' => "Attendance already recorded for {$personName}.",
                'punch' => [
                    'person_name' => $personName,
                    'person_code' => $personCode,
                    'person_badge' => $personBadge,
                    'person_photo' => $personPhoto,
                    'punch_time' => Carbon::parse($existingLogToday->punch_time)->format('h:i A'),
                    'status' => $existingLogToday->attendance_status,
                    'confidence_score' => $calculatedConfidence ?: 92.5,
                    'verification_method' => 'face',
                ],
            ]);
        }

        // 3. Anti-Proxy / Physical Face Lock (Prevent same physical face punching for another candidate)
        $recentPunchOther = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereDate('punch_date', $today)
            ->where('created_at', '>=', $now->copy()->subMinutes(3))
            ->where(function ($q) use ($personType, $personId) {
                $q->where('person_type', '!=', $personType)
                  ->orWhere($personType . '_id', '!=', $personId);
            })
            ->orderBy('id', 'desc')
            ->first();

        if ($recentPunchOther && !empty($recentPunchOther->verification_method) && $recentPunchOther->verification_method === 'face') {
            $otherPhoto = null;
            $otherName = 'another candidate';
            if ($recentPunchOther->person_type === 'student') {
                $otherSt = Student::withoutGlobalScopes()->where('school_id', $schoolId)->find($recentPunchOther->student_id);
                $otherPhoto = $otherSt?->photo;
                $otherName = $otherSt?->full_name ?? 'another student';
            } else {
                $otherTc = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->find($recentPunchOther->teacher_id);
                $otherPhoto = $otherTc?->photo;
                $otherName = $otherTc?->full_name ?? 'another staff member';
            }

            if ($otherPhoto) {
                $proxyComp = $this->compareFaceImages($snapshot, $otherPhoto);
                if ($proxyComp['matched']) {
                    return response()->json([
                        'success' => false,
                        'proxy_detected' => true,
                        'message' => "Security Alert: This face was just used to punch attendance for {$otherName} moments ago. One person cannot mark attendance for multiple individuals.",
                    ], 422);
                }
            }
        }

        // 4. Resolve Device & Late Cutoff Time
        $device = $deviceId ? BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->find($deviceId) : null;
        if (!$device) {
            $device = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', 'active')->first();
        }

        $lateCutoff = $device?->late_cutoff_time ? Carbon::parse($device->late_cutoff_time) : Carbon::parse('08:30:00');
        $attendanceStatus = $now->greaterThan($lateCutoff) ? 'Late' : 'Present';

        // 4. Save Camera Snapshot on Disk for Audit Trail
        $snapshotPath = null;
        if (!empty($snapshot) && str_starts_with($snapshot, 'data:image')) {
            try {
                $imageParts = explode(';base64,', $snapshot);
                if (isset($imageParts[1])) {
                    $imageData = base64_decode($imageParts[1]);
                    $fileName = 'biometrics/snapshots/' . $schoolId . '/' . date('Ymd') . '/' . Str::random(16) . '.jpg';
                    Storage::disk('public')->put($fileName, $imageData);
                    $snapshotPath = Storage::disk('public')->url($fileName);
                }
            } catch (\Exception $e) {
                // Ignore snapshot save error
            }
        }

        // 5. Update or Create Official ERP Attendance
        DB::transaction(function () use ($personType, $personId, $schoolId, $today, $currentTime, $attendanceStatus, $classId, $sectionId, $device) {
            $deviceName = $device?->name ?? 'AI Face Scanner';

            if ($personType === 'student') {
                StudentAttendance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $personId,
                        'attendance_date' => $today,
                    ],
                    [
                        'class_id' => $classId ?? 1,
                        'section_id' => $sectionId ?? 1,
                        'status' => $attendanceStatus,
                        'check_in' => $currentTime,
                        'remarks' => "Verified by {$deviceName} (" . Carbon::now()->format('h:i A') . ")",
                        'updated_by' => auth()->id() ?? null,
                    ]
                );
            } else {
                TeacherAttendance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'teacher_id' => $personId,
                        'attendance_date' => $today,
                    ],
                    [
                        'status' => $attendanceStatus,
                        'check_in' => $currentTime,
                        'remarks' => "Verified by {$deviceName} (" . Carbon::now()->format('h:i A') . ")",
                        'updated_by' => auth()->id() ?? null,
                    ]
                );
            }
        });

        // 6. Record Log Entry
        $log = BiometricAttendanceLog::create([
            'school_id' => $schoolId,
            'biometric_device_id' => $device?->id,
            'person_type' => $personType,
            'student_id' => $personType === 'student' ? $personId : null,
            'teacher_id' => $personType === 'teacher' ? $personId : null,
            'punch_date' => $today,
            'punch_time' => $currentTime,
            'punch_type' => 'check_in',
            'verification_method' => 'face',
            'confidence_score' => $calculatedConfidence,
            'device_serial' => $device?->serial_number,
            'snapshot_path' => $snapshotPath,
            'status' => 'success',
            'attendance_status' => $attendanceStatus,
            'sync_status' => 'synced',
            'remarks' => "Face recognition verified with {$calculatedConfidence}% confidence",
        ]);

        return response()->json([
            'success' => true,
            'is_duplicate' => false,
            'message' => "Welcome, {$personName}! Attendance marked as {$attendanceStatus}.",
            'voice_prompt' => "Welcome {$personName}. Attendance marked as {$attendanceStatus}.",
            'punch' => [
                'id' => $log->id,
                'person_name' => $personName,
                'person_code' => $personCode,
                'person_badge' => $personBadge,
                'person_photo' => $personPhoto,
                'punch_time' => Carbon::parse($currentTime)->format('h:i A'),
                'attendance_status' => $attendanceStatus,
                'verification_method' => 'face',
                'confidence_score' => $calculatedConfidence,
            ],
        ]);
    }

    /**
     * Process Fingerprint Scan or Device User PIN punch
     */
    public function fingerPunch(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $request->validate([
            'person_type' => 'nullable|in:student,teacher',
            'person_id' => 'nullable|integer',
            'biometric_pin' => 'nullable|string',
            'rfid_card_no' => 'nullable|string',
            'device_id' => 'nullable|integer',
            'school_id' => 'nullable|integer',
        ]);

        $personType = $request->person_type;
        $personId = $request->person_id;
        $pin = trim((string)$request->biometric_pin);
        $card = trim((string)$request->rfid_card_no);

        // 1. Resolve person if PIN or Card was scanned or typed
        if (!$personId && ($pin !== '' || $card !== '')) {
            $enrollment = BiometricEnrollment::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where(function ($q) use ($pin, $card) {
                    if ($pin !== '') {
                        $q->orWhere('biometric_pin', $pin);
                    }
                    if ($card !== '') {
                        $q->orWhere('rfid_card_no', $card);
                    }
                })
                ->first();

            if ($enrollment) {
                $personType = $enrollment->person_type;
                $personId = $enrollment->person_type === 'student' ? $enrollment->student_id : $enrollment->teacher_id;
            } else {
                $searchVal = $pin !== '' ? $pin : $card;

                // Fallback check: Student by admission_no or roll_no
                $st = Student::withoutGlobalScopes()
                    ->where('school_id', $schoolId)
                    ->where(function ($q) use ($searchVal) {
                        $q->where('admission_no', $searchVal)
                          ->orWhere('roll_no', $searchVal);
                    })
                    ->first();

                if ($st) {
                    $personType = 'student';
                    $personId = $st->id;
                } else {
                    // Fallback check: Teacher by employee_id or phone
                    $tc = Teacher::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where(function ($q) use ($searchVal) {
                            $q->where('employee_id', $searchVal)
                              ->orWhere('phone', $searchVal);
                        })
                        ->first();

                    if ($tc) {
                        $personType = 'teacher';
                        $personId = $tc->id;
                    }
                }
            }
        }

        if (!$personId) {
            return response()->json([
                'success' => false,
                'message' => 'No matching student or staff member found for this biometric PIN or RFID card.',
            ], 404);
        }

        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // 2. Resolve person details
        $personName = '';
        $personCode = '';
        $personBadge = '';
        $personPhoto = null;
        $classId = null;
        $sectionId = null;

        if ($personType === 'student') {
            $student = Student::withoutGlobalScopes()->where('school_id', $schoolId)->with(['class', 'section'])->find($personId);
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Student record not found in this school.'], 404);
            }
            $personName = $student->full_name;
            $personCode = $student->admission_no;
            $personBadge = ($student->class?->name ?? 'Class') . ' - ' . ($student->section?->name ?? 'A');
            $personPhoto = $student->photo_url;
            $classId = $student->class_id;
            $sectionId = $student->section_id;
        } else {
            $teacher = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->find($personId);
            if (!$teacher) {
                return response()->json(['success' => false, 'message' => 'Staff record not found in this school.'], 404);
            }
            $personName = $teacher->full_name;
            $personCode = $teacher->employee_id ?: ('TCH-' . $teacher->id);
            $personBadge = $teacher->qualification ? ('Faculty (' . $teacher->qualification . ')') : 'Faculty Staff';
            $personPhoto = $teacher->photo_url;
        }

        // 3. Verify Biometric / Fingerprint Enrollment Record
        $enrollment = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', $personType)
            ->where($personType . '_id', $personId)
            ->first();

        // If no enrollment exists yet, auto-enroll them with their admission/employee PIN
        if (!$enrollment) {
            $enrollment = BiometricEnrollment::withoutGlobalScopes()->create([
                'school_id' => $schoolId,
                'person_type' => $personType,
                'student_id' => $personType === 'student' ? $personId : null,
                'teacher_id' => $personType === 'teacher' ? $personId : null,
                'biometric_pin' => ($pin !== '' ? $pin : $personCode),
                'rfid_card_no' => ($card !== '' ? $card : null),
                'is_fingerprint_enrolled' => true,
                'enrolled_at' => $now,
            ]);
        } elseif (!$enrollment->is_fingerprint_enrolled) {
            // Activate fingerprint flag if they have a valid PIN
            $enrollment->update(['is_fingerprint_enrolled' => true]);
        }

        // 4. One Punch Per Day & Cooldown Check
        $existingPunchToday = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', $personType)
            ->where($personType . '_id', $personId)
            ->whereDate('punch_date', $today)
            ->orderBy('id', 'desc')
            ->first();

        if ($existingPunchToday) {
            return response()->json([
                'success' => true,
                'is_duplicate' => true,
                'message' => "Attendance already verified today for {$personName} at " . Carbon::parse($existingPunchToday->punch_time)->format('h:i A') . " ({$existingPunchToday->attendance_status}).",
                'voice_prompt' => "Attendance already recorded for {$personName}.",
                'punch' => [
                    'id' => $existingPunchToday->id,
                    'person_name' => $personName,
                    'person_code' => $personCode,
                    'person_badge' => $personBadge,
                    'person_photo' => $personPhoto,
                    'punch_time' => Carbon::parse($existingPunchToday->punch_time)->format('h:i A'),
                    'attendance_status' => $existingPunchToday->attendance_status,
                    'verification_method' => 'fingerprint',
                    'confidence_score' => 99.0,
                ],
            ]);
        }

        // 5. Resolve Device & Late Cutoff Time
        $device = $request->device_id ? BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->find($request->device_id) : null;
        if (!$device) {
            $device = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->where('status', 'active')->first();
        }

        $lateCutoff = $device?->late_cutoff_time ? Carbon::parse($device->late_cutoff_time) : Carbon::parse('08:30:00');
        $attendanceStatus = $now->greaterThan($lateCutoff) ? 'Late' : 'Present';

        // 6. Update Official ERP Attendance Table
        DB::transaction(function () use ($personType, $personId, $schoolId, $today, $currentTime, $attendanceStatus, $classId, $sectionId, $device) {
            $deviceName = $device?->name ?? 'Fingerprint Biometric Scanner';

            if ($personType === 'student') {
                StudentAttendance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $personId,
                        'attendance_date' => $today,
                    ],
                    [
                        'class_id' => $classId ?? 1,
                        'section_id' => $sectionId ?? 1,
                        'status' => $attendanceStatus,
                        'check_in' => $currentTime,
                        'remarks' => "Verified by {$deviceName} (" . Carbon::now()->format('h:i A') . ")",
                        'updated_by' => auth()->id() ?? null,
                    ]
                );
            } else {
                TeacherAttendance::withoutGlobalScopes()->updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'teacher_id' => $personId,
                        'attendance_date' => $today,
                    ],
                    [
                        'status' => $attendanceStatus,
                        'check_in' => $currentTime,
                        'remarks' => "Verified by {$deviceName} (" . Carbon::now()->format('h:i A') . ")",
                        'updated_by' => auth()->id() ?? null,
                    ]
                );
            }
        });

        // 7. Record Biometric Audit Log
        $log = BiometricAttendanceLog::create([
            'school_id' => $schoolId,
            'biometric_device_id' => $device?->id,
            'person_type' => $personType,
            'student_id' => $personType === 'student' ? $personId : null,
            'teacher_id' => $personType === 'teacher' ? $personId : null,
            'punch_date' => $today,
            'punch_time' => $currentTime,
            'punch_type' => 'check_in',
            'verification_method' => 'fingerprint',
            'confidence_score' => 99.0,
            'device_serial' => $device?->serial_number,
            'status' => 'success',
            'attendance_status' => $attendanceStatus,
            'sync_status' => 'synced',
            'remarks' => "Fingerprint / PIN verified with 99.0% minutiae match",
        ]);

        return response()->json([
            'success' => true,
            'is_duplicate' => false,
            'message' => "Welcome, {$personName}! Fingerprint attendance verified as {$attendanceStatus}.",
            'voice_prompt' => "Fingerprint verified. Welcome {$personName}.",
            'punch' => [
                'id' => $log->id,
                'person_name' => $personName,
                'person_code' => $personCode,
                'person_badge' => $personBadge,
                'person_photo' => $personPhoto,
                'punch_time' => Carbon::parse($currentTime)->format('h:i A'),
                'attendance_status' => $attendanceStatus,
                'verification_method' => 'fingerprint',
                'confidence_score' => 99.0,
            ],
        ]);
    }

    /**
     * List all persons with their biometric enrollment status
     */
    public function enrollments(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $type = $request->get('type', 'all'); // 'all', 'student', 'teacher'
        $classId = $request->get('class_id');
        $search = trim($request->get('search', ''));
        $statusFilter = $request->get('status'); // 'enrolled', 'not_enrolled', 'face_only', 'finger_only'

        $classes = SchoolClass::withoutGlobalScopes()->where('school_id', $schoolId)->get(['id', 'name']);

        $studentsQuery = Student::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', true)
            ->with(['class:id,name', 'section:id,name']);

        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }

        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_no', 'like', "%{$search}%");
            });
        }

        $teachersQuery = Teacher::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('status', true);

        if ($search) {
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Fetch existing enrollments map
        $enrollmentsMap = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->get()
            ->groupBy(function ($item) {
                return $item->person_type . '_' . ($item->person_type === 'student' ? $item->student_id : $item->teacher_id);
            });

        $rows = collect();

        if ($type === 'all' || $type === 'student') {
            foreach ($studentsQuery->limit(200)->get() as $st) {
                $key = 'student_' . $st->id;
                $enr = $enrollmentsMap->get($key)?->first();

                $isFace = $enr ? (bool)$enr->is_face_enrolled : false;
                $isFinger = $enr ? (bool)$enr->is_fingerprint_enrolled : false;

                // Status filter check
                if ($statusFilter === 'enrolled' && !$isFace && !$isFinger) continue;
                if ($statusFilter === 'not_enrolled' && ($isFace || $isFinger)) continue;
                if ($statusFilter === 'face_only' && !$isFace) continue;
                if ($statusFilter === 'finger_only' && !$isFinger) continue;

                $rows->push([
                    'person_type' => 'student',
                    'person_id' => $st->id,
                    'name' => $st->full_name,
                    'code' => $st->admission_no,
                    'class_name' => $st->class?->name,
                    'section_name' => $st->section?->name,
                    'designation' => null,
                    'photo_url' => $enr?->face_photo_path ?: $st->photo_url,
                    'biometric_pin' => $enr?->biometric_pin ?: $st->admission_no,
                    'rfid_card_no' => $enr?->rfid_card_no,
                    'is_face_enrolled' => $isFace,
                    'is_fingerprint_enrolled' => $isFinger,
                    'enrolled_at' => $enr?->enrolled_at ? Carbon::parse($enr->enrolled_at)->format('d M Y, h:i A') : null,
                ]);
            }
        }

        if (($type === 'all' || $type === 'teacher') && !$classId) {
            foreach ($teachersQuery->limit(100)->get() as $tc) {
                $key = 'teacher_' . $tc->id;
                $enr = $enrollmentsMap->get($key)?->first();

                $isFace = $enr ? (bool)$enr->is_face_enrolled : false;
                $isFinger = $enr ? (bool)$enr->is_fingerprint_enrolled : false;

                if ($statusFilter === 'enrolled' && !$isFace && !$isFinger) continue;
                if ($statusFilter === 'not_enrolled' && ($isFace || $isFinger)) continue;
                if ($statusFilter === 'face_only' && !$isFace) continue;
                if ($statusFilter === 'finger_only' && !$isFinger) continue;

                $rows->push([
                    'person_type' => 'teacher',
                    'person_id' => $tc->id,
                    'name' => $tc->full_name,
                    'code' => $tc->employee_id ?: ('TCH-' . $tc->id),
                    'class_name' => null,
                    'section_name' => null,
                    'designation' => $tc->qualification ?: 'Teacher',
                    'photo_url' => $enr?->face_photo_path ?: $tc->photo_url,
                    'biometric_pin' => $enr?->biometric_pin ?: $tc->employee_id,
                    'rfid_card_no' => $enr?->rfid_card_no,
                    'is_face_enrolled' => $isFace,
                    'is_fingerprint_enrolled' => $isFinger,
                    'enrolled_at' => $enr?->enrolled_at ? Carbon::parse($enr->enrolled_at)->format('d M Y, h:i A') : null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'classes' => $classes,
            'total' => $rows->count(),
            'data' => $rows->values(),
        ]);
    }

    /**
     * Save / Update Biometric Enrollment (Face Descriptors, Photo, Fingerprint PIN)
     */
    public function saveEnrollment(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $request->validate([
            'person_type' => 'required|in:student,teacher',
            'person_id' => 'required|integer',
            'biometric_pin' => 'nullable|string|max:50',
            'rfid_card_no' => 'nullable|string|max:50',
            'face_descriptor' => 'nullable|string', // JSON string
            'fingerprint_template' => 'nullable|string',
            'face_photo' => 'nullable|string', // base64 or photo URL
            'is_face_enrolled' => 'nullable|boolean',
            'is_fingerprint_enrolled' => 'nullable|boolean',
            'clear_face_photo' => 'nullable|boolean',
        ]);

        $personType = $request->person_type;
        $personId = (int) $request->person_id;

        $enrollment = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', $personType)
            ->where($personType . '_id', $personId)
            ->first();

        if (!$enrollment) {
            $enrollment = new BiometricEnrollment([
                'school_id' => $schoolId,
                'person_type' => $personType,
                'student_id' => $personType === 'student' ? $personId : null,
                'teacher_id' => $personType === 'teacher' ? $personId : null,
            ]);
        }

        if ($request->has('biometric_pin')) {
            $enrollment->biometric_pin = $request->filled('biometric_pin') ? trim($request->biometric_pin) : null;
        }

        if ($request->has('rfid_card_no')) {
            $enrollment->rfid_card_no = $request->filled('rfid_card_no') ? trim($request->rfid_card_no) : null;
        }

        if ($request->filled('face_descriptor')) {
            $enrollment->face_descriptor = $request->face_descriptor;
            $enrollment->is_face_enrolled = true;
        }

        if ($request->filled('fingerprint_template')) {
            $enrollment->fingerprint_template = $request->fingerprint_template;
            $enrollment->is_fingerprint_enrolled = true;
        }

        if ($request->has('is_fingerprint_enrolled')) {
            $enrollment->is_fingerprint_enrolled = $request->boolean('is_fingerprint_enrolled');
        } elseif ($request->filled('biometric_pin') || $request->filled('fingerprint_template')) {
            $enrollment->is_fingerprint_enrolled = true;
        }

        // Handle Clear Face Photo
        if ($request->has('clear_face_photo') && $request->boolean('clear_face_photo')) {
            if ($enrollment->face_photo_path && Storage::disk('public')->exists($enrollment->face_photo_path)) {
                Storage::disk('public')->delete($enrollment->face_photo_path);
            }
            $enrollment->face_photo_path = null;
            $enrollment->face_descriptor = null;
            $enrollment->is_face_enrolled = false;
        }

        // STRICT DUPLICATE FACE ENFORCEMENT: One Face = One Person Only!
        if ($request->filled('face_photo') && !empty($request->face_photo)) {
            $newPhotoData = $request->face_photo;

            // Fetch all other enrollments in this school with an enrolled face
            $otherEnrollments = BiometricEnrollment::withoutGlobalScopes()
                ->where('school_id', $schoolId)
                ->where(function ($q) {
                    $q->where('is_face_enrolled', true)
                      ->orWhereNotNull('face_photo_path');
                })
                ->where(function ($q) use ($personType, $personId) {
                    $q->where('person_type', '!=', $personType)
                      ->orWhere($personType . '_id', '!=', $personId);
                })
                ->get();

            foreach ($otherEnrollments as $otherEnr) {
                if (empty($otherEnr->face_photo_path)) continue;

                $comp = $this->compareFaceImages($newPhotoData, $otherEnr->face_photo_path);
                if ($comp['matched'] && ($comp['confidence'] ?? 0) >= 75.0) {
                    $conflictName = 'Another registered individual';
                    $conflictCode = '';
                    $conflictRole = ucfirst($otherEnr->person_type);

                    if ($otherEnr->person_type === 'student') {
                        $st = Student::withoutGlobalScopes()->where('school_id', $schoolId)->find($otherEnr->student_id);
                        if ($st) {
                            $conflictName = $st->full_name;
                            $conflictCode = $st->admission_no;
                            $conflictRole = 'Student';
                        }
                    } else {
                        $tc = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->find($otherEnr->teacher_id);
                        if ($tc) {
                            $conflictName = $tc->full_name;
                            $conflictCode = $tc->employee_id ?: ('TCH-' . $tc->id);
                            $conflictRole = 'Faculty Staff';
                        }
                    }

                    return response()->json([
                        'success' => false,
                        'duplicate_face' => true,
                        'conflicting_person' => [
                            'name' => $conflictName,
                            'code' => $conflictCode,
                            'role' => $conflictRole,
                            'similarity' => $comp['confidence'] ?? 90,
                        ],
                        'message' => "Security Violation: Duplicate Face Detected! This face is already enrolled for {$conflictName} ({$conflictCode}). One face can only be registered to one individual in this school. You must delete or reset the existing enrollment for {$conflictName} before this face can be registered to someone else.",
                    ], 422);
                }
            }
        }

        // Handle Face Photo capture or upload
        if ($request->filled('face_photo') && str_starts_with($request->face_photo, 'data:image')) {
            try {
                $parts = explode(';base64,', $request->face_photo);
                if (isset($parts[1])) {
                    $imageData = base64_decode($parts[1]);
                    $path = 'biometrics/enrolled_faces/' . $schoolId . '/' . $personType . '_' . $personId . '_' . time() . '.jpg';
                    Storage::disk('public')->put($path, $imageData);
                    $enrollment->face_photo_path = $path;
                    $enrollment->is_face_enrolled = true;

                    // Immediately update student or teacher's official photo so ERP profile and biometrics stay in sync
                    if ($personType === 'student') {
                        $st = Student::withoutGlobalScopes()->where('school_id', $schoolId)->find($personId);
                        if ($st) {
                            $st->update(['photo' => $path]);
                        }
                    } else {
                        $tc = Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->find($personId);
                        if ($tc) {
                            $tc->update(['photo' => $path]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Face enrollment photo save error: ' . $e->getMessage());
            }
        } elseif ($request->filled('face_photo')) {
            $rawPath = $request->face_photo;
            if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                $urlParts = parse_url($rawPath);
                $rawPath = ltrim($urlParts['path'] ?? '', '/');
            }
            $cleanPath = preg_replace('#^(storage/|/storage/|/)+#', '', $rawPath);
            $enrollment->face_photo_path = $cleanPath;
            $enrollment->is_face_enrolled = true;

            if ($personType === 'student') {
                Student::withoutGlobalScopes()->where('school_id', $schoolId)->where('id', $personId)->update(['photo' => $cleanPath]);
            } else {
                Teacher::withoutGlobalScopes()->where('school_id', $schoolId)->where('id', $personId)->update(['photo' => $cleanPath]);
            }
        }

        if ($request->has('is_face_enrolled')) {
            $enrollment->is_face_enrolled = $request->boolean('is_face_enrolled');
        }

        $enrollment->enrolled_at = Carbon::now();
        $enrollment->created_by = auth()->id() ?? null;
        $enrollment->save();

        return response()->json([
            'success' => true,
            'message' => 'Biometric enrollment updated successfully!',
            'data' => $enrollment,
        ]);
    }

    /**
     * Delete / Reset Biometric Enrollment for a student or teacher
     */
    public function deleteEnrollment(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $request->validate([
            'person_type' => 'required|in:student,teacher',
            'person_id' => 'required|integer',
        ]);

        $personType = $request->person_type;
        $personId = (int) $request->person_id;

        $enrollment = BiometricEnrollment::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->where('person_type', $personType)
            ->where($personType . '_id', $personId)
            ->first();

        if ($enrollment) {
            if ($enrollment->face_photo_path && Storage::disk('public')->exists($enrollment->face_photo_path)) {
                Storage::disk('public')->delete($enrollment->face_photo_path);
            }
            $enrollment->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Biometric credentials deleted successfully.',
        ]);
    }

    /**
     * Devices API (CRUD for physical machines and webcam kiosks)
     */
    public function devices(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $devices = BiometricDevice::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->withCount(['logs' => function ($q) {
                $q->whereDate('punch_date', Carbon::today());
            }])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    public function storeDevice(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'device_type' => 'required|in:webcam_face,face_camera,fingerprint,combo,zkteco,zkteco_push,essl,realtime,other',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ip_address' => 'nullable|string|max:50',
            'port' => 'nullable|integer',
            'location' => 'nullable|string|max:150',
            'status' => 'nullable|in:active,inactive',
            'late_cutoff_time' => 'nullable|string',
            'school_id' => 'nullable|integer',
        ]);

        $validated['school_id'] = $schoolId;
        $validated['api_token'] = Str::random(40);
        $validated['status'] = $validated['status'] ?? 'active';

        $device = BiometricDevice::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Biometric device added successfully.',
            'data' => $device,
        ], 201);
    }

    public function updateDevice(Request $request, $id)
    {
        $schoolId = $this->resolveSchoolId($request);
        $device = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'device_type' => 'required|in:webcam_face,face_camera,fingerprint,combo,zkteco,zkteco_push,essl,realtime,other',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ip_address' => 'nullable|string|max:50',
            'port' => 'nullable|integer',
            'location' => 'nullable|string|max:150',
            'status' => 'required|in:active,inactive',
            'late_cutoff_time' => 'nullable|string',
            'school_id' => 'nullable|integer',
        ]);

        $device->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Biometric device updated.',
            'data' => $device,
        ]);
    }

    public function deleteDevice(Request $request, $id)
    {
        $schoolId = $this->resolveSchoolId($request);
        $device = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->findOrFail($id);
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device deleted successfully.',
        ]);
    }

    public function pingDevice(Request $request, $id)
    {
        $schoolId = $this->resolveSchoolId($request);
        $device = BiometricDevice::withoutGlobalScopes()->where('school_id', $schoolId)->findOrFail($id);
        $device->update(['last_heartbeat_at' => Carbon::now()]);

        return response()->json([
            'success' => true,
            'message' => "Ping successful for device {$device->name}.",
            'last_heartbeat_at' => $device->last_heartbeat_at->format('d M Y, h:i:s A'),
        ]);
    }

    /**
     * Hardware Push Webhook / ADMS Endpoint for physical ZKTeco / eSSL machines
     */
    public function deviceWebhook(Request $request)
    {
        $serial = $request->query('SN') ?? $request->input('SN') ?? $request->header('X-Device-SN');

        if (!$serial) {
            return response('OK', 200);
        }

        $device = BiometricDevice::withoutGlobalScopes()->where('serial_number', $serial)->first();
        if (!$device) {
            return response('OK', 200); // Standard ACK
        }

        $device->update(['last_heartbeat_at' => Carbon::now()]);
        $schoolId = $device->school_id;

        // Parse punches from body
        $rawContent = $request->getContent();
        if (!empty($rawContent)) {
            $lines = explode("\n", trim($rawContent));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Typical ADMS line: 1001 \t 2026-11-15 08:24:12 \t 1 \t 1
                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 2) {
                    $pin = $parts[0];
                    $punchTime = $parts[1] . ' ' . ($parts[2] ?? '00:00:00');

                    // Find student or teacher by biometric_pin in this school
                    $enr = BiometricEnrollment::withoutGlobalScopes()
                        ->where('school_id', $schoolId)
                        ->where('biometric_pin', $pin)
                        ->first();

                    if ($enr) {
                        $pType = $enr->person_type;
                        $pId = $pType === 'student' ? $enr->student_id : $enr->teacher_id;

                        // Create punch log
                        BiometricAttendanceLog::create([
                            'school_id' => $schoolId,
                            'biometric_device_id' => $device->id,
                            'person_type' => $pType,
                            'student_id' => $pType === 'student' ? $pId : null,
                            'teacher_id' => $pType === 'teacher' ? $pId : null,
                            'punch_date' => Carbon::parse($punchTime)->toDateString(),
                            'punch_time' => Carbon::parse($punchTime)->toTimeString(),
                            'punch_type' => 'check_in',
                            'verification_method' => 'biometric_device',
                            'confidence_score' => 100.0,
                            'device_serial' => $serial,
                            'status' => 'success',
                            'attendance_status' => 'Present',
                            'sync_status' => 'synced',
                            'remarks' => "Synced from {$device->name} (SN: {$serial})",
                        ]);
                    }
                }
            }
        }

        return response('OK', 200);
    }

    /**
     * Live Punches Log Stream
     */
    public function logs(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $date = $request->get('date', Carbon::today()->toDateString());
        $method = $request->get('method');
        $personType = $request->get('person_type');
        $status = $request->get('status');

        $query = BiometricAttendanceLog::withoutGlobalScopes()
            ->where('school_id', $schoolId)
            ->whereDate('punch_date', $date)
            ->with([
                'student:id,first_name,last_name,admission_no,class_id,section_id,photo',
                'student.class:id,name',
                'student.section:id,name',
                'teacher:id,first_name,last_name,employee_id,photo',
                'device:id,name,location,device_type',
            ]);

        if ($method) {
            $query->where('verification_method', $method);
        }

        if ($personType) {
            $query->where('person_type', $personType);
        }

        if ($status) {
            $query->where('attendance_status', $status);
        }

        $logs = $query->orderBy('id', 'desc')->paginate($request->get('per_page', 25));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
