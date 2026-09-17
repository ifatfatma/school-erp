<template>
    <nav class="navbar navbar-light bg-white shadow-sm">

        <div class="container-fluid">

            <span class="navbar-brand">
                School ERP
            </span>

            <button
                type="button"
                class="btn btn-danger"
                :disabled="loggingOut"
                @click="logout"
            >
                <span
                    v-if="loggingOut"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                {{ loggingOut ? "Logging out..." : "Logout" }}
            </button>

        </div>

    </nav>
</template>


<script setup>

import Swal from "sweetalert2";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";


const router = useRouter();

const auth = useAuthStore();

const loggingOut = ref(false);


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

async function logout() {

    /*
    |--------------------------------------------------------------------------
    | Already logging out
    |--------------------------------------------------------------------------
    */

    if (loggingOut.value) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIRM
    |--------------------------------------------------------------------------
    */

    const result = await Swal.fire({

        title: "Logout?",

        text: "Do you really want to logout?",

        icon: "question",

        showCancelButton: true,

        confirmButtonText: "Logout",

        cancelButtonText: "Cancel",

        reverseButtons: true

    });


    if (!result.isConfirmed) {
        return;
    }


    loggingOut.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | Call Store Logout
        |--------------------------------------------------------------------------
        */

        await auth.logout();


    } catch (error) {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | A 401 here means the server token is already invalid/expired.
        | We still need to logout the frontend.
        |
        */

        console.warn(
            "Logout API returned an error:",
            error
        );

    } finally {

        /*
        |--------------------------------------------------------------------------
        | ALWAYS CLEAR FRONTEND AUTH
        |--------------------------------------------------------------------------
        */

        localStorage.removeItem("token");

        localStorage.removeItem("user");

        localStorage.removeItem("auth_user");

        localStorage.removeItem("currentUser");

        localStorage.removeItem("authUser");

        localStorage.removeItem("logged_user");


        sessionStorage.removeItem("token");

        sessionStorage.removeItem("user");

        sessionStorage.removeItem("auth_user");

        sessionStorage.removeItem("currentUser");

        sessionStorage.removeItem("authUser");

        sessionStorage.removeItem("logged_user");


        /*
        |--------------------------------------------------------------------------
        | Clear any auth-related localStorage values
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

        } catch (e) {

            console.warn(
                "Unable to clear some localStorage values.",
                e
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Redirect
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