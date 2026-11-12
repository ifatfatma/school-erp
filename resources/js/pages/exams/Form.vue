<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from "vue";
import axios from "@/api/axios";
import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamTypeStore } from "@/stores/examType";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    "submit",
    "cancel",
]);

const authStore = useAuthStore();
const sessionStore = useAcademicSessionStore();
const examTypeStore = useExamTypeStore();
const allSchools = ref([]);

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const title = computed(() =>
    props.form.id
        ? "Edit Exam"
        : "Create Exam"
);

async function loadData() {
    const params = {};
    if (props.form.school_id) {
        params.school_id = props.form.school_id;
    }
    await Promise.all([
        sessionStore.load({
            per_page: 1000,
            ...params
        }),
        examTypeStore.fetch({
            per_page: 1000,
            status: 1,
            ...params
        }),
    ]);

    if (!props.form.academic_session_id && sessionStore.sessions.length > 0) {
        const currentSession = sessionStore.sessions.find(s => s.is_current) || sessionStore.sessions[0];
        if (currentSession) {
            props.form.academic_session_id = currentSession.id;
        }
    }
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

function onSchoolChange() {
    loadData();
}

function handleTenantSwitched() {
    loadData();
}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        loadData()
    ]);
    window.addEventListener('tenant-switched', handleTenantSwitched);
});

onBeforeUnmount(() => {
    window.removeEventListener('tenant-switched', handleTenantSwitched);
});
</script>

<template>

<form @submit.prevent="emit('submit')">

<div class="card shadow-sm">

<div class="card-header">

<h5 class="mb-0">

{{ title }}

</h5>

</div>

<div class="card-body">

<div class="row">

<!-- Target School / Campus (Super Admin) -->
<div class="col-md-12 mb-3" v-if="isSuperAdmin && allSchools.length > 0">
    <label class="form-label fw-semibold">
        <i class="bi bi-buildings text-primary me-1"></i> Target School / Campus
    </label>
    <select
        class="form-select"
        v-model="form.school_id"
        @change="onSchoolChange"
    >
        <option value="">All / Current Active School</option>
        <option
            v-for="s in allSchools"
            :key="'exm-form-sch-' + s.id"
            :value="s.id"
        >
            {{ s.name }} ({{ s.code }})
        </option>
    </select>
    <small class="text-muted">Optionally choose a specific campus to filter sessions and exam types.</small>
</div>

<!-- Academic Session -->

<div class="col-md-6 mb-3">

<label class="form-label">

Academic Session <span class="text-danger">*</span>

</label>

<select
class="form-select"
v-model="form.academic_session_id"
>

<option value="">

Select Academic Session

</option>

<option
v-for="session in sessionStore.sessions"
:key="session.id"
:value="session.id"
>

{{ session.name }} {{ session.school?.name ? '(' + session.school.name + ')' : '' }}

</option>

</select>

<div
class="text-danger"
v-if="errors.academic_session_id"
>

{{ errors.academic_session_id[0] }}

</div>

</div>

<!-- Exam Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Type <span class="text-danger">*</span>

</label>

<select
class="form-select"
v-model="form.exam_type_id"
>

<option value="">

Select Exam Type

</option>

<option
v-for="item in examTypeStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }} {{ item.school?.name ? '(' + item.school.name + ')' : '' }}

</option>

</select>

<div
class="text-danger"
v-if="errors.exam_type_id"
>

{{ errors.exam_type_id[0] }}

</div>

</div>

<!-- Exam Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Name

</label>

<input
type="text"
class="form-control"
v-model="form.name"
/>

<div
class="text-danger"
v-if="errors.name"
>

{{ errors.name[0] }}

</div>

</div>

<!-- Exam Code -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Code

</label>

<input
type="text"
class="form-control"
v-model="form.code"
/>

<div
class="text-danger"
v-if="errors.code"
>

{{ errors.code[0] }}

</div>

</div>

<!-- Start Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

Start Date

</label>

<input
type="date"
class="form-control"
v-model="form.start_date"
/>

<div
class="text-danger"
v-if="errors.start_date"
>

{{ errors.start_date[0] }}

</div>

</div>

<!-- End Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

End Date

</label>

<input
type="date"
class="form-control"
v-model="form.end_date"
/>

<div
class="text-danger"
v-if="errors.end_date"
>

{{ errors.end_date[0] }}

</div>

</div>

<!-- Description -->

<div class="col-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
class="form-control"
rows="3"
v-model="form.description"
></textarea>

<div
class="text-danger"
v-if="errors.description"
>

{{ errors.description[0] }}

</div>

</div>

<!-- Flags -->

<div class="col-md-4 mb-3">

<div class="form-check">

<input
type="checkbox"
class="form-check-input"
id="status"
v-model="form.status"
/>

<label
class="form-check-label"
for="status"
>

Active

</label>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="form-check">

<input
type="checkbox"
class="form-check-input"
id="marksLocked"
v-model="form.is_marks_locked"
/>

<label
class="form-check-label"
for="marksLocked"
>

Lock Marks

</label>

</div>

</div>

<div class="col-md-4 mb-3">

<div class="form-check">

<input
type="checkbox"
class="form-check-input"
id="resultPublished"
v-model="form.is_result_published"
/>

<label
class="form-check-label"
for="resultPublished"
>

Publish Result

</label>

</div>

</div>

</div>

</div>

<div class="card-footer text-end">

<button
type="button"
class="btn btn-secondary me-2"
@click="emit('cancel')"
>

Cancel

</button>

<button
type="submit"
class="btn btn-primary"
:disabled="loading"
>

Save Exam

</button>

</div>

</div>

</form>

</template>
