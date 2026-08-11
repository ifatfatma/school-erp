<script setup>

import { onMounted, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useResultGradeStore } from "@/stores/resultGrade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

import Form from "./Form.vue";

const route = useRoute();
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

async function load() {

    try {

        const data =
            await store.find(route.params.id);

        Object.assign(form, data);

    } catch (error) {

        console.error(error);

        router.push({
            name: "result-grades.index",
        });
    }
}

async function save() {

    try {

        await store.update(
            route.params.id,
            form
        );

        router.push({
            name: "result-grades.index",
        });

    } catch (error) {

        console.error(error);

    }
}

onMounted(load);

</script>

<template>

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            {
                label: 'Result Grades',
                to: { name: 'result-grades.index' }
            },
            { label: 'Edit' }
        ]"
    />

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Edit Result Grade
            </h4>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
            />

            <div class="text-end mt-4">

                <AppButton
                    text="Update"
                    :loading="store.saving"
                    @click="save"
                />

            </div>

        </div>

    </div>

</template>