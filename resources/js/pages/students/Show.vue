<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useStudentStore } from "@/stores/student";

const route = useRoute();
const router = useRouter();

const studentStore = useStudentStore();

const loading = ref(false);

onMounted(async () => {

    loading.value = true;

    try {

        await studentStore.find(route.params.id);

    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

});

const student = studentStore;
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between align-items-center">

<h4>Student Details</h4>

<button
class="btn btn-secondary"
@click="router.back()"
>
Back
</button>

</div>

<div
class="card-body"
v-if="!loading && student.item"
>

<div class="row">

<div class="col-md-3 text-center">

<img
:src="student.item.photo_url"
class="img-thumbnail"
style="width:180px;height:180px;object-fit:cover"
/>

</div>

<div class="col-md-9">

<table class="table table-bordered">

<tbody>

<tr>

<th width="220">
Admission No
</th>

<td>
{{ student.item.admission_no }}
</td>

</tr>

<tr>

<th>
Roll No
</th>

<td>
{{ student.item.roll_no }}
</td>

</tr>

<tr>

<th>
Student Name
</th>

<td>
{{ student.item.full_name }}
</td>

</tr>

<tr>

<th>
Gender
</th>

<td>
{{ student.item.gender }}
</td>

</tr>

<tr>

<th>
Date Of Birth
</th>

<td>
{{ student.item.dob }}
</td>

</tr>

<tr>

<th>
Admission Date
</th>

<td>
{{ student.item.admission_date }}
</td>

</tr>

<tr>

<th>
Class
</th>

<td>
{{ student.item.class?.name }}
</td>

</tr>

<tr>

<th>
Section
</th>

<td>
{{ student.item.section?.name }}
</td>

</tr>

<tr>

<th>
Blood Group
</th>

<td>
{{ student.item.blood_group }}
</td>

</tr>

<tr>

<th>
Religion
</th>

<td>
{{ student.item.religion }}
</td>

</tr>

<tr>

<th>
Category
</th>

<td>
{{ student.item.category }}
</td>

</tr>

<tr>

<th>
Aadhaar
</th>

<td>
{{ student.item.aadhaar_no }}
</td>

</tr>

<tr>

<th>
Mobile
</th>

<td>
{{ student.item.mobile }}
</td>

</tr>

<tr>

<th>
Email
</th>

<td>
{{ student.item.email }}
</td>

</tr>

<tr>

<th>
Address
</th>

<td>
{{ student.item.address }}
</td>

</tr>

<tr>

<th>
Father Name
</th>

<td>
{{ student.item.father_name }}
</td>

</tr>

<tr>

<th>
Father Mobile
</th>

<td>
{{ student.item.father_mobile }}
</td>

</tr>

<tr>

<th>
Father Occupation
</th>

<td>
{{ student.item.father_occupation }}
</td>

</tr>

<tr>

<th>
Mother Name
</th>

<td>
{{ student.item.mother_name }}
</td>

</tr>

<tr>

<th>
Mother Mobile
</th>

<td>
{{ student.item.mother_mobile }}
</td>

</tr>

<tr>

<th>
Guardian Name
</th>

<td>
{{ student.item.guardian_name }}
</td>

</tr>

<tr>

<th>
Guardian Mobile
</th>

<td>
{{ student.item.guardian_mobile }}
</td>

</tr>

<tr>

<th>
Status
</th>

<td>

<span
class="badge"
:class="student.item.status ? 'bg-success' : 'bg-danger'"
>

{{ student.item.status_text }}

</span>

</td>

</tr>

<tr>

<th>
Created At
</th>

<td>
{{ student.item.created_at }}
</td>

</tr>

<tr>

<th>
Updated At
</th>

<td>
{{ student.item.updated_at }}
</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

<div
v-else
class="card-body text-center"
>

Loading...

</div>

</div>

</div>

</template>