<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";
import AppButton from "@/components/ui/AppButton.vue";
import { useSectionStore } from "@/stores/section";

const route = useRoute();
const router = useRouter();
const store = useSectionStore();

const form = reactive({

    class_id: "",

    name: "",

    capacity: 0,

    status: true,

});

async function load() {

    const item = await store.find(route.params.id);

    Object.assign(form, item);

}

async function update() {

    await store.update(
        route.params.id,
        form
    );

    router.push({
        name: "sections.index",
    });

}

onMounted(load);

</script>

<template>

    <Form
        :form="form"
        :errors="store.errors"
    />

    <div class="mt-3 text-end">
        <AppButton
            text="Update"
            :loading="store.saving"
            @click="update"
        />
    </div>

</template>