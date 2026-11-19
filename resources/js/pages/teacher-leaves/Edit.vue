<script setup>
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";

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
    try {
        const [teacherResult, leaveTypeResult] = await Promise.allSettled([
            teacherApi.getAll({
                per_page: 1000,
                status: 1
            }),
            leaveTypeApi.getAll({
                per_page: 1000,
                status: 1
            })
        ]);

        if (teacherResult.status === 'fulfilled' && teacherResult.value?.data) {
            const raw = teacherResult.value.data;
            teachers.value = Array.isArray(raw.data) ? raw.data : (Array.isArray(raw) ? raw : []);
        } else {
            console.warn("Could not load teachers:", teacherResult?.reason);
            teachers.value = [];
        }

        if (leaveTypeResult.status === 'fulfilled' && leaveTypeResult.value?.data) {
            const raw = leaveTypeResult.value.data;
            leaveTypes.value = Array.isArray(raw.data) ? raw.data : (Array.isArray(raw) ? raw : []);
        } else {
            console.warn("Could not load leave types:", leaveTypeResult?.reason);
            leaveTypes.value = [];
        }

    } catch (e) {
        console.error("Error loading leave dropdowns:", e);
    }
};

const loadLeaveRequest = async () => {
    try {
        const data = await store.get(route.params.id);
        if (data) {
            form.teacher_id = data.teacher_id;
            form.leave_type_id = data.leave_type_id;
            form.from_date = data.from_date;
            form.to_date = data.to_date;
            form.total_days = data.total_days;
            form.reason = data.reason;
            form.status = data.status;
            form.remarks = data.remarks;
        }
    } catch (e) {
        console.error("Error loading leave request:", e);
    }
};

const submit = async () => {
    loading.value = true;

    try {
        await store.update(route.params.id, form);

        await Swal.fire({
            icon: 'success',
            title: 'Updated!',
            text: 'Leave request updated successfully.',
            timer: 1500,
            showConfirmButton: false,
        });

        router.push({
            name: "teacher-leaves.index"
        });

    } catch (e) {
        console.error(e);
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: e.response?.data?.message || 'Could not update leave request.',
        });
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
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Teacher Leaves', to: { name: 'teacher-leaves.index' } },
            { label: 'Edit Leave' }
        ]"
    />

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-pencil-square text-primary me-2"></i>Edit Leave Request
            </h5>

            <RouterLink
                :to="{ name:'teacher-leaves.index' }"
                class="btn btn-sm btn-outline-secondary rounded-pill px-3"
            >
                <i class="bi bi-arrow-left me-1"></i> Back
            </RouterLink>
        </div>

        <div class="card-body p-4">
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
