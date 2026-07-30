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
            }
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
