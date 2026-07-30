<template>

<div class="container py-5">

    <h2>

        Welcome {{ auth.user?.name }}

    </h2>

    <hr>

    <div class="card">

        <div class="card-body">

            <h5>School ERP Dashboard</h5>

            <p>

                Logged in as

                <strong>

                    {{ auth.user?.roles[0]?.name }}

                </strong>

            </p>

        </div>

    </div>

</div>

</template>

<script setup>

import { useAuthStore } from "@/stores/auth";

const auth = useAuthStore();

</script>