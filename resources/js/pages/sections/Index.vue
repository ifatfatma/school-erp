<template>
    <div class="container-fluid py-2">
        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Sections' }
            ]"
        />

        <AppCard title="Sections">

        <template #header-action>

            <AppButton
                text="Add Section"
                icon="bi bi-plus-circle"
                @click="createSection"
            />

        </template>

        <div class="row g-2 mb-3 align-items-center">

            <!-- School Filter (Super Admin) -->
            <div class="col-md-3" v-if="isSuperAdmin && allSchools.length > 0">
                <select
                    v-model="filters.school_id"
                    class="form-select fw-semibold"
                    @change="onSchoolChange"
                >
                    <option value="">All Schools</option>
                    <option
                        v-for="s in allSchools"
                        :key="'sec-sch-' + s.id"
                        :value="s.id"
                    >
                        {{ s.name }} ({{ s.code }})
                    </option>
                </select>
            </div>

            <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-4' : 'col-md-6'">
                <AppSearch
                    v-model="search"
                    placeholder="Search Section..."
                    @search="load(1)"
                />
            </div>

            <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-3' : 'col-md-3'">

                <AppSelect
                    v-model="filters.class_id"
                    :options="classOptions"
                    option-label="name"
                    option-value="id"
                    placeholder="All Classes"
                    @change="load(1)"
                />

            </div>

        </div>

        <AppLoader v-if="store.loading" />

        <div
            v-else
            class="table-responsive"
        >

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="70">#</th>

                        <th v-if="isSuperAdmin">School / Campus</th>

                        <th>Class</th>

                        <th>Section</th>

                        <th>Capacity</th>

                        <th>Status</th>

                        <th width="170">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="(item,index) in store.items"
                        :key="item.id"
                    >

                        <td>
                            {{ index + 1 }}
                        </td>

                        <td v-if="isSuperAdmin">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                <i class="bi bi-building me-1"></i> {{ item.school_name || item.school?.name || 'School' }}
                            </span>
                            <small v-if="item.school_code || item.school?.code" class="text-muted d-block font-monospace">
                                ({{ item.school_code || item.school?.code }})
                            </small>
                        </td>

                        <td>
                            {{ item.class_name }}
                        </td>

                        <td>
                            {{ item.name }}
                        </td>

                        <td>
                            {{ item.capacity }}
                        </td>

                        <td>

                            <AppBadge
                                :type="item.status ? 'success' : 'danger'"
                            >
                                {{ item.status ? 'Active' : 'Inactive' }}
                            </AppBadge>

                        </td>

                        <td>
<div class="d-flex gap-2">
    <div class="btn-group btn-group-sm">
                            <AppButton
                                color="info"
                                size="sm"
                                icon="bi bi-eye"
                                title="View Section"
                                @click="viewSection(item.id)"
                            />

                            <AppButton
                                color="warning"
                                size="sm"
                                icon="bi bi-pencil"
                                title="Edit Section"
                                @click="editSection(item.id)"
                            />

                            <AppButton
                                color="danger"
                                size="sm"
                                icon="bi bi-trash"
                                title="Delete Section"
                                @click="removeSection(item.id)"
                            />
</div>
</div>
                        </td>

                    </tr>

                    <tr v-if="!store.items.length">

                        <td
                            :colspan="isSuperAdmin ? 7 : 6"
                            class="text-center text-muted py-4"
                        >
                            No sections found.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <AppPagination
            :pagination="store.pagination"
            @change="load"
        />

    </AppCard>
    </div>
</template>
<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import axios from "@/api/axios";

import { useSectionStore } from "@/stores/section";
import { useAuthStore } from "@/stores/auth";
import classApi from "@/api/class";

import AppButton from "@/components/ui/AppButton.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppCard from "@/components/ui/AppCard.vue";
import AppSearch from "@/components/ui/AppSearch.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppLoader from "@/components/ui/AppLoader.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

const router = useRouter();
const store = useSectionStore();
const authStore = useAuthStore();

const search = ref("");

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const allSchools = ref([]);

const filters = reactive({
    school_id: "",
    class_id: "",
});

const classOptions = ref([]);

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

async function onSchoolChange() {
    filters.class_id = "";
    await Promise.all([
        loadClasses(),
        load(1)
    ]);
}

async function load(page = 1) {
    await store.load({
        page,
        search: search.value,
        school_id: filters.school_id || undefined,
        class_id: filters.class_id,
    });
}

async function loadClasses() {
    const { data } = await classApi.getAll({
        per_page: 1000,
        school_id: filters.school_id || undefined,
    });

    classOptions.value = data.data;
}

function createSection() {
    router.push({
        name: "sections.create",
    });
}

function viewSection(id) {
    router.push({
        name: "sections.show",
        params: { id },
    });
}

function editSection(id) {
    router.push({
        name: "sections.edit",
        params: { id },
    });
}

async function removeSection(id) {

    const result = await Swal.fire({
        title: "Delete Section?",
        text: "This record will be deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        confirmButtonColor: "#dc3545",
    });

    if (!result.isConfirmed) {
        return;
    }

    await store.delete(id);

    await load();

    Swal.fire(
        "Deleted!",
        "Section deleted successfully.",
        "success"
    );
}

onMounted(async () => {

    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }

    await Promise.all([
        loadSchools(),
        loadClasses(),
        load(),
    ]);

});
</script>