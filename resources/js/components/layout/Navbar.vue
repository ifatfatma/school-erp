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
            <div class="page-title">

                <i class="bi bi-mortarboard-fill page-title-icon"></i>

                <span>
                    {{ pageTitle }}
                </span>

            </div>

        </div>


        <!-- =====================================================
             RIGHT SIDE
        ====================================================== -->
        <div class="navbar-right">

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
    ref
} from "vue";

import {
    useRoute,
    useRouter
} from "vue-router";

import Swal from "sweetalert2";

import {
    useAuthStore
} from "@/stores/auth";


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
| PAGE TITLE
|--------------------------------------------------------------------------
*/

const pageTitle = computed(() => {

    /*
    |--------------------------------------------------------------------------
    | Use route meta title first
    |--------------------------------------------------------------------------
    */

    if (route.meta?.title) {

        return route.meta.title;

    }


    /*
    |--------------------------------------------------------------------------
    | Convert route name into readable title
    |--------------------------------------------------------------------------
    */

    if (route.name) {

        return String(route.name)
            .replace(/[-_]/g, " ")
            .replace(/\b\w/g, letter =>
                letter.toUpperCase()
            );

    }


    return "School ERP";

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
        | Redirect to Login
        |--------------------------------------------------------------------------
        */

        await router.replace("/login");


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