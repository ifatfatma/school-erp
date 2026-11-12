<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";
import Swal from "sweetalert2";

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

    if (item) {
        form.class_id = item.class_id;
        form.section_id = item.section_id;
        form.subject_id = item.subject_id;
        form.teacher_id = item.teacher_id;
        form.day = item.day;
        form.start_time = item.start_time ? item.start_time.substring(0, 5) : "";
        form.end_time = item.end_time ? item.end_time.substring(0, 5) : "";
        form.room = item.room || "";
        form.status = !!item.status;
    }

}

async function update() {

    try {

        await store.update(route.params.id, form);

        await Swal.fire({
            icon: "success",
            title: "Success",
            text: "Timetable period updated successfully."
        });

        router.push({
            name: "timetables.index",
        });

    } catch (e) {
        if (e.response?.data?.errors) {
            const firstErr = Object.values(e.response.data.errors)[0]?.[0];
            if (firstErr) {
                Swal.fire("Validation Error", firstErr, "warning");
            }
        }
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

        <div class="card-header d-flex justify-content-between align-items-center">

            <h4 class="mb-0">Edit Timetable</h4>

            <RouterLink
                :to="{ name: 'timetables.index' }"
                class="btn btn-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i> Back
            </RouterLink>

        </div>

        <div class="card-body">

            <Form
                :form="form"
                :errors="store.errors"
            />

            <div class="text-end mt-4">

                <RouterLink
                    :to="{ name: 'timetables.index' }"
                    class="btn btn-secondary me-2"
                >
                    Cancel
                </RouterLink>

                <AppButton
                    text="Update Timetable"
                    :loading="store.saving"
                    @click="update"
                />

            </div>

        </div>

    </div>

</div>

</template>