<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";

const route = useRoute();
const router = useRouter();

const store = useStudentAttendanceStore();

const form = reactive({

    attendance_date: "",

    school_id: "",

    student_id: "",

    class_id: "",

    section_id: "",

    status: "Present",

    check_in: "",

    check_out: "",

    remarks: ""

});

const statuses = [

    "Present",

    "Absent",

    "Late",

    "Half Day",

    "Leave"

];

onMounted(async () => {

    const item = await store.find(route.params.id);

    Object.assign(form, item);

});

async function submit(){

    await store.update(route.params.id, form);

    router.push({
        name:"student-attendance.index"
    });

}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header">

<h4>Edit Student Attendance</h4>

</div>

<div class="card-body">

<form @submit.prevent="submit">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Student

</label>

<input
class="form-control"
:readonly="true"
:value="store.item?.student?.name"
/>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Class

</label>

<input
class="form-control"
:readonly="true"
:value="store.item?.class?.name"
/>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Section

</label>

<input
class="form-control"
:readonly="true"
:value="store.item?.section?.name"
/>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Attendance Date

</label>

<input
type="date"
class="form-control"
v-model="form.attendance_date"
/>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Status

</label>

<select
class="form-select"
v-model="form.status"
>

<option
v-for="status in statuses"
:key="status"
:value="status"
>

{{ status }}

</option>

</select>

<div
class="text-danger"
v-if="store.errors.status"
>

{{ store.errors.status[0] }}

</div>

</div>

<div class="col-md-2 mb-3">

<label class="form-label">

Check In

</label>

<input
type="time"
class="form-control"
v-model="form.check_in"
/>

</div>

<div class="col-md-2 mb-3">

<label class="form-label">

Check Out

</label>

<input
type="time"
class="form-control"
v-model="form.check_out"
/>

</div>

<div class="col-12 mb-3">

<label class="form-label">

Remarks

</label>

<textarea
rows="3"
class="form-control"
v-model="form.remarks"
></textarea>

<div
class="text-danger"
v-if="store.errors.remarks"
>

{{ store.errors.remarks[0] }}

</div>

</div>

</div>

<div class="mt-3">

<button
class="btn btn-primary"
type="submit"
:disabled="store.saving"
>

{{ store.saving ? "Updating..." : "Update Attendance" }}

</button>

<button
type="button"
class="btn btn-secondary ms-2"
@click="router.back()"
>

Cancel

</button>

</div>

</form>

</div>

</div>

</div>

</template>