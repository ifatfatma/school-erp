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
use App\Http\Controllers\Api\LibraryReportController;
use App\Http\Controllers\Api\Inventory\InventoryItemController;
use App\Http\Controllers\Api\Inventory\InventoryPurchaseController;
use App\Http\Controllers\Api\Inventory\InventorySupplierController;
use App\Http\Controllers\Api\Inventory\InventoryStockMovementController;
use App\Http\Controllers\Api\Inventory\InventoryCategoryController;
use App\Http\Controllers\Api\Inventory\InventoryUnitController;
use App\Http\Controllers\Api\Inventory\InventoryStockInController;
use App\Http\Controllers\Api\Inventory\InventoryStockOutController;
use App\Http\Controllers\Api\Inventory\InventoryStockAdjustmentController;
use App\Http\Controllers\Api\Inventory\InventoryDashboardController;
use App\Http\Controllers\Api\Transport\TransportVehicleController;
use App\Http\Controllers\Api\Transport\TransportDriverController;
use App\Http\Controllers\Api\Transport\TransportRouteController;
use App\Http\Controllers\Api\Transport\TransportStudentController;
use App\Http\Controllers\Api\Transport\TransportRouteStopController;
use App\Http\Controllers\Api\Transport\TransportAssignmentController;
use App\Http\Controllers\Api\Transport\TransportFeeController;
use App\Http\Controllers\Api\Transport\TransportVehicleMaintenanceController;
use App\Http\Controllers\Api\Transport\TransportExpenseController;
use App\Http\Controllers\Api\Transport\TransportReportController;
use App\Http\Controllers\Api\Administrator\RoleController;
use App\Http\Controllers\Api\Administrator\UserController;
use App\Http\Controllers\Api\Administrator\PermissionController;
use App\Http\Controllers\Api\Hostel\HostelBuildingController;
use App\Http\Controllers\Api\Hostel\HostelRoomController;

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
    Route::prefix('library-reports')->group(function () {

        Route::get(
            '/dashboard',
            [LibraryReportController::class, 'dashboard']
        );
        Route::get(
            '/issued-books',
            [LibraryReportController::class, 'issuedBooks']
        );

        Route::get(
            '/returned-books',
            [LibraryReportController::class, 'returnedBooks']
        );

        Route::get(
            '/overdue-books',
            [LibraryReportController::class, 'overdueBooks']
        );

        Route::get(
            '/renewals',
            [LibraryReportController::class, 'renewals']
        );

        Route::get(
            '/lost-damaged',
            [LibraryReportController::class, 'lostDamaged']
        );

        Route::get(
            '/outstanding-fines',
            [LibraryReportController::class, 'outstandingFines']
        );

        Route::get(
            '/fine-collection',
            [LibraryReportController::class, 'fineCollection']
        );
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
    Route::prefix('inventory/items')
        ->group(function () {

            Route::get(
                '/',
                [InventoryItemController::class, 'index']
            );

            Route::post(
                '/',
                [InventoryItemController::class, 'store']
            );

            Route::get(
                '/search',
                [InventoryItemController::class, 'search']
            );

            Route::get(
                '/low-stock',
                [InventoryItemController::class, 'lowStock']
            );

            Route::get(
                '/{id}',
                [InventoryItemController::class, 'show']
            );

            Route::put(
                '/{id}',
                [InventoryItemController::class, 'update']
            );

            Route::delete(
                '/{id}',
                [InventoryItemController::class, 'destroy']
            );
        });

    Route::prefix('inventory/purchases')
        ->group(function () {

            Route::get(
                '/',
                [InventoryPurchaseController::class, 'index']
            );

            Route::post(
                '/',
                [InventoryPurchaseController::class, 'store']
            );

            Route::get(
                '/{id}',
                [InventoryPurchaseController::class, 'show']
            );

            Route::delete(
                '/{id}',
                [InventoryPurchaseController::class, 'destroy']
            );
        });


    Route::prefix('inventory/suppliers')
        ->group(function () {

            Route::get(
                '/',
                [InventorySupplierController::class, 'index']
            );

            Route::post(
                '/',
                [InventorySupplierController::class, 'store']
            );

            Route::get(
                '/{id}',
                [InventorySupplierController::class, 'show']
            );

            Route::put(
                '/{id}',
                [InventorySupplierController::class, 'update']
            );

            Route::delete(
                '/{id}',
                [InventorySupplierController::class, 'destroy']
            );
        });


    Route::prefix('inventory/stock-movements')
        ->group(function () {

            Route::get(
                '/',
                [InventoryStockMovementController::class, 'index']
            );

            Route::get(
                '/item/{itemId}',
                [InventoryStockMovementController::class, 'itemLedger']
            );

            Route::get(
                '/{id}',
                [InventoryStockMovementController::class, 'show']
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Inventory Categories
    |--------------------------------------------------------------------------
    */

    Route::prefix('inventory/categories')->group(function () {

        Route::get('/', [
            InventoryCategoryController::class,
            'index'
        ]);

        Route::post('/', [
            InventoryCategoryController::class,
            'store'
        ]);

        Route::get('/{id}', [
            InventoryCategoryController::class,
            'show'
        ]);

        Route::put('/{id}', [
            InventoryCategoryController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            InventoryCategoryController::class,
            'destroy'
        ]);
    });
    Route::prefix('inventory/units')->group(function () {

        Route::get('/', [
            InventoryUnitController::class,
            'index'
        ]);

        Route::post('/', [
            InventoryUnitController::class,
            'store'
        ]);

        Route::get('/{id}', [
            InventoryUnitController::class,
            'show'
        ]);

        Route::put('/{id}', [
            InventoryUnitController::class,
            'update'
        ]);

        Route::post('/{id}/status', [
            InventoryUnitController::class,
            'status'
        ]);

        Route::delete('/{id}', [
            InventoryUnitController::class,
            'destroy'
        ]);
    });


    Route::prefix('inventory/stock-ins')->group(function () {

        Route::get('/form-data', [
            InventoryStockInController::class,
            'formData'
        ]);

        Route::get('/', [
            InventoryStockInController::class,
            'index'
        ]);

        Route::post('/', [
            InventoryStockInController::class,
            'store'
        ]);

        Route::get('/{id}', [
            InventoryStockInController::class,
            'show'
        ]);

        Route::put('/{id}', [
            InventoryStockInController::class,
            'update'
        ]);

        Route::delete('/{id}', [
            InventoryStockInController::class,
            'destroy'
        ]);
    });

    Route::prefix('inventory/stock-outs')->group(function () {

    Route::get(
        '/form-data',
        [
            InventoryStockOutController::class,
            'formData'
        ]
    );

    Route::get(
        '/',
        [
            InventoryStockOutController::class,
            'index'
        ]
    );

    Route::post(
        '/',
        [
            InventoryStockOutController::class,
            'store'
        ]
    );

    Route::get(
        '/{id}',
        [
            InventoryStockOutController::class,
            'show'
        ]
    );

});



Route::prefix('inventory/stock-adjustments')->group(function () {

    Route::get(
        '/items',
        [
            InventoryStockAdjustmentController::class,
            'items'
        ]
    );

    Route::get(
        '/',
        [
            InventoryStockAdjustmentController::class,
            'index'
        ]
    );

    Route::post(
        '/',
        [
            InventoryStockAdjustmentController::class,
            'store'
        ]
    );

});

Route::prefix('inventory/dashboard')->group(function () {

    Route::get(
        '/',
        [
            InventoryDashboardController::class,
            'index'
        ]
    );

    Route::get(
        '/summary',
        [
            InventoryDashboardController::class,
            'summary'
        ]
    );

    Route::get(
        '/categories',
        [
            InventoryDashboardController::class,
            'categories'
        ]
    );

});


Route::prefix('transport/vehicles')->group(function () {

    Route::get(
        '/',
        [
            TransportVehicleController::class,
            'index'
        ]
    );

    Route::post(
        '/',
        [
            TransportVehicleController::class,
            'store'
        ]
    );

    Route::get(
        '/{id}',
        [
            TransportVehicleController::class,
            'show'
        ]
    );

    Route::put(
        '/{id}',
        [
            TransportVehicleController::class,
            'update'
        ]
    );

    Route::delete(
        '/{id}',
        [
            TransportVehicleController::class,
            'destroy'
        ]
    );

    Route::post(
        '/{id}/status',
        [
            TransportVehicleController::class,
            'status'
        ]
    );

});

Route::prefix('transport/drivers')->group(function () {

    Route::get(
        '/',
        [TransportDriverController::class, 'index']
    );

    Route::post(
        '/',
        [TransportDriverController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportDriverController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportDriverController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportDriverController::class, 'destroy']
    );

    Route::post(
        '/{id}/status',
        [TransportDriverController::class, 'status']
    );

});


Route::prefix('transport/routes')->group(function () {

    Route::get(
        '/',
        [TransportRouteController::class, 'index']
    );

    Route::post(
        '/',
        [TransportRouteController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportRouteController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportRouteController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportRouteController::class, 'destroy']
    );

    Route::post(
        '/{id}/status',
        [TransportRouteController::class, 'status']
    );

});


Route::prefix('transport/students')->group(function () {

    // OPTIONS MUST COME BEFORE /{id}
    Route::get(
        '/options',
        [TransportStudentController::class, 'options']
    );

    Route::get(
        '/route/{routeId}/stops',
        [TransportStudentController::class, 'routeStops']
    );

    Route::get(
        '/',
        [TransportStudentController::class, 'index']
    );

    Route::post(
        '/',
        [TransportStudentController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportStudentController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportStudentController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportStudentController::class, 'destroy']
    );

    Route::post(
        '/{id}/status',
        [TransportStudentController::class, 'status']
    );

});



Route::prefix('transport/stops')->group(function () {

    Route::get(
        '/',
        [TransportRouteStopController::class, 'index']
    );

    Route::get(
        '/options',
        [TransportRouteStopController::class, 'options']
    );

    Route::post(
        '/',
        [TransportRouteStopController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportRouteStopController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportRouteStopController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportRouteStopController::class, 'destroy']
    );

});


Route::prefix('transport/assignments')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Fixed routes MUST come before /{id}
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/options',
        [TransportAssignmentController::class, 'options']
    );

    Route::get(
        '/route/{routeId}/stops',
        [TransportAssignmentController::class, 'routeStops']
    );

    Route::get(
        '/',
        [TransportAssignmentController::class, 'index']
    );

    Route::post(
        '/',
        [TransportAssignmentController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportAssignmentController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportAssignmentController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportAssignmentController::class, 'destroy']
    );

    Route::post(
        '/{id}/status',
        [TransportAssignmentController::class, 'status']
    );

});



Route::prefix('transport/fees')->group(function () {

    Route::get(
        '/options',
        [TransportFeeController::class, 'options']
    );

    Route::get(
        '/student/{studentId}/assignments',
        [TransportFeeController::class, 'studentAssignments']
    );

    Route::post(
        '/generate',
        [TransportFeeController::class, 'generate']
    );

    Route::get(
        '/',
        [TransportFeeController::class, 'index']
    );

    Route::post(
        '/',
        [TransportFeeController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportFeeController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportFeeController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportFeeController::class, 'destroy']
    );

});


Route::prefix('transport/maintenance')->group(function () {

    Route::get(
        '/options',
        [TransportVehicleMaintenanceController::class, 'options']
    );

    Route::get(
        '/',
        [TransportVehicleMaintenanceController::class, 'index']
    );

    Route::post(
        '/',
        [TransportVehicleMaintenanceController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportVehicleMaintenanceController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportVehicleMaintenanceController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportVehicleMaintenanceController::class, 'destroy']
    );

});


Route::prefix('transport/expenses')->group(function () {

    Route::get(
        '/options',
        [TransportExpenseController::class, 'options']
    );

    Route::get(
        '/',
        [TransportExpenseController::class, 'index']
    );

    Route::post(
        '/',
        [TransportExpenseController::class, 'store']
    );

    Route::get(
        '/{id}',
        [TransportExpenseController::class, 'show']
    );

    Route::put(
        '/{id}',
        [TransportExpenseController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [TransportExpenseController::class, 'destroy']
    );

});

Route::prefix('transport/reports')->group(function () {

    Route::get(
        '/options',
        [TransportReportController::class, 'options']
    );

    Route::get(
        '/summary',
        [TransportReportController::class, 'summary']
    );

    Route::get(
        '/vehicles',
        [TransportReportController::class, 'vehicles']
    );

    Route::get(
        '/drivers',
        [TransportReportController::class, 'drivers']
    );

    Route::get(
        '/maintenance',
        [TransportReportController::class, 'maintenance']
    );

    Route::get(
        '/fuel',
        [TransportReportController::class, 'fuel']
    );

    Route::get(
        '/expenses',
        [TransportReportController::class, 'expenses']
    );

});

Route::prefix('administrator/roles')->group(function () {

    Route::get(
        '/',
        [RoleController::class, 'index']
    );

    Route::get(
        '/options',
        [RoleController::class, 'options']
    );

    Route::post(
        '/',
        [RoleController::class, 'store']
    );

    Route::get(
        '/{id}',
        [RoleController::class, 'show']
    );

    Route::put(
        '/{id}',
        [RoleController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [RoleController::class, 'destroy']
    );

    Route::get(
        '/{id}/permissions',
        [RoleController::class, 'permissions']
    );

    Route::put(
        '/{id}/permissions',
        [RoleController::class, 'updatePermissions']
    );

});

Route::prefix('administrator/users')->group(function () {

    Route::get(
        '/',
        [UserController::class, 'index']
    );

    Route::get(
        '/options',
        [UserController::class, 'options']
    );

    Route::post(
        '/',
        [UserController::class, 'store']
    );

    Route::get(
        '/{id}',
        [UserController::class, 'show']
    );

    Route::put(
        '/{id}',
        [UserController::class, 'update']
    );

    Route::delete(
        '/{id}',
        [UserController::class, 'destroy']
    );

    Route::get(
        '/{id}/roles',
        [UserController::class, 'roles']
    );

    Route::put(
        '/{id}/roles',
        [UserController::class, 'updateRoles']
    );

});

Route::middleware('auth:sanctum')
    ->prefix('administrator/permissions')
    ->group(function () {

        Route::get(
            '/',
            [PermissionController::class, 'index']
        );

        Route::get(
            '/options',
            [PermissionController::class, 'options']
        );

        Route::post(
            '/',
            [PermissionController::class, 'store']
        );

        Route::get(
            '/{id}',
            [PermissionController::class, 'show']
        );

        Route::put(
            '/{id}',
            [PermissionController::class, 'update']
        );

        Route::delete(
            '/{id}',
            [PermissionController::class, 'destroy']
        );

    });


    Route::prefix('hostel')->group(function () {

    Route::get('/buildings', [
        HostelBuildingController::class,
        'index'
    ]);

    Route::post('/buildings', [
        HostelBuildingController::class,
        'store'
    ]);

    Route::get('/buildings/{id}', [
        HostelBuildingController::class,
        'show'
    ]);

    Route::put('/buildings/{id}', [
        HostelBuildingController::class,
        'update'
    ]);

    Route::delete('/buildings/{id}', [
        HostelBuildingController::class,
        'destroy'
    ]);

    Route::patch('/buildings/{id}/status', [
        HostelBuildingController::class,
        'toggleStatus'
    ]);

});

Route::prefix('hostel')
    ->group(function () {

    /*
|--------------------------------------------------------------------------
| HOSTEL ROOMS
|--------------------------------------------------------------------------
*/

Route::get(
    '/rooms/buildings',
    [
        HostelRoomController::class,
        'buildings'
    ]
);

Route::get(
    '/rooms',
    [
        HostelRoomController::class,
        'index'
    ]
);

Route::post(
    '/rooms',
    [
        HostelRoomController::class,
        'store'
    ]
);

Route::get(
    '/rooms/{id}',
    [
        HostelRoomController::class,
        'show'
    ]
)->whereNumber('id');

Route::put(
    '/rooms/{id}',
    [
        HostelRoomController::class,
        'update'
    ]
)->whereNumber('id');

Route::patch(
    '/rooms/{id}/status',
    [
        HostelRoomController::class,
        'toggleStatus'
    ]
)->whereNumber('id');

Route::delete(
    '/rooms/{id}',
    [
        HostelRoomController::class,
        'destroy'
    ]
)->whereNumber('id');

    });

});
