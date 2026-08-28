<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Suppliers
                </h4>

                <small class="text-muted">
                    Manage inventory suppliers
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Supplier
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


        <!-- Search -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Search name, code, phone, email..."
                            @keyup.enter="loadSuppliers(1)"
                        >

                    </div>


                    <div class="col-md-3">

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


                    <div class="col-md-4 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadSuppliers(1)"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Supplier Table -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Supplier List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Contact Person
                                </th>

                                <th>
                                    Phone
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Location
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
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div
                                        class="text-muted mt-2"
                                    >
                                        Loading suppliers...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    suppliers.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-building fs-2 d-block mb-2"
                                    ></i>

                                    No inventory suppliers found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    supplier,
                                    index
                                ) in suppliers"
                                :key="supplier.id"
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
                                        {{ supplier.name }}
                                    </strong>

                                </td>


                                <td>
                                    {{ supplier.code || '-' }}
                                </td>


                                <td>
                                    {{
                                        supplier.contact_person ||
                                        '-'
                                    }}
                                </td>


                                <td>
                                    {{ supplier.phone || '-' }}
                                </td>


                                <td>
                                    {{ supplier.email || '-' }}
                                </td>


                                <td>

                                    <span
                                        v-if="
                                            supplier.city ||
                                            supplier.state
                                        "
                                    >
                                        {{
                                            [
                                                supplier.city,
                                                supplier.state
                                            ]
                                                .filter(Boolean)
                                                .join(', ')
                                        }}
                                    </span>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            supplier.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >

                                        {{
                                            supplier.status
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
                                        :disabled="
                                            actionId === supplier.id
                                        "
                                        @click="
                                            openEdit(supplier)
                                        "
                                    >
                                        <i
                                            class="bi bi-pencil"
                                        ></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        :disabled="
                                            actionId === supplier.id
                                        "
                                        @click="
                                            toggleStatus(supplier)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === supplier.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-toggle-on"
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId === supplier.id
                                        "
                                        @click="
                                            deleteSupplier(supplier)
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


        <!-- Add / Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveSupplier"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Inventory Supplier'
                                        : 'Add Inventory Supplier'
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

                            <!-- Form Error -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Supplier Name -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Supplier Name

                                        <span
                                            class="text-danger"
                                        >
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
                                        placeholder="Enter supplier name"
                                    >


                                    <div
                                        v-if="formErrors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.name }}
                                    </div>

                                </div>


                                <!-- Code -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Supplier Code
                                    </label>


                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="SUP-001"
                                    >


                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-3">

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


                                <!-- Contact Person -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Contact Person
                                    </label>


                                    <input
                                        v-model="
                                            form.contact_person
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.contact_person
                                        }"
                                        placeholder="Contact person's name"
                                    >


                                    <div
                                        v-if="
                                            formErrors.contact_person
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.contact_person
                                        }}
                                    </div>

                                </div>


                                <!-- Phone -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Phone
                                    </label>


                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.phone
                                        }"
                                        placeholder="Phone number"
                                    >


                                    <div
                                        v-if="formErrors.phone"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.phone }}
                                    </div>

                                </div>


                                <!-- Email -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Email
                                    </label>


                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.email
                                        }"
                                        placeholder="supplier@example.com"
                                    >


                                    <div
                                        v-if="formErrors.email"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.email }}
                                    </div>

                                </div>


                                <!-- Address -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Address
                                    </label>


                                    <textarea
                                        v-model="form.address"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.address
                                        }"
                                        rows="2"
                                        placeholder="Full address"
                                    ></textarea>


                                    <div
                                        v-if="formErrors.address"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.address }}
                                    </div>

                                </div>


                                <!-- City -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        City
                                    </label>


                                    <input
                                        v-model="form.city"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.city
                                        }"
                                        placeholder="City"
                                    >


                                    <div
                                        v-if="formErrors.city"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.city }}
                                    </div>

                                </div>


                                <!-- State -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        State
                                    </label>


                                    <input
                                        v-model="form.state"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.state
                                        }"
                                        placeholder="State"
                                    >


                                    <div
                                        v-if="formErrors.state"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.state }}
                                    </div>

                                </div>


                                <!-- Country -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Country
                                    </label>


                                    <input
                                        v-model="form.country"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.country
                                        }"
                                        placeholder="Country"
                                    >


                                    <div
                                        v-if="formErrors.country"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.country }}
                                    </div>

                                </div>


                                <!-- Tax Number -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Tax Number
                                    </label>


                                    <input
                                        v-model="
                                            form.tax_number
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.tax_number
                                        }"
                                        placeholder="GST / Tax number"
                                    >


                                    <div
                                        v-if="
                                            formErrors.tax_number
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.tax_number }}
                                    </div>

                                </div>


                                <!-- Notes -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Notes
                                    </label>


                                    <textarea
                                        v-model="form.notes"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.notes
                                        }"
                                        rows="3"
                                        placeholder="Additional notes..."
                                    ></textarea>


                                    <div
                                        v-if="formErrors.notes"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.notes }}
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
                                        ? 'Update Supplier'
                                        : 'Save Supplier'
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
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const suppliers =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const actionId =
    ref(null);

const showModal =
    ref(false);

const editing =
    ref(false);

const editingId =
    ref(null);

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

        status: '',

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
| Empty Form
|--------------------------------------------------------------------------
*/

const emptyForm =
    () => ({

        name: '',

        code: '',

        contact_person: '',

        phone: '',

        email: '',

        address: '',

        city: '',

        state: '',

        country: '',

        tax_number: '',

        notes: '',

        status: true

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

                Accept:
                    'application/json'

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
| Load Suppliers
|--------------------------------------------------------------------------
*/

const loadSuppliers =
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

                status:
                    filters.value.status === ''
                        ? undefined
                        : filters.value.status

            };


            const response =
                await axios.get(

                    '/api/inventory/suppliers',

                    {

                        ...getAuthConfig(),

                        params

                    }

                );


            const result =
                response.data?.data;


            suppliers.value =
                result?.data ||
                [];


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
                'Inventory supplier loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load inventory suppliers.';

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
    (supplier) => {

        editing.value = true;

        editingId.value =
            supplier.id;


        form.value = {

            name:
                supplier.name ||
                '',

            code:
                supplier.code ||
                '',

            contact_person:
                supplier.contact_person ||
                '',

            phone:
                supplier.phone ||
                '',

            email:
                supplier.email ||
                '',

            address:
                supplier.address ||
                '',

            city:
                supplier.city ||
                '',

            state:
                supplier.state ||
                '',

            country:
                supplier.country ||
                '',

            tax_number:
                supplier.tax_number ||
                '',

            notes:
                supplier.notes ||
                '',

            status:
                Boolean(
                    supplier.status
                )

        };


        formErrors.value = {};

        formError.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Close Modal
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
| Save Supplier
|--------------------------------------------------------------------------
*/

const saveSupplier =
    async () => {

        if (saving.value) {

            return;

        }


        saving.value = true;

        formErrors.value = {};

        formError.value = '';

        error.value = '';

        success.value = '';


        try {

            const payload = {

                name:
                    form.value.name,

                code:
                    form.value.code ||
                    null,

                contact_person:
                    form.value.contact_person ||
                    null,

                phone:
                    form.value.phone ||
                    null,

                email:
                    form.value.email ||
                    null,

                address:
                    form.value.address ||
                    null,

                city:
                    form.value.city ||
                    null,

                state:
                    form.value.state ||
                    null,

                country:
                    form.value.country ||
                    null,

                tax_number:
                    form.value.tax_number ||
                    null,

                notes:
                    form.value.notes ||
                    null,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(

                        `/api/inventory/suppliers/${editingId.value}`,

                        payload,

                        getAuthConfig()

                    );

            } else {

                response =
                    await axios.post(

                        '/api/inventory/suppliers',

                        payload,

                        getAuthConfig()

                    );

            }


            success.value =
                response.data?.message ||
                'Inventory supplier saved successfully.';


            showModal.value = false;


            await loadSuppliers(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory supplier save error:',
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
                    'Unable to save inventory supplier.';

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

const toggleStatus =
    async (
        supplier
    ) => {

        const newStatus =
            !Boolean(
                supplier.status
            );


        if (
            !window.confirm(

                `Change "${supplier.name}" status to ${
                    newStatus
                        ? 'Active'
                        : 'Inactive'
                }?`

            )
        ) {

            return;

        }


        actionId.value =
            supplier.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(

                    `/api/inventory/suppliers/${supplier.id}/status`,

                    {

                        status:
                            newStatus

                    },

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory supplier status updated successfully.';


            await loadSuppliers(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory supplier status error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update inventory supplier status.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete Supplier
|--------------------------------------------------------------------------
*/

const deleteSupplier =
    async (
        supplier
    ) => {

        if (
            !window.confirm(

                `Delete inventory supplier "${supplier.name}"?`

            )
        ) {

            return;

        }


        actionId.value =
            supplier.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(

                    `/api/inventory/suppliers/${supplier.id}`,

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory supplier deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                suppliers.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadSuppliers(page);


        } catch (err) {

            console.error(
                'Delete inventory supplier error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete inventory supplier.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters =
    () => {

        filters.value = {

            search: '',

            status: '',

            per_page: 20

        };


        loadSuppliers(1);

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


        loadSuppliers(page);

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadSuppliers(1);

    }
);

</script>