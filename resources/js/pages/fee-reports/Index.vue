<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Fee Reports
                </h4>

                <small class="text-muted">
                    Collection and outstanding fee reports
                </small>
            </div>

            <button
                type="button"
                class="btn btn-outline-secondary"
                :disabled="loading"
                @click="printReport"
            >
                <i class="bi bi-printer me-1"></i>
                Print Report
            </button>

        </div>


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>


        <!-- Report Type -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="btn-group">

                    <button
                        type="button"
                        class="btn"
                        :class="
                            reportType === 'collection'
                                ? 'btn-primary'
                                : 'btn-outline-primary'
                        "
                        @click="
                            changeReportType(
                                'collection'
                            )
                        "
                    >
                        <i class="bi bi-cash-stack me-1"></i>
                        Collection Report
                    </button>


                    <button
                        type="button"
                        class="btn"
                        :class="
                            reportType === 'dues'
                                ? 'btn-primary'
                                : 'btn-outline-primary'
                        "
                        @click="
                            changeReportType(
                                'dues'
                            )
                        "
                    >
                        <i class="bi bi-wallet2 me-1"></i>
                        Due Report
                    </button>

                </div>

            </div>

        </div>


        <!-- Filters -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <!-- From -->
                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            v-model="filters.date_from"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- To -->
                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            v-model="filters.date_to"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- Student -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Student ID
                        </label>

                        <input
                            v-model="filters.student_id"
                            type="number"
                            min="1"
                            class="form-control"
                            placeholder="Enter student ID"
                        >

                    </div>


                    <!-- Payment Method -->
                    <div
                        v-if="
                            reportType ===
                            'collection'
                        "
                        class="col-md-2"
                    >

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            v-model="
                                filters.payment_method
                            "
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Cash">
                                Cash
                            </option>

                            <option value="Card">
                                Card
                            </option>

                            <option value="Bank Transfer">
                                Bank Transfer
                            </option>

                            <option value="UPI">
                                UPI
                            </option>

                            <option value="Cheque">
                                Cheque
                            </option>

                            <option value="Online">
                                Online
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>


                    <!-- Status -->
                    <div
                        v-if="
                            reportType ===
                            'collection'
                        "
                        class="col-md-2"
                    >

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Success">
                                Success
                            </option>

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="Cancelled">
                                Cancelled
                            </option>

                        </select>

                    </div>

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadReport"
                    >

                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-1"
                        ></span>

                        Generate Report

                    </button>


                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        :disabled="loading"
                        @click="resetFilters"
                    >
                        Reset
                    </button>

                </div>

            </div>

        </div>


        <!-- =============================== -->
        <!-- Collection Summary -->
        <!-- =============================== -->

        <template
            v-if="
                reportType ===
                'collection'
            "
        >

            <div
                v-if="summary"
                class="row g-3 mb-3"
            >

                <!-- Total Collection -->
                <div class="col-md-3">

                    <div class="card border-start border-primary border-4 h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Total Collection
                            </div>

                            <h4 class="mb-0 mt-2">

                                ₹{{
                                    formatAmount(
                                        summary.total_collection
                                    )
                                }}

                            </h4>

                        </div>

                    </div>

                </div>


                <!-- Payments -->
                <div class="col-md-3">

                    <div class="card border-start border-success border-4 h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Total Payments
                            </div>

                            <h4 class="mb-0 mt-2">
                                {{
                                    summary.total_payments || 0
                                }}
                            </h4>

                        </div>

                    </div>

                </div>


                <!-- Cash -->
                <div class="col-md-3">

                    <div class="card border-start border-warning border-4 h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                Cash
                            </div>

                            <h4 class="mb-0 mt-2">

                                ₹{{
                                    formatAmount(
                                        summary.cash
                                    )
                                }}

                            </h4>

                        </div>

                    </div>

                </div>


                <!-- UPI -->
                <div class="col-md-3">

                    <div class="card border-start border-info border-4 h-100">

                        <div class="card-body">

                            <div class="text-muted small">
                                UPI
                            </div>

                            <h4 class="mb-0 mt-2">

                                ₹{{
                                    formatAmount(
                                        summary.upi
                                    )
                                }}

                            </h4>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Payment Method Summary -->
            <div
                v-if="summary"
                class="card mb-3"
            >

                <div class="card-header">
                    <strong>
                        Payment Method Summary
                    </strong>
                </div>


                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Cash
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.cash
                                        )
                                    }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Card
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.card
                                        )
                                    }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    UPI
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.upi
                                        )
                                    }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Bank Transfer
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.bank_transfer
                                        )
                                    }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Cheque
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.cheque
                                        )
                                    }}
                                </div>

                            </div>

                        </div>


                        <div class="col-md-2">

                            <div class="border rounded p-3">

                                <small class="text-muted">
                                    Online
                                </small>

                                <div class="fw-bold">
                                    ₹{{
                                        formatAmount(
                                            summary.online
                                        )
                                    }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Collection Records -->
            <div class="card">

                <div class="card-header">

                    <strong>
                        Collection Details
                    </strong>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Receipt No
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Admission No
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Payment Method
                                    </th>

                                    <th class="text-end">
                                        Amount
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr v-if="loading">

                                    <td
                                        colspan="8"
                                        class="text-center py-5"
                                    >

                                        <div
                                            class="spinner-border text-primary"
                                        ></div>

                                        <div class="mt-2 text-muted">
                                            Generating report...
                                        </div>

                                    </td>

                                </tr>


                                <tr
                                    v-else-if="
                                        collectionRecords.length === 0
                                    "
                                >

                                    <td
                                        colspan="8"
                                        class="text-center py-5 text-muted"
                                    >
                                        No collection records found.
                                    </td>

                                </tr>


                                <tr
                                    v-else
                                    v-for="(
                                        payment,
                                        index
                                    ) in collectionRecords"
                                    :key="payment.id"
                                >

                                    <td>
                                        {{ index + 1 }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ payment.receipt_no }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{
                                            payment.student?.full_name
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            payment.student?.admission_no
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            formatDate(
                                                payment.payment_date
                                            )
                                        }}
                                    </td>

                                    <td>
                                        {{ payment.payment_method }}
                                    </td>

                                    <td class="text-end">

                                        ₹{{
                                            formatAmount(
                                                payment.total_amount
                                            )
                                        }}

                                    </td>

                                    <td>

                                        <span
                                            class="badge"
                                            :class="
                                                statusClass(
                                                    payment.status
                                                )
                                            "
                                        >
                                            {{ payment.status }}
                                        </span>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </template>


        <!-- =============================== -->
        <!-- Due Report -->
        <!-- =============================== -->

        <template
            v-if="
                reportType ===
                'dues'
            "
        >

            <div class="card">

                <div class="card-header">

                    <strong>
                        Outstanding Fee Dues
                    </strong>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Admission No
                                    </th>

                                    <th>
                                        Fee Type
                                    </th>

                                    <th>
                                        Fee Structure
                                    </th>

                                    <th>
                                        Due Date
                                    </th>

                                    <th class="text-end">
                                        Amount
                                    </th>

                                    <th class="text-end">
                                        Paid
                                    </th>

                                    <th class="text-end">
                                        Due
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr v-if="loading">

                                    <td
                                        colspan="10"
                                        class="text-center py-5"
                                    >

                                        <div
                                            class="spinner-border text-primary"
                                        ></div>

                                        <div class="mt-2 text-muted">
                                            Generating report...
                                        </div>

                                    </td>

                                </tr>


                                <tr
                                    v-else-if="
                                        dueRecords.length === 0
                                    "
                                >

                                    <td
                                        colspan="10"
                                        class="text-center py-5 text-muted"
                                    >
                                        No outstanding dues found.
                                    </td>

                                </tr>


                                <tr
                                    v-else
                                    v-for="(
                                        due,
                                        index
                                    ) in dueRecords"
                                    :key="due.id"
                                >

                                    <td>
                                        {{ index + 1 }}
                                    </td>

                                    <td>
                                        {{
                                            due.student?.full_name
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            due.student?.admission_no
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            due.fee_type?.name
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            due.fee_structure?.name
                                            || "-"
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            formatDate(
                                                due.due_date
                                            )
                                        }}
                                    </td>

                                    <td class="text-end">

                                        ₹{{
                                            formatAmount(
                                                due.amount
                                            )
                                        }}

                                    </td>

                                    <td class="text-end">

                                        ₹{{
                                            formatAmount(
                                                due.paid_amount
                                            )
                                        }}

                                    </td>

                                    <td class="text-end fw-bold">

                                        ₹{{
                                            formatAmount(
                                                getDueAmount(
                                                    due
                                                )
                                            )
                                        }}

                                    </td>

                                    <td>

                                        <span
                                            class="badge"
                                            :class="
                                                dueStatusClass(
                                                    due
                                                )
                                            "
                                        >

                                            {{
                                                due.status
                                                || "Due"
                                            }}

                                        </span>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </template>

    </div>
</template>


<script setup>

import {
    onMounted,
    ref
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const reportType = ref(
    "collection"
);

const loading = ref(false);

const error = ref("");

const summary = ref(null);

const collectionRecords = ref([]);

const dueRecords = ref([]);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    date_from: "",

    date_to: "",

    student_id: "",

    payment_method: "",

    status: ""

});


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

    const token =
        localStorage.getItem("token") ||
        localStorage.getItem("auth_token") ||
        localStorage.getItem("access_token") ||
        localStorage.getItem("authToken") ||
        sessionStorage.getItem("token") ||
        sessionStorage.getItem("auth_token") ||
        sessionStorage.getItem("access_token") ||
        sessionStorage.getItem("authToken");


    const config = {

        withCredentials: true,

        headers: {

            Accept: "application/json"

        }

    };


    if (token) {

        config.headers.Authorization =
            `Bearer ${token}`;

    }


    return config;

};


/*
|--------------------------------------------------------------------------
| Load Report
|--------------------------------------------------------------------------
*/

const loadReport = async () => {

    loading.value = true;

    error.value = "";

    summary.value = null;

    collectionRecords.value = [];

    dueRecords.value = [];


    try {

        const params = {

            date_from:
                filters.value.date_from ||
                undefined,

            date_to:
                filters.value.date_to ||
                undefined,

            student_id:
                filters.value.student_id ||
                undefined,

            payment_method:
                filters.value.payment_method ||
                undefined,

            status:
                filters.value.status ||
                undefined

        };


        let url;


        if (
            reportType.value ===
            "collection"
        ) {

            url =
                "/api/fee-reports/collection";

        } else {

            url =
                "/api/fee-reports/dues";

        }


        const response =
            await axios.get(
                url,
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const data =
            response.data?.data;


        if (
            reportType.value ===
            "collection"
        ) {

            summary.value =
                data?.summary || null;

            collectionRecords.value =
                data?.records || [];

        } else {

            dueRecords.value =
                Array.isArray(data)
                    ? data
                    : [];

        }

    } catch (err) {

        console.error(
            "Fee report error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to generate report.";

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Change Report Type
|--------------------------------------------------------------------------
*/

const changeReportType = (
    type
) => {

    reportType.value =
        type;

    loadReport();

};


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        date_from: "",

        date_to: "",

        student_id: "",

        payment_method: "",

        status: ""

    };


    loadReport();

};


/*
|--------------------------------------------------------------------------
| Format Amount
|--------------------------------------------------------------------------
*/

const formatAmount = (
    amount
) => {

    return Number(
        amount || 0
    ).toLocaleString(
        "en-IN",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

const formatDate = (
    date
) => {

    if (!date) {

        return "-";

    }


    const value =
        new Date(date);


    if (
        Number.isNaN(
            value.getTime()
        )
    ) {

        return date;

    }


    return value.toLocaleDateString(
        "en-IN",
        {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        }
    );

};


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    if (
        status ===
        "Success"
    ) {

        return "bg-success";

    }


    if (
        status ===
        "Pending"
    ) {

        return "bg-warning text-dark";

    }


    if (
        status ===
        "Cancelled"
    ) {

        return "bg-danger";

    }


    return "bg-secondary";

};


const dueStatusClass = (
    due
) => {

    if (
        due.status ===
        "Paid"
    ) {

        return "bg-success";

    }


    if (
        due.status ===
        "Partial"
    ) {

        return "bg-warning text-dark";

    }


    return "bg-danger";

};


/*
|--------------------------------------------------------------------------
| Due Amount
|--------------------------------------------------------------------------
*/

const getDueAmount = (
    due
) => {

    /*
     * Prefer an existing due_amount
     * if your API provides it.
     */

    if (
        due.due_amount !==
        undefined &&
        due.due_amount !==
        null
    ) {

        return due.due_amount;

    }


    return Math.max(

        Number(
            due.amount || 0
        )
        -
        Number(
            due.paid_amount || 0
        ),

        0

    );

};


/*
|--------------------------------------------------------------------------
| Print
|--------------------------------------------------------------------------
*/

const printReport = () => {

    window.print();

};


/*
|--------------------------------------------------------------------------
| Initial
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadReport();

});

</script>