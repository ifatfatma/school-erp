<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Transport Vehicles</h4>
                <small class="text-muted">
                    Manage school transport vehicles
                </small>
            </div>

            <button
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Vehicle
            </button>
        </div>

        <!-- ALERT -->
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

        <!-- FILTER -->
        <div class="card mb-3">
            <div class="card-header">
                <strong>Search Vehicles</strong>
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
                            placeholder="Vehicle no / registration / model"
                            @keyup.enter="loadVehicles(1)"
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

                            <option value="Maintenance">
                                Maintenance
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="loading"
                            @click="loadVehicles(1)"
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
                            @click="loadVehicles(
                                pagination.current_page
                            )"
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
                    Vehicles
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
                                <th width="55">#</th>
                                <th>Vehicle No.</th>
                                <th>Registration No.</th>
                                <th>Type</th>
                                <th>Model</th>
                                <th class="text-center">
                                    Capacity
                                </th>
                                <th>Insurance</th>
                                <th>Fitness</th>
                                <th>Pollution</th>
                                <th>Status</th>
                                <th width="130">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <!-- LOADING -->
                            <tr v-if="loading">
                                <td
                                    colspan="11"
                                    class="text-center py-5"
                                >
                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading vehicles...
                                    </div>
                                </td>
                            </tr>

                            <!-- EMPTY -->
                            <tr
                                v-else-if="
                                    vehicles.length === 0
                                "
                            >
                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >
                                    <i
                                        class="bi bi-bus-front fs-2 d-block mb-2"
                                    ></i>

                                    No vehicles found.
                                </td>
                            </tr>

                            <!-- DATA -->
                            <tr
                                v-for="(
                                    vehicle,
                                    index
                                ) in vehicles"
                                :key="vehicle.id"
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
                                        {{ vehicle.vehicle_no }}
                                    </strong>
                                </td>

                                <td>
                                    {{
                                        vehicle.registration_no ||
                                        '-'
                                    }}
                                </td>

                                <td>
                                    {{
                                        vehicle.vehicle_type ||
                                        '-'
                                    }}
                                </td>

                                <td>
                                    {{
                                        vehicle.model ||
                                        '-'
                                    }}
                                </td>

                                <td class="text-center">
                                    {{
                                        vehicle.capacity ||
                                        '-'
                                    }}
                                </td>

                                <!-- INSURANCE -->
                                <td>
                                    <span
                                        :class="
                                            expiryClass(
                                                vehicle.insurance_expiry
                                            )
                                        "
                                    >
                                        {{
                                            formatDate(
                                                vehicle.insurance_expiry
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- FITNESS -->
                                <td>
                                    <span
                                        :class="
                                            expiryClass(
                                                vehicle.fitness_expiry
                                            )
                                        "
                                    >
                                        {{
                                            formatDate(
                                                vehicle.fitness_expiry
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- POLLUTION -->
                                <td>
                                    <span
                                        :class="
                                            expiryClass(
                                                vehicle.pollution_expiry
                                            )
                                        "
                                    >
                                        {{
                                            formatDate(
                                                vehicle.pollution_expiry
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                vehicle.status
                                            )
                                        "
                                    >
                                        {{ vehicle.status }}
                                    </span>
                                </td>

                                <!-- ACTION -->
                                <td>

                                    <div
                                        class="d-flex gap-1"
                                    >

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewVehicle(
                                                    vehicle.id
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
                                                    vehicle
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
                                                deleteVehicle(
                                                    vehicle
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
                        @submit.prevent="saveVehicle"
                    >

                        <div class="modal-header">

                            <div>
                                <h5 class="modal-title">
                                    {{
                                        editing
                                            ? 'Edit Vehicle'
                                            : 'Add Vehicle'
                                    }}
                                </h5>

                                <small class="text-muted">
                                    Vehicle information
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

                                <!-- Vehicle No -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Vehicle No.
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.vehicle_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="BUS-01"
                                        required
                                    >

                                    <small
                                        v-if="
                                            formErrors.vehicle_no
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.vehicle_no
                                        }}
                                    </small>

                                </div>


                                <!-- Registration -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Registration No.
                                    </label>

                                    <input
                                        v-model="
                                            form.registration_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="WB-01-AB-1234"
                                    >

                                    <small
                                        v-if="
                                            formErrors.registration_no
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.registration_no
                                        }}
                                    </small>

                                </div>


                                <!-- Type -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Vehicle Type
                                    </label>

                                    <select
                                        v-model="
                                            form.vehicle_type
                                        "
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Bus">
                                            Bus
                                        </option>

                                        <option value="Van">
                                            Van
                                        </option>

                                        <option value="Mini Bus">
                                            Mini Bus
                                        </option>

                                        <option value="Car">
                                            Car
                                        </option>

                                        <option value="Other">
                                            Other
                                        </option>

                                    </select>

                                </div>


                                <!-- Model -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Model
                                    </label>

                                    <input
                                        v-model="
                                            form.model
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Vehicle model"
                                    >

                                </div>


                                <!-- Capacity -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Capacity
                                    </label>

                                    <input
                                        v-model.number="
                                            form.capacity
                                        "
                                        type="number"
                                        min="1"
                                        class="form-control"
                                        placeholder="40"
                                    >

                                </div>


                                <!-- Purchase Date -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Purchase Date
                                    </label>

                                    <input
                                        v-model="
                                            form.purchase_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

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

                                        <option value="Inactive">
                                            Inactive
                                        </option>

                                        <option value="Maintenance">
                                            Maintenance
                                        </option>

                                    </select>

                                </div>


                                <!-- Insurance -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Insurance Expiry
                                    </label>

                                    <input
                                        v-model="
                                            form.insurance_expiry
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Fitness -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fitness Expiry
                                    </label>

                                    <input
                                        v-model="
                                            form.fitness_expiry
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Pollution -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Pollution Expiry
                                    </label>

                                    <input
                                        v-model="
                                            form.pollution_expiry
                                        "
                                        type="date"
                                        class="form-control"
                                    >

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
                                        ? 'Update Vehicle'
                                        : 'Save Vehicle'
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
                                Vehicle Details
                            </h5>

                            <small
                                v-if="selectedVehicle"
                                class="text-muted"
                            >
                                {{
                                    selectedVehicle.vehicle_no
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

                        </div>


                        <div
                            v-else-if="
                                selectedVehicle
                            "
                            class="row g-3"
                        >

                            <div class="col-md-6">
                                <label class="text-muted small">
                                    Vehicle No.
                                </label>

                                <div class="fw-bold">
                                    {{
                                        selectedVehicle.vehicle_no
                                    }}
                                </div>
                            </div>


                            <div class="col-md-6">
                                <label class="text-muted small">
                                    Registration No.
                                </label>

                                <div>
                                    {{
                                        selectedVehicle.registration_no ||
                                        '-'
                                    }}
                                </div>
                            </div>


                            <div class="col-md-6">
                                <label class="text-muted small">
                                    Vehicle Type
                                </label>

                                <div>
                                    {{
                                        selectedVehicle.vehicle_type ||
                                        '-'
                                    }}
                                </div>
                            </div>


                            <div class="col-md-6">
                                <label class="text-muted small">
                                    Model
                                </label>

                                <div>
                                    {{
                                        selectedVehicle.model ||
                                        '-'
                                    }}
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Capacity
                                </label>

                                <div>
                                    {{
                                        selectedVehicle.capacity ||
                                        '-'
                                    }}
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Purchase Date
                                </label>

                                <div>
                                    {{
                                        formatDate(
                                            selectedVehicle.purchase_date
                                        )
                                    }}
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Status
                                </label>

                                <div>
                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                selectedVehicle.status
                                            )
                                        "
                                    >
                                        {{
                                            selectedVehicle.status
                                        }}
                                    </span>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Insurance Expiry
                                </label>

                                <div
                                    :class="
                                        expiryClass(
                                            selectedVehicle.insurance_expiry
                                        )
                                    "
                                >
                                    {{
                                        formatDate(
                                            selectedVehicle.insurance_expiry
                                        )
                                    }}
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Fitness Expiry
                                </label>

                                <div
                                    :class="
                                        expiryClass(
                                            selectedVehicle.fitness_expiry
                                        )
                                    "
                                >
                                    {{
                                        formatDate(
                                            selectedVehicle.fitness_expiry
                                        )
                                    }}
                                </div>
                            </div>


                            <div class="col-md-4">
                                <label class="text-muted small">
                                    Pollution Expiry
                                </label>

                                <div
                                    :class="
                                        expiryClass(
                                            selectedVehicle.pollution_expiry
                                        )
                                    "
                                >
                                    {{
                                        formatDate(
                                            selectedVehicle.pollution_expiry
                                        )
                                    }}
                                </div>
                            </div>


                            <div class="col-md-12">

                                <label class="text-muted small">
                                    Remarks
                                </label>

                                <div>
                                    {{
                                        selectedVehicle.remarks ||
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

const vehicles = ref([]);

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

const selectedVehicle = ref(null);


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

    vehicle_no: '',

    registration_no: '',

    vehicle_type: '',

    model: '',

    capacity: '',

    purchase_date: '',

    insurance_expiry: '',

    fitness_expiry: '',

    pollution_expiry: '',

    status: 'Active',

    remarks: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| AUTH
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
| LOAD VEHICLES
|--------------------------------------------------------------------------
*/

const loadVehicles = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/vehicles',

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


        vehicles.value =
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
            'Vehicle loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load vehicles.';

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

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

const openEdit = (
    vehicle
) => {

    editing.value = true;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        vehicle_no:
            vehicle.vehicle_no || '',

        registration_no:
            vehicle.registration_no || '',

        vehicle_type:
            vehicle.vehicle_type || '',

        model:
            vehicle.model || '',

        capacity:
            vehicle.capacity || '',

        purchase_date:
            dateInput(
                vehicle.purchase_date
            ),

        insurance_expiry:
            dateInput(
                vehicle.insurance_expiry
            ),

        fitness_expiry:
            dateInput(
                vehicle.fitness_expiry
            ),

        pollution_expiry:
            dateInput(
                vehicle.pollution_expiry
            ),

        status:
            vehicle.status || 'Active',

        remarks:
            vehicle.remarks || ''

    };


    selectedVehicle.value =
        vehicle;


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
| CLOSE MODAL
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
| SAVE VEHICLE
|--------------------------------------------------------------------------
*/

const saveVehicle = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';


    saving.value = true;


    try {

        const payload = {

            vehicle_no:
                form.value.vehicle_no,

            registration_no:
                form.value.registration_no ||
                null,

            vehicle_type:
                form.value.vehicle_type ||
                null,

            model:
                form.value.model ||
                null,

            capacity:
                form.value.capacity
                    ? Number(
                        form.value.capacity
                    )
                    : null,

            purchase_date:
                form.value.purchase_date ||
                null,

            insurance_expiry:
                form.value.insurance_expiry ||
                null,

            fitness_expiry:
                form.value.fitness_expiry ||
                null,

            pollution_expiry:
                form.value.pollution_expiry ||
                null,

            status:
                form.value.status,

            remarks:
                form.value.remarks ||
                null

        };


        let response;


        if (editing.value) {

            response =
                await axios.put(

                    `/api/transport/vehicles/${selectedVehicle.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/vehicles',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Vehicle updated successfully.'
                    : 'Vehicle created successfully.'
            );


        showModal.value = false;


        await loadVehicles(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Vehicle save error:',
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
                'Unable to save vehicle.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW VEHICLE
|--------------------------------------------------------------------------
*/

const viewVehicle = async (
    id
) => {

    showViewModal.value = true;

    viewLoading.value = true;

    selectedVehicle.value = null;


    try {

        const response =
            await axios.get(

                `/api/transport/vehicles/${id}`,

                getAuthConfig()

            );


        selectedVehicle.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Vehicle view error:',
            err
        );


        showViewModal.value = false;


        error.value =
            err.response?.data?.message ||
            'Unable to load vehicle details.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteVehicle = async (
    vehicle
) => {

    if (
        !confirm(
            `Are you sure you want to delete "${vehicle.vehicle_no}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/vehicles/${vehicle.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Vehicle deleted successfully.';


        await loadVehicles(
            pagination.value.current_page
        );


        /*
        |--------------------------------------------------------------------------
        | Go back one page if last record deleted
        |--------------------------------------------------------------------------
        */

        if (
            vehicles.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadVehicles(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Vehicle delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete vehicle.';

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

        case 'Maintenance':

            return 'bg-warning text-dark';

        default:

            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| EXPIRY CLASS
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


    if (
        diff < 0
    ) {

        return 'text-danger fw-bold';

    }


    if (
        diff <= 30
    ) {

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


    loadVehicles(1);

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


    loadVehicles(
        page
    );

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadVehicles(1);

    }
);

</script>