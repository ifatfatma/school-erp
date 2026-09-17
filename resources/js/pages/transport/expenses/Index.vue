<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Fuel / Transport Expenses
                </h4>

                <small class="text-muted">
                    Manage fuel and other transport expenses
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
                <strong>
                    Search / Filter
                </strong>
            </div>

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
                            placeholder="Expense No / Vehicle / Vendor"
                            @keyup.enter="loadData(1)"
                        >

                    </div>


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


                    <div class="col-md-3">

                        <label class="form-label">
                            Expense Type
                        </label>

                        <select
                            v-model="filters.expense_type"
                            class="form-select"
                            @change="loadData(1)"
                        >

                            <option value="">
                                All Types
                            </option>

                            <option
                                v-for="type in expenseTypes"
                                :key="type"
                                :value="type"
                            >
                                {{ type }}
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Driver
                        </label>

                        <select
                            v-model="filters.transport_driver_id"
                            class="form-select"
                            @change="loadData(1)"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                v-for="driver in drivers"
                                :key="driver.id"
                                :value="driver.id"
                            >
                                {{ driver.name }}
                            </option>

                        </select>

                    </div>


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
                    Transport Expenses
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
                                <th>Expense No</th>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                                <th width="120">Action</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr v-if="loading">

                                <td
                                    colspan="11"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading expenses...
                                    </div>

                                </td>

                            </tr>


                            <tr v-else-if="records.length === 0">

                                <td
                                    colspan="11"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-fuel-pump fs-2 d-block mb-2"
                                    ></i>

                                    No transport expenses found.

                                </td>

                            </tr>


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
                                            record.expense_no || '-'
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            record.expense_date
                                        )
                                    }}

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
                                        record.driver_name || '-'
                                    }}
                                </td>


                                <td>

                                    <span
                                        class="badge bg-light text-dark border"
                                    >
                                        {{
                                            record.expense_type
                                        }}
                                    </span>

                                </td>


                                <td>

                                    <template
                                        v-if="
                                            record.quantity !== null &&
                                            record.quantity !== ''
                                        "
                                    >

                                        {{
                                            formatAmount(
                                                record.quantity
                                            )
                                        }}

                                        {{
                                            record.unit || ''
                                        }}

                                    </template>

                                    <template v-else>
                                        -
                                    </template>

                                </td>


                                <td>

                                    <template
                                        v-if="
                                            Number(record.rate || 0) > 0
                                        "
                                    >

                                        ₹
                                        {{
                                            formatAmount(
                                                record.rate
                                            )
                                        }}

                                    </template>

                                    <template v-else>
                                        -
                                    </template>

                                </td>


                                <td>

                                    <strong>
                                        ₹
                                        {{
                                            formatAmount(
                                                record.amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>
                                    {{
                                        record.vendor || '-'
                                    }}
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


                    Page
                    {{ pagination.current_page }}
                    of
                    {{ pagination.last_page }}


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
             ADD / EDIT
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
                                        ? 'Edit Transport Expense'
                                        : 'Add Transport Expense'
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
                                    </label>

                                    <select
                                        v-model="
                                            form.transport_vehicle_id
                                        "
                                        class="form-select"
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


                                <!-- DRIVER -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Driver
                                    </label>

                                    <select
                                        v-model="
                                            form.transport_driver_id
                                        "
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Driver
                                        </option>

                                        <option
                                            v-for="
                                                driver in drivers
                                            "
                                            :key="driver.id"
                                            :value="driver.id"
                                        >
                                            {{ driver.name }}
                                        </option>

                                    </select>

                                </div>


                                <!-- DATE -->
                                <div class="col-md-4">

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
                                        required
                                    >

                                </div>


                                <!-- TYPE -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Expense Type
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="
                                            form.expense_type
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option
                                            v-for="
                                                type in expenseTypes
                                            "
                                            :key="type"
                                            :value="type"
                                        >
                                            {{ type }}
                                        </option>

                                    </select>

                                </div>


                                <!-- UNIT -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Unit
                                    </label>

                                    <input
                                        v-model="
                                            form.unit
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Litre / KM / Piece"
                                    >

                                </div>


                                <!-- QUANTITY -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Quantity
                                    </label>

                                    <input
                                        v-model.number="
                                            form.quantity
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        @input="calculateAmount"
                                    >

                                </div>


                                <!-- RATE -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Rate
                                    </label>

                                    <input
                                        v-model.number="
                                            form.rate
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        @input="calculateAmount"
                                    >

                                </div>


                                <!-- AMOUNT -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Amount
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model.number="
                                            form.amount
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        required
                                    >

                                    <small class="text-muted">
                                        Quantity × Rate is calculated automatically.
                                    </small>

                                </div>


                                <!-- VENDOR -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Vendor / Petrol Pump
                                    </label>

                                    <input
                                        v-model="
                                            form.vendor
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Vendor / Petrol Pump"
                                    >

                                </div>


                                <!-- PAYMENT -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Payment Method
                                    </label>

                                    <select
                                        v-model="
                                            form.payment_method
                                        "
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Payment Method
                                        </option>

                                        <option
                                            v-for="
                                                method in paymentMethods
                                            "
                                            :key="method"
                                            :value="method"
                                        >
                                            {{ method }}
                                        </option>

                                    </select>

                                </div>


                                <!-- REFERENCE -->
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
                                        placeholder="Bill / Receipt No"
                                    >

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
                                        placeholder="Expense description..."
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
             VIEW
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
                            Expense Details
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
                                    Expense No
                                </small>

                                <div class="fw-bold">
                                    {{
                                        selectedRecord.expense_no
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRecord.expense_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Vehicle
                                </small>

                                <div>
                                    {{
                                        selectedRecord.vehicle_no ||
                                        '-'
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
                                    Driver
                                </small>

                                <div>
                                    {{
                                        selectedRecord.driver_name ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Type
                                </small>

                                <div>
                                    {{
                                        selectedRecord.expense_type
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Amount
                                </small>

                                <div class="fw-bold">

                                    ₹
                                    {{
                                        formatAmount(
                                            selectedRecord.amount
                                        )
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Vendor
                                </small>

                                <div>
                                    {{
                                        selectedRecord.vendor ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Payment Method
                                </small>

                                <div>
                                    {{
                                        selectedRecord.payment_method ||
                                        '-'
                                    }}
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

import {
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


const records = ref([]);

const vehicles = ref([]);

const drivers = ref([]);

const expenseTypes = ref([]);

const paymentMethods = ref([]);

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


const filters = ref({

    search: '',

    transport_vehicle_id: '',

    transport_driver_id: '',

    expense_type: '',

    date_from: '',

    date_to: '',

    per_page: 20

});


const pagination = ref({

    current_page: 1,

    last_page: 1,

    per_page: 20,

    total: 0,

    from: 0,

    to: 0

});


const emptyForm = () => ({

    transport_vehicle_id: '',

    transport_driver_id: '',

    expense_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    expense_type: '',

    quantity: '',

    unit: '',

    rate: 0,

    amount: 0,

    vendor: '',

    payment_method: '',

    reference_no: '',

    description: '',

    remarks: ''

});


const form = ref(
    emptyForm()
);


const authConfig = () => {

    const token =
        localStorage.getItem('token') ||
        localStorage.getItem('auth_token') ||
        localStorage.getItem('access_token') ||
        sessionStorage.getItem('token') ||
        sessionStorage.getItem('auth_token') ||
        sessionStorage.getItem('access_token');


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
| OPTIONS
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    try {

        const response =
            await axios.get(
                '/api/transport/expenses/options',
                authConfig()
            );


        const data =
            response.data?.data || {};


        vehicles.value =
            Array.isArray(data.vehicles)
                ? data.vehicles
                : [];


        drivers.value =
            Array.isArray(data.drivers)
                ? data.drivers
                : [];


        expenseTypes.value =
            Array.isArray(data.expense_types)
                ? data.expense_types
                : [];


        paymentMethods.value =
            Array.isArray(data.payment_methods)
                ? data.payment_methods
                : [];

    } catch (err) {

        console.error(
            err
        );

        error.value =
            err.response?.data?.message ||
            'Unable to load expense options.';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD
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

                '/api/transport/expenses',

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

                        transport_driver_id:
                            filters.value
                                .transport_driver_id ||
                            undefined,

                        expense_type:
                            filters.value
                                .expense_type ||
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
            err
        );

        error.value =
            err.response?.data?.message ||
            'Unable to load expenses.';

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

    form.value =
        emptyForm();

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

        transport_driver_id:
            record.transport_driver_id || '',

        expense_date:
            formatInputDate(
                record.expense_date
            ),

        expense_type:
            record.expense_type || '',

        quantity:
            record.quantity || '',

        unit:
            record.unit || '',

        rate:
            Number(
                record.rate || 0
            ),

        amount:
            Number(
                record.amount || 0
            ),

        vendor:
            record.vendor || '',

        payment_method:
            record.payment_method || '',

        reference_no:
            record.reference_no || '',

        description:
            record.description || '',

        remarks:
            record.remarks || ''

    };


    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| CALCULATE
|--------------------------------------------------------------------------
*/

const calculateAmount = () => {

    const quantity =
        Number(
            form.value.quantity || 0
        );

    const rate =
        Number(
            form.value.rate || 0
        );


    if (
        quantity > 0 &&
        rate > 0
    ) {

        form.value.amount =
            Number(
                (
                    quantity *
                    rate
                ).toFixed(2)
            );

    }

};


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveRecord = async () => {

    saving.value = true;

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            transport_vehicle_id:
                form.value.transport_vehicle_id
                    ? Number(
                        form.value.transport_vehicle_id
                    )
                    : null,

            transport_driver_id:
                form.value.transport_driver_id
                    ? Number(
                        form.value.transport_driver_id
                    )
                    : null,

            expense_date:
                form.value.expense_date,

            expense_type:
                form.value.expense_type,

            quantity:
                form.value.quantity === ''
                    ? null
                    : Number(
                        form.value.quantity
                    ),

            unit:
                form.value.unit ||
                null,

            rate:
                Number(
                    form.value.rate || 0
                ),

            amount:
                Number(
                    form.value.amount || 0
                ),

            vendor:
                form.value.vendor ||
                null,

            payment_method:
                form.value.payment_method ||
                null,

            reference_no:
                form.value.reference_no ||
                null,

            description:
                form.value.description ||
                null,

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

                    `/api/transport/expenses/${selectedRecord.value.id}`,

                    payload,

                    authConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/expenses',

                    payload,

                    authConfig()

                );

        }


        success.value =
            response.data?.message ||
            'Expense saved successfully.';


        showModal.value = false;


        await loadData(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            err
        );


        if (
            err.response?.status === 422
        ) {

            const errors =
                err.response.data?.errors ||
                {};


            const first =
                Object.values(errors)[0];


            formError.value =
                Array.isArray(first)
                    ? first[0]
                    : (
                        first ||
                        err.response.data?.message ||
                        'Validation failed.'
                    );

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save expense.';

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

    showViewModal.value = true;

    viewLoading.value = true;

    selectedRecord.value = null;


    try {

        const response =
            await axios.get(

                `/api/transport/expenses/${id}`,

                authConfig()

            );


        selectedRecord.value =
            response.data?.data ||
            null;

    } catch (err) {

        showViewModal.value = false;

        error.value =
            err.response?.data?.message ||
            'Unable to load expense.';

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
            `Delete expense ${record.expense_no || ''}?`
        )
    ) {

        return;

    }


    try {

        const response =
            await axios.delete(

                `/api/transport/expenses/${record.id}`,

                authConfig()

            );


        success.value =
            response.data?.message ||
            'Expense deleted successfully.';


        await loadData(
            pagination.value.current_page
        );

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete expense.';

    }

};


/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    if (!saving.value) {

        showModal.value = false;

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

        transport_driver_id: '',

        expense_type: '',

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
| FORMAT
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

    if (!value) {

        return '-';

    }


    const parts =
        String(value)
            .substring(0, 10)
            .split('-');


    if (parts.length !== 3) {

        return value;

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
| INITIAL
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadOptions();

        await loadData(1);

    }
);

</script>