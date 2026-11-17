<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import Swal from "sweetalert2";

import Form from "./Form.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useExamStore } from "@/stores/exam";

const route = useRoute();
const router = useRouter();

const examStore = useExamStore();

const form = reactive({

    id: null,

    academic_session_id: "",

    exam_type_id: "",

    name: "",

    code: "",

    description: "",

    start_date: "",

    end_date: "",

    status: true,

    is_marks_locked: false,

    is_result_published: false,

});

onMounted(async () => {

    const exam = await examStore.find(route.params.id);

    Object.assign(form, exam);

});

async function update() {
    try {
        await examStore.update(form.id, form);
        await Swal.fire({
            icon: "success",
            title: "Success",
            text: "Exam updated successfully.",
            timer: 1500,
            showConfirmButton: false
        });
        router.push({
            name: "exams.index"
        });
    } catch (e) {
        console.error(e);
        Swal.fire({
            icon: "error",
            title: "Update Failed",
            text: e.response?.data?.message || (e.response?.data?.errors?.exam ? e.response.data.errors.exam[0] : "Failed to update exam.")
        });
    }
}

function cancel() {

    router.push({
        name: "exams.index"
    });

}
</script>

<template>

<div class="container-fluid py-2">

<AppBreadcrumb
    :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Examinations', to: { name: 'exams.index' } },
        { label: 'Edit' }
    ]"
/>

<Form

    :form="form"

    :errors="examStore.errors"

    :loading="examStore.saving"

    @submit="update"

    @cancel="cancel"

/>

</div>

</template>