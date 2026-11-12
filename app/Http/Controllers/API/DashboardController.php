<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Exam;
use App\Models\Expense;
use App\Models\FeePayment;
use App\Models\HostelBuilding;
use App\Models\HostelRoom;
use App\Models\Income;
use App\Models\Inventory\InventoryItem;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\Transport\TransportVehicle;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Get school ID from authenticated user.
     */
    protected function schoolId()
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        /*
        |--------------------------------------------------------------------------
        | Normal school user
        |--------------------------------------------------------------------------
        */

        if (!empty($user->school_id)) {
            return (int) $user->school_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | If Super Admin has no school_id, use the first active school.
        |
        |--------------------------------------------------------------------------
        */

        if (
            ($user->user_type ?? '') === 'super_admin' ||
            strtolower((string) ($user->user_type ?? '')) === 'super admin'
        ) {
            $schoolId = DB::table('schools')
                ->where('status', 1)
                ->orderBy('id')
                ->value('id');

            if ($schoolId) {
                return (int) $schoolId;
            }
        }

        abort(
            422,
            'School could not be determined.'
        );
    }


    /**
     * Dashboard.
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                abort(401, 'Unauthenticated.');
            }

            $tenantManager = app(TenantManager::class);
            $isSuperAdmin = $tenantManager->isSuperAdmin($user);

            // Super Admin: Global Mode (All Schools)
            if ($isSuperAdmin && $tenantManager->getTenantId() === null) {
                return response()->json([
                    'success' => true,
                    'data'    => $this->getGlobalDashboardData(),
                ]);
            }

            // Individual school mode
            if ($isSuperAdmin) {
                $schoolId = $tenantManager->getTenantId() ?? $user->school_id;
                if (!$schoolId) {
                    $schoolId = School::where('status', 1)->value('id') ?? 1;
                }
            } else {
                if (empty($user->school_id)) {
                    abort(403, 'No school assigned to this account.');
                }
                $schoolId = (int) $user->school_id;
            }

            $school = School::find($schoolId);
            if (!$school) {
                abort(404, 'School not found.');
            }


            /*
            |--------------------------------------------------------------------------
            | Current Academic Session
            |--------------------------------------------------------------------------
            */

            $currentSession = AcademicSession::query()
                ->where('school_id', $schoolId)
                ->where('status', 1)
                ->where('is_current', 1)
                ->orderByDesc('id')
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Fallback latest active session
            |--------------------------------------------------------------------------
            */

            if (!$currentSession) {
                $currentSession = AcademicSession::query()
                    ->where('school_id', $schoolId)
                    ->where('status', 1)
                    ->orderByDesc('id')
                    ->first();
            }


            $sessionId =
                $currentSession?->id;


            /*
            |--------------------------------------------------------------------------
            | STUDENTS
            |--------------------------------------------------------------------------
            */

            $studentQuery = Student::query()
                ->where('school_id', $schoolId);

            if ($sessionId) {
                $studentQuery->where(
                    'academic_session_id',
                    $sessionId
                );
            }

            $totalStudents =
                (clone $studentQuery)->count();

            $activeStudents =
                (clone $studentQuery)
                    ->where('status', 1)
                    ->count();

            $inactiveStudents =
                (clone $studentQuery)
                    ->where('status', 0)
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | TEACHERS
            |--------------------------------------------------------------------------
            */

            $teacherQuery = Teacher::query()
                ->where('school_id', $schoolId);

            $totalTeachers =
                (clone $teacherQuery)->count();

            $activeTeachers =
                (clone $teacherQuery)
                    ->where('status', 1)
                    ->count();

            $inactiveTeachers =
                (clone $teacherQuery)
                    ->where('status', 0)
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | ACADEMIC
            |--------------------------------------------------------------------------
            */

            $totalClasses =
                SchoolClass::query()
                    ->where('school_id', $schoolId)
                    ->count();

            $activeClasses =
                SchoolClass::query()
                    ->where('school_id', $schoolId)
                    ->where('status', 1)
                    ->count();

            $totalSections =
                Section::query()
                    ->where('school_id', $schoolId)
                    ->count();

            $activeSections =
                Section::query()
                    ->where('school_id', $schoolId)
                    ->where('status', 1)
                    ->count();

            /*
            |--------------------------------------------------------------------------
            | Subjects
            |--------------------------------------------------------------------------
            |
            | Your subjects table in the database does not contain school_id.
            |
            |--------------------------------------------------------------------------
            */

            $totalSubjects =
                Subject::query()
                    ->where('status', 1)
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | EXAMS
            |--------------------------------------------------------------------------
            */

            $examQuery = Exam::query()
                ->where('school_id', $schoolId);

            if ($sessionId) {
                $examQuery->where(
                    'academic_session_id',
                    $sessionId
                );
            }

            $totalExams =
                (clone $examQuery)->count();

            $publishedExams =
                (clone $examQuery)
                    ->where(
                        'is_result_published',
                        1
                    )
                    ->count();

            $upcomingExams =
                (clone $examQuery)
                    ->whereDate(
                        'start_date',
                        '>=',
                        now()->toDateString()
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | FEES
            |--------------------------------------------------------------------------
            */

            $feePaymentQuery = FeePayment::query()
                ->where(
                    'school_id',
                    $schoolId
                );

            if ($sessionId) {
                $feePaymentQuery->where(
                    'academic_session_id',
                    $sessionId
                );
            }

            $feeCollected =
                (clone $feePaymentQuery)
                    ->where(
                        'status',
                        'Success'
                    )
                    ->sum('total_amount');


            /*
            |--------------------------------------------------------------------------
            | FEE DUES
            |--------------------------------------------------------------------------
            */

            $feeDueQuery =
                DB::table('student_fee_dues')
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->whereNull(
                        'deleted_at'
                    );

            if ($sessionId) {
                $feeDueQuery->where(
                    'academic_session_id',
                    $sessionId
                );
            }

            $totalFeeDue =
                (clone $feeDueQuery)
                    ->sum('balance_amount');


            $pendingFeeStudents =
                (clone $feeDueQuery)
                    ->where(
                        'balance_amount',
                        '>',
                        0
                    )
                    ->distinct(
                        'student_id'
                    )
                    ->count(
                        'student_id'
                    );


            /*
            |--------------------------------------------------------------------------
            | TODAY'S FEE COLLECTION
            |--------------------------------------------------------------------------
            */

            $todayFeeCollection =
                (clone $feePaymentQuery)
                    ->where(
                        'status',
                        'Success'
                    )
                    ->whereDate(
                        'payment_date',
                        now()->toDateString()
                    )
                    ->sum('total_amount');


            /*
            |--------------------------------------------------------------------------
            | EXPENSES
            |--------------------------------------------------------------------------
            */

            $expenseQuery =
                Expense::query()
                    ->where(
                        'school_id',
                        $schoolId
                    );

            $totalExpenses =
                (clone $expenseQuery)
                    ->where(
                        'status',
                        'Paid'
                    )
                    ->sum('amount');


            $todayExpenses =
                (clone $expenseQuery)
                    ->where(
                        'status',
                        'Paid'
                    )
                    ->whereDate(
                        'expense_date',
                        now()->toDateString()
                    )
                    ->sum('amount');


            /*
            |--------------------------------------------------------------------------
            | INCOME
            |--------------------------------------------------------------------------
            */

            $incomeQuery =
                Income::query()
                    ->where(
                        'school_id',
                        $schoolId
                    );

            $otherIncome =
                (clone $incomeQuery)
                    ->where(
                        'status',
                        'Received'
                    )
                    ->sum('amount');


            $todayOtherIncome =
                (clone $incomeQuery)
                    ->where(
                        'status',
                        'Received'
                    )
                    ->whereDate(
                        'income_date',
                        now()->toDateString()
                    )
                    ->sum('amount');


            /*
            |--------------------------------------------------------------------------
            | ATTENDANCE - TODAY
            |--------------------------------------------------------------------------
            */

            $today =
                now()->toDateString();


            $studentAttendanceQuery =
                StudentAttendance::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->whereDate(
                        'attendance_date',
                        $today
                    );


            $studentPresent =
                (clone $studentAttendanceQuery)
                    ->where(
                        'status',
                        'Present'
                    )
                    ->count();

            $studentAbsent =
                (clone $studentAttendanceQuery)
                    ->where(
                        'status',
                        'Absent'
                    )
                    ->count();

            $studentLate =
                (clone $studentAttendanceQuery)
                    ->where(
                        'status',
                        'Late'
                    )
                    ->count();

            $studentLeave =
                (clone $studentAttendanceQuery)
                    ->where(
                        'status',
                        'Leave'
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | TEACHER ATTENDANCE - TODAY
            |--------------------------------------------------------------------------
            */

            $teacherAttendanceQuery =
                TeacherAttendance::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->whereDate(
                        'attendance_date',
                        $today
                    );


            $teacherPresent =
                (clone $teacherAttendanceQuery)
                    ->where(
                        'status',
                        'Present'
                    )
                    ->count();

            $teacherAbsent =
                (clone $teacherAttendanceQuery)
                    ->where(
                        'status',
                        'Absent'
                    )
                    ->count();

            $teacherLate =
                (clone $teacherAttendanceQuery)
                    ->where(
                        'status',
                        'Late'
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | HOSTEL
            |--------------------------------------------------------------------------
            */

            $hostelBuildings =
                HostelBuilding::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->count();

            $hostelRooms =
                HostelRoom::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->count();

            $hostelBeds =
                HostelRoom::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->sum('capacity');

            $occupiedBeds =
                HostelRoom::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->sum('occupied_beds');


            $availableBeds =
                max(
                    0,
                    (int) $hostelBeds -
                    (int) $occupiedBeds
                );


            /*
            |--------------------------------------------------------------------------
            | INVENTORY
            |--------------------------------------------------------------------------
            */

            $inventoryItems =
                InventoryItem::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->count();

            $lowStockItems =
                InventoryItem::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->whereColumn(
                        'opening_stock',
                        '<=',
                        'reorder_level'
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | TRANSPORT
            |--------------------------------------------------------------------------
            */

            $transportVehicles =
                TransportVehicle::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->where(
                        'status',
                        'Active'
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | MONTHLY FINANCIAL DATA
            |--------------------------------------------------------------------------
            */

            $monthlyFees = FeePayment::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'status',
                    'Success'
                )
                ->where(
                    'payment_date',
                    '>=',
                    now()
                        ->startOfMonth()
                        ->toDateString()
                )
                ->sum('total_amount');


            $monthlyExpenses = Expense::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'status',
                    'Paid'
                )
                ->where(
                    'expense_date',
                    '>=',
                    now()
                        ->startOfMonth()
                        ->toDateString()
                )
                ->sum('amount');


            $monthlyIncome = Income::query()
                ->where(
                    'school_id',
                    $schoolId
                )
                ->where(
                    'status',
                    'Received'
                )
                ->where(
                    'income_date',
                    '>=',
                    now()
                        ->startOfMonth()
                        ->toDateString()
                )
                ->sum('amount');


            /*
            |--------------------------------------------------------------------------
            | FINANCIAL BALANCE
            |--------------------------------------------------------------------------
            */

            $monthlyTotalIncome =
                (float) $monthlyFees +
                (float) $monthlyIncome;

            $monthlyBalance =
                $monthlyTotalIncome -
                (float) $monthlyExpenses;


            /*
            |--------------------------------------------------------------------------
            | STUDENT GENDER SUMMARY
            |--------------------------------------------------------------------------
            */

            $genderData =
                Student::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->when(
                        $sessionId,
                        function ($query) use ($sessionId) {
                            $query->where(
                                'academic_session_id',
                                $sessionId
                            );
                        }
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->select(
                        'gender',
                        DB::raw(
                            'COUNT(*) as total'
                        )
                    )
                    ->groupBy(
                        'gender'
                    )
                    ->pluck(
                        'total',
                        'gender'
                    );


            /*
            |--------------------------------------------------------------------------
            | RECENT STUDENTS
            |--------------------------------------------------------------------------
            */

            $recentStudents =
                Student::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->when(
                        $sessionId,
                        function ($query) use ($sessionId) {
                            $query->where(
                                'academic_session_id',
                                $sessionId
                            );
                        }
                    )
                    ->orderByDesc('id')
                    ->limit(5)
                    ->get([
                        'id',
                        'admission_no',
                        'first_name',
                        'last_name',
                        'gender',
                        'status',
                        'created_at'
                    ])
                    ->map(function ($student) {

                        return [
                            'id' =>
                                $student->id,

                            'admission_no' =>
                                $student->admission_no,

                            'name' =>
                                trim(
                                    $student->first_name .
                                    ' ' .
                                    ($student->last_name ?? '')
                                ),

                            'gender' =>
                                $student->gender,

                            'status' =>
                                (bool) $student->status,

                            'created_at' =>
                                $student->created_at
                                    ? $student->created_at
                                        ->format('d M Y')
                                    : null,
                        ];

                    });


            /*
            |--------------------------------------------------------------------------
            | UPCOMING EXAMS
            |--------------------------------------------------------------------------
            */

            $upcomingExamList =
                Exam::query()
                    ->where(
                        'school_id',
                        $schoolId
                    )
                    ->when(
                        $sessionId,
                        function ($query) use ($sessionId) {
                            $query->where(
                                'academic_session_id',
                                $sessionId
                            );
                        }
                    )
                    ->where(
                        'status',
                        1
                    )
                    ->whereDate(
                        'start_date',
                        '>=',
                        $today
                    )
                    ->orderBy(
                        'start_date'
                    )
                    ->limit(5)
                    ->get([
                        'id',
                        'name',
                        'code',
                        'start_date',
                        'end_date',
                        'is_result_published'
                    ]);


            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'data' => [

                    /*
                    | Basic
                    */

                    'is_global' => false,

                    'school_id' =>
                        $schoolId,

                    'school_name' =>
                        $school->name,

                    'school_code' =>
                        $school->code,

                    'academic_session' => $currentSession
                        ? [
                            'id' =>
                                $currentSession->id,

                            'name' =>
                                $currentSession->name,

                            'start_date' =>
                                $currentSession->start_date,

                            'end_date' =>
                                $currentSession->end_date,
                        ]
                        : null,


                    /*
                    | Students
                    */

                    'students' => [

                        'total' =>
                            $totalStudents,

                        'active' =>
                            $activeStudents,

                        'inactive' =>
                            $inactiveStudents,

                        'male' =>
                            (int) (
                                $genderData['Male']
                                ?? 0
                            ),

                        'female' =>
                            (int) (
                                $genderData['Female']
                                ?? 0
                            ),

                        'other' =>
                            (int) (
                                $genderData['Other']
                                ?? 0
                            ),
                    ],


                    /*
                    | Teachers
                    */

                    'teachers' => [

                        'total' =>
                            $totalTeachers,

                        'active' =>
                            $activeTeachers,

                        'inactive' =>
                            $inactiveTeachers,
                    ],


                    /*
                    | Academic
                    */

                    'academic' => [

                        'classes' =>
                            $totalClasses,

                        'active_classes' =>
                            $activeClasses,

                        'sections' =>
                            $totalSections,

                        'active_sections' =>
                            $activeSections,

                        'subjects' =>
                            $totalSubjects,
                    ],


                    /*
                    | Exams
                    */

                    'exams' => [

                        'total' =>
                            $totalExams,

                        'published' =>
                            $publishedExams,

                        'upcoming' =>
                            $upcomingExams,
                    ],


                    /*
                    | Fees
                    */

                    'fees' => [

                        'collected' =>
                            (float) $feeCollected,

                        'due' =>
                            (float) $totalFeeDue,

                        'pending_students' =>
                            $pendingFeeStudents,

                        'today' =>
                            (float) $todayFeeCollection,

                        'monthly' =>
                            (float) $monthlyFees,
                    ],


                    /*
                    | Finance
                    */

                    'finance' => [

                        'expenses' =>
                            (float) $totalExpenses,

                        'today_expenses' =>
                            (float) $todayExpenses,

                        'other_income' =>
                            (float) $otherIncome,

                        'today_other_income' =>
                            (float) $todayOtherIncome,

                        'monthly_income' =>
                            (float) $monthlyTotalIncome,

                        'monthly_expenses' =>
                            (float) $monthlyExpenses,

                        'monthly_balance' =>
                            (float) $monthlyBalance,
                    ],


                    /*
                    | Student Attendance
                    */

                    'student_attendance' => [

                        'present' =>
                            $studentPresent,

                        'absent' =>
                            $studentAbsent,

                        'late' =>
                            $studentLate,

                        'leave' =>
                            $studentLeave,
                    ],


                    /*
                    | Teacher Attendance
                    */

                    'teacher_attendance' => [

                        'present' =>
                            $teacherPresent,

                        'absent' =>
                            $teacherAbsent,

                        'late' =>
                            $teacherLate,
                    ],


                    /*
                    | Hostel
                    */

                    'hostel' => [

                        'buildings' =>
                            $hostelBuildings,

                        'rooms' =>
                            $hostelRooms,

                        'beds' =>
                            (int) $hostelBeds,

                        'occupied_beds' =>
                            (int) $occupiedBeds,

                        'available_beds' =>
                            $availableBeds,
                    ],


                    /*
                    | Inventory
                    */

                    'inventory' => [

                        'items' =>
                            $inventoryItems,

                        'low_stock' =>
                            $lowStockItems,
                    ],


                    /*
                    | Transport
                    */

                    'transport' => [

                        'vehicles' =>
                            $transportVehicles,
                    ],


                    /*
                    | Recent students
                    */

                    'recent_students' =>
                        $recentStudents,


                    /*
                    | Upcoming exams
                    */

                    'upcoming_exam_list' =>
                        $upcomingExamList,

                ]

            ]);

        } catch (Throwable $e) {

            report($e);

            return response()->json([

                'success' => false,

                'message' =>
                    'Unable to load dashboard.',

                'error' =>
                    config('app.debug')
                        ? $e->getMessage()
                        : null,

            ], 500);
        }
    }

    /**
     * Build system-wide global dashboard data for Super Admin.
     */
    protected function getGlobalDashboardData(): array
    {
        $totalStudents = Student::withoutGlobalScopes()->count();
        $activeStudents = Student::withoutGlobalScopes()->where('status', 1)->count();
        $inactiveStudents = Student::withoutGlobalScopes()->where('status', 0)->count();
        $maleStudents = Student::withoutGlobalScopes()->where('gender', 'Male')->count();
        $femaleStudents = Student::withoutGlobalScopes()->where('gender', 'Female')->count();
        $otherStudents = Student::withoutGlobalScopes()->whereNotIn('gender', ['Male', 'Female'])->count();

        $totalTeachers = Teacher::withoutGlobalScopes()->count();
        $activeTeachers = Teacher::withoutGlobalScopes()->where('status', 1)->count();
        $inactiveTeachers = Teacher::withoutGlobalScopes()->where('status', 0)->count();

        $totalClasses = SchoolClass::withoutGlobalScopes()->count();
        $activeClasses = SchoolClass::withoutGlobalScopes()->where('status', 1)->count();
        $totalSections = Section::withoutGlobalScopes()->count();
        $activeSections = Section::withoutGlobalScopes()->where('status', 1)->count();
        $totalSubjects = Subject::withoutGlobalScopes()->where('status', 1)->count();

        $totalExams = Exam::withoutGlobalScopes()->count();
        $publishedExams = Exam::withoutGlobalScopes()->where('is_result_published', 1)->count();
        $upcomingExams = Exam::withoutGlobalScopes()->where('start_date', '>=', now()->toDateString())->count();

        $feeCollected = (float) FeePayment::withoutGlobalScopes()->where('status', 'Success')->sum('total_amount');
        $totalFeeDue = (float) DB::table('student_fee_dues')->whereNull('deleted_at')->sum('balance_amount');
        $todayFeeCollection = (float) FeePayment::withoutGlobalScopes()->where('status', 'Success')->whereDate('payment_date', now()->toDateString())->sum('total_amount');
        $monthlyFees = (float) FeePayment::withoutGlobalScopes()->where('status', 'Success')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('total_amount');

        $totalExpenses = (float) Expense::withoutGlobalScopes()->where('status', 'Paid')->sum('amount');
        $todayExpenses = (float) Expense::withoutGlobalScopes()->where('status', 'Paid')->whereDate('expense_date', now()->toDateString())->sum('amount');
        $otherIncome = (float) Income::withoutGlobalScopes()->where('status', 'Received')->sum('amount');
        $todayOtherIncome = (float) Income::withoutGlobalScopes()->where('status', 'Received')->whereDate('income_date', now()->toDateString())->sum('amount');
        $monthlyTotalIncome = (float) Income::withoutGlobalScopes()->where('status', 'Received')->whereMonth('income_date', now()->month)->whereYear('income_date', now()->year)->sum('amount');
        $monthlyExpenses = (float) Expense::withoutGlobalScopes()->where('status', 'Paid')->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount');
        $monthlyBalance = $monthlyTotalIncome - $monthlyExpenses;

        $studentPresent = StudentAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Present')->count();
        $studentAbsent = StudentAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Absent')->count();
        $studentLate = StudentAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Late')->count();
        $studentLeave = StudentAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Leave')->count();

        $teacherPresent = TeacherAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Present')->count();
        $teacherAbsent = TeacherAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Absent')->count();
        $teacherLate = TeacherAttendance::withoutGlobalScopes()->whereDate('attendance_date', now()->toDateString())->where('status', 'Late')->count();

        $recentStudents = Student::withoutGlobalScopes()
            ->with(['school:id,name,code', 'class:id,name', 'section:id,name'])
            ->latest('id')
            ->limit(5)
            ->get();

        $upcomingExamList = Exam::withoutGlobalScopes()
            ->latest('id')
            ->limit(5)
            ->get();

        $schoolList = School::withCount(['students', 'teachers'])
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'status']);

        return [
            'is_global' => true,
            'school_id' => null,
            'school_name' => 'All Schools (Global Mode)',
            'school_code' => 'GLOBAL',
            'academic_session' => null,
            'students' => [
                'total' => $totalStudents,
                'active' => $activeStudents,
                'inactive' => $inactiveStudents,
                'male' => $maleStudents,
                'female' => $femaleStudents,
                'other' => $otherStudents,
            ],
            'teachers' => [
                'total' => $totalTeachers,
                'active' => $activeTeachers,
                'inactive' => $inactiveTeachers,
            ],
            'academic' => [
                'classes' => $totalClasses,
                'active_classes' => $activeClasses,
                'sections' => $totalSections,
                'active_sections' => $activeSections,
                'subjects' => $totalSubjects,
            ],
            'exams' => [
                'total' => $totalExams,
                'published' => $publishedExams,
                'upcoming' => $upcomingExams,
            ],
            'fees' => [
                'collected' => $feeCollected,
                'due' => $totalFeeDue,
                'pending_students' => 0,
                'today' => $todayFeeCollection,
                'monthly' => $monthlyFees,
            ],
            'finance' => [
                'expenses' => $totalExpenses,
                'today_expenses' => $todayExpenses,
                'other_income' => $otherIncome,
                'today_other_income' => $todayOtherIncome,
                'monthly_income' => $monthlyTotalIncome,
                'monthly_expenses' => $monthlyExpenses,
                'monthly_balance' => $monthlyBalance,
            ],
            'student_attendance' => [
                'present' => $studentPresent,
                'absent' => $studentAbsent,
                'late' => $studentLate,
                'leave' => $studentLeave,
            ],
            'teacher_attendance' => [
                'present' => $teacherPresent,
                'absent' => $teacherAbsent,
                'late' => $teacherLate,
            ],
            'hostel' => [
                'buildings' => HostelBuilding::withoutGlobalScopes()->count(),
                'rooms' => HostelRoom::withoutGlobalScopes()->count(),
                'total_capacity' => 0,
                'occupied_beds' => 0,
                'available_beds' => 0,
            ],
            'inventory' => [
                'total_items' => 0,
                'low_stock' => 0,
                'out_of_stock' => 0,
            ],
            'transport' => [
                'vehicles' => 0,
                'active_vehicles' => 0,
                'routes' => 0,
                'assigned_students' => 0,
            ],
            'recent_students' => $recentStudents,
            'upcoming_exam_list' => $upcomingExamList,
            'global' => [
                'total_schools' => School::count(),
                'active_schools' => School::where('status', 1)->count(),
                'inactive_schools' => School::where('status', 0)->count(),
                'schools' => $schoolList,
            ],
        ];
    }
}