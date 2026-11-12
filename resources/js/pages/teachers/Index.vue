<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "@/api/axios";

import { useTeacherStore } from "@/stores/teacher";
import { useAuthStore } from "@/stores/auth";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppSearch from "@/components/ui/AppSearch.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppTable from "@/components/ui/AppTable.vue";

const router = useRouter();
const store = useTeacherStore();
const authStore = useAuthStore();

const allSchools = ref([]);

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const filters = reactive({
    search: "",
    school_id: "",
    status: "",
    page: 1,
    per_page: 10,
});

const columns = computed(() => {
    const cols = [
        {
            key: "photo",
            label: "Photo",
            class: "text-center",
        },
        {
            key: "employee_id",
            label: "Employee ID",
        },
    ];

    if (isSuperAdmin.value) {
        cols.push({
            key: "school",
            label: "School / Campus",
        });
    }

    cols.push(
        {
            key: "name",
            label: "Teacher Name",
        },
        {
            key: "phone",
            label: "Phone",
        },
        {
            key: "email",
            label: "Email",
        },
        {
            key: "status",
            label: "Status",
            class: "text-center",
        },
        {
            key: "actions",
            label: "Actions",
            class: "text-center",
        },
    );

    return cols;
});

async function loadSchools() {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get("/tenants");
            if (res.data?.data?.length) {
                allSchools.value = res.data.data;
                return;
            }
        } catch (e) {}

        try {
            const res2 = await axios.get("/public/saas/schools");
            if (res2.data?.data?.length) {
                allSchools.value = res2.data.data;
            }
        } catch (e2) {}
    }
}

async function loadTeachers() {
    await store.fetch(filters);
    if ((!allSchools.value || allSchools.value.length === 0) && store.allSchools?.length) {
        allSchools.value = store.allSchools;
    }
}

function onSchoolFilterChange() {
    filters.page = 1;
    loadTeachers();
}

function onSearch() {
    filters.page = 1;
    loadTeachers();
}

function changePage(page) {
    filters.page = page;
    loadTeachers();
}

function createTeacher() {
    router.push({
        name: "teachers.create",
    });
}

function viewTeacher(id) {
    router.push({
        name: "teachers.show",
        params: { id },
    });
}

function editTeacher(id) {
    router.push({
        name: "teachers.edit",
        params: { id },
    });
}

async function deleteTeacher(id) {
    if (!confirm("Are you sure you want to delete this teacher?")) {
        return;
    }

    await store.delete(id);
    loadTeachers();
}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        loadTeachers(),
    ]);
});
</script>

<template>
    <div class="container-fluid">
    <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Teachers'},
            ]"
        />

    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">

            <div class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                <!-- School Filter (Super Admin) -->
                <div v-if="isSuperAdmin && allSchools.length > 0" style="min-width: 220px; max-width: 280px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-primary">
                            <i class="bi bi-buildings"></i>
                        </span>
                        <select
                            class="form-select border-start-0 fw-semibold"
                            v-model="filters.school_id"
                            @change="onSchoolFilterChange"
                        >
                            <option value="">All Schools</option>
                            <option
                                v-for="item in allSchools"
                                :key="'sch-opt-'+item.id"
                                :value="item.id"
                            >
                                {{ item.name }} ({{ item.code }})
                            </option>
                        </select>
                    </div>
                </div>

                <div style="min-width: 220px; max-width: 320px;">
                    <AppSearch
                        v-model="filters.search"
                        placeholder="Search name, employee ID, phone..."
                        @search="onSearch"
                        @keyup.enter="onSearch"
                    />
                </div>

                <div>
                    <select
                        class="form-select"
                        v-model="filters.status"
                        @change="onSearch"
                    >
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <AppButton @click="createTeacher" text="Add Teacher" class="btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Add Teacher
            </AppButton>

        </div>

        <div class="card-body">

            <AppTable
                :columns="columns"
                :items="store.items"
                :loading="store.loading"
            >

                <!-- Photo -->
                <template #photo="{ item }">

                    <img
                        v-if="item.photo"
                        :src="item.photo"
                        class="rounded-circle border"
                        style="width:40px;height:40px;object-fit:cover"
                    />

                    <div
                        v-else
                        class="rounded-circle bg-light text-secondary border d-inline-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; font-weight: 600; font-size: 14px;"
                    >
                        {{ (item.first_name || 'T').charAt(0).toUpperCase() }}
                    </div>

                </template>

                <!-- School / Campus (Super Admin) -->
                <template #school="{ item }">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                        <i class="bi bi-building me-1"></i> {{ item.school_name || item.school?.name || 'School' }}
                    </span>
                    <small v-if="item.school_code || item.school?.code" class="text-muted d-block font-monospace">
                        ({{ item.school_code || item.school?.code }})
                    </small>
                </template>

                <!-- Name -->
                <template #name="{ item }">

                    <a
                        href="#"
                        class="fw-bold text-decoration-none"
                        @click.prevent="viewTeacher(item.id)"
                    >
                        {{ item.name }}
                    </a>

                </template>

                <!-- Status -->
                <template #status="{ item }">

                    <AppBadge
                        :type="item.status ? 'success' : 'danger'"
                    >
                        {{ item.status ? "Active" : "Inactive" }}
                    </AppBadge>

                </template>

                <!-- Actions -->
                <template #actions="{ item }">

                    <div class="d-flex justify-content-center gap-2">
<div class="btn-group btn-group-sm">
                        <AppButton
                            size="sm"
                            color="info"
                            icon="bi bi-eye"
                            @click="viewTeacher(item.id)" 
                        >
                            View
                        </AppButton>

                        <AppButton
                            size="sm"
                            color="success"
                            icon="bi bi-pencil"
                            @click="editTeacher(item.id)" 
                        >
                            Edit
                        </AppButton>

                        <AppButton
                            size="sm"
                            icon="bi bi-trash"
                            variant="danger"
                            color="danger"
                            @click="deleteTeacher(item.id)"
                        >
                            Delete
                        </AppButton>

                    </div>
</div>
                </template>

            </AppTable>

        </div>

        <div class="card-footer bg-white border-0">

            <AppPagination
                :meta="store.pagination"
                @change="changePage"
            />

        </div>

    </div>
    </div>
</template>