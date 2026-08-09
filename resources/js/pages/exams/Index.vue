<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useExamStore } from "@/stores/exam";
import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamTypeStore } from "@/stores/examType";

const router = useRouter();

const examStore = useExamStore();
const sessionStore = useAcademicSessionStore();
const examTypeStore = useExamTypeStore();

const filters = reactive({

    search: "",

    academic_session_id: "",

    exam_type_id: "",

    status: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "name", label: "Exam" },

    { key: "code", label: "Code" },

    { key: "academic_session", label: "Academic Session" },

    { key: "exam_type", label: "Exam Type" },

    { key: "start_date", label: "Start Date" },

    { key: "end_date", label: "End Date" },

    { key: "marks", label: "Marks" },

    { key: "result", label: "Result" },

    { key: "status", label: "Status" },

    { key: "action", label: "Action" },

];

onMounted(async () => {

    await Promise.all([

        load(),

        sessionStore.load({ per_page: 1000 }),

        examTypeStore.fetch({
            per_page: 1000,
            status: 1
        })

    ]);

});

async function load(page = 1) {

    filters.page = page;

    await examStore.fetch(filters);

}

function reset() {

    filters.search = "";

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

<div class="row g-3 mb-3">

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

</template>