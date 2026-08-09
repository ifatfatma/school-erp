<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";
import { useExamStore } from "@/stores/exam";

const router = useRouter();
const examStore = useExamStore();

const form = reactive({

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

<Form

    :form="form"

    :errors="examStore.errors"

    :loading="examStore.saving"

    @submit="save"

    @cancel="cancel"

/>

</template>