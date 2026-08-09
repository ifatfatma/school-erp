<script setup>
import { computed, onMounted, watch } from "vue";

import { useExamStore } from "@/stores/exam";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useSubjectStore } from "@/stores/subject";
import { useTeacherStore } from "@/stores/teacher";

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
    }

});

const emit = defineEmits([
    "submit",
    "cancel"
]);

const examStore = useExamStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const subjectStore = useSubjectStore();
const teacherStore = useTeacherStore();

const title = computed(() =>
    props.form.id
        ? "Edit Exam Schedule"
        : "Create Exam Schedule"
);

onMounted(async () => {

    await Promise.all([

        examStore.active(),

        classStore.active(),

        subjectStore.active(),

        teacherStore.active()

    ]);

});

watch(
    () => props.form.class_id,
    async (classId, oldClassId) => {

        if (!classId) {
            sectionStore.items = [];
            return;
        }

        await sectionStore.load({
            class_id: classId,
            status: 1,
            per_page: 1000
        });

        // Clear only if the user changed the class
        if (oldClassId && oldClassId !== classId) {
            props.form.section_id = "";
        }

    },
    {
        immediate: true
    }
);
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

<!-- Exam -->

<div class="col-md-6 mb-3">

<label class="form-label">Exam *</label>

<select
class="form-select"
v-model="form.exam_id"
>

<option value="">Select Exam</option>

<option
v-for="item in examStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.exam_id"
>

{{ errors.exam_id[0] }}

</div>

</div>

<!-- Class -->

<div class="col-md-6 mb-3">

<label class="form-label">Class *</label>

<select
class="form-select"
v-model="form.class_id"
>

<option value="">Select Class</option>

<option
v-for="item in classStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.class_id"
>

{{ errors.class_id[0] }}

</div>

</div>

<!-- Section -->

<div class="col-md-6 mb-3">

<label class="form-label">Section *</label>

<select
class="form-select"
v-model="form.section_id"
>

<option value="">Select Section</option>

<option
v-for="item in sectionStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.section_id"
>

{{ errors.section_id[0] }}

</div>

</div>

<!-- Subject -->

<div class="col-md-6 mb-3">

<label class="form-label">Subject *</label>

<select
class="form-select"
v-model="form.subject_id"
>

<option value="">Select Subject</option>

<option
v-for="item in subjectStore.items"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

<div
class="text-danger"
v-if="errors.subject_id"
>

{{ errors.subject_id[0] }}

</div>

</div>

<!-- Teacher -->

<div class="col-md-6 mb-3">

<label class="form-label">

Teacher

</label>

<select
class="form-select"
v-model="form.teacher_id"
>

<option value="">

Select Teacher

</option>

<option
v-for="item in teacherStore.items"
:key="item.id"
:value="item.id"
>

{{ item.first_name }} {{ item.last_name }}

</option>

</select>

</div>

<!-- Exam Date -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Date *

</label>

<input
type="date"
class="form-control"
v-model="form.exam_date"
/>

<div
class="text-danger"
v-if="errors.exam_date"
>

{{ errors.exam_date[0] }}

</div>

</div>

<!-- Start Time -->

<div class="col-md-4 mb-3">

<label class="form-label">

Start Time *

</label>

<input
type="time"
class="form-control"
v-model="form.start_time"
/>

<div
class="text-danger"
v-if="errors.start_time"
>

{{ errors.start_time[0] }}

</div>

</div>

<!-- End Time -->

<div class="col-md-4 mb-3">

<label class="form-label">

End Time *

</label>

<input
type="time"
class="form-control"
v-model="form.end_time"
/>

<div
class="text-danger"
v-if="errors.end_time"
>

{{ errors.end_time[0] }}

</div>

</div>

<!-- Room -->

<div class="col-md-4 mb-3">

<label class="form-label">

Room No.

</label>

<input
class="form-control"
v-model="form.room_no"
/>

</div>

<!-- Full Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Full Marks

</label>

<input
type="number"
class="form-control"
v-model="form.full_marks"
/>

<div
class="text-danger"
v-if="errors.full_marks"
>

{{ errors.full_marks[0] }}

</div>

</div>

<!-- Pass Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Pass Marks

</label>

<input
type="number"
class="form-control"
v-model="form.pass_marks"
/>

<div
class="text-danger"
v-if="errors.pass_marks"
>

{{ errors.pass_marks[0] }}

</div>

</div>

<!-- Instructions -->

<div class="col-md-12 mb-3">

<label class="form-label">

Instructions

</label>

<textarea
rows="3"
class="form-control"
v-model="form.instructions"
></textarea>

</div>

<!-- Status -->

<div class="col-md-12">

<div class="form-check">

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
class="btn btn-primary"
type="submit"
:disabled="loading"
>

{{ loading ? "Saving..." : "Save Schedule" }}

</button>

</div>

</div>

</form>

</template>