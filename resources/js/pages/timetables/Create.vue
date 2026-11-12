<script setup>
import { reactive } from "vue";
import { useRouter, RouterLink } from "vue-router";
import Swal from "sweetalert2";

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

        await Swal.fire({
            icon: "success",
            title: "Success",
            text: "Timetable period created successfully."
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
                    text="Save Timetable"
                    :loading="store.saving"
                    @click="save"
                />

            </div>

        </div>

    </div>

</div>

</template>