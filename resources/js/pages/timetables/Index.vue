<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import axios from "@/api/axios";

import { useTimetableStore } from "@/stores/timetable";
import { useAuthStore } from "@/stores/auth";
import classApi from "@/api/class";

import AppButton from "@/components/ui/AppButton.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

const router = useRouter();
const store = useTimetableStore();
const authStore = useAuthStore();

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const allSchools = ref([]);
const classOptions = ref([]);

const days = [
    { id: "", name: "All Days" },
    { id: "Monday", name: "Monday" },
    { id: "Tuesday", name: "Tuesday" },
    { id: "Wednesday", name: "Wednesday" },
    { id: "Thursday", name: "Thursday" },
    { id: "Friday", name: "Friday" },
    { id: "Saturday", name: "Saturday" },
    { id: "Sunday", name: "Sunday" },
];

const filters = reactive({
    search: "",
    school_id: "",
    class_id: "",
    day: "",
    status: "",
    page: 1,
});

async function load() {
    await store.fetch(filters);
}

async function loadSchools() {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get('/tenants');
            if (res.data?.data?.length) {
                allSchools.value = res.data.data;
            }
        } catch (e) {}
    }
}

async function loadClasses() {
    try {
        const params = { per_page: 1000 };
        if (filters.school_id) {
            params.school_id = filters.school_id;
        }
        const { data } = await classApi.getAll(params);
        classOptions.value = data.data ?? [];
    } catch (error) {
        console.error("Failed to load classes:", error);
    }
}

async function onSchoolChange() {
    filters.class_id = "";
    filters.page = 1;
    await Promise.all([
        loadClasses(),
        load(),
    ]);
}

function searchData() {
    filters.page = 1;
    load();
}

function resetFilters() {
    filters.search = "";
    filters.school_id = "";
    filters.class_id = "";
    filters.day = "";
    filters.status = "";
    filters.page = 1;
    loadClasses();
    load();
}

function handleTenantSwitched() {
    loadClasses();
    load();
}

function create() {
    router.push({ name: "timetables.create" });
}

function view(id) {
    router.push({
        name: "timetables.show",
        params: { id },
    });
}

function edit(id) {
    router.push({
        name: "timetables.edit",
        params: { id },
    });
}

async function remove(id) {
    const result = await Swal.fire({
        title: "Delete Timetable Slot?",
        text: "This period entry will be permanently removed.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, Delete",
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        await store.delete(id);
        Swal.fire("Deleted!", "Timetable entry deleted successfully.", "success");
        load();
    } catch (e) {
        Swal.fire("Error", "Failed to delete timetable entry.", "error");
    }
}

watch(
    () => filters.page,
    load
);

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        loadClasses(),
        load(),
    ]);
    window.addEventListener("tenant-switched", handleTenantSwitched);
});

onBeforeUnmount(() => {
    window.removeEventListener("tenant-switched", handleTenantSwitched);
});
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Timetables' }
        ]"
    />

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Timetable Management</h4>

            <AppButton
                text="Create Timetable"
                icon="bi bi-plus-circle"
                @click="create"
            />
        </div>

        <div class="card-body">
            <!-- Filter Bar -->
            <div class="row g-2 mb-3">
                <!-- School Filter (Super Admin) -->
                <div class="col-md-2" v-if="isSuperAdmin">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-primary">
                            <i class="bi bi-buildings"></i>
                        </span>
                        <select
                            v-model="filters.school_id"
                            class="form-select border-start-0 fw-semibold"
                            @change="onSchoolChange"
                        >
                            <option value="">All Schools</option>
                            <option
                                v-for="s in allSchools"
                                :key="'tt-sch-' + s.id"
                                :value="s.id"
                            >
                                {{ s.name }} ({{ s.code }})
                            </option>
                        </select>
                    </div>
                </div>

                <div :class="isSuperAdmin ? 'col-md-3' : 'col-md-3'">
                    <input
                        v-model="filters.search"
                        class="form-control"
                        placeholder="Search room, subject, teacher..."
                        @keyup.enter="searchData"
                    />
                </div>

                <div :class="isSuperAdmin ? 'col-md-2' : 'col-md-3'">
                    <select
                        v-model="filters.class_id"
                        class="form-select"
                        @change="searchData"
                    >
                        <option value="">All Classes</option>
                        <option
                            v-for="c in classOptions"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select
                        v-model="filters.day"
                        class="form-select"
                        @change="searchData"
                    >
                        <option
                            v-for="d in days"
                            :key="d.id"
                            :value="d.id"
                        >
                            {{ d.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-1">
                    <select
                        v-model="filters.status"
                        class="form-select"
                        @change="searchData"
                    >
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-2 d-flex gap-2' : 'col-md-2 d-flex gap-2'">
                    <button
                        class="btn btn-primary w-50"
                        @click="searchData"
                        title="Search"
                    >
                        <i class="bi bi-search"></i>
                    </button>
                    <button
                        class="btn btn-secondary w-50"
                        @click="resetFilters"
                        title="Reset"
                    >
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Day</th>
                            <th v-if="isSuperAdmin">School</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Time</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="item in store.items"
                            :key="item.id"
                        >
                            <td><span class="badge bg-secondary">{{ item.day }}</span></td>
                            <td v-if="isSuperAdmin">
                                <span v-if="item.school" class="badge bg-light text-dark border">
                                    <i class="bi bi-building me-1"></i>{{ item.school.name }} ({{ item.school.code }})
                                </span>
                                <span v-else class="text-muted small">Global</span>
                            </td>
                            <td><strong>{{ item.class_name }}</strong></td>
                            <td>{{ item.section_name }}</td>
                            <td>{{ item.subject_name }}</td>
                            <td>{{ item.teacher_name || 'N/A' }}</td>
                            <td>
                                <span class="text-nowrap">
                                    {{ item.start_time }} - {{ item.end_time }}
                                </span>
                            </td>
                            <td>{{ item.room || '-' }}</td>
                            <td>
                                <AppBadge
                                    :type="item.status ? 'success' : 'danger'"
                                >
                                    {{ item.status ? "Active" : "Inactive" }}
                                </AppBadge>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button
                                        class="btn btn-info text-white"
                                        title="View Details"
                                        @click="view(item.id)"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button
                                        class="btn btn-warning text-dark"
                                        title="Edit Timetable"
                                        @click="edit(item.id)"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        class="btn btn-danger"
                                        title="Delete Timetable"
                                        @click="remove(item.id)"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-if="!store.loading && store.items.length === 0"
                        >
                            <td :colspan="isSuperAdmin ? 10 : 9" class="text-center py-4 text-muted">
                                No timetable records found.
                            </td>
                        </tr>

                        <tr v-if="store.loading">
                            <td :colspan="isSuperAdmin ? 10 : 9" class="text-center py-4">
                                <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                                Loading timetables...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <AppPagination
                    :pagination="store.pagination"
                    @change="page => filters.page = page"
                />
            </div>
        </div>
    </div>
</div>
</template>
