<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

import { useStudentPromotionStore } from "@/stores/studentPromotion";
import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useAcademicSessionStore } from "@/stores/academicSession";

const router = useRouter();

const promotionStore = useStudentPromotionStore();
const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const sessionStore = useAcademicSessionStore();

const students = ref([]);

const form = reactive({

    from_session_id: "",

    from_class_id: "",

    from_section_id: "",

    to_session_id: "",

    to_class_id: "",

    to_section_id: "",

    promotion_date: new Date().toISOString().substring(0,10),

    promotion_type: "Promoted",

    remarks: "",

    student_ids: [],

});

async function loadMasters(){

    await Promise.all([

        sessionStore.load({per_page:1000}),

        classStore.fetch({per_page:1000}),

        sectionStore.load({per_page:1000})

    ]);

}

loadMasters();

async function searchStudents(){

    await studentStore.fetch({

        academic_session_id: form.from_session_id,

        class_id: form.from_class_id,

        section_id: form.from_section_id,

        per_page:1000

    });

    students.value = studentStore.items;

}

function selectAll(){

    form.student_ids = students.value.map(i=>i.id);

}

function unSelectAll(){

    form.student_ids=[];

}

async function promote(){

    try{

        await promotionStore.bulkPromotion(form);

        alert("Students promoted successfully.");

        router.push({

            name:"student-promotions.index"

        });

    }catch(e){

        console.log(e);

    }

}
</script>

<template>

<div class="container-fluid">

<div class="card shadow">

<div class="card-header">

<h4>

Bulk Student Promotion

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-4">

<label>Current Session</label>

<select
class="form-select"
v-model="form.from_session_id"
>

<option value="">

Select

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

<div class="col-md-4">

<label>Current Class</label>

<select
class="form-select"
v-model="form.from_class_id"
>

<option value="">

Select

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

<div class="col-md-4">

<label>Current Section</label>

<select
class="form-select"
v-model="form.from_section_id"
>

<option value="">

Select

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

</div>

<div class="mt-3">

<button
class="btn btn-primary"
@click="searchStudents"
>

Load Students

</button>

</div>

<hr>

<div
v-if="students.length"
>

<div class="d-flex justify-content-between mb-3">

<div>

<button
class="btn btn-success btn-sm"
@click="selectAll"
>

Select All

</button>

<button
class="btn btn-secondary btn-sm ms-2"
@click="unSelectAll"
>

Clear

</button>

</div>

<strong>

{{ form.student_ids.length }}

Selected

</strong>

</div>

<div class="table-responsive">

<table class="table table-bordered">

<thead>

<tr>

<th width="40">

</th>

<th>

Admission

</th>

<th>

Roll

</th>

<th>

Student

</th>

</tr>

</thead>

<tbody>

<tr
v-for="student in students"
:key="student.id"
>

<td>

<input

type="checkbox"

:checked="form.student_ids.includes(student.id)"

@change="

$event.target.checked

? form.student_ids.push(student.id)

: form.student_ids = form.student_ids.filter(i=>i!=student.id)

"

>

</td>

<td>

{{ student.admission_no }}

</td>

<td>

{{ student.roll_no }}

</td>

<td>

{{ student.full_name }}

</td>

</tr>

</tbody>

</table>

</div>

<hr>

<h5>

Promotion Details

</h5>

<div class="row">

<div class="col-md-4">

<label>

Next Session

</label>

<select
class="form-select"
v-model="form.to_session_id"
>

<option value="">

Select

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

<div class="col-md-4">

<label>

Next Class

</label>

<select
class="form-select"
v-model="form.to_class_id"
>

<option value="">

Select

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

<div class="col-md-4">

<label>

Next Section

</label>

<select
class="form-select"
v-model="form.to_section_id"
>

<option value="">

Select

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

<div class="col-md-4 mt-3">

<label>

Promotion Date

</label>

<input
type="date"
class="form-control"
v-model="form.promotion_date"
/>

</div>

<div class="col-md-4 mt-3">

<label>

Promotion Type

</label>

<select
class="form-select"
v-model="form.promotion_type"
>

<option>

Promoted

</option>

<option>

Repeated

</option>

<option>

Transferred

</option>

<option>

Graduated

</option>

<option>

Dropped

</option>

</select>

</div>

<div class="col-md-12 mt-3">

<label>

Remarks

</label>

<textarea
class="form-control"
rows="3"
v-model="form.remarks"
/>

</div>

</div>

<div class="mt-4">

<button
class="btn btn-success"
@click="promote"
:disabled="promotionStore.saving"
>

{{ promotionStore.saving ? "Processing..." : "Promote Selected Students" }}

</button>

</div>

</div>

</div>

</div>

</div>

</template>