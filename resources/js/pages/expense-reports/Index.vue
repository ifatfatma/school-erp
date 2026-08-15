<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Expense Reports
                </h4>

                <small class="text-muted">
                    Analyze expenses, payments and categories
                </small>
            </div>

            <div>

                <button
                    type="button"
                    class="btn btn-outline-success me-2"
                    @click="exportExcel"
                    :disabled="loading"
                >
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Excel
                </button>

                <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="exportPdf"
                    :disabled="loading"
                >
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    PDF
                </button>

            </div>

        </div>


        <!-- Messages -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
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


                    <!-- Category -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="
                                filters.expense_category_id
                            "
                            class="form-select"
                        >

                            <option value="">
                                All Categories
                            </option>

                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>

                        </select>

                    </div>


                    <!-- Payment -->
                    <div class="col-md-2">

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
                                All Methods
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
                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                        >

                            <option value="">
                                All Status
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


                    <!-- Search -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Expense / Payee"
                            @keyup.enter="loadReport"
                        >

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

                        <i
                            v-else
                            class="bi bi-search me-1"
                        ></i>

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


        <!-- Summary Cards -->
        <div class="row g-3 mb-4">

            <div class="col-md-3">

                <div class="card h-100 border-start border-primary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Expenses
                        </small>

                        <h3 class="mt-2 mb-1">
                            ₹{{ formatAmount(summary.total_amount) }}
                        </h3>

                        <small class="text-muted">
                            {{ summary.total_records }} records
                        </small>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card h-100 border-start border-success border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Successful
                        </small>

                        <h3 class="mt-2 mb-1 text-success">
                            ₹{{ formatAmount(summary.success_amount) }}
                        </h3>

                        <small class="text-muted">
                            {{ summary.success_records }} records
                        </small>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card h-100 border-start border-warning border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Pending
                        </small>

                        <h3 class="mt-2 mb-1 text-warning">
                            ₹{{ formatAmount(summary.pending_amount) }}
                        </h3>

                        <small class="text-muted">
                            {{ summary.pending_records }} records
                        </small>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card h-100 border-start border-danger border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Cancelled
                        </small>

                        <h3 class="mt-2 mb-1 text-danger">
                            ₹{{ formatAmount(summary.cancelled_amount) }}
                        </h3>

                        <small class="text-muted">
                            {{ summary.cancelled_records }} records
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <!-- Category + Payment -->
        <div class="row g-3 mb-4">

            <!-- Category -->
            <div class="col-lg-6">

                <div class="card h-100">

                    <div class="card-header">
                        <strong>
                            Category-wise Expenses
                        </strong>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            Category
                                        </th>

                                        <th>
                                            Records
                                        </th>

                                        <th class="text-end">
                                            Amount
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr
                                        v-if="
                                            categorySummary.length === 0
                                        "
                                    >

                                        <td
                                            colspan="3"
                                            class="text-center text-muted py-4"
                                        >
                                            No category data.
                                        </td>

                                    </tr>


                                    <tr
                                        v-for="
                                            item in categorySummary
                                        "
                                        :key="
                                            item.expense_category_id
                                        "
                                    >

                                        <td>

                                            <strong>
                                                {{
                                                    item.name ||
                                                    "Uncategorized"
                                                }}
                                            </strong>

                                        </td>

                                        <td>
                                            {{
                                                item.total_records
                                            }}
                                        </td>

                                        <td class="text-end">

                                            ₹{{
                                                formatAmount(
                                                    item.total_amount
                                                )
                                            }}

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Payment Method -->
            <div class="col-lg-6">

                <div class="card h-100">

                    <div class="card-header">
                        <strong>
                            Payment Method Summary
                        </strong>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>
                                            Payment Method
                                        </th>

                                        <th>
                                            Records
                                        </th>

                                        <th class="text-end">
                                            Amount
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr
                                        v-if="
                                            paymentMethodSummary.length === 0
                                        "
                                    >

                                        <td
                                            colspan="3"
                                            class="text-center text-muted py-4"
                                        >
                                            No payment data.
                                        </td>

                                    </tr>


                                    <tr
                                        v-for="
                                            item in paymentMethodSummary
                                        "
                                        :key="
                                            item.payment_method
                                        "
                                    >

                                        <td>

                                            <span
                                                class="badge bg-secondary"
                                            >
                                                {{
                                                    item.payment_method
                                                }}
                                            </span>

                                        </td>

                                        <td>
                                            {{
                                                item.total_records
                                            }}
                                        </td>

                                        <td class="text-end">

                                            ₹{{
                                                formatAmount(
                                                    item.total_amount
                                                )
                                            }}

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Daily Summary -->
        <div class="card mb-4">

            <div class="card-header">

                <strong>
                    Daily Expense Summary
                </strong>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    dailySummary.length === 0
                                "
                            >

                                <td
                                    colspan="3"
                                    class="text-center text-muted py-4"
                                >
                                    No daily data.
                                </td>

                            </tr>


                            <tr
                                v-for="
                                    item in dailySummary
                                "
                                :key="
                                    item.expense_date
                                "
                            >

                                <td>

                                    {{
                                        formatDate(
                                            item.expense_date
                                        )
                                    }}

                                </td>

                                <td>
                                    {{
                                        item.total_records
                                    }}
                                </td>

                                <td class="text-end">

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                item.total_amount
                                            )
                                        }}
                                    </strong>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- Expense Details -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Expense Details
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Expense No
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Payee
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

                            <tr
                                v-if="
                                    expenses.length === 0
                                "
                            >

                                <td
                                    colspan="8"
                                    class="text-center text-muted py-5"
                                >
                                    No expenses found.
                                </td>

                            </tr>


                            <tr
                                v-for="(
                                    expense,
                                    index
                                ) in expenses"
                                :key="
                                    expense.id
                                "
                            >

                                <td>

                                    {{
                                        (
                                            pagination.current_page - 1
                                        ) *
                                        pagination.per_page +
                                        index +
                                        1
                                    }}

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            expense.expense_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            expense.expense_no ||
                                            `#${expense.id}`
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        expense.category?.name ||
                                        "Uncategorized"
                                    }}

                                </td>


                                <td>

                                    {{
                                        expense.payee_name ||
                                        "-"
                                    }}

                                </td>


                                <td>

                                    <span class="badge bg-secondary">

                                        {{
                                            expense.payment_method ||
                                            "-"
                                        }}

                                    </span>

                                </td>


                                <td class="text-end">

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                expense.amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                expense.status
                                            )
                                        "
                                    >
                                        {{
                                            expense.status
                                        }}
                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- Pagination -->
            <div
                v-if="
                    pagination.last_page > 1
                "
                class="card-footer"
            >

                <div
                    class="d-flex justify-content-between align-items-center"
                >

                    <div class="text-muted">

                        Showing
                        {{ pagination.from || 0 }}
                        to
                        {{ pagination.to || 0 }}
                        of
                        {{ pagination.total }}

                    </div>


                    <div>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary me-1"
                            :disabled="
                                loading ||
                                pagination.current_page <= 1
                            "
                            @click="
                                changePage(
                                    pagination.current_page - 1
                                )
                            "
                        >
                            Previous
                        </button>


                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            :disabled="
                                loading ||
                                pagination.current_page >=
                                    pagination.last_page
                            "
                            @click="
                                changePage(
                                    pagination.current_page + 1
                                )
                            "
                        >
                            Next
                        </button>

                    </div>

                </div>

            </div>

        </div>

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

const loading =
    ref(false);

const error =
    ref("");

const categories =
    ref([]);

const categorySummary =
    ref([]);

const paymentMethodSummary =
    ref([]);

const dailySummary =
    ref([]);

const expenses =
    ref([]);


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const summary =
    ref({

        total_amount: 0,

        success_amount: 0,

        pending_amount: 0,

        cancelled_amount: 0,

        total_records: 0,

        success_records: 0,

        pending_records: 0,

        cancelled_records: 0

    });


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: "",

        date_from: "",

        date_to: "",

        expense_category_id: "",

        payment_method: "",

        status: "",

        per_page: 20

    });


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pagination =
    ref({

        current_page: 1,

        last_page: 1,

        per_page: 20,

        total: 0,

        from: 0,

        to: 0

    });


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

const getAuthConfig =
    () => {

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
| Categories
|--------------------------------------------------------------------------
*/

const loadCategories =
    async () => {

        try {

            const response =
                await axios.get(
                    "/api/expense-categories",
                    {
                        ...getAuthConfig(),

                        params: {
                            per_page: 100
                        }
                    }
                );


            const result =
                response.data?.data;


            categories.value =
                result?.data || [];

        } catch (err) {

            console.error(
                "Category loading error:",
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Load Report
|--------------------------------------------------------------------------
*/

const loadReport =
    async () => {

        loading.value = true;

        error.value = "";


        try {

            const params = {

                search:
                    filters.value.search ||
                    undefined,

                date_from:
                    filters.value.date_from ||
                    undefined,

                date_to:
                    filters.value.date_to ||
                    undefined,

                expense_category_id:
                    filters.value
                        .expense_category_id ||
                    undefined,

                payment_method:
                    filters.value.payment_method ||
                    undefined,

                status:
                    filters.value.status ||
                    undefined,

                per_page:
                    filters.value.per_page,

                page:
                    pagination.value.current_page

            };


            const response =
                await axios.get(
                    "/api/expense-reports",
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const data =
                response.data?.data;


            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            summary.value =
                data?.summary || {
                    total_amount: 0,
                    success_amount: 0,
                    pending_amount: 0,
                    cancelled_amount: 0,
                    total_records: 0,
                    success_records: 0,
                    pending_records: 0,
                    cancelled_records: 0
                };


            /*
            |--------------------------------------------------------------------------
            | Tables
            |--------------------------------------------------------------------------
            */

            categorySummary.value =
                data?.category_summary || [];


            paymentMethodSummary.value =
                data?.payment_method_summary || [];


            dailySummary.value =
                data?.daily_summary || [];


            /*
            |--------------------------------------------------------------------------
            | Expenses
            |--------------------------------------------------------------------------
            */

            const result =
                data?.expenses;


            expenses.value =
                result?.data || [];


            pagination.value = {

                current_page:
                    result?.current_page || 1,

                last_page:
                    result?.last_page || 1,

                per_page:
                    result?.per_page ||
                    filters.value.per_page,

                total:
                    result?.total || 0,

                from:
                    result?.from || 0,

                to:
                    result?.to || 0

            };

        } catch (err) {

            console.error(
                "Expense report error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to load expense report.";

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const changePage =
    (page) => {

        if (
            page < 1 ||
            page >
                pagination.value.last_page
        ) {

            return;

        }


        pagination.value.current_page =
            page;


        loadReport();

    };


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters =
    () => {

        filters.value = {

            search: "",

            date_from: "",

            date_to: "",

            expense_category_id: "",

            payment_method: "",

            status: "",

            per_page: 20

        };


        pagination.value.current_page =
            1;


        loadReport();

    };


/*
|--------------------------------------------------------------------------
| Export Excel
|--------------------------------------------------------------------------
*/

const exportExcel =
    async () => {

        try {

            const params = {

                ...filters.value,

                export: "excel"

            };


            const response =
                await axios.get(
                    "/api/expense-reports/export/excel",
                    {
                        ...getAuthConfig(),
                        params,
                        responseType: "blob"
                    }
                );


            const blob =
                new Blob(
                    [response.data],
                    {
                        type:
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                    }
                );


            const url =
                window.URL.createObjectURL(
                    blob
                );


            const link =
                document.createElement(
                    "a"
                );


            link.href = url;

            link.download =
                "expense-report.xlsx";

            link.click();


            window.URL.revokeObjectURL(
                url
            );

        } catch (err) {

            console.error(
                "Excel export error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to export Excel.";

        }

    };


/*
|--------------------------------------------------------------------------
| Export PDF
|--------------------------------------------------------------------------
*/

const exportPdf =
    async () => {

        try {

            const params = {

                ...filters.value,

                export: "pdf"

            };


            const response =
                await axios.get(
                    "/api/expense-reports/export/pdf",
                    {
                        ...getAuthConfig(),
                        params,
                        responseType: "blob"
                    }
                );


            const blob =
                new Blob(
                    [response.data],
                    {
                        type:
                            "application/pdf"
                    }
                );


            const url =
                window.URL.createObjectURL(
                    blob
                );


            const link =
                document.createElement(
                    "a"
                );


            link.href = url;

            link.download =
                "expense-report.pdf";

            link.click();


            window.URL.revokeObjectURL(
                url
            );

        } catch (err) {

            console.error(
                "PDF export error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to export PDF.";

        }

    };


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const formatAmount =
    (amount) => {

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


const formatDate =
    (date) => {

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


const statusClass =
    (status) => {

        if (
            status === "Success"
        ) {
            return "bg-success";
        }


        if (
            status === "Pending"
        ) {
            return "bg-warning text-dark";
        }


        if (
            status === "Cancelled"
        ) {
            return "bg-danger";
        }


        return "bg-secondary";

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadCategories();

        await loadReport();

    }
);

</script>