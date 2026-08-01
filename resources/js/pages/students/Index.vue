<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";

const router = useRouter();

const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();

const filters = reactive({
    search: "",
    class_id: "",
    section_id: "",
    status: "",
    page: 1,
    per_page: 15,
});

const columns = [
    { key: "photo", label: "Photo" },
    { key: "admission_no", label: "Admission No" },
    { key: "roll_no", label: "Roll No" },
    { key: "student", label: "Student" },
    { key: "class", label: "Class" },
    { key: "section", label: "Section" },
    { key: "mobile", label: "Mobile" },
    { key: "status", label: "Status" },
    { key: "action", label: "Action" },
];

onMounted(async () => {
    await Promise.all([
        classStore.fetch(),
        sectionStore.load(),
        load(),
    ]);
});

async function load() {
    await studentStore.fetch(filters);
}

function reset() {
    filters.search = "";
    filters.class_id = "";
    filters.section_id = "";
    filters.status = "";
    filters.page = 1;

    load();
}

async function remove(id) {

    if (!confirm("Delete this student?"))
        return;

    await studentStore.delete(id);

    load();
}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">

<h4>Students</h4>

<RouterLink
    :to="{ name:'students.create' }"
    class="btn btn-primary"
>
    Add Student
</RouterLink>

</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-3">

<input
class="form-control"
placeholder="Search..."
v-model="filters.search"
/>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filters.class_id"
>

<option value="">
All Classes
</option>

<option
v-for="item in classStore.items"
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
v-model="filters.section_id"
>

<option value="">
All Sections
</option>

<option
v-for="item in sectionStore.items"
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
All Status
</option>

<option :value="true">
Active
</option>

<option :value="false">
Inactive
</option>

</select>

</div>

<div class="col-md-3 text-end">

<button
class="btn btn-primary me-2"
@click="load"
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
    :items="studentStore.items"
    :loading="studentStore.loading"
>

<template #photo="{ item }">

<img
:src="item.photo_url"
class="rounded"
style="width:45px;height:45px;object-fit:cover"
/>

</template>

<template #student="{ item }">

<strong>
{{ item.full_name }}
</strong>

</template>

<template #class="{ item }">

{{ item.class?.name }}

</template>

<template #section="{ item }">

{{ item.section?.name }}

</template>

<template #status="{ item }">

<span
class="badge"
:class="item.status ? 'bg-success' : 'bg-danger'"
>

{{ item.status_text }}

</span>

</template>

<template #action="{ item }">

<div class="btn-group">

<RouterLink
class="btn btn-info btn-sm"
:to="{
name:'students.show',
params:{id:item.id}
}"
>
View
</RouterLink>

<RouterLink
class="btn btn-warning btn-sm"
:to="{
name:'students.edit',
params:{id:item.id}
}"
>
Edit
</RouterLink>

<button
class="btn btn-danger btn-sm"
@click="remove(item.id)"
>
Delete
</button>

</div>

</template>

</AppTable>

</div>

</div>

</div>

</template>