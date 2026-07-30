<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";
import AppButton from "@/components/ui/AppButton.vue";
import { useSectionStore } from "@/stores/section";
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const router = useRouter();
const store = useSectionStore();

const form = reactive({

    class_id: "",

    name: "",

    capacity: 0,

    status: true,

});

async function save() {

    await store.create(form);

    router.push({
        name: "sections.index",
    });

}
</script>

<template>
<div class="container-fluid">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Sections', to: { name: 'sections.index' } },
                { label: 'Create' }
            ]"
        />

        <div class="card">

            <div class="card-header">
                <h4 class="mb-0">Create Class</h4>
            </div>

            <div class="card-body">
    <Form
        :form="form"
        :errors="store.errors"
    />

    <div class="mt-3 text-end">
        <AppButton
            text="Save"
            :loading="store.saving"
            @click="save"
        />
    </div>

    </div>
    </div>
    </div>

</template>