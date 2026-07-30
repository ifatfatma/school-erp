<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";

import AppButton from "@/components/ui/AppButton.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useTimetableStore } from "@/stores/timetable";

const router = useRouter();
const route = useRoute();

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

async function load() {

    const item = await store.find(route.params.id);

    Object.assign(form, item);

}

async function update() {

    try {

        await store.update(route.params.id, form);

        router.push({
            name: "timetables.index",
        });

    } catch (e) {

    }

}

onMounted(load);

</script>

<template>

<div class="container-fluid">

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Timetables', to: { name: 'timetables.index' } },
            { label: 'Edit' }
        ]"
    />

    <div class="card">

        <div class="card-header">

            <h4>Edit Timetable</h4>

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
                    @click="update"
                />

            </div>

        </div>

    </div>

</div>

</template>