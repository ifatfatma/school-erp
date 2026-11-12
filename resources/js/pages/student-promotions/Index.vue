<script setup>
import { reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";
import Swal from "sweetalert2";

import AppTable from "@/components/ui/AppTable.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useStudentPromotionStore } from "@/stores/studentPromotion";
import { useAcademicSessionStore } from "@/stores/academicSession";

const promotionStore = useStudentPromotionStore();
const sessionStore = useAcademicSessionStore();

const filters = reactive({
    search: "",
    to_session_id: "",
    promotion_type: "",
    page: 1,
    per_page: 15,
});

const columns = [
    { key: "student", label: "Student" },
    { key: "from", label: "From" },
    { key: "to", label: "To" },
    { key: "promotion_date", label: "Promotion Date" },
    { key: "promotion_type", label: "Type" },
    { key: "action", label: "Action" },
];

onMounted(async () => {
    await sessionStore.load({ per_page: 1000 });
    load();
});

async function load() {
    await promotionStore.fetch(filters);
}

function onPageChange(page) {
    filters.page = page;
    load();
}

async function remove(id) {
    const result = await Swal.fire({
        title: "Delete Promotion Record?",
        text: "Are you sure you want to delete this promotion history log?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) return;

    try {
        await promotionStore.delete(id);
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Promotion history record deleted.",
            timer: 1500,
            showConfirmButton: false,
        });
        load();
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: e.response?.data?.message || "Failed to delete promotion history.",
        });
    }
}

function reset() {
    filters.search = "";
    filters.to_session_id = "";
    filters.promotion_type = "";
    filters.page = 1;
    load();
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Student Promotions' }
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>Student Promotion History
            </h5>

            <RouterLink
                class="btn btn-primary"
                :to="{ name: 'student-promotions.bulk' }"
            >
                <i class="bi bi-box-arrow-in-up-right me-1"></i> Bulk Promotion
            </RouterLink>
        </div>

        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input
                        class="form-control"
                        placeholder="Search student or admission no..."
                        v-model="filters.search"
                        @keyup.enter="load"
                    />
                </div>

                <div class="col-md-3">
                    <select
                        class="form-select"
                        v-model="filters.to_session_id"
                        @change="load"
                    >
                        <option value="">All Promoted Sessions</option>
                        <option
                            v-for="s in sessionStore.sessions"
                            :key="s.id"
                            :value="s.id"
                        >
                            {{ s.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select
                        class="form-select"
                        v-model="filters.promotion_type"
                        @change="load"
                    >
                        <option value="">All Promotion Types</option>
                        <option value="Promoted">Promoted</option>
                        <option value="Passed">Passed</option>
                        <option value="Failed">Failed</option>
                        <option value="Detained">Detained</option>
                    </select>
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-primary me-2" @click="load">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <button class="btn btn-secondary" @click="reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>

            <AppTable
                :columns="columns"
                :items="promotionStore.items"
                :loading="promotionStore.loading"
            >
                <template #student="{ item }">
                    <div class="fw-bold">{{ item.student?.full_name || 'N/A' }}</div>
                    <small class="text-muted" v-if="item.student?.admission_no">
                        Adm: {{ item.student.admission_no }}
                    </small>
                </template>

                <template #from="{ item }">
                    <span class="fw-semibold">{{ item.from_session?.name || '-' }}</span>
                    <br>
                    <small class="text-muted">
                        {{ item.from_class?.name || '-' }}
                        <span v-if="item.from_section?.name">({{ item.from_section.name }})</span>
                    </small>
                </template>

                <template #to="{ item }">
                    <span class="fw-semibold text-primary">{{ item.to_session?.name || '-' }}</span>
                    <br>
                    <small class="text-muted">
                        {{ item.to_class?.name || '-' }}
                        <span v-if="item.to_section?.name">({{ item.to_section.name }})</span>
                    </small>
                </template>

                <template #promotion_type="{ item }">
                    <span
                        class="badge"
                        :class="'bg-' + (item.promotion_badge || (item.promotion_type === 'Promoted' ? 'success' : 'secondary'))"
                    >
                        {{ item.promotion_type }}
                    </span>
                </template>

                <template #action="{ item }">
                    <div class="btn-group btn-group-sm">
                        <RouterLink
                            class="btn btn-info text-white"
                            :to="{
                                name: 'student-promotions.show',
                                params: { id: item.id }
                            }"
                            title="View"
                        >
                            <i class="bi bi-eye"></i>
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

            <div class="mt-3" v-if="promotionStore.pagination && promotionStore.pagination.total > 0">
                <AppPagination
                    :pagination="promotionStore.pagination"
                    @change="onPageChange"
                />
            </div>
        </div>
    </div>
</div>
</template>
