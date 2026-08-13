export default [
    {
        title: "Dashboard",
        icon: "bi bi-speedometer2",
        route: "/dashboard",
    },

    {
        title: "School Management",
        icon: "bi bi-building",
        children: [
            {
                title: "School",
                icon: "bi bi-building-fill",
                route: "/school",
                permission: "school.view",
            },
            {
                title: "Academic Sessions",
                icon: "bi bi-calendar3",
                route: "/academic-sessions",
                permission: "academic_session.view",
            },
        ],
    },

    {
        title: "Academic",
        icon: "bi bi-book",
        children: [
            {
                title: "Classes",
                route: "/classes",
                permission: "class.view",
            },
            {
                title: "Sections",
                route: "/sections",
                permission: "section.view",
            },
            {
                title: "Subjects",
                route: "/subjects",
                permission: "subject.view",
            },
            {
                title: "Timetables",
                route: "/timetables",
                permission: "timetable.view",
            },
        ],
    },

    {
        title: "Students",
        icon: "bi bi-mortarboard",
        children: [
            {
                title: "Students",
                route: "/students",
                permission: "student.view",
            },
            {
                title: "Attendance",
                route: "/student-attendance",
                permission: "attendance.view",
            },
            {
                title: "Guardians",
                route: "/guardians",
                permission: "guardian.view",
            },
            {
                title: "Promotions",
                route: "/student-promotions",
                permission: "promotion.view",
            },
        ],
    },

    {
        title: "Teachers",
        icon: "bi bi-person-workspace",
        children: [
            {
                title: "Teachers",
                route: "/teachers",
                permission: "teacher.view",
            },
            {
                title: "Leave Requests",
                route: "/teacher-leaves",
                permission: "leave.view",
            },
            {
                title: "Leave Types",
                route: "/leave-types",
                permission: "leave_types.view",
            },
            {
                title: "Attendance",
                route: "/teacher-attendances",
                permission: "teacher_attendance.view",
            }
        ],
    },

    {
        title: "Examinations",
        icon: "bi bi-pencil-square",
        children: [
            {
                title: "Exam Types",
                route: "/exam-types",
                permission: "exam_types.view",
            },
            {
                title: "Exam Subjects",
                route: "/exam-subjects",
                permission: "exam_subjects.view",
            },
            {
                title: "Exam Schedule",
                route: "/exam-schedules",
                permission: "exam_schedules.view",
            },
            {
                title: "Exams",
                route: "/exams",
                permission: "exam.view",
            },{
                title: "Exam Marks",
                route: "/exam-marks",
                permission: "exam_marks.view",
            },
            {
                title: "Grades",
                route: "/grades",
                permission: "grade.view",
            },
            {
                title: "Result Grades",
                route: "/result-grades",
                permission: "result_grades.view",
            },
            {
                title: "Report Cards",
                route: "/report-cards",
                permission: "report_card.view",
            },
            {
                title: "Exam Ranks",
                route: "/exam-ranks",
                permission: "exam_ranks.index",
            },
        ],
    },

    {
        title: "Fees & Accounts",
        icon: "bi bi-cash-stack",
        children: [
            {
                title: "Fee Types",
                route: "/fee-types",
                permission: "fee_types.view",
            },{
                title: "Fee Structures",
                route: "/fee-structures",
                permission: "fee_structures.view",
            },
            {
                title: "Fee Dues",
                route: "/fee-dues",
                permission: "fee-dues.index",
            },
            {
                title: "Fee Assignments",
                route: "/fee-assignments",
                permission: "fee-assignments.index",
            },
            {
                title: "Fee Collection",
                route: "/fee-collection",
                permission: "fee-collection.index",
            },
            {
                title: "Payment History",
                route: "/payment-history",
                permission: "payment-history.index",
            },
            {
                title: "Fee Receipts",
                route: "/fee-receipts",
                permission: "fee-receipts.index",
            },
            {
                title: "Discounts",
                route: "/discounts",
                permission: "discounts.index",
            },
            {
                title: "Expenses",
                route: "/expenses",
                permission: "expense.view",
            },
            {
                title: "Income",
                route: "/income",
                permission: "income.view",
            },
        ],
    },

    {
        title: "Library",
        icon: "bi bi-journal-bookmark",
        children: [
            {
                title: "Books",
                route: "/library/books",
                permission: "library.view",
            },
            {
                title: "Categories",
                route: "/library/categories",
                permission: "book_category.view",
            },
        ],
    },

    {
        title: "Inventory",
        icon: "bi bi-box-seam",
        children: [
            {
                title: "Items",
                route: "/inventory/items",
                permission: "inventory.view",
            },
            {
                title: "Suppliers",
                route: "/inventory/suppliers",
                permission: "supplier.view",
            },
            {
                title: "Purchases",
                route: "/inventory/purchases",
                permission: "purchase.view",
            },
        ],
    },

    {
        title: "Transport",
        icon: "bi bi-bus-front",
        route: "/transport",
        permission: "transport.view",
    },

    {
        title: "Hostel",
        icon: "bi bi-house-door",
        route: "/hostels",
        permission: "hostel.view",
    },

    {
        title: "Reports",
        icon: "bi bi-graph-up",
        route: "/reports",
        permission: "report.view",
    },

    {
        title: "Administration",
        icon: "bi bi-shield-lock",
        children: [
            {
                title: "Users",
                route: "/users",
                permission: "user.view",
            },
            {
                title: "Roles",
                route: "/roles",
                permission: "role.view",
            },
            {
                title: "Permissions",
                route: "/permissions",
                permission: "permission.view",
            },
        ],
    },

    {
        title: "Settings",
        icon: "bi bi-gear",
        route: "/settings",
        permission: "settings.view",
    },
];