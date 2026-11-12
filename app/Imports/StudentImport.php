<?php

namespace App\Imports;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Services\TenantManager;
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
        $activeSession = AcademicSession::where('is_current', true)->first();
        $academicSessionId = $activeSession ? $activeSession->id : 1;

        $classesCache = SchoolClass::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->get();

        $sectionsCache = Section::withoutGlobalScopes()
            ->where('school_id', $this->schoolId)
            ->get();

        $studentRole = Role::where('name', 'Student')->where('guard_name', 'sanctum')->first();

        $rowNumber = 1; // Heading is row 1, data starts at row 2

        foreach ($rows as $rawRow)
        {
            $rowNumber++;
            $row = $this->normalizeKeys($rawRow->toArray());

            // Check if row is empty
            $firstName = trim($row['first_name'] ?? '');
            if (empty($firstName)) {
                continue; // Skip blank lines silently
            }

            $lastName = trim($row['last_name'] ?? '');
            $admissionNo = trim($row['admission_no'] ?? '');

            // 1. Resolve Class
            $classId = null;
            $classInput = trim($row['class'] ?? ($row['class_name'] ?? ''));
            if (!empty($classInput)) {
                $matchedClass = $classesCache->first(function ($c) use ($classInput) {
                    return strcasecmp($c->name, $classInput) === 0 || (string)$c->id === $classInput;
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

            // If still no section, use the first section for that class if available
            if (!$sectionId) {
                $firstSec = $sectionsCache->firstWhere('class_id', $classId);
                if ($firstSec) {
                    $sectionId = $firstSec->id;
                }
            }

            // 3. Auto-generate Admission Number if empty
            if (empty($admissionNo)) {
                $count = Student::withoutGlobalScopes()->where('school_id', $this->schoolId)->count() + $this->importedCount + $this->skippedCount + 1;
                $year = date('Y');
                $admissionNo = sprintf("ADM-%s-%04d", $year, $count);
            }

            // 4. Duplicate Check
            $exists = Student::withoutGlobalScopes()
                ->where('school_id', $this->schoolId)
                ->where('admission_no', $admissionNo)
                ->exists();

            if ($exists) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => "{$firstName} {$lastName}",
                    'admission_no' => $admissionNo,
                    'error' => "Admission number '{$admissionNo}' already exists in this school.",
                ];
                $this->skippedCount++;
                continue;
            }

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
                    $bloodGroup, $fatherName, $fatherMobile, $motherName, $address, $studentRole
                ) {
                    $userId = null;

                    // Create User Account if requested
                    if ($this->createPortalAccounts) {
                        $userEmail = $email ?: strtolower($admissionNo) . '@school.internal';
                        
                        // Ensure unique email for user
                        $user = User::where('email', $userEmail)->first();
                        if (!$user) {
                            $user = User::create([
                                'school_id' => $this->schoolId,
                                'name' => trim("{$firstName} {$lastName}"),
                                'username' => strtolower($admissionNo),
                                'email' => $userEmail,
                                'password' => Hash::make('12345678'),
                                'user_type' => 'student',
                                'status' => 1,
                            ]);

                            if ($studentRole) {
                                $user->assignRole($studentRole);
                            }
                        }
                        $userId = $user->id;
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
            $cleanKey = strtolower(trim($key));
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
