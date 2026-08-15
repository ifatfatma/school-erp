<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Recurring Income
                </h4>

                <small class="text-muted">
                    Manage automatic and scheduled income
                </small>
            </div>

            <div>

                <button
                    type="button"
                    class="btn btn-outline-primary me-2"
                    :disabled="generatingDue"
                    @click="generateDue"
                >
                    <span
                        v-if="generatingDue"
                        class="spinner-border spinner-border-sm me-1"
                    ></span>

                    <i
                        v-else
                        class="bi bi-arrow-repeat me-1"
                    ></i>

                    Generate Due
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    @click="openCreate"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Recurring Income
                </button>

            </div>

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

                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Name, reference, received from..."
                            @keyup.enter="loadRecurring(1)"
                        >

                    </div>


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

                            <option value="Cancelled">
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Due
                        </label>

                        <select
                            v-model="filters.due"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="1">
                                Due Only
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadRecurring(1)"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <strong>
                    Recurring Income List
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

                                <th width="50">
                                    #
                                </th>

                                <th>
                                    Name
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Received From
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Frequency
                                </th>

                                <th>
                                    Next Date
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

                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div class="spinner-border text-primary"></div>

                                    <div class="text-muted mt-2">
                                        Loading...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    recurringIncomes.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-inbox fs-2 d-block mb-2"
                                    ></i>

                                    No recurring income found.

                                </td>

                            </tr>


                            <tr
                                v-else
                                v-for="(
                                    item,
                                    index
                                ) in recurringIncomes"
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

                                    <small
                                        v-if="
                                            item.reference_no
                                        "
                                        class="d-block text-muted"
                                    >
                                        Ref:
                                        {{ item.reference_no }}
                                    </small>

                                </td>


                                <td>
                                    {{
                                        item.category?.name ||
                                        '-'
                                    }}
                                </td>


                                <td>
                                    {{
                                        item.received_from ||
                                        '-'
                                    }}
                                </td>


                                <td>

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                item.amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span class="badge bg-info text-dark">
                                        {{ item.frequency }}
                                    </span>

                                </td>


                                <td>

                                    <span
                                        :class="{
                                            'text-danger fw-bold':
                                                isDue(item)
                                        }"
                                    >
                                        {{
                                            formatDate(
                                                item.next_date
                                            )
                                        }}
                                    </span>

                                    <span
                                        v-if="isDue(item)"
                                        class="badge bg-danger ms-1"
                                    >
                                        Due
                                    </span>

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
                                        v-if="
                                            item.status === 'Active' &&
                                            item.auto_create &&
                                            isDue(item)
                                        "
                                        type="button"
                                        class="btn btn-sm btn-success me-1"
                                        title="Generate Income"
                                        :disabled="
                                            actionId === item.id
                                        "
                                        @click="
                                            generateIncome(
                                                item
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === item.id
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
                                            openEdit(item)
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
                                            openStatus(item)
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
                                            actionId === item.id
                                        "
                                        @click="
                                            deleteRecurring(item)
                                        "
                                    >

                                        <i
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

                    <span class="text-muted">

                        Showing
                        {{ pagination.from || 0 }}
                        to
                        {{ pagination.to || 0 }}
                        of
                        {{ pagination.total }}

                    </span>


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
                        @submit.prevent="saveRecurring"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Recurring Income'
                                        : 'Add Recurring Income'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
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


                                <!-- Name -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        placeholder="Monthly Rent"
                                    >

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
                                        placeholder="Person / Company"
                                    >

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
                                        placeholder="Reference number"
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
                                            v-model="form.amount"
                                            type="number"
                                            min="0.01"
                                            step="0.01"
                                            class="form-control"
                                        >

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


                                <!-- Frequency -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Frequency
                                        <span class="text-danger">*</span>
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


                                <!-- Start -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Start Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.start_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Next -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Next Date
                                    </label>

                                    <input
                                        v-model="
                                            form.next_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Leave empty to calculate automatically.
                                    </small>

                                </div>


                                <!-- End -->
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


                                <!-- Auto Create -->
                                <div class="col-md-6">

                                    <div class="form-check form-switch mt-4">

                                        <input
                                            id="autoCreate"
                                            v-model="
                                                form.auto_create
                                            "
                                            class="form-check-input"
                                            type="checkbox"
                                        >

                                        <label
                                            for="autoCreate"
                                            class="form-check-label"
                                        >
                                            Automatically create income
                                        </label>

                                    </div>

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

                                        <option value="Active">
                                            Active
                                        </option>

                                        <option value="Paused">
                                            Paused
                                        </option>

                                        <option value="Completed">
                                            Completed
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
                                        class="form-control"
                                        rows="3"
                                        placeholder="Description..."
                                    ></textarea>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
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
                                        ? 'Update'
                                        : 'Save'
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

                            <option value="Active">
                                Active
                            </option>

                            <option value="Paused">
                                Paused
                            </option>

                            <option value="Completed">
                                Completed
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

const recurringIncomes =
    ref([]);

const categories =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const statusSaving =
    ref(false);

const generatingDue =
    ref(false);

const actionId =
    ref(null);

const editing =
    ref(false);

const editingId =
    ref(null);

const statusId =
    ref(null);

const showModal =
    ref(false);

const showStatusModal =
    ref(false);

const error =
    ref('');

const success =
    ref('');

const formError =
    ref('');

const formErrors =
    ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: '',

        frequency: '',

        status: '',

        due: '',

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

const emptyForm =
    () => ({

        income_category_id: '',

        name: '',

        reference_no: '',

        received_from: '',

        amount: '',

        payment_method: 'Cash',

        frequency: 'Monthly',

        start_date:
            new Date()
                .toISOString()
                .substring(0, 10),

        end_date: '',

        next_date: '',

        auto_create: true,

        status: 'Active',

        description: ''

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

        status: 'Active'

    });


/*
|--------------------------------------------------------------------------
| Axios config
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
| Load Recurring Income
|--------------------------------------------------------------------------
*/

const loadRecurring =
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

                frequency:
                    filters.value.frequency ||
                    undefined,

                status:
                    filters.value.status ||
                    undefined,

                due:
                    filters.value.due ||
                    undefined

            };


            const response =
                await axios.get(
                    '/api/recurring-incomes',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const result =
                response.data?.data;


            recurringIncomes.value =
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

        } catch (err) {

            console.error(
                'Recurring income loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load recurring income.';

        } finally {

            loading.value = false;

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
    (item) => {

        editing.value = true;

        editingId.value =
            item.id;


        form.value = {

            income_category_id:
                item.income_category_id ||
                '',

            name:
                item.name ||
                '',

            reference_no:
                item.reference_no ||
                '',

            received_from:
                item.received_from ||
                '',

            amount:
                item.amount ||
                '',

            payment_method:
                item.payment_method ||
                'Cash',

            frequency:
                item.frequency ||
                'Monthly',

            start_date:
                dateValue(
                    item.start_date
                ),

            end_date:
                dateValue(
                    item.end_date
                ),

            next_date:
                dateValue(
                    item.next_date
                ),

            auto_create:
                Boolean(
                    item.auto_create
                ),

            status:
                item.status ||
                'Active',

            description:
                item.description ||
                ''

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

const saveRecurring =
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

                name:
                    form.value.name,

                reference_no:
                    form.value.reference_no ||
                    null,

                received_from:
                    form.value.received_from,

                amount:
                    form.value.amount,

                payment_method:
                    form.value.payment_method,

                frequency:
                    form.value.frequency,

                start_date:
                    form.value.start_date,

                end_date:
                    form.value.end_date ||
                    null,

                next_date:
                    form.value.next_date ||
                    null,

                auto_create:
                    form.value.auto_create,

                status:
                    form.value.status,

                description:
                    form.value.description ||
                    null

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/recurring-incomes/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        '/api/recurring-incomes',
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                'Recurring income saved successfully.';


            showModal.value =
                false;


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Save recurring income error:',
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
                    'Unable to save recurring income.';

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
    (item) => {

        statusId.value =
            item.id;

        statusForm.value.status =
            item.status ||
            'Active';

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
                    `/api/recurring-incomes/${statusId.value}/status`,
                    {
                        status:
                            statusForm.value.status
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Status updated successfully.';


            showStatusModal.value =
                false;


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to update status.';

        } finally {

            statusSaving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Generate one
|--------------------------------------------------------------------------
*/

const generateIncome =
    async (
        item
    ) => {

        if (
            !window.confirm(
                `Generate income for "${item.name}"?`
            )
        ) {
            return;
        }


        actionId.value =
            item.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(
                    `/api/recurring-incomes/${item.id}/generate`,
                    {},
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Income generated successfully.';


            await loadRecurring(
                pagination.value.current_page
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to generate income.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Generate Due
|--------------------------------------------------------------------------
*/

const generateDue =
    async () => {

        if (
            !window.confirm(
                'Generate all due recurring incomes?'
            )
        ) {
            return;
        }


        generatingDue.value =
            true;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(
                    '/api/recurring-incomes/generate-due',
                    {},
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Due incomes generated successfully.';


            await loadRecurring(
                1
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to generate due incomes.';

        } finally {

            generatingDue.value =
                false;

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


        try {

            const response =
                await axios.delete(
                    `/api/recurring-incomes/${item.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Recurring income deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                recurringIncomes.value.length === 1 &&
                page > 1
            ) {
                page--;
            }


            await loadRecurring(
                page
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to delete recurring income.';

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

            frequency: '',

            status: '',

            due: '',

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

const dateValue =
    (date) => {

        if (!date) {
            return '';
        }

        return String(
            date
        ).substring(
            0,
            10
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


const isDue =
    (item) => {

        if (
            !item.next_date ||
            item.status !== 'Active' ||
            !item.auto_create
        ) {
            return false;
        }

        const today =
            new Date();

        today.setHours(
            0,
            0,
            0,
            0
        );

        const next =
            new Date(
                item.next_date
            );

        next.setHours(
            0,
            0,
            0,
            0
        );

        return next <= today;

    };


const statusClass =
    (status) => {

        switch (status) {

            case 'Active':
                return 'bg-success';

            case 'Paused':
                return 'bg-warning text-dark';

            case 'Completed':
                return 'bg-primary';

            case 'Cancelled':
                return 'bg-danger';

            default:
                return 'bg-secondary';

        }

    };


/*
|--------------------------------------------------------------------------
| Initial load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadCategories();

        await loadRecurring(1);

    }
);

</script>