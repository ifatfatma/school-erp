<script setup>
import { computed , watch } from "vue";

const props = defineProps({

    form: {
        type: Object,
        required: true,
    },
    academic_session: {
        type: Array,
        default: () => [],
    },
    classes: {
        type: Array,
        default: () => [],
    },

    sections: {
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

const filteredSections = computed(() => {

    if (!props.form.class_id) {
        return [];
    }

    return props.sections.filter(
        section => Number(section.class_id) === Number(props.form.class_id)
    );

});

const emit = defineEmits([
    "submit",
    "cancel",
]);

const genders = computed(() => [
    "Male",
    "Female",
    "Other",
]);

watch(
    () => props.form.class_id,
    (newClassId, oldClassId) => {

        if (
            oldClassId !== undefined &&
            Number(newClassId) !== Number(oldClassId)
        ) {
            props.form.section_id = "";
        }

    }
);
</script>

<template>

<form @submit.prevent="$emit('submit')">
<div
    v-if="Object.keys(errors).length"
    class="alert alert-danger mb-3"
>
    <strong>Please fix the following errors:</strong>

    <ul class="mb-0">
        <li
            v-for="(messages, field) in errors"
            :key="field"
        >
            {{ messages[0] }}
        </li>
    </ul>
</div>
<div class="card">

<div class="card-header">
    <h5>Student Information</h5>
</div>

<div class="card-body">

<div class="row g-2">

<div class="col-md-4 mb-3">
<label class="form-label">Admission No</label>

<input
    class="form-control"
    v-model="form.admission_no"
/>

<div class="text-danger" v-if="errors.admission_no">
{{ errors.admission_no[0] }}
</div>

</div>

<div class="col-md-4 mb-3">
<label class="form-label">Roll No</label>

<input
    class="form-control"
    v-model="form.roll_no"
/>
<div class="text-danger" v-if="errors.roll_no">
{{ errors.roll_no[0] }}
</div>

</div>

<div class="col-md-4 mb-3">
<label class="form-label">Admission Date</label>

<input
    type="date"
    class="form-control"
    v-model="form.admission_date"
/>

<div class="text-danger" v-if="errors.admission_date">
{{ errors.admission_date[0] }}
</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
First Name
</label>

<input
class="form-control"
v-model="form.first_name"
/>

<div class="text-danger" v-if="errors.first_name">
{{ errors.first_name[0] }}
</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Last Name
</label>

<input
class="form-control"
v-model="form.last_name"
/>
<div class="text-danger" v-if="errors.last_name">
{{ errors.last_name[0] }}
</div>
</div>

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
v-for="gender in genders"
:key="gender"
:value="gender"
>
{{ gender }}
</option>

</select>

<div class="text-danger" v-if="errors.gender">
{{ errors.gender[0] }}
</div>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Date Of Birth
</label>

<input
type="date"
class="form-control"
v-model="form.dob"
/>

<div class="text-danger" v-if="errors.dob">
{{ errors.dob[0] }}
</div>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Blood Group
</label>

<input
class="form-control"
v-model="form.blood_group"
/>
<div class="text-danger" v-if="errors.blood_group">
{{ errors.blood_group[0] }}
</div>
</div>


<div class="col-md-3 mb-3">

<label class="form-label">
Academic Session
</label>

<select
class="form-select"
v-model="form.academic_session_id"
>

<option value="">
Select Session
</option>

<option
v-for="item in academic_session"
:key="item.id"
:value="item.id"
>
{{ item.name }}
</option>

</select>

<div class="text-danger" v-if="errors.academic_session_id">
{{ errors.academic_session_id[0] }}
</div>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">
Class
</label>

<select
class="form-select"
v-model="form.class_id"
>

<option value="">
Select Class
</option>

<option
v-for="item in classes"
:key="item.id"
:value="item.id"
>
{{ item.name }}
</option>

</select>

<div class="text-danger" v-if="errors.class_id">
{{ errors.class_id[0] }}
</div>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">
Section
</label>

<select
class="form-select"
v-model="form.section_id"
>

<option value="">
Select Section
</option>

<option
    v-for="item in filteredSections"
    :key="item.id"
    :value="item.id"
>
    {{ item.name }}
</option>

</select>

<div class="text-danger" v-if="errors.section_id">
{{ errors.section_id[0] }}
</div>

</div>

<div class="col-md-3 mb-3">

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

<div class="card mt-3">

<div class="card-header">
<h5>Contact Information</h5>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">
Mobile
</label>

<input
class="form-control"
v-model="form.mobile"
/>
<div class="text-danger" v-if="errors.mobile">
{{ errors.mobile[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Email
</label>

<input
type="email"
class="form-control"
v-model="form.email"
/>
<div class="text-danger" v-if="errors.email">
{{ errors.email[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Aadhaar
</label>

<input
class="form-control"
v-model="form.aadhaar_no"
/>
<div class="text-danger" v-if="errors.aadhaar_no">
{{ errors.aadhaar_no[0] }}
</div>
</div>

<div class="col-12">

<label class="form-label">
Address
</label>

<textarea
rows="3"
class="form-control"
v-model="form.address"
/>
<div class="text-danger" v-if="errors.address">
{{ errors.address[0] }}
</div>
</div>

</div>

</div>

</div>

<div class="card mt-3">

<div class="card-header">
<h5>Parent Information</h5>
</div>

<div class="card-body">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">
Father Name
</label>

<input
class="form-control"
v-model="form.father_name"
/>
<div class="text-danger" v-if="errors.father_name">
{{ errors.father_name[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Father Mobile
</label>

<input
class="form-control"
v-model="form.father_mobile"
/>
<div class="text-danger" v-if="errors.father_mobile">
{{ errors.father_mobile[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Father Occupation
</label>

<input
class="form-control"
v-model="form.father_occupation"
/>
<div class="text-danger" v-if="errors.father_occupation">
{{ errors.father_occupation[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Mother Name
</label>

<input
class="form-control"
v-model="form.mother_name"
/>
<div class="text-danger" v-if="errors.mother_name">
{{ errors.mother_name[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Mother Mobile
</label>

<input
class="form-control"
v-model="form.mother_mobile"
/>
<div class="text-danger" v-if="errors.mother_mobile">
{{ errors.mother_mobile[0] }}
</div>
</div>

<div class="col-md-4 mb-3">

<label class="form-label">
Photo
</label>

<input
type="file"
class="form-control"
@change="form.photo = $event.target.files[0]"
/>
<div class="text-danger" v-if="errors.photo">
{{ errors.photo[0] }}
</div>
</div>

</div>

</div>

</div>

<div class="mt-4">

<button
class="btn btn-primary"
type="submit"
:disabled="loading"
>
{{ loading ? "Saving..." : "Save Student" }}
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