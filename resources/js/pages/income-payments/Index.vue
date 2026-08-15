<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Income Payments
                </h4>

                <small class="text-muted">
                    Manage payments received against income entries
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Payment
            </button>

        </div>


        <!-- Alerts -->
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
                            placeholder="Receipt, income no, person..."
                            @keyup.enter="loadPayments(1)"
                        >

                    </div>


                    <!-- Income -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Income
                        </label>

                        <select
                            v-model="filters.income_id"
                            class="form-select"
                        >

                            <option value="">
                                All Income
                            </option>

                            <option
                                v-for="income in incomes"
                                :key="income.id"
                                :value="income.id"
                            >
                                {{
                                    income.income_no ||
                                    `#${income.id}`
                                }}
                                -
                                {{ income.received_from }}
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

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary btn-sm me-2"
                        :disabled="loading"
                        @click="loadPayments(1)"
                    >

                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-1"
                        ></span>

                        <i
                            v-else
                            class="bi bi-search me-1"
                        ></i>

                        Search

                    </button>


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
                            Total Payments
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
                            Successful
                        </small>

                        <h4 class="mb-0 text-success">
                            ₹{{ formatAmount(summary.success_amount) }}
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


        <!-- Payment Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Payment History
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
                                    Receipt No
                                </th>

                                <th>
                                    Income
                                </th>

                                <th>
                                    Received From
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

                                <th
                                    width="160"
                                    class="text-center"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- Loading -->
                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div class="spinner-border text-primary"></div>

                                    <div class="text-muted mt-2">
                                        Loading payments...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    payments.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-inbox fs-2 d-block mb-2"
                                    ></i>

                                    No payment records found.

                                </td>

                            </tr>


                            <!-- Data -->
                            <tr
                                v-else
                                v-for="(
                                    payment,
                                    index
                                ) in payments"
                                :key="payment.id"
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
                                            payment.payment_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{ payment.receipt_no }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            payment.income
                                        "
                                    >
                                        {{
                                            payment.income.income_no ||
                                            `#${payment.income.id}`
                                        }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>


                                <td>

                                    {{
                                        payment.income
                                            ?.received_from ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    <span class="badge bg-secondary">
                                        {{
                                            payment.payment_method
                                        }}
                                    </span>

                                </td>


                                <td class="text-end">

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                payment.amount
                                            )
                                        }}
                                    </strong>

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


                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                payment
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
                                                payment
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
                                            actionId ===
                                            payment.id
                                        "
                                        @click="
                                            deletePayment(
                                                payment
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                payment.id
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


        <!-- Add/Edit Payment Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="savePayment"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Income Payment'
                                        : 'Add Income Payment'
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

                                <!-- Income -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Income
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.income_id
                                        "
                                        class="form-select"
                                        :disabled="editing"
                                        :class="{
                                            'is-invalid':
                                                formErrors.income_id
                                        }"
                                        @change="
                                            loadRemainingAmount
                                        "
                                    >

                                        <option value="">
                                            Select Income
                                        </option>

                                        <option
                                            v-for="income in incomes"
                                            :key="income.id"
                                            :value="income.id"
                                        >

                                            {{
                                                income.income_no ||
                                                `#${income.id}`
                                            }}

                                            -
                                            {{ income.received_from }}

                                            -
                                            ₹{{
                                                formatAmount(
                                                    income.amount
                                                )
                                            }}

                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.income_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.income_id
                                        }}
                                    </div>

                                </div>


                                <!-- Remaining -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Remaining Amount
                                    </label>

                                    <div
                                        class="form-control bg-light fw-bold"
                                    >
                                        ₹{{
                                            formatAmount(
                                                remainingAmount
                                            )
                                        }}
                                    </div>

                                </div>


                                <!-- Receipt -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Receipt No
                                    </label>

                                    <input
                                        v-model="
                                            form.receipt_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Auto generated if empty"
                                    >

                                    <small class="text-muted">
                                        Example: INR-2026-000001
                                    </small>

                                </div>


                                <!-- Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Payment Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.payment_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.payment_date
                                        }"
                                    >

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


                                <!-- Transaction -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Transaction No
                                    </label>

                                    <input
                                        v-model="
                                            form.transaction_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Transaction number"
                                    >

                                </div>


                                <!-- Bank -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Bank Name
                                    </label>

                                    <input
                                        v-model="
                                            form.bank_name
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Bank name"
                                    >

                                </div>


                                <!-- Cheque -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Cheque No
                                    </label>

                                    <input
                                        v-model="
                                            form.cheque_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Cheque number"
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


                                <!-- Remarks -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Remarks
                                    </label>

                                    <textarea
                                        v-model="
                                            form.remarks
                                        "
                                        rows="3"
                                        class="form-control"
                                        placeholder="Enter remarks..."
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
                                :disabled="
                                    saving ||
                                    !form.income_id
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update Payment'
                                        : 'Save Payment'
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
                            Change Payment Status
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
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const payments =
    ref([]);

const incomes =
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

const remainingAmount =
    ref(0);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: '',

        income_id: '',

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

        success_amount: 0,

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

        income_id: '',

        receipt_no: '',

        payment_date:
            new Date()
                .toISOString()
                .substring(
                    0,
                    10
                ),

        amount: '',

        payment_method: 'Cash',

        transaction_no: '',

        bank_name: '',

        cheque_no: '',

        remarks: '',

        status: 'Success'

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

        status: 'Success'

    });


/*
|--------------------------------------------------------------------------
| Auth
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
| Load Incomes
|--------------------------------------------------------------------------
*/

const loadIncomes =
    async () => {

        try {

            const response =
                await axios.get(
                    '/api/incomes',
                    {
                        ...getAuthConfig(),

                        params: {
                            per_page: 100
                        }
                    }
                );


            const result =
                response.data?.data;


            incomes.value =
                result?.data || [];

        } catch (err) {

            console.error(
                'Income loading error:',
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Load Payments
|--------------------------------------------------------------------------
*/

const loadPayments =
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

                income_id:
                    filters.value.income_id ||
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
                    '/api/income-payments',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const result =
                response.data?.data;


            payments.value =
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
                'Payment loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load payment records.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const loadSummary =
    async () => {

        try {

            const params = {

                income_id:
                    filters.value.income_id ||
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
                    '/api/income-payments/summary',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            summary.value =
                response.data?.data || {

                    total_amount: 0,

                    success_amount: 0,

                    pending_amount: 0,

                    cancelled_amount: 0

                };

        } catch (err) {

            console.error(
                'Payment summary error:',
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Remaining Amount
|--------------------------------------------------------------------------
*/

const loadRemainingAmount =
    async () => {

        if (!form.value.income_id) {

            remainingAmount.value =
                0;

            return;

        }


        try {

            const response =
                await axios.get(
                    `/api/income-payments/remaining/${form.value.income_id}`,
                    getAuthConfig()
                );


            remainingAmount.value =
                Number(
                    response.data?.data
                        ?.remaining_amount || 0
                );

        } catch (err) {

            console.error(
                'Remaining amount error:',
                err
            );

            remainingAmount.value =
                0;

        }

    };


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

const openCreate =
    () => {

        editing.value = false;

        editingId.value = null;

        form.value =
            emptyForm();

        remainingAmount.value =
            0;

        formErrors.value = {};

        formError.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEdit =
    async (
        payment
    ) => {

        editing.value = true;

        editingId.value =
            payment.id;


        form.value = {

            income_id:
                payment.income_id ||
                '',

            receipt_no:
                payment.receipt_no ||
                '',

            payment_date:
                payment.payment_date
                    ? String(
                        payment.payment_date
                    ).substring(
                        0,
                        10
                    )
                    : '',

            amount:
                payment.amount ||
                '',

            payment_method:
                payment.payment_method ||
                'Cash',

            transaction_no:
                payment.transaction_no ||
                '',

            bank_name:
                payment.bank_name ||
                '',

            cheque_no:
                payment.cheque_no ||
                '',

            remarks:
                payment.remarks ||
                '',

            status:
                payment.status ||
                'Success'

        };


        formErrors.value = {};

        formError.value = '';

        await loadRemainingAmount();

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

const savePayment =
    async () => {

        saving.value = true;

        formErrors.value = {};

        formError.value = '';

        error.value = '';

        success.value = '';


        try {

            const payload = {

                income_id:
                    form.value.income_id,

                receipt_no:
                    form.value.receipt_no ||
                    null,

                payment_date:
                    form.value.payment_date,

                amount:
                    form.value.amount,

                payment_method:
                    form.value.payment_method,

                transaction_no:
                    form.value.transaction_no ||
                    null,

                bank_name:
                    form.value.bank_name ||
                    null,

                cheque_no:
                    form.value.cheque_no ||
                    null,

                remarks:
                    form.value.remarks ||
                    null,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/income-payments/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        '/api/income-payments',
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                'Payment saved successfully.';


            showModal.value =
                false;


            await loadPayments(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Save payment error:',
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
                    'Unable to save payment.';

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
    (payment) => {

        statusId.value =
            payment.id;

        statusForm.value.status =
            payment.status ||
            'Success';

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
                    `/api/income-payments/${statusId.value}/status`,
                    {
                        status:
                            statusForm.value.status
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Payment status updated successfully.';


            showStatusModal.value =
                false;


            await loadPayments(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Status error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update payment status.';

        } finally {

            statusSaving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deletePayment =
    async (
        payment
    ) => {

        if (
            !window.confirm(
                `Delete receipt "${payment.receipt_no}"?`
            )
        ) {
            return;
        }


        actionId.value =
            payment.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(
                    `/api/income-payments/${payment.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Payment deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                payments.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadPayments(page);

        } catch (err) {

            console.error(
                'Delete payment error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete payment.';

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

            income_id: '',

            payment_method: '',

            status: '',

            date_from: '',

            date_to: '',

            per_page: 20

        };


        loadPayments(1);

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


        loadPayments(page);

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
            status === 'Success'
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

        await loadIncomes();

        await loadPayments(1);

    }
);

</script>