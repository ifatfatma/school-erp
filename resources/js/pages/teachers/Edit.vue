<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useTeacherStore } from "@/stores/teacher";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import Form from "./Form.vue";

const route = useRoute();
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

async function loadTeacher() {

    try {

        const teacher = await store.find(route.params.id);

        Object.assign(form, {
            employee_id: teacher.employee_id,
            first_name: teacher.first_name,
            last_name: teacher.last_name,
            gender: teacher.gender,
            date_of_birth: teacher.date_of_birth,
            phone: teacher.phone,
            email: teacher.email,
            qualification: teacher.qualification,
            experience: teacher.experience,
            joining_date: teacher.joining_date,
            address: teacher.address,
            photo: teacher.photo,
            status: teacher.status,
        });

    } catch (e) {

        console.error(e);

        router.push({
            name: "teachers.index",
        });

    }

}

async function updateTeacher() {

    try {

        await store.update(route.params.id, form);

        router.push({
            name: "teachers.index",
        });

    } catch (e) {

        console.error(e);

    }

}

onMounted(loadTeacher);
</script>

<template>

    <AppBreadcrumb
        title="Edit Teacher"
        :items="[
            { title: 'Teachers', to: { name:'teachers.index' } },
            { title: 'Edit' }
        ]"
    />

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Edit Teacher
            </h5>
            
        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
                :loading="store.saving"
                @submit="updateTeacher"
            />

        </div>

    </div>

</template>