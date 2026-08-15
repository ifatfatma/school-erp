<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Late Fees
                </h4>

                <small class="text-muted">
                    Manage late fee rules
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Late Fee
            </button>

        </div>


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>


        <!-- Success -->
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
                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Late fee name / code"
                            @keyup.enter="loadLateFees(1)"
                        >

                    </div>


                    <!-- Type -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Late Fee Type
                        </label>

                        <select
                            v-model="filters.late_fee_type"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Fixed">
                                Fixed
                            </option>

                            <option value="Percentage">
                                Percentage
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

                            <option value="1">
                                Active
                            </option>

                            <option value="0">
                                Inactive
                            </option>

                        </select>

                    </div>


                    <!-- Per Page -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Per Page
                        </label>

                        <select
                            v-model="filters.per_page"
                            class="form-select"
                        >

                            <option :value="10">
                                10
                            </option>

                            <option :value="20">
                                20
                            </option>

                            <option :value="50">
                                50
                            </option>

                            <option :value="100">
                                100
                            </option>

                        </select>

                    </div>

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadLateFees(1)"
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


        <!-- List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Late Fee List
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
                                    Name
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Value
                                </th>

                                <th>
                                    Grace Days
                                </th>

                                <th>
                                    Calculation
                                </th>

                                <th>
                                    Maximum
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="180"
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

                                    <div class="mt-2 text-muted">
                                        Loading late fees...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="lateFees.length === 0"
                            >

                                <td
                                    colspan="10"
                                    class="text-center py-5 text-muted"
                                >
                                    No late fees found.
                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-else
                                v-for="(
                                    lateFee,
                                    index
                                ) in lateFees"
                                :key="lateFee.id"
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
                                        {{ lateFee.name }}
                                    </strong>

                                    <div
                                        v-if="
                                            lateFee.description
                                        "
                                        class="small text-muted"
                                    >
                                        {{ lateFee.description }}
                                    </div>

                                </td>


                                <td>

                                    <span
                                        v-if="lateFee.code"
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ lateFee.code }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            lateFee.late_fee_type ===
                                            'Percentage'
                                                ? 'bg-info'
                                                : 'bg-primary'
                                        "
                                    >
                                        {{ lateFee.late_fee_type }}
                                    </span>

                                </td>


                                <td>

                                    <strong>
                                        {{ formatValue(lateFee) }}
                                    </strong>

                                </td>


                                <td>

                                    <span class="badge bg-light text-dark border">

                                        {{ lateFee.grace_days }}

                                        {{
                                            lateFee.grace_days === 1
                                                ? 'Day'
                                                : 'Days'
                                        }}

                                    </span>

                                </td>


                                <td>

                                    <span
                                        v-if="lateFee.per_day"
                                        class="badge bg-warning text-dark"
                                    >
                                        Per Day
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        One Time
                                    </span>

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            lateFee.max_amount !==
                                            null &&
                                            lateFee.max_amount !==
                                            undefined
                                        "
                                    >
                                        ₹{{
                                            formatAmount(
                                                lateFee.max_amount
                                            )
                                        }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        No limit
                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            lateFee.status
                                                ? 'bg-success'
                                                : 'bg-danger'
                                        "
                                    >

                                        {{
                                            lateFee.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}

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
                                                lateFee
                                            )
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Toggle -->
                                    <button
                                        type="button"
                                        class="btn btn-sm"
                                        :class="
                                            lateFee.status
                                                ? 'btn-outline-warning'
                                                : 'btn-outline-success'
                                        "
                                        :title="
                                            lateFee.status
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                        :disabled="
                                            actionId ===
                                            lateFee.id
                                        "
                                        @click="
                                            toggleStatus(
                                                lateFee
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                lateFee.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi"
                                            :class="
                                                lateFee.status
                                                    ? 'bi-toggle-off'
                                                    : 'bi-toggle-on'
                                            "
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger ms-1"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            lateFee.id
                                        "
                                        @click="
                                            deleteLateFee(
                                                lateFee
                                            )
                                        "
                                    >
                                        <i class="bi bi-trash"></i>
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


        <!-- Add / Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form @submit.prevent="saveLateFee">

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Late Fee'
                                        : 'Add Late Fee'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                @click="closeModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <!-- Form Error -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Name -->
                                <div class="col-md-8">

                                    <label class="form-label">

                                        Late Fee Name
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="e.g. Monthly Late Fee"
                                    >

                                    <div
                                        v-if="formErrors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.name }}
                                    </div>

                                </div>


                                <!-- Code -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Code
                                    </label>

                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="e.g. LATE100"
                                    >

                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
                                    </div>

                                </div>


                                <!-- Type -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Late Fee Type
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="
                                            form.late_fee_type
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.late_fee_type
                                        }"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Fixed">
                                            Fixed
                                        </option>

                                        <option value="Percentage">
                                            Percentage
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.late_fee_type
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.late_fee_type
                                        }}
                                    </div>

                                </div>


                                <!-- Value -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Value
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <div class="input-group">

                                        <input
                                            v-model="form.value"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    formErrors.value
                                            }"
                                            :placeholder="
                                                form.late_fee_type ===
                                                'Percentage'
                                                    ? 'e.g. 2'
                                                    : 'e.g. 100'
                                            "
                                        >

                                        <span
                                            v-if="
                                                form.late_fee_type ===
                                                'Percentage'
                                            "
                                            class="input-group-text"
                                        >
                                            %
                                        </span>

                                        <span
                                            v-else-if="
                                                form.late_fee_type ===
                                                'Fixed'
                                            "
                                            class="input-group-text"
                                        >
                                            ₹
                                        </span>

                                    </div>

                                    <div
                                        v-if="formErrors.value"
                                        class="text-danger small mt-1"
                                    >
                                        {{ formErrors.value }}
                                    </div>

                                </div>


                                <!-- Grace Days -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Grace Days
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.grace_days"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.grace_days
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.grace_days
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.grace_days
                                        }}
                                    </div>

                                </div>


                                <!-- Per Day -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Calculation
                                    </label>

                                    <select
                                        v-model="form.per_day"
                                        class="form-select"
                                    >

                                        <option :value="false">
                                            One Time
                                        </option>

                                        <option :value="true">
                                            Per Day
                                        </option>

                                    </select>

                                </div>


                                <!-- Maximum -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Maximum Amount
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model="
                                                form.max_amount
                                            "
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    formErrors.max_amount
                                            }"
                                            placeholder="Optional"
                                        >

                                    </div>

                                    <div
                                        v-if="
                                            formErrors.max_amount
                                        "
                                        class="text-danger small mt-1"
                                    >
                                        {{
                                            formErrors.max_amount
                                        }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="form.status"
                                        class="form-select"
                                    >

                                        <option :value="true">
                                            Active
                                        </option>

                                        <option :value="false">
                                            Inactive
                                        </option>

                                    </select>

                                </div>


                                <!-- Description -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="
                                            form.description
                                        "
                                        rows="3"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.description
                                        }"
                                        placeholder="Optional description"
                                    ></textarea>

                                    <div
                                        v-if="
                                            formErrors.description
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.description
                                        }}
                                    </div>

                                </div>


                                <!-- Preview -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Preview
                                    </label>

                                    <div class="border rounded p-3 bg-light">

                                        <div
                                            v-if="
                                                form.late_fee_type
                                            "
                                        >

                                            <strong>
                                                {{ form.name || 'Late Fee' }}
                                            </strong>

                                            <div class="mt-2">

                                                {{
                                                    previewValue
                                                }}

                                            </div>

                                            <div class="small text-muted mt-1">

                                                After
                                                {{
                                                    form.grace_days || 0
                                                }}
                                                grace day(s)

                                            </div>

                                            <div
                                                v-if="
                                                    form.per_day
                                                "
                                                class="badge bg-warning text-dark mt-2"
                                            >
                                                Per Day
                                            </div>

                                        </div>

                                        <span
                                            v-else
                                            class="text-muted"
                                        >
                                            Select a late fee type
                                        </span>

                                    </div>

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
                                        ? 'Update Late Fee'
                                        : 'Save Late Fee'
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

const lateFees = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const error = ref("");

const success = ref("");

const formError = ref("");

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    late_fee_type: "",

    status: "",

    per_page: 20

});


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    name: "",

    code: "",

    late_fee_type: "",

    value: "",

    grace_days: 0,

    max_amount: "",

    per_day: false,

    description: "",

    status: true

});


const form = ref(
    emptyForm()
);


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
| Load Late Fees
|--------------------------------------------------------------------------
*/

const loadLateFees = async (
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

            late_fee_type:
                filters.value.late_fee_type ||
                undefined,

            status:
                filters.value.status === ""
                    ? undefined
                    : filters.value.status

        };


        const response = await axios.get(
            "/api/late-fees",
            {
                ...getAuthConfig(),
                params
            }
        );


        const result =
            response.data?.data;


        lateFees.value =
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
            "Late fee list error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load late fees.";

    } finally {

        loading.value = false;

    }

};


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

    error.value = "";

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = (
    lateFee
) => {

    editing.value = true;

    editingId.value =
        lateFee.id;


    form.value = {

        name:
            lateFee.name || "",

        code:
            lateFee.code || "",

        late_fee_type:
            lateFee.late_fee_type || "",

        value:
            lateFee.value ?? "",

        grace_days:
            lateFee.grace_days ?? 0,

        max_amount:
            lateFee.max_amount ?? "",

        per_day:
            Boolean(lateFee.per_day),

        description:
            lateFee.description || "",

        status:
            Boolean(lateFee.status)

    };


    formErrors.value = {};

    formError.value = "";

    error.value = "";

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
| Save
|--------------------------------------------------------------------------
*/

const saveLateFee = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = "";

    error.value = "";

    success.value = "";


    try {

        const payload = {

            name:
                form.value.name,

            code:
                form.value.code ||
                null,

            late_fee_type:
                form.value.late_fee_type,

            value:
                form.value.value,

            grace_days:
                form.value.grace_days,

            max_amount:
                form.value.max_amount ||
                null,

            per_day:
                Boolean(
                    form.value.per_day
                ),

            description:
                form.value.description ||
                null,

            status:
                Boolean(
                    form.value.status
                )

        };


        let response;


        if (editing.value) {

            response = await axios.put(
                `/api/late-fees/${editingId.value}`,
                payload,
                getAuthConfig()
            );

        } else {

            response = await axios.post(
                "/api/late-fees",
                payload,
                getAuthConfig()
            );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? "Late fee updated successfully."
                    : "Late fee created successfully."
            );


        showModal.value = false;


        await loadLateFees(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Save late fee error:",
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
                "Unable to save late fee.";

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

const toggleStatus = async (
    lateFee
) => {

    const action =
        lateFee.status
            ? "deactivate"
            : "activate";


    if (
        !window.confirm(
            `Are you sure you want to ${action} this late fee?`
        )
    ) {

        return;

    }


    actionId.value =
        lateFee.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.patch(
                `/api/late-fees/${lateFee.id}/toggle-status`,
                {},
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Status updated successfully.";


        await loadLateFees(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Toggle late fee error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to update status.";

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteLateFee = async (
    lateFee
) => {

    if (
        !window.confirm(
            `Delete "${lateFee.name}"?`
        )
    ) {

        return;

    }


    actionId.value =
        lateFee.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(
                `/api/late-fees/${lateFee.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Late fee deleted successfully.";


        let page =
            pagination.value.current_page;


        if (
            lateFees.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadLateFees(page);

    } catch (err) {

        console.error(
            "Delete late fee error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to delete late fee.";

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: "",

        late_fee_type: "",

        status: "",

        per_page: 20

    };


    loadLateFees(1);

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


    loadLateFees(page);

};


/*
|--------------------------------------------------------------------------
| Helpers
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


const formatValue = (
    lateFee
) => {

    if (
        lateFee.late_fee_type ===
        "Percentage"
    ) {

        return `${Number(
            lateFee.value || 0
        )}%`;

    }


    return `₹${formatAmount(
        lateFee.value
    )}`;

};


/*
|--------------------------------------------------------------------------
| Preview
|--------------------------------------------------------------------------
*/

const previewValue = computed(() => {

    if (
        !form.value.late_fee_type ||
        !form.value.value
    ) {

        return "-";

    }


    if (
        form.value.late_fee_type ===
        "Percentage"
    ) {

        return `${form.value.value}%`;

    }


    return `₹${formatAmount(
        form.value.value
    )}`;

});


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadLateFees(1);

});

</script>