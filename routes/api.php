<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\SchoolController;
use App\Http\Controllers\API\AcademicSessionController;
use App\Http\Controllers\API\SchoolClassController;
use App\Http\Controllers\API\SubjectController;
use App\Http\Controllers\API\SectionController;
use App\Http\Controllers\API\TimetableController;
use App\Http\Controllers\API\TeacherController;
use App\Http\Controllers\API\LeaveTypeController;
use App\Http\Controllers\API\LeaveRequestController;
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\GuardianController;
use App\Http\Controllers\Api\StudentPromotionController;
use App\Http\Controllers\Api\ExamTypeController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamScheduleController;
use App\Http\Controllers\Api\ExamSubjectController;
use App\Http\Controllers\Api\ExamMarkController;
use App\Http\Controllers\Api\GradeController;
use App\Http\Controllers\Api\ReportCardController;
use App\Http\Controllers\Api\ResultGradeController;
use App\Http\Controllers\Api\ExamRankController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get(
    'academic-sessions/active',
    [AcademicSessionController::class, 'active']
    );

    Route::apiResource(
        'academic-sessions',
        AcademicSessionController::class
    );

    Route::get(
    'classes/active',
    [SchoolClassController::class, 'active']
    );
    Route::apiResource('classes', SchoolClassController::class);

    Route::get(
    'subjects/active',
    [SubjectController::class, 'active']
    );
    Route::apiResource('subjects', SubjectController::class);

    Route::apiResource('timetables', TimetableController::class);
    Route::get(
    'sections/active',
    [SectionController::class, 'active']
    );
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('schools', SchoolController::class);
    Route::get(
    'teachers/active',
    [TeacherController::class, 'active']
    );
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('leave-types', LeaveTypeController::class);
    Route::apiResource('teacher-leaves', LeaveRequestController::class);
    Route::post('teacher-leaves/{id}/approve', [LeaveRequestController::class, 'approve']);
    Route::post('teacher-leaves/{id}/reject', [LeaveRequestController::class, 'reject']);
    Route::apiResource('teacher-attendances', TeacherAttendanceController::class);
    Route::apiResource('students', StudentController::class);
    Route::get('students-active', [StudentController::class, 'active']);
    Route::get('students-by-class', [StudentController::class, 'byClass']);

    Route::apiResource('student-attendances', StudentAttendanceController::class);
    Route::post('student-attendances/bulk', [StudentAttendanceController::class, 'bulkStore']);
    Route::get('student-attendances/monthly', [StudentAttendanceController::class, 'monthly']);
    Route::get('student-attendances/summary', [StudentAttendanceController::class, 'summary']);
    Route::apiResource('guardians', GuardianController::class);
    Route::apiResource('student-promotions', StudentPromotionController::class);
    Route::post('student-promotions/bulk', [StudentPromotionController::class, 'bulkPromotion']);
    //exam

    Route::get('exam-types/active', [ExamTypeController::class, 'active']);
    Route::apiResource('exam-types', ExamTypeController::class);

    Route::get('exams/active', [ExamController::class, 'active']);
    Route::patch('exams/{exam}/lock-marks', [ExamController::class, 'lockMarks']);
    Route::patch('exams/{exam}/unlock-marks', [ExamController::class, 'unlockMarks']);
    Route::patch('exams/{exam}/publish-result', [ExamController::class, 'publishResult']);
    Route::patch('exams/{exam}/unpublish-result', [ExamController::class, 'unpublishResult']);

    Route::apiResource('exams', ExamController::class);


Route::get(
    'exam-schedules/active',
    [ExamScheduleController::class, 'active']
);

Route::apiResource(
    'exam-schedules',
    ExamScheduleController::class
);


Route::get(
    'exam-subjects/active',
    [ExamSubjectController::class, 'active']
);

Route::get(
    'exam-subjects/exam/{exam}/class/{class}',
    [ExamSubjectController::class, 'byExamAndClass']
);

Route::apiResource(
    'exam-subjects',
    ExamSubjectController::class
);



Route::prefix('exam-marks')->group(function () {

    // Load marks entry grid
    Route::get(
        'load',
        [ExamMarkController::class, 'load']
    );

    // Save all marks
    Route::post(
        'save',
        [ExamMarkController::class, 'save']
    );

    // View a student's marks
    Route::get(
        'student/{student}',
        [ExamMarkController::class, 'student']
    );

    // Lock Marks
    Route::patch(
        'lock/{exam}',
        [ExamMarkController::class, 'lock']
    );

    // Unlock Marks
    Route::patch(
        'unlock/{exam}',
        [ExamMarkController::class, 'unlock']
    );

});



Route::get(
    'grades/active',
    [GradeController::class, 'active']
);

Route::apiResource(
    'grades',
    GradeController::class
);

Route::get(
    'report-cards/student/{student}/exam/{exam}',
    [ReportCardController::class, 'student']
);
    // Optional restore route
    Route::post('schools/{id}/restore', [SchoolController::class, 'restore'])->name('schools.restore');
    Route::get(
    'result-grades/active',
    [ResultGradeController::class, 'active']
);

Route::apiResource(
    'result-grades',
    ResultGradeController::class
);



Route::middleware('auth:sanctum')->group(function () {

    Route::post(
        '/exam-ranks/calculate',
        [ExamRankController::class, 'calculate']
    );

});

});
