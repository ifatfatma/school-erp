<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Recurring Expenses
                </h4>

                <small class="text-muted">
                    Manage recurring and scheduled expenses
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Recurring Expense
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
                            placeholder="Name / payee"
                            @keyup.enter="loadRecurring(1)"
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


                    <!-- Frequency -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Frequency
                        </label>

                        <select
                            v-model="filters.frequency"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Daily">
                                Daily
                            </option>

                            <option value="Weekly">
                                Weekly
                            </option>

                            <option value="Monthly">
                                Monthly
                            </option>

                            <option value="Quarterly">
                                Quarterly
                            </option>

                            <option value="Yearly">
                                Yearly
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

                            <option value="Active">
                                Active
                            </option>

                            <option value="Paused">
                                Paused
                            </option>

                            <option value="Completed">
                                Completed
                            </option>

                        </select>

                    </div>


                    <!-- Auto Create -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Auto Create
                        </label>

                        <select
                            v-model="
                                filters.auto_create
                            "
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="1">
                                Enabled
                            </option>

                            <option value="0">
                                Disabled
                            </option>

                        </select>

                    </div>


                    <!-- Search button -->
                    <div
                        class="col-md-1 d-flex align-items-end"
                    >

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            :disabled="loading"
                            @click="loadRecurring(1)"
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
                        :disabled="loading"
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

                <div
                    class="card border-start border-primary border-4"
                >

                    <div class="card-body">

                        <small class="text-muted">
                            Total
                        </small>

                        <h4 class="mb-0">
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
                            Active
                        </small>

                        <h4 class="mb-0 text-success">
                            {{ activeCount }}
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
                            Due
                        </small>

                        <h4 class="mb-0 text-warning">
                            {{ dueCount }}
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
                            Monthly Amount
                        </small>

                        <h4 class="mb-0">
                            ₹{{ formatAmount(monthlyAmount) }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        <!-- Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Recurring Expense List
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

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Payee
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Frequency
                                </th>

                                <th>
                                    Next Due
                                </th>

                                <th>
                                    Auto
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="230"
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

                                    <div class="text-muted mt-2">
                                        Loading recurring expenses...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    recurringExpenses.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center py-5 text-muted"
                                >
                                    No recurring expenses found.
                                </td>

                            </tr>


                            <!-- Data -->
                            <tr
                                v-else
                                v-for="(
                                    item,
                                    index
                                ) in recurringExpenses"
                                :key="item.id"
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
                                        {{ item.name }}
                                    </strong>

                                    <div
                                        v-if="
                                            item.description
                                        "
                                        class="small text-muted"
                                    >
                                        {{
                                            truncate(
                                                item.description
                                            )
                                        }}
                                    </div>

                                </td>


                                <td>
                                    {{
                                        item.category?.name ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    {{
                                        item.payee_name ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    ₹{{
                                        formatAmount(
                                            item.amount
                                        )
                                    }}
                                </td>


                                <td>

                                    <span
                                        class="badge bg-info text-dark"
                                    >
                                        {{ item.frequency }}
                                    </span>

                                </td>


                                <td>

                                    <span
                                        :class="{
                                            'text-danger fw-bold':
                                                isDue(item),
                                            'text-muted':
                                                !isDue(item)
                                        }"
                                    >
                                        {{
                                            formatDate(
                                                item.next_due_date
                                            )
                                        }}
                                    </span>

                                    <div
                                        v-if="isDue(item)"
                                        class="small text-danger"
                                    >
                                        Due
                                    </div>

                                </td>


                                <td>

                                    <div
                                        class="form-check form-switch"
                                    >

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            :checked="
                                                Boolean(
                                                    item.auto_create
                                                )
                                            "
                                            :disabled="
                                                actionId ===
                                                item.id
                                            "
                                            @change="
                                                toggleAutoCreate(
                                                    item
                                                )
                                            "
                                        >

                                    </div>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                item.status
                                            )
                                        "
                                    >
                                        {{ item.status }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- Generate -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success me-1"
                                        title="Generate Expense"
                                        :disabled="
                                            actionId ===
                                            item.id ||
                                            item.status !==
                                                'Active'
                                        "
                                        @click="
                                            generateExpense(
                                                item
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                item.id &&
                                                actionType ===
                                                    'generate'
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-plus-circle"
                                        ></i>

                                    </button>


                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                item
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </button>


                                    <!-- Pause / Resume -->
                                    <button
                                        v-if="
                                            item.status ===
                                            'Active'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Pause"
                                        :disabled="
                                            actionId ===
                                            item.id
                                        "
                                        @click="
                                            changeStatus(
                                                item,
                                                'Paused'
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-pause-fill"
                                        ></i>

                                    </button>


                                    <button
                                        v-else-if="
                                            item.status ===
                                            'Paused'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-success me-1"
                                        title="Resume"
                                        :disabled="
                                            actionId ===
                                            item.id
                                        "
                                        @click="
                                            changeStatus(
                                                item,
                                                'Active'
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-play-fill"
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            item.id
                                        "
                                        @click="
                                            deleteRecurring(
                                                item
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                item.id &&
                                                actionType ===
                                                    'delete'
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


        <!-- Modal -->
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
                            saveRecurring
                        "
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? "Edit Recurring Expense"
                                        : "Add Recurring Expense"
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

                                <!-- Name -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Name
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.name
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="e.g. School Internet"
                                    >

                                    <div
                                        v-if="
                                            formErrors.name
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.name
                                        }}
                                    </div>

                                </div>


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
                                        placeholder="Vendor / Payee"
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
                                        min="0.01"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.amount
                                        }"
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


                                <!-- Frequency -->
                                <div class="col-md-3">

                                    <label class="form-label">

                                        Frequency
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="
                                            form.frequency
                                        "
                                        class="form-select"
                                    >

                                        <option value="Daily">
                                            Daily
                                        </option>

                                        <option value="Weekly">
                                            Weekly
                                        </option>

                                        <option value="Monthly">
                                            Monthly
                                        </option>

                                        <option value="Quarterly">
                                            Quarterly
                                        </option>

                                        <option value="Yearly">
                                            Yearly
                                        </option>

                                    </select>

                                </div>


                                <!-- Start Date -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Start Date
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.start_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.start_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.start_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.start_date
                                        }}
                                    </div>

                                </div>


                                <!-- Next Due -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Next Due Date
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.next_due_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.next_due_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.next_due_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.next_due_date
                                        }}
                                    </div>

                                </div>


                                <!-- End Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        End Date
                                    </label>

                                    <input
                                        v-model="
                                            form.end_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Payment Method -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Payment Method
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


                                <!-- Status -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="
                                            form.status
                                        "
                                        class="form-select"
                                    >

                                        <option value="Active">
                                            Active
                                        </option>

                                        <option value="Paused">
                                            Paused
                                        </option>

                                        <option value="Completed">
                                            Completed
                                        </option>

                                    </select>

                                </div>


                                <!-- Auto Create -->
                                <div class="col-md-4">

                                    <label class="form-label d-block">
                                        Automatic Expense Creation
                                    </label>

                                    <div class="form-check form-switch mt-2">

                                        <input
                                            v-model="
                                                form.auto_create
                                            "
                                            class="form-check-input"
                                            type="checkbox"
                                            id="autoCreate"
                                        >

                                        <label
                                            class="form-check-label"
                                            for="autoCreate"
                                        >
                                            Enable Auto Create
                                        </label>

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
                                        rows="3"
                                        class="form-control"
                                        placeholder="Expense description"
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
                                        ? "Update"
                                        : "Save"
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

const recurringExpenses =
    ref([]);

const categories =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const actionId =
    ref(null);

const actionType =
    ref("");

const error =
    ref("");

const success =
    ref("");

const formError =
    ref("");

const formErrors =
    ref({});

const showModal =
    ref(false);

const editing =
    ref(false);

const editingId =
    ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: "",

        expense_category_id: "",

        frequency: "",

        status: "",

        auto_create: "",

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
| Form
|--------------------------------------------------------------------------
*/

const today = () => {

    return new Date()
        .toISOString()
        .substring(0, 10);

};


const emptyForm = () => ({

    expense_category_id: "",

    name: "",

    payee_name: "",

    amount: "",

    frequency: "Monthly",

    start_date: today(),

    end_date: "",

    next_due_date: today(),

    payment_method: "Cash",

    description: "",

    auto_create: true,

    status: "Active"

});


const form =
    ref(
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
                "Category error:",
                err
            );

        }

    };


/*
|--------------------------------------------------------------------------
| Recurring Expenses
|--------------------------------------------------------------------------
*/

const loadRecurring =
    async (
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
                    filters.value
                        .expense_category_id ||
                    undefined,

                frequency:
                    filters.value.frequency ||
                    undefined,

                status:
                    filters.value.status ||
                    undefined,

                auto_create:
                    filters.value.auto_create === ""
                        ? undefined
                        : filters.value.auto_create

            };


            const response =
                await axios.get(
                    "/api/recurring-expenses",
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const result =
                response.data?.data;


            recurringExpenses.value =
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
                "Recurring expense error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to load recurring expenses.";

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const activeCount =
    computed(() => {

        return recurringExpenses.value
            .filter(
                item =>
                    item.status === "Active"
            )
            .length;

    });


const dueCount =
    computed(() => {

        return recurringExpenses.value
            .filter(
                item =>
                    isDue(item)
            )
            .length;

    });


const monthlyAmount =
    computed(() => {

        return recurringExpenses.value
            .filter(
                item =>
                    item.status === "Active"
            )
            .reduce(
                (
                    total,
                    item
                ) => {

                    const amount =
                        Number(
                            item.amount || 0
                        );


                    if (
                        item.frequency ===
                        "Daily"
                    ) {

                        return total +
                            amount * 30;

                    }


                    if (
                        item.frequency ===
                        "Weekly"
                    ) {

                        return total +
                            amount * 4.3333;

                    }


                    if (
                        item.frequency ===
                        "Quarterly"
                    ) {

                        return total +
                            amount / 3;

                    }


                    if (
                        item.frequency ===
                        "Yearly"
                    ) {

                        return total +
                            amount / 12;

                    }


                    return total +
                        amount;

                },
                0
            );

    });


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

        formErrors.value = {};

        formError.value = "";

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEdit =
    (item) => {

        editing.value = true;

        editingId.value =
            item.id;


        form.value = {

            expense_category_id:
                item.expense_category_id ||
                "",

            name:
                item.name ||
                "",

            payee_name:
                item.payee_name ||
                "",

            amount:
                item.amount ||
                "",

            frequency:
                item.frequency ||
                "Monthly",

            start_date:
                item.start_date
                    ? item.start_date
                        .substring(0, 10)
                    : "",

            end_date:
                item.end_date
                    ? item.end_date
                        .substring(0, 10)
                    : "",

            next_due_date:
                item.next_due_date
                    ? item.next_due_date
                        .substring(0, 10)
                    : "",

            payment_method:
                item.payment_method ||
                "Cash",

            description:
                item.description ||
                "",

            auto_create:
                Boolean(
                    item.auto_create
                ),

            status:
                item.status ||
                "Active"

        };


        formErrors.value = {};

        formError.value = "";

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

const saveRecurring =
    async () => {

        saving.value = true;

        formErrors.value = {};

        formError.value = "";

        error.value = "";

        success.value = "";


        try {

            const payload = {

                expense_category_id:
                    form.value
                        .expense_category_id,

                name:
                    form.value.name,

                payee_name:
                    form.value.payee_name,

                amount:
                    form.value.amount,

                frequency:
                    form.value.frequency,

                start_date:
                    form.value.start_date,

                end_date:
                    form.value.end_date ||
                    null,

                next_due_date:
                    form.value.next_due_date,

                payment_method:
                    form.value.payment_method,

                description:
                    form.value.description ||
                    null,

                auto_create:
                    form.value.auto_create,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/recurring-expenses/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        "/api/recurring-expenses",
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                "Recurring expense saved successfully.";


            showModal.value = false;


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                "Save recurring error:",
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
                    "Unable to save recurring expense.";

            }

        } finally {

            saving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Generate Expense
|--------------------------------------------------------------------------
*/

const generateExpense =
    async (
        item
    ) => {

        if (
            !window.confirm(
                `Generate an expense for "${item.name}" of ₹${formatAmount(item.amount)}?`
            )
        ) {

            return;

        }


        actionId.value =
            item.id;

        actionType.value =
            "generate";

        error.value = "";

        success.value = "";


        try {

            const response =
                await axios.post(
                    `/api/recurring-expenses/${item.id}/generate`,
                    {},
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                "Expense generated successfully.";


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                "Generate expense error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to generate expense.";

        } finally {

            actionId.value = null;

            actionType.value = "";

        }

    };


/*
|--------------------------------------------------------------------------
| Change Status
|--------------------------------------------------------------------------
*/

const changeStatus =
    async (
        item,
        status
    ) => {

        actionId.value =
            item.id;

        actionType.value =
            "status";

        error.value = "";

        success.value = "";


        try {

            const response =
                await axios.post(
                    `/api/recurring-expenses/${item.id}/status`,
                    {
                        status
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                "Status updated successfully.";


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                "Status error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to update status.";

        } finally {

            actionId.value = null;

            actionType.value = "";

        }

    };


/*
|--------------------------------------------------------------------------
| Toggle Auto Create
|--------------------------------------------------------------------------
*/

const toggleAutoCreate =
    async (
        item
    ) => {

        actionId.value =
            item.id;

        actionType.value =
            "auto";

        error.value = "";

        success.value = "";


        try {

            const response =
                await axios.post(
                    `/api/recurring-expenses/${item.id}/toggle-auto-create`,
                    {},
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                "Auto-create setting updated.";


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                "Auto-create error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to update auto-create setting.";


            await loadRecurring(
                pagination.value.current_page
            );

        } finally {

            actionId.value = null;

            actionType.value = "";

        }

    };


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteRecurring =
    async (
        item
    ) => {

        if (
            !window.confirm(
                `Delete "${item.name}"?`
            )
        ) {

            return;

        }


        actionId.value =
            item.id;

        actionType.value =
            "delete";

        error.value = "";

        success.value = "";


        try {

            const response =
                await axios.delete(
                    `/api/recurring-expenses/${item.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                "Recurring expense deleted successfully.";


            let page =
                pagination.value.current_page;


            if (
                recurringExpenses.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadRecurring(page);

        } catch (err) {

            console.error(
                "Delete recurring error:",
                err
            );


            error.value =
                err.response?.data?.message ||
                "Unable to delete recurring expense.";

        } finally {

            actionId.value = null;

            actionType.value = "";

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

            search: "",

            expense_category_id: "",

            frequency: "",

            status: "",

            auto_create: "",

            per_page: 20

        };


        loadRecurring(1);

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


        loadRecurring(page);

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


const isDue =
    (item) => {

        if (
            !item.next_due_date ||
            item.status !== "Active"
        ) {

            return false;

        }


        const todayDate =
            new Date();

        todayDate.setHours(
            0,
            0,
            0,
            0
        );


        const dueDate =
            new Date(
                item.next_due_date
            );

        dueDate.setHours(
            0,
            0,
            0,
            0
        );


        return dueDate <= todayDate;

    };


const statusClass =
    (status) => {

        if (
            status === "Active"
        ) {

            return "bg-success";

        }


        if (
            status === "Paused"
        ) {

            return "bg-warning text-dark";

        }


        if (
            status === "Completed"
        ) {

            return "bg-secondary";

        }


        return "bg-secondary";

    };


const truncate =
    (
        text,
        length = 50
    ) => {

        if (!text) {
            return "";
        }


        return text.length > length
            ? text.substring(
                0,
                length
            ) + "..."
            : text;

    };


/*
|--------------------------------------------------------------------------
| Initial
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadCategories();

        await loadRecurring(1);

    }
);

</script>