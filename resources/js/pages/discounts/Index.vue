<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Discounts
                </h4>

                <small class="text-muted">
                    Manage fee discounts
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Discount
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
                            placeholder="Discount name / code"
                            @keyup.enter="loadDiscounts(1)"
                        >

                    </div>


                    <!-- Type -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Discount Type
                        </label>

                        <select
                            v-model="filters.discount_type"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Percentage">
                                Percentage
                            </option>

                            <option value="Fixed">
                                Fixed
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
                        @click="loadDiscounts(1)"
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
                    Discount List
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
                                    Maximum Discount
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
                                    colspan="8"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="mt-2 text-muted">
                                        Loading discounts...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="discounts.length === 0"
                            >

                                <td
                                    colspan="8"
                                    class="text-center py-5 text-muted"
                                >
                                    No discounts found.
                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-else
                                v-for="(
                                    discount,
                                    index
                                ) in discounts"
                                :key="discount.id"
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
                                        {{ discount.name }}
                                    </strong>

                                    <div
                                        v-if="discount.description"
                                        class="small text-muted"
                                    >
                                        {{ discount.description }}
                                    </div>

                                </td>


                                <td>

                                    <span
                                        v-if="discount.code"
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ discount.code }}
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
                                            discount.discount_type ===
                                            'Percentage'
                                                ? 'bg-info'
                                                : 'bg-primary'
                                        "
                                    >

                                        {{
                                            discount.discount_type
                                        }}

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        {{
                                            formatValue(
                                                discount
                                            )
                                        }}

                                    </strong>

                                </td>


                                <td>

                                    <template
                                        v-if="
                                            discount.discount_type ===
                                            'Percentage'
                                        "
                                    >

                                        <span
                                            v-if="
                                                discount.max_discount !==
                                                null &&
                                                discount.max_discount !==
                                                undefined
                                            "
                                        >

                                            ₹{{
                                                formatAmount(
                                                    discount.max_discount
                                                )
                                            }}

                                        </span>

                                        <span
                                            v-else
                                            class="text-muted"
                                        >
                                            No limit
                                        </span>

                                    </template>

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
                                            discount.status
                                                ? 'bg-success'
                                                : 'bg-danger'
                                        "
                                    >

                                        {{
                                            discount.status
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
                                                discount
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
                                            discount.status
                                                ? 'btn-outline-warning'
                                                : 'btn-outline-success'
                                        "
                                        :title="
                                            discount.status
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                        :disabled="
                                            actionId ===
                                            discount.id
                                        "
                                        @click="
                                            toggleStatus(
                                                discount
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                discount.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi"
                                            :class="
                                                discount.status
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
                                            discount.id
                                        "
                                        @click="
                                            deleteDiscount(
                                                discount
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


        <!-- Add/Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form @submit.prevent="saveDiscount">

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Discount'
                                        : 'Add Discount'
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

                                        Discount Name
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
                                        placeholder="Enter discount name"
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
                                        placeholder="e.g. SIBLING10"
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

                                        Discount Type
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="form.discount_type"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.discount_type
                                        }"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Percentage">
                                            Percentage
                                        </option>

                                        <option value="Fixed">
                                            Fixed
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.discount_type
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.discount_type
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
                                                form.discount_type ===
                                                'Percentage'
                                                    ? 'e.g. 10'
                                                    : 'e.g. 500'
                                            "
                                        >

                                        <span
                                            class="input-group-text"
                                            v-if="
                                                form.discount_type ===
                                                'Percentage'
                                            "
                                        >
                                            %
                                        </span>

                                        <span
                                            class="input-group-text"
                                            v-else-if="
                                                form.discount_type ===
                                                'Fixed'
                                            "
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


                                <!-- Maximum -->
                                <div
                                    v-if="
                                        form.discount_type ===
                                        'Percentage'
                                    "
                                    class="col-md-6"
                                >

                                    <label class="form-label">

                                        Maximum Discount

                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model="
                                                form.max_discount
                                            "
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    formErrors.max_discount
                                            }"
                                            placeholder="Optional"
                                        >

                                    </div>

                                    <div
                                        v-if="
                                            formErrors.max_discount
                                        "
                                        class="text-danger small mt-1"
                                    >
                                        {{
                                            formErrors.max_discount
                                        }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div
                                    class="col-md-6"
                                    :class="{
                                        'col-md-6':
                                            form.discount_type ===
                                            'Percentage'
                                    }"
                                >

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
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="form.description"
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
                                        ? 'Update Discount'
                                        : 'Save Discount'
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
    ref,
    onMounted
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const discounts = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const error = ref("");

const success = ref("");

const formError = ref("");

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);


/*
|--------------------------------------------------------------------------
| Form Errors
|--------------------------------------------------------------------------
*/

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    discount_type: "",

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

    discount_type: "",

    value: "",

    max_discount: "",

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
| Load Discounts
|--------------------------------------------------------------------------
*/

const loadDiscounts = async (
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
                filters.value.search || undefined,

            discount_type:
                filters.value.discount_type ||
                undefined,

            status:
                filters.value.status === ""
                    ? undefined
                    : filters.value.status

        };


        const response = await axios.get(
            "/api/discounts",
            {
                ...getAuthConfig(),
                params
            }
        );


        const result =
            response.data?.data;


        discounts.value =
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
            "Discount list error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load discounts.";

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
    discount
) => {

    editing.value = true;

    editingId.value =
        discount.id;


    form.value = {

        name:
            discount.name || "",

        code:
            discount.code || "",

        discount_type:
            discount.discount_type || "",

        value:
            discount.value ?? "",

        max_discount:
            discount.max_discount ?? "",

        description:
            discount.description || "",

        status:
            Boolean(
                discount.status
            )

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

const saveDiscount = async () => {

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
                form.value.code || null,

            discount_type:
                form.value.discount_type,

            value:
                form.value.value,

            max_discount:
                form.value.discount_type ===
                "Percentage"
                    ? (
                        form.value.max_discount
                        || null
                    )
                    : null,

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
                `/api/discounts/${editingId.value}`,
                payload,
                getAuthConfig()
            );

        } else {

            response = await axios.post(
                "/api/discounts",
                payload,
                getAuthConfig()
            );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? "Discount updated successfully."
                    : "Discount created successfully."
            );


        showModal.value = false;


        await loadDiscounts(
            pagination.value.current_page
        );


    } catch (err) {

        console.error(
            "Save discount error:",
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
                "Unable to save discount.";

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
    discount
) => {

    const action =
        discount.status
            ? "deactivate"
            : "activate";


    if (
        !window.confirm(
            `Are you sure you want to ${action} this discount?`
        )
    ) {

        return;

    }


    actionId.value =
        discount.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.patch(
                `/api/discounts/${discount.id}/toggle-status`,
                {},
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Status updated successfully.";


        await loadDiscounts(
            pagination.value.current_page
        );


    } catch (err) {

        console.error(
            "Toggle discount error:",
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

const deleteDiscount = async (
    discount
) => {

    if (
        !window.confirm(
            `Delete "${discount.name}"?`
        )
    ) {

        return;

    }


    actionId.value =
        discount.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(
                `/api/discounts/${discount.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Discount deleted successfully.";


        let page =
            pagination.value.current_page;


        /*
         * If deleting the last record
         * from the current page, go back.
         */
        if (
            discounts.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadDiscounts(
            page
        );


    } catch (err) {

        console.error(
            "Delete discount error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to delete discount.";

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

        discount_type: "",

        status: "",

        per_page: 20

    };


    loadDiscounts(1);

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


    loadDiscounts(page);

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
    discount
) => {

    if (
        discount.discount_type ===
        "Percentage"
    ) {

        return `${Number(
            discount.value || 0
        )}%`;

    }


    return `₹${formatAmount(
        discount.value
    )}`;

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadDiscounts(1);

});

</script>