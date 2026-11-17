<?php

namespace App\Imports;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Spatie\Permission\Models\Role;

class StudentImport implements ToCollection, WithHeadingRow
{
    protected int $schoolId;
    protected ?int $defaultClassId;
    protected ?int $defaultSectionId;
    protected bool $createPortalAccounts;

    public int $importedCount = 0;
    public int $skippedCount = 0;
    public array $errors = [];

    public function __construct(
        int $schoolId,
        ?int $defaultClassId = null,
        ?int $defaultSectionId = null,
        bool $createPortalAccounts = true
    ) {
        $this->schoolId = $schoolId;
        $this->defaultClassId = $defaultClassId;
        $this->defaultSectionId = $defaultSectionId;
        $this->createPortalAccounts = $createPortalAccounts;
    }

    public function collection(Collection $rows)
    {
        @ini_set('max_execution_time', '0');
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');

        // 1. Resolve Active Session for this School
        $activeSession = AcademicSession::where('school_id', $this->schoolId)->where('is_current', true)->first()
            ?: (AcademicSession::where('school_id', $this->schoolId)->first()
            ?: AcademicSession::where('is_current', true)->first());
        $academicSessionId = $activeSession ? $activeSession->id : 1;

        // 2. Pre-fetch Classes & Sections for this School into memory
        $classesCache = SchoolClass::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->get();

        $sectionsCache = Section::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->get();

        // 3. Pre-fetch existing admission numbers for this school to avoid N+1 DB queries
        $existingAdmissionNos = Student::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->pluck('admission_no')
            ->map(fn($v) => strtolower(trim((string)$v)))
            ->flip()
            ->toArray();

        // 4. Pre-fetch existing user accounts to avoid N+1 DB queries
        $existingUsers = User::whereNotNull('email')
            ->orWhereNotNull('username')
            ->get(['id', 'email', 'username']);

        $existingEmailsMap = [];
        $existingUsernamesMap = [];
        foreach ($existingUsers as $u) {
            if (!empty($u->email)) {
                $existingEmailsMap[strtolower(trim($u->email))] = $u->id;
            }
            if (!empty($u->username)) {
                $existingUsernamesMap[strtolower(trim($u->username))] = $u->id;
            }
        }

        // 5. Pre-hash default portal password ONCE (massive performance boost vs hashing 500x)
        $defaultPasswordHash = Hash::make('12345678');

        // 6. Role lookup
        $studentRole = Role::where('name', 'Student')->where('guard_name', 'sanctum')->first();

        // 7. Base student count for auto-generating admission numbers
        $baseStudentCount = Student::withoutGlobalScopes()->where('school_id', $this->schoolId)->count();
        $currentYear = date('Y');
        $rowNumber = 1;

        foreach ($rows as $rawRow)
        {
            $rowNumber++;

            if ($rowNumber % 25 === 0) {
                @set_time_limit(180);
            }

            $row = $this->normalizeKeys($rawRow->toArray());

            // Skip empty rows
            $firstName = trim($row['first_name'] ?? '');
            if (empty($firstName)) {
                continue;
            }

            $lastName = trim($row['last_name'] ?? '');
            $admissionNo = trim($row['admission_no'] ?? '');

            // 1. Resolve Class
            $classId = null;
            $classInput = trim($row['class'] ?? ($row['class_name'] ?? ''));
            if (!empty($classInput)) {
                $matchedClass = $classesCache->first(function ($c) use ($classInput) {
                    return strcasecmp($c->name, $classInput) === 0 
                        || (string)$c->id === $classInput
                        || (string)$c->numeric_name === $classInput;
                });
                if ($matchedClass) {
                    $classId = $matchedClass->id;
                }
            }

            if (!$classId && $this->defaultClassId) {
                $classId = $this->defaultClassId;
            }

            if (!$classId) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => "{$firstName} {$lastName}",
                    'admission_no' => $admissionNo ?: 'N/A',
                    'error' => "Class '{$classInput}' was not found. Please provide a valid class.",
                ];
                $this->skippedCount++;
                continue;
            }

            // 2. Resolve Section
            $sectionId = null;
            $sectionInput = trim($row['section'] ?? ($row['section_name'] ?? ''));
            if (!empty($sectionInput)) {
                $matchedSection = $sectionsCache->first(function ($s) use ($sectionInput, $classId) {
                    $matchesClass = !$classId || (int)$s->class_id === (int)$classId;
                    $matchesName = strcasecmp($s->name, $sectionInput) === 0 
                        || strcasecmp($s->name, "Section {$sectionInput}") === 0
                        || (string)$s->id === $sectionInput;
                    return $matchesClass && $matchesName;
                });
                if ($matchedSection) {
                    $sectionId = $matchedSection->id;
                }
            }

            if (!$sectionId && $this->defaultSectionId) {
                $sectionId = $this->defaultSectionId;
            }

            // Fallback: pick first section of that class
            if (!$sectionId) {
                $firstSec = $sectionsCache->firstWhere('class_id', $classId);
                if ($firstSec) {
                    $sectionId = $firstSec->id;
                }
            }

            // 3. Auto-generate Admission Number if empty
            if (empty($admissionNo)) {
                $count = $baseStudentCount + $this->importedCount + $this->skippedCount + 1;
                $admissionNo = sprintf("ADM-%s-%04d", $currentYear, $count);
            }

            // 4. Duplicate Check (in-memory)
            $normAdmissionNo = strtolower($admissionNo);
            if (isset($existingAdmissionNos[$normAdmissionNo])) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => "{$firstName} {$lastName}",
                    'admission_no' => $admissionNo,
                    'error' => "Admission number '{$admissionNo}' already exists in this school.",
                ];
                $this->skippedCount++;
                continue;
            }
            // Mark as used in memory for remaining rows
            $existingAdmissionNos[$normAdmissionNo] = true;

            // 5. Gender Normalization
            $genderRaw = strtolower(trim($row['gender'] ?? 'male'));
            $gender = match ($genderRaw) {
                'female', 'f' => 'Female',
                'other', 'o' => 'Other',
                default => 'Male',
            };

            // 6. Dates Parsing
            $dob = $this->parseDate($row['dob'] ?? ($row['date_of_birth'] ?? null));
            $admissionDate = $this->parseDate($row['admission_date'] ?? null) ?: Carbon::today()->toDateString();

            // 7. Contact Details
            $mobile = trim($row['mobile'] ?? ($row['phone'] ?? '')) ?: null;
            $email = trim($row['email'] ?? '') ?: null;
            $bloodGroup = trim($row['blood_group'] ?? '') ?: null;
            $fatherName = trim($row['father_name'] ?? '') ?: 'N/A';
            $fatherMobile = trim($row['father_mobile'] ?? '') ?: null;
            $motherName = trim($row['mother_name'] ?? '') ?: null;
            $address = trim($row['address'] ?? '') ?: null;
            $rollNo = trim($row['roll_no'] ?? '') ?: null;

            try {
                DB::transaction(function () use (
                    $admissionNo, $rollNo, $firstName, $lastName, $classId, $sectionId,
                    $academicSessionId, $gender, $dob, $admissionDate, $mobile, $email,
                    $bloodGroup, $fatherName, $fatherMobile, $motherName, $address,
                    $studentRole, $defaultPasswordHash, &$existingEmailsMap, &$existingUsernamesMap
                ) {
                    $userId = null;

                    // Create User Account if requested
                    if ($this->createPortalAccounts) {
                        $userEmail = $email ? strtolower(trim($email)) : strtolower($admissionNo) . '@school.internal';
                        $username = strtolower($admissionNo);

                        if (isset($existingEmailsMap[$userEmail])) {
                            $userId = $existingEmailsMap[$userEmail];
                        } elseif (isset($existingUsernamesMap[$username])) {
                            $userId = $existingUsernamesMap[$username];
                        } else {
                            $user = User::create([
                                'school_id' => $this->schoolId,
                                'name' => trim("{$firstName} {$lastName}"),
                                'username' => $username,
                                'email' => $userEmail,
                                'password' => $defaultPasswordHash,
                                'user_type' => 'student',
                                'status' => 1,
                            ]);

                            if ($studentRole) {
                                DB::table('model_has_roles')->insertOrIgnore([
                                    'role_id' => $studentRole->id,
                                    'model_type' => User::class,
                                    'model_id' => $user->id,
                                ]);
                            }

                            $userId = $user->id;
                            $existingEmailsMap[$userEmail] = $userId;
                            $existingUsernamesMap[$username] = $userId;
                        }
                    }

                    // Create Student Record
                    Student::create([
                        'school_id' => $this->schoolId,
                        'user_id' => $userId,
                        'academic_session_id' => $academicSessionId,
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'admission_no' => $admissionNo,
                        'roll_no' => $rollNo,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'gender' => $gender,
                        'dob' => $dob,
                        'admission_date' => $admissionDate,
                        'mobile' => $mobile,
                        'email' => $email,
                        'blood_group' => $bloodGroup,
                        'father_name' => $fatherName,
                        'father_mobile' => $fatherMobile,
                        'mother_name' => $motherName,
                        'address' => $address,
                        'status' => 1,
                        'created_by' => Auth::id(),
                    ]);

                    $this->importedCount++;
                });
            } catch (\Throwable $e) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => "{$firstName} {$lastName}",
                    'admission_no' => $admissionNo,
                    'error' => $e->getMessage(),
                ];
                $this->skippedCount++;
            }
        }
    }

    /**
     * Map fuzzy headings like 'First Name *', 'class_name_eg_class_1', etc. to clean keys
     */
    protected function normalizeKeys(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim((string)$key));
            $cleanKey = preg_replace('/[^a-z0-9_]/', '_', $cleanKey);
            $cleanKey = preg_replace('/_+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');

            if (str_contains($cleanKey, 'first_name')) {
                $normalized['first_name'] = $value;
            } elseif (str_contains($cleanKey, 'last_name')) {
                $normalized['last_name'] = $value;
            } elseif (str_contains($cleanKey, 'admission_no')) {
                $normalized['admission_no'] = $value;
            } elseif (str_contains($cleanKey, 'roll')) {
                $normalized['roll_no'] = $value;
            } elseif (str_contains($cleanKey, 'class')) {
                $normalized['class'] = $value;
            } elseif (str_contains($cleanKey, 'section')) {
                $normalized['section'] = $value;
            } elseif (str_contains($cleanKey, 'gender')) {
                $normalized['gender'] = $value;
            } elseif (str_contains($cleanKey, 'dob') || str_contains($cleanKey, 'birth')) {
                $normalized['dob'] = $value;
            } elseif (str_contains($cleanKey, 'admission_date')) {
                $normalized['admission_date'] = $value;
            } elseif (str_contains($cleanKey, 'blood')) {
                $normalized['blood_group'] = $value;
            } elseif (str_contains($cleanKey, 'father_mobile') || str_contains($cleanKey, 'father_phone')) {
                $normalized['father_mobile'] = $value;
            } elseif (str_contains($cleanKey, 'father')) {
                $normalized['father_name'] = $value;
            } elseif (str_contains($cleanKey, 'mother')) {
                $normalized['mother_name'] = $value;
            } elseif (str_contains($cleanKey, 'mobile') || str_contains($cleanKey, 'phone')) {
                $normalized['mobile'] = $value;
            } elseif (str_contains($cleanKey, 'email')) {
                $normalized['email'] = $value;
            } elseif (str_contains($cleanKey, 'address')) {
                $normalized['address'] = $value;
            } else {
                $normalized[$cleanKey] = $value;
            }
        }
        return $normalized;
    }

    /**
     * Safely parse Excel date serial number or date strings
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // If Excel numeric timestamp
        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float)$value))->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
