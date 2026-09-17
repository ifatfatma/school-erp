<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Transport Routes</h4>
                <small class="text-muted">
                    Manage transport routes and route stops
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Route
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
                <strong>Search Routes</strong>
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
                            placeholder="Route name / code / location"
                            @keyup.enter="loadRoutes(1)"
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
                            @click="loadRoutes(1)"
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
                                loadRoutes(
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


        <!-- ROUTES TABLE -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Transport Routes
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
                                    Code
                                </th>

                                <th>
                                    Route Name
                                </th>

                                <th>
                                    Start
                                </th>

                                <th>
                                    End
                                </th>

                                <th>
                                    Stops
                                </th>

                                <th>
                                    Distance
                                </th>

                                <th>
                                    Fare
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="145">
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

                                    <div class="text-muted mt-2">
                                        Loading routes...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-else-if="
                                    routes.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-sign-turn-right fs-2 d-block mb-2"
                                    ></i>

                                    No routes found.

                                </td>

                            </tr>


                            <!-- DATA -->

                            <tr
                                v-for="(
                                    route,
                                    index
                                ) in routes"
                                :key="route.id"
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

                                    <span
                                        v-if="route.code"
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ route.code }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>


                                <td>

                                    <strong>
                                        {{ route.name }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        route.start_location ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        route.end_location ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    <span class="badge bg-info text-dark">

                                        {{
                                            route.stops?.length ||
                                            0
                                        }}

                                    </span>

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            route.distance !== null &&
                                            route.distance !== undefined
                                        "
                                    >

                                        {{
                                            route.distance
                                        }}
                                        km

                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>


                                <td>

                                    ₹
                                    {{
                                        formatAmount(
                                            route.fare
                                        )
                                    }}

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                route.status
                                            )
                                        "
                                    >
                                        {{ route.status }}
                                    </span>

                                </td>


                                <td>

                                    <div
                                        class="d-flex gap-1"
                                    >

                                        <!-- VIEW -->

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-info"
                                            title="View"
                                            @click="
                                                viewRoute(
                                                    route.id
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-eye"
                                            ></i>

                                        </button>


                                        <!-- EDIT -->

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            @click="
                                                openEdit(
                                                    route
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>

                                        </button>


                                        <!-- DELETE -->

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                            @click="
                                                deleteRoute(
                                                    route
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


        <!-- ======================================================
             ADD / EDIT MODAL
        ======================================================= -->

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
                        @submit.prevent="saveRoute"
                    >

                        <!-- HEADER -->

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Transport Route'
                                            : 'Add Transport Route'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Route and stop information
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

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <!-- ROUTE INFORMATION -->

                            <div class="row g-3">

                                <!-- NAME -->

                                <div class="col-md-6">

                                    <label class="form-label">

                                        Route Name
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
                                        placeholder="e.g. Subhasgram Route"
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
                                        Route Code
                                    </label>

                                    <input
                                        v-model="
                                            form.code
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g. R001"
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


                                <!-- START -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Start Location
                                    </label>

                                    <input
                                        v-model="
                                            form.start_location
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Starting location"
                                    >

                                </div>


                                <!-- END -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        End Location
                                    </label>

                                    <input
                                        v-model="
                                            form.end_location
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="School / ending location"
                                    >

                                </div>


                                <!-- DISTANCE -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Distance (KM)
                                    </label>

                                    <input
                                        v-model.number="
                                            form.distance
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        placeholder="0"
                                    >

                                </div>


                                <!-- TRAVEL TIME -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Travel Time
                                    </label>

                                    <input
                                        v-model="
                                            form.travel_time
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="e.g. 45 minutes"
                                    >

                                </div>


                                <!-- FARE -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Default Fare
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

                            </div>


                            <!-- DIVIDER -->

                            <hr class="my-4">


                            <!-- STOPS HEADER -->

                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >

                                <div>

                                    <h6 class="mb-1">
                                        Route Stops
                                    </h6>

                                    <small class="text-muted">
                                        Add stops in travelling order
                                    </small>

                                </div>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    @click="addStop"
                                >

                                    <i
                                        class="bi bi-plus-lg me-1"
                                    ></i>

                                    Add Stop

                                </button>

                            </div>


                            <!-- NO STOPS -->

                            <div
                                v-if="
                                    form.stops.length === 0
                                "
                                class="border rounded p-4 text-center text-muted"
                            >

                                <i
                                    class="bi bi-geo-alt fs-2 d-block mb-2"
                                ></i>

                                No stops added.

                                <div class="mt-2">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        @click="addStop"
                                    >
                                        Add First Stop
                                    </button>

                                </div>

                            </div>


                            <!-- STOPS -->

                            <div
                                v-else
                                class="table-responsive border rounded"
                            >

                                <table
                                    class="table table-bordered align-middle mb-0"
                                >

                                    <thead class="table-light">

                                        <tr>

                                            <th
                                                width="65"
                                                class="text-center"
                                            >
                                                #
                                            </th>

                                            <th>
                                                Stop Name
                                            </th>

                                            <th>
                                                Stop Code
                                            </th>

                                            <th
                                                width="110"
                                            >
                                                Order
                                            </th>

                                            <th
                                                width="130"
                                            >
                                                Pickup
                                            </th>

                                            <th
                                                width="130"
                                            >
                                                Drop
                                            </th>

                                            <th
                                                width="120"
                                            >
                                                Fare
                                            </th>

                                            <th
                                                width="65"
                                            >
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="(
                                                stop,
                                                index
                                            ) in form.stops"
                                            :key="
                                                stop._key
                                            "
                                        >

                                            <!-- NUMBER -->

                                            <td
                                                class="text-center fw-bold"
                                            >

                                                {{
                                                    index + 1
                                                }}

                                            </td>


                                            <!-- NAME -->

                                            <td>

                                                <input
                                                    v-model="
                                                        stop.stop_name
                                                    "
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Stop name"
                                                    required
                                                >

                                            </td>


                                            <!-- CODE -->

                                            <td>

                                                <input
                                                    v-model="
                                                        stop.stop_code
                                                    "
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="ST01"
                                                >

                                            </td>


                                            <!-- ORDER -->

                                            <td>

                                                <input
                                                    v-model.number="
                                                        stop.stop_order
                                                    "
                                                    type="number"
                                                    min="1"
                                                    class="form-control"
                                                >

                                            </td>


                                            <!-- PICKUP -->

                                            <td>

                                                <input
                                                    v-model="
                                                        stop.pickup_time
                                                    "
                                                    type="time"
                                                    class="form-control"
                                                >

                                            </td>


                                            <!-- DROP -->

                                            <td>

                                                <input
                                                    v-model="
                                                        stop.drop_time
                                                    "
                                                    type="time"
                                                    class="form-control"
                                                >

                                            </td>


                                            <!-- FARE -->

                                            <td>

                                                <input
                                                    v-model.number="
                                                        stop.fare
                                                    "
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="form-control"
                                                    placeholder="0"
                                                >

                                            </td>


                                            <!-- DELETE -->

                                            <td
                                                class="text-center"
                                            >

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Remove Stop"
                                                    @click="
                                                        removeStop(
                                                            index
                                                        )
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


                            <!-- REMARKS -->

                            <div class="mt-3">

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
                                        ? 'Update Route'
                                        : 'Save Route'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- ======================================================
             VIEW MODAL
        ======================================================= -->

        <div
            v-if="showViewModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <div>

                            <h5 class="modal-title">
                                Route Details
                            </h5>

                            <small class="text-muted">
                                Transport route information
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
                                Loading route...
                            </div>

                        </div>


                        <div
                            v-else-if="
                                selectedRoute
                            "
                        >

                            <!-- ROUTE DETAILS -->

                            <div class="row g-3 mb-4">

                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Route Name
                                    </label>

                                    <div class="fw-bold">
                                        {{
                                            selectedRoute.name
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Route Code
                                    </label>

                                    <div>
                                        {{
                                            selectedRoute.code ||
                                            '-'
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
                                                    selectedRoute.status
                                                )
                                            "
                                        >
                                            {{
                                                selectedRoute.status
                                            }}
                                        </span>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Start Location
                                    </label>

                                    <div>
                                        {{
                                            selectedRoute.start_location ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        End Location
                                    </label>

                                    <div>
                                        {{
                                            selectedRoute.end_location ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Distance
                                    </label>

                                    <div>
                                        {{
                                            selectedRoute.distance ||
                                            0
                                        }}
                                        km
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Travel Time
                                    </label>

                                    <div>
                                        {{
                                            selectedRoute.travel_time ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Default Fare
                                    </label>

                                    <div class="fw-bold">
                                        ₹
                                        {{
                                            formatAmount(
                                                selectedRoute.fare
                                            )
                                        }}
                                    </div>

                                </div>

                            </div>


                            <!-- STOPS -->

                            <h6 class="mb-3">
                                Route Stops
                            </h6>


                            <div
                                v-if="
                                    !selectedRoute.stops ||
                                    selectedRoute.stops.length === 0
                                "
                                class="text-muted border rounded p-4 text-center"
                            >

                                No stops configured.

                            </div>


                            <div
                                v-else
                                class="table-responsive"
                            >

                                <table
                                    class="table table-bordered align-middle"
                                >

                                    <thead class="table-light">

                                        <tr>

                                            <th>
                                                #
                                            </th>

                                            <th>
                                                Stop
                                            </th>

                                            <th>
                                                Code
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

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="
                                                stop in selectedRoute.stops
                                            "
                                            :key="
                                                stop.id
                                            "
                                        >

                                            <td>
                                                {{
                                                    stop.stop_order
                                                }}
                                            </td>

                                            <td>
                                                <strong>
                                                    {{
                                                        stop.stop_name
                                                    }}
                                                </strong>
                                            </td>

                                            <td>
                                                {{
                                                    stop.stop_code ||
                                                    '-'
                                                }}
                                            </td>

                                            <td>
                                                {{
                                                    formatTime(
                                                        stop.pickup_time
                                                    )
                                                }}
                                            </td>

                                            <td>
                                                {{
                                                    formatTime(
                                                        stop.drop_time
                                                    )
                                                }}
                                            </td>

                                            <td>
                                                ₹
                                                {{
                                                    formatAmount(
                                                        stop.fare
                                                    )
                                                }}
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

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

const selectedRoute = ref(null);


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

    start_location: '',

    end_location: '',

    distance: 0,

    travel_time: '',

    fare: 0,

    status: 'Active',

    remarks: '',

    stops: []

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
| LOAD ROUTES
|--------------------------------------------------------------------------
*/

const loadRoutes = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/routes',

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


        routes.value =
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
            'Route loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load routes.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| ADD STOP
|--------------------------------------------------------------------------
*/

const addStop = () => {

    const nextOrder =
        form.value.stops.length + 1;


    form.value.stops.push({

        _key:
            `${Date.now()}_${Math.random()}`,

        stop_name: '',

        stop_code: '',

        stop_order:
            nextOrder,

        pickup_time: '',

        drop_time: '',

        fare: 0,

        remarks: ''

    });

};


/*
|--------------------------------------------------------------------------
| REMOVE STOP
|--------------------------------------------------------------------------
*/

const removeStop = (
    index
) => {

    if (
        !confirm(
            'Remove this stop?'
        )
    ) {

        return;

    }


    form.value.stops.splice(
        index,
        1
    );


    /*
    |--------------------------------------------------------------------------
    | Re-number stops
    |--------------------------------------------------------------------------
    */

    form.value.stops.forEach(
        (
            stop,
            index
        ) => {

            stop.stop_order =
                index + 1;

        }
    );

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedRoute.value =
        null;

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
    route
) => {

    editing.value = true;

    selectedRoute.value =
        route;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        name:
            route.name ||
            '',

        code:
            route.code ||
            '',

        start_location:
            route.start_location ||
            '',

        end_location:
            route.end_location ||
            '',

        distance:
            Number(
                route.distance ||
                0
            ),

        travel_time:
            route.travel_time ||
            '',

        fare:
            Number(
                route.fare ||
                0
            ),

        status:
            route.status ||
            'Active',

        remarks:
            route.remarks ||
            '',

        stops:
            (
                route.stops ||
                []
            ).map(
                (
                    stop,
                    index
                ) => ({

                    _key:
                        `${stop.id || Date.now()}_${index}`,

                    id:
                        stop.id ||
                        null,

                    stop_name:
                        stop.stop_name ||
                        '',

                    stop_code:
                        stop.stop_code ||
                        '',

                    stop_order:
                        Number(
                            stop.stop_order ||
                            index + 1
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

                })
            )

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

    showModal.value =
        false;

};


/*
|--------------------------------------------------------------------------
| SAVE ROUTE
|--------------------------------------------------------------------------
*/

const saveRoute = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';

    saving.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | Sort stops by order
        |--------------------------------------------------------------------------
        */

        const sortedStops =
            [...form.value.stops]
                .sort(
                    (
                        a,
                        b
                    ) =>
                        Number(
                            a.stop_order
                        ) -
                        Number(
                            b.stop_order
                        )
                );


        /*
        |--------------------------------------------------------------------------
        | Re-number
        |--------------------------------------------------------------------------
        */

        sortedStops.forEach(
            (
                stop,
                index
            ) => {

                stop.stop_order =
                    index + 1;

            }
        );


        const payload = {

            name:
                form.value.name,

            code:
                form.value.code ||
                null,

            start_location:
                form.value.start_location ||
                null,

            end_location:
                form.value.end_location ||
                null,

            distance:
                Number(
                    form.value.distance ||
                    0
                ),

            travel_time:
                form.value.travel_time ||
                null,

            fare:
                Number(
                    form.value.fare ||
                    0
                ),

            status:
                form.value.status,

            remarks:
                form.value.remarks ||
                null,

            stops:
                sortedStops.map(
                    stop => ({

                        stop_name:
                            stop.stop_name,

                        stop_code:
                            stop.stop_code ||
                            null,

                        stop_order:
                            Number(
                                stop.stop_order
                            ),

                        pickup_time:
                            stop.pickup_time ||
                            null,

                        drop_time:
                            stop.drop_time ||
                            null,

                        fare:
                            Number(
                                stop.fare ||
                                0
                            ),

                        remarks:
                            stop.remarks ||
                            null

                    })
                )

        };


        let response;


        if (
            editing.value &&
            selectedRoute.value
        ) {

            response =
                await axios.put(

                    `/api/transport/routes/${selectedRoute.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/routes',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Route updated successfully.'
                    : 'Route created successfully.'
            );


        showModal.value =
            false;


        await loadRoutes(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Route save error:',
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
                'Unable to save route.';

        }

    } finally {

        saving.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW ROUTE
|--------------------------------------------------------------------------
*/

const viewRoute = async (
    id
) => {

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedRoute.value =
        null;


    try {

        const response =
            await axios.get(

                `/api/transport/routes/${id}`,

                getAuthConfig()

            );


        selectedRoute.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Route view error:',
            err
        );


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load route details.';

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

const deleteRoute = async (
    route
) => {

    if (
        !confirm(
            `Are you sure you want to delete "${route.name}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/routes/${route.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Route deleted successfully.';


        await loadRoutes(
            pagination.value.current_page
        );


        if (
            routes.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadRoutes(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Route delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete route.';

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
| TIME INPUT
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

        status: '',

        per_page: 20

    };


    loadRoutes(1);

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


    loadRoutes(page);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadRoutes(1);

    }
);

</script>