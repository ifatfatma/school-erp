<script setup>
import { computed, onMounted, ref } from "vue";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

import api from "@/api/axios";

const exams = ref([]);
const loading = ref(false);
const publishingId = ref(null);
const errors = ref({});

const form = ref({
    exam_id: "",
});

const selectedExam = computed(() => {
    return exams.value.find(
        exam => Number(exam.id) === Number(form.value.exam_id)
    );
});


/*
|--------------------------------------------------------------------------
| Load Exams
|--------------------------------------------------------------------------
*/

const loadExams = async () => {

    loading.value = true;

    try {

        const { data } = await api.get(
            "/exams/active"
        );

        exams.value = data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load exams:",
            error
        );

        errors.value = {
            general: "Unable to load examinations."
        };

    } finally {

        loading.value = false;

    }
};


/*
|--------------------------------------------------------------------------
| Publish Result
|--------------------------------------------------------------------------
*/

const publishResult = async (exam) => {

    if (!exam) {
        return;
    }

    if (exam.is_result_published) {
        return;
    }

    const confirmed = window.confirm(
        `Are you sure you want to publish the result for "${exam.name}"?`
    );

    if (!confirmed) {
        return;
    }

    publishingId.value = exam.id;

    errors.value = {};

    try {

        const { data } = await api.patch(
            `/exams/${exam.id}/publish-result`
        );

        if (data.success !== false) {

            exam.is_result_published = true;

            /*
             * Refresh from server so the UI always
             * reflects the database value.
             */
            await loadExams();

        }

    } catch (error) {

        console.error(
            "Publish result error:",
            error
        );

        errors.value =
            error.response?.data?.errors ?? {
                general:
                    error.response?.data?.message ??
                    "Failed to publish result."
            };

    } finally {

        publishingId.value = null;

    }
};


/*
|--------------------------------------------------------------------------
| Unpublish Result
|--------------------------------------------------------------------------
*/

const unpublishResult = async (exam) => {

    if (!exam) {
        return;
    }

    if (!exam.is_result_published) {
        return;
    }

    const confirmed = window.confirm(
        `Are you sure you want to unpublish the result for "${exam.name}"?`
    );

    if (!confirmed) {
        return;
    }

    publishingId.value = exam.id;

    errors.value = {};

    try {

        const { data } = await api.patch(
            `/exams/${exam.id}/unpublish-result`
        );

        if (data.success !== false) {

            exam.is_result_published = false;

            await loadExams();

        }

    } catch (error) {

        console.error(
            "Unpublish result error:",
            error
        );

        errors.value =
            error.response?.data?.errors ?? {
                general:
                    error.response?.data?.message ??
                    "Failed to unpublish result."
            };

    } finally {

        publishingId.value = null;

    }
};


/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

const formatDate = (date) => {

    if (!date) {
        return "-";
    }

    return new Date(date).toLocaleDateString(
        "en-IN",
        {
            day: "2-digit",
            month: "short",
            year: "numeric"
        }
    );

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadExams();

});
</script>


<template>

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Result Publish' }
        ]"
    />


    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Result Publish
            </h4>

        </div>


        <div class="card-body">

            <!-- Error -->

            <div
                v-if="errors.general"
                class="alert alert-danger"
            >
                {{ errors.general }}
            </div>


            <!-- Loading -->

            <div
                v-if="loading"
                class="text-center py-4"
            >

                <div
                    class="spinner-border text-primary"
                ></div>

                <div class="mt-2 text-muted">
                    Loading examinations...
                </div>

            </div>


            <!-- Exam Select -->

            <div
                v-else
                class="row mb-4"
            >

                <div class="col-md-6">

                    <label class="form-label">
                        Examination
                    </label>

                    <select
                        v-model="form.exam_id"
                        class="form-select"
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
                            <span v-if="exam.code">
                                ({{ exam.code }})
                            </span>
                        </option>

                    </select>

                </div>

            </div>


            <!-- Selected Exam -->

            <div
                v-if="selectedExam"
                class="card border mb-4"
            >

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3">

                            <small class="text-muted">
                                Examination
                            </small>

                            <div class="fw-semibold">
                                {{ selectedExam.name }}
                            </div>

                        </div>


                        <div class="col-md-3">

                            <small class="text-muted">
                                Code
                            </small>

                            <div>
                                {{ selectedExam.code || "-" }}
                            </div>

                        </div>


                        <div class="col-md-3">

                            <small class="text-muted">
                                Start Date
                            </small>

                            <div>
                                {{ formatDate(selectedExam.start_date) }}
                            </div>

                        </div>


                        <div class="col-md-3">

                            <small class="text-muted">
                                End Date
                            </small>

                            <div>
                                {{ formatDate(selectedExam.end_date) }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Exam List -->

            <div
                v-if="!loading && exams.length"
                class="table-responsive"
            >

                <table
                    class="table table-bordered table-hover"
                >

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Examination
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Start Date
                            </th>

                            <th>
                                End Date
                            </th>

                            <th class="text-center">
                                Status
                            </th>

                            <th
                                class="text-center"
                                width="180"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="(exam, index) in exams"
                            :key="exam.id"
                        >

                            <td>
                                {{ index + 1 }}
                            </td>


                            <td>

                                <strong>
                                    {{ exam.name }}
                                </strong>

                            </td>


                            <td>
                                {{ exam.code || "-" }}
                            </td>


                            <td>
                                {{ formatDate(exam.start_date) }}
                            </td>


                            <td>
                                {{ formatDate(exam.end_date) }}
                            </td>


                            <td class="text-center">

                                <span
                                    v-if="exam.is_result_published"
                                    class="badge bg-success"
                                >
                                    Published
                                </span>

                                <span
                                    v-else
                                    class="badge bg-secondary"
                                >
                                    Not Published
                                </span>

                            </td>


                            <td class="text-center">

                                <!-- Unpublish -->

                                <button
                                    v-if="exam.is_result_published"
                                    type="button"
                                    class="btn btn-sm btn-warning"
                                    :disabled="
                                        publishingId === exam.id
                                    "
                                    @click="unpublishResult(exam)"
                                >

                                    <span
                                        v-if="
                                            publishingId === exam.id
                                        "
                                        class="spinner-border spinner-border-sm me-1"
                                    ></span>

                                    Unpublish

                                </button>


                                <!-- Publish -->

                                <button
                                    v-else
                                    type="button"
                                    class="btn btn-sm btn-success"
                                    :disabled="
                                        publishingId === exam.id
                                    "
                                    @click="publishResult(exam)"
                                >

                                    <span
                                        v-if="
                                            publishingId === exam.id
                                        "
                                        class="spinner-border spinner-border-sm me-1"
                                    ></span>

                                    Publish Result

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Empty -->

            <div
                v-if="!loading && !exams.length"
                class="text-center text-muted py-5"
            >

                No examinations found.

            </div>

        </div>

    </div>

</template>