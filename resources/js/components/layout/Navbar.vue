<template>
<nav class="navbar navbar-light bg-white shadow-sm">

    <div class="container-fluid">

        <span class="navbar-brand">
            School ERP
        </span>

        <button
            class="btn btn-danger"
            @click="logout"
        >
            Logout
        </button>

    </div>

</nav>
</template>

<script setup>

import Swal from "sweetalert2";

import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/auth";

const router = useRouter();

const auth = useAuthStore();

async function logout(){

    const result = await Swal.fire({

        title:"Logout?",

        text:"Do you really want to logout?",

        icon:"question",

        showCancelButton:true,

        confirmButtonText:"Logout"

    });

    if(!result.isConfirmed){

        return;

    }

    await auth.logout();

    await Swal.fire({
        icon: "success",
        title: "Logged Out",
        text: "You have been logged out successfully.",
        timer: 1500,
        showConfirmButton: false,
    });

    router.replace("/login");

}
</script>