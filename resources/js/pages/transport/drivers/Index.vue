<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Transport Drivers</h4>
                <small class="text-muted">
                    Manage school transport drivers
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Driver
            </button>
        </div>


        <!-- ALERTS -->
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


        <!-- FILTERS -->
        <div class="card mb-3">

            <div class="card-header">
                <strong>Search Drivers</strong>
            </div>

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
                            placeholder="Name / Code / Phone / License No."
                            @keyup.enter="loadDrivers(1)"
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
                                All Status
                            </option>

                            <option value="Active">
                                Active
                            </option>

                            <option value="Inactive">
                                Inactive
                            </option>
                        </select>

                    </div>


                    <div class="col-md-4 d-flex align-items-end gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="loading"
                            @click="loadDrivers(1)"
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
                            Reset
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            :disabled="loading"
                            @click="
                                loadDrivers(
                                    pagination.current_page
                                )
                            "
                        >
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- TABLE -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Drivers
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle mb-0"
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
                                    Code
                                </th>

                                <th>
                                    Phone
                                </th>

                                <th>
                                    License No.
                                </th>

                                <th>
                                    License Expiry
                                </th>

                                <th>
                                    Joining Date
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="130">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- LOADING -->

                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading drivers...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-else-if="
                                    drivers.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-person-badge fs-2 d-block mb-2"
                                    ></i>

                                    No drivers found.

                                </td>

                            </tr>


                            <!-- DATA -->

                            <tr
                                v-for="(
                                    driver,
                                    index
                                ) in drivers"
                                :key="driver.id"
                            >

                                <td>

                                    {{
                                        (
                                            pagination.current_page -
                                            1
                                        ) *
                                        pagination.per_page +
                                        index +
                                        1
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{ driver.name }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        driver.code ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        driver.phone ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        driver.license_no ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    <span
                                        :class="
                                            expiryClass(
                                                driver.license_expiry
                                            )
                                        "
                                    >

                                        {{
                                            formatDate(
                                                driver.license_expiry
                                            )
                                        }}

                                    </span>

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            driver.joining_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                driver.status
                                            )
                                        "
                                    >

                                        {{
                                            driver.status
                                        }}

                                    </span>

                                </td>


                                <td>

                                    <div
                                        class="d-flex gap-1"
                                    >

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewDriver(
                                                    driver.id
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-eye"
                                            ></i>

                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            @click="
                                                openEdit(
                                                    driver
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
                                            @click="
                                                deleteDriver(
                                                    driver
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-trash"
                                            ></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- PAGINATION -->

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


                        <span class="mx-2">

                            Page
                            {{ pagination.current_page }}
                            of
                            {{ pagination.last_page }}

                        </span>


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


        <!-- =========================================================
             CREATE / EDIT MODAL
        ========================================================== -->

        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveDriver"
                    >

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Driver'
                                            : 'Add Driver'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Driver information
                                </small>

                            </div>


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

                                <!-- NAME -->

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
                                        placeholder="Driver name"
                                        required
                                    >

                                    <small
                                        v-if="
                                            formErrors.name
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.name
                                        }}
                                    </small>

                                </div>


                                <!-- CODE -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Driver Code
                                    </label>

                                    <input
                                        v-model="
                                            form.code
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="DRV-001"
                                    >

                                    <small
                                        v-if="
                                            formErrors.code
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.code
                                        }}
                                    </small>

                                </div>


                                <!-- PHONE -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <input
                                        v-model="
                                            form.phone
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Phone number"
                                    >

                                </div>


                                <!-- EMAIL -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input
                                        v-model="
                                            form.email
                                        "
                                        type="email"
                                        class="form-control"
                                        placeholder="driver@example.com"
                                    >

                                    <small
                                        v-if="
                                            formErrors.email
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.email
                                        }}
                                    </small>

                                </div>


                                <!-- LICENSE -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        License No.
                                    </label>

                                    <input
                                        v-model="
                                            form.license_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Driving license number"
                                    >

                                </div>


                                <!-- LICENSE EXPIRY -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        License Expiry
                                    </label>

                                    <input
                                        v-model="
                                            form.license_expiry
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- JOINING DATE -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Joining Date
                                    </label>

                                    <input
                                        v-model="
                                            form.joining_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- STATUS -->

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

                                        <option value="Inactive">
                                            Inactive
                                        </option>

                                    </select>

                                </div>


                                <!-- ADDRESS -->

                                <div class="col-md-12">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea
                                        v-model="
                                            form.address
                                        "
                                        rows="2"
                                        class="form-control"
                                        placeholder="Driver address"
                                    ></textarea>

                                </div>


                                <!-- REMARKS -->

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
                                        placeholder="Additional remarks"
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

                                <i
                                    v-else
                                    class="bi bi-check-lg me-1"
                                ></i>

                                {{
                                    editing
                                        ? 'Update Driver'
                                        : 'Save Driver'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =========================================================
             VIEW MODAL
        ========================================================== -->

        <div
            v-if="showViewModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <div>

                            <h5 class="modal-title">
                                Driver Details
                            </h5>

                            <small
                                v-if="selectedDriver"
                                class="text-muted"
                            >
                                {{
                                    selectedDriver.name
                                }}
                            </small>

                        </div>


                        <button
                            type="button"
                            class="btn-close"
                            @click="
                                showViewModal = false
                            "
                        ></button>

                    </div>


                    <div class="modal-body">

                        <div
                            v-if="viewLoading"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="text-muted mt-2">
                                Loading driver details...
                            </div>

                        </div>


                        <div
                            v-else-if="
                                selectedDriver
                            "
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Name
                                </label>

                                <div class="fw-bold">
                                    {{
                                        selectedDriver.name
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Driver Code
                                </label>

                                <div>
                                    {{
                                        selectedDriver.code ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Phone
                                </label>

                                <div>
                                    {{
                                        selectedDriver.phone ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Email
                                </label>

                                <div>
                                    {{
                                        selectedDriver.email ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    License No.
                                </label>

                                <div>
                                    {{
                                        selectedDriver.license_no ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    License Expiry
                                </label>

                                <div
                                    :class="
                                        expiryClass(
                                            selectedDriver.license_expiry
                                        )
                                    "
                                >
                                    {{
                                        formatDate(
                                            selectedDriver.license_expiry
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Joining Date
                                </label>

                                <div>
                                    {{
                                        formatDate(
                                            selectedDriver.joining_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Status
                                </label>

                                <div>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                selectedDriver.status
                                            )
                                        "
                                    >
                                        {{
                                            selectedDriver.status
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-12">

                                <label class="text-muted small">
                                    Address
                                </label>

                                <div>
                                    {{
                                        selectedDriver.address ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-12">

                                <label class="text-muted small">
                                    Remarks
                                </label>

                                <div>
                                    {{
                                        selectedDriver.remarks ||
                                        '-'
                                    }}
                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="
                                showViewModal = false
                            "
                        >
                            Close
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
| STATE
|--------------------------------------------------------------------------
*/

const drivers = ref([]);

const loading = ref(false);

const saving = ref(false);

const viewLoading = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showModal = ref(false);

const showViewModal = ref(false);

const editing = ref(false);

const selectedDriver = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    status: '',

    per_page: 20

});


/*
|--------------------------------------------------------------------------
| PAGINATION
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
| FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    name: '',

    code: '',

    phone: '',

    email: '',

    license_no: '',

    license_expiry: '',

    joining_date: '',

    address: '',

    remarks: '',

    status: 'Active'

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| AUTH CONFIG
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

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
| LOAD DRIVERS
|--------------------------------------------------------------------------
*/

const loadDrivers = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/drivers',

                {

                    ...getAuthConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        status:
                            filters.value.status ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        drivers.value =
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
            'Driver loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load drivers.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    form.value =
        emptyForm();

    formErrors.value = {};

    formError.value = '';

    selectedDriver.value =
        null;

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

const openEdit = (
    driver
) => {

    editing.value = true;

    selectedDriver.value =
        driver;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        name:
            driver.name || '',

        code:
            driver.code || '',

        phone:
            driver.phone || '',

        email:
            driver.email || '',

        license_no:
            driver.license_no || '',

        license_expiry:
            dateInput(
                driver.license_expiry
            ),

        joining_date:
            dateInput(
                driver.joining_date
            ),

        address:
            driver.address || '',

        remarks:
            driver.remarks || '',

        status:
            driver.status || 'Active'

    };


    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| DATE INPUT
|--------------------------------------------------------------------------
*/

const dateInput = (
    value
) => {

    if (!value) {

        return '';

    }


    return String(
        value
    ).substring(
        0,
        10
    );

};


/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    if (saving.value) {

        return;

    }

    showModal.value =
        false;

};


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveDriver = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';

    saving.value = true;


    try {

        const payload = {

            name:
                form.value.name,

            code:
                form.value.code ||
                null,

            phone:
                form.value.phone ||
                null,

            email:
                form.value.email ||
                null,

            license_no:
                form.value.license_no ||
                null,

            license_expiry:
                form.value.license_expiry ||
                null,

            joining_date:
                form.value.joining_date ||
                null,

            address:
                form.value.address ||
                null,

            remarks:
                form.value.remarks ||
                null,

            status:
                form.value.status

        };


        let response;


        if (
            editing.value &&
            selectedDriver.value
        ) {

            response =
                await axios.put(

                    `/api/transport/drivers/${selectedDriver.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/drivers',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Driver updated successfully.'
                    : 'Driver created successfully.'
            );


        showModal.value =
            false;


        await loadDrivers(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Driver save error:',
            err
        );


        if (
            err.response?.status ===
            422
        ) {

            const errors =
                err.response.data?.errors ||
                {};


            Object.keys(
                errors
            ).forEach(
                key => {

                    formErrors.value[key] =
                        Array.isArray(
                            errors[key]
                        )
                            ? errors[key][0]
                            : errors[key];

                }
            );


            formError.value =
                err.response.data?.message ||
                'Please correct the form errors.';

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save driver.';

        }

    } finally {

        saving.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

const viewDriver = async (
    id
) => {

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedDriver.value =
        null;


    try {

        const response =
            await axios.get(

                `/api/transport/drivers/${id}`,

                getAuthConfig()

            );


        selectedDriver.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Driver view error:',
            err
        );


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load driver details.';

    } finally {

        viewLoading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteDriver = async (
    driver
) => {

    if (
        !confirm(
            `Are you sure you want to delete "${driver.name}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/drivers/${driver.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Driver deleted successfully.';


        await loadDrivers(
            pagination.value.current_page
        );


        if (
            drivers.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadDrivers(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Driver delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete driver.';

    }

};


/*
|--------------------------------------------------------------------------
| STATUS CLASS
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    switch (status) {

        case 'Active':
            return 'bg-success';

        case 'Inactive':
            return 'bg-secondary';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| LICENSE EXPIRY
|--------------------------------------------------------------------------
*/

const expiryClass = (
    date
) => {

    if (!date) {

        return 'text-muted';

    }


    const expiry =
        new Date(
            `${dateInput(date)}T00:00:00`
        );


    const today =
        new Date();


    today.setHours(
        0,
        0,
        0,
        0
    );


    const diff =
        Math.ceil(
            (
                expiry.getTime() -
                today.getTime()
            ) /
            (
                1000 *
                60 *
                60 *
                24
            )
        );


    if (diff < 0) {

        return 'text-danger fw-bold';

    }


    if (diff <= 30) {

        return 'text-warning fw-bold';

    }


    return 'text-success';

};


/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

const formatDate = (
    date
) => {

    if (!date) {

        return '-';

    }


    const value =
        dateInput(date);


    const parts =
        value.split('-');


    if (
        parts.length !== 3
    ) {

        return date;

    }


    return `${parts[2]}-${parts[1]}-${parts[0]}`;

};


/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        status: '',

        per_page: 20

    };


    loadDrivers(1);

};


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

const changePage = (
    page
) => {

    if (
        page < 1 ||
        page >
            pagination.value.last_page
    ) {

        return;

    }


    loadDrivers(page);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadDrivers(1);

    }
);

</script>