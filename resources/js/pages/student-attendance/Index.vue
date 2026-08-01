<script setup>
import { reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";

const attendanceStore = useStudentAttendanceStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();

const filters = reactive({

    search: "",

    attendance_date: "",

    class_id: "",

    section_id: "",

    status: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "student", label: "Student" },

    { key: "class", label: "Class" },

    { key: "section", label: "Section" },

    { key: "attendance_date", label: "Date" },

    { key: "status", label: "Status" },

    { key: "remarks", label: "Remarks" },

    { key: "action", label: "Action" },

];

onMounted(async () => {

    await Promise.all([

        classStore.fetch(),

        sectionStore.load(),

        load(),

    ]);

});

async function load(){

    await attendanceStore.fetch(filters);

}

function reset(){

    filters.search = "";

    filters.attendance_date = "";

    filters.class_id = "";

    filters.section_id = "";

    filters.status = "";

    filters.page = 1;

    load();

}

async function remove(id){

    if(!confirm("Delete attendance?")){

        return;

    }

    await attendanceStore.delete(id);

    load();

}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between">

<h4>Student Attendance</h4>

<RouterLink
    :to="{name:'student-attendance.bulk'}"
    class="btn btn-primary"
>
    Mark Attendance
</RouterLink>

</div>

<div class="card-body">

<div class="row g-3 mb-3">

<div class="col-md-3">

<input
class="form-control"
placeholder="Search Student"
v-model="filters.search"
/>

</div>

<div class="col-md-2">

<input
type="date"
class="form-control"
v-model="filters.attendance_date"
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

<option>Present</option>

<option>Absent</option>

<option>Late</option>

<option>Half Day</option>

<option>Leave</option>

</select>

</div>

<div class="col-md-1">

<button
class="btn btn-primary w-100"
@click="load"
>
Go
</button>

</div>

</div>

<AppTable
    :columns="columns"
    :items="attendanceStore.items"
    :loading="attendanceStore.loading"
>

<template #student="{ item }">

{{ item.student?.name }}

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
:class="'bg-' + item.status_badge"
>
{{ item.status }}
</span>

</template>

<template #action="{ item }">

<div class="btn-group">

<RouterLink
class="btn btn-info btn-sm"
:to="{
name:'student-attendance.show',
params:{id:item.id}
}"
>
View
</RouterLink>

<RouterLink
class="btn btn-warning btn-sm"
:to="{
name:'student-attendance.edit',
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