<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import { useGradeStore } from "@/stores/grade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

import Form from "./Form.vue";

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

async function save() {

    try {

        await store.create(form);

        router.push({
            name: "grades.index",
        });

    } catch (error) {

        console.error(
            "Failed to create grade:",
            error
        );

    }
}

function cancel() {

    router.push({
        name: "grades.index",
    });

}
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
                label: 'Create'
            }
        ]"
    />

    <div class="card">

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Create Grade
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
                    text="Save"
                    :loading="store.saving"
                    @click="save"
                />

            </div>

        </div>

    </div>

</template>