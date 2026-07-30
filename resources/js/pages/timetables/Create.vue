<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";

import AppButton from "@/components/ui/AppButton.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useTimetableStore } from "@/stores/timetable";

const router = useRouter();
const store = useTimetableStore();

const form = reactive({

    class_id: "",

    section_id: "",

    subject_id: "",

    teacher_id: "",

    day: "",

    start_time: "",

    end_time: "",

    room: "",

    status: true,

});

async function save() {

    try {

        await store.create(form);

        router.push({
            name: "timetables.index",
        });

    } catch (e) {
        // Validation errors handled by store
    }

}
</script>

<template>

<div class="container-fluid">

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Timetables', to: { name: 'timetables.index' } },
            { label: 'Create' }
        ]"
    />

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Create Timetable
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

</div>

</template>