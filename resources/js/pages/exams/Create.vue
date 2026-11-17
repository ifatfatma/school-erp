<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

import Form from "./Form.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useExamStore } from "@/stores/exam";

const router = useRouter();
const examStore = useExamStore();

const form = reactive({

    school_id: "",

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

async function save() {
    try {
        await examStore.create(form);
        await Swal.fire({
            icon: "success",
            title: "Success",
            text: "Exam created successfully.",
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
            title: "Creation Failed",
            text: e.response?.data?.message || (e.response?.data?.errors?.exam ? e.response.data.errors.exam[0] : "Failed to create exam.")
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
        { label: 'Create' }
    ]"
/>

<Form

    :form="form"

    :errors="examStore.errors"

    :loading="examStore.saving"

    @submit="save"

    @cancel="cancel"

/>

</div>

</template>