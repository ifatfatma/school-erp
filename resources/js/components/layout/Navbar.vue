 <template>
    <nav class="admin-navbar">

        <!-- =====================================================
             LEFT SIDE
        ====================================================== -->
        <div class="navbar-left">

            <!-- Sidebar Toggle -->
            <button
                type="button"
                class="sidebar-menu-btn"
                title="Toggle Sidebar"
                aria-label="Toggle Sidebar"
                @click="toggleSidebar"
            >
                <i class="bi bi-list"></i>
            </button>


            <!-- Page Title -->
            <div class="page-title d-flex align-items-center">

                <i class="bi bi-mortarboard-fill page-title-icon me-2"></i>

                <span>
                    {{ pageTitle }}
                </span>

            </div>

        </div>


        <!-- =====================================================
             RIGHT SIDE
        ====================================================== -->
        <div class="navbar-right d-flex align-items-center">

            <!-- Active School Switcher for Super Admin / Static Badge for School Admin -->
            <div v-if="isSuperAdmin && tenantStore.tenants.length > 0" class="dropdown me-2">
                <button
                    class="btn btn-sm btn-light border rounded-pill d-flex align-items-center gap-2 px-3 py-1 shadow-sm dropdown-toggle fw-semibold"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Switch Active Institution / Campus"
                >
                    <i class="bi bi-buildings text-primary"></i>
                    <span class="text-dark text-truncate" style="max-width: 190px;">
                        {{ currentSchoolName || 'Select School' }}
                    </span>
                    <span v-if="currentSchoolCode" class="badge bg-white text-primary border small font-monospace d-none d-md-inline">
                        {{ currentSchoolCode }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-1 py-1" style="max-height: 400px; overflow-y: auto; min-width: 280px;">
                    <li><h6 class="dropdown-header small text-uppercase fw-bold text-muted">Switch Active Campus</h6></li>
                    <li>
                        <button
                            type="button"
                            class="dropdown-item d-flex align-items-center justify-content-between py-2"
                            :class="{ active: tenantStore.activeSchoolId === 'all' || tenantStore.isGlobal }"
                            @click="onTenantSelect('all')"
                        >
                            <span><i class="bi bi-globe me-2 text-primary"></i> Global / All Schools</span>
                            <i v-if="tenantStore.activeSchoolId === 'all' || tenantStore.isGlobal" class="bi bi-check-lg text-primary ms-2"></i>
                        </button>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li v-for="t in tenantStore.tenants" :key="'nav-t-'+t.id">
                        <button
                            type="button"
                            class="dropdown-item d-flex align-items-center justify-content-between py-2"
                            :class="{ active: String(tenantStore.activeSchoolId) === String(t.id) }"
                            @click="onTenantSelect(t.id)"
                        >
                            <div class="text-truncate me-2">
                                <div class="fw-semibold text-truncate" style="max-width: 220px;">{{ t.name }}</div>
                                <small class="text-muted font-monospace">{{ t.code }}</small>
                            </div>
                            <i v-if="String(tenantStore.activeSchoolId) === String(t.id)" class="bi bi-check-lg text-primary"></i>
                        </button>
                    </li>
                </ul>
            </div>
            <div
                v-else-if="currentSchoolName"
                class="d-flex align-items-center gap-2 me-2 px-3 py-1 rounded-pill bg-light border text-muted small shadow-sm"
                title="Active Institution"
            >
                <i class="bi bi-building text-primary"></i>
                <span class="fw-semibold text-dark text-truncate" style="max-width: 200px;">
                    {{ currentSchoolName }}
                </span>
                <span v-if="currentSchoolCode" class="badge bg-white text-primary border small font-monospace d-none d-md-inline">
                    {{ currentSchoolCode }}
                </span>
            </div>

            <!-- Quick link to Root Platform Website CMS (Super Admin) -->
            <router-link
                v-if="isSuperAdmin"
                to="/admin/root-website/manager"
                class="btn btn-sm btn-outline-warning text-dark rounded-pill me-2 d-none d-lg-flex align-items-center gap-1 shadow-sm py-1 px-3"
                title="Edit Root SaaS Marketing Website ( / )"
            >
                <i class="bi bi-globe-americas text-primary"></i>
                <span class="small fw-semibold">Root Web CMS</span>
            </router-link>

            <!-- Quick link to School Website CMS -->
            <router-link
                v-if="isSuperAdmin || canAccessSchoolWebsiteCms"
                to="/admin/website/manager"
                class="btn btn-sm btn-outline-primary rounded-pill me-3 d-none d-md-flex align-items-center gap-1 shadow-sm py-1 px-3"
                title="Edit Active School Front Website Content"
            >
                <i class="bi bi-window-sidebar"></i>
                <span class="small fw-semibold">School Web CMS</span>
            </router-link>

            <!-- User -->
            <div
                v-if="auth.user"
                class="navbar-user"
            >

                <!-- Avatar -->
                <div class="navbar-avatar">
                    {{ userInitial }}
                </div>


                <!-- User Information -->
                <div class="navbar-user-info">

                    <div class="navbar-user-name">
                        {{ auth.user.name }}
                    </div>

                    <small>
                        {{ userRole }}
                    </small>

                </div>

            </div>


            <!-- Logout -->
            <button
                type="button"
                class="logout-btn"
                :disabled="loggingOut"
                @click="logout"
            >

                <span
                    v-if="loggingOut"
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true"
                ></span>

                <i
                    v-else
                    class="bi bi-box-arrow-right"
                ></i>

                <span class="logout-text">
                    {{ loggingOut ? "Logging out..." : "Logout" }}
                </span>

            </button>

        </div>

    </nav>
</template>


<script setup>

import {
    computed,
    ref,
    watchEffect,
    onMounted,
    onUnmounted
} from "vue";

import {
    useRoute,
    useRouter
} from "vue-router";

import Swal from "sweetalert2";

import {
    useAuthStore
} from "@/stores/auth";

import {
    useTenantStore
} from "@/stores/tenant";


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    "toggle-sidebar"
]);


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const router = useRouter();

const route = useRoute();


/*
|--------------------------------------------------------------------------
| AUTH STORE
|--------------------------------------------------------------------------
*/

const auth = useAuthStore();
const tenantStore = useTenantStore();

const isSuperAdmin = computed(() => {
    const user = auth.user;
    if (!user) return false;
    const type = String(user.user_type || '').toLowerCase();
    if (type === 'super_admin' || type === 'super admin') return true;
    if (Array.isArray(user.roles)) {
        return user.roles.some(r => {
            const roleName = typeof r === 'string' ? r : (r.name || '');
            return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin';
        });
    }
    return !!tenantStore.isSuperAdmin;
});

const currentSchoolName = computed(() => {
    if (tenantStore.isGlobal || tenantStore.activeSchoolId === 'all') {
        return 'Global / All Schools';
    }
    return tenantStore.currentTenant?.name || auth.user?.school?.name || null;
});

const currentSchoolCode = computed(() => {
    if (tenantStore.isGlobal || tenantStore.activeSchoolId === 'all') {
        return null;
    }
    return tenantStore.currentTenant?.code || auth.user?.school?.code || null;
});

const canAccessSchoolWebsiteCms = computed(() => {
    if (isSuperAdmin.value) return true;
    const plan = String(auth.user?.school?.subscription_plan || 'starter').toLowerCase();
    if (plan === 'starter' || plan === 'basic') return false;
    const features = auth.user?.school?.features;
    if (Array.isArray(features)) return features.includes('website_cms');
    return true;
});

function handleTenantSwitched(e) {
    if (e.detail?.school) {
        tenantStore.currentTenant = e.detail.school;
        tenantStore.activeSchoolId = String(e.detail.school.id);
        tenantStore.isGlobal = false;
    } else if (e.detail?.school_id === 'all') {
        tenantStore.currentTenant = null;
        tenantStore.activeSchoolId = 'all';
        tenantStore.isGlobal = true;
    }
}

onMounted(async () => {
    window.addEventListener('tenant-switched', handleTenantSwitched);
    if (!auth.user && auth.token) {
        await auth.loadProfile();
    }
    if (isSuperAdmin.value) {
        await Promise.all([
            tenantStore.fetchTenants(),
            tenantStore.fetchCurrent(),
        ]);
    } else {
        await tenantStore.fetchCurrent();
    }
});

onUnmounted(() => {
    window.removeEventListener('tenant-switched', handleTenantSwitched);
});

async function onTenantSelect(id) {
    await tenantStore.switchTenant(id);
}

const logoError = ref(false);

const schoolLogo = computed(() => {
    if (logoError.value) return null;
    const l = auth.user?.school?.logo;
    if (!l || l === "[]" || l.endsWith("/[]") || l === "null") return null;
    return l;
});


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const loggingOut = ref(false);


/*
|--------------------------------------------------------------------------
| SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/

function toggleSidebar() {

    emit("toggle-sidebar");

}


/*
|--------------------------------------------------------------------------
| PAGE TITLE FORMATTER
|--------------------------------------------------------------------------
*/

const routeTitleMap = {
    "dashboard": "Dashboard",
    "school": "School Management",
    "academic-sessions.index": "Academic Sessions",
    "academic-sessions.create": "Create Academic Session",
    "academic-sessions.edit": "Edit Academic Session",
    "student-attendance.index": "Student Attendance",
    "student-attendance.bulk": "Bulk Student Attendance",
    "student-attendance.monthly": "Monthly Student Attendance",
    "student-attendance.show": "Student Attendance Details",
    "student-promotions.index": "Student Promotions",
    "student-promotions.bulk": "Bulk Student Promotion",
    "student-promotions.show": "Promotion History",
    "teacher-attendances.index": "Teacher Attendance",
    "teacher-attendances.bulk": "Bulk Teacher Attendance",
    "teacher-attendances.monthly": "Monthly Teacher Attendance",
    "teacher-leaves.index": "Teacher Leave Requests",
    "teacher-leaves.create": "Apply for Leave",
    "leave-types.index": "Leave Types",
    "exam-types.index": "Exam Types",
    "exam-subjects.index": "Exam Subjects",
    "exam-schedules.index": "Exam Schedules",
    "exams.index": "Exams",
    "exam-marks.index": "Exam Marks Entry",
    "exam-ranks.index": "Exam Ranks",
    "grades.index": "Grades",
    "result-grades.index": "Result Grades",
    "report-cards.index": "Report Cards",
    "fee-types.index": "Fee Types",
    "fee-structures.index": "Fee Structures",
    "fee-dues.index": "Fee Dues",
    "fee-assignments.index": "Fee Assignments",
    "fee-collection.index": "Fee Collection",
    "fee-collection": "Fee Collection",
    "payment-history.index": "Payment History",
    "fee-receipts.index": "Fee Receipts",
    "discounts.index": "Fee Discounts",
    "late-fees.index": "Late Fee Rules",
    "fee-reports.index": "Fee Reports",
    "expense-categories.index": "Expense Categories",
    "expenses.index": "Expenses",
    "expense-payments.index": "Expense Payments",
    "recurring-expenses.index": "Recurring Expenses",
    "expense-reports.index": "Expense Reports",
    "income-categories.index": "Income Categories",
    "incomes.index": "Income Entries",
    "income-payments.index": "Income Payments",
    "recurring-incomes.index": "Recurring Incomes",
    "income-reports.index": "Income Reports",
    "admin.admissions.applications": "Online Admission Applications",
    "admin.admissions.inquiries": "Website Inquiries",
    "admin.website.manager": "Website Content Management (CMS)",
    "website-manager": "Website Content Management (CMS)",
    "library.dashboard": "Library Dashboard",
    "hostel.dashboard": "Hostel Dashboard",
    "hostel.reports": "Hostel Reports",
    "profile": "My Profile",
    "settings": "Settings",
};

function formatRouteTitle(name) {
    if (!name) return "School ERP";

    const raw = String(name).trim();

    // 1. Direct map lookup
    if (routeTitleMap[raw]) {
        return routeTitleMap[raw];
    }

    // 2. Singularize helper
    const singularize = (str) => {
        if (!str) return "";
        if (str.endsWith("ies")) return str.slice(0, -3) + "y";
        if (str.endsWith("ses")) return str.slice(0, -2);
        if (str.endsWith("s") && !str.endsWith("ss") && !str.endsWith("us")) return str.slice(0, -1);
        return str;
    };

    // 3. Title Case helper
    const toTitleCase = (str) => {
        return str
            .replace(/[-_]/g, " ")
            .split(" ")
            .filter(Boolean)
            .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
            .join(" ");
    };

    // 4. Dot-separated names (e.g. "subjects.create", "classes.edit", "student-attendance.monthly")
    if (raw.includes(".")) {
        const parts = raw.split(".");
        const action = parts.pop().toLowerCase();
        const rawEntity = parts.join("-");
        const entityTitle = toTitleCase(rawEntity);
        const singularEntity = singularize(entityTitle);

        if (action === "index" || action === "list") {
            return entityTitle;
        }

        if (action === "create" || action === "add" || action === "new") {
            return `Create ${singularEntity}`;
        }

        if (action === "edit" || action === "update") {
            return `Edit ${singularEntity}`;
        }

        if (action === "show" || action === "view" || action === "details") {
            return `${singularEntity} Details`;
        }

        if (action === "bulk") {
            return `Bulk ${entityTitle}`;
        }

        if (action === "monthly") {
            return `Monthly ${entityTitle}`;
        }

        if (action === "report" || action === "reports") {
            return `${entityTitle} Reports`;
        }

        return `${toTitleCase(action)} ${entityTitle}`;
    }

    // 5. Standalone kebab-case or snake_case
    return toTitleCase(raw);
}

const pageTitle = computed(() => {
    if (route.meta?.title) {
        return route.meta.title;
    }

    return formatRouteTitle(route.name);
});

watchEffect(() => {
    if (pageTitle.value) {
        document.title = `${pageTitle.value} | School ERP`;
    }
});


/*
|--------------------------------------------------------------------------
| USER INITIAL
|--------------------------------------------------------------------------
*/

const userInitial = computed(() => {

    const name =
        auth.user?.name ||
        "U";

    return name
        .charAt(0)
        .toUpperCase();

});


/*
|--------------------------------------------------------------------------
| USER ROLE
|--------------------------------------------------------------------------
*/

const userRole = computed(() => {

    return (
        auth.user?.roles?.[0]?.name ||
        auth.user?.user_type ||
        "User"
    );

});


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

async function logout() {

    /*
    |--------------------------------------------------------------------------
    | Prevent double click
    |--------------------------------------------------------------------------
    */

    if (loggingOut.value) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Confirmation
    |--------------------------------------------------------------------------
    */

    const result = await Swal.fire({

        title: "Logout?",

        text: "Do you really want to logout?",

        icon: "question",

        showCancelButton: true,

        confirmButtonText: "Logout",

        cancelButtonText: "Cancel",

        reverseButtons: true,

        buttonsStyling: true

    });


    if (!result.isConfirmed) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Start Logout
    |--------------------------------------------------------------------------
    */

    loggingOut.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | Call Pinia Auth Logout
        |--------------------------------------------------------------------------
        */

        await auth.logout();

    } catch (error) {

        /*
        |--------------------------------------------------------------------------
        | Important:
        |
        | Even if API logout fails because the token is already expired,
        | frontend authentication must still be cleared.
        |--------------------------------------------------------------------------
        */

        console.warn(
            "Logout API returned an error:",
            error
        );

    } finally {

        /*
        |--------------------------------------------------------------------------
        | Clear known localStorage values
        |--------------------------------------------------------------------------
        */

        localStorage.removeItem("token");
        localStorage.removeItem("active_school_id");
        localStorage.removeItem("user");

        localStorage.removeItem("auth_user");

        localStorage.removeItem("currentUser");

        localStorage.removeItem("authUser");

        localStorage.removeItem("logged_user");


        /*
        |--------------------------------------------------------------------------
        | Clear known sessionStorage values
        |--------------------------------------------------------------------------
        */

        sessionStorage.removeItem("token");

        sessionStorage.removeItem("user");

        sessionStorage.removeItem("auth_user");

        sessionStorage.removeItem("currentUser");

        sessionStorage.removeItem("authUser");

        sessionStorage.removeItem("logged_user");


        /*
        |--------------------------------------------------------------------------
        | Clear any auth/token related localStorage values
        |--------------------------------------------------------------------------
        */

        try {

            Object.keys(localStorage).forEach(key => {

                const lowerKey =
                    key.toLowerCase();

                if (
                    lowerKey.includes("token") ||
                    lowerKey.includes("auth")
                ) {

                    localStorage.removeItem(key);

                }

            });

        } catch (error) {

            console.warn(
                "Unable to clear some localStorage values.",
                error
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Redirect to Login (Preserve School Context for School Admin/Staff)
        |--------------------------------------------------------------------------
        */

        const schoolCode = auth.user?.school?.code || currentSchoolCode.value || "";
        const wasSuperAdmin = isSuperAdmin.value;

        if (schoolCode && !wasSuperAdmin) {
            await router.replace(`/s/${schoolCode}/login`);
        } else {
            await router.replace("/login");
        }


        /*
        |--------------------------------------------------------------------------
        | Success Message
        |--------------------------------------------------------------------------
        */

        await Swal.fire({

            icon: "success",

            title: "Logged Out",

            text: "You have been logged out successfully.",

            timer: 1500,

            showConfirmButton: false

        });


        loggingOut.value = false;

    }

}

</script>


<style scoped>

/* =========================================================
   NAVBAR
========================================================= */

.admin-navbar {

    width: 100%;

    height: 72px;

    min-height: 72px;

    background: #ffffff;

    border-bottom: 1px solid #e9ecef;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 24px;

    position: sticky;

    top: 0;

    z-index: 1000;

    box-sizing: border-box;

}


/* =========================================================
   LEFT
========================================================= */

.navbar-left {

    min-width: 0;

    display: flex;

    align-items: center;

}


/* =========================================================
   SIDEBAR BUTTON
========================================================= */

.sidebar-menu-btn {

    width: 40px;

    height: 40px;

    min-width: 40px;

    border: 0;

    border-radius: 9px;

    background: #f1f3f5;

    color: #343a40;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

    cursor: pointer;

    margin-right: 12px;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.15s ease;

}


.sidebar-menu-btn:hover {

    background: #e9ecef;

    color: #0d6efd;

}


.sidebar-menu-btn:active {

    transform: scale(0.94);

}


.sidebar-menu-btn:focus {

    outline: none;

    box-shadow:
        0 0 0 3px rgba(13, 110, 253, 0.15);

}


/* =========================================================
   PAGE TITLE
========================================================= */

.page-title {

    min-width: 0;

    display: flex;

    align-items: center;

    color: #212529;

    font-size: 18px;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


.page-title-icon {

    color: #0d6efd;

    font-size: 18px;

    flex-shrink: 0;

    margin-right: 8px;

}


/* =========================================================
   RIGHT
========================================================= */

.navbar-right {

    flex-shrink: 0;

    display: flex;

    align-items: center;

    gap: 14px;

}


/* =========================================================
   USER
========================================================= */

.navbar-user {

    display: flex;

    align-items: center;

    min-width: 0;

}


/* =========================================================
   AVATAR
========================================================= */

.navbar-avatar {

    width: 38px;

    height: 38px;

    min-width: 38px;

    border-radius: 50%;

    background: #0d6efd;

    color: #ffffff;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 14px;

    font-weight: 700;

}


/* =========================================================
   USER INFO
========================================================= */

.navbar-user-info {

    min-width: 0;

    margin-left: 9px;

}


.navbar-user-name {

    max-width: 150px;

    color: #212529;

    font-size: 14px;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


.navbar-user-info small {

    display: block;

    max-width: 150px;

    color: #6c757d;

    font-size: 12px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


/* =========================================================
   LOGOUT
========================================================= */

.logout-btn {

    height: 40px;

    border: 0;

    border-radius: 8px;

    padding: 0 14px;

    background: #dc3545;

    color: #ffffff;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 6px;

    font-size: 13px;

    font-weight: 500;

    white-space: nowrap;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.15s ease;

}


.logout-btn:hover {

    background: #bb2d3b;

}


.logout-btn:active {

    transform: scale(0.96);

}


.logout-btn:disabled {

    opacity: 0.7;

    cursor: not-allowed;

}


.logout-btn:focus {

    outline: none;

    box-shadow:
        0 0 0 3px rgba(220, 53, 69, 0.15);

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 991.98px) {

    .admin-navbar {

        padding: 0 16px;

    }


    .navbar-user-info {

        display: none;

    }


    .navbar-right {

        gap: 10px;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 575.98px) {

    .admin-navbar {

        height: 60px;

        min-height: 60px;

        padding: 0 10px;

    }


    .sidebar-menu-btn {

        width: 36px;

        height: 36px;

        min-width: 36px;

        margin-right: 7px;

        font-size: 18px;

    }


    .page-title {

        font-size: 14px;

    }


    .page-title-icon {

        font-size: 14px;

        margin-right: 4px;

    }


    .navbar-right {

        gap: 6px;

    }


    .navbar-avatar {

        width: 32px;

        height: 32px;

        min-width: 32px;

        font-size: 13px;

    }


    .logout-btn {

        width: 34px;

        height: 34px;

        padding: 0;

        min-width: 34px;

        border-radius: 8px;

    }


    .logout-text {

        display: none;

    }

}


/* =========================================================
   VERY SMALL MOBILE
========================================================= */

@media (max-width: 280px) {

    .admin-navbar {

        padding: 0 7px;

    }


    .sidebar-menu-btn {

        width: 34px;

        height: 34px;

        min-width: 34px;

        margin-right: 5px;

    }


    .page-title {

        max-width: 70px;

        font-size: 13px;

    }


    .page-title-icon {

        display: none;

    }


    .navbar-avatar {

        display: none;

    }


    .navbar-right {

        gap: 4px;

    }


    .logout-btn {

        width: 32px;

        height: 32px;

        min-width: 32px;

    }

}

</style>