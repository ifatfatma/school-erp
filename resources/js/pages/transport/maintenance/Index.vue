<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Vehicle Maintenance
                </h4>

                <small class="text-muted">
                    Manage vehicle servicing, repairs and maintenance costs
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Maintenance
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


        <!-- FILTER CARD -->
        <div class="card mb-3">

            <div class="card-header">
                <strong>
                    Search / Filter
                </strong>
            </div>


            <div class="card-body">

                <div class="row g-3">

                    <!-- SEARCH -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Maintenance No / Vehicle / Provider"
                            @keyup.enter="loadData(1)"
                        >

                    </div>


                    <!-- VEHICLE -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Vehicle
                        </label>

                        <select
                            v-model="filters.transport_vehicle_id"
                            class="form-select"
                            @change="loadData(1)"
                        >

                            <option value="">
                                All Vehicles
                            </option>

                            <option
                                v-for="vehicle in vehicles"
                                :key="vehicle.id"
                                :value="vehicle.id"
                            >

                                {{ vehicle.vehicle_no }}

                                <template
                                    v-if="vehicle.registration_no"
                                >
                                    — {{ vehicle.registration_no }}
                                </template>

                            </option>

                        </select>

                    </div>


                    <!-- TYPE -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Maintenance Type
                        </label>

                        <select
                            v-model="filters.maintenance_type"
                            class="form-select"
                            @change="loadData(1)"
                        >

                            <option value="">
                                All Types
                            </option>

                            <option
                                v-for="type in maintenanceTypes"
                                :key="type"
                                :value="type"
                            >
                                {{ type }}
                            </option>

                        </select>

                    </div>


                    <!-- STATUS -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                            @change="loadData(1)"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                v-for="status in statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status }}
                            </option>

                        </select>

                    </div>


                    <!-- DATE FROM -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Date From
                        </label>

                        <input
                            v-model="filters.date_from"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- DATE TO -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Date To
                        </label>

                        <input
                            v-model="filters.date_to"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- FILTER BUTTON -->
                    <div class="col-md-6 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            @click="loadData(1)"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetFilters"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- TABLE -->
        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <strong>
                    Maintenance Records
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

                                <th>#</th>

                                <th>
                                    Maintenance No
                                </th>

                                <th>
                                    Vehicle
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Provider
                                </th>

                                <th>
                                    Cost
                                </th>

                                <th>
                                    Next Service
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="120">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- LOADING -->
                            <tr v-if="loading">

                                <td
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="mt-2 text-muted">
                                        Loading...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->
                            <tr v-else-if="records.length === 0">

                                <td
                                    colspan="10"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-tools fs-2 d-block mb-2"
                                    ></i>

                                    No maintenance records found.

                                </td>

                            </tr>


                            <!-- RECORDS -->
                            <tr
                                v-for="(
                                    record,
                                    index
                                ) in records"
                                :key="record.id"
                            >

                                <td>
                                    {{
                                        (pagination.current_page - 1) *
                                        pagination.per_page +
                                        index +
                                        1
                                    }}
                                </td>


                                <td>

                                    <strong>
                                        {{
                                            record.maintenance_no || '-'
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            record.vehicle_no || '-'
                                        }}
                                    </strong>

                                    <div
                                        v-if="record.registration_no"
                                        class="small text-muted"
                                    >
                                        {{
                                            record.registration_no
                                        }}
                                    </div>

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            record.maintenance_date
                                        )
                                    }}
                                </td>


                                <td>

                                    <span
                                        class="badge bg-light text-dark border"
                                    >
                                        {{
                                            record.maintenance_type
                                        }}
                                    </span>

                                </td>


                                <td>
                                    {{
                                        record.service_provider || '-'
                                    }}
                                </td>


                                <td>

                                    <strong>
                                        ₹
                                        {{
                                            formatAmount(
                                                record.cost
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            record.next_service_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                record.status
                                            )
                                        "
                                    >
                                        {{
                                            record.status
                                        }}
                                    </span>

                                </td>


                                <td>

                                    <div class="d-flex gap-1">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewRecord(
                                                    record.id
                                                )
                                            "
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            @click="
                                                openEdit(
                                                    record
                                                )
                                            "
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                            @click="
                                                deleteRecord(
                                                    record
                                                )
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
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
                v-if="pagination.last_page > 1"
                class="card-footer d-flex justify-content-between align-items-center"
            >

                <span class="text-muted">

                    Showing
                    {{ pagination.from || 0 }}
                    -
                    {{ pagination.to || 0 }}
                    of
                    {{ pagination.total }}

                </span>


                <div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary me-2"
                        :disabled="
                            pagination.current_page <= 1 ||
                            loading
                        "
                        @click="
                            changePage(
                                pagination.current_page - 1
                            )
                        "
                    >
                        Previous
                    </button>


                    <span>
                        Page
                        {{ pagination.current_page }}
                        of
                        {{ pagination.last_page }}
                    </span>


                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary ms-2"
                        :disabled="
                            pagination.current_page >=
                            pagination.last_page ||
                            loading
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


        <!-- ==================================================
             ADD / EDIT MODAL
        =================================================== -->

        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form @submit.prevent="saveRecord">

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Vehicle Maintenance'
                                        : 'Add Vehicle Maintenance'
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

                                <!-- VEHICLE -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Vehicle
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.transport_vehicle_id
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Vehicle
                                        </option>


                                        <option
                                            v-for="
                                                vehicle in vehicles
                                            "
                                            :key="vehicle.id"
                                            :value="vehicle.id"
                                        >

                                            {{
                                                vehicle.vehicle_no
                                            }}

                                            <template
                                                v-if="
                                                    vehicle.registration_no
                                                "
                                            >

                                                —
                                                {{
                                                    vehicle.registration_no
                                                }}

                                            </template>

                                        </option>

                                    </select>

                                </div>


                                <!-- DATE -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Maintenance Date
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        v-model="
                                            form.maintenance_date
                                        "
                                        type="date"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- TYPE -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Maintenance Type
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.maintenance_type
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Type
                                        </option>


                                        <option
                                            v-for="
                                                type in maintenanceTypes
                                            "
                                            :key="type"
                                            :value="type"
                                        >
                                            {{ type }}
                                        </option>

                                    </select>

                                </div>


                                <!-- ODOMETER -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Odometer
                                    </label>


                                    <input
                                        v-model.number="
                                            form.odometer
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        placeholder="KM"
                                    >

                                </div>


                                <!-- PROVIDER -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Service Provider
                                    </label>


                                    <input
                                        v-model="
                                            form.service_provider
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Garage / Service Center"
                                    >

                                </div>


                                <!-- COST -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Cost
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        v-model.number="
                                            form.cost
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- NEXT DATE -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Next Service Date
                                    </label>


                                    <input
                                        v-model="
                                            form.next_service_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- NEXT ODOMETER -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Next Service Odometer
                                    </label>


                                    <input
                                        v-model.number="
                                            form.next_service_odometer
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        placeholder="KM"
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

                                        <option
                                            v-for="
                                                status in statuses
                                            "
                                            :key="status"
                                            :value="status"
                                        >
                                            {{ status }}
                                        </option>

                                    </select>

                                </div>


                                <!-- DESCRIPTION -->
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
                                        placeholder="Describe the maintenance work..."
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
                                        class="form-control"
                                        rows="2"
                                        placeholder="Additional remarks..."
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
                                        ? 'Update'
                                        : 'Save'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- ==================================================
             VIEW MODAL
        =================================================== -->

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

                        <h5 class="modal-title">
                            Maintenance Details
                        </h5>


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
                            v-else-if="selectedRecord"
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <small class="text-muted">
                                    Maintenance No
                                </small>

                                <div class="fw-bold">
                                    {{
                                        selectedRecord.maintenance_no
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Vehicle
                                </small>

                                <div class="fw-bold">

                                    {{
                                        selectedRecord.vehicle_no
                                    }}

                                    <span
                                        v-if="
                                            selectedRecord.registration_no
                                        "
                                    >
                                        —
                                        {{
                                            selectedRecord.registration_no
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Maintenance Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRecord.maintenance_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Maintenance Type
                                </small>

                                <div>
                                    {{
                                        selectedRecord.maintenance_type
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Odometer
                                </small>

                                <div>
                                    {{
                                        selectedRecord.odometer ||
                                        '-'
                                    }}
                                    KM
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Cost
                                </small>

                                <div class="fw-bold">
                                    ₹
                                    {{
                                        formatAmount(
                                            selectedRecord.cost
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Service Provider
                                </small>

                                <div>
                                    {{
                                        selectedRecord.service_provider ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Status
                                </small>

                                <div>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                selectedRecord.status
                                            )
                                        "
                                    >
                                        {{
                                            selectedRecord.status
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Next Service Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRecord.next_service_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Next Service Odometer
                                </small>

                                <div>
                                    {{
                                        selectedRecord.next_service_odometer ||
                                        '-'
                                    }}
                                    KM
                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Description
                                </small>

                                <div>
                                    {{
                                        selectedRecord.description ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Remarks
                                </small>

                                <div>
                                    {{
                                        selectedRecord.remarks ||
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

import { onMounted, onBeforeUnmount,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const records = ref([]);

const vehicles = ref([]);

const maintenanceTypes = ref([]);

const statuses = ref([]);

const loading = ref(false);

const saving = ref(false);

const viewLoading = ref(false);

const success = ref('');

const error = ref('');

const formError = ref('');

const showModal = ref(false);

const showViewModal = ref(false);

const editing = ref(false);

const selectedRecord = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    transport_vehicle_id: '',

    maintenance_type: '',

    status: '',

    date_from: '',

    date_to: '',

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

    transport_vehicle_id: '',

    maintenance_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    maintenance_type: '',

    odometer: '',

    service_provider: '',

    description: '',

    cost: 0,

    next_service_date: '',

    next_service_odometer: '',

    status: 'Completed',

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

const authConfig = () => {

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
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) {
        config.headers['X-School-Id'] = activeSchoolId;
    }


    return config;
};


/*
|--------------------------------------------------------------------------
| OPTIONS
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    try {

        const response =
            await axios.get(
                '/api/transport/maintenance/options',
                authConfig()
            );


        const data =
            response.data?.data || {};


        vehicles.value =
            Array.isArray(data.vehicles)
                ? data.vehicles
                : [];


        maintenanceTypes.value =
            Array.isArray(
                data.maintenance_types
            )
                ? data.maintenance_types
                : [];


        statuses.value =
            Array.isArray(
                data.statuses
            )
                ? data.statuses
                : [];

    } catch (err) {

        console.error(
            'Maintenance options error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load maintenance options.';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD DATA
|--------------------------------------------------------------------------
*/

const loadData = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/maintenance',

                {

                    ...authConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        transport_vehicle_id:
                            filters.value
                                .transport_vehicle_id ||
                            undefined,

                        maintenance_type:
                            filters.value
                                .maintenance_type ||
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

                    }

                }

            );


        const result =
            response.data?.data;


        records.value =
            result?.data ||
            [];


        pagination.value = {

            current_page:
                Number(
                    result?.current_page || 1
                ),

            last_page:
                Number(
                    result?.last_page || 1
                ),

            per_page:
                Number(
                    result?.per_page ||
                    filters.value.per_page
                ),

            total:
                Number(
                    result?.total || 0
                ),

            from:
                Number(
                    result?.from || 0
                ),

            to:
                Number(
                    result?.to || 0
                )

        };

    } catch (err) {

        console.error(
            'Maintenance loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load maintenance records.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedRecord.value = null;

    form.value = emptyForm();

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/

const openEdit = (
    record
) => {

    editing.value = true;

    selectedRecord.value = record;

    formError.value = '';


    form.value = {

        transport_vehicle_id:
            record.transport_vehicle_id || '',

        maintenance_date:
            formatInputDate(
                record.maintenance_date
            ),

        maintenance_type:
            record.maintenance_type || '',

        odometer:
            record.odometer || '',

        service_provider:
            record.service_provider || '',

        description:
            record.description || '',

        cost:
            Number(
                record.cost || 0
            ),

        next_service_date:
            formatInputDate(
                record.next_service_date
            ),

        next_service_odometer:
            record.next_service_odometer || '',

        status:
            record.status || 'Completed',

        remarks:
            record.remarks || ''

    };


    showModal.value = true;

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

    showModal.value = false;

};


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveRecord = async () => {

    formError.value = '';

    success.value = '';

    error.value = '';

    saving.value = true;


    try {

        const payload = {

            transport_vehicle_id:
                Number(
                    form.value.transport_vehicle_id
                ),

            maintenance_date:
                form.value.maintenance_date,

            maintenance_type:
                form.value.maintenance_type,

            odometer:
                form.value.odometer === ''
                    ? null
                    : Number(
                        form.value.odometer
                    ),

            service_provider:
                form.value.service_provider ||
                null,

            description:
                form.value.description ||
                null,

            cost:
                Number(
                    form.value.cost || 0
                ),

            next_service_date:
                form.value.next_service_date ||
                null,

            next_service_odometer:
                form.value.next_service_odometer === ''
                    ? null
                    : Number(
                        form.value.next_service_odometer
                    ),

            status:
                form.value.status,

            remarks:
                form.value.remarks ||
                null

        };


        let response;


        if (
            editing.value &&
            selectedRecord.value
        ) {

            response =
                await axios.put(

                    `/api/transport/maintenance/${selectedRecord.value.id}`,

                    payload,

                    authConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/maintenance',

                    payload,

                    authConfig()

                );

        }


        success.value =
            response.data?.message ||
            'Maintenance saved successfully.';


        showModal.value = false;


        await loadData(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Maintenance save error:',
            err
        );


        if (
            err.response?.status === 422
        ) {

            const errors =
                err.response.data?.errors ||
                {};


            const firstError =
                Object.values(errors)[0];


            formError.value =
                Array.isArray(firstError)
                    ? firstError[0]
                    : (
                        firstError ||
                        err.response.data?.message ||
                        'Validation failed.'
                    );

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save maintenance record.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

const viewRecord = async (
    id
) => {

    selectedRecord.value = null;

    showViewModal.value = true;

    viewLoading.value = true;


    try {

        const response =
            await axios.get(

                `/api/transport/maintenance/${id}`,

                authConfig()

            );


        selectedRecord.value =
            response.data?.data ||
            null;

    } catch (err) {

        showViewModal.value = false;

        error.value =
            err.response?.data?.message ||
            'Unable to load maintenance record.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteRecord = async (
    record
) => {

    if (
        !confirm(
            `Delete maintenance record ${record.maintenance_no || ''}?`
        )
    ) {

        return;

    }


    try {

        const response =
            await axios.delete(

                `/api/transport/maintenance/${record.id}`,

                authConfig()

            );


        success.value =
            response.data?.message ||
            'Maintenance record deleted successfully.';


        await loadData(
            pagination.value.current_page
        );


        if (
            records.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadData(
                pagination.value.current_page - 1
            );

        }

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete maintenance record.';

    }

};


/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        transport_vehicle_id: '',

        maintenance_type: '',

        status: '',

        date_from: '',

        date_to: '',

        per_page: 20

    };


    loadData(1);

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
        page > pagination.value.last_page
    ) {

        return;

    }


    loadData(page);

};


/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

    if (!value) {

        return '-';

    }


    const date =
        String(value)
            .substring(0, 10);


    const parts =
        date.split('-');


    if (parts.length !== 3) {

        return date;

    }


    return `${parts[2]}-${parts[1]}-${parts[0]}`;

};


const formatInputDate = (
    value
) => {

    if (!value) {

        return '';

    }


    return String(value)
        .substring(0, 10);

};


/*
|--------------------------------------------------------------------------
| AMOUNT
|--------------------------------------------------------------------------
*/

const formatAmount = (
    value
) => {

    return Number(
        value || 0
    ).toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    switch (status) {

        case 'Completed':
            return 'bg-success';

        case 'In Progress':
            return 'bg-warning text-dark';

        case 'Scheduled':
            return 'bg-info text-dark';

        case 'Cancelled':
            return 'bg-danger';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadOptions();

        await loadData(1);

    }
);



const onTenantChanged = () => { loadRecords(1); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>