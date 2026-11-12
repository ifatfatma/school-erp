
<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import axios from "@/api/axios";

import AppTable from "@/components/ui/AppTable.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useExamStore } from "@/stores/exam";
import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamTypeStore } from "@/stores/examType";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const examStore = useExamStore();
const sessionStore = useAcademicSessionStore();
const examTypeStore = useExamTypeStore();

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const allSchools = ref([]);

const filters = reactive({
    search: "",
    school_id: "",
    academic_session_id: "",
    exam_type_id: "",
    status: "",
    page: 1,
    per_page: 15,
});

const columns = computed(() => {
    const cols = [
        { key: "name", label: "Exam" },
        { key: "code", label: "Code" },
    ];

    if (isSuperAdmin.value) {
        cols.push({ key: "school", label: "School / Campus" });
    }

    cols.push(
        { key: "academic_session", label: "Academic Session" },
        { key: "exam_type", label: "Exam Type" },
        { key: "start_date", label: "Start Date" },
        { key: "end_date", label: "End Date" },
        { key: "marks", label: "Marks" },
        { key: "result", label: "Result" },
        { key: "status", label: "Status" },
        { key: "action", label: "Action" }
    );

    return cols;
});

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

function handleTenantSwitched() {
    load();
}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        load(),
        sessionStore.load({ per_page: 1000 }),
        examTypeStore.fetch({
            per_page: 1000,
            status: 1
        })
    ]);
    window.addEventListener("tenant-switched", handleTenantSwitched);
});

onBeforeUnmount(() => {
    window.removeEventListener("tenant-switched", handleTenantSwitched);
});

async function load(page = 1) {

    filters.page = page;

    await examStore.fetch(filters);

}

function reset() {

    filters.search = "";

    filters.school_id = "";

    filters.academic_session_id = "";

    filters.exam_type_id = "";

    filters.status = "";

    load();

}

function create() {

    router.push({
        name: "exams.create"
    });

}

function edit(id) {

    router.push({
        name: "exams.edit",
        params: { id }
    });

}

async function remove(id) {

    if (!confirm("Delete this Exam?")) return;

    await examStore.delete(id);

    load(filters.page);

}

async function lock(item) {

    await examStore.lockMarks(item.id);

    load(filters.page);

}

async function unlock(item) {

    await examStore.unlockMarks(item.id);

    load(filters.page);

}

async function publish(item) {

    await examStore.publishResult(item.id);

    load(filters.page);

}

async function unpublish(item) {

    await examStore.unpublishResult(item.id);

    load(filters.page);

}
</script>

<template>

<div class="container-fluid py-2">

<AppBreadcrumb
    :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Examinations' }
    ]"
/>

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">

Examinations

</h5>

<button
class="btn btn-primary"
@click="create"
>

<i class="fas fa-plus"></i>

Add Exam

</button>

</div>

<div class="card-body">

<div class="row g-2 mb-3">

<div class="col-md-2" v-if="isSuperAdmin">
    <div class="input-group">
        <span class="input-group-text bg-light border-end-0 text-primary">
            <i class="bi bi-buildings"></i>
        </span>
        <select
            v-model="filters.school_id"
            class="form-select border-start-0 fw-semibold"
            @change="load(1)"
        >
            <option value="">All Schools</option>
            <option
                v-for="s in allSchools"
                :key="'ex-sch-' + s.id"
                :value="s.id"
            >
                {{ s.name }} ({{ s.code }})
            </option>
        </select>
    </div>
</div>

<div class="col-md-2">

<input
class="form-control"
placeholder="Search"
v-model="filters.search"
/>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filters.academic_session_id"
>

<option value="">

Academic Session

</option>

<option
v-for="item in sessionStore.sessions"
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
v-model="filters.exam_type_id"
>

<option value="">

Exam Type

</option>

<option
v-for="item in examTypeStore.items"
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
>

<option value="">

Status

</option>

<option :value="1">

Active

</option>

<option :value="0">

Inactive

</option>

</select>

</div>
<div class="col-md-2">

<button
class="btn btn-primary me-2"
@click="load()"
>

Search

</button>

<button
class="btn btn-secondary"
@click="reset"
>

Reset

</button>

</div>
</div>



<AppTable

:columns="columns"

:items="examStore.items"

:pagination="examStore.pagination"

:loading="examStore.loading"

@page-change="load"

>

<template #school="{ item }">
    <span v-if="item.school" class="badge bg-light text-dark border">
        <i class="bi bi-building me-1"></i>{{ item.school.name }} ({{ item.school.code }})
    </span>
    <span v-else class="text-muted small">Global</span>
</template>

<template #academic_session="{ item }">

{{ item.academic_session?.name }}

</template>

<template #exam_type="{ item }">

{{ item.exam_type?.name }}

</template>

<template #marks="{ item }">

<span
class="badge"
:class="item.is_marks_locked ? 'bg-danger':'bg-success'"
>

{{ item.marks_lock_label }}

</span>

</template>

<template #result="{ item }">

<span
class="badge"
:class="item.is_result_published ? 'bg-success':'bg-secondary'"
>

{{ item.result_publish_label }}

</span>

</template>

<template #status="{ item }">

<span
class="badge"
:class="item.status ? 'bg-success':'bg-danger'"
>

{{ item.status_label }}

</span>

</template>

<template #action="{ item }">

<div class="btn-group">

<button
class="btn btn-warning btn-sm"
@click="edit(item.id)"
>

<i class="bi bi-pencil"></i>

</button>

<button
class="btn btn-danger btn-sm"
@click="remove(item.id)"
>

<i class="bi bi-trash"></i>

</button>

<button
v-if="!item.is_marks_locked"
class="btn btn-dark btn-sm"
@click="lock(item)"
>

Lock

</button>

<button
v-else
class="btn btn-success btn-sm"
@click="unlock(item)"
>

Unlock

</button>

<button
v-if="!item.is_result_published"
class="btn btn-info btn-sm"
@click="publish(item)"
>

Publish

</button>

<button
v-else
class="btn btn-secondary btn-sm"
@click="unpublish(item)"
>

Unpublish

</button>

</div>

</template>

</AppTable>

</div>

</div>

</div>

</template>