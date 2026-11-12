<template>
    <div class="container-fluid py-2">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Expense Management' },
                { label: 'Expenses' }
            ]"
        />

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Expenses</h4>
                <small class="text-muted">
                    Manage school expenses
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Expense
            </button>
        </div>

        <!-- Messages -->
        <div v-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <div v-if="success" class="alert alert-success">
            {{ success }}
        </div>

        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Expense no / payee / reference"
                            @keyup.enter="loadExpenses(1)"
                        >
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="filters.expense_category_id"
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

                    <div class="col-md-1">
                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                        >
                            <option value="">All</option>
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">
                                Cancelled
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Payment
                        </label>

                        <select
                            v-model="filters.payment_method"
                            class="form-select"
                        >
                            <option value="">All</option>
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Bank Transfer">
                                Bank Transfer
                            </option>
                            <option value="UPI">UPI</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Online">Online</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                </div>

                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadExpenses(1)"
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

        <!-- Summary -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Records
                        </small>

                        <h4 class="mb-0 mt-1">
                            {{ pagination.total }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Paid
                        </small>

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalPaid) }}
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

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalPending) }}
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

                        <h4 class="mb-0 mt-1">
                            ₹{{ formatAmount(totalCancelled) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        <!-- Expense List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>
                    Expense List
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
                                <th width="60">#</th>

                                <th>Expense No</th>

                                <th>Date</th>

                                <th>Category</th>

                                <th>Payee</th>

                                <th>Payment</th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>Status</th>

                                <th width="180" class="text-center">
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
                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="mt-2 text-muted">
                                        Loading expenses...
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-else-if="
                                    expenses.length === 0
                                "
                            >
                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >
                                    No expenses found.
                                </td>
                            </tr>

                            <tr
                                v-else
                                v-for="(
                                    expense,
                                    index
                                ) in expenses"
                                :key="expense.id"
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
                                    <strong>
                                        {{ expense.expense_no || "-" }}
                                    </strong>
                                </td>

                                <td>
                                    {{ formatDate(expense.expense_date) }}
                                </td>

                                <td>
                                    {{
                                        expense.category?.name ||
                                        "-"
                                    }}
                                </td>

                                <td>
                                    {{ expense.payee_name }}
                                </td>

                                <td>
                                    {{ expense.payment_method }}
                                </td>

                                <td class="text-end">
                                    ₹{{
                                        formatAmount(
                                            expense.amount
                                        )
                                    }}
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
                                        {{ expense.status }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    <!-- Attachment -->
                                    <a
                                        v-if="
                                            expense.attachment_url
                                        "
                                        :href="
                                            expense.attachment_url
                                        "
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary me-1"
                                        title="Attachment"
                                    >
                                        <i class="bi bi-paperclip"></i>
                                    </a>

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                expense
                                            )
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            expense.id
                                        "
                                        @click="
                                            deleteExpense(
                                                expense
                                            )
                                        "
                                    >
                                        <span
                                            v-if="
                                                actionId ===
                                                expense.id
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
                v-if="pagination.last_page > 1"
                class="card-footer"
            >

                <div class="d-flex justify-content-between align-items-center">

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

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="
                            saveExpense
                        "
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                {{
                                    editing
                                        ? "Edit Expense"
                                        : "Add Expense"
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
                                        Category
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        v-model="
                                            form.expense_category_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.expense_category_id
                                        }"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option
                                            v-for="
                                                category in categories
                                            "
                                            :key="
                                                category.id
                                            "
                                            :value="
                                                category.id
                                            "
                                        >
                                            {{ category.name }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.expense_category_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.expense_category_id
                                        }}
                                    </div>

                                </div>


                                <!-- Date -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Expense Date
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.expense_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.expense_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.expense_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.expense_date
                                        }}
                                    </div>

                                </div>


                                <!-- Expense No -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Expense No
                                    </label>

                                    <input
                                        v-model="
                                            form.expense_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Auto generated"
                                    >

                                </div>


                                <!-- Payee -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Payee / Vendor
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.payee_name
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.payee_name
                                        }"
                                        placeholder="Enter payee/vendor name"
                                    >

                                    <div
                                        v-if="
                                            formErrors.payee_name
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.payee_name
                                        }}
                                    </div>

                                </div>


                                <!-- Payment Method -->
                                <div class="col-md-3">

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


                                <!-- Amount -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Amount
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

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
                                        placeholder="Transaction / cheque / bill no"
                                    >

                                </div>


                                <!-- Status -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="
                                            form.status
                                        "
                                        class="form-select"
                                    >

                                        <option value="Paid">
                                            Paid
                                        </option>

                                        <option value="Pending">
                                            Pending
                                        </option>

                                        <option value="Cancelled">
                                            Cancelled
                                        </option>

                                    </select>

                                </div>


                                <!-- Attachment -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Attachment
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                                        @change="
                                            handleFile
                                        "
                                    >

                                    <small
                                        class="text-muted"
                                    >
                                        Max 5 MB
                                    </small>

                                </div>


                                <!-- Existing Attachment -->
                                <div
                                    v-if="
                                        editing &&
                                        existingAttachment
                                    "
                                    class="col-md-12"
                                >

                                    <div class="alert alert-light border">

                                        Existing attachment:

                                        <a
                                            :href="
                                                existingAttachment
                                            "
                                            target="_blank"
                                            class="ms-2"
                                        >
                                            View Attachment
                                        </a>

                                    </div>

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
                                        rows="4"
                                        class="form-control"
                                        placeholder="Enter expense details"
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
                                        ? "Update Expense"
                                        : "Save Expense"
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
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const expenses = ref([]);

const categories = ref([]);

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

const selectedFile = ref(null);

const existingAttachment = ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    expense_category_id: "",

    date_from: "",

    date_to: "",

    payment_method: "",

    status: "",

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

    expense_category_id: "",

    expense_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    expense_no: "",

    payee_name: "",

    payment_method: "Cash",

    amount: "",

    reference_no: "",

    description: "",

    status: "Paid"

});


const form = ref(
    emptyForm()
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
| Load Categories
|--------------------------------------------------------------------------
*/

const loadCategories = async () => {

    try {

        const response =
            await axios.get(
                "/api/expense-categories",
                {
                    ...getAuthConfig(),
                    params: {
                        per_page: 100,
                        status: 1
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
| Load Expenses
|--------------------------------------------------------------------------
*/

const loadExpenses = async (
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

            expense_category_id:
                filters.value.expense_category_id ||
                undefined,

            date_from:
                filters.value.date_from ||
                undefined,

            date_to:
                filters.value.date_to ||
                undefined,

            payment_method:
                filters.value.payment_method ||
                undefined,

            status:
                filters.value.status ||
                undefined

        };


        const response =
            await axios.get(
                "/api/expenses",
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


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
            "Expense loading error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load expenses.";

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Computed Totals
|--------------------------------------------------------------------------
*/

const totalPaid = computed(() => {

    return expenses.value

        .filter(
            expense =>
                expense.status === "Paid"
        )

        .reduce(
            (total, expense) =>
                total +
                Number(
                    expense.amount || 0
                ),
            0
        );

});


const totalPending = computed(() => {

    return expenses.value

        .filter(
            expense =>
                expense.status === "Pending"
        )

        .reduce(
            (total, expense) =>
                total +
                Number(
                    expense.amount || 0
                ),
            0
        );

});


const totalCancelled = computed(() => {

    return expenses.value

        .filter(
            expense =>
                expense.status === "Cancelled"
        )

        .reduce(
            (total, expense) =>
                total +
                Number(
                    expense.amount || 0
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

    formErrors.value = {};

    formError.value = "";

    selectedFile.value = null;

    existingAttachment.value = null;

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = (
    expense
) => {

    editing.value = true;

    editingId.value =
        expense.id;


    form.value = {

        expense_category_id:
            expense.expense_category_id
            ?? "",

        expense_date:
            expense.expense_date
                ? expense.expense_date
                    .substring(0, 10)
                : "",

        expense_no:
            expense.expense_no || "",

        payee_name:
            expense.payee_name || "",

        payment_method:
            expense.payment_method ||
            "Cash",

        amount:
            expense.amount || "",

        reference_no:
            expense.reference_no || "",

        description:
            expense.description || "",

        status:
            expense.status || "Paid"

    };


    formErrors.value = {};

    formError.value = "";

    selectedFile.value = null;

    existingAttachment.value =
        expense.attachment_url ||
        null;

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Close Modal
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
| File
|--------------------------------------------------------------------------
*/

const handleFile = (
    event
) => {

    selectedFile.value =
        event.target.files?.[0] ||
        null;

};


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveExpense = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = "";

    error.value = "";

    success.value = "";


    try {

        const data =
            new FormData();


        data.append(
            "expense_category_id",
            form.value.expense_category_id
        );

        data.append(
            "expense_date",
            form.value.expense_date
        );

        data.append(
            "expense_no",
            form.value.expense_no || ""
        );

        data.append(
            "payee_name",
            form.value.payee_name
        );

        data.append(
            "payment_method",
            form.value.payment_method
        );

        data.append(
            "amount",
            form.value.amount
        );

        data.append(
            "reference_no",
            form.value.reference_no || ""
        );

        data.append(
            "description",
            form.value.description || ""
        );

        data.append(
            "status",
            form.value.status
        );


        if (selectedFile.value) {

            data.append(
                "attachment",
                selectedFile.value
            );

        }


        let response;


        if (editing.value) {

            /*
             * Laravel PUT + multipart/form-data
             * can be problematic on some PHP setups.
             * Use POST with _method=PUT.
             */

            data.append(
                "_method",
                "PUT"
            );


            response =
                await axios.post(
                    `/api/expenses/${editingId.value}`,
                    data,
                    {
                        ...getAuthConfig(),

                        headers: {
                            ...getAuthConfig().headers,

                            "Content-Type":
                                "multipart/form-data"
                        }
                    }
                );

        } else {

            response =
                await axios.post(
                    "/api/expenses",
                    data,
                    {
                        ...getAuthConfig(),

                        headers: {
                            ...getAuthConfig().headers,

                            "Content-Type":
                                "multipart/form-data"
                        }
                    }
                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? "Expense updated successfully."
                    : "Expense created successfully."
            );


        showModal.value = false;


        await loadExpenses(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Save expense error:",
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
                "Please correct the form errors.";

        } else {

            formError.value =
                err.response?.data?.message ||
                "Unable to save expense.";

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

const deleteExpense = async (
    expense
) => {

    if (
        !window.confirm(
            `Delete expense "${expense.expense_no || expense.id}"?`
        )
    ) {

        return;

    }


    actionId.value =
        expense.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(
                `/api/expenses/${expense.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Expense deleted successfully.";


        let page =
            pagination.value.current_page;


        if (
            expenses.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadExpenses(page);

    } catch (err) {

        console.error(
            "Delete expense error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to delete expense.";

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

        expense_category_id: "",

        date_from: "",

        date_to: "",

        payment_method: "",

        status: "",

        per_page: 20

    };


    loadExpenses(1);

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


    loadExpenses(page);

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

    if (status === "Paid") {
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

    await loadCategories();

    await loadExpenses(1);

});

</script>