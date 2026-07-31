<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";

import { useTeacherAttendanceStore } from "@/stores/teacherAttendance";
import { useTeacherStore } from "@/stores/teacher";

const router = useRouter();

const attendanceStore = useTeacherAttendanceStore();
const teacherStore = useTeacherStore();

const loading = ref(false);

const form = reactive({
    teacher_id: "",
    attendance_date: new Date().toISOString().split("T")[0],
    status: "Present",
    check_in: "",
    check_out: "",
    remarks: "",
});

onMounted(async () => {
    await teacherStore.fetch();
});

async function submit() {

    loading.value = true;

    attendanceStore.errors = {};

    try {

        const formData = new FormData();

        Object.keys(form).forEach(key => {
            formData.append(key, form[key] ?? "");
        });

        await attendanceStore.create(formData);

        router.push({
            name: "teacher-attendances.index",
        });

    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
}

function cancel() {
    router.back();
}
</script>

<template>

    <div class="card">

        <div class="card-header">
            <h5>Create Teacher Attendance</h5>
        </div>

        <div class="card-body">

            <Form
                :form="form"
                :teachers="teacherStore.items"
                :errors="attendanceStore.errors"
                :loading="loading"
                @submit="submit"
                @cancel="cancel"
            />

        </div>

    </div>

</template>