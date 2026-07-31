<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useTeacherAttendanceStore } from "@/stores/teacherAttendance";
import { useTeacherStore } from "@/stores/teacher";

const router = useRouter();

const attendanceStore = useTeacherAttendanceStore();
const teacherStore = useTeacherStore();

const deleting = ref(false);

const columns = [
    { key: "teacher", label: "Teacher" },
    { key: "attendance_date", label: "Date" },
    { key: "status", label: "Status" },
    { key: "check_in", label: "Check In" },
    { key: "check_out", label: "Check Out" },
    { key: "action", label: "Action" },
];

const filters = reactive({
    teacher_id: "",
    attendance_date: "",
    status: "",
});

onMounted(async () => {
    await Promise.all([
        teacherStore.fetch(),
        loadData(),
    ]);
});

async function loadData() {

    attendanceStore.filters.teacher_id = filters.teacher_id;
    attendanceStore.filters.attendance_date = filters.attendance_date;
    attendanceStore.filters.status = filters.status;

    await attendanceStore.fetchItems();

}

function createAttendance() {
    router.push({
        name: "teacher-attendances.create",
    });
}

function editAttendance(id) {
    router.push({
        name: "teacher-attendances.edit",
        params: { id },
    });
}

function showAttendance(id) {
    router.push({
        name: "teacher-attendances.show",
        params: { id },
    });
}

async function deleteAttendance(id) {

    if (!confirm("Delete this attendance?"))
        return;

    deleting.value = true;

    try {

        await attendanceStore.delete(id);

        await loadData();

    } finally {

        deleting.value = false;

    }

}

function badge(status) {

    switch (status) {

        case "Present":
            return "bg-success";

        case "Absent":
            return "bg-danger";

        case "Late":
            return "bg-warning";

        case "Half Day":
            return "bg-info";

        case "Leave":
            return "bg-secondary";

        default:
            return "bg-light text-dark";
    }

}
</script>

<template>

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h5>Teacher Attendance</h5>

        <button
            class="btn btn-primary"
            @click="createAttendance"
        >
            Add Attendance
        </button>

    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-4">

                <select
                    class="form-select"
                    v-model="filters.teacher_id"
                >

                    <option value="">All Teachers</option>

                    <option
                        v-for="teacher in teacherStore.items"
                        :key="teacher.id"
                        :value="teacher.id"
                    >
                        {{ teacher.name }}
                    </option>

                </select>

            </div>

            <div class="col-md-3">

                <input
                    type="date"
                    class="form-control"
                    v-model="filters.attendance_date"
                >

            </div>

            <div class="col-md-3">

                <select
                    class="form-select"
                    v-model="filters.status"
                >
                    <option value="">All Status</option>
                    <option>Present</option>
                    <option>Absent</option>
                    <option>Late</option>
                    <option>Half Day</option>
                    <option>Leave</option>
                </select>

            </div>

            <div class="col-md-2">

                <button
                    class="btn btn-success w-100"
                    @click="loadData"
                >
                    Search
                </button>

            </div>

        </div>

        <AppTable
            :columns="columns"
            :items="attendanceStore.items"
            :loading="attendanceStore.loading"
        >

            <template #teacher="{ item }">
                {{ item.teacher?.name }}
            </template>

            <template #status="{ item }">

                <span
                    class="badge"
                    :class="badge(item.status)"
                >
                    {{ item.status }}
                </span>

            </template>

            <template #check_in="{ item }">
                {{ item.check_in || "-" }}
            </template>

            <template #check_out="{ item }">
                {{ item.check_out || "-" }}
            </template>

            <template #action="{ item }">

                <div class="btn-group">

                    <button
                        class="btn btn-sm btn-info"
                        @click="showAttendance(item.id)"
                    >
                        View
                    </button>

                    <button
                        class="btn btn-sm btn-warning"
                        @click="editAttendance(item.id)"
                    >
                        Edit
                    </button>

                    <button
                        class="btn btn-sm btn-danger"
                        :disabled="deleting"
                        @click="deleteAttendance(item.id)"
                    >
                        Delete
                    </button>

                </div>

            </template>

        </AppTable>

    </div>

</div>

</template>