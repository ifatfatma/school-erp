<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Income Entries
                </h4>

                <small class="text-muted">
                    Manage school income and revenue
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Income
            </button>

        </div>


        <!-- Success -->
        <div
            v-if="success"
            class="alert alert-success alert-dismissible fade show"
        >
            {{ success }}

            <button
                type="button"
                class="btn-close"
                @click="success = ''"
            ></button>
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

            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Income no, received from..."
                            @keyup.enter="loadIncomes(1)"
                        >

                    </div>


                    <!-- Category -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="
                                filters.income_category_id
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


                    <!-- From Date -->
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


                    <!-- To Date -->
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


                    <!-- Search -->
                    <div class="col-md-1 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            :disabled="loading"
                            title="Search"
                            @click="loadIncomes(1)"
                        >

                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm"
                            ></span>

                            <i
                                v-else
                                class="bi bi-search"
                            ></i>

                        </button>

                    </div>

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm"
                        @click="resetFilters"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </button>

                </div>

            </div>

        </div>


        <!-- Summary -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">

                <div class="card border-start border-primary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Income
                        </small>

                        <h4 class="mb-0">
                            ₹{{ formatAmount(summary.total_amount) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-start border-success border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Received
                        </small>

                        <h4 class="mb-0 text-success">
                            ₹{{ formatAmount(summary.received_amount) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-start border-warning border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Pending
                        </small>

                        <h4 class="mb-0 text-warning">
                            ₹{{ formatAmount(summary.pending_amount) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-start border-danger border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Cancelled
                        </small>

                        <h4 class="mb-0 text-danger">
                            ₹{{ formatAmount(summary.cancelled_amount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        <!-- Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Income List
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

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Income No
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Received From
                                </th>

                                <th>
                                    Payment
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="150"
                                    class="text-center"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div class="spinner-border text-primary"></div>

                                    <div class="text-muted mt-2">
                                        Loading income records...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    incomes.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-inbox fs-2 d-block mb-2"
                                    ></i>

                                    No income records found.

                                </td>

                            </tr>


                            <tr
                                v-else
                                v-for="(
                                    income,
                                    index
                                ) in incomes"
                                :key="income.id"
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
                                            income.income_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            income.income_no ||
                                            `#${income.id}`
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        income.category?.name ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    {{ income.received_from }}

                                </td>


                                <td>

                                    <span class="badge bg-secondary">
                                        {{
                                            income.payment_method
                                        }}
                                    </span>

                                </td>


                                <td class="text-end">

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                income.amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                income.status
                                            )
                                        "
                                    >
                                        {{ income.status }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                income
                                            )
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        @click="
                                            openStatus(
                                                income
                                            )
                                        "
                                    >
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId === income.id
                                        "
                                        @click="
                                            deleteIncome(
                                                income
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                income.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-trash"
                                        ></i>

                                    </button>

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


        <!-- Add/Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="saveIncome"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Income'
                                        : 'Add Income'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Category -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Income Category
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.income_category_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.income_category_id
                                        }"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{ category.name }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.income_category_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.income_category_id
                                        }}
                                    </div>

                                </div>


                                <!-- Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Income Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.income_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.income_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.income_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.income_date
                                        }}
                                    </div>

                                </div>


                                <!-- Income No -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Income No
                                    </label>

                                    <input
                                        v-model="
                                            form.income_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Auto generated if empty"
                                    >

                                    <small class="text-muted">
                                        Example: INC-2026-000001
                                    </small>

                                </div>


                                <!-- Received From -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Received From
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.received_from
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.received_from
                                        }"
                                        placeholder="Person / Organization"
                                    >

                                    <div
                                        v-if="
                                            formErrors.received_from
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.received_from
                                        }}
                                    </div>

                                </div>


                                <!-- Payment Method -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Payment Method
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.payment_method
                                        "
                                        class="form-select"
                                    >

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


                                <!-- Amount -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Amount
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model="
                                                form.amount
                                            "
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    formErrors.amount
                                            }"
                                            placeholder="0.00"
                                        >

                                    </div>

                                    <div
                                        v-if="
                                            formErrors.amount
                                        "
                                        class="text-danger small mt-1"
                                    >
                                        {{
                                            formErrors.amount
                                        }}
                                    </div>

                                </div>


                                <!-- Reference -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Reference No
                                    </label>

                                    <input
                                        v-model="
                                            form.reference_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Transaction / cheque no."
                                    >

                                </div>


                                <!-- Status -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="
                                            form.status
                                        "
                                        class="form-select"
                                    >

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


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="
                                            form.description
                                        "
                                        rows="3"
                                        class="form-control"
                                        placeholder="Enter description..."
                                    ></textarea>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="saving"
                                @click="closeModal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="saving"
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update Income'
                                        : 'Save Income'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- Status Modal -->
        <div
            v-if="showStatusModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div class="modal-dialog modal-sm modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Change Status
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            @click="
                                showStatusModal = false
                            "
                        ></button>

                    </div>


                    <div class="modal-body">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="statusForm.status"
                            class="form-select"
                        >

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


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="
                                showStatusModal = false
                            "
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="statusSaving"
                            @click="updateStatus"
                        >

                            <span
                                v-if="statusSaving"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            Update

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    computed,
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const incomes =
    ref([]);

const categories =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const statusSaving =
    ref(false);

const actionId =
    ref(null);

const error =
    ref('');

const success =
    ref('');

const formError =
    ref('');

const formErrors =
    ref({});

const showModal =
    ref(false);

const showStatusModal =
    ref(false);

const editing =
    ref(false);

const editingId =
    ref(null);

const statusId =
    ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: '',

        income_category_id: '',

        payment_method: '',

        status: '',

        date_from: '',

        date_to: '',

        per_page: 20

    });


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const summary =
    ref({

        total_amount: 0,

        received_amount: 0,

        pending_amount: 0,

        cancelled_amount: 0

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
| Form
|--------------------------------------------------------------------------
*/

const emptyForm =
    () => ({

        income_category_id: '',

        income_date:
            new Date()
                .toISOString()
                .substring(
                    0,
                    10
                ),

        income_no: '',

        received_from: '',

        payment_method: 'Cash',

        amount: '',

        reference_no: '',

        description: '',

        status: 'Received'

    });


const form =
    ref(
        emptyForm()
    );


/*
|--------------------------------------------------------------------------
| Status Form
|--------------------------------------------------------------------------
*/

const statusForm =
    ref({

        status: 'Received'

    });


/*
|--------------------------------------------------------------------------
| Authentication
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
                    '/api/incomes/categories',
                    getAuthConfig()
                );


            categories.value =
                response.data?.data || [];

        } catch (err) {

            console.error(
                'Category loading error:',
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Load Summary
|--------------------------------------------------------------------------
*/

const loadSummary =
    async () => {

        try {

            const params = {

                income_category_id:
                    filters.value.income_category_id ||
                    undefined,

                payment_method:
                    filters.value.payment_method ||
                    undefined,

                status:
                    filters.value.status ||
                    undefined,

                date_from:
                    filters.value.date_from ||
                    undefined,

                date_to:
                    filters.value.date_to ||
                    undefined

            };


            const response =
                await axios.get(
                    '/api/incomes/summary',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            summary.value =
                response.data?.data || {

                    total_amount: 0,

                    received_amount: 0,

                    pending_amount: 0,

                    cancelled_amount: 0

                };

        } catch (err) {

            console.error(
                'Income summary error:',
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Load Incomes
|--------------------------------------------------------------------------
*/

const loadIncomes =
    async (
        page = 1
    ) => {

        loading.value = true;

        error.value = '';


        try {

            const params = {

                page,

                per_page:
                    filters.value.per_page,

                search:
                    filters.value.search ||
                    undefined,

                income_category_id:
                    filters.value.income_category_id ||
                    undefined,

                payment_method:
                    filters.value.payment_method ||
                    undefined,

                status:
                    filters.value.status ||
                    undefined,

                date_from:
                    filters.value.date_from ||
                    undefined,

                date_to:
                    filters.value.date_to ||
                    undefined

            };


            const response =
                await axios.get(
                    '/api/incomes',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const result =
                response.data?.data;


            incomes.value =
                result?.data || [];


            pagination.value = {

                current_page:
                    Number(
                        result?.current_page ||
                        1
                    ),

                last_page:
                    Number(
                        result?.last_page ||
                        1
                    ),

                per_page:
                    Number(
                        result?.per_page ||
                        filters.value.per_page
                    ),

                total:
                    Number(
                        result?.total ||
                        0
                    ),

                from:
                    Number(
                        result?.from ||
                        0
                    ),

                to:
                    Number(
                        result?.to ||
                        0
                    )

            };


            await loadSummary();

        } catch (err) {

            console.error(
                'Income loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load income records.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreate =
    () => {

        editing.value = false;

        editingId.value = null;

        form.value =
            emptyForm();

        formErrors.value = {};

        formError.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit =
    (income) => {

        editing.value = true;

        editingId.value =
            income.id;


        form.value = {

            income_category_id:
                income.income_category_id ||
                '',

            income_date:
                income.income_date
                    ? String(
                        income.income_date
                    ).substring(
                        0,
                        10
                    )
                    : '',

            income_no:
                income.income_no ||
                '',

            received_from:
                income.received_from ||
                '',

            payment_method:
                income.payment_method ||
                'Cash',

            amount:
                income.amount ||
                '',

            reference_no:
                income.reference_no ||
                '',

            description:
                income.description ||
                '',

            status:
                income.status ||
                'Received'

        };


        formErrors.value = {};

        formError.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Close
|--------------------------------------------------------------------------
*/

const closeModal =
    () => {

        if (saving.value) {
            return;
        }

        showModal.value = false;

    };


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveIncome =
    async () => {

        saving.value = true;

        formErrors.value = {};

        formError.value = '';

        error.value = '';

        success.value = '';


        try {

            const payload = {

                income_category_id:
                    form.value.income_category_id,

                income_date:
                    form.value.income_date,

                income_no:
                    form.value.income_no ||
                    null,

                received_from:
                    form.value.received_from,

                payment_method:
                    form.value.payment_method,

                amount:
                    form.value.amount,

                reference_no:
                    form.value.reference_no ||
                    null,

                description:
                    form.value.description ||
                    null,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/incomes/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        '/api/incomes',
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                'Income saved successfully.';


            showModal.value = false;


            await loadIncomes(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Save income error:',
                err
            );


            if (
                err.response?.status === 422
            ) {

                formErrors.value =
                    Object.fromEntries(

                        Object.entries(
                            err.response.data?.errors ||
                            {}
                        ).map(
                            ([key, value]) => [

                                key,

                                Array.isArray(value)
                                    ? value[0]
                                    : value

                            ]
                        )

                    );


                formError.value =
                    err.response.data?.message ||
                    'Please correct the form errors.';

            } else {

                formError.value =
                    err.response?.data?.message ||
                    'Unable to save income.';

            }

        } finally {

            saving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const openStatus =
    (income) => {

        statusId.value =
            income.id;

        statusForm.value.status =
            income.status ||
            'Received';

        showStatusModal.value =
            true;

    };


const updateStatus =
    async () => {

        statusSaving.value = true;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(
                    `/api/incomes/${statusId.value}/status`,
                    {
                        status:
                            statusForm.value.status
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Income status updated successfully.';


            showStatusModal.value =
                false;


            await loadIncomes(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Status update error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update status.';

        } finally {

            statusSaving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteIncome =
    async (
        income
    ) => {

        if (
            !window.confirm(
                `Delete income "${income.income_no || income.id}"?`
            )
        ) {

            return;

        }


        actionId.value =
            income.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(
                    `/api/incomes/${income.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Income deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                incomes.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadIncomes(
                page
            );

        } catch (err) {

            console.error(
                'Delete income error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete income.';

        } finally {

            actionId.value = null;

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

            search: '',

            income_category_id: '',

            payment_method: '',

            status: '',

            date_from: '',

            date_to: '',

            per_page: 20

        };


        loadIncomes(1);

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


        loadIncomes(page);

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


const statusClass =
    (status) => {

        if (
            status === 'Received'
        ) {
            return 'bg-success';
        }


        if (
            status === 'Pending'
        ) {
            return 'bg-warning text-dark';
        }


        if (
            status === 'Cancelled'
        ) {
            return 'bg-danger';
        }


        return 'bg-secondary';

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadCategories();

        await loadIncomes(1);

    }
);

</script>