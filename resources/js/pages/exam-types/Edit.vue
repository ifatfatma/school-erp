<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useExamTypeStore } from "@/stores/examType";

const route = useRoute();
const router = useRouter();

const examTypeStore = useExamTypeStore();

const form = reactive({

    id: null,

    name: "",

    code: "",

    description: "",

    status: 1,

});

onMounted(async () => {

    const item = await examTypeStore.find(route.params.id);

    Object.assign(form, item);

});

async function update() {

    try {

        await examTypeStore.update(form.id, form);

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

<div class="container-fluid py-2">

<AppBreadcrumb
    :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Exam Types', to: { name: 'exam-types.index' } },
        { label: 'Edit' }
    ]"
/>

<Form

    :form="form"

    :errors="examTypeStore.errors"

    :loading="examTypeStore.saving"

    @submit="update"

    @cancel="cancel"

/>

</div>

</template>