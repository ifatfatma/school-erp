<script setup>
import { computed } from "vue";

const props = defineProps({

    form: {
        type: Object,
        required: true,
    },

    students: {
        type: Array,
        default: () => [],
    },

    errors: {
        type: Object,
        default: () => ({}),
    },

    loading: {
        type: Boolean,
        default: false,
    }

});

defineEmits([
    "submit",
    "cancel",
]);

const guardianTypes = computed(() => [
    "Father",
    "Mother",
    "Grandfather",
    "Grandmother",
    "Uncle",
    "Aunt",
    "Brother",
    "Sister",
    "Legal Guardian",
    "Other",
]);

const genders = computed(() => [
    "Male",
    "Female",
    "Other",
]);
</script>

<template>

<form @submit.prevent="$emit('submit')">

<div
    v-if="Object.keys(errors).length"
    class="alert alert-danger mb-3"
>
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0">
        <li v-for="(messages, field) in errors" :key="field">
            {{ messages[0] }}
        </li>
    </ul>
</div>

<div class="card shadow-sm mb-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">
Guardian Information
</h5>

</div>

<div class="card-body">

<div class="row">

<!-- Guardian Type -->

<div class="col-md-4 mb-3">

<label class="form-label">
Guardian Type
</label>

<select
class="form-select"
v-model="form.guardian_type"
>

<option value="">
Select Type
</option>

<option
v-for="item in guardianTypes"
:key="item"
:value="item"
>
{{ item }}
</option>

</select>

<div class="text-danger small">
{{ errors.guardian_type?.[0] }}
</div>

</div>

<!-- First Name -->

<div class="col-md-4 mb-3">

<label class="form-label">
First Name
</label>

<input
class="form-control"
v-model="form.first_name"
/>

<div class="text-danger small">
{{ errors.first_name?.[0] }}
</div>

</div>

<!-- Last Name -->

<div class="col-md-4 mb-3">

<label class="form-label">
Last Name
</label>

<input
class="form-control"
v-model="form.last_name"
/>

<div class="text-danger small">
{{ errors.last_name?.[0] }}
</div>

</div>

<!-- Gender -->

<div class="col-md-4 mb-3">

<label class="form-label">
Gender
</label>

<select
class="form-select"
v-model="form.gender"
>

<option value="">
Select Gender
</option>

<option
v-for="item in genders"
:key="item"
:value="item"
>
{{ item }}
</option>

</select>

</div>

<!-- DOB -->

<div class="col-md-4 mb-3">

<label class="form-label">
Date of Birth
</label>

<input
type="date"
class="form-control"
v-model="form.dob"
/>

</div>

<!-- Mobile -->

<div class="col-md-4 mb-3">

<label class="form-label">
Mobile
</label>

<input
class="form-control"
v-model="form.mobile"
/>

<div class="text-danger small">
{{ errors.mobile?.[0] }}
</div>

</div>

<!-- Alternate Mobile -->

<div class="col-md-4 mb-3">

<label class="form-label">
Alternate Mobile
</label>

<input
class="form-control"
v-model="form.alternate_mobile"
/>

</div>

<!-- Email -->

<div class="col-md-4 mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
class="form-control"
v-model="form.email"
/>

</div>

<!-- Occupation -->

<div class="col-md-4 mb-3">

<label class="form-label">
Occupation
</label>

<input
class="form-control"
v-model="form.occupation"
/>

</div>

<!-- Annual Income -->

<div class="col-md-4 mb-3">

<label class="form-label">
Annual Income
</label>

<input
type="number"
class="form-control"
v-model="form.annual_income"
/>

</div>

<!-- Education -->

<div class="col-md-4 mb-3">

<label class="form-label">
Education
</label>

<input
class="form-control"
v-model="form.education"
/>

</div>

<!-- Aadhaar -->

<div class="col-md-4 mb-3">

<label class="form-label">
Aadhaar No
</label>

<input
class="form-control"
v-model="form.aadhaar_no"
/>

</div>

<!-- PAN -->

<div class="col-md-4 mb-3">

<label class="form-label">
PAN No
</label>

<input
class="form-control"
v-model="form.pan_no"
/>

</div>

<!-- Address -->

<div class="col-md-12 mb-3">

<label class="form-label">
Address
</label>

<textarea
rows="3"
class="form-control"
v-model="form.address"
></textarea>

</div>

<!-- City -->

<div class="col-md-3 mb-3">

<label class="form-label">
City
</label>

<input
class="form-control"
v-model="form.city"
/>

</div>

<!-- State -->

<div class="col-md-3 mb-3">

<label class="form-label">
State
</label>

<input
class="form-control"
v-model="form.state"
/>

</div>

<!-- Country -->

<div class="col-md-3 mb-3">

<label class="form-label">
Country
</label>

<input
class="form-control"
v-model="form.country"
/>

</div>

<!-- Pincode -->

<div class="col-md-3 mb-3">

<label class="form-label">
Pincode
</label>

<input
class="form-control"
v-model="form.pincode"
/>

</div>

<!-- Students -->

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">
    <i class="bi bi-people text-primary me-1"></i> Link Students
</label>

<select
multiple
class="form-select border-primary"
v-model="form.student_ids"
style="height:150px"
>

<option
v-for="student in students"
:key="student.id"
:value="student.id"
>
{{ student.full_name }} (Adm: {{ student.admission_no || student.id }})
</option>

</select>
<small class="text-muted d-block mt-1">Hold Ctrl (or Cmd) to select multiple students.</small>

<div class="text-danger small" v-if="errors.student_ids">
    {{ errors.student_ids[0] }}
</div>

</div>

<!-- Primary Student -->

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">
    <i class="bi bi-star-fill text-warning me-1"></i> Primary Student
</label>

<select
class="form-select"
v-model="form.primary_student_id"
>

<option value="">
Select Primary Student
</option>

<option
v-for="student in students"
:key="student.id"
:value="student.id"
>
{{ student.full_name }} (Adm: {{ student.admission_no || student.id }})
</option>

</select>
<small class="text-muted d-block mt-1">Designate which child is the primary student for this guardian.</small>

<div class="text-danger small" v-if="errors.primary_student_id">
    {{ errors.primary_student_id[0] }}
</div>

</div>

<!-- Photo -->

<div class="col-md-6 mb-3">

    <label class="form-label">
        Photo
    </label>

    <input
        type="file"
        class="form-control"
        @change="form.photo = $event.target.files[0]"
    />

    <div
        v-if="form.photo_url"
        class="mt-2"
    >

        <img
            :src="form.photo_url"
            class="img-thumbnail"
            style="max-width:120px;"
        />

    </div>

    <div class="text-danger small">

        {{ errors.photo?.[0] }}

    </div>

</div>
<!-- Status -->

<div class="col-md-6 mb-3">

<label class="form-label">
Status
</label>

<select
class="form-select"
v-model="form.status"
>

<option :value="1">
Active
</option>

<option :value="0">
Inactive
</option>

</select>

</div>

</div>

</div>

</div>

<div class="text-end">

<button
type="submit"
class="btn btn-primary"
:disabled="loading"
>

{{ loading ? "Saving..." : "Save Guardian" }}

</button>

<button
type="button"
class="btn btn-secondary ms-2"
@click="$emit('cancel')"
>

Cancel

</button>

</div>

</form>

</template>