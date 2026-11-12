<template>
    <div class="container-fluid">

        <!-- PAGE HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Transport Assignments</h4>
                <small class="text-muted">
                    Assign routes, stops, vehicles and drivers
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Assignment
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


        <!-- FILTER CARD -->
        <div class="card mb-3">

            <div class="card-header">
                <strong>Search Assignments</strong>
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
                            placeholder="Route / Vehicle / Driver"
                            @keyup.enter="loadAssignments(1)"
                        >

                    </div>


                    <!-- ROUTE -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Route
                        </label>

                        <select
                            v-model="filters.route_id"
                            class="form-select"
                            @change="loadAssignments(1)"
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


                    <!-- VEHICLE -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Vehicle
                        </label>

                        <select
                            v-model="filters.vehicle_id"
                            class="form-select"
                            @change="loadAssignments(1)"
                        >

                            <option value="">
                                All
                            </option>

                            <option
                                v-for="vehicle in vehicles"
                                :key="vehicle.id"
                                :value="vehicle.id"
                            >
                                {{ vehicle.vehicle_no }}
                            </option>

                        </select>

                    </div>


                    <!-- DRIVER -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Driver
                        </label>

                        <select
                            v-model="filters.driver_id"
                            class="form-select"
                            @change="loadAssignments(1)"
                        >

                            <option value="">
                                All Drivers
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


                    <!-- STATUS -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                            @change="loadAssignments(1)"
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


                    <!-- BUTTONS -->
                    <div class="col-md-9 d-flex align-items-end gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="loading"
                            @click="loadAssignments(1)"
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
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
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
                    Transport Assignments
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
                                    Vehicle
                                </th>

                                <th>
                                    Driver
                                </th>

                                <th>
                                    Start Date
                                </th>

                                <th>
                                    End Date
                                </th>

                                <th>
                                    Status
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
                                        Loading assignments...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->
                            <tr v-else-if="assignments.length === 0">

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-diagram-3 fs-2 d-block mb-2"
                                    ></i>

                                    No transport assignments found.

                                </td>

                            </tr>


                            <!-- DATA -->
                            <tr
                                v-for="(
                                    assignment,
                                    index
                                ) in assignments"
                                :key="assignment.id"
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
                                            assignment.route?.name ||
                                            getRouteName(
                                                assignment.transport_route_id
                                            ) ||
                                            '-'
                                        }}
                                    </strong>

                                    <div
                                        v-if="
                                            assignment.route?.code ||
                                            getRouteCode(
                                                assignment.transport_route_id
                                            )
                                        "
                                        class="small text-muted"
                                    >
                                        {{
                                            assignment.route?.code ||
                                            getRouteCode(
                                                assignment.transport_route_id
                                            )
                                        }}
                                    </div>

                                </td>


                                <!-- STOP -->
                                <td>

                                    {{
                                        assignment.stop?.stop_name ||
                                        '-'
                                    }}

                                    <div
                                        v-if="
                                            assignment.stop?.stop_order
                                        "
                                        class="small text-muted"
                                    >
                                        Stop #
                                        {{
                                            assignment.stop.stop_order
                                        }}
                                    </div>

                                </td>


                                <!-- VEHICLE -->
                                <td>

                                    <strong>
                                        {{
                                            assignment.vehicle?.vehicle_no ||
                                            getVehicleName(
                                                assignment.transport_vehicle_id
                                            ) ||
                                            '-'
                                        }}
                                    </strong>

                                    <div
                                        v-if="
                                            assignment.vehicle?.registration_no
                                        "
                                        class="small text-muted"
                                    >
                                        {{
                                            assignment.vehicle.registration_no
                                        }}
                                    </div>

                                </td>


                                <!-- DRIVER -->
                                <td>

                                    <strong>
                                        {{
                                            assignment.driver?.name ||
                                            getDriverName(
                                                assignment.transport_driver_id
                                            ) ||
                                            '-'
                                        }}
                                    </strong>

                                    <div
                                        v-if="
                                            assignment.driver?.phone
                                        "
                                        class="small text-muted"
                                    >
                                        {{
                                            assignment.driver.phone
                                        }}
                                    </div>

                                </td>


                                <!-- START -->
                                <td>
                                    {{
                                        formatDate(
                                            assignment.start_date
                                        )
                                    }}
                                </td>


                                <!-- END -->
                                <td>
                                    {{
                                        formatDate(
                                            assignment.end_date
                                        )
                                    }}
                                </td>


                                <!-- STATUS -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            assignment.status === 'Active'
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            assignment.status
                                        }}
                                    </span>

                                </td>


                                <!-- ACTION -->
                                <td>

                                    <div class="d-flex gap-1">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewAssignment(
                                                    assignment.id
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
                                                    assignment
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
                                                deleteAssignment(
                                                    assignment
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

                    <form @submit.prevent="saveAssignment">

                        <!-- HEADER -->
                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Transport Assignment'
                                            : 'Add Transport Assignment'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Assign vehicle and driver to a route
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
                                <div class="col-md-6">

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
                                        @change="onRouteChange"
                                    >

                                        <option value="">
                                            Select Route
                                        </option>


                                        <option
                                            v-for="
                                                route in routes
                                            "
                                            :key="route.id"
                                            :value="route.id"
                                        >

                                            {{ route.name }}

                                            <template
                                                v-if="route.code"
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


                                <!-- STOP -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Stop
                                    </label>


                                    <select
                                        v-model="
                                            form.transport_route_stop_id
                                        "
                                        class="form-select"
                                        :disabled="
                                            !form.transport_route_id ||
                                            loadingStops
                                        "
                                    >

                                        <option value="">

                                            {{
                                                loadingStops
                                                    ? 'Loading stops...'
                                                    : 'All Route / No Specific Stop'
                                            }}

                                        </option>


                                        <option
                                            v-for="
                                                stop in routeStops
                                            "
                                            :key="stop.id"
                                            :value="stop.id"
                                        >

                                            {{ stop.stop_name }}

                                            <template
                                                v-if="
                                                    stop.stop_order
                                                "
                                            >
                                                —
                                                Stop
                                                {{
                                                    stop.stop_order
                                                }}
                                            </template>

                                        </option>

                                    </select>


                                    <small
                                        v-if="
                                            formErrors
                                                .transport_route_stop_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors
                                                .transport_route_stop_id
                                        }}
                                    </small>

                                </div>


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

                                            {{ vehicle.vehicle_no }}

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


                                    <small
                                        v-if="
                                            formErrors
                                                .transport_vehicle_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors
                                                .transport_vehicle_id
                                        }}
                                    </small>

                                </div>


                                <!-- DRIVER -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Driver
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.transport_driver_id
                                        "
                                        class="form-select"
                                        required
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

                                            <template
                                                v-if="driver.phone"
                                            >
                                                —
                                                {{ driver.phone }}
                                            </template>

                                        </option>

                                    </select>


                                    <small
                                        v-if="
                                            formErrors
                                                .transport_driver_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors
                                                .transport_driver_id
                                        }}
                                    </small>

                                </div>


                                <!-- ASSIGNMENT DATE -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Assignment Date
                                    </label>

                                    <input
                                        v-model="
                                            form.assignment_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- START DATE -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Start Date
                                    </label>

                                    <input
                                        v-model="
                                            form.start_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- END DATE -->
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


                                <!-- STATUS -->
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

                                    </select>

                                </div>


                                <!-- REMARKS -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Remarks
                                    </label>


                                    <textarea
                                        v-model="
                                            form.remarks
                                        "
                                        rows="2"
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
                                        ? 'Update Assignment'
                                        : 'Save Assignment'
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
                                Assignment Details
                            </h5>

                            <small class="text-muted">
                                Transport assignment information
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
                                Loading assignment...
                            </div>

                        </div>


                        <div
                            v-else-if="selectedAssignment"
                            class="row g-4"
                        >

                            <!-- ROUTE -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Route
                                </div>

                                <div class="fw-bold">

                                    {{
                                        selectedAssignment
                                            .route?.name ||
                                        getRouteName(
                                            selectedAssignment
                                                .transport_route_id
                                        ) ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- STOP -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Stop
                                </div>

                                <div>

                                    {{
                                        selectedAssignment
                                            .stop?.stop_name ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- VEHICLE -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Vehicle
                                </div>

                                <div class="fw-bold">

                                    {{
                                        selectedAssignment
                                            .vehicle?.vehicle_no ||
                                        getVehicleName(
                                            selectedAssignment
                                                .transport_vehicle_id
                                        ) ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- DRIVER -->
                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Driver
                                </div>

                                <div class="fw-bold">

                                    {{
                                        selectedAssignment
                                            .driver?.name ||
                                        getDriverName(
                                            selectedAssignment
                                                .transport_driver_id
                                        ) ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <!-- ASSIGNMENT DATE -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Assignment Date
                                </div>

                                <div>
                                    {{
                                        formatDate(
                                            selectedAssignment
                                                .assignment_date
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- START DATE -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Start Date
                                </div>

                                <div>
                                    {{
                                        formatDate(
                                            selectedAssignment
                                                .start_date
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- END DATE -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    End Date
                                </div>

                                <div>
                                    {{
                                        formatDate(
                                            selectedAssignment
                                                .end_date
                                        )
                                    }}
                                </div>

                            </div>


                            <!-- STATUS -->
                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Status
                                </div>

                                <span
                                    class="badge"
                                    :class="
                                        selectedAssignment.status ===
                                        'Active'
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        selectedAssignment.status
                                    }}
                                </span>

                            </div>


                            <!-- REMARKS -->
                            <div class="col-md-12">

                                <div class="text-muted small">
                                    Remarks
                                </div>

                                <div>
                                    {{
                                        selectedAssignment
                                            .remarks ||
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

const assignments = ref([]);

const routes = ref([]);

const vehicles = ref([]);

const drivers = ref([]);

const routeStops = ref([]);

const loading = ref(false);

const loadingStops = ref(false);

const saving = ref(false);

const viewLoading = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showModal = ref(false);

const showViewModal = ref(false);

const editing = ref(false);

const selectedAssignment = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    route_id: '',

    vehicle_id: '',

    driver_id: '',

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

    transport_route_id: '',

    transport_route_stop_id: '',

    transport_vehicle_id: '',

    transport_driver_id: '',

    assignment_date: '',

    start_date: '',

    end_date: '',

    status: 'Active',

    remarks: ''

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
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) {
        config.headers['X-School-Id'] = activeSchoolId;
    }


    return config;

};


/*
|--------------------------------------------------------------------------
| LOAD OPTIONS
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    try {

        const response =
            await axios.get(

                '/api/transport/assignments/options',

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


        vehicles.value =
            Array.isArray(
                data.vehicles
            )
                ? data.vehicles
                : [];


        drivers.value =
            Array.isArray(
                data.drivers
            )
                ? data.drivers
                : [];


    } catch (err) {

        console.error(
            'Assignment options error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load transport options.';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD ASSIGNMENTS
|--------------------------------------------------------------------------
*/

const loadAssignments = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/assignments',

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
                            undefined,

                        vehicle_id:
                            filters.value.vehicle_id ||
                            undefined,

                        driver_id:
                            filters.value.driver_id ||
                            undefined,

                        status:
                            filters.value.status ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        assignments.value =
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
            'Assignment loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load transport assignments.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| LOAD ROUTE STOPS
|--------------------------------------------------------------------------
*/

const loadRouteStops = async (
    routeId
) => {

    routeStops.value = [];


    if (!routeId) {

        return;

    }


    loadingStops.value = true;


    try {

        const response =
            await axios.get(

                `/api/transport/assignments/route/${routeId}/stops`,

                getAuthConfig()

            );


        const data =
            response.data?.data;


        routeStops.value =
            Array.isArray(data)
                ? data
                : [];

    } catch (err) {

        console.error(
            'Route stops error:',
            err
        );


        routeStops.value = [];

    } finally {

        loadingStops.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| ROUTE CHANGE
|--------------------------------------------------------------------------
*/

const onRouteChange = async () => {

    form.value.transport_route_stop_id = '';

    await loadRouteStops(
        form.value.transport_route_id
    );

};


/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedAssignment.value = null;

    form.value =
        emptyForm();

    routeStops.value = [];

    formErrors.value = {};

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/

const openEdit = async (
    assignment
) => {

    editing.value = true;

    selectedAssignment.value =
        assignment;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        transport_route_id:
            assignment.transport_route_id ||
            '',

        transport_route_stop_id:
            assignment.transport_route_stop_id ||
            '',

        transport_vehicle_id:
            assignment.transport_vehicle_id ||
            '',

        transport_driver_id:
            assignment.transport_driver_id ||
            '',

        assignment_date:
            formatDateInput(
                assignment.assignment_date
            ),

        start_date:
            formatDateInput(
                assignment.start_date
            ),

        end_date:
            formatDateInput(
                assignment.end_date
            ),

        status:
            assignment.status ||
            'Active',

        remarks:
            assignment.remarks ||
            ''

    };


    showModal.value = true;


    await loadRouteStops(
        form.value.transport_route_id
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
| SAVE
|--------------------------------------------------------------------------
*/

const saveAssignment = async () => {

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

            transport_route_stop_id:
                form.value.transport_route_stop_id
                    ? Number(
                        form.value.transport_route_stop_id
                    )
                    : null,

            transport_vehicle_id:
                Number(
                    form.value.transport_vehicle_id
                ),

            transport_driver_id:
                Number(
                    form.value.transport_driver_id
                ),

            assignment_date:
                form.value.assignment_date ||
                null,

            start_date:
                form.value.start_date ||
                null,

            end_date:
                form.value.end_date ||
                null,

            status:
                form.value.status,

            remarks:
                form.value.remarks ||
                null

        };


        let response;


        if (
            editing.value &&
            selectedAssignment.value
        ) {

            response =
                await axios.put(

                    `/api/transport/assignments/${selectedAssignment.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/assignments',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Assignment updated successfully.'
                    : 'Assignment created successfully.'
            );


        showModal.value = false;


        await loadAssignments(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Assignment save error:',
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
                'Unable to save assignment.';

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

const viewAssignment = async (
    id
) => {

    showViewModal.value = true;

    viewLoading.value = true;

    selectedAssignment.value = null;


    try {

        const response =
            await axios.get(

                `/api/transport/assignments/${id}`,

                getAuthConfig()

            );


        selectedAssignment.value =
            response.data?.data ||
            null;

    } catch (err) {

        console.error(
            'Assignment view error:',
            err
        );


        showViewModal.value = false;


        error.value =
            err.response?.data?.message ||
            'Unable to load assignment.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteAssignment = async (
    assignment
) => {

    if (
        !confirm(
            'Are you sure you want to delete this transport assignment?'
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/assignments/${assignment.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Assignment deleted successfully.';


        await loadAssignments(
            pagination.value.current_page
        );


        if (
            assignments.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadAssignments(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Assignment delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete assignment.';

    }

};


/*
|--------------------------------------------------------------------------
| LOOKUPS
|--------------------------------------------------------------------------
*/

const getRouteName = (
    id
) => {

    const route =
        routes.value.find(
            item =>
                Number(item.id) ===
                Number(id)
        );


    return route?.name || '';

};


const getRouteCode = (
    id
) => {

    const route =
        routes.value.find(
            item =>
                Number(item.id) ===
                Number(id)
        );


    return route?.code || '';

};


const getVehicleName = (
    id
) => {

    const vehicle =
        vehicles.value.find(
            item =>
                Number(item.id) ===
                Number(id)
        );


    return vehicle?.vehicle_no || '';

};


const getDriverName = (
    id
) => {

    const driver =
        drivers.value.find(
            item =>
                Number(item.id) ===
                Number(id)
        );


    return driver?.name || '';

};


/*
|--------------------------------------------------------------------------
| DATE FORMAT
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


const formatDateInput = (
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
| RESET
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        route_id: '',

        vehicle_id: '',

        driver_id: '',

        status: '',

        per_page: 20

    };


    loadAssignments(1);

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


    loadAssignments(page);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadOptions();

        await loadAssignments(1);

    }
);



const onTenantChanged = () => { loadAssignments(1); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>