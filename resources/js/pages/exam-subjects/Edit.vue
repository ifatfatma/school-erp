<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";

import Form from "./Form.vue";
import { useExamSubjectStore } from "@/stores/examSubject";

const route = useRoute();
const router = useRouter();
const toast = useToast();

const store = useExamSubjectStore();

const form = reactive({

    id: null,

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

onMounted(async () => {

    await loadData();

});

async function loadData()
{
    try {

        const item = await store.find(route.params.id);

        Object.assign(form, {

            id: item.id,

            exam_id: item.exam_id,

            class_id: item.class_id,

            subject_id: item.subject_id,

            full_marks: item.full_marks,

            pass_marks: item.pass_marks,

            theory_marks: item.theory_marks,

            practical_marks: item.practical_marks,

            viva_marks: item.viva_marks,

            project_marks: item.project_marks,

            display_order: item.display_order,

            status: item.status

        });

    } catch (e) {

        toast.error("Unable to load record.");

        router.push({
            name: "exam-subjects.index"
        });

    }
}

async function submit()
{
    try {

        await store.update(form.id, form);

        toast.success("Exam Subject updated successfully.");

        router.push({
            name: "exam-subjects.index"
        });

    } catch (e) {

        console.error(e);

    }
}
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5>Edit Exam Subject</h5>

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

                :loading="store.loading"

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

                Update

            </button>

        </div>

    </div>

</div>

</template>