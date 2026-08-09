<script setup>
import { computed, onMounted } from "vue";

import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamTypeStore } from "@/stores/examType";

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

const emit = defineEmits([
    "submit",
    "cancel",
]);

const sessionStore = useAcademicSessionStore();
const examTypeStore = useExamTypeStore();

const title = computed(() =>
    props.form.id
        ? "Edit Exam"
        : "Create Exam"
);

onMounted(async () => {

    await Promise.all([

        sessionStore.load({
            per_page: 1000,
        }),

        examTypeStore.fetch({
            per_page: 1000,
            status: 1,
        }),

    ]);

});
</script>

<template>

<form @submit.prevent="emit('submit')">

<div class="card shadow-sm">

<div class="card-header">

<h5 class="mb-0">

{{ title }}

</h5>

</div>

<div class="card-body">

<div class="row">

<!-- Academic Session -->

<div class="col-md-6 mb-3">

<label class="form-label">

Academic Session <span class="text-danger">*</span>

</label>

<select
class="form-select"
v-model="form.academic_session_id"
>

<option value="">

Select Academic Session

</option>

<option
v-for="session in sessionStore.sessions"
:key="session.id"
:value="session.id"
>

{{ session.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.academic_session_id"
>

{{ errors.academic_session_id[0] }}

</div>

</div>

<!-- Exam Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Type <span class="text-danger">*</span>

</label>

<select
class="form-select"
v-model="form.exam_type_id"
>

<option value="">

Select Exam Type

</option>

<option
v-for="item in examTypeStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.exam_type_id"
>

{{ errors.exam_type_id[0] }}

</div>

</div>

<!-- Exam Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Name

</label>

<input
type="text"
class="form-control"
v-model="form.name"
/>

<div
class="text-danger"
v-if="errors.name"
>

{{ errors.name[0] }}

</div>

</div>

<!-- Exam Code -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Code

</label>

<input
type="text"
class="form-control"
v-model="form.code"
/>

<div
class="text-danger"
v-if="errors.code"
>

{{ errors.code[0] }}

</div>

</div>

<!-- Start Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

Start Date

</label>

<input
type="date"
class="form-control"
v-model="form.start_date"
/>

<div
class="text-danger"
v-if="errors.start_date"
>

{{ errors.start_date[0] }}

</div>

</div>

<!-- End Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

End Date

</label>

<input
type="date"
class="form-control"
v-model="form.end_date"
/>

<div
class="text-danger"
v-if="errors.end_date"
>

{{ errors.end_date[0] }}

</div>

</div>

<!-- Description -->

<div class="col-md-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
rows="3"
class="form-control"
v-model="form.description"
></textarea>

</div>

<!-- Status -->

<div class="col-md-4 mb-3">

<div class="form-check mt-4">

<input
class="form-check-input"
type="checkbox"
v-model="form.status"
/>

<label class="form-check-label">

Active

</label>

</div>

</div>

<!-- Marks Locked -->

<div class="col-md-4 mb-3">

<div class="form-check mt-4">

<input
class="form-check-input"
type="checkbox"
v-model="form.is_marks_locked"
/>

<label class="form-check-label">

Lock Marks

</label>

</div>

</div>

<!-- Result Published -->

<div class="col-md-4 mb-3">

<div class="form-check mt-4">

<input
class="form-check-input"
type="checkbox"
v-model="form.is_result_published"
/>

<label class="form-check-label">

Publish Result

</label>

</div>

</div>

</div>

</div>

<div class="card-footer text-end">

<button
type="button"
class="btn btn-secondary me-2"
@click="emit('cancel')"
>

Cancel

</button>

<button
type="submit"
class="btn btn-primary"
:disabled="loading"
>

{{ loading ? 'Saving...' : 'Save Exam' }}

</button>

</div>

</div>

</form>

</template>