<script setup>
import { ref, reactive } from "vue";

import { useStudentAttendanceStore } from "@/stores/studentAttendance";
import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";

const attendanceStore = useStudentAttendanceStore();
const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();

const loadingStudents = ref(false);

const form = reactive({

    attendance_date: new Date().toISOString().slice(0,10),

    class_id: "",

    section_id: "",

    records: []

});

async function loadStudents(){

    loadingStudents.value = true;

    try{

        await studentStore.fetch({

            class_id: form.class_id,

            section_id: form.section_id,

            per_page:1000,

            status:1

        });

        form.records = studentStore.items.map(student=>({

            student_id: student.id,

            status:"Present",

            remarks:"",

            check_in:null,

            check_out:null

        }));

    }finally{

        loadingStudents.value=false;

    }

}

async function saveAttendance(){

    await attendanceStore.bulk(form);

    alert("Attendance saved successfully.");

}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header">

<h4>Student Attendance</h4>

</div>

<div class="card-body">

<div class="row g-3">

<div class="col-md-3">

<label>Date</label>

<input
type="date"
class="form-control"
v-model="form.attendance_date"
/>

</div>

<div class="col-md-3">

<label>Class</label>

<select
class="form-select"
v-model="form.class_id"
>

<option value="">
Select Class
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

<div class="col-md-3">

<label>Section</label>

<select
class="form-select"
v-model="form.section_id"
>

<option value="">
Select Section
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

<div class="col-md-3 d-flex align-items-end">

<button
class="btn btn-primary w-100"
@click="loadStudents"
>

Load Students

</button>

</div>

</div>

<hr>

<div
v-if="form.records.length"
class="table-responsive"
>

<table class="table table-bordered">

<thead>

<tr>

<th width="60">
#
</th>

<th>
Student
</th>

<th width="420">
Attendance
</th>

<th>
Remarks
</th>

</tr>

</thead>

<tbody>

<tr
v-for="(row,index) in form.records"
:key="row.student_id"
>

<td>

{{ index+1 }}

</td>

<td>

{{ studentStore.items[index].full_name }}

</td>

<td>

<div class="d-flex gap-3">

<label>

<input
type="radio"
:value="'Present'"
v-model="row.status"
/>

Present

</label>

<label>

<input
type="radio"
:value="'Absent'"
v-model="row.status"
/>

Absent

</label>

<label>

<input
type="radio"
:value="'Late'"
v-model="row.status"
/>

Late

</label>

<label>

<input
type="radio"
:value="'Half Day'"
v-model="row.status"
/>

Half Day

</label>

<label>

<input
type="radio"
:value="'Leave'"
v-model="row.status"
/>

Leave

</label>

</div>

</td>

<td>

<input
class="form-control"
v-model="row.remarks"
/>

</td>

</tr>

</tbody>

</table>

</div>

<div
class="text-end mt-4"
v-if="form.records.length"
>

<button
class="btn btn-success"
@click="saveAttendance"
>

Save Attendance

</button>

</div>

</div>

</div>

</div>

</template>