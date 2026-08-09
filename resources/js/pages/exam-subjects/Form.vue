<script setup>
import { computed, onMounted } from "vue";

import { useAcademicSessionStore } from "@/stores/academicSession";
import { useExamStore } from "@/stores/exam";
import { useClassStore } from "@/stores/class";
import { useSubjectStore } from "@/stores/subject";

const props = defineProps({
    form: Object,
    errors: {
        type: Object,
        default: () => ({})
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const sessionStore = useAcademicSessionStore();
const examStore = useExamStore();
const classStore = useClassStore();
const subjectStore = useSubjectStore();

onMounted(async () => {

    await Promise.all([
        sessionStore.active(),
        examStore.active(),
        classStore.active(),
        subjectStore.active()
    ]);

});

const componentTotal = computed(() => {

    return Number(props.form.theory_marks || 0)
        + Number(props.form.practical_marks || 0)
        + Number(props.form.viva_marks || 0)
        + Number(props.form.project_marks || 0);

});

const isValid = computed(() => {

    return componentTotal.value ==
        Number(props.form.full_marks || 0);

});
</script>

<template>

<div class="row">

    <!-- Exam -->

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Exam <span class="text-danger">*</span>
        </label>

        <select
            class="form-select"
            v-model="form.exam_id">

            <option value="">Select Exam</option>

            <option
                v-for="item in examStore.items"
                :key="item.id"
                :value="item.id">

                {{ item.name }}

            </option>

        </select>

        <div class="text-danger small">
            {{ errors.exam_id?.[0] }}
        </div>

    </div>

    <!-- Class -->

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Class <span class="text-danger">*</span>
        </label>

        <select
            class="form-select"
            v-model="form.class_id">

            <option value="">Select Class</option>

            <option
                v-for="item in classStore.items"
                :key="item.id"
                :value="item.id">

                {{ item.name }}

            </option>

        </select>

        <div class="text-danger small">
            {{ errors.class_id?.[0] }}
        </div>

    </div>

    <!-- Subject -->

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Subject <span class="text-danger">*</span>
        </label>

        <select
            class="form-select"
            v-model="form.subject_id">

            <option value="">Select Subject</option>

            <option
                v-for="item in subjectStore.items"
                :key="item.id"
                :value="item.id">

                {{ item.name }}

            </option>

        </select>

        <div class="text-danger small">
            {{ errors.subject_id?.[0] }}
        </div>

    </div>

    <!-- Full Marks -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Full Marks</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.full_marks">

    </div>

    <!-- Pass Marks -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Pass Marks</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.pass_marks">

    </div>

    <!-- Theory -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Theory</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.theory_marks">

    </div>

    <!-- Practical -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Practical</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.practical_marks">

    </div>

    <!-- Viva -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Viva</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.viva_marks">

    </div>

    <!-- Project -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Project</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.project_marks">

    </div>

    <!-- Display Order -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Display Order</label>

        <input
            type="number"
            class="form-control"
            v-model.number="form.display_order">

    </div>

    <!-- Status -->

    <div class="col-md-3 mb-3">

        <label class="form-label">Status</label>

        <select
            class="form-select"
            v-model="form.status">

            <option :value="true">Active</option>

            <option :value="false">Inactive</option>

        </select>

    </div>

</div>

<div class="alert"
     :class="isValid ? 'alert-success' : 'alert-danger'">

    <strong>Component Total:</strong>

    {{ componentTotal }}

    /

    {{ form.full_marks }}

    <span v-if="isValid">

        ✔ Configuration Valid

    </span>

    <span v-else>

        ✘ Component Total must equal Full Marks

    </span>

</div>

</template>