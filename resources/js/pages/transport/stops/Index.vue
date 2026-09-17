<template>
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Transport Stops</h4>
                <small class="text-muted">
                    Manage route stops, timings and transport fares
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Stop
            </button>
        </div>

        <!-- SUCCESS -->
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

        <!-- ERROR -->
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
                <strong>Search Stops</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <!-- SEARCH -->
                    <div class="col-md-5">
                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Stop name / code / route"
                            @keyup.enter="loadStops(1)"
                        >
                    </div>

                    <!-- ROUTE -->
                    <div class="col-md-4">
                        <label class="form-label">
                            Route
                        </label>

                        <select
                            v-model="filters.route_id"
                            class="form-select"
                            @change="loadStops(1)"
                        >
                            <option value="">
                                All Routes
                            </option>

                            <option
                                v-for="route in routes"
                                :key="route.id"
                                :value="route.id"
                            >
                                {{ route.name }}
                                <template v-if="route.code">
                                    ({{ route.code }})
                                </template>
                            </option>
                        </select>
                    </div>

                    <!-- BUTTONS -->
                    <div class="col-md-3 d-flex align-items-end gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="loading"
                            @click="loadStops(1)"
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

                    </div>

                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Transport Stops
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Route
                                </th>

                                <th>
                                    Stop
                                </th>

                                <th>
                                    Code
                                </th>

                                <th width="80">
                                    Order
                                </th>

                                <th>
                                    Pickup
                                </th>

                                <th>
                                    Drop
                                </th>

                                <th>
                                    Fare
                                </th>

                                <th width="135">
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
                                        Loading stops...
                                    </div>

                                </td>

                            </tr>

                            <!-- EMPTY -->
                            <tr
                                v-else-if="stops.length === 0"
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-geo-alt fs-2 d-block mb-2"
                                    ></i>

                                    No stops found.

                                </td>

                            </tr>

                            <!-- DATA -->
                            <tr
                                v-for="(
                                    stop,
                                    index
                                ) in stops"
                                :key="stop.id"
                            >

                                <!-- NUMBER -->
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

                                <!-- ROUTE -->
                                <td>

                                    <strong>
                                        {{
                                            stop.route?.name ||
                                            getRouteName(
                                                stop.transport_route_id
                                            ) ||
                                            '-'
                                        }}
                                    </strong>

                                    <div
                                        v-if="
                                            stop.route?.code ||
                                            getRouteCode(
                                                stop.transport_route_id
                                            )
                                        "
                                        class="small text-muted"
                                    >
                                        {{
                                            stop.route?.code ||
                                            getRouteCode(
                                                stop.transport_route_id
                                            )
                                        }}
                                    </div>

                                </td>

                                <!-- STOP -->
                                <td>

                                    <strong>
                                        {{ stop.stop_name }}
                                    </strong>

                                </td>

                                <!-- CODE -->
                                <td>

                                    <span
                                        v-if="stop.stop_code"
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ stop.stop_code }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>

                                <!-- ORDER -->
                                <td class="text-center">

                                    <span class="badge bg-secondary">
                                        {{ stop.stop_order }}
                                    </span>

                                </td>

                                <!-- PICKUP -->
                                <td>
                                    {{ formatTime(stop.pickup_time) }}
                                </td>

                                <!-- DROP -->
                                <td>
                                    {{ formatTime(stop.drop_time) }}
                                </td>

                                <!-- FARE -->
                                <td>

                                    ₹
                                    {{
                                        formatAmount(stop.fare)
                                    }}

                                </td>

                                <!-- ACTION -->
                                <td>

                                    <div class="d-flex gap-1">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewStop(stop.id)
                                            "
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            @click="
                                                openEdit(stop)
                                            "
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                            @click="
                                                deleteStop(stop)
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


        <!-- =====================================================
             ADD / EDIT MODAL
        ====================================================== -->

        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form @submit.prevent="saveStop">

                        <!-- HEADER -->
                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Transport Stop'
                                            : 'Add Transport Stop'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Configure route stop information
                                </small>

                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <!-- BODY -->
                        <div class="modal-body">

                            <!-- FORM ERROR -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- ROUTE -->
                                <div class="col-md-12">

                                    <label class="form-label">

                                        Route
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="
                                            form.transport_route_id
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Route
                                        </option>

                                        <option
                                            v-for="
                                                route in routes
                                            "
                                            :key="
                                                route.id
                                            "
                                            :value="
                                                route.id
                                            "
                                        >

                                            {{ route.name }}

                                            <template
                                                v-if="
                                                    route.code
                                                "
                                            >
                                                ({{ route.code }})
                                            </template>

                                        </option>

                                    </select>

                                    <small
                                        v-if="
                                            formErrors.transport_route_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors
                                                .transport_route_id
                                        }}
                                    </small>

                                </div>


                                <!-- STOP NAME -->
                                <div class="col-md-8">

                                    <label class="form-label">

                                        Stop Name
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.stop_name
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g. Subhasgram"
                                        required
                                    >

                                    <small
                                        v-if="
                                            formErrors.stop_name
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.stop_name
                                        }}
                                    </small>

                                </div>


                                <!-- STOP CODE -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Stop Code
                                    </label>

                                    <input
                                        v-model="
                                            form.stop_code
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="ST001"
                                    >

                                    <small
                                        v-if="
                                            formErrors.stop_code
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.stop_code
                                        }}
                                    </small>

                                </div>


                                <!-- STOP ORDER -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Stop Order
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model.number="
                                            form.stop_order
                                        "
                                        type="number"
                                        min="1"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- PICKUP -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Pickup Time
                                    </label>

                                    <input
                                        v-model="
                                            form.pickup_time
                                        "
                                        type="time"
                                        class="form-control"
                                    >

                                </div>


                                <!-- DROP -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Drop Time
                                    </label>

                                    <input
                                        v-model="
                                            form.drop_time
                                        "
                                        type="time"
                                        class="form-control"
                                    >

                                </div>


                                <!-- FARE -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fare
                                    </label>

                                    <input
                                        v-model.number="
                                            form.fare
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        placeholder="0.00"
                                    >

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


                        <!-- FOOTER -->
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
                                        ? 'Update Stop'
                                        : 'Save Stop'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =====================================================
             VIEW MODAL
        ====================================================== -->

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
                                Stop Details
                            </h5>

                            <small class="text-muted">
                                Transport stop information
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
                                Loading stop...
                            </div>

                        </div>


                        <div
                            v-else-if="selectedStop"
                            class="row g-4"
                        >

                            <!-- ROUTE -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Route
                                </div>

                                <div class="fw-bold">

                                    {{
                                        selectedStop.route?.name ||
                                        getRouteName(
                                            selectedStop.transport_route_id
                                        ) ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- CODE -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Route Code
                                </div>

                                <div>

                                    {{
                                        selectedStop.route?.code ||
                                        getRouteCode(
                                            selectedStop.transport_route_id
                                        ) ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- STOP -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Stop Name
                                </div>

                                <div class="fw-bold">
                                    {{
                                        selectedStop.stop_name
                                    }}
                                </div>

                            </div>


                            <!-- STOP CODE -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Stop Code
                                </div>

                                <div>

                                    {{
                                        selectedStop.stop_code ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- ORDER -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Stop Order
                                </div>

                                <div>
                                    {{
                                        selectedStop.stop_order
                                    }}
                                </div>

                            </div>


                            <!-- PICKUP -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Pickup Time
                                </div>

                                <div>
                                    {{
                                        formatTime(
                                            selectedStop.pickup_time
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- DROP -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Drop Time
                                </div>

                                <div>
                                    {{
                                        formatTime(
                                            selectedStop.drop_time
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- FARE -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Fare
                                </div>

                                <div class="fw-bold">
                                    ₹
                                    {{
                                        formatAmount(
                                            selectedStop.fare
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- REMARKS -->
                            <div class="col-md-12">

                                <div class="text-muted small">
                                    Remarks
                                </div>

                                <div>
                                    {{
                                        selectedStop.remarks ||
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

const stops = ref([]);

const routes = ref([]);

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

const selectedStop = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    route_id: '',

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

    transport_route_id: '',

    stop_name: '',

    stop_code: '',

    stop_order: 1,

    pickup_time: '',

    drop_time: '',

    fare: 0,

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
| LOAD ROUTE OPTIONS
|--------------------------------------------------------------------------
*/

const loadRoutes = async () => {

    try {

        const response =
            await axios.get(

                '/api/transport/stops/options',

                getAuthConfig()

            );


        const data =
            response.data?.data ||
            {};


        routes.value =
            Array.isArray(
                data.routes
            )
                ? data.routes
                : [];


    } catch (err) {

        console.error(
            'Route options error:',
            err
        );


        /*
        |--------------------------------------------------------------------------
        | Fallback: try routes API
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await axios.get(

                    '/api/transport/routes',

                    {

                        ...getAuthConfig(),

                        params: {
                            per_page: 1000
                        }

                    }

                );


            const data =
                response.data?.data;


            routes.value =
                Array.isArray(data?.data)
                    ? data.data
                    : Array.isArray(data)
                        ? data
                        : [];


        } catch (fallbackError) {

            console.error(
                'Route fallback error:',
                fallbackError
            );

            routes.value = [];

        }

    }

};


/*
|--------------------------------------------------------------------------
| LOAD STOPS
|--------------------------------------------------------------------------
*/

const loadStops = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/stops',

                {

                    ...getAuthConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        route_id:
                            filters.value.route_id ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        stops.value =
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
            'Stops loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stops.';

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

    selectedStop.value = null;

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
    stop
) => {

    editing.value = true;

    selectedStop.value =
        stop;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        transport_route_id:
            stop.transport_route_id ||
            '',

        stop_name:
            stop.stop_name ||
            '',

        stop_code:
            stop.stop_code ||
            '',

        stop_order:
            Number(
                stop.stop_order ||
                1
            ),

        pickup_time:
            formatTimeInput(
                stop.pickup_time
            ),

        drop_time:
            formatTimeInput(
                stop.drop_time
            ),

        fare:
            Number(
                stop.fare ||
                0
            ),

        remarks:
            stop.remarks ||
            ''

    };


    showModal.value = true;

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
| SAVE STOP
|--------------------------------------------------------------------------
*/

const saveStop = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';

    saving.value = true;


    try {

        const payload = {

            transport_route_id:
                Number(
                    form.value.transport_route_id
                ),

            stop_name:
                form.value.stop_name,

            stop_code:
                form.value.stop_code ||
                null,

            stop_order:
                Number(
                    form.value.stop_order ||
                    1
                ),

            pickup_time:
                form.value.pickup_time ||
                null,

            drop_time:
                form.value.drop_time ||
                null,

            fare:
                Number(
                    form.value.fare ||
                    0
                ),

            remarks:
                form.value.remarks ||
                null

        };


        let response;


        if (
            editing.value &&
            selectedStop.value
        ) {

            response =
                await axios.put(

                    `/api/transport/stops/${selectedStop.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/stops',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Stop updated successfully.'
                    : 'Stop created successfully.'
            );


        showModal.value = false;


        await loadStops(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Stop save error:',
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
                'Unable to save stop.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW STOP
|--------------------------------------------------------------------------
*/

const viewStop = async (
    id
) => {

    showViewModal.value = true;

    viewLoading.value = true;

    selectedStop.value = null;


    try {

        const response =
            await axios.get(

                `/api/transport/stops/${id}`,

                getAuthConfig()

            );


        selectedStop.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Stop view error:',
            err
        );


        showViewModal.value = false;


        error.value =
            err.response?.data?.message ||
            'Unable to load stop details.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE STOP
|--------------------------------------------------------------------------
*/

const deleteStop = async (
    stop
) => {

    if (
        !confirm(
            `Are you sure you want to delete "${stop.stop_name}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/stops/${stop.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Stop deleted successfully.';


        await loadStops(
            pagination.value.current_page
        );


        if (
            stops.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadStops(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Stop delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete stop.';

    }

};


/*
|--------------------------------------------------------------------------
| GET ROUTE NAME
|--------------------------------------------------------------------------
*/

const getRouteName = (
    routeId
) => {

    const route =
        routes.value.find(
            item =>
                Number(item.id) ===
                Number(routeId)
        );


    return route?.name || '';

};


/*
|--------------------------------------------------------------------------
| GET ROUTE CODE
|--------------------------------------------------------------------------
*/

const getRouteCode = (
    routeId
) => {

    const route =
        routes.value.find(
            item =>
                Number(item.id) ===
                Number(routeId)
        );


    return route?.code || '';

};


/*
|--------------------------------------------------------------------------
| FORMAT AMOUNT
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
| FORMAT TIME
|--------------------------------------------------------------------------
*/

const formatTime = (
    value
) => {

    if (!value) {
        return '-';
    }


    return String(
        value
    ).substring(
        0,
        5
    );

};


/*
|--------------------------------------------------------------------------
| FORMAT TIME FOR INPUT
|--------------------------------------------------------------------------
*/

const formatTimeInput = (
    value
) => {

    if (!value) {
        return '';
    }


    return String(
        value
    ).substring(
        0,
        5
    );

};


/*
|--------------------------------------------------------------------------
| RESET FILTERS
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        route_id: '',

        per_page: 20

    };


    loadStops(1);

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


    loadStops(page);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadRoutes();

        await loadStops(1);

    }
);

</script>