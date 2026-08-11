<script setup>

import { reactive } from "vue";
import { useRouter } from "vue-router";

import { useResultGradeStore } from "@/stores/resultGrade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

import Form from "./Form.vue";

const router = useRouter();
const store = useResultGradeStore();

const form = reactive({
    name: "",
    min_percentage: "",
    max_percentage: "",
    grade_point: "",
    is_pass: true,
    description: "",
    status: true,
});

async function save() {

    try {

        await store.create(form);

        router.push({
            name: "result-grades.index",
        });

    } catch (error) {

        console.error(error);

    }
}

</script>

<template>

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            {
                label: 'Result Grades',
                to: { name: 'result-grades.index' }
            },
            { label: 'Create' }
        ]"
    />

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Create Result Grade
            </h4>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
            />

            <div class="text-end mt-4">

                <AppButton
                    text="Save"
                    :loading="store.saving"
                    @click="save"
                />

            </div>

        </div>

    </div>

</template>