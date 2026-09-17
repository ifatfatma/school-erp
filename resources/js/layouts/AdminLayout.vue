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

                <RouterView />

            </main>


            <!-- FOOTER -->
            <Footer />

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    onMounted,
    onBeforeUnmount
} from "vue";

import Sidebar from "@/components/layout/Sidebar.vue";
import Navbar from "@/components/layout/Navbar.vue";
import Footer from "@/components/layout/Footer.vue";


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