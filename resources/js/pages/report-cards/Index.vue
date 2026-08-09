<script setup>

import { onMounted, ref, watch } from "vue";
import { useRouter } from "vue-router";

import reportCardApi from "@/api/reportCard";
import examApi from "@/api/exam";
import classApi from "@/api/class";
import sectionApi from "@/api/section";
import studentApi from "@/api/student";

import AppSelect from "@/components/ui/AppSelect.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

const router = useRouter();

const examOptions = ref([]);
const classOptions = ref([]);
const sectionOptions = ref([]);
const studentOptions = ref([]);

const loading = ref(false);

const filters = ref({
    exam_id: "",
    class_id: "",
    section_id: "",
    student_id: "",
});

async function loadExams() {

    try {

        const { data } = await examApi.active();

        examOptions.value = data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load exams:",
            error
        );

    }

}

async function loadClasses() {

    try {

        const { data } = await classApi.getAll({
            per_page: 1000,
            status: 1,
        });

        classOptions.value = data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load classes:",
            error
        );

    }

}

async function loadSections() {

    sectionOptions.value = [];
    filters.value.section_id = "";
    filters.value.student_id = "";
    studentOptions.value = [];

    if (!filters.value.class_id) {
        return;
    }

    try {

        const { data } = await sectionApi.getAll({
            per_page: 1000,
            status: 1,
            class_id: filters.value.class_id,
        });

        sectionOptions.value = data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load sections:",
            error
        );

    }

}

async function loadStudents() {

    studentOptions.value = [];
    filters.value.student_id = "";

    if (
        !filters.value.class_id ||
        !filters.value.section_id
    ) {
        return;
    }

    try {

        const { data } = await studentApi.getAll({
            per_page: 1000,
            status: 1,
            class_id: filters.value.class_id,
            section_id: filters.value.section_id,
        });

        studentOptions.value = data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load students:",
            error
        );

    }

}

watch(
    () => filters.value.class_id,
    loadSections
);

watch(
    () => filters.value.section_id,
    loadStudents
);

function viewReport() {

    if (
        !filters.value.student_id ||
        !filters.value.exam_id
    ) {
        return;
    }

    router.push({
        name: "report-cards.show",
        params: {
            student: filters.value.student_id,
            exam: filters.value.exam_id,
        },
    });

}

onMounted(async () => {

    await Promise.all([
        loadExams(),
        loadClasses(),
    ]);

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
                label: 'Report Cards'
            }
        ]"
    />

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Report Cards
            </h4>

        </div>

        <div class="card-body">

            <div class="row">

                <!-- Exam -->

                <div class="col-md-6 mb-3">

                    <AppSelect
                        v-model="filters.exam_id"
                        label="Exam"
                        :options="examOptions"
                        option-label="name"
                        option-value="id"
                    />

                </div>

                <!-- Class -->

                <div class="col-md-6 mb-3">

                    <AppSelect
                        v-model="filters.class_id"
                        label="Class"
                        :options="classOptions"
                        option-label="name"
                        option-value="id"
                    />

                </div>

                <!-- Section -->

                <div class="col-md-6 mb-3">

                    <AppSelect
                        v-model="filters.section_id"
                        label="Section"
                        :options="sectionOptions"
                        option-label="name"
                        option-value="id"
                    />

                </div>

                <!-- Student -->

                <div class="col-md-6 mb-3">

                    <AppSelect
                        v-model="filters.student_id"
                        label="Student"
                        :options="studentOptions"
                        option-label="full_name"
                        option-value="id"
                    />

                </div>

            </div>

            <div class="text-end mt-3">

                <AppButton
                    text="View Report Card"
                    :disabled="
                        !filters.exam_id ||
                        !filters.student_id
                    "
                    @click="viewReport"
                />

            </div>

        </div>

    </div>

</template>