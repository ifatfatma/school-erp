<template>

<div class="d-flex">

    <Sidebar />

    <div class="flex-grow-1">

        <Navbar />

        <main class="container-fluid p-4">

            <router-view />

        </main>

        <Footer />

    </div>

</div>

</template>

<script setup>

import Sidebar from "@/components/layout/Sidebar.vue";
import Navbar from "@/components/layout/Navbar.vue";
import Footer from "@/components/layout/Footer.vue";

</script>