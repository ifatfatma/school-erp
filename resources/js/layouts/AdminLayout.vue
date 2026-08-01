<template>

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <div class="sidebar-wrapper">

        <Sidebar />

    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 content-wrapper">

        <Navbar />

        <main class="container-fluid py-4 px-4">

            <RouterView />

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

<style scoped>
.sidebar-wrapper{
    flex: 0 0 260px;
    width:260px;
}

.content-wrapper{
    flex:1;
    min-width:0;
}
</style>