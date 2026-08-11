<script setup>

import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import reportCardApi from "@/api/reportCard";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const error = ref("");

const report = ref({
    student: null,
    exam: null,
    subjects: [],
    summary: {},
});

async function loadReport() {

    loading.value = true;
    error.value = "";

    try {

        const { data } = await reportCardApi.student(
            route.params.student,
            route.params.exam
        );

        report.value = data.data;

    } catch (e) {

        console.error(
            "Failed to load report card:",
            e
        );

        error.value =
            e.response?.data?.message ??
            "Failed to load report card.";

    } finally {

        loading.value = false;

    }

}

function goBack() {

    router.push({
        name: "report-cards.index",
    });

}

function printReport() {

    window.print();

}

onMounted(() => {

    loadReport();

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
                label: 'Report Cards',
                to: {
                    name: 'report-cards.index'
                }
            },
            {
                label: 'Report Card'
            }
        ]"
    />

    <!-- Loading -->

    <div
        v-if="loading"
        class="text-center py-5"
    >

        Loading report card...

    </div>

    <!-- Error -->

    <div
        v-else-if="error"
        class="alert alert-danger"
    >

        {{ error }}

        <div class="mt-3">

            <button
                type="button"
                class="btn btn-secondary"
                @click="goBack"
            >
                Back
            </button>

        </div>

    </div>

    <!-- Report -->

    <div
        v-else
        class="report-card-container"
    >

        <!-- Actions -->

        <div
            class="d-flex justify-content-between align-items-center mb-3 no-print"
        >

            <button
                type="button"
                class="btn btn-secondary"
                @click="goBack"
            >
                ← Back
            </button>

            <button
                type="button"
                class="btn btn-primary"
                @click="printReport"
            >
                🖨 Print Report Card
            </button>

        </div>


        <!-- Report Card -->

        <div class="card report-card">

            <!-- Header -->

            <div class="card-body">

                <div class="text-center mb-4">

                    <h2 class="mb-1">
                        School Report Card
                    </h2>

                    <h4 class="mb-0">
                        {{ report.exam?.name }}
                    </h4>

                </div>


                <!-- Student Information -->

                <div class="row mb-4">

                    <div class="col-md-6">

                        <table class="table table-sm table-borderless">

                            <tbody>

                                <tr>

                                    <th width="150">
                                        Student Name
                                    </th>

                                    <td>
                                        :
                                        {{
                                            report.student?.full_name
                                            ??
                                            (
                                                report.student?.first_name
                                                + ' '
                                                + (
                                                    report.student?.last_name
                                                    ?? ''
                                                )
                                            )
                                        }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Admission No
                                    </th>

                                    <td>
                                        :
                                        {{ report.student?.admission_no }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Roll No
                                    </th>

                                    <td>
                                        :
                                        {{ report.student?.roll_no }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <div class="col-md-6">

                        <table class="table table-sm table-borderless">

                            <tbody>

                                <tr>

                                    <th width="150">
                                        Class
                                    </th>

                                    <td>
                                        :
                                        {{ report.student?.class?.name }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Section
                                    </th>

                                    <td>
                                        :
                                        {{ report.student?.section?.name }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Academic Session
                                    </th>

                                    <td>
                                        :
                                        {{ report.exam?.academic_session?.session_name
                                            ?? report.exam?.academic_session?.name
                                            ?? ''
                                        }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- Subjects -->

                <div class="table-responsive">

                    <table
                        class="table table-bordered text-center align-middle"
                    >

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th class="text-start">
                                    Subject
                                </th>

                                <th>
                                    Full Marks
                                </th>

                                <th>
                                    Theory
                                </th>

                                <th>
                                    Practical
                                </th>

                                <th>
                                    Viva
                                </th>

                                <th>
                                    Project
                                </th>

                                <th>
                                    Obtained
                                </th>

                                <th>
                                    %
                                </th>

                                <th>
                                    Grade
                                </th>

                                <th>
                                    Result
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in report.subjects"
                                :key="item.schedule_id"
                            >

                                <td>
                                    {{ index + 1 }}
                                </td>

                                <td class="text-start">

                                    {{ item.subject?.name }}

                                </td>

                                <td>
                                    {{ item.full_marks }}
                                </td>

                                <td>
                                    {{ item.theory_marks }}
                                </td>

                                <td>
                                    {{ item.practical_marks }}
                                </td>

                                <td>
                                    {{ item.viva_marks }}
                                </td>

                                <td>
                                    {{ item.project_marks }}
                                </td>

                                <td>
                                    <strong>
                                        {{ item.total_marks }}
                                    </strong>
                                </td>

                                <td>
                                    {{ item.percentage }}%
                                </td>

                                <td>

                                    <strong>
                                        {{ item.grade?.name ?? '-' }}
                                    </strong>

                                </td>

                                <td>

                                    <span
                                        :class="
                                            item.result === 'Pass'
                                                ? 'badge bg-success'
                                                : item.result === 'Fail'
                                                    ? 'badge bg-danger'
                                                    : 'badge bg-secondary'
                                        "
                                    >
                                        {{ item.result }}
                                    </span>

                                </td>

                            </tr>

                            <tr
                                v-if="
                                    !report.subjects ||
                                    !report.subjects.length
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center py-4"
                                >
                                    No marks available.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- Summary -->

                <div class="row mt-4">

                    <div class="col-md-6">

                        <table class="table table-bordered">

                            <tbody>

                                <tr>

                                    <th>
                                        Total Full Marks
                                    </th>

                                    <td>
                                        {{
                                            report.summary
                                                ?.total_full_marks
                                            ?? 0
                                        }}
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Total Obtained
                                    </th>

                                    <td>
                                        {{
                                            report.summary
                                                ?.total_obtained_marks
                                            ?? 0
                                        }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <div class="col-md-6">

                        <table class="table table-bordered">

                            <tbody>

                                <tr>

                                    <th>
                                        Percentage
                                    </th>

                                    <td>
                                        {{
                                            report.summary
                                                ?.percentage
                                            ?? 0
                                        }}%
                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Grade
                                    </th>

                                    <td>

                                        <strong>
                                            {{
                                                report.summary
                                                    ?.grade
                                                    ?.name
                                                ?? '-'
                                            }}
                                        </strong>

                                    </td>

                                </tr>

                                <tr>

                                    <th>
                                        Result
                                    </th>

                                    <td>

                                        <strong
                                            :class="
                                                report.summary?.result === 'Pass'
                                                    ? 'text-success'
                                                    : 'text-danger'
                                            "
                                        >
                                            {{
                                                report.summary?.result
                                            }}
                                        </strong>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- Signatures -->

                <div class="row mt-5 text-center">

                    <div class="col-md-4">

                        <div class="border-top pt-2">
                            Class Teacher
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="border-top pt-2">
                            Principal
                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="border-top pt-2">
                            Parent / Guardian
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>

<style scoped>

.report-card-container {
    max-width: 1200px;
    margin: 0 auto;
}

.report-card {
    background: #fff;
}

/* Normal screen */
.no-print {
    display: flex;
}

/* PRINT */
@media print {

    @page {
        size: A4 portrait;
        margin: 10mm;
    }

    /*
     * Hide everything outside the report card.
     */
    :global(body *) {
        visibility: hidden !important;
    }

    /*
     * Show only report card.
     */
    .report-card-container,
    .report-card-container * {
        visibility: visible !important;
    }

    /*
     * Put report at the top-left of the paper.
     */
    .report-card-container {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;

        width: 100% !important;
        max-width: none !important;

        margin: 0 !important;
        padding: 0 !important;
    }

    .report-card {
        width: 100% !important;

        border: none !important;
        box-shadow: none !important;

        margin: 0 !important;
        padding: 0 !important;
    }

    /*
     * Hide buttons.
     */
    .no-print {
        display: none !important;
        visibility: hidden !important;
    }

    /*
     * Remove Bootstrap/card spacing that can
     * create unnecessary print space.
     */
    .card-body {
        padding: 0 !important;
    }

    /*
     * Keep table rows together where possible.
     */
    table {
        page-break-inside: auto;
    }

    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    thead {
        display: table-header-group;
    }

    /*
     * Prevent report heading from separating.
     */
    .report-card-container {
        break-inside: avoid;
    }

    body {
        background: #fff !important;
    }

}

</style>