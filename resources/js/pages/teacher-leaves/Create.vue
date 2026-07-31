<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";

import { useLeaveRequestStore } from "@/stores/leaveRequest";
import teacherApi from "@/api/teacher";
import leaveTypeApi from "@/api/leaveType";

const router = useRouter();
const store = useLeaveRequestStore();

const loading = ref(false);

const teachers = ref([]);
const leaveTypes = ref([]);

const form = reactive({
    teacher_id: "",
    leave_type_id: "",
    from_date: "",
    to_date: "",
    total_days: 0,
    reason: "",
    attachment: null,
    status: 0,
    remarks: "",
});

const loadDropdowns = async () => {

    try {

        const [teacherRes, leaveTypeRes] = await Promise.all([
            teacherApi.getAll({
                per_page: 1000,
                status: 1
            }),
            leaveTypeApi.getAll({
                per_page: 1000,
                status: 1
            })
        ]);

        teachers.value = teacherRes.data.data;
        leaveTypes.value = leaveTypeRes.data.data;

    } catch (e) {
        console.error(e);
    }

};

const submit = async () => {

    loading.value = true;

    try {

        await store.create(form);

        alert("Leave request created successfully.");

        router.push({
            name: "teacher-leaves.index"
        });

    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

};

onMounted(loadDropdowns);
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <h5>Create Leave Request</h5>

            <RouterLink
                class="btn btn-secondary"
                :to="{ name: 'teacher-leaves.index' }"
            >
                Back
            </RouterLink>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
                :teachers="teachers"
                :leave-types="leaveTypes"
                @submit="submit"
            />

        </div>

    </div>

</div>

</template>