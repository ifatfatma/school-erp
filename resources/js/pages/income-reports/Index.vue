<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Income Reports
                </h4>

                <small class="text-muted">
                    Analyze income collection and trends
                </small>
            </div>

            <button
                type="button"
                class="btn btn-outline-primary"
                :disabled="loading"
                @click="loadAllReports"
            >
                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                <i
                    v-else
                    class="bi bi-arrow-clockwise me-1"
                ></i>

                Refresh
            </button>

        </div>


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger alert-dismissible fade show"
        >
            {{ error }}

            <button
                type="button"
                class="btn-close"
                @click="error = ''"
            ></button>
        </div>


        <!-- Filters -->
        <div class="card mb-3">

            <div class="card-header">

                <strong>
                    Report Filters
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <!-- From -->
                    <div class="col-md-3">

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
                    <div class="col-md-3">

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
                    <div class="col-md-3">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="filters.category_id"
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


                    <!-- Status -->
                    <div class="col-md-3">

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

                            <option value="Received">
                                Received
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
                        @click="loadAllReports"
                    >
                        <i class="bi bi-search me-1"></i>
                        Apply Filters
                    </button>


                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="resetFilters"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>

                </div>

            </div>

        </div>


        <!-- Summary Cards -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">

                <div class="card h-100 border-start border-primary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Income
                        </small>

                        <h4 class="mt-2 mb-0">
                            ₹{{ formatAmount(summary.total_amount) }}
                        </h4>

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
                            Received
                        </small>

                        <h4 class="mt-2 mb-0 text-success">
                            ₹{{ formatAmount(summary.received_amount) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card h-100 border-start border-warning border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Pending
                        </small>

                        <h4 class="mt-2 mb-0 text-warning">
                            ₹{{ formatAmount(summary.pending_amount) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card h-100 border-start border-danger border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Cancelled
                        </small>

                        <h4 class="mt-2 mb-0 text-danger">
                            ₹{{ formatAmount(summary.cancelled_amount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        <!-- Report Tabs -->
        <div class="card">

            <div class="card-header p-0">

                <ul class="nav nav-tabs">

                    <li class="nav-item">

                        <button
                            type="button"
                            class="nav-link"
                            :class="{
                                active:
                                    activeTab === 'daily'
                            }"
                            @click="
                                activeTab = 'daily'
                            "
                        >
                            Daily
                        </button>

                    </li>


                    <li class="nav-item">

                        <button
                            type="button"
                            class="nav-link"
                            :class="{
                                active:
                                    activeTab === 'monthly'
                            }"
                            @click="
                                activeTab = 'monthly'
                            "
                        >
                            Monthly
                        </button>

                    </li>


                    <li class="nav-item">

                        <button
                            type="button"
                            class="nav-link"
                            :class="{
                                active:
                                    activeTab === 'yearly'
                            }"
                            @click="
                                activeTab = 'yearly'
                            "
                        >
                            Yearly
                        </button>

                    </li>


                    <li class="nav-item">

                        <button
                            type="button"
                            class="nav-link"
                            :class="{
                                active:
                                    activeTab === 'category'
                            }"
                            @click="
                                activeTab = 'category'
                            "
                        >
                            Category Wise
                        </button>

                    </li>


                    <li class="nav-item">

                        <button
                            type="button"
                            class="nav-link"
                            :class="{
                                active:
                                    activeTab === 'payment'
                            }"
                            @click="
                                activeTab = 'payment'
                            "
                        >
                            Payment Method
                        </button>

                    </li>

                </ul>

            </div>


            <div class="card-body">

                <!-- DAILY -->
                <div
                    v-if="
                        activeTab === 'daily'
                    "
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered table-hover"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th class="text-end">
                                    Received
                                </th>

                                <th class="text-end">
                                    Pending
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    daily.length === 0
                                "
                            >

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No records found.
                                </td>

                            </tr>


                            <tr
                                v-for="row in daily"
                                :key="row.report_date"
                            >

                                <td>
                                    {{
                                        formatDate(
                                            row.report_date
                                        )
                                    }}
                                </td>

                                <td>
                                    {{ row.total_records }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            row.total_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-success">
                                    ₹{{
                                        formatAmount(
                                            row.received_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-warning">
                                    ₹{{
                                        formatAmount(
                                            row.pending_amount
                                        )
                                    }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- MONTHLY -->
                <div
                    v-if="
                        activeTab === 'monthly'
                    "
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered table-hover"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Month
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th class="text-end">
                                    Received
                                </th>

                                <th class="text-end">
                                    Pending
                                </th>

                                <th class="text-end">
                                    Cancelled
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    monthly.length === 0
                                "
                            >

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-4"
                                >
                                    No records found.
                                </td>

                            </tr>


                            <tr
                                v-for="row in monthly"
                                :key="row.month"
                            >

                                <td>
                                    {{ row.month }}
                                </td>

                                <td>
                                    {{ row.total_records }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            row.total_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-success">
                                    ₹{{
                                        formatAmount(
                                            row.received_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-warning">
                                    ₹{{
                                        formatAmount(
                                            row.pending_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-danger">
                                    ₹{{
                                        formatAmount(
                                            row.cancelled_amount
                                        )
                                    }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- YEARLY -->
                <div
                    v-if="
                        activeTab === 'yearly'
                    "
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered table-hover"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Year
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th class="text-end">
                                    Received
                                </th>

                                <th class="text-end">
                                    Pending
                                </th>

                                <th class="text-end">
                                    Cancelled
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    yearly.length === 0
                                "
                            >

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-4"
                                >
                                    No records found.
                                </td>

                            </tr>


                            <tr
                                v-for="row in yearly"
                                :key="row.year"
                            >

                                <td>
                                    <strong>
                                        {{ row.year }}
                                    </strong>
                                </td>

                                <td>
                                    {{ row.total_records }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            row.total_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-success">
                                    ₹{{
                                        formatAmount(
                                            row.received_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-warning">
                                    ₹{{
                                        formatAmount(
                                            row.pending_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-danger">
                                    ₹{{
                                        formatAmount(
                                            row.cancelled_amount
                                        )
                                    }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- CATEGORY -->
                <div
                    v-if="
                        activeTab === 'category'
                    "
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered table-hover"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th class="text-end">
                                    Received
                                </th>

                                <th class="text-end">
                                    Pending
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    categoryWise.length === 0
                                "
                            >

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No records found.
                                </td>

                            </tr>


                            <tr
                                v-for="row in categoryWise"
                                :key="
                                    row.income_category_id
                                "
                            >

                                <td>
                                    {{
                                        row.name ||
                                        'Uncategorized'
                                    }}
                                </td>

                                <td>
                                    {{ row.total_records }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            row.total_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-success">
                                    ₹{{
                                        formatAmount(
                                            row.received_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-warning">
                                    ₹{{
                                        formatAmount(
                                            row.pending_amount
                                        )
                                    }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- PAYMENT METHOD -->
                <div
                    v-if="
                        activeTab === 'payment'
                    "
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered table-hover"
                    >

                        <thead class="table-light">

                            <tr>

                                <th>
                                    Payment Method
                                </th>

                                <th>
                                    Records
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th class="text-end">
                                    Received
                                </th>

                                <th class="text-end">
                                    Pending
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-if="
                                    paymentWise.length === 0
                                "
                            >

                                <td
                                    colspan="5"
                                    class="text-center text-muted py-4"
                                >
                                    No records found.
                                </td>

                            </tr>


                            <tr
                                v-for="row in paymentWise"
                                :key="
                                    row.payment_method
                                "
                            >

                                <td>

                                    <span class="badge bg-secondary">
                                        {{
                                            row.payment_method ||
                                            'Other'
                                        }}
                                    </span>

                                </td>

                                <td>
                                    {{ row.total_records }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            row.total_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-success">
                                    ₹{{
                                        formatAmount(
                                            row.received_amount
                                        )
                                    }}
                                </td>

                                <td class="text-end text-warning">
                                    ₹{{
                                        formatAmount(
                                            row.pending_amount
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
</template>


<script setup>

import {
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const loading =
    ref(false);

const error =
    ref('');

const activeTab =
    ref('monthly');


const categories =
    ref([]);


/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/

const summary =
    ref({

        total_records: 0,

        total_amount: 0,

        received_amount: 0,

        pending_amount: 0,

        cancelled_amount: 0

    });


const daily =
    ref([]);

const monthly =
    ref([]);

const yearly =
    ref([]);

const categoryWise =
    ref([]);

const paymentWise =
    ref([]);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        date_from: '',

        date_to: '',

        category_id: '',

        status: ''

    });


/*
|--------------------------------------------------------------------------
| Axios
|--------------------------------------------------------------------------
*/

const getAuthConfig =
    () => {

        const token =
            localStorage.getItem('token') ||
            localStorage.getItem('auth_token') ||
            localStorage.getItem('access_token') ||
            localStorage.getItem('authToken') ||
            sessionStorage.getItem('token') ||
            sessionStorage.getItem('auth_token') ||
            sessionStorage.getItem('access_token') ||
            sessionStorage.getItem('authToken');


        const config = {

            withCredentials: true,

            headers: {
                Accept: 'application/json'
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
| Load Categories
|--------------------------------------------------------------------------
*/

const loadCategories =
    async () => {

        try {

            const response =
                await axios.get(
                    '/api/income-categories',
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
                result?.data ||
                result ||
                [];

        } catch (err) {

            console.error(
                'Category loading error:',
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Common params
|--------------------------------------------------------------------------
*/

const reportParams =
    () => {

        return {

            date_from:
                filters.value.date_from ||
                undefined,

            date_to:
                filters.value.date_to ||
                undefined,

            category_id:
                filters.value.category_id ||
                undefined,

            status:
                filters.value.status ||
                undefined

        };

    };


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const loadSummary =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/summary',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        summary.value =
            response.data?.data ||
            summary.value;

    };


/*
|--------------------------------------------------------------------------
| Daily
|--------------------------------------------------------------------------
*/

const loadDaily =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/daily',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        daily.value =
            response.data?.data ||
            [];

    };


/*
|--------------------------------------------------------------------------
| Monthly
|--------------------------------------------------------------------------
*/

const loadMonthly =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/monthly',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        monthly.value =
            response.data?.data ||
            [];

    };


/*
|--------------------------------------------------------------------------
| Yearly
|--------------------------------------------------------------------------
*/

const loadYearly =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/yearly',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        yearly.value =
            response.data?.data ||
            [];

    };


/*
|--------------------------------------------------------------------------
| Category
|--------------------------------------------------------------------------
*/

const loadCategoryWise =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/category-wise',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        categoryWise.value =
            response.data?.data ||
            [];

    };


/*
|--------------------------------------------------------------------------
| Payment Method
|--------------------------------------------------------------------------
*/

const loadPaymentWise =
    async () => {

        const response =
            await axios.get(
                '/api/income-reports/payment-method-wise',
                {
                    ...getAuthConfig(),

                    params:
                        reportParams()
                }
            );


        paymentWise.value =
            response.data?.data ||
            [];

    };


/*
|--------------------------------------------------------------------------
| Load All
|--------------------------------------------------------------------------
*/

const loadAllReports =
    async () => {

        loading.value = true;

        error.value = '';


        try {

            await Promise.all([

                loadSummary(),

                loadDaily(),

                loadMonthly(),

                loadYearly(),

                loadCategoryWise(),

                loadPaymentWise()

            ]);

        } catch (err) {

            console.error(
                'Income report error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load income reports.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters =
    () => {

        filters.value = {

            date_from: '',

            date_to: '',

            category_id: '',

            status: ''

        };


        loadAllReports();

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
            'en-IN',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );

    };


const formatDate =
    (date) => {

        if (!date) {
            return '-';
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
            'en-IN',
            {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }
        );

    };


/*
|--------------------------------------------------------------------------
| Initial load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadCategories();

        await loadAllReports();

    }
);

</script>