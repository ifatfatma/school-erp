<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import { useTeacherStore } from "@/stores/teacher";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import Form from "./Form.vue";

const router = useRouter();
const store = useTeacherStore();

const form = reactive({
    employee_id: "",
    first_name: "",
    last_name: "",
    gender: "Male",
    date_of_birth: "",
    phone: "",
    email: "",
    qualification: "",
    experience: "",
    joining_date: "",
    address: "",
    photo: null,
    status: 1,
});

async function save() {
    try {

        await store.create(form);

        router.push({
            name: "teachers.index",
        });

    } catch (e) {
        console.error(e);
    }
}
</script>

<template>
<div class="container-fluid">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Teachers', to: { name: 'teachers.index' } },
                { label: 'Create' }
            ]"
        />
    

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Add Teacher
            </h5>
            
        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
                :loading="store.saving"
                @submit="save"
            />

        </div>

    </div>
    </div>

</template>