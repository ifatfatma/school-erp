<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";
import { useExamTypeStore } from "@/stores/examType";

const router = useRouter();
const examTypeStore = useExamTypeStore();

const form = reactive({

    name: "",

    code: "",

    description: "",

    status: 1,

});

async function save() {

    try {

        await examTypeStore.create(form);

        router.push({
            name: "exam-types.index",
        });

    } catch (e) {

        console.error(e);

    }

}

function cancel() {

    router.push({
        name: "exam-types.index",
    });

}
</script>

<template>

<Form

    :form="form"

    :errors="examTypeStore.errors"

    :loading="examTypeStore.saving"

    @submit="save"

    @cancel="cancel"

/>

</template>