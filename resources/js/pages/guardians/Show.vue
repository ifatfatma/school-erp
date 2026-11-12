<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useGuardianStore } from "@/stores/guardian";

const route = useRoute();
const router = useRouter();

const guardianStore = useGuardianStore();

const guardian = ref(null);

onMounted(async () => {

    guardian.value = await guardianStore.find(route.params.id);

});
</script>

<template>

<div class="container-fluid">

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h4 class="mb-0">
Guardian Details
</h4>

<div>

<button
class="btn btn-secondary me-2"
@click="router.back()"
>
Back
</button>

<RouterLink
class="btn btn-warning"
:to="{
name:'guardians.edit',
params:{id:guardian?.id}
}"
>
Edit
</RouterLink>

</div>

</div>

<div
class="card-body"
v-if="guardian"
>

<div class="row">

<div class="col-md-3 text-center">

<img

v-if="guardian.photo_url"

:src="guardian.photo_url"

class="img-thumbnail rounded"

style="max-width:180px"

>

<div
v-else
class="border rounded p-5 bg-light"
>

<i class="fas fa-user fa-5x text-secondary"></i>

</div>

<h5 class="mt-3">

{{ guardian.full_name }}

</h5>

<span
class="badge"
:class="guardian.status ? 'bg-success':'bg-danger'"
>

{{ guardian.status ? 'Active':'Inactive' }}

</span>

</div>

<div class="col-md-9">

<div class="row">

<div class="col-md-6 mb-3">

<strong>Guardian Type</strong>

<p>{{ guardian.guardian_type }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Gender</strong>

<p>{{ guardian.gender || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Date of Birth</strong>

<p>{{ guardian.dob || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Relation</strong>

<p>{{ guardian.relation || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Mobile</strong>

<p>{{ guardian.mobile }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Alternate Mobile</strong>

<p>{{ guardian.alternate_mobile || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Email</strong>

<p>{{ guardian.email || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Occupation</strong>

<p>{{ guardian.occupation || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Annual Income</strong>

<p>{{ guardian.annual_income || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Education</strong>

<p>{{ guardian.education || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>Aadhaar No</strong>

<p>{{ guardian.aadhaar_no || "-" }}</p>

</div>

<div class="col-md-6 mb-3">

<strong>PAN No</strong>

<p>{{ guardian.pan_no || "-" }}</p>

</div>

<div class="col-12">

<strong>Address</strong>

<p>

{{ guardian.address }}

<br>

{{ guardian.city }}

{{ guardian.state }}

{{ guardian.country }}

{{ guardian.pincode }}

</p>

</div>

</div>

</div>

</div>

<hr>

<h5 class="mb-3">

Linked Students

</h5>

<div
class="table-responsive"
v-if="guardian.students?.length"
>

<table class="table table-bordered">

<thead>

<tr>

<th>Admission No</th>

<th>Roll No</th>

<th>Student</th>

<th>Primary</th>

</tr>

</thead>

<tbody>

<tr
v-for="student in guardian.students"
:key="student.id"
>

<td>

{{ student.admission_no }}

</td>

<td>

{{ student.roll_no }}

</td>

<td>

{{ student.name }}

</td>

<td>

<span
class="badge bg-success"
v-if="student.is_primary"
>

Primary

</span>

</td>

</tr>

</tbody>

</table>

</div>

<div
v-else
class="alert alert-info d-flex justify-content-between align-items-center"
>

<span><i class="bi bi-info-circle me-1"></i> No students linked with this guardian yet.</span>

<RouterLink
:to="{ name: 'guardians.edit', params: { id: guardian.id } }"
class="btn btn-sm btn-primary rounded-pill px-3"
>
<i class="bi bi-link-45deg me-1"></i> Link Student Now
</RouterLink>

</div>

</div>

</div>

</div>

</template>