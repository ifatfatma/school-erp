<template>
    <div class="admin-layout">

        <!-- =========================================
             SIDEBAR
        ========================================== -->
        <aside
            class="admin-sidebar"
            :class="{
                'sidebar-collapsed': sidebarCollapsed,
                'mobile-open': mobileSidebarOpen
            }"
        >
            <Sidebar
                :collapsed="sidebarCollapsed"
                @toggle-collapse="toggleDesktopSidebar"
                @close-mobile="closeMobileSidebar"
            />
        </aside>


        <!-- =========================================
             MOBILE OVERLAY
        ========================================== -->
        <div
            v-if="mobileSidebarOpen"
            class="sidebar-overlay"
            @click="closeMobileSidebar"
        ></div>


        <!-- =========================================
             MAIN CONTENT
        ========================================== -->
        <div
            class="admin-content"
            :class="{
                'content-collapsed': sidebarCollapsed
            }"
        >

            <!-- NAVBAR -->
            <Navbar
                @toggle-sidebar="toggleSidebarFromNavbar"
            />
            


            <!-- PAGE CONTENT -->
            <main class="admin-main">

                <!-- Subscription Feature Locked State -->
                <div v-if="isRouteLocked" class="container py-5 text-center">
                    <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto bg-white" style="max-width: 580px;">
                        <div class="bg-warning-subtle text-warning rounded-circle mx-auto p-4 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-lock-fill fs-1 text-warning"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Feature Not Included in Subscription</h4>
                        <p class="text-muted small mb-4">
                            The <strong>{{ lockedFeatureName }}</strong> module is not available on 
                            <strong>{{ currentSchoolName }}</strong>'s current 
                            <span class="badge bg-secondary-subtle text-secondary border font-monospace">{{ currentPlanName }}</span> package.
                            Please upgrade your institution's subscription plan to access this module.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <router-link to="/dashboard" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-speedometer2 me-1"></i> Return to Dashboard
                            </router-link>
                        </div>
                    </div>
                </div>

                <RouterView v-else />

            </main>


            <!-- FOOTER -->
            <Footer />

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    computed,
    onMounted,
    onBeforeUnmount
} from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useTenantStore } from "@/stores/tenant";

import Sidebar from "@/components/layout/Sidebar.vue";
import Navbar from "@/components/layout/Navbar.vue";
import Footer from "@/components/layout/Footer.vue";

const route = useRoute();
const auth = useAuthStore();
const tenantStore = useTenantStore();

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

const currentRequiredFeature = computed(() => {
    if (route.meta?.feature) return route.meta.feature;
    for (const [prefix, feat] of Object.entries(PATH_FEATURE_MAP)) {
        if (route.path.startsWith(prefix)) {
            return feat;
        }
    }
    return null;
});

const isRouteLocked = computed(() => {
    const feat = currentRequiredFeature.value;
    if (!feat) return false;

    const user = auth.user;
    if (!user) return false;

    // Super Admin has 100% full, unrestricted access to all modules and features in School ERP
    const isSuperAdminUser = user?.user_type === "super_admin" || user?.roles?.some(r => r.name === "Super Admin");
    if (isSuperAdminUser) return false;

    const activeSchool = user.school;
    if (!activeSchool) return false;

    const features = activeSchool.features || [];
    const plan = (activeSchool.subscription_plan || "starter").toLowerCase();

    if (Array.isArray(features) && features.length > 0) {
        return !features.includes(feat);
    }

    const upperFeatures = ["hostel", "library", "transport", "inventory", "website_cms", "online_admissions", "custom_domain", "multi_branch"];
    return (plan === "starter" || plan === "basic") && upperFeatures.includes(feat);
});

const lockedFeatureName = computed(() => {
    return (currentRequiredFeature.value || "This").replace(/_/g, " ").toUpperCase();
});

const currentSchoolName = computed(() => {
    return tenantStore.currentTenant?.name || auth.user?.school?.name || "School";
});

const currentPlanName = computed(() => {
    const plan = tenantStore.currentTenant?.subscription_plan || auth.user?.school?.subscription_plan || "starter";
    return plan.toUpperCase();
});


/*
|--------------------------------------------------------------------------
| SIDEBAR STATE
|--------------------------------------------------------------------------
*/

const sidebarCollapsed = ref(false);

const mobileSidebarOpen = ref(false);


/*
|--------------------------------------------------------------------------
| SCREEN SIZE
|--------------------------------------------------------------------------
*/

function isMobile() {

    return window.innerWidth < 992;

}


/*
|--------------------------------------------------------------------------
| NAVBAR HAMBURGER
|--------------------------------------------------------------------------
*/

function toggleSidebarFromNavbar() {

    if (isMobile()) {

        mobileSidebarOpen.value =
            !mobileSidebarOpen.value;

        return;

    }


    sidebarCollapsed.value =
        !sidebarCollapsed.value;

}


/*
|--------------------------------------------------------------------------
| DESKTOP SIDEBAR TOGGLE
|--------------------------------------------------------------------------
*/

function toggleDesktopSidebar() {

    if (isMobile()) {

        mobileSidebarOpen.value =
            !mobileSidebarOpen.value;

        return;

    }


    sidebarCollapsed.value =
        !sidebarCollapsed.value;

}


/*
|--------------------------------------------------------------------------
| CLOSE MOBILE SIDEBAR
|--------------------------------------------------------------------------
*/

function closeMobileSidebar() {

    mobileSidebarOpen.value = false;

}


/*
|--------------------------------------------------------------------------
| ESC KEY
|--------------------------------------------------------------------------
*/

function handleKeydown(event) {

    if (
        event.key === "Escape" &&
        mobileSidebarOpen.value
    ) {

        closeMobileSidebar();

    }

}


/*
|--------------------------------------------------------------------------
| WINDOW RESIZE
|--------------------------------------------------------------------------
*/

function handleResize() {

    /*
    |--------------------------------------------------------------------------
    | When moving from mobile to desktop
    |--------------------------------------------------------------------------
    */

    if (!isMobile()) {

        mobileSidebarOpen.value = false;

    }

}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    window.addEventListener(
        "keydown",
        handleKeydown
    );

    window.addEventListener(
        "resize",
        handleResize
    );

});


onBeforeUnmount(() => {

    window.removeEventListener(
        "keydown",
        handleKeydown
    );

    window.removeEventListener(
        "resize",
        handleResize
    );

});

</script>


<style scoped>

/* =========================================================
   MAIN LAYOUT
========================================================= */

.admin-layout {

    min-height: 100vh;

    background: #f5f7fb;

}


/* =========================================================
   SIDEBAR
========================================================= */

.admin-sidebar {

    position: fixed;

    top: 0;

    left: 0;

    width: 260px;

    height: 100vh;

    z-index: 1050;

    overflow: hidden;

    transition:
        width 0.25s ease,
        transform 0.25s ease;

}


.admin-sidebar.sidebar-collapsed {

    width: 75px;

}


/* =========================================================
   MAIN CONTENT
========================================================= */

.admin-content {

    min-height: 100vh;

    margin-left: 260px;

    width: calc(100% - 260px);

    display: flex;

    flex-direction: column;

    transition:
        margin-left 0.25s ease,
        width 0.25s ease;

}


.admin-content.content-collapsed {

    margin-left: 75px;

    width: calc(100% - 75px);

}


/* =========================================================
   PAGE CONTENT
========================================================= */

.admin-main {

    flex: 1;

    min-width: 0;

    padding: 24px;

}


/* =========================================================
   MOBILE OVERLAY
========================================================= */

.sidebar-overlay {

    position: fixed;

    inset: 0;

    z-index: 1040;

    background: rgba(0, 0, 0, 0.45);

    backdrop-filter: blur(2px);

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 991.98px) {

    .admin-sidebar {

        width: 260px;

        transform: translateX(-100%);

        box-shadow: none;

    }


    .admin-sidebar.mobile-open {

        transform: translateX(0);

        box-shadow:
            10px 0 30px rgba(0, 0, 0, 0.18);

    }


    /*
    |--------------------------------------------------------------------------
    | Sidebar collapsed state does not affect mobile width
    |--------------------------------------------------------------------------
    */

    .admin-sidebar.sidebar-collapsed {

        width: 260px;

    }


    .admin-content,
    .admin-content.content-collapsed {

        margin-left: 0;

        width: 100%;

    }


    .admin-main {

        padding: 18px 15px;

    }

}


/* =========================================================
   SMALL MOBILE
========================================================= */

@media (max-width: 575.98px) {

    .admin-main {

        padding: 15px 12px;

    }

}

</style>