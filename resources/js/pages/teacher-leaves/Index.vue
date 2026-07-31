<script setup>
import { ref, reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useLeaveRequestStore } from "@/stores/leaveRequest";

import teacherApi from "@/api/teacher";
import leaveTypeApi from "@/api/leaveType";
const columns = [
    { key: "sl", label: "#" },
    { key: "teacher", label: "Teacher" },
    { key: "leave_type", label: "Leave Type" },
    { key: "from_date", label: "From" },
    { key: "to_date", label: "To" },
    { key: "total_days", label: "Days" },
    { key: "status", label: "Status" },
    { key: "action", label: "Action", class: "text-center" },
];

const store = useLeaveRequestStore();

const teachers = ref([]);
const leaveTypes = ref([]);

const filters = reactive({
    search: "",
    teacher_id: "",
    leave_type_id: "",
    status: "",
    from_date: "",
    to_date: "",
    page: 1,
    per_page: 15,
});

const loadDropdowns = async () => {

    const [teacherRes, leaveTypeRes] = await Promise.all([
        teacherApi.getAll({
            per_page: 1000,
            status: 1,
        }),
        leaveTypeApi.getAll({
            per_page: 1000,
            status: 1,
        }),
    ]);

    teachers.value = teacherRes.data.data;
    leaveTypes.value = leaveTypeRes.data.data;
};

const loadData = async () => {
    await store.fetch(filters);
};

const remove = async (id) => {

    if (!confirm("Delete this leave request?")) {
        return;
    }

    await store.delete(id);

    loadData();
};

const approve = async (id) => {

    if (!confirm("Approve this leave request?")) {
        return;
    }

    await store.approve(id);

    loadData();
};

const reject = async (id) => {

    if (!confirm("Reject this leave request?")) {
        return;
    }

    await store.reject(id);

    loadData();
};

const resetFilters = () => {

    filters.search = "";
    filters.teacher_id = "";
    filters.leave_type_id = "";
    filters.status = "";
    filters.from_date = "";
    filters.to_date = "";
    filters.page = 1;

    loadData();
};

onMounted(async () => {

    await loadDropdowns();

    await loadData();

});
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Leave Requests
            </h5>

            <RouterLink
                :to="{ name:'teacher-leaves.create' }"
                class="btn btn-primary"
            >
                Add Leave Request
            </RouterLink>

        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-3">

                    <input
                        class="form-control"
                        placeholder="Search..."
                        v-model="filters.search"
                    >

                </div>

                <div class="col-md-2">

                    <select
                        class="form-select"
                        v-model="filters.teacher_id"
                    >

                        <option value="">
                            All Teachers
                        </option>

                        <option
                            v-for="teacher in teachers"
                            :key="teacher.id"
                            :value="teacher.id"
                        >
                            {{ teacher.name }}
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select
                        class="form-select"
                        v-model="filters.leave_type_id"
                    >

                        <option value="">
                            All Leave Types
                        </option>

                        <option
                            v-for="type in leaveTypes"
                            :key="type.id"
                            :value="type.id"
                        >
                            {{ type.name }}
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select
                        class="form-select"
                        v-model="filters.status"
                    >

                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                        <option value="3">Cancelled</option>

                    </select>

                </div>

                <div class="col-md-3 text-end">

                    <button
                        class="btn btn-primary me-2"
                        @click="loadData"
                    >
                        Search
                    </button>

                    <button
                        class="btn btn-secondary"
                        @click="resetFilters"
                    >
                        Reset
                    </button>

                </div>

            </div>

            <AppTable
    :columns="columns"
    :items="store.items"
    :loading="store.loading"
>

    <template #sl="{ index }">
        {{ index + 1 }}
    </template>

    <template #teacher="{ item }">
        {{ item.teacher?.name }}
    </template>

    <template #leave_type="{ item }">
        {{ item.leave_type?.name }}
    </template>

    <template #status="{ item }">
        <span
            class="badge"
            :class="{
                'bg-warning': item.status == 0,
                'bg-success': item.status == 1,
                'bg-danger': item.status == 2,
                'bg-secondary': item.status == 3
            }"
        >
            {{ item.status_text }}
        </span>
    </template>

    <template #action="{ item }">

        <RouterLink
            class="btn btn-info btn-sm me-1"
            :to="{ name:'teacher-leaves.show', params:{ id:item.id } }"
        >
            View
        </RouterLink>

        <RouterLink
            class="btn btn-primary btn-sm me-1"
            :to="{ name:'teacher-leaves.edit', params:{ id:item.id } }"
        >
            Edit
        </RouterLink>

        <button
            class="btn btn-danger btn-sm me-1"
            @click="remove(item.id)"
        >
            Delete
        </button>

        <template v-if="item.status == 0">

            <button
                class="btn btn-success btn-sm me-1"
                @click="approve(item.id)"
            >
                Approve
            </button>

            <button
                class="btn btn-warning btn-sm"
                @click="reject(item.id)"
            >
                Reject
            </button>

        </template>

    </template>

</AppTable>

        </div>

    </div>

</div>

</template>