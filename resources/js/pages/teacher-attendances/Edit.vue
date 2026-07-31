<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";

import { useTeacherAttendanceStore } from "@/stores/teacherAttendance";
import { useTeacherStore } from "@/stores/teacher";

const router = useRouter();
const route = useRoute();

const attendanceStore = useTeacherAttendanceStore();
const teacherStore = useTeacherStore();

const loading = ref(false);

const form = reactive({
    teacher_id: "",
    attendance_date: "",
    status: "Present",
    check_in: "",
    check_out: "",
    remarks: "",
});

onMounted(async () => {

    loading.value = true;

    try {

        await Promise.all([
            teacherStore.fetch(),
            attendanceStore.fetchItem(route.params.id)
        ]);

        const item = attendanceStore.item;

        form.teacher_id = item.teacher_id;
        form.attendance_date = item.attendance_date;
        form.status = item.status;
        form.check_in = item.check_in ?? "";
        form.check_out = item.check_out ?? "";
        form.remarks = item.remarks ?? "";

    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

});

async function submit() {

    loading.value = true;

    attendanceStore.errors = {};

    try {

        const formData = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            formData.append(key, value ?? "");
        });

        await attendanceStore.update(
            route.params.id,
            formData
        );

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

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Edit Teacher Attendance</h5>
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