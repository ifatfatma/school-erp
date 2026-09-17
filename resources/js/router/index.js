import { createRouter, createWebHistory } from "vue-router";

import Login from "@/pages/auth/Login.vue";
import Dashboard from "@/pages/dashboard/Dashboard.vue";
import School from "@/pages/school/SchoolIndex.vue";

import AcademicSessionIndex from "@/pages/academic-session/Index.vue";
import AcademicSessionCreate from "@/pages/academic-session/Create.vue";
import AcademicSessionEdit from "@/pages/academic-session/Edit.vue";

import AdminLayout from "@/layouts/AdminLayout.vue";

const routes = [
    {
        path: "/",
        component: AdminLayout,
        meta: {
            requiresAuth: true,
        },
        children: [
            {
                path: "dashboard",
                component: Dashboard,
            },
            {
                path: "/school",
                name: "school",
                component: School,
                meta: {
                    requiresAuth: true,
                },
            },
            {
                path: "academic-sessions",
                name: "academic-sessions.index",
                component: AcademicSessionIndex,
            },
            {
                path: "academic-sessions/create",
                name: "academic-sessions.create",
                component: AcademicSessionCreate,
            },
            {
                path: "academic-sessions/:id/edit",
                name: "academic-sessions.edit",
                component: AcademicSessionEdit,
                props: true,
            },
            {
                path: "/classes",
                children: [
                    {
                        path: "",
                        name: "classes.index",
                        component: () => import("@/pages/classes/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "classes.create",
                        component: () => import("@/pages/classes/Create.vue"),
                    },
                    {
                        path: ":id/edit",
                        name: "classes.edit",
                        component: () => import("@/pages/classes/Edit.vue"),
                    },
                    {
                        path: '/classes/:id',
                        name: 'classes.show',
                        component: () => import('@/pages/classes/Show.vue')
                    }
                ],
            },
            {
                path: "/subjects",
                children: [
                    {
                        path: "",
                        name: "subjects.index",
                        component: () => import("@/pages/subjects/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "subjects.create",
                        component: () => import("@/pages/subjects/Create.vue"),
                    },
                    {
                        path: ":id/edit",
                        name: "subjects.edit",
                        component: () => import("@/pages/subjects/Edit.vue"),
                    },
                    {
                        path: '/subjects/:id',
                        name: 'subjects.show',
                        component: () => import('@/pages/subjects/Show.vue')
                    }
                ],
            },{
                path: "/sections",
                children: [
                    {
                        path: "",
                        name: "sections.index",
                        component: () => import("@/pages/sections/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "sections.create",
                        component: () => import("@/pages/sections/Create.vue"),
                    },
                    {
                        path: ":id",
                        name: "sections.show",
                        component: () => import("@/pages/sections/Show.vue"),
                    },
                    {
                        path: ":id/edit",
                        name: "sections.edit",
                        component: () => import("@/pages/sections/Edit.vue"),
                    },
                ],
            },{
                path: "/timetables",
                children: [
                    {
                        path: "",
                        name: "timetables.index",
                        component: () => import("@/pages/timetables/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "timetables.create",
                        component: () => import("@/pages/timetables/Create.vue"),
                    },
                    {
                        path: ":id",
                        name: "timetables.show",
                        component: () => import("@/pages/timetables/Show.vue"),
                    },
                    {
                        path: ":id/edit",
                        name: "timetables.edit",
                        component: () => import("@/pages/timetables/Edit.vue"),
                    },
                ],
            },{
                path: "/teachers",
                children: [
                    {
                        path: "",
                        name: "teachers.index",
                        component: () => import("@/pages/teachers/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "teachers.create",
                        component: () => import("@/pages/teachers/Create.vue"),
                    },
                    {
                        path: ":id",
                        name: "teachers.show",
                        component: () => import("@/pages/teachers/Show.vue"),
                    },
                    {
                        path: ":id/edit",
                        name: "teachers.edit",
                        component: () => import("@/pages/teachers/Edit.vue"),
                    },
                ],
            },{
                path: "/leave-types",
                children: [
                    {
                        path: "/leave-types",
                        name: "leave-types.index",
                        component: () => import("@/pages/leave-types/Index.vue"),
                    },

                    {
                        path: "/leave-types/create",
                        name: "leave-types.create",
                        component: () => import("@/pages/leave-types/Create.vue"),
                    },

                    {
                        path: "/leave-types/:id",
                        name: "leave-types.show",
                        component: () => import("@/pages/leave-types/Show.vue"),
                        props: true,
                    },

                    {
                        path: "/leave-types/:id/edit",
                        name: "leave-types.edit",
                        component: () => import("@/pages/leave-types/Edit.vue"),
                        props: true,
                    },

                ],
            },{
                path: "/teacher-leaves",
                children: [
                    {
                        path: "/teacher-leaves",
                        name: "teacher-leaves.index",
                        component: () => import("@/pages/teacher-leaves/Index.vue"),
                    },

                    {
                        path: "/teacher-leaves/create",
                        name: "teacher-leaves.create",
                        component: () => import("@/pages/teacher-leaves/Create.vue"),
                    },

                    {
                        path: "/teacher-leaves/:id",
                        name: "teacher-leaves.show",
                        component: () => import("@/pages/teacher-leaves/Show.vue"),
                        props: true,
                    },

                    {
                        path: "/teacher-leaves/:id/edit",
                        name: "teacher-leaves.edit",
                        component: () => import("@/pages/teacher-leaves/Edit.vue"),
                        props: true,
                    },

                ],
            },{
                path: "/teacher-attendances",
                children: [
                    {
                        path: "/teacher-attendances",
                        name: "teacher-attendances.index",
                        component: () => import("@/pages/teacher-attendances/Index.vue"),
                    },

                    {
                        path: "/teacher-attendances/create",
                        name: "teacher-attendances.create",
                        component: () => import("@/pages/teacher-attendances/Create.vue"),
                    },

                    {
                        path: "/teacher-attendances/:id",
                        name: "teacher-attendances.show",
                        component: () => import("@/pages/teacher-attendances/Show.vue"),
                        props: true,
                    },

                    {
                        path: "/teacher-attendances/:id/edit",
                        name: "teacher-attendances.edit",
                        component: () => import("@/pages/teacher-attendances/Edit.vue"),
                        props: true,
                    },

                ],
            },{
                path: "/students",
                children: [
                    {
                        path: "/students",
                        name: "students.index",
                        component: () => import("@/pages/students/Index.vue"),
                    },

                    {
                        path: "/students/create",
                        name: "students.create",
                        component: () => import("@/pages/students/Create.vue"),
                    },

                    {
                        path: "/students/:id",
                        name: "students.show",
                        component: () => import("@/pages/students/Show.vue"),
                        props: true,
                    },

                    {
                        path: "/students/:id/edit",
                        name: "students.edit",
                        component: () => import("@/pages/students/Edit.vue"),
                        props: true,
                    },

                ],
            },{
                path: "/student-attendance",
                children: [

                    {
                        path: "/student-attendance",
                        name: "student-attendance.index",
                        component: () =>import("@/pages/student-attendance/Index.vue")
                    },

                    {
                        path: "/student-attendance/bulk",
                        name: "student-attendance.bulk",
                        component: () =>import("@/pages/student-attendance/BulkAttendance.vue")                        
                    },

                    {
                        path: "/student-attendance/:id",
                        name: "student-attendance.show",
                        component: () =>import("@/pages/student-attendance/Show.vue"),
                        props: true,
                        meta: {
                            title: "Attendance Details",
                        },
                    },

                    {
                        path: "/student-attendance/:id/edit",
                        name: "student-attendance.edit",
                        component: () =>import("@/pages/student-attendance/Edit.vue"),
                        props: true,
                        meta: {
                            title: "Edit Attendance",
                        },
                    },

                    {
                        path: "/student-attendance/monthly/report",
                        name: "student-attendance.monthly",
                        component: () =>import("@/pages/student-attendance/MonthlyAttendance.vue"),
                        meta: {
                            title: "Monthly Attendance",
                        },
                    },

                ],

            },{
                    path: "/guardians",
                    children: [

                        {
                            path: "/guardians",
                            name: "guardians.index",
                            component: () => import("@/pages/guardians/Index.vue"),
                            meta: {
                                title: "Guardians",
                            },
                        },

                        {
                            path: "/guardians/create",
                            name: "guardians.create",
                            component: () => import("@/pages/guardians/Create.vue"),
                            meta: {
                                title: "Add Guardian",
                            },
                        },

                        {
                            path: "/guardians/:id",
                            name: "guardians.show",
                            component: () => import("@/pages/guardians/Show.vue"),
                            props: true,
                            meta: {
                                title: "Guardian Details",
                            },
                        },

                        {
                            path: "/guardians/:id/edit",
                            name: "guardians.edit",
                            component: () => import("@/pages/guardians/Edit.vue"),
                            props: true,
                            meta: {
                                title: "Edit Guardian",
                            },
                        },

                    ],

            },{
                    path: "/student-promotions",

                    children: [
                        {
                            path: "/student-promotions",
                            name: "student-promotions.index",
                            component: () => import("@/pages/student-promotions/Index.vue"),
                        },

                        {
                            path: "/student-promotions/bulk",
                            name: "student-promotions.bulk",
                            component: () => import("@/pages/student-promotions/BulkPromotion.vue"),
                        },

                        {
                            path: "/student-promotions/:id",
                            name: "student-promotions.show",
                            component: () => import("@/pages/student-promotions/Show.vue"),
                        },

                    ]

            },{
                    path: "/exam-types",

                    children: [
                        {
                            path: "/exam-types",
                            name: "exam-types.index",
                            component: () => import("@/pages/exam-types/Index.vue"),
                        },
                        
                        {
                        path: "/exam-types/create",
                        name: "exam-types.create",
                        component: () => import("@/pages/exam-types/Create.vue"),
                       },
                        {
                        path: "/exam-types/:id/Edit",
                        name: "exam-types.edit",
                        component: () => import("@/pages/exam-types/Edit.vue"),
                       },


                    ]

            },{
                    path: "/exams",

                    children: [
                        {
                            path: "/exams",
                            name: "exams.index",
                            component: () => import("@/pages/exams/Index.vue"),
                        },
                        
                        {
                        path: "/exams/create",
                        name: "exams.create",
                        component: () => import("@/pages/exams/Create.vue"),
                       },
                        {
                        path: "/exams/:id/Edit",
                        name: "exams.edit",
                        component: () => import("@/pages/exams/Edit.vue"),
                       },


                    ]

            },{
                    path: "/exam-schedules",

                    children: [
                        {
                            path: "/exam-schedules",
                            name: "exam-schedules.index",
                            component: () => import("@/pages/exam-schedules/Index.vue"),
                        },
                        
                        {
                        path: "/exam-schedules/create",
                        name: "exam-schedules.create",
                        component: () => import("@/pages/exam-schedules/Create.vue"),
                       },
                        {
                        path: "/exam-schedules/:id/Edit",
                        name: "exam-schedules.edit",
                        component: () => import("@/pages/exam-schedules/Edit.vue"),
                       },


                    ]

            },{
                    path: "/exam-subjects",

                    children: [
                        {
                            path: "/exam-subjects",
                            name: "exam-subjects.index",
                            component: () => import("@/pages/exam-subjects/Index.vue"),
                        },
                        
                        {
                        path: "/exam-subjects/create",
                        name: "exam-subjects.create",
                        component: () => import("@/pages/exam-subjects/Create.vue"),
                       },
                        {
                        path: "/exam-subjects/:id/Edit",
                        name: "exam-subjects.edit",
                        component: () => import("@/pages/exam-subjects/Edit.vue"),
                       },


                    ]

            },{
                    path: "/exam-marks",
                    name: "exam-marks.index",
                    component: () => import("@/pages/exam-marks/Index.vue"),
                    meta: {
                        title: "Marks Entry",
                        requiresAuth: true,
                        permission: "exam_mark.view",
                    },
            },{
                path: "/report-cards",
                children: [
                    {
                        path: "",
                        name: "report-cards.index",
                        component: () => import("@/pages/report-cards/Index.vue"),
                    },
                    {
                        path: "/report-cards/:student/:exam",
                        name: "report-cards.show",
                        component: () =>
                            import("@/pages/report-cards/Show.vue"),
                    },
                ],
            },{
                path: "/result-grades",
                children: [
                    {
                        path: "",
                        name: "result-grades.index",
                        component: () => import("@/pages/result-grades/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "result-grades.create",
                        component: () => import("@/pages/result-grades/Create.vue"),
                    },                    
                    {
                        path: ":id/edit",
                        name: "result-grades.edit",
                        component: () => import("@/pages/result-grades/Edit.vue"),
                    },
                ],
            },{
                path: "/exam-ranks",
                children: [
                    {
                        path: "",
                        name: "exam-ranks.index",
                        component: () =>
                            import("@/pages/exam-ranks/Index.vue"),
                    }
                ],
            },{
                path: "/result-publish",
                name: "result-publish.index",
                component: () =>
                    import("@/pages/result-publish/Index.vue"),
            },{
                path: "/fee-types",
                name: "fee-types.index",
                component: () =>
                    import("@/pages/fee-types/Index.vue"),
            },{
                path: "/fee-types/create",
                name: "fee-types.create",
                component: () =>
                    import("@/pages/fee-types/Create.vue"),
            },{
                path: "/fee-types/:id/edit",
                name: "fee-types.edit",
                component: () =>
                    import("@/pages/fee-types/Edit.vue"),
            },{
                path: "/fee-structures",
                children: [
                    {
                        path: "",
                        name: "fee-structures.index",
                        component: () => import("@/pages/fee-structures/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "fee-structures.create",
                        component: () => import("@/pages/fee-structures/Create.vue"),
                    },                    
                    {
                        path: ":id/edit",
                        name: "fee-structures.edit",
                        component: () => import("@/pages/fee-structures/Edit.vue"),
                    },
                ],
            },{
                path: "/fee-assignments",
                name: "fee-assignments.index",
                component: () =>
                    import("@/pages/fee-assignments/Index.vue"),
            },{
                path: "/fee-dues",
                name: "fee-dues.index",
                component: () =>
                    import(
                        "@/pages/fee-dues/Index.vue"
                    ),
            },{
                path: "/fee-collection",
                name: "fee-collection.index",
                component: () =>
                    import(
                        "@/pages/fee-collection/Index.vue"
                    ),
            },{
                path: "/payment-history",
                name: "payment-history",
                component: () =>
                    import(
                        "@/pages/payment-history/Index.vue"
                    )
            },{
                path: "/fee-receipts",
                name: "fee-receipts",
                component: () =>
                    import(
                        "@/pages/fee-receipts/Index.vue"
                    )
            },{
                    path: "/discounts",
                    name: "discounts",
                    component: () =>
                        import(
                            "@/pages/discounts/Index.vue"
                        )
                },{
                    path: "/late-fees",
                    name: "late-fees",
                    component: () =>
                        import(
                            "@/pages/late-fees/Index.vue"
                        )
                },{
                    path: "/fee-reports",
                    name: "fee-reports",
                    component: () =>
                        import(
                            "@/pages/fee-reports/Index.vue"
                        )
                },{
                    path: "/expense-categories",
                    name: "expense-categories",
                    component: () =>
                        import(
                            "@/pages/expense-categories/Index.vue"
                        )
                },{
                    path: "/expenses",
                    name: "expenses",
                    component: () =>
                        import(
                            "@/pages/expenses/Index.vue"
                        )
                },{
                    path: "/expense-payments",
                    name: "expense-payments",
                    component: () =>
                        import(
                            "@/pages/expense-payments/Index.vue"
                        )
                },{
                    path: "/recurring-expenses",
                    name: "recurring-expenses",
                    component: () =>
                        import(
                            "@/pages/recurring-expenses/Index.vue"
                        )
                },{
                    path: "/expense-reports",
                    name: "expense-reports",
                    component: () =>
                        import(
                            "@/pages/expense-reports/Index.vue"
                        )
                },{
                        path: "/income-categories",
                        name: "income-categories",
                        component: () =>
                            import(
                                "@/pages/income-categories/Index.vue"
                            )
                    },{
                        path: "/incomes",
                        name: "incomes",
                        component: () =>
                            import(
                                "@/pages/incomes/Index.vue"
                            )
                    },{
                        path: "/income-payments",
                        name: "income-payments",
                        component: () =>
                            import(
                                "@/pages/income-payments/Index.vue"
                            )
                    },{
                        path: "/recurring-incomes",
                        name: "recurring-incomes",
                        component: () =>
                            import(
                                "@/pages/recurring-incomes/Index.vue"
                            )
                    },
                    {
                        path: "/income-reports",
                        name: "income-reports",
                        component: () =>
                            import(
                                "@/pages/income-reports/Index.vue"
                            )
                    },{
                        path: "/authors",
                        name: "authors",
                        component: () =>
                            import(
                                "@/pages/authors/Index.vue"
                            )
                    },{
                        path: "/book-categories",
                        name: "book-categories",
                        component: () =>
                            import(
                                "@/pages/book-categories/Index.vue"
                            )
                    },{
                        path: "/publishers",
                        name: "publishers",
                        component: () =>
                            import(
                                "@/pages/publishers/Index.vue"
                            )
                    },{
                        path: "/books",
                        name: "books",
                        component: () =>
                            import(
                                "@/pages/books/Index.vue"
                            )
                    },{
                            path: "/book-copies",
                            name: "book-copies",
                            component: () =>
                                import(
                                    "@/pages/book-copies/Index.vue"
                                )
                    },{
                        path: "/library-members",
                        name: "library-members",
                        component: () =>
                            import(
                                "@/pages/library-members/Index.vue"
                            )
                    },{
                        path: "/book-issues",
                        name: "book-issues",
                        component: () =>
                            import(
                                "@/pages/book-issues/Index.vue"
                            )
                    },{
                        path: "/book-returns",
                        name: "book-returns",
                        component: () =>
                            import(
                                "@/pages/book-returns/Index.vue"
                            )
                    },{
                        path: '/book-renewals',
                        name: 'book-renewals',
                        component: () =>
                            import('@/pages/book-renewals/Index.vue')
                    },{
                        path: '/library-reports',
                        name: 'library-reports',
                        component: () =>
                            import('@/pages/library-reports/Index.vue')
                    },{
                        path: '/library-fines',
                        name: 'library-fines',
                        component: () =>
                            import('@/pages/library-fines/Index.vue')
                    },{
                        path: '/lost-damaged-books',
                        name: 'lost-damaged-books',
                        component: () =>
                            import('@/pages/lost-damaged-books/Index.vue')
                    },{
                        path: '/settings',
                        name: 'settings',
                        component: () =>
                            import(
                                '@/pages/settings/Index.vue'
                            )
                    },{
                        path: '/inventory/categories',
                        name: 'categories',
                        component: () =>
                            import(
                                '@/pages/inventory/categories/Index.vue'
                            )
                    },{
                        path: '/inventory/units',
                        name: 'units',
                        component: () =>
                            import(
                                '@/pages/inventory/units/Index.vue'
                            )
                    },{
                        path: '/inventory/suppliers',
                        name: 'suppliers',
                        component: () =>
                            import(
                                '@/pages/inventory/suppliers/Index.vue'
                            )
                    },{
                        path: '/inventory/items',
                        name: 'items',
                        component: () =>
                            import(
                                '@/pages/inventory/items/Index.vue'
                            )
                    },{
                        path: '/inventory/purchases',
                        name: 'purchases',
                        component: () =>
                            import(
                                '@/pages/inventory/purchases/Index.vue'
                            )
                    },{
                        path: '/inventory/stock-ins',
                        name: 'inventory-stock-ins',
                        component: () =>
                            import(
                                '@/pages/inventory/stockins/Index.vue'
                            )
                    },{
                        path: '/inventory/stockmovements',
                        name: 'stockmovements',
                        component: () =>
                            import(
                                '@/pages/inventory/stockmovements/Index.vue'
                            )
                    },{
                        path: '/inventory/stock-adjustments',
                        name: 'inventory-stock-adjustments',
                        component: () =>
                            import(
                                '@/pages/inventory/stockadjustments/Index.vue'
                            )
                    },{
                        path: '/inventory/dashboard',
                        name: 'inventory-dashboard',
                        component: () =>
                            import(
                                '@/pages/inventory/dashboard/Index.vue'
                            )
                    },{
                        path: '/inventory/stock-outs',
                        name: 'inventory-stock-outs',
                        component: () =>
                            import(
                                '@/pages/inventory/stockouts/Index.vue'
                            )
                    },{
                            path: '/transport/vehicles',
                            name: 'transport-vehicles',
                            component: () =>
                                import(
                                    '@/pages/transport/vehicles/Index.vue'
                                )
                        },{
                            path: '/transport/drivers',
                            name: 'transport-drivers',
                            component: () =>
                                import(
                                    '@/pages/transport/drivers/Index.vue'
                                )
                        },{
                            path: '/transport/students',
                            name: 'transport-students',
                            component: () =>
                                import(
                                    '@/pages/transport/students/Index.vue'
                                )
                        },{
                                path: '/transport/routes',
                                name: 'transport-routes',
                                component: () =>
                                    import(
                                        '@/pages/transport/routes/Index.vue'
                                    )
                            },{
                            path: '/transport/drivers',
                            name: 'transport-drivers',
                            component: () =>
                                import(
                                    '@/pages/transport/drivers/Index.vue'
                                )
                        },{
                            path: '/transport/stops',
                            name: 'transport-stops',
                            component: () =>
                                import(
                                    '@/pages/transport/stops/Index.vue'
                                )
                        },{
                            path: '/transport/assignments',
                            name: 'transport-assignments',
                            component: () =>
                                import(
                                    '@/pages/transport/assignments/Index.vue'
                                )
                        },{
                        path: '/transport/fees',
                        name: 'transport-fees',
                        component: () =>
                             import('@/pages/transport/fees/Index.vue')
                         },{
                                path: '/transport/maintenance',
                                name: 'transport-maintenance',
                                component: () =>
                                    import(
                                        '@/pages/transport/maintenance/Index.vue'
                                    )
                            },{
                                path: '/transport/expenses',
                                name: 'transport-expenses',
                                component: () =>
                                    import(
                                        '@/pages/transport/expenses/Index.vue'
                                    )
                            },{
                                path: '/transport/reports',
                                name: 'transport-reports',
                                component: () =>
                                    import(
                                        '@/pages/transport/reports/Index.vue'
                                    )
                            },{
                                    path: '/administrator/users',
                                    name: 'administrator-users',
                                    component: () =>
                                        import(
                                            '@/pages/administrator/users/Index.vue'
                                        ),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'view_users'
                                    }
                                },

                                {
                                    path: '/administrator/roles',
                                    name: 'administrator-roles',
                                    component: () =>
                                        import(
                                            '@/pages/administrator/roles/Index.vue'
                                        ),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'view_roles'
                                    }
                                },

                                {
                                    path: '/administrator/permissions',
                                    name: 'administrator-permissions',
                                    component: () =>
                                        import(
                                            '@/pages/administrator/permissions/Index.vue'
                                        ),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'view_permissions'
                                    }
                                },{
                                    path: '/hostel/buildings',
                                    name: 'hostel.buildings',
                                    component: () =>
                                        import('@/pages/hostel/buildings/Index.vue')
                                },{
                                        path: '/hostel/rooms',
                                        name: 'hostel.rooms',
                                        component: () =>
                                            import(
                                                '@/pages/hostel/rooms/Index.vue'
                                            ),
                                        meta: {
                                            requiresAuth: true,
                                            permission: 'hostel_rooms.view'
                                        }
                                    },
        ],
    },
    
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: {
            guest: true,
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");

    if (to.meta.requiresAuth && !token) {
        return next("/login");
    }

    if (to.meta.guest && token) {
        return next("/dashboard");
    }

    next();
});

export default router;
