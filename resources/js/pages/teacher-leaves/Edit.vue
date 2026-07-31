<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";

import { useLeaveRequestStore } from "@/stores/leaveRequest";
import teacherApi from "@/api/teacher";
import leaveTypeApi from "@/api/leaveType";

const route = useRoute();
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
};

const loadLeaveRequest = async () => {

    const data = await store.get(route.params.id);

    form.teacher_id = data.teacher_id;
    form.leave_type_id = data.leave_type_id;
    form.from_date = data.from_date;
    form.to_date = data.to_date;
    form.total_days = data.total_days;
    form.reason = data.reason;
    form.status = data.status;
    form.remarks = data.remarks;
};

const submit = async () => {

    loading.value = true;

    try {

        await store.update(route.params.id, form);

        alert("Leave request updated successfully.");

        router.push({
            name: "teacher-leaves.index"
        });

    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

};

onMounted(async () => {

    await loadDropdowns();

    await loadLeaveRequest();

});
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5>Edit Leave Request</h5>

            <RouterLink
                :to="{ name:'teacher-leaves.index' }"
                class="btn btn-secondary"
            >
                Back
            </RouterLink>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :teachers="teachers"
                :leave-types="leaveTypes"
                :errors="store.errors"
                :is-edit="true"
                @submit="submit"
            />

        </div>

    </div>

</div>

</template>