<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Exam Rank Calculation
                </h4>

                <p class="text-muted mb-0">
                    Calculate and view student ranks for an examination.
                </p>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                :disabled="loading || !canCalculate"
                @click="calculateRanks"
            >
                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                {{ loading ? 'Calculating...' : 'Calculate Rank' }}
            </button>

        </div>


        <!-- Filters -->
        <div class="card mb-4">

            <div class="card-header">
                <h5 class="mb-0">
                    Select Examination
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- Exam -->
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Examination
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            v-model="form.exam_id"
                            class="form-select"
                            @change="examChanged"
                        >

                            <option value="">
                                Select Examination
                            </option>

                            <option
                                v-for="exam in exams"
                                :key="exam.id"
                                :value="exam.id"
                            >
                                {{ exam.name }}
                            </option>

                        </select>

                        <div
                            v-if="errors.exam_id"
                            class="text-danger small mt-1"
                        >
                            {{ errors.exam_id[0] }}
                        </div>

                    </div>


                    <!-- Class -->
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Class
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            v-model="form.class_id"
                            class="form-select"
                            @change="classChanged"
                        >

                            <option value="">
                                Select Class
                            </option>

                            <option
                                v-for="item in classes"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>

                        </select>

                        <div
                            v-if="errors.class_id"
                            class="text-danger small mt-1"
                        >
                            {{ errors.class_id[0] }}
                        </div>

                    </div>


                    <!-- Section -->
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Section
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            v-model="form.section_id"
                            class="form-select"
                            :disabled="!form.class_id"
                        >

                            <option value="">
                                Select Section
                            </option>

                            <option
                                v-for="section in filteredSections"
                                :key="section.id"
                                :value="section.id"
                            >
                                {{ section.name }}
                            </option>

                        </select>

                        <div
                            v-if="errors.section_id"
                            class="text-danger small mt-1"
                        >
                            {{ errors.section_id[0] }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Results -->
        <div
            v-if="results.length"
            class="card"
        >

            <div class="card-header d-flex justify-content-between">

                <h5 class="mb-0">
                    Rank List
                </h5>

                <span class="badge bg-primary">
                    {{ results.length }} Students
                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    <thead>

                        <tr>

                            <th>
                                Rank
                            </th>

                            <th>
                                Roll No
                            </th>

                            <th>
                                Admission No
                            </th>

                            <th>
                                Student
                            </th>

                            <th class="text-center">
                                Full Marks
                            </th>

                            <th class="text-center">
                                Obtained
                            </th>

                            <th class="text-center">
                                Percentage
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="row in results"
                            :key="row.student_id"
                        >

                            <td class="text-center">

                                <span
                                    v-if="row.rank"
                                    class="fw-bold"
                                >
                                    {{ row.rank }}
                                </span>

                                <span
                                    v-else
                                    class="text-muted"
                                >
                                    -
                                </span>

                            </td>


                            <td>
                                {{ row.student?.roll_no ?? '-' }}
                            </td>


                            <td>
                                {{ row.student?.admission_no ?? '-' }}
                            </td>


                            <td>

                                <strong>
                                    {{ studentName(row.student) }}
                                </strong>

                            </td>


                            <td class="text-center">
                                {{ formatNumber(row.total_full_marks) }}
                            </td>


                            <td class="text-center">
                                {{ formatNumber(row.total_obtained_marks) }}
                            </td>


                            <td class="text-center">

                                <strong>
                                    {{ formatNumber(row.percentage) }}%
                                </strong>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>


        <!-- Empty -->
        <div
            v-else-if="calculated"
            class="card"
        >

            <div class="card-body text-center py-5">

                <h5>
                    No ranking data found
                </h5>

                <p class="text-muted mb-0">
                    No students with complete marks were found
                    for the selected examination.
                </p>

            </div>

        </div>


        <!-- Initial -->
        <div
            v-else
            class="card"
        >

            <div class="card-body text-center py-5">

                <h5>
                    Calculate Examination Rank
                </h5>

                <p class="text-muted mb-0">
                    Select examination, class and section
                    and click Calculate Rank.
                </p>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    computed,
    onMounted
} from "vue";

import api from "@/api/axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const calculated = ref(false);

const results = ref([]);

const exams = ref([]);

const classes = ref([]);

const sections = ref([]);

const errors = ref({});


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({

    exam_id: "",

    class_id: "",

    section_id: "",

});


/*
|--------------------------------------------------------------------------
| Filter Sections By Class
|--------------------------------------------------------------------------
*/

const filteredSections = computed(() => {

    if (!form.value.class_id) {
        return [];
    }

    return sections.value.filter(section => {

        return Number(section.class_id) ===
            Number(form.value.class_id);

    });

});


/*
|--------------------------------------------------------------------------
| Can Calculate
|--------------------------------------------------------------------------
*/

const canCalculate = computed(() => {

    return Boolean(

        form.value.exam_id &&
        form.value.class_id &&
        form.value.section_id

    );

});


/*
|--------------------------------------------------------------------------
| Load Exams
|--------------------------------------------------------------------------
*/

const loadExams = async () => {

    try {

        const response = await api.get(
            "/exams/active"
        );

        console.log(
            "EXAMS:",
            response.data
        );

        exams.value =
            response.data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load exams:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Classes
|--------------------------------------------------------------------------
*/

const loadClasses = async () => {

    try {

        const response = await api.get(
            "/classes/active"
        );

        console.log(
            "CLASSES:",
            response.data
        );

        classes.value =
            response.data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load classes:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Sections
|--------------------------------------------------------------------------
*/

const loadSections = async () => {

    try {

        const response = await api.get(
            "/sections/active"
        );

        console.log(
            "SECTIONS:",
            response.data
        );

        sections.value =
            response.data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load sections:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Exam Changed
|--------------------------------------------------------------------------
*/

const examChanged = () => {

    results.value = [];

    calculated.value = false;

    errors.value = {};

};


/*
|--------------------------------------------------------------------------
| Class Changed
|--------------------------------------------------------------------------
*/

const classChanged = () => {

    form.value.section_id = "";

    results.value = [];

    calculated.value = false;

    errors.value = {};

};


/*
|--------------------------------------------------------------------------
| Student Name
|--------------------------------------------------------------------------
*/

const studentName = (student) => {

    if (!student) {
        return "-";
    }

    if (student.full_name) {
        return student.full_name;
    }

    const name = [

        student.first_name,

        student.middle_name,

        student.last_name

    ].filter(Boolean);

    return name.length
        ? name.join(" ")
        : "-";

};


/*
|--------------------------------------------------------------------------
| Format Number
|--------------------------------------------------------------------------
*/

const formatNumber = (value) => {

    const number = Number(value ?? 0);

    return Number.isInteger(number)
        ? number
        : number.toFixed(2);

};


/*
|--------------------------------------------------------------------------
| Calculate Ranks
|--------------------------------------------------------------------------
*/

const calculateRanks = async () => {

    if (!canCalculate.value) {

        return;

    }


    loading.value = true;

    calculated.value = false;

    errors.value = {};

    results.value = [];


    try {

        const response = await api.post(

            "/exam-ranks/calculate",

            {

                exam_id:
                    Number(form.value.exam_id),

                class_id:
                    Number(form.value.class_id),

                section_id:
                    Number(form.value.section_id),

            }

        );


        console.log(
            "RANK RESPONSE:",
            response.data
        );


        if (response.data.success) {

            results.value =
                response.data.data ?? [];

            calculated.value = true;

        }

    } catch (error) {

        console.error(
            "Rank calculation failed:",
            error
        );


        if (
            error.response?.status === 422
        ) {

            errors.value =
                error.response.data.errors ?? {};

        } else {

            alert(
                error.response?.data?.message ??
                "Failed to calculate examination rank."
            );

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Page Data
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await Promise.all([

        loadExams(),

        loadClasses(),

        loadSections(),

    ]);

});

</script>