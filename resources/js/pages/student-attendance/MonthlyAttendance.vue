<script setup>
import { reactive, ref, onMounted } from "vue";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";
import { useStudentStore } from "@/stores/student";

const attendanceStore = useStudentAttendanceStore();
const studentStore = useStudentStore();

const form = reactive({
    student_id: "",
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
});

const loading = ref(false);

const months = [
    { value: 1, name: "January" },
    { value: 2, name: "February" },
    { value: 3, name: "March" },
    { value: 4, name: "April" },
    { value: 5, name: "May" },
    { value: 6, name: "June" },
    { value: 7, name: "July" },
    { value: 8, name: "August" },
    { value: 9, name: "September" },
    { value: 10, name: "October" },
    { value: 11, name: "November" },
    { value: 12, name: "December" },
];

const years = [];

for (let i = 2024; i <= 2035; i++) {
    years.push(i);
}

onMounted(async () => {

    await studentStore.fetch({
        per_page: 1000,
        status: 1,
    });

});

async function loadReport() {

    loading.value = true;

    try {

        await attendanceStore.monthly(form);

        await attendanceStore.summaryReport(form);

    } finally {

        loading.value = false;

    }

}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header">

<h4>Monthly Attendance Report</h4>

</div>

<div class="card-body">

<div class="row g-3 mb-4">

<div class="col-md-4">

<label class="form-label">

Student

</label>

<select
class="form-select"
v-model="form.student_id"
>

<option value="">
Select Student
</option>

<option
v-for="student in studentStore.items"
:key="student.id"
:value="student.id"
>

{{ student.full_name }}

</option>

</select>

</div>

<div class="col-md-3">

<label class="form-label">

Month

</label>

<select
class="form-select"
v-model="form.month"
>

<option
v-for="month in months"
:key="month.value"
:value="month.value"
>

{{ month.name }}

</option>

</select>

</div>

<div class="col-md-3">

<label class="form-label">

Year

</label>

<select
class="form-select"
v-model="form.year"
>

<option
v-for="year in years"
:key="year"
:value="year"
>

{{ year }}

</option>

</select>

</div>

<div class="col-md-2 d-flex align-items-end">

<button
class="btn btn-primary w-100"
@click="loadReport"
>

Load

</button>

</div>

</div>

<div
class="row mb-4"
v-if="attendanceStore.summary.total"
>

<div class="col">

<div class="card bg-success text-white">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.present }}</h5>

Present

</div>

</div>

</div>

<div class="col">

<div class="card bg-danger text-white">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.absent }}</h5>

Absent

</div>

</div>

</div>

<div class="col">

<div class="card bg-warning">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.late }}</h5>

Late

</div>

</div>

</div>

<div class="col">

<div class="card bg-info text-white">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.half_day }}</h5>

Half Day

</div>

</div>

</div>

<div class="col">

<div class="card bg-secondary text-white">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.leave }}</h5>

Leave

</div>

</div>

</div>

<div class="col">

<div class="card bg-dark text-white">

<div class="card-body text-center">

<h5>{{ attendanceStore.summary.total }}</h5>

Total

</div>

</div>

</div>

</div>

<div
class="table-responsive"
v-if="attendanceStore.monthlyItems.length"
>

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Date</th>

<th>Status</th>

<th>Check In</th>

<th>Check Out</th>

<th>Remarks</th>

</tr>

</thead>

<tbody>

<tr
v-for="item in attendanceStore.monthlyItems"
:key="item.id"
>

<td>

{{ item.attendance_date }}

</td>

<td>

<span
class="badge"
:class="'bg-' + item.status_badge"
>

{{ item.status }}

</span>

</td>

<td>

{{ item.check_in || "-" }}

</td>

<td>

{{ item.check_out || "-" }}

</td>

<td>

{{ item.remarks || "-" }}

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

</div>

</template>