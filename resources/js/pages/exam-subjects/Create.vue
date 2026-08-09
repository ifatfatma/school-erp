<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

import Form from "./Form.vue";
import { useExamSubjectStore } from "@/stores/examSubject";

const router = useRouter();
const toast = useToast();

const store = useExamSubjectStore();

const form = reactive({

    exam_id: "",

    class_id: "",

    subject_id: "",

    full_marks: 100,

    pass_marks: 40,

    theory_marks: 100,

    practical_marks: 0,

    viva_marks: 0,

    project_marks: 0,

    display_order: 1,

    status: true

});

async function submit()
{
    try {

        await store.create(form);

        toast.success("Exam Subject created successfully.");

        router.push({
            name: "exam-subjects.index"
        });

    } catch (e) {
        console.error(e);
    }
}

function resetForm()
{
    Object.assign(form, {

        exam_id: "",

        class_id: "",

        subject_id: "",

        full_marks: 100,

        pass_marks: 40,

        theory_marks: 100,

        practical_marks: 0,

        viva_marks: 0,

        project_marks: 0,

        display_order: 1,

        status: true

    });

    store.errors = {};
}
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <h5>Create Exam Subject</h5>

            <RouterLink
                :to="{ name:'exam-subjects.index' }"
                class="btn btn-secondary">

                Back

            </RouterLink>

        </div>

        <div class="card-body">

            <Form

                :form="form"

                :errors="store.errors"

                :loading="store.saving"

            />

        </div>

        <div class="card-footer">

            <button
                class="btn btn-primary"
                @click="submit"
                :disabled="store.saving">

                <span
                    v-if="store.saving"
                    class="spinner-border spinner-border-sm me-2">
                </span>

                Save

            </button>

            <button
                class="btn btn-warning ms-2"
                @click="resetForm">

                Reset

            </button>

        </div>

    </div>

</div>

</template>