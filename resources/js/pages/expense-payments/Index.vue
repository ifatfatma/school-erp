<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Expense Payments</h4>
                <small class="text-muted">
                    Manage payments made against expenses
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


        <!-- Messages -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>

        <div
            v-if="success"
            class="alert alert-success"
        >
            {{ success }}
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
                            placeholder="Transaction / cheque / remarks"
                            @keyup.enter="loadPayments(1)"
                        >

                    </div>


                    <!-- Expense -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Expense
                        </label>

                        <select
                            v-model="filters.expense_id"
                            class="form-select"
                        >

                            <option value="">
                                All Expenses
                            </option>

                            <option
                                v-for="expense in expenses"
                                :key="expense.id"
                                :value="expense.id"
                            >
                                {{
                                    expense.expense_no ||
                                    `Expense #${expense.id}`
                                }}
                                -
                                {{ expense.payee_name }}
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

                </div>


                <div class="row g-3 mt-1">

                    <!-- Payment Method -->
                    <div class="col-md-3">

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


                    <div class="col-md-9 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadPayments(1)"
                        >

                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            Search

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

        </div>


        <!-- Summary -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">

                <div
                    class="card border-start border-primary border-4"
                >

                    <div class="card-body">

                        <small class="text-muted">
                            Records
                        </small>

                        <h4 class="mb-0 mt-1">
                            {{ pagination.total }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div
                    class="card border-start border-success border-4"
                >

                    <div class="card-body">

                        <small class="text-muted">
                            Successful Payments
                        </small>

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalSuccess) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div
                    class="card border-start border-warning border-4"
                >

                    <div class="card-body">

                        <small class="text-muted">
                            Pending Payments
                        </small>

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalPending) }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div
                    class="card border-start border-danger border-4"
                >

                    <div class="card-body">

                        <small class="text-muted">
                            Cancelled Payments
                        </small>

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalCancelled) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        <!-- Payment List -->
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

                    <table
                        class="table table-hover table-bordered mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Expense
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Payee
                                </th>

                                <th>
                                    Method
                                </th>

                                <th>
                                    Transaction
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="130"
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
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div
                                        class="text-muted mt-2"
                                    >
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
                                    colspan="10"
                                    class="text-center py-5 text-muted"
                                >
                                    No expense payments found.
                                </td>

                            </tr>


                            <!-- Records -->
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
                                        {{
                                            payment.expense?.expense_no ||
                                            `#${payment.expense_id}`
                                        }}
                                    </strong>

                                </td>


                                <td>
                                    {{
                                        payment.expense
                                            ?.category
                                            ?.name ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    {{
                                        payment.expense
                                            ?.payee_name ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    {{
                                        payment.payment_method
                                    }}
                                </td>


                                <td>

                                    <span
                                        v-if="
                                            payment.transaction_no
                                        "
                                    >
                                        {{
                                            payment.transaction_no
                                        }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
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
                                        {{
                                            payment.status
                                        }}
                                    </span>

                                </td>


                                <td class="text-center">

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

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </button>


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


        <!-- Add/Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5);"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="
                            savePayment
                        "
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? "Edit Expense Payment"
                                        : "Add Expense Payment"
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

                            <!-- Error -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Expense -->
                                <div class="col-md-8">

                                    <label class="form-label">

                                        Expense
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.expense_id
                                        "
                                        class="form-select"
                                        :disabled="editing"
                                        :class="{
                                            'is-invalid':
                                                formErrors.expense_id
                                        }"
                                        @change="
                                            loadExpenseSummary
                                        "
                                    >

                                        <option value="">
                                            Select Expense
                                        </option>


                                        <option
                                            v-for="
                                                expense in expenseOptions
                                            "
                                            :key="
                                                expense.id
                                            "
                                            :value="
                                                expense.id
                                            "
                                        >

                                            {{
                                                expense.expense_no ||
                                                `Expense #${expense.id}`
                                            }}

                                            -
                                            {{
                                                expense.payee_name
                                            }}

                                            -
                                            ₹{{
                                                formatAmount(
                                                    expense.amount
                                                )
                                            }}

                                        </option>

                                    </select>


                                    <div
                                        v-if="
                                            formErrors.expense_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.expense_id
                                        }}
                                    </div>

                                </div>


                                <!-- Expense Amount -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Expense Amount
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="
                                            formatAmount(
                                                summary.expense_amount
                                            )
                                        "
                                        readonly
                                    >

                                </div>


                                <!-- Summary -->
                                <div
                                    v-if="
                                        form.expense_id
                                    "
                                    class="col-md-12"
                                >

                                    <div
                                        class="row g-2"
                                    >

                                        <div class="col-md-4">

                                            <div
                                                class="p-3 bg-light border rounded"
                                            >

                                                <small
                                                    class="text-muted"
                                                >
                                                    Paid
                                                </small>

                                                <h5
                                                    class="mb-0 text-success"
                                                >
                                                    ₹{{
                                                        formatAmount(
                                                            summary.paid_amount
                                                        )
                                                    }}
                                                </h5>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div
                                                class="p-3 bg-light border rounded"
                                            >

                                                <small
                                                    class="text-muted"
                                                >
                                                    Due
                                                </small>

                                                <h5
                                                    class="mb-0 text-danger"
                                                >
                                                    ₹{{
                                                        formatAmount(
                                                            summary.due_amount
                                                        )
                                                    }}
                                                </h5>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div
                                                class="p-3 bg-light border rounded"
                                            >

                                                <small
                                                    class="text-muted"
                                                >
                                                    Payment
                                                </small>

                                                <h5
                                                    class="mb-0 text-primary"
                                                >
                                                    ₹{{
                                                        formatAmount(
                                                            form.amount
                                                        )
                                                    }}
                                                </h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Payment Date -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Payment Date
                                        <span class="text-danger">
                                            *
                                        </span>

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

                                    <div
                                        v-if="
                                            formErrors.payment_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.payment_date
                                        }}
                                    </div>

                                </div>


                                <!-- Amount -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Payment Amount
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.amount
                                        "
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.amount
                                        }"
                                        placeholder="0.00"
                                    >

                                    <div
                                        v-if="
                                            formErrors.amount
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.amount
                                        }}
                                    </div>

                                </div>


                                <!-- Payment Method -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Payment Method
                                        <span class="text-danger">
                                            *
                                        </span>

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
                                        placeholder="Transaction / reference number"
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
                                        placeholder="Payment remarks"
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
                                        ? "Update Payment"
                                        : "Save Payment"
                                }}

                            </button>

                        </div>

                    </form>

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
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const payments = ref([]);

const expenses = ref([]);

const expenseOptions = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const error = ref("");

const success = ref("");

const formError = ref("");

const formErrors = ref({});

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    expense_id: "",

    payment_method: "",

    status: "",

    date_from: "",

    date_to: "",

    per_page: 20

});


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pagination = ref({

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

const emptyForm = () => ({

    expense_id: "",

    payment_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    amount: "",

    payment_method: "Cash",

    transaction_no: "",

    bank_name: "",

    cheque_no: "",

    remarks: "",

    status: "Success"

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const emptySummary = () => ({

    expense_amount: 0,

    paid_amount: 0,

    pending_amount: 0,

    cancelled_amount: 0,

    due_amount: 0

});


const summary = ref(
    emptySummary()
);


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
| Load Expenses
|--------------------------------------------------------------------------
*/

const loadExpenses = async () => {

    try {

        const response =
            await axios.get(
                "/api/expenses",
                {
                    ...getAuthConfig(),

                    params: {
                        per_page: 100,

                        status: ""
                    }
                }
            );


        const result =
            response.data?.data;


        expenses.value =
            result?.data || [];


        expenseOptions.value =
            expenses.value;

    } catch (err) {

        console.error(
            "Expense loading error:",
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Payments
|--------------------------------------------------------------------------
*/

const loadPayments = async (
    page = 1
) => {

    loading.value = true;

    error.value = "";


    try {

        const params = {

            page,

            per_page:
                filters.value.per_page,

            search:
                filters.value.search ||
                undefined,

            expense_id:
                filters.value.expense_id ||
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
                "/api/expense-payments",
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
            "Payment loading error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load expense payments.";

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const loadExpenseSummary = async () => {

    if (!form.value.expense_id) {

        summary.value =
            emptySummary();

        return;

    }


    try {

        const response =
            await axios.get(
                `/api/expenses/${form.value.expense_id}/payment-summary`,
                getAuthConfig()
            );


        summary.value =
            response.data?.data ||
            emptySummary();

    } catch (err) {

        console.error(
            "Summary loading error:",
            err
        );


        summary.value =
            emptySummary();

    }

};


/*
|--------------------------------------------------------------------------
| Totals
|--------------------------------------------------------------------------
*/

const totalSuccess = computed(() => {

    return payments.value

        .filter(
            payment =>
                payment.status === "Success"
        )

        .reduce(
            (total, payment) =>
                total +
                Number(
                    payment.amount || 0
                ),
            0
        );

});


const totalPending = computed(() => {

    return payments.value

        .filter(
            payment =>
                payment.status === "Pending"
        )

        .reduce(
            (total, payment) =>
                total +
                Number(
                    payment.amount || 0
                ),
            0
        );

});


const totalCancelled = computed(() => {

    return payments.value

        .filter(
            payment =>
                payment.status === "Cancelled"
        )

        .reduce(
            (total, payment) =>
                total +
                Number(
                    payment.amount || 0
                ),
            0
        );

});


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    editingId.value = null;

    form.value =
        emptyForm();

    summary.value =
        emptySummary();

    formErrors.value = {};

    formError.value = "";

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = async (
    payment
) => {

    editing.value = true;

    editingId.value =
        payment.id;


    form.value = {

        expense_id:
            payment.expense_id || "",

        payment_date:
            payment.payment_date
                ? payment.payment_date
                    .substring(0, 10)
                : "",

        amount:
            payment.amount || "",

        payment_method:
            payment.payment_method ||
            "Cash",

        transaction_no:
            payment.transaction_no ||
            "",

        bank_name:
            payment.bank_name ||
            "",

        cheque_no:
            payment.cheque_no ||
            "",

        remarks:
            payment.remarks ||
            "",

        status:
            payment.status ||
            "Success"

    };


    formErrors.value = {};

    formError.value = "";

    showModal.value = true;


    await loadExpenseSummary();

};


/*
|--------------------------------------------------------------------------
| Close
|--------------------------------------------------------------------------
*/

const closeModal = () => {

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

const savePayment = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = "";

    error.value = "";

    success.value = "";


    try {

        const payload = {

            expense_id:
                form.value.expense_id,

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
                    `/api/expense-payments/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    "/api/expense-payments",
                    payload,
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? "Payment updated successfully."
                    : "Payment recorded successfully."
            );


        showModal.value = false;


        await loadPayments(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Save payment error:",
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
                "Please correct the errors.";

        } else {

            formError.value =
                err.response?.data?.message ||
                "Unable to save payment.";

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deletePayment = async (
    payment
) => {

    if (
        !window.confirm(
            `Delete this payment of ₹${formatAmount(payment.amount)}?`
        )
    ) {

        return;

    }


    actionId.value =
        payment.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(
                `/api/expense-payments/${payment.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Payment deleted successfully.";


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
            "Delete payment error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to delete payment.";

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: "",

        expense_id: "",

        payment_method: "",

        status: "",

        date_from: "",

        date_to: "",

        per_page: 20

    };


    loadPayments(1);

};


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const changePage = (
    page
) => {

    if (
        page < 1 ||
        page > pagination.value.last_page
    ) {

        return;

    }


    loadPayments(page);

};


/*
|--------------------------------------------------------------------------
| Formatting
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


const statusClass = (
    status
) => {

    if (status === "Success") {
        return "bg-success";
    }

    if (status === "Pending") {
        return "bg-warning text-dark";
    }

    if (status === "Cancelled") {
        return "bg-danger";
    }

    return "bg-secondary";

};


/*
|--------------------------------------------------------------------------
| Initial
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await loadExpenses();

    await loadPayments(1);

});

</script>