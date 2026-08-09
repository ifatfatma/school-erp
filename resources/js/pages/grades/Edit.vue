<script setup>
import { onMounted, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useGradeStore } from "@/stores/grade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

import Form from "./Form.vue";

const route = useRoute();
const router = useRouter();

const store = useGradeStore();

const form = reactive({
    name: "",
    min_percentage: "",
    max_percentage: "",
    grade_point: "",
    is_pass: true,
    description: "",
    status: true,
});

async function loadGrade() {

    try {

        const data = await store.find(route.params.id);

        Object.assign(form, data);

    } catch (error) {

        console.error(
            "Failed to load grade:",
            error
        );

        router.push({
            name: "grades.index",
        });
    }
}

async function update() {

    try {

        await store.update(
            route.params.id,
            form
        );

        router.push({
            name: "grades.index",
        });

    } catch (error) {

        console.error(
            "Failed to update grade:",
            error
        );
    }
}

function cancel() {

    router.push({
        name: "grades.index",
    });

}

onMounted(() => {
    loadGrade();
});
</script>

<template>

    <AppBreadcrumb
        :items="[
            {
                label: 'Dashboard',
                to: '/'
            },
            {
                label: 'Examination'
            },
            {
                label: 'Grade System',
                to: {
                    name: 'grades.index'
                }
            },
            {
                label: 'Edit'
            }
        ]"
    />

    <div class="card">

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Edit Grade
            </h4>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
            />

            <div class="d-flex justify-content-end gap-2 mt-4">

                <button
                    type="button"
                    class="btn btn-secondary"
                    @click="cancel"
                >
                    Cancel
                </button>

                <AppButton
                    text="Update"
                    :loading="store.saving"
                    @click="update"
                />

            </div>

        </div>

    </div>

</template>