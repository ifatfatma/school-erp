<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from "vue";
import { useRouter } from "vue-router";

import { useResultGradeStore } from "@/stores/resultGrade";
import { useAuthStore } from "@/stores/auth";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppInput from "@/components/ui/AppInput.vue";

const router = useRouter();
const store = useResultGradeStore();
const authStore = useAuthStore();

const search = ref("");
const selectedSchool = ref("");

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const allSchools = computed(() => store.allSchools || []);

async function load() {
    const params = {
        search: search.value,
        per_page: 100,
    };
    if (isSuperAdmin.value && selectedSchool.value) {
        params.school_id = selectedSchool.value;
    }
    await store.fetch(params);
}

function handleTenantSwitched() {
    load();
}

function create() {
    router.push({
        name: "result-grades.create",
    });
}

function edit(id) {
    router.push({
        name: "result-grades.edit",
        params: {
            id,
        },
    });
}

async function remove(id) {
    if (!confirm("Are you sure you want to delete this grade?")) {
        return;
    }

    try {
        await store.remove(id);
        await load();
    } catch (error) {
        console.error("Failed to delete grade:", error);
    }
}

onMounted(() => {
    load();
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
                { label: 'Examinations' },
                { label: 'Grades & Grading Scale' }
            ]"
        />

        <div class="card shadow-sm border-0">
            <div
                class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center"
            >
                <div>
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-award text-primary me-2"></i> Grade System & Grading Scale
                    </h4>
                    <p class="text-muted small mb-0 mt-1">
                        Define institutional percentage ranges, grade letters, grade points (GPA), and pass/fail thresholds.
                    </p>
                </div>

                <AppButton
                    text="Add Grade"
                    icon="bi bi-plus-circle me-1"
                    @click="create"
                />
            </div>

            <div class="card-body">
                <div class="row g-3 mb-4">
                    <!-- School Filter (Super Admin) -->
                    <div class="col-md-3" v-if="isSuperAdmin && allSchools.length > 0">
                        <label class="form-label small fw-semibold text-muted">School / Campus</label>
                        <select
                            v-model="selectedSchool"
                            class="form-select fw-semibold"
                            @change="load"
                        >
                            <option value="">All Schools</option>
                            <option
                                v-for="s in allSchools"
                                :key="'grd-sch-' + s.id"
                                :value="s.id"
                            >
                                {{ s.name }} ({{ s.code }})
                            </option>
                        </select>
                    </div>

                    <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-4' : 'col-md-5'">
                        <label class="form-label small fw-semibold text-muted">Search Grade</label>
                        <AppInput
                            v-model="search"
                            placeholder="Search by grade name or description..."
                            @keyup.enter="load"
                        />
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <AppButton
                            text="Search"
                            icon="bi bi-search me-1"
                            @click="load"
                        />
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="60" class="text-center">#</th>
                                <th>Grade</th>
                                <th v-if="isSuperAdmin">School</th>
                                <th>Percentage Range</th>
                                <th>Grade Point (GPA)</th>
                                <th>Result</th>
                                <th>Status</th>
                                <th width="150" class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr
                                v-for="(grade, index) in store.items"
                                :key="grade.id"
                            >
                                <td class="text-center text-muted fw-bold">
                                    {{ index + 1 }}
                                </td>

                                <td>
                                    <span class="badge bg-primary fs-6 px-3 py-2 fw-bold">
                                        {{ grade.name }}
                                    </span>
                                    <div v-if="grade.description" class="small text-muted mt-1">
                                        {{ grade.description }}
                                    </div>
                                </td>

                                <td v-if="isSuperAdmin">
                                    <span v-if="grade.school" class="badge bg-light text-dark border">
                                        <i class="bi bi-building me-1"></i>{{ grade.school.name }} ({{ grade.school.code }})
                                    </span>
                                    <span v-else class="text-muted small">Global</span>
                                </td>

                                <td>
                                    <strong>{{ grade.min_percentage }}%</strong>
                                    <span class="text-muted mx-1">to</span>
                                    <strong>{{ grade.max_percentage }}%</strong>
                                </td>

                                <td>
                                    <span class="fw-semibold">{{ grade.grade_point }}</span>
                                </td>

                                <td>
                                    <span
                                        :class="
                                            grade.is_pass
                                                ? 'badge bg-success-subtle text-success border border-success-subtle px-2 py-1'
                                                : 'badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1'
                                        "
                                    >
                                        <i :class="grade.is_pass ? 'bi bi-check-circle me-1' : 'bi bi-x-circle me-1'"></i>
                                        {{ grade.is_pass ? 'Pass' : 'Fail' }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        :class="
                                            grade.status
                                                ? 'badge bg-success'
                                                : 'badge bg-secondary'
                                        "
                                    >
                                        {{ grade.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <button
                                        class="btn btn-sm btn-outline-primary me-1"
                                        @click="edit(grade.id)"
                                        title="Edit Grade"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        @click="remove(grade.id)"
                                        title="Delete Grade"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="!store.loading && !store.items.length">
                                <td
                                    :colspan="isSuperAdmin ? 8 : 7"
                                    class="text-center text-muted py-5"
                                >
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    No grading scales found. Click <strong>Add Grade</strong> to define one.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
