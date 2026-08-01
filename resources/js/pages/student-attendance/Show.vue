<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";

const route = useRoute();
const router = useRouter();

const store = useStudentAttendanceStore();

const attendance = ref(null);

onMounted(async () => {

    attendance.value = await store.find(route.params.id);

});
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between">

<h4>Attendance Details</h4>

<button
class="btn btn-secondary"
@click="router.back()"
>
Back
</button>

</div>

<div
class="card-body"
v-if="attendance"
>

<div class="row">

<div class="col-md-6 mb-3">

<label class="fw-bold">

Student

</label>

<p>

{{ attendance.student?.name }}

</p>

</div>

<div class="col-md-3 mb-3">

<label class="fw-bold">

Admission No

</label>

<p>

{{ attendance.student?.admission_no }}

</p>

</div>

<div class="col-md-3 mb-3">

<label class="fw-bold">

Roll No

</label>

<p>

{{ attendance.student?.roll_no }}

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Class

</label>

<p>

{{ attendance.class?.name }}

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Section

</label>

<p>

{{ attendance.section?.name }}

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Attendance Date

</label>

<p>

{{ attendance.attendance_date }}

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Status

</label>

<p>

<span
class="badge"
:class="'bg-' + attendance.status_badge"
>

{{ attendance.status }}

</span>

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Check In

</label>

<p>

{{ attendance.check_in ?? "-" }}

</p>

</div>

<div class="col-md-4 mb-3">

<label class="fw-bold">

Check Out

</label>

<p>

{{ attendance.check_out ?? "-" }}

</p>

</div>

<div class="col-md-12">

<label class="fw-bold">

Remarks

</label>

<p>

{{ attendance.remarks || "-" }}

</p>

</div>

</div>

</div>

</div>

</div>

</template>