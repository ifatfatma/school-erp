<script setup>
import { computed } from "vue";

const props = defineProps({

    form: {
        type: Object,
        required: true,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },

    loading: {
        type: Boolean,
        default: false,
    },

});

defineEmits([
    "submit",
    "cancel",
]);

const title = computed(() =>
    props.form.id
        ? "Edit Exam Type"
        : "Create Exam Type"
);
</script>

<template>

<form @submit.prevent="$emit('submit')">

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h5 class="mb-0">

{{ title }}

</h5>

</div>

<div class="card-body">

<div class="row">

<!-- Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Type Name <span class="text-danger">*</span>

</label>

<input
type="text"
class="form-control"
v-model="form.name"
placeholder="Enter Exam Type"
/>

<div
class="text-danger mt-1"
v-if="errors.name"
>
{{ errors.name[0] }}
</div>

</div>

<!-- Code -->

<div class="col-md-6 mb-3">

<label class="form-label">

Code

</label>

<input
type="text"
class="form-control"
v-model="form.code"
placeholder="UT1 / HY / AN"
/>

<div
class="text-danger mt-1"
v-if="errors.code"
>
{{ errors.code[0] }}
</div>

</div>

<!-- Description -->

<div class="col-md-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
rows="4"
class="form-control"
v-model="form.description"
placeholder="Optional description"
/>

<div
class="text-danger mt-1"
v-if="errors.description"
>
{{ errors.description[0] }}
</div>

</div>

<!-- Status -->

<div class="col-md-4 mb-3">

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

<div
class="text-danger mt-1"
v-if="errors.status"
>
{{ errors.status[0] }}
</div>

</div>

</div>

</div>

<div class="card-footer text-end">

<button
type="button"
class="btn btn-secondary me-2"
@click="$emit('cancel')"
>

Cancel

</button>

<button
type="submit"
class="btn btn-primary"
:disabled="loading"
>

{{ loading ? "Saving..." : "Save Exam Type" }}

</button>

</div>

</div>

</form>

</template>