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
use App\Http\Controllers\Api\ReportCardController;
use App\Http\Controllers\Api\ResultGradeController;
use App\Http\Controllers\Api\ExamRankController;
use App\Http\Controllers\Api\FeeCollectionController;
use App\Http\Controllers\Api\FeeTypeController;
use App\Http\Controllers\Api\FeeStructureController;
use App\Http\Controllers\Api\FeeAssignmentController;
use App\Http\Controllers\Api\FeeDueController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\FeeReceiptController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\LateFeeController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\ExpensePaymentController;
use App\Http\Controllers\Api\RecurringExpenseController;
use App\Http\Controllers\Api\ExpenseReportController;
use App\Http\Controllers\Api\IncomeCategoryController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\IncomePaymentController;
use App\Http\Controllers\Api\RecurringIncomeController;
use App\Http\Controllers\Api\IncomeReportController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\PublisherController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookCopyController;
use App\Http\Controllers\Api\LibraryMemberController;
use App\Http\Controllers\Api\BookIssueController;
use App\Http\Controllers\Api\BookReturnController;
use App\Http\Controllers\Api\BookRenewalController;
use App\Http\Controllers\Api\SettingController;



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



Route::post(
        '/exam-ranks/calculate',
        [ExamRankController::class, 'calculate']
    );



Route::prefix('fee-collection')->group(function () {

    Route::get('/students', [
        FeeCollectionController::class,
        'students'
    ]);

    Route::get('/student/{student}/dues', [
        FeeCollectionController::class,
        'dues'
    ]);

    Route::post('/collect', [
        FeeCollectionController::class,
        'collect'
    ]);

    Route::get('/{payment}', [
        FeeCollectionController::class,
        'show'
    ]);

    Route::get('/{payment}/receipt', [
        FeeCollectionController::class,
        'receipt'
    ]);

});



Route::prefix('fee-types')->group(function () {

    Route::get('/', [
        FeeTypeController::class,
        'index'
    ]);

    Route::post('/', [
        FeeTypeController::class,
        'store'
    ]);

    Route::get('/{id}', [
        FeeTypeController::class,
        'show'
    ]);

    Route::put('/{id}', [
        FeeTypeController::class,
        'update'
    ]);

    Route::delete('/{id}', [
        FeeTypeController::class,
        'destroy'
    ]);

});


Route::prefix('fee-structures')->group(function () {

    Route::get('/', [
        FeeStructureController::class,
        'index'
    ]);

    Route::post('/', [
        FeeStructureController::class,
        'store'
    ]);

    Route::get('/{id}', [
        FeeStructureController::class,
        'show'
    ]);

    Route::put('/{id}', [
        FeeStructureController::class,
        'update'
    ]);

    Route::delete('/{id}', [
        FeeStructureController::class,
        'destroy'
    ]);

});


Route::prefix('fee-assignments')->group(function () {

    Route::get('/', [
        FeeAssignmentController::class,
        'index'
    ]);

    Route::get('/students', [
        FeeAssignmentController::class,
        'students'
    ]);

    Route::post('/assign', [
        FeeAssignmentController::class,
        'assign'
    ]);

    Route::delete('/{id}', [
        FeeAssignmentController::class,
        'destroy'
    ]);

});


Route::prefix('fee-dues')->group(function () {

    Route::get('/', [
        FeeDueController::class,
        'index'
    ]);

    Route::get('/summary', [
        FeeDueController::class,
        'summary'
    ]);

    Route::get('/{id}', [
        FeeDueController::class,
        'show'
    ]);

});



Route::prefix('payment-history')->group(function () {

    Route::get('/', [
        PaymentHistoryController::class,
        'index'
    ]);

    Route::get('/{id}', [
        PaymentHistoryController::class,
        'show'
    ]);

});


Route::prefix('fee-receipts')->group(function () {

        Route::get('/', [
            FeeReceiptController::class,
            'index'
        ]);

        Route::get('/{id}', [
            FeeReceiptController::class,
            'show'
        ]);

        Route::get('/{id}/pdf', [
            FeeReceiptController::class,
            'pdf'
        ]);

        Route::get('/{id}/download', [
            FeeReceiptController::class,
            'download'
        ]);

    });

    Route::prefix('discounts')->group(function () {

        Route::get('/', [
            DiscountController::class,
            'index'
        ]);

        Route::get('/{id}', [
            DiscountController::class,
            'show'
        ]);

        Route::post('/', [
            DiscountController::class,
            'store'
        ]);

        Route::put('/{id}', [
            DiscountController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            DiscountController::class,
            'destroy'
        ]);

        Route::patch('/{id}/toggle-status', [
            DiscountController::class,
            'toggleStatus'
        ]);

    });

    Route::prefix('late-fees')->group(function () {

        Route::get('/', [
            LateFeeController::class,
            'index'
        ]);

        Route::get('/{id}', [
            LateFeeController::class,
            'show'
        ]);

        Route::post('/', [
            LateFeeController::class,
            'store'
        ]);

        Route::put('/{id}', [
            LateFeeController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            LateFeeController::class,
            'destroy'
        ]);

        Route::patch('/{id}/toggle-status', [
            LateFeeController::class,
            'toggleStatus'
        ]);

    });

    Route::prefix('expense-categories')->group(function () {

        Route::get('/', [
            ExpenseCategoryController::class,
            'index'
        ]);

        Route::get('/{id}', [
            ExpenseCategoryController::class,
            'show'
        ]);

        Route::post('/', [
            ExpenseCategoryController::class,
            'store'
        ]);

        Route::put('/{id}', [
            ExpenseCategoryController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            ExpenseCategoryController::class,
            'destroy'
        ]);

        Route::patch('/{id}/toggle-status', [
            ExpenseCategoryController::class,
            'toggleStatus'
        ]);

    });

    Route::prefix('expenses')->group(function () {

        Route::get('/', [
            ExpenseController::class,
            'index'
        ]);

        Route::get('/total', [
            ExpenseController::class,
            'total'
        ]);

        Route::get('/{id}', [
            ExpenseController::class,
            'show'
        ]);

        Route::post('/', [
            ExpenseController::class,
            'store'
        ]);

        Route::put('/{id}', [
            ExpenseController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            ExpenseController::class,
            'destroy'
        ]);

        Route::patch('/{id}/status', [
            ExpenseController::class,
            'status'
        ]);

    });

    Route::prefix('expense-payments')->group(function () {

        Route::get('/', [
            ExpensePaymentController::class,
            'index'
        ]);

        Route::get('/{id}', [
            ExpensePaymentController::class,
            'show'
        ]);

        Route::post('/', [
            ExpensePaymentController::class,
            'store'
        ]);

        Route::put('/{id}', [
            ExpensePaymentController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            ExpensePaymentController::class,
            'destroy'
        ]);

    });


    /*
     * Keep this outside the {id} route
     * because expense ID is different from
     * payment ID.
     */
    Route::get(
        '/expenses/{expenseId}/payment-summary',
        [
            ExpensePaymentController::class,
            'summary'
        ]
    );


    Route::get(
        '/recurring-expenses',
        [RecurringExpenseController::class, 'index']
    );

    Route::post(
        '/recurring-expenses',
        [RecurringExpenseController::class, 'store']
    );

    Route::get(
        '/recurring-expenses/{id}',
        [RecurringExpenseController::class, 'show']
    );

    Route::put(
        '/recurring-expenses/{id}',
        [RecurringExpenseController::class, 'update']
    );

    Route::delete(
        '/recurring-expenses/{id}',
        [RecurringExpenseController::class, 'destroy']
    );

    Route::post(
        '/recurring-expenses/{id}/generate',
        [RecurringExpenseController::class, 'generate']
    );

    Route::post(
        '/recurring-expenses/{id}/status',
        [RecurringExpenseController::class, 'status']
    );

    Route::post(
        '/recurring-expenses/{id}/toggle-auto-create',
        [RecurringExpenseController::class, 'toggleAutoCreate']
    );

    Route::prefix('expense-reports')->group(function () {

        Route::get('/', [
            ExpenseReportController::class,
            'index'
        ]);

        Route::get('/category-summary', [
            ExpenseReportController::class,
            'categorySummary'
        ]);

        Route::get('/payment-method-summary', [
            ExpenseReportController::class,
            'paymentMethodSummary'
        ]);

        Route::get('/daily-summary', [
            ExpenseReportController::class,
            'dailySummary'
        ]);


    });


    Route::prefix('income-categories')->group(function () {

        Route::get('/', [
            IncomeCategoryController::class,
            'index'
        ]);

        /*
         * Keep /active before /{id}
         */
        Route::get('/active', [
            IncomeCategoryController::class,
            'active'
        ]);

        Route::post('/', [
            IncomeCategoryController::class,
            'store'
        ]);

        Route::get('/{id}', [
            IncomeCategoryController::class,
            'show'
        ]);

        Route::put('/{id}', [
            IncomeCategoryController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            IncomeCategoryController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            IncomeCategoryController::class,
            'status'
        ]);

    });



    Route::prefix('incomes')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | List
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            IncomeController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        | Must be before /{id}
        |--------------------------------------------------------------------------
        */

        Route::get('/categories', [
            IncomeController::class,
            'categories'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        Route::get('/summary', [
            IncomeController::class,
            'summary'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Route::post('/', [
            IncomeController::class,
            'store'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Show
        |--------------------------------------------------------------------------
        */

        Route::get('/{id}', [
            IncomeController::class,
            'show'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Route::put('/{id}', [
            IncomeController::class,
            'update'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/{id}', [
            IncomeController::class,
            'destroy'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        Route::post('/{id}/status', [
            IncomeController::class,
            'status'
        ]);

    });



    Route::prefix('income-payments')->group(function () {

        Route::get('/', [
            IncomePaymentController::class,
            'index'
        ]);

        Route::get('/summary', [
            IncomePaymentController::class,
            'summary'
        ]);

        Route::get('/remaining/{incomeId}', [
            IncomePaymentController::class,
            'remaining'
        ]);

        Route::post('/', [
            IncomePaymentController::class,
            'store'
        ]);

        Route::get('/{id}', [
            IncomePaymentController::class,
            'show'
        ]);

        Route::put('/{id}', [
            IncomePaymentController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            IncomePaymentController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            IncomePaymentController::class,
            'status'
        ]);
    });



    Route::prefix('recurring-incomes')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | List
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            RecurringIncomeController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Generate Due
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | This must be before /{id}
        |--------------------------------------------------------------------------
        */

        Route::post('/generate-due', [
            RecurringIncomeController::class,
            'generateDue'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Route::post('/', [
            RecurringIncomeController::class,
            'store'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Show
        |--------------------------------------------------------------------------
        */

        Route::get('/{id}', [
            RecurringIncomeController::class,
            'show'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Route::put('/{id}', [
            RecurringIncomeController::class,
            'update'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/{id}', [
            RecurringIncomeController::class,
            'destroy'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        Route::post('/{id}/status', [
            RecurringIncomeController::class,
            'status'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Generate Income
        |--------------------------------------------------------------------------
        */

        Route::post('/{id}/generate', [
            RecurringIncomeController::class,
            'generate'
        ]);

    });



    Route::prefix('income-reports')->group(function () {

        Route::get('/summary', [
            IncomeReportController::class,
            'summary'
        ]);

        Route::get('/daily', [
            IncomeReportController::class,
            'daily'
        ]);

        Route::get('/monthly', [
            IncomeReportController::class,
            'monthly'
        ]);

        Route::get('/yearly', [
            IncomeReportController::class,
            'yearly'
        ]);

        Route::get('/category-wise', [
            IncomeReportController::class,
            'categoryWise'
        ]);

        Route::get('/payment-method-wise', [
            IncomeReportController::class,
            'paymentMethodWise'
        ]);




    });




    Route::prefix('authors')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | List
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            AuthorController::class,
            'index'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Active Authors
        |--------------------------------------------------------------------------
        | IMPORTANT: Keep this BEFORE /{id}
        |--------------------------------------------------------------------------
        */

        Route::get('/active', [
            AuthorController::class,
            'active'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Route::post('/', [
            AuthorController::class,
            'store'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Show
        |--------------------------------------------------------------------------
        */

        Route::get('/{id}', [
            AuthorController::class,
            'show'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Route::put('/{id}', [
            AuthorController::class,
            'update'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Delete
        |--------------------------------------------------------------------------
        */

        Route::delete('/{id}', [
            AuthorController::class,
            'destroy'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        Route::post('/{id}/status', [
            AuthorController::class,
            'status'
        ]);

    });



    Route::prefix('book-categories')->group(function () {

        Route::get('/', [
            BookCategoryController::class,
            'index'
        ]);

        Route::get('/active', [
            BookCategoryController::class,
            'active'
        ]);

        Route::post('/', [
            BookCategoryController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookCategoryController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookCategoryController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookCategoryController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            BookCategoryController::class,
            'status'
        ]);

    });



    Route::prefix('publishers')->group(function () {

        Route::get('/', [
            PublisherController::class,
            'index'
        ]);

        /*
         * Must be before /{id}
         */
        Route::get('/active', [
            PublisherController::class,
            'active'
        ]);

        Route::post('/', [
            PublisherController::class,
            'store'
        ]);

        Route::get('/{id}', [
            PublisherController::class,
            'show'
        ]);

        Route::put('/{id}', [
            PublisherController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            PublisherController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            PublisherController::class,
            'status'
        ]);

    });




    Route::prefix('books')->group(function () {

        Route::get('/', [
            BookController::class,
            'index'
        ]);

        // Keep before /{id}
        Route::get('/active', [
            BookController::class,
            'active'
        ]);

        Route::post('/', [
            BookController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            BookController::class,
            'status'
        ]);

    });



    Route::prefix('book-copies')->group(function () {

        Route::get('/', [
            BookCopyController::class,
            'index'
        ]);

        /*
         * These must come BEFORE /{id}
         */
        Route::get('/available', [
            BookCopyController::class,
            'available'
        ]);

        Route::get('/statistics', [
            BookCopyController::class,
            'statistics'
        ]);

        Route::post('/barcode', [
            BookCopyController::class,
            'barcode'
        ]);

        Route::post('/accession', [
            BookCopyController::class,
            'accession'
        ]);

        Route::post('/', [
            BookCopyController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookCopyController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookCopyController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookCopyController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            BookCopyController::class,
            'status'
        ]);

        Route::post('/{id}/condition', [
            BookCopyController::class,
            'condition'
        ]);

    });



    Route::prefix('library-members')->group(function () {

        Route::get('/', [
            LibraryMemberController::class,
            'index'
        ]);

        /*
         * Keep these before /{id}
         */
        Route::get('/active', [
            LibraryMemberController::class,
            'active'
        ]);

        Route::get('/statistics', [
            LibraryMemberController::class,
            'statistics'
        ]);

        Route::post('/', [
            LibraryMemberController::class,
            'store'
        ]);

        Route::get('/{id}', [
            LibraryMemberController::class,
            'show'
        ]);

        Route::put('/{id}', [
            LibraryMemberController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            LibraryMemberController::class,
            'destroy'
        ]);

        Route::post('/{id}/status', [
            LibraryMemberController::class,
            'status'
        ]);

    });




    Route::prefix('book-issues')->group(function () {

        Route::get('/', [
            BookIssueController::class,
            'index'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Specific routes BEFORE /{id}
        |--------------------------------------------------------------------------
        */

        Route::get('/statistics', [
            BookIssueController::class,
            'statistics'
        ]);

        Route::get('/overdue', [
            BookIssueController::class,
            'overdue'
        ]);

        Route::get('/member/{memberId}', [
            BookIssueController::class,
            'memberIssues'
        ]);

        Route::post('/', [
            BookIssueController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookIssueController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookIssueController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookIssueController::class,
            'destroy'
        ]);

    });



    Route::prefix('book-returns')->group(function () {

        Route::get('/', [
            BookReturnController::class,
            'index'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Specific routes must come before /{id}
        |--------------------------------------------------------------------------
        */

        Route::get('/statistics', [
            BookReturnController::class,
            'statistics'
        ]);

        Route::get('/pending-fines', [
            BookReturnController::class,
            'pendingFines'
        ]);

        Route::get('/member/{memberId}', [
            BookReturnController::class,
            'memberReturns'
        ]);

        Route::post('/', [
            BookReturnController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookReturnController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookReturnController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookReturnController::class,
            'destroy'
        ]);

    });



    Route::prefix('book-renewals')->group(function () {

        Route::get('/', [
            BookRenewalController::class,
            'index'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Specific routes BEFORE /{id}
        |--------------------------------------------------------------------------
        */

        Route::get('/statistics', [
            BookRenewalController::class,
            'statistics'
        ]);

        Route::get('/member/{memberId}', [
            BookRenewalController::class,
            'memberRenewals'
        ]);

        Route::get('/issue/{issueId}', [
            BookRenewalController::class,
            'issueRenewals'
        ]);

        Route::post('/', [
            BookRenewalController::class,
            'store'
        ]);

        Route::get('/{id}', [
            BookRenewalController::class,
            'show'
        ]);

        Route::put('/{id}', [
            BookRenewalController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            BookRenewalController::class,
            'destroy'
        ]);

    });



    Route::prefix('settings')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | All Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            SettingController::class,
            'index',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Defaults
        |--------------------------------------------------------------------------
        */

        Route::post('/create-defaults', [
            SettingController::class,
            'createDefaults',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Group
        |--------------------------------------------------------------------------
        */

        Route::get('/group/{group}', [
            SettingController::class,
            'group',
        ]);

        Route::get('/group/{group}/details', [
            SettingController::class,
            'groupDetails',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Save Group
        |--------------------------------------------------------------------------
        */

        Route::put('/group/{group}', [
            SettingController::class,
            'saveGroup',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Single Setting
        |--------------------------------------------------------------------------
        */

        Route::get('/{group}/{key}', [
            SettingController::class,
            'show',
        ]);

        Route::post('/', [
            SettingController::class,
            'store',
        ]);

        Route::delete('/{group}/{key}', [
            SettingController::class,
            'destroy',
        ]);

    });



});
