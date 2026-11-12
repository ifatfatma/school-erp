<script setup>
import { reactive, computed, watch, onMounted } from "vue";
import { RouterLink } from "vue-router";
import Swal from "sweetalert2";

import AppTable from "@/components/ui/AppTable.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";

const attendanceStore = useStudentAttendanceStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();

const filters = reactive({
    search: "",
    attendance_date: "",
    class_id: "",
    section_id: "",
    status: "",
    page: 1,
    per_page: 15,
});

const columns = [
    { key: "student", label: "Student" },
    { key: "class", label: "Class" },
    { key: "section", label: "Section" },
    { key: "attendance_date", label: "Date" },
    { key: "status", label: "Status" },
    { key: "remarks", label: "Remarks" },
    { key: "action", label: "Action" },
];

const availableSections = computed(() => {
    if (!filters.class_id) return sectionStore.items || [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(filters.class_id)
    );
});

watch(() => filters.class_id, () => {
    filters.section_id = "";
});

onMounted(async () => {
    await Promise.all([
        classStore.fetch({ per_page: 100 }),
        sectionStore.load({ per_page: 200 }),
        load(),
    ]);
});

async function load() {
    await attendanceStore.fetch(filters);
}

function onPageChange(page) {
    filters.page = page;
    load();
}

function reset() {
    filters.search = "";
    filters.attendance_date = "";
    filters.class_id = "";
    filters.section_id = "";
    filters.status = "";
    filters.page = 1;
    load();
}

async function remove(id) {
    const result = await Swal.fire({
        title: "Delete Record?",
        text: "Are you sure you want to delete this attendance record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) return;

    try {
        await attendanceStore.delete(id);
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Attendance record deleted.",
            timer: 1500,
            showConfirmButton: false,
        });
        load();
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: e.response?.data?.message || "Failed to delete record.",
        });
    }
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Student Attendance' }
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-calendar-check text-primary me-2"></i>Student Attendance Records
            </h5>

            <div class="d-flex gap-2">
                <RouterLink
                    :to="{ name: 'student-attendance.monthly' }"
                    class="btn btn-outline-info btn-sm"
                >
                    <i class="bi bi-bar-chart-line me-1"></i> Monthly Report
                </RouterLink>

                <RouterLink
                    :to="{ name: 'student-attendance.bulk' }"
                    class="btn btn-primary btn-sm"
                >
                    <i class="bi bi-plus-circle me-1"></i> Mark Attendance
                </RouterLink>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input
                        class="form-control"
                        placeholder="Search student name..."
                        v-model="filters.search"
                        @keyup.enter="load"
                    />
                </div>

                <div class="col-md-2">
                    <input
                        type="date"
                        class="form-control"
                        v-model="filters.attendance_date"
                        @change="load"
                    />
                </div>

                <div class="col-md-2">
                    <select
                        class="form-select"
                        v-model="filters.class_id"
                        @change="load"
                    >
                        <option value="">All Classes</option>
                        <option
                            v-for="item in classStore.items"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select
                        class="form-select"
                        v-model="filters.section_id"
                        @change="load"
                    >
                        <option value="">All Sections</option>
                        <option
                            v-for="item in availableSections"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select
                        class="form-select"
                        v-model="filters.status"
                        @change="load"
                    >
                        <option value="">All Status</option>
                        <option value="Present">Present</option>
                        <option value="Absent">Absent</option>
                        <option value="Late">Late</option>
                        <option value="Half Day">Half Day</option>
                        <option value="Leave">Leave</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button
                        class="btn btn-primary w-100"
                        @click="load"
                        title="Search"
                    >
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <AppTable
                :columns="columns"
                :items="attendanceStore.items"
                :loading="attendanceStore.loading"
            >
                <template #student="{ item }">
                    <div class="fw-bold">{{ item.student?.full_name || item.student?.name || 'N/A' }}</div>
                    <small class="text-muted" v-if="item.student?.admission_no">
                        Adm: {{ item.student.admission_no }}
                    </small>
                </template>

                <template #class="{ item }">
                    <span class="badge bg-light text-dark border">
                        {{ item.class?.name || '-' }}
                    </span>
                </template>

                <template #section="{ item }">
                    <span class="badge bg-light text-secondary border">
                        {{ item.section?.name || '-' }}
                    </span>
                </template>

                <template #status="{ item }">
                    <span
                        class="badge"
                        :class="'bg-' + (item.status_badge || (item.status === 'Present' ? 'success' : item.status === 'Absent' ? 'danger' : 'warning'))"
                    >
                        {{ item.status }}
                    </span>
                </template>

                <template #remarks="{ item }">
                    <span class="text-muted small">{{ item.remarks || '-' }}</span>
                </template>

                <template #action="{ item }">
                    <div class="btn-group btn-group-sm">
                        <RouterLink
                            class="btn btn-info text-white"
                            :to="{
                                name: 'student-attendance.show',
                                params: { id: item.id }
                            }"
                            title="View"
                        >
                            <i class="bi bi-eye"></i>
                        </RouterLink>

                        <RouterLink
                            class="btn btn-warning text-dark"
                            :to="{
                                name: 'student-attendance.edit',
                                params: { id: item.id }
                            }"
                            title="Edit"
                        >
                            <i class="bi bi-pencil"></i>
                        </RouterLink>

                        <button
                            class="btn btn-danger"
                            @click="remove(item.id)"
                            title="Delete"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </template>
            </AppTable>

            <div class="mt-3" v-if="attendanceStore.pagination && attendanceStore.pagination.total > 0">
                <AppPagination
                    :pagination="attendanceStore.pagination"
                    @change="onPageChange"
                />
            </div>
        </div>
    </div>
</div>
</template>
