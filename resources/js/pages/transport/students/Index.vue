<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Student Transport</h4>
                <small class="text-muted">
                    Assign students to transport routes, stops, vehicles and drivers
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Assign Student
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

        <!-- FILTERS -->
        <div class="card mb-3">

            <div class="card-header">
                <strong>Search Transport Assignments</strong>
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
                            placeholder="Student / Route / Vehicle / Driver"
                            @keyup.enter="loadAssignments(1)"
                        >

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Route
                        </label>

                        <select
                            v-model="filters.route_id"
                            class="form-select"
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
                            </option>

                        </select>

                    </div>

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

                            <option value="Active">
                                Active
                            </option>

                            <option value="Inactive">
                                Inactive
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">

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
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            :disabled="loading"
                            @click="
                                loadAssignments(
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
                    Student Transport Assignments
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
                                    Student
                                </th>

                                <th>
                                    Session
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
                                    Pickup Type
                                </th>

                                <th>
                                    Fee
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="125">
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
                                        Loading assignments...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-else-if="
                                    assignments.length === 0
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-bus-front fs-2 d-block mb-2"
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


                                <!-- STUDENT -->

                                <td>

                                    <strong>
                                        {{
                                            studentName(
                                                assignment.student_id
                                            )
                                        }}
                                    </strong>

                                </td>


                                <!-- SESSION -->

                                <td>

                                    {{
                                        assignment.academic_session ||
                                        '-'
                                    }}

                                </td>


                                <!-- ROUTE -->

                                <td>

                                    {{
                                        assignment.route?.name ||
                                        '-'
                                    }}

                                </td>


                                <!-- STOP -->

                                <td>

                                    {{
                                        assignment.stop?.stop_name ||
                                        '-'
                                    }}

                                </td>


                                <!-- VEHICLE -->

                                <td>

                                    {{
                                        assignment.vehicle?.vehicle_no ||
                                        '-'
                                    }}

                                </td>


                                <!-- DRIVER -->

                                <td>

                                    {{
                                        assignment.driver?.name ||
                                        '-'
                                    }}

                                </td>


                                <!-- PICKUP -->

                                <td>

                                    <span class="badge bg-info text-dark">

                                        {{
                                            assignment.pickup_type ||
                                            '-'
                                        }}

                                    </span>

                                </td>


                                <!-- FEE -->

                                <td>

                                    ₹
                                    {{
                                        formatAmount(
                                            assignment.transport_fee
                                        )
                                    }}

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                assignment.status
                                            )
                                        "
                                    >

                                        {{
                                            assignment.status
                                        }}

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
                                                viewAssignment(
                                                    assignment.id
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
                                                    assignment
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
                                                deleteAssignment(
                                                    assignment
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


        <!-- =====================================================
             ADD / EDIT MODAL
        ====================================================== -->

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
                        @submit.prevent="saveAssignment"
                    >

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Transport Assignment'
                                            : 'Assign Student to Transport'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Student transport assignment
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

                            <!-- FORM ERROR -->

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">


                                <!-- STUDENT -->

                                <div class="col-md-6">

                                    <label class="form-label">

                                        Student
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <select
                                        v-model="
                                            form.student_id
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="">
                                            Select Student
                                        </option>

                                        <option
                                            v-for="
                                                student in students
                                            "
                                            :key="student.id"
                                            :value="student.id"
                                        >

                                            {{
                                                studentDisplay(
                                                    student
                                                )
                                            }}

                                        </option>

                                    </select>

                                    <small
                                        v-if="
                                            formErrors.student_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors.student_id
                                        }}
                                    </small>

                                </div>


                                <!-- SESSION -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Academic Session
                                    </label>

                                    <select
                                        v-model="
                                            form.academic_session
                                        "
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Session
                                        </option>

                                        <option
                                            v-for="
                                                session in sessions
                                            "
                                            :key="session.id"
                                            :value="
                                                session.name
                                            "
                                        >

                                            {{
                                                session.name
                                            }}

                                        </option>

                                    </select>

                                </div>


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
                                        @change="
                                            routeChanged
                                        "
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

                                            {{
                                                route.name
                                            }}

                                            <span
                                                v-if="
                                                    route.code
                                                "
                                            >
                                                -
                                                {{
                                                    route.code
                                                }}
                                            </span>

                                        </option>

                                    </select>

                                </div>


                                <!-- STOP -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Route Stop
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
                                                    : 'Select Stop'
                                            }}

                                        </option>

                                        <option
                                            v-for="
                                                stop in routeStops
                                            "
                                            :key="stop.id"
                                            :value="stop.id"
                                        >

                                            {{
                                                stop.stop_order
                                            }}.
                                            {{
                                                stop.stop_name
                                            }}

                                            <span
                                                v-if="
                                                    stop.pickup_time
                                                "
                                            >
                                                -
                                                {{
                                                    formatTime(
                                                        stop.pickup_time
                                                    )
                                                }}
                                            </span>

                                        </option>

                                    </select>

                                </div>


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

                                            <span
                                                v-if="
                                                    vehicle.registration_no
                                                "
                                            >
                                                -
                                                {{
                                                    vehicle.registration_no
                                                }}
                                            </span>

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

                                            {{
                                                driver.name
                                            }}

                                            <span
                                                v-if="
                                                    driver.phone
                                                "
                                            >
                                                -
                                                {{
                                                    driver.phone
                                                }}
                                            </span>

                                        </option>

                                    </select>

                                </div>


                                <!-- PICKUP TYPE -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Pickup / Drop
                                    </label>

                                    <select
                                        v-model="
                                            form.pickup_type
                                        "
                                        class="form-select"
                                    >

                                        <option value="Pickup">
                                            Pickup Only
                                        </option>

                                        <option value="Drop">
                                            Drop Only
                                        </option>

                                        <option value="Both">
                                            Pickup & Drop
                                        </option>

                                    </select>

                                </div>


                                <!-- FEE -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Transport Fee
                                    </label>

                                    <input
                                        v-model.number="
                                            form.transport_fee
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        placeholder="0.00"
                                    >

                                    <small
                                        v-if="
                                            selectedStopFare !== null
                                        "
                                        class="text-muted"
                                    >
                                        Stop fare:
                                        ₹{{ formatAmount(
                                            selectedStopFare
                                        ) }}
                                    </small>

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


                                <!-- START DATE -->

                                <div class="col-md-6">

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

                                <div class="col-md-6">

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
                                :disabled="
                                    saving ||
                                    loadingOptions
                                "
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
                                Transport Assignment
                            </h5>

                            <small class="text-muted">
                                Assignment Details
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
                                Loading...
                            </div>

                        </div>


                        <div
                            v-else-if="
                                selectedAssignment
                            "
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Student
                                </label>

                                <div class="fw-bold">

                                    {{
                                        studentName(
                                            selectedAssignment.student_id
                                        )
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Academic Session
                                </label>

                                <div>

                                    {{
                                        selectedAssignment.academic_session ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Route
                                </label>

                                <div>

                                    {{
                                        selectedAssignment.route?.name ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Stop
                                </label>

                                <div>

                                    {{
                                        selectedAssignment.stop?.stop_name ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Vehicle
                                </label>

                                <div>

                                    {{
                                        selectedAssignment.vehicle?.vehicle_no ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Driver
                                </label>

                                <div>

                                    {{
                                        selectedAssignment.driver?.name ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-4">

                                <label class="text-muted small">
                                    Pickup Type
                                </label>

                                <div>

                                    <span
                                        class="badge bg-info text-dark"
                                    >
                                        {{
                                            selectedAssignment.pickup_type
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <label class="text-muted small">
                                    Transport Fee
                                </label>

                                <div class="fw-bold">

                                    ₹
                                    {{
                                        formatAmount(
                                            selectedAssignment.transport_fee
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
                                                selectedAssignment.status
                                            )
                                        "
                                    >

                                        {{
                                            selectedAssignment.status
                                        }}

                                    </span>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    Start Date
                                </label>

                                <div>

                                    {{
                                        formatDate(
                                            selectedAssignment.start_date
                                        )
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <label class="text-muted small">
                                    End Date
                                </label>

                                <div>

                                    {{
                                        formatDate(
                                            selectedAssignment.end_date
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
                                        selectedAssignment.remarks ||
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
    computed,
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const assignments = ref([]);

const students = ref([]);

const routes = ref([]);

const vehicles = ref([]);

const drivers = ref([]);

const sessions = ref([]);

const routeStops = ref([]);

const loading = ref(false);

const saving = ref(false);

const loadingOptions = ref(false);

const loadingStops = ref(false);

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
| EMPTY FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    student_id: '',

    academic_session: '',

    transport_route_id: '',

    transport_route_stop_id: '',

    transport_vehicle_id: '',

    transport_driver_id: '',

    pickup_type: 'Both',

    transport_fee: 0,

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


    return config;

};


/*
|--------------------------------------------------------------------------
| LOAD OPTIONS
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    loadingOptions.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/students/options',

                getAuthConfig()

            );


        const data =
            response.data?.data ||
            {};


        students.value =
            data.students ||
            [];


        routes.value =
            data.routes ||
            [];


        vehicles.value =
            data.vehicles ||
            [];


        drivers.value =
            data.drivers ||
            [];


        sessions.value =
            data.sessions ||
            [];


    } catch (err) {

        console.error(
            'Transport options error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load transport options.';

    } finally {

        loadingOptions.value =
            false;

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

                '/api/transport/students',

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

    form.value.transport_route_stop_id =
        '';


    if (!routeId) {

        return;

    }


    loadingStops.value = true;


    try {

        const response =
            await axios.get(

                `/api/transport/students/route/${routeId}/stops`,

                getAuthConfig()

            );


        routeStops.value =
            response.data?.data ||
            [];


    } catch (err) {

        console.error(
            'Route stops error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load route stops.';

    } finally {

        loadingStops.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| ROUTE CHANGED
|--------------------------------------------------------------------------
*/

const routeChanged = async () => {

    await loadRouteStops(
        form.value.transport_route_id
    );


    /*
    |--------------------------------------------------------------------------
    | Set route default fare
    |--------------------------------------------------------------------------
    */

    const route =
        routes.value.find(
            item =>
                Number(item.id) ===
                Number(
                    form.value.transport_route_id
                )
        );


    if (
        route &&
        !editing.value
    ) {

        form.value.transport_fee =
            Number(
                route.fare || 0
            );

    }

};


/*
|--------------------------------------------------------------------------
| SELECTED STOP FARE
|--------------------------------------------------------------------------
*/

const selectedStopFare =
    computed(() => {

        if (
            !form.value.transport_route_stop_id
        ) {

            return null;

        }


        const stop =
            routeStops.value.find(
                item =>
                    Number(item.id) ===
                    Number(
                        form.value.transport_route_stop_id
                    )
            );


        if (!stop) {

            return null;

        }


        return Number(
            stop.fare || 0
        );

    });


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = async () => {

    editing.value = false;

    selectedAssignment.value =
        null;

    form.value =
        emptyForm();

    formErrors.value = {};

    formError.value = '';

    routeStops.value = [];

    showModal.value = true;


    if (
        students.value.length === 0 ||
        routes.value.length === 0
    ) {

        await loadOptions();

    }

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
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

        student_id:
            assignment.student_id ||
            '',

        academic_session:
            assignment.academic_session ||
            '',

        transport_route_id:
            assignment.transport_route_id ||
            assignment.route?.id ||
            '',

        transport_route_stop_id:
            assignment.transport_route_stop_id ||
            assignment.stop?.id ||
            '',

        transport_vehicle_id:
            assignment.transport_vehicle_id ||
            assignment.vehicle?.id ||
            '',

        transport_driver_id:
            assignment.transport_driver_id ||
            assignment.driver?.id ||
            '',

        pickup_type:
            assignment.pickup_type ||
            'Both',

        transport_fee:
            Number(
                assignment.transport_fee ||
                0
            ),

        start_date:
            dateInput(
                assignment.start_date
            ),

        end_date:
            dateInput(
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


    if (
        students.value.length === 0
    ) {

        await loadOptions();

    }


    await loadRouteStops(
        form.value.transport_route_id
    );


    /*
    |--------------------------------------------------------------------------
    | Restore selected stop after loading
    |--------------------------------------------------------------------------
    */

    form.value.transport_route_stop_id =
        assignment.transport_route_stop_id ||
        assignment.stop?.id ||
        '';

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

    showModal.value =
        false;

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

            student_id:
                form.value.student_id,

            academic_session:
                form.value.academic_session ||
                null,

            transport_route_id:
                form.value.transport_route_id,

            transport_route_stop_id:
                form.value.transport_route_stop_id ||
                null,

            transport_vehicle_id:
                form.value.transport_vehicle_id ||
                null,

            transport_driver_id:
                form.value.transport_driver_id ||
                null,

            pickup_type:
                form.value.pickup_type,

            transport_fee:
                Number(
                    form.value.transport_fee ||
                    0
                ),

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

                    `/api/transport/students/${selectedAssignment.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/students',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Transport assignment updated successfully.'
                    : 'Transport assignment created successfully.'
            );


        showModal.value =
            false;


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
                'Unable to save transport assignment.';

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

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedAssignment.value =
        null;


    try {

        const response =
            await axios.get(

                `/api/transport/students/${id}`,

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


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load assignment details.';

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

const deleteAssignment = async (
    assignment
) => {

    const student =
        studentName(
            assignment.student_id
        );


    if (
        !confirm(
            `Are you sure you want to remove transport assignment for "${student}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/students/${assignment.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Transport assignment deleted successfully.';


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
| STUDENT NAME
|--------------------------------------------------------------------------
*/

const studentName = (
    studentId
) => {

    const student =
        students.value.find(
            item =>
                Number(item.id) ===
                Number(studentId)
        );


    if (!student) {

        return `Student #${studentId}`;

    }


    return studentDisplay(
        student
    );

};


/*
|--------------------------------------------------------------------------
| STUDENT DISPLAY
|--------------------------------------------------------------------------
|
| Supports common School ERP student fields.
|
*/

const studentDisplay = (
    student
) => {

    if (!student) {

        return '-';

    }


    const name =
        student.name ||
        student.student_name ||
        (
            [
                student.first_name,
                student.last_name
            ]
            .filter(Boolean)
            .join(' ')
        );


    const admission =
        student.admission_no ||
        student.admission_number ||
        student.roll_no ||
        '';


    if (
        name &&
        admission
    ) {

        return `${name} (${admission})`;

    }


    return (
        name ||
        admission ||
        `Student #${student.id}`
    );

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
| FORMAT DATE
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

    if (!value) {

        return '-';

    }


    const date =
        dateInput(value);


    const parts =
        date.split('-');


    if (
        parts.length !== 3
    ) {

        return value;

    }


    return `${parts[2]}-${parts[1]}-${parts[0]}`;

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
| FORMAT AMOUNT
|--------------------------------------------------------------------------
*/

const formatAmount = (
    value
) => {

    const number =
        Number(
            value || 0
        );


    return number.toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

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
| RESET FILTERS
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        route_id: '',

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


    loadAssignments(
        page
    );

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([
            loadOptions(),
            loadAssignments(1)
        ]);

    }
);

</script>