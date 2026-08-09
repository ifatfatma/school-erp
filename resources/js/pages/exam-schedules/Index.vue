<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useExamScheduleStore } from "@/stores/examSchedule";
import { useExamStore } from "@/stores/exam";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useExamSubjectStore } from "@/stores/examSubject";

const router = useRouter();

const scheduleStore = useExamScheduleStore();
const examStore = useExamStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const subjectStore = useExamSubjectStore();

const filters = reactive({

    search: "",

    exam_id: "",

    class_id: "",

    section_id: "",

    subject_id: "",

    exam_date: "",

    status: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "exam", label: "Exam" },

    { key: "class", label: "Class" },

    { key: "section", label: "Section" },

    { key: "subject", label: "Subject" },

    { key: "teacher", label: "Teacher" },

    { key: "exam_date", label: "Date" },

    { key: "time", label: "Time" },

    { key: "marks", label: "Marks" },

    { key: "room", label: "Room" },

    { key: "status", label: "Status" },

    { key: "action", label: "Action" },

];

onMounted(async () => {

    await Promise.all([

        load(),

        examStore.active(),

        classStore.active(),
        sectionStore.active(),

        subjectStore.active()

    ]);

});

async function load(page = 1) {

    filters.page = page;

    await scheduleStore.fetch(filters);

}

async function loadSections() {

    filters.section_id = "";

    if (!filters.class_id) {

        sectionStore.items = [];

        return;

    }

    await sectionStore.load({

        class_id: filters.class_id,

        per_page: 1000,

        status: 1

    });

}

function reset() {

    filters.search = "";

    filters.exam_id = "";

    filters.class_id = "";

    filters.section_id = "";

    filters.subject_id = "";

    filters.exam_date = "";

    filters.status = "";

    load();

}

function create() {

    router.push({

        name: "exam-schedules.create"

    });

}

function edit(id) {

    router.push({

        name: "exam-schedules.edit",

        params: { id }

    });

}

async function remove(id) {

    if (!confirm("Delete this Exam Schedule?")) return;

    await scheduleStore.delete(id);

    load(filters.page);

}
</script>

<template>

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">

Exam Schedule

</h5>

<button

class="btn btn-primary"

@click="create"

>

<i class="fas fa-plus"></i>

Add Schedule

</button>

</div>

<div class="card-body">

<div class="row g-3 mb-3">

<div class="col-md-3">

<select

class="form-select"

v-model="filters.exam_id"

>

<option value="">Exam</option>

<option

v-for="item in examStore.items"

:key="item.id"

:value="item.id"

>

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-3">

<select

class="form-select"

v-model="filters.class_id"

@change="loadSections"

>

<option value="">Class</option>

<option

v-for="item in classStore.items"

:key="item.id"

:value="item.id"

>

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-3">

<select

class="form-select"

v-model="filters.section_id"

>

<option value="">Section</option>

<option

v-for="item in sectionStore.items"

:key="item.id"

:value="item.id"

>

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-3">
<select

class="form-select"

v-model="filters.subject_id"

>

<option value="">Subject</option>

<option

v-for="item in subjectStore.items"

:key="item.id"

:value="item.id"

>

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-3">

<input

type="date"

class="form-control"

v-model="filters.exam_date"

/>

</div>

<div class="col-md-3">

<select

class="form-select"

v-model="filters.status"

>

<option value="">Status</option>

<option :value="1">Active</option>

<option :value="0">Inactive</option>

</select>

</div>

<div class="col-md-6">

<input

class="form-control"

placeholder="Search"

v-model="filters.search"

/>

</div>

</div>

<div class="mb-3">

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

<AppTable

:columns="columns"

:items="scheduleStore.items"

:pagination="scheduleStore.pagination"

:loading="scheduleStore.loading"

@page-change="load"

>

<template #exam="{ item }">

{{ item.exam?.name }}

</template>

<template #class="{ item }">

{{ item.class?.name }}

</template>

<template #section="{ item }">

{{ item.section?.name }}

</template>

<template #subject="{ item }">

{{ item.subject?.name }}

</template>

<template #teacher="{ item }">

{{ item.teacher?.name || "-" }}

</template>

<template #time="{ item }">

{{ item.start_time }} - {{ item.end_time }}

</template>

<template #marks="{ item }">

{{ item.pass_marks }} / {{ item.full_marks }}

</template>

<template #room="{ item }">

{{ item.room_no || "-" }}

</template>

<template #status="{ item }">

<span

class="badge"

:class="item.status ? 'bg-success' : 'bg-danger'"

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

</div>

</template>

</AppTable>

</div>

</div>

</template>