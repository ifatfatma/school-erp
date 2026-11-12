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
        title: "Root Website CMS",
        icon: "bi bi-globe-americas",
        route: "/admin/root-website/manager",
        permission: "website_settings.view",
        superAdminOnly: true,
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
            {
                title: "Online Classes",
                route: "/online-classes",
                permission: "online_classes.view",
                feature: "online_classes",
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
        title: "Admissions & School Web",
        icon: "bi bi-file-earmark-person",
        children: [
            {
                title: "Applications",
                route: "/admin/admissions/applications",
                permission: "online_admissions.view",
                feature: "online_admissions",
            },
            {
                title: "Website Inquiries",
                route: "/admin/admissions/inquiries",
                permission: "website_inquiries.view",
                feature: "online_admissions",
            },
            {
                title: "School Website CMS",
                route: "/admin/website/manager",
                permission: "website_settings.view",
                feature: "website_cms",
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
                permission: "grade.view, result_grades.view",
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
                title: "Late Fees",
                route: "/late-fees",
                permission: "late-fees.index",
            },
            {
                title: "Fee Reports",
                route: "/fee-reports",
                permission: "fee-reports.index",
            }
        ],
    },
     {
        title: "Expense Management",
        icon: "bi bi-cash-stack",
        children: [
            {
                title: "Expense Categories",
                route: "/expense-categories",
                permission: "expense_categories.view",
            },
            {
                title: "Expenses",
                route: "/expenses",
                permission: "expenses.view",
            },
            {
                title: "Expense Payments",
                route: "/expense-payments",
                permission: "expense_payments.view",
            },{
                title: "Recurring Expenses",
                route: "/recurring-expenses",
                permission: "recurring_expenses.view",
            },{
                title: "Expense Reports",
                route: "/expense-reports",
                permission: "expense_reports.view",
            },
        ]
    },
    ,
     {
        title: "Income Management",
        icon: "bi bi-cash-stack",
        children: [
                {
                    title: "Income Categories",
                    route: "/income-categories",
                    permission: "income_categories.view",
                },{
                    title: "Income Entries",
                    route: "/incomes",
                    permission: "incomes.view",
                },{
                    title: "Income Payments",
                    route: "/income-payments",
                    permission: "income_payments.view",
                },{
                    title: "Recurring Income",
                    route: "/recurring-incomes",
                    permission: "recurring_incomes.view",
                },{
                    title: "Income Reports",
                    route: "/income-reports",
                    permission: "income_reports.view",
                }
        ]
    },
    {
        title: "Library",
        icon: "bi bi-journal-bookmark",
        feature: "library",
        children: [
                {
                    title: "Dashboard",
                    route: "/library/dashboard",
                    permission: "library_dashboard.view",
                },
                {
                    title: "Book Categories",
                    route: "/book-categories",
                    permission: "book_categories.view",
                },
                {
                    title: "Authors",
                    route: "/authors",
                    permission: "authors.view",
                },
                {
                    title: "Publishers",
                    route: "/publishers",
                    permission: "publishers.view",
                },
                {
                    title: "Books",
                    route: "/books",
                    permission: "books.view",
                },
                {
                    title: "Book Copies",
                    route: "/book-copies",
                    permission: "book_copies.view",
                },{
                    title: "Library Members",
                    route: "/library-members",
                    permission: "library_members.view",
                },{
                    title: "Book Issues",
                    route: "/book-issues",
                    permission: "book_issues.view",
                },{
                    title: "Book Return",
                    route: "/book-returns",
                    permission: "book_returns.view",
                },{
                    title: 'Book Renewal',
                    route: '/book-renewals',
                    permission: "book_renewals.view",
                },{
                    title: "Lost / Damaged Books",
                    route: "/lost-damaged-books",
                    permission: "lost_damaged_books.view",
                },{
                    title: 'Library Fines',
                    route: '/library-fines',
                    permission: "library_fines.view",
                },{
                    title: 'Fine Collection',
                    route: '/fine-collections',
                    permission: "fine_collections.view",
                },{
                    title: 'Library Reports',
                    route: '/library-reports',
                    permission: "library_reports.view",
                }
        ],
    },

    {
        title: "Inventory",
        icon: "bi bi-box-seam",
        children: [
            {
                    title: 'Categories',
                    route: '/inventory/categories',
                    permission: "categories.view",
            },
            {
                    title: 'Units',
                    route: '/inventory/units',
                    permission: "units.view",
            },
            {
                    title: 'Suppliers',
                    route: '/inventory/suppliers',
                    permission: "suppliers.view",
            },
            {
                title: "Items",
                route: "/inventory/items",
                permission: "inventory.view",
            },
            {
                title: "Purchases",
                route: "/inventory/purchases",
                permission: "purchase.view",
            },
            {
                title: "Stock Ins",
                route: "/inventory/stock-ins",
                permission: "stock_ins.view",
            },{
                title: "Stock Movements",
                route: "/inventory/stockmovements",
                permission: "stock_movements.view",
            },{
                title: "Inventory Dashboard",
                route: "/inventory/dashboard",
                permission: "inventory_dashboard.view",
            },{
                title: "Stock Outs",
                route: "/inventory/stock-outs",
                permission: "inventory_stock_outs.view",
            },
        ],
    },

    {
        title: "Transport",
        icon: "bi bi-bus-front",
        feature: "transport",
        children: [
                {
                title: "Vehicles",
                route: "/transport/vehicles",
                permission: "transport_vehicles.view",
                },{
                    title: 'Routes',
                    route: '/transport/routes',
                    permission: "transport_routes.view",
                },
                {
                    title: 'Stops',
                    route: '/transport/stops',
                    permission: "transport_stops.view",
                },{
                    title: 'Drivers',
                    route: '/transport/drivers',
                    permission: "transport_drivers.view",
                },
                {
                    title: 'Student Transport',
                    route: '/transport/students',
                    permission: "transport_students.view",
                },{
                    title: 'Transport Assignments',
                    route: '/transport/assignments',
                    permission: "transport_assignments.view",
                },{
                    title: 'Transport Fees',
                    route: '/transport/fees',
                    permission: "transport_fees.view",
                },{
                    title: 'Vehicle Maintenance',
                    route: '/transport/maintenance',
                    permission: "transport_maintenance.view",
                },{
                    title: "Fuel / Expenses",
                    route: "/transport/expenses",
                    permission: "transport_expenses.view",
                },
                {
                    title: "Transport Reports",
                    route: "/transport/reports",
                    permission: "transport_reports.view",
                },
        ]
    },
   {
        title: "Hostel",
        icon: "bi bi-house-door",
        feature: "hostel",
        children: [
            {
                title: "Dashboard",
                route: "/hostel/dashboard",
                permission: "hostel_dashboard.view",
            },
            {
                title: "Hostels",
                route: "/hostel/hostels",
                permission: "hostel.view",
            },
            {
                title: "Buildings",
                route: "/hostel/buildings",
                permission: "hostel_buildings.view",
            },
            {
                title: "Floors",
                route: "/hostel/floors",
                permission: "hostel_floors.view",
            },
            {
                title: "Rooms",
                route: "/hostel/rooms",
                permission: "hostel_rooms.view",
            },
            {
                title: "Beds",
                route: "/hostel/beds",
                permission: "hostel_beds.view",
            },
            {
                title: "Hostel Students",
                route: "/hostel/students",
                permission: "hostel_students.view",
            },
            {
                title: "Room Allocation",
                route: "/hostel/allocations",
                permission: "hostel_allocations.view",
            },
            {
                title: "Room Transfer",
                route: "/hostel/transfers",
                permission: "hostel_transfers.view",
            },
            {
                title: "Hostel Fees",
                route: "/hostel/fees",
                permission: "hostel_fees.view",
            },
            {
                title: "Hostel Attendance",
                route: "/hostel/attendance",
                permission: "hostel_attendance.view",
            },
            {
                title: "Complaints",
                route: "/hostel/complaints",
                permission: "hostel_complaints.view",
            },
            {
                title: "Visitors",
                route: "/hostel/visitors",
                permission: "hostel_visitors.view",
            },
            {
                title: "Hostel Reports",
                route: "/hostel/reports",
                permission: "hostel_reports.view",
            },
        ]
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
                route: "/administrator/users",
                permission: "user.view",
            },
            {
                title: "Roles",
                route: "/administrator/roles",
                permission: "role.view",
            },
            {
                title: "Permissions",
                route: "/administrator/permissions",
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