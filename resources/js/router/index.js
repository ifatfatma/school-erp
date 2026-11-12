import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import Login from "@/pages/auth/Login.vue";
import Dashboard from "@/pages/dashboard/Dashboard.vue";
import School from "@/pages/school/SchoolIndex.vue";

import AcademicSessionIndex from "@/pages/academic-session/Index.vue";
import AcademicSessionCreate from "@/pages/academic-session/Create.vue";
import AcademicSessionEdit from "@/pages/academic-session/Edit.vue";

import AdminLayout from "@/layouts/AdminLayout.vue";

const routes = [
    /*
    |--------------------------------------------------------------------------
    | Central School ERP SaaS Platform (Root Domain)
    |--------------------------------------------------------------------------
    */
    {
        path: "/",
        component: () => import("@/layouts/SaasLayout.vue"),
        children: [
            {
                path: "",
                name: "saas.landing",
                component: () => import("@/pages/saas/SaasLanding.vue"),
            },
        ],
    },

    /*
    |--------------------------------------------------------------------------
    | Individual School Portals (/s/:schoolCode)
    |--------------------------------------------------------------------------
    */
    {
        path: "/s/:schoolCode",
        component: () => import("@/layouts/WebsiteLayout.vue"),
        children: [
            {
                path: "",
                name: "tenant.website.home",
                component: () => import("@/pages/website/Home.vue"),
            },
            {
                path: "about",
                name: "tenant.website.about",
                component: () => import("@/pages/website/About.vue"),
            },
            {
                path: "academics",
                name: "tenant.website.academics",
                component: () => import("@/pages/website/Academics.vue"),
            },
            {
                path: "admissions",
                name: "tenant.website.admissions",
                component: () => import("@/pages/website/Admissions.vue"),
            },
            {
                path: "admissions/apply",
                name: "tenant.website.admissions.apply",
                component: () => import("@/pages/website/ApplyAdmission.vue"),
            },
            {
                path: "admissions/track",
                name: "tenant.website.admissions.track",
                component: () => import("@/pages/website/TrackAdmission.vue"),
            },
            {
                path: "contact",
                name: "tenant.website.contact",
                component: () => import("@/pages/website/Contact.vue"),
            },
        ],
    },

    /*
    |--------------------------------------------------------------------------
    | Direct / Root Admissions & Public Pages (e.g. /admissions/apply)
    |--------------------------------------------------------------------------
    */
    {
        path: "/admissions",
        component: () => import("@/layouts/WebsiteLayout.vue"),
        children: [
            {
                path: "",
                name: "public.admissions",
                component: () => import("@/pages/website/Admissions.vue"),
            },
            {
                path: "apply",
                name: "public.admissions.apply",
                component: () => import("@/pages/website/ApplyAdmission.vue"),
            },
            {
                path: "track",
                name: "public.admissions.track",
                component: () => import("@/pages/website/TrackAdmission.vue"),
            },
        ],
    },
    {
        path: "/apply",
        redirect: "/admissions/apply",
    },
    {
        path: "/track",
        redirect: "/admissions/track",
    },
    {
        path: "/",
        component: () => import("@/layouts/WebsiteLayout.vue"),
        children: [
            {
                path: "about",
                name: "public.website.about",
                component: () => import("@/pages/website/About.vue"),
            },
            {
                path: "academics",
                name: "public.website.academics",
                component: () => import("@/pages/website/Academics.vue"),
            },
            {
                path: "contact",
                name: "public.website.contact",
                component: () => import("@/pages/website/Contact.vue"),
            },
        ],
    },

    /*
    |--------------------------------------------------------------------------
    | School-Scoped Auth Routes
    |--------------------------------------------------------------------------
    */
    {
        path: "/s/:schoolCode/portal/login",
        name: "tenant.portal.login",
        component: () => import("@/pages/portal/Login.vue"),
        meta: {
            guest: true,
        },
    },
    {
        path: "/s/:schoolCode/login",
        name: "tenant.staff.login",
        component: Login,
        meta: {
            guest: true,
        },
    },

    /*
    |--------------------------------------------------------------------------
    | Admin ERP Routes
    |--------------------------------------------------------------------------
    */
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
                path: "/online-classes",
                name: "online-classes.index",
                component: () => import("@/pages/online-classes/Index.vue"),
                meta: {
                    requiresAuth: true,
                    permission: "online_classes.view",
                    feature: "online_classes",
                },
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
                path: "/grades",
                children: [
                    {
                        path: "",
                        name: "grades.index",
                        component: () => import("@/pages/result-grades/Index.vue"),
                    },
                    {
                        path: "create",
                        name: "grades.create",
                        component: () => import("@/pages/result-grades/Create.vue"),
                    },                    
                    {
                        path: ":id/edit",
                        name: "grades.edit",
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
                        path: "/library/dashboard",
                        name: "library.dashboard",
                        component: () =>
                            import(
                                "@/pages/library/dashboard/Index.vue"
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
                        path: '/fine-collections',
                        name: 'fine-collections',
                        component: () =>
                            import('@/pages/fine-collections/Index.vue')
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
                                    path: '/hostel/dashboard',
                                    name: 'hostel.dashboard',
                                    component: () =>
                                        import('@/pages/hostel/dashboard/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_dashboard.view'
                                    }
                                },{
                                    path: '/hostel/hostels',
                                    name: 'hostel.hostels',
                                    component: () =>
                                        import('@/pages/hostel/hostels/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel.view'
                                    }
                                },{
                                    path: '/hostel/buildings',
                                    name: 'hostel.buildings',
                                    component: () =>
                                        import('@/pages/hostel/buildings/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_buildings.view'
                                    }
                                },{
                                    path: '/hostel/floors',
                                    name: 'hostel.floors',
                                    component: () =>
                                        import('@/pages/hostel/floors/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_floors.view'
                                    }
                                },{
                                    path: '/hostel/rooms',
                                    name: 'hostel.rooms',
                                    component: () =>
                                        import('@/pages/hostel/rooms/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_rooms.view'
                                    }
                                },{
                                    path: '/hostel/beds',
                                    name: 'hostel.beds',
                                    component: () =>
                                        import('@/pages/hostel/beds/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_beds.view'
                                    }
                                },{
                                    path: '/hostel/students',
                                    name: 'hostel.students',
                                    component: () =>
                                        import('@/pages/hostel/students/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_students.view'
                                    }
                                },{
                                    path: '/hostel/allocations',
                                    name: 'hostel.allocations',
                                    component: () =>
                                        import('@/pages/hostel/allocations/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_allocations.view'
                                    }
                                },{
                                    path: '/hostel/transfers',
                                    name: 'hostel.transfers',
                                    component: () =>
                                        import('@/pages/hostel/transfers/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_transfers.view'
                                    }
                                },{
                                    path: '/hostel/fees',
                                    name: 'hostel.fees',
                                    component: () =>
                                        import('@/pages/hostel/fees/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_fees.view'
                                    }
                                },{
                                    path: '/hostel/attendance',
                                    name: 'hostel.attendance',
                                    component: () =>
                                        import('@/pages/hostel/attendance/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_attendance.view'
                                    }
                                },{
                                    path: '/hostel/complaints',
                                    name: 'hostel.complaints',
                                    component: () =>
                                        import('@/pages/hostel/complaints/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_complaints.view'
                                    }
                                },{
                                    path: '/hostel/visitors',
                                    name: 'hostel.visitors',
                                    component: () =>
                                        import('@/pages/hostel/visitors/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_visitors.view'
                                    }
                                },{
                                    path: '/hostel/reports',
                                    name: 'hostel.reports',
                                    component: () =>
                                        import('@/pages/hostel/reports/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'hostel_reports.view'
                                    }
                                },
                                {
                                    path: '/admin/admissions/applications',
                                    name: 'admin.admissions.applications',
                                    component: () =>
                                        import('@/pages/admissions/applications/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'online_admissions.view'
                                    }
                                },
                                {
                                    path: '/admin/admissions/inquiries',
                                    name: 'admin.admissions.inquiries',
                                    component: () =>
                                        import('@/pages/admissions/inquiries/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'website_inquiries.view'
                                    }
                                },
                                {
                                    path: '/admin/website/manager',
                                    name: 'admin.website.manager',
                                    component: () =>
                                        import('@/pages/website-manager/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'website_settings.view',
                                        feature: 'website_cms'
                                    }
                                },
                                {
                                    path: '/admin/root-website/manager',
                                    name: 'admin.root.website.manager',
                                    component: () =>
                                        import('@/pages/root-website-manager/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'website_settings.view',
                                        superAdminOnly: true
                                    }
                                },
                                {
                                    path: '/reports',
                                    name: 'reports.index',
                                    component: () =>
                                        import('@/pages/reports/Index.vue'),
                                    meta: {
                                        requiresAuth: true,
                                        permission: 'report.view'
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

    /*
    |--------------------------------------------------------------------------
    | Student & Parent Portal Routes
    |--------------------------------------------------------------------------
    */
    {
        path: "/portal/login",
        name: "portal.login",
        component: () => import("@/pages/portal/Login.vue"),
        meta: {
            guest: true,
        },
    },
    {
        path: "/portal",
        component: () => import("@/layouts/PortalLayout.vue"),
        meta: {
            requiresAuth: true,
        },
        children: [
            {
                path: "",
                redirect: "/portal/dashboard",
            },
            {
                path: "dashboard",
                name: "portal.dashboard",
                component: () => import("@/pages/portal/Dashboard.vue"),
            },
            {
                path: "attendance",
                name: "portal.attendance",
                component: () => import("@/pages/portal/Attendance.vue"),
            },
            {
                path: "timetable",
                name: "portal.timetable",
                component: () => import("@/pages/portal/Timetable.vue"),
            },
            {
                path: "online-classes",
                name: "portal.online-classes",
                component: () => import("@/pages/portal/OnlineClasses.vue"),
            },
            {
                path: "fees",
                name: "portal.fees",
                component: () => import("@/pages/portal/Fees.vue"),
            },
            {
                path: "results",
                name: "portal.results",
                component: () => import("@/pages/portal/Results.vue"),
            },
            {
                path: "library",
                name: "portal.library",
                component: () => import("@/pages/portal/Library.vue"),
            },
            {
                path: "profile",
                name: "portal.profile",
                component: () => import("@/pages/portal/Profile.vue"),
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem("token");
    const requiresAuth = to.matched.some(record => record.meta?.requiresAuth) || !!to.meta?.requiresAuth;

    if (requiresAuth && !token) {
        if (to.path.startsWith("/portal")) {
            const schoolCode = localStorage.getItem("portal_school_code") || sessionStorage.getItem("portal_school_code");
            if (schoolCode) {
                return next(`/s/${schoolCode}/portal/login`);
            }
            return next("/portal/login");
        }
        return next("/login");
    }

    if (to.meta?.guest && token) {
        if (to.path.startsWith("/portal") || to.path.includes("/portal/login")) {
            return next("/portal/dashboard");
        }
        return next("/dashboard");
    }

    if (token) {
        try {
            const auth = useAuthStore();
            // Await user profile if not yet loaded in memory
            if (!auth.user) {
                try {
                    await auth.loadProfile();
                } catch (e) {
                    localStorage.removeItem("token");
                    return next("/login");
                }
            }

            const user = auth.user;
            const isSuperAdminUser = user?.user_type === "super_admin" || user?.roles?.some(r => r.name === "Super Admin");

            // Super Admin has 100% unrestricted platform-wide access to all modules and routes
            if (isSuperAdminUser) {
                return next();
            }

            if (to.meta?.superAdminOnly && !isSuperAdminUser) {
                return next("/dashboard");
            }

            // Path prefix to subscription feature mapping
            const PATH_FEATURE_MAP = {
                "/hostel": "hostel",
                "/library": "library",
                "/book-": "library",
                "/books": "library",
                "/authors": "library",
                "/publishers": "library",
                "/transport": "transport",
                "/inventory": "inventory",
                "/online-classes": "online_classes",
                "/admin/website": "website_cms",
                "/admin/admissions": "online_admissions",
            };

            let requiredFeature = to.meta?.feature || null;
            if (!requiredFeature) {
                for (const [prefix, feat] of Object.entries(PATH_FEATURE_MAP)) {
                    if (to.path.startsWith(prefix)) {
                        requiredFeature = feat;
                        break;
                    }
                }
            }

            // Subscription plan feature gating for tenant schools (School Admins, Teachers, Students)
            if (requiredFeature && user && user.school) {
                const schoolFeatures = user.school.features || [];
                const plan = (user.school.subscription_plan || "starter").toLowerCase();

                let isAllowed = false;
                if (Array.isArray(schoolFeatures) && schoolFeatures.length > 0) {
                    isAllowed = schoolFeatures.includes(requiredFeature);
                } else {
                    const upperFeatures = ["hostel", "library", "transport", "inventory", "website_cms", "online_admissions", "custom_domain", "multi_branch"];
                    isAllowed = !((plan === "starter" || plan === "basic") && upperFeatures.includes(requiredFeature));
                }

                if (!isAllowed) {
                    const featureName = requiredFeature.replace(/_/g, " ").toUpperCase();
                    sessionStorage.setItem(
                        "subscription_lock_msg",
                        `The [${featureName}] module is not included in your school's [${plan.toUpperCase()}] package. Please upgrade to unlock.`
                    );
                    return next("/dashboard");
                }
            }
        } catch (e) {
            console.error("Router beforeEach subscription check error:", e);
        }
    }

    next();
});

export default router;
