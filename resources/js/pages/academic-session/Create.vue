<script setup>

import { reactive } from "vue";

import { useRouter } from "vue-router";

import { useAuthStore } from "@/stores/auth";
import { useAcademicSessionStore } from "@/stores/academicSession";

import Form from "./Form.vue";

const store = useAcademicSessionStore();

const auth = useAuthStore();

const router = useRouter();

const form = reactive({

    school_id: auth.user.school_id,

    name: "",

    start_date: "",

    end_date: "",

    status: true,

    is_current: false

});

async function save() {

    await store.create(form);

    router.push("/academic-sessions");

}

</script>

<template>

<div class="container-fluid">

    <h3>Create Academic Session</h3>

    <Form
        :form="form"
        @submit="save"
    />

</div>

</template>