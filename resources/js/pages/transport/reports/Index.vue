<template>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Transport Reports
                </h4>

                <small class="text-muted">
                    Transport operations, fuel, expenses and maintenance reports
                </small>
            </div>

            <button
                class="btn btn-outline-primary"
                @click="loadReport"
            >
                <i class="bi bi-arrow-clockwise me-1"></i>
                Refresh
            </button>

        </div>


        <!-- FILTER -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">

                        <label class="form-label">
                            Report
                        </label>

                        <select
                            v-model="reportType"
                            class="form-select"
                            @change="loadReport"
                        >

                            <option value="summary">
                                Summary
                            </option>

                            <option value="vehicles">
                                Vehicle Report
                            </option>

                            <option value="drivers">
                                Driver Report
                            </option>

                            <option value="fuel">
                                Fuel Report
                            </option>

                            <option value="maintenance">
                                Maintenance Report
                            </option>

                            <option value="expenses">
                                Expense Report
                            </option>

                        </select>

                    </div>


                    <div class="col-md-3">

                        <label class="form-label">
                            Vehicle
                        </label>

                        <select
                            v-model="filters.transport_vehicle_id"
                            class="form-select"
                            @change="loadReport"
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
                                    —
                                    {{ vehicle.registration_no }}
                                </template>

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


                    <div class="col-md-12">

                        <button
                            class="btn btn-primary"
                            @click="loadReport"
                        >
                            <i class="bi bi-search me-1"></i>
                            Generate Report
                        </button>

                        <button
                            class="btn btn-outline-secondary ms-2"
                            @click="resetFilters"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- ERROR -->

        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>


        <!-- SUMMARY -->

        <div
            v-if="reportType === 'summary'"
            class="row g-3"
        >

            <div class="col-md-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Vehicles
                        </small>

                        <h3 class="mb-0">
                            {{ summary.vehicles }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Drivers
                        </small>

                        <h3 class="mb-0">
                            {{ summary.drivers }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <small class="text-muted">
                            Fuel Cost
                        </small>

                        <h3 class="mb-0">
                            ₹{{ money(summary.fuel_cost) }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <small class="text-muted">
                            Maintenance
                        </small>

                        <h3 class="mb-0">
                            ₹{{ money(summary.maintenance_cost) }}
                        </h3>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Expenses
                        </small>

                        <h3 class="mb-0">
                            ₹{{ money(summary.total_expenses) }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <!-- TABLE -->

        <div
            v-else
            class="card"
        >

            <div class="card-header">

                <strong>
                    {{ reportTitle }}
                </strong>

                <span class="float-end text-muted">
                    {{ rows.length }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div
                    v-if="loading"
                    class="text-center py-5"
                >

                    <div class="spinner-border text-primary"></div>

                    <div class="text-muted mt-2">
                        Generating report...
                    </div>

                </div>


                <div
                    v-else
                    class="table-responsive"
                >

                    <!-- VEHICLES -->

                    <table
                        v-if="reportType === 'vehicles'"
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>
                                <th>Vehicle</th>
                                <th>Registration</th>
                                <th>Type</th>
                                <th>Model</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Expenses</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="row in rows"
                                :key="row.id"
                            >

                                <td>
                                    {{ row.vehicle_no }}
                                </td>

                                <td>
                                    {{ row.registration_no || '-' }}
                                </td>

                                <td>
                                    {{ row.vehicle_type || '-' }}
                                </td>

                                <td>
                                    {{ row.model || '-' }}
                                </td>

                                <td>
                                    {{ row.capacity || '-' }}
                                </td>

                                <td>
                                    {{ row.status }}
                                </td>

                                <td>
                                    ₹{{ money(row.expense_total) }}
                                </td>

                            </tr>

                        </tbody>

                    </table>


                    <!-- DRIVERS -->

                    <table
                        v-else-if="reportType === 'drivers'"
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Phone</th>
                                <th>License</th>
                                <th>Status</th>
                                <th>Expenses</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="row in rows"
                                :key="row.id"
                            >

                                <td>
                                    {{ row.name }}
                                </td>

                                <td>
                                    {{ row.code || '-' }}
                                </td>

                                <td>
                                    {{ row.phone || '-' }}
                                </td>

                                <td>
                                    {{ row.license_no || '-' }}
                                </td>

                                <td>
                                    {{ row.status }}
                                </td>

                                <td>
                                    ₹{{ money(row.expense_total) }}
                                </td>

                            </tr>

                        </tbody>

                    </table>


                    <!-- FUEL -->

                    <table
                        v-else-if="reportType === 'fuel'"
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>
                                <th>Date</th>
                                <th>Expense No</th>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="row in rows"
                                :key="row.id"
                            >

                                <td>
                                    {{ date(row.expense_date) }}
                                </td>

                                <td>
                                    {{ row.expense_no || '-' }}
                                </td>

                                <td>
                                    {{ row.vehicle_no || '-' }}
                                </td>

                                <td>
                                    {{ row.expense_type }}
                                </td>

                                <td>
                                    {{ row.quantity || '-' }}
                                    {{ row.unit || '' }}
                                </td>

                                <td>
                                    ₹{{ money(row.rate) }}
                                </td>

                                <td>
                                    <strong>
                                        ₹{{ money(row.amount) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ row.vendor || '-' }}
                                </td>

                            </tr>

                        </tbody>

                    </table>


                    <!-- MAINTENANCE -->

                    <table
                        v-else-if="reportType === 'maintenance'"
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>
                                <th>Date</th>
                                <th>Maintenance No</th>
                                <th>Vehicle</th>
                                <th>Type</th>
                                <th>Provider</th>
                                <th>Cost</th>
                                <th>Status</th>
                                <th>Next Service</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="row in rows"
                                :key="row.id"
                            >

                                <td>
                                    {{ date(row.maintenance_date) }}
                                </td>

                                <td>
                                    {{ row.maintenance_no }}
                                </td>

                                <td>
                                    {{ row.vehicle_no }}
                                </td>

                                <td>
                                    {{ row.maintenance_type }}
                                </td>

                                <td>
                                    {{ row.service_provider || '-' }}
                                </td>

                                <td>
                                    ₹{{ money(row.cost) }}
                                </td>

                                <td>
                                    {{ row.status }}
                                </td>

                                <td>
                                    {{ date(row.next_service_date) }}
                                </td>

                            </tr>

                        </tbody>

                    </table>


                    <!-- EXPENSES -->

                    <table
                        v-else-if="reportType === 'expenses'"
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>
                                <th>Date</th>
                                <th>Expense No</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="row in rows"
                                :key="row.id"
                            >

                                <td>
                                    {{ date(row.expense_date) }}
                                </td>

                                <td>
                                    {{ row.expense_no || '-' }}
                                </td>

                                <td>
                                    {{ row.vehicle_no || '-' }}
                                </td>

                                <td>
                                    {{ row.driver_name || '-' }}
                                </td>

                                <td>
                                    {{ row.expense_type }}
                                </td>

                                <td>
                                    <strong>
                                        ₹{{ money(row.amount) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ row.vendor || '-' }}
                                </td>

                            </tr>

                        </tbody>

                    </table>


                    <div
                        v-if="rows.length === 0"
                        class="text-center text-muted py-5"
                    >
                        No records found.
                    </div>

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
} from 'vue';

import axios from 'axios';


const loading = ref(false);

const error = ref('');

const reportType = ref('summary');

const rows = ref([]);

const vehicles = ref([]);

const summary = ref({

    vehicles: 0,

    drivers: 0,

    fuel_cost: 0,

    maintenance_cost: 0,

    total_expenses: 0

});


const filters = ref({

    transport_vehicle_id: '',

    date_from: '',

    date_to: ''

});


const reportTitle = computed(() => {

    const titles = {

        vehicles: 'Vehicle Report',

        drivers: 'Driver Report',

        fuel: 'Fuel Report',

        maintenance: 'Maintenance Report',

        expenses: 'Transport Expense Report'

    };

    return titles[reportType.value] || '';

});


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


const loadOptions = async () => {

    try {

        const response =
            await axios.get(
                '/api/transport/reports/options',
                authConfig()
            );


        vehicles.value =
            response.data?.data?.vehicles || [];

    } catch (e) {

        console.error(e);

    }

};


const loadReport = async () => {

    loading.value = true;

    error.value = '';


    try {

        const params = {

            transport_vehicle_id:
                filters.value.transport_vehicle_id ||
                undefined,

            date_from:
                filters.value.date_from ||
                undefined,

            date_to:
                filters.value.date_to ||
                undefined

        };


        let endpoint;


        switch (reportType.value) {

            case 'vehicles':
                endpoint =
                    '/api/transport/reports/vehicles';
                break;

            case 'drivers':
                endpoint =
                    '/api/transport/reports/drivers';
                break;

            case 'fuel':
                endpoint =
                    '/api/transport/reports/fuel';
                break;

            case 'maintenance':
                endpoint =
                    '/api/transport/reports/maintenance';
                break;

            case 'expenses':
                endpoint =
                    '/api/transport/reports/expenses';
                break;

            default:
                endpoint =
                    '/api/transport/reports/summary';

        }


        const response =
            await axios.get(
                endpoint,
                {
                    ...authConfig(),
                    params
                }
            );


        if (
            reportType.value === 'summary'
        ) {

            summary.value =
                response.data?.data || {};

            rows.value = [];

        } else {

            rows.value =
                response.data?.data || [];

        }

    } catch (e) {

        console.error(e);

        error.value =
            e.response?.data?.message ||
            'Unable to generate report.';

    } finally {

        loading.value = false;

    }

};


const resetFilters = () => {

    filters.value = {

        transport_vehicle_id: '',

        date_from: '',

        date_to: ''

    };

    loadReport();

};


const date = (
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


const money = (
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


onMounted(
    async () => {

        await loadOptions();

        await loadReport();

    }
);

</script>