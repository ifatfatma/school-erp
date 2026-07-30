<script setup>
import { ref, onMounted } from "vue";

import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

import classApi from "@/api/class";
import sectionApi from "@/api/section";
import subjectApi from "@/api/subject";
import teacherApi from "@/api/teacher";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const classOptions = ref([]);
const sectionOptions = ref([]);
const subjectOptions = ref([]);
const teacherOptions = ref([]);

const days = [
    { id: "Monday", name: "Monday" },
    { id: "Tuesday", name: "Tuesday" },
    { id: "Wednesday", name: "Wednesday" },
    { id: "Thursday", name: "Thursday" },
    { id: "Friday", name: "Friday" },
    { id: "Saturday", name: "Saturday" },
    { id: "Sunday", name: "Sunday" },
];

async function loadClasses() {
    const { data } = await classApi.getAll({
        per_page: 1000,
        status: 1,
    });

    classOptions.value = data.data;
}

async function loadSections() {
    const { data } = await sectionApi.getAll({
        per_page: 1000,
        status: 1,
    });

    sectionOptions.value = data.data;
}

async function loadSubjects() {
    const { data } = await subjectApi.getAll({
        per_page: 1000,
        status: 1,
    });

    subjectOptions.value = data.data;
}

async function loadTeachers() {
    const { data } = await teacherApi.getAll({
        per_page: 1000,
        status: 1,
    });

    teacherOptions.value = data.data;
}

onMounted(() => {
    loadClasses();
    loadSections();
    loadSubjects();
    loadTeachers();
});
</script>

<template>
    <div class="row">

        <!-- Class -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.class_id"
                label="Class"
                :options="classOptions"
                option-label="name"
                option-value="id"
                :error="errors.class_id?.[0]"
            />
        </div>

        <!-- Section -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.section_id"
                label="Section"
                :options="sectionOptions"
                option-label="name"
                option-value="id"
                :error="errors.section_id?.[0]"
            />
        </div>

        <!-- Subject -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.subject_id"
                label="Subject"
                :options="subjectOptions"
                option-label="name"
                option-value="id"
                :error="errors.subject_id?.[0]"
            />
        </div>

        <!-- Teacher -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.teacher_id"
                label="Teacher"
                :options="teacherOptions"
                option-label="name"
                option-value="id"
                :error="errors.teacher_id?.[0]"
            />
        </div>

        <!-- Day -->
        <div class="col-md-4 mb-3">
            <AppSelect
                v-model="form.day"
                label="Day"
                :options="days"
                option-label="name"
                option-value="id"
                :error="errors.day?.[0]"
            />
        </div>

        <!-- Start Time -->
        <div class="col-md-4 mb-3">
            <AppInput
                v-model="form.start_time"
                label="Start Time"
                type="time"
                :error="errors.start_time?.[0]"
            />
        </div>

        <!-- End Time -->
        <div class="col-md-4 mb-3">
            <AppInput
                v-model="form.end_time"
                label="End Time"
                type="time"
                :error="errors.end_time?.[0]"
            />
        </div>

        <!-- Room -->
        <div class="col-md-6 mb-3">
            <AppInput
                v-model="form.room"
                label="Room"
                :error="errors.room?.[0]"
            />
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.status"
                label="Status"
                :options="[
                    { id: true, name: 'Active' },
                    { id: false, name: 'Inactive' }
                ]"
                option-label="name"
                option-value="id"
                :error="errors.status?.[0]"
            />
        </div>

    </div>
</template>