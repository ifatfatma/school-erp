<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

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

        router.push({
            name: "exams.index"
        });

    } catch (e) {

        console.error(e);

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