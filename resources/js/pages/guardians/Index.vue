<script setup>
import { reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";
import Swal from "sweetalert2";

import AppTable from "@/components/ui/AppTable.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useGuardianStore } from "@/stores/guardian";

const guardianStore = useGuardianStore();

const filters = reactive({
    search: "",
    guardian_type: "",
    status: "",
    page: 1,
    per_page: 15,
});

const columns = [
    { key: "photo", label: "Photo" },
    { key: "name", label: "Guardian" },
    { key: "guardian_type", label: "Type" },
    { key: "mobile", label: "Mobile" },
    { key: "email", label: "Email" },
    { key: "students", label: "Students" },
    { key: "status", label: "Status" },
    { key: "action", label: "Action" },
];

onMounted(() => {
    load();
});

async function load() {
    await guardianStore.fetch(filters);
}

function onPageChange(page) {
    filters.page = page;
    load();
}

async function remove(id) {
    const result = await Swal.fire({
        title: "Delete Guardian?",
        text: "Are you sure you want to delete this guardian record?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) return;

    try {
        await guardianStore.delete(id);
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Guardian has been removed.",
            timer: 1500,
            showConfirmButton: false,
        });
        load();
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: e.response?.data?.message || "Failed to delete guardian.",
        });
    }
}

function reset() {
    filters.search = "";
    filters.guardian_type = "";
    filters.status = "";
    filters.page = 1;
    load();
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Guardians' }
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-shield-person text-primary me-2"></i>Guardian List
            </h5>

            <RouterLink
                class="btn btn-primary"
                :to="{ name: 'guardians.create' }"
            >
                <i class="bi bi-plus-circle me-1"></i> Add Guardian
            </RouterLink>
        </div>

        <div class="card-body">
            <div class="row g-2 mb-4">
                <div class="col-md-4">
                    <input
                        class="form-control"
                        placeholder="Search name, mobile, email..."
                        v-model="filters.search"
                        @keyup.enter="load"
                    />
                </div>

                <div class="col-md-3">
                    <select
                        class="form-select"
                        v-model="filters.guardian_type"
                        @change="load"
                    >
                        <option value="">All Types</option>
                        <option>Father</option>
                        <option>Mother</option>
                        <option>Grandfather</option>
                        <option>Grandmother</option>
                        <option>Uncle</option>
                        <option>Aunt</option>
                        <option>Brother</option>
                        <option>Sister</option>
                        <option>Legal Guardian</option>
                        <option>Other</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select
                        class="form-select"
                        v-model="filters.status"
                        @change="load"
                    >
                        <option value="">All Status</option>
                        <option :value="1">Active</option>
                        <option :value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button
                            class="btn btn-primary flex-fill"
                            @click="load"
                        >
                            <i class="bi bi-search me-1"></i> Filter
                        </button>

                        <button
                            class="btn btn-secondary flex-fill"
                            @click="reset"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <AppTable
                :columns="columns"
                :items="guardianStore.items"
                :loading="guardianStore.loading"
            >
                <template #photo="{ item }">
                    <img
                        v-if="item.photo_url"
                        :src="item.photo_url"
                        class="rounded border bg-light"
                        style="width: 40px; height: 40px; object-fit: cover;"
                        @error="e => e.target.style.display = 'none'"
                    />
                    <div
                        v-else
                        class="rounded bg-light text-secondary border d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; font-weight: 600; font-size: 14px;"
                    >
                        {{ (item.name || 'G').charAt(0).toUpperCase() }}
                    </div>
                </template>

                <template #name="{ item }">
                    <div class="fw-bold">{{ item.name }}</div>
                    <small class="text-muted" v-if="item.occupation">{{ item.occupation }}</small>
                </template>

                <template #guardian_type="{ item }">
                    <span class="badge bg-light text-dark border">
                        {{ item.guardian_type || item.relation || '-' }}
                    </span>
                </template>

                <template #students="{ item }">
                    <div v-if="item.students && item.students.length">
                        <span
                            v-for="s in item.students"
                            :key="s.id"
                            class="badge bg-info text-dark me-1 mb-1"
                        >
                            {{ s.full_name || s.name }}
                        </span>
                    </div>
                    <span v-else class="text-muted small">None linked</span>
                </template>

                <template #status="{ item }">
                    <span
                        class="badge"
                        :class="item.status ? 'bg-success' : 'bg-danger'"
                    >
                        {{ item.status ? "Active" : "Inactive" }}
                    </span>
                </template>

                <template #action="{ item }">
                    <div class="btn-group btn-group-sm">
                        <RouterLink
                            class="btn btn-info text-white"
                            :to="{
                                name: 'guardians.show',
                                params: { id: item.id }
                            }"
                            title="View"
                        >
                            <i class="bi bi-eye"></i>
                        </RouterLink>

                        <RouterLink
                            class="btn btn-warning text-dark"
                            :to="{
                                name: 'guardians.edit',
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

            <div class="mt-3" v-if="guardianStore.pagination && guardianStore.pagination.total > 0">
                <AppPagination
                    :pagination="guardianStore.pagination"
                    @change="onPageChange"
                />
            </div>
        </div>
    </div>
</div>
</template>
