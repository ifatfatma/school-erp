<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">Transport Fees</h4>
                <small class="text-muted">
                    Manage student transport fee configurations
                </small>
            </div>

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-success"
                    @click="showGenerateModal = true"
                >
                    <i class="bi bi-calendar-check me-1"></i>
                    Generate Monthly Fees
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    @click="openCreate"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Add Transport Fee
                </button>

            </div>

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
                <strong>Search Transport Fees</strong>
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
                            placeholder="Student / Admission No / Route / Stop"
                            @keyup.enter="loadFees(1)"
                        >

                    </div>


                    <!-- STUDENT -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Student
                        </label>

                        <select
                            v-model="filters.student_id"
                            class="form-select"
                            @change="loadFees(1)"
                        >

                            <option value="">
                                All Students
                            </option>

                            <option
                                v-for="student in students"
                                :key="student.id"
                                :value="student.id"
                            >

                                {{ studentName(student) }}

                                —
                                {{ student.admission_no }}

                            </option>

                        </select>

                    </div>


                    <!-- ROUTE -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Route
                        </label>

                        <select
                            v-model="filters.route_id"
                            class="form-select"
                            @change="loadFees(1)"
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


                    <!-- STATUS -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                            @change="loadFees(1)"
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


                    <!-- BUTTONS -->
                    <div class="col-md-12">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadFees(1)"
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
                    Student Transport Fees
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
                                    Route
                                </th>

                                <th>
                                    Stop
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Discount
                                </th>

                                <th>
                                    Cycle
                                </th>

                                <th>
                                    Start Date
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
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading transport fees...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->
                            <tr v-else-if="fees.length === 0">

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-cash-stack fs-2 d-block mb-2"
                                    ></i>

                                    No transport fee configurations found.

                                </td>

                            </tr>


                            <!-- DATA -->
                            <tr
                                v-for="(
                                    fee,
                                    index
                                ) in fees"
                                :key="fee.id"
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


                                <!-- STUDENT -->
                                <td>

                                    <strong>
                                        {{
                                            studentName(
                                                fee.student_data
                                            )
                                        }}
                                    </strong>

                                    <div class="small text-muted">

                                        {{
                                            fee.student_data
                                                ?.admission_no ||
                                            '-'
                                        }}

                                    </div>

                                </td>


                                <!-- ROUTE -->
                                <td>

                                    {{
                                        fee.route?.name ||
                                        '-'
                                    }}

                                    <div
                                        v-if="fee.route?.code"
                                        class="small text-muted"
                                    >
                                        {{ fee.route.code }}
                                    </div>

                                </td>


                                <!-- STOP -->
                                <td>

                                    {{
                                        fee.stop?.stop_name ||
                                        fee.assignment?.stop?.stop_name ||
                                        '-'
                                    }}

                                </td>


                                <!-- AMOUNT -->
                                <td>

                                    <strong>
                                        ₹
                                        {{
                                            formatAmount(
                                                fee.amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <!-- DISCOUNT -->
                                <td>

                                    ₹
                                    {{
                                        formatAmount(
                                            fee.discount
                                        )
                                    }}

                                </td>


                                <!-- BILLING -->
                                <td>

                                    <span
                                        class="badge bg-light text-dark border"
                                    >
                                        {{
                                            fee.billing_cycle
                                        }}
                                    </span>

                                </td>


                                <!-- START -->
                                <td>

                                    {{
                                        formatDate(
                                            fee.start_date
                                        )
                                    }}

                                </td>


                                <!-- STATUS -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            fee.status === 'Active'
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            fee.status
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
                                                viewFee(
                                                    fee.id
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
                                                    fee
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
                                                deleteFee(
                                                    fee
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

                    <form @submit.prevent="saveFee">

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">

                                    {{
                                        editing
                                            ? 'Edit Transport Fee'
                                            : 'Add Transport Fee'
                                    }}

                                </h5>

                                <small class="text-muted">
                                    Configure transport charges for a student
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
                                <div class="col-md-12">

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
                                        @change="
                                            onStudentChange
                                        "
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

                                            {{ studentName(student) }}

                                            —
                                            {{ student.admission_no }}

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


                                <!-- ASSIGNMENT -->
                                <div class="col-md-12">

                                    <label class="form-label">

                                        Transport Assignment
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.transport_assignment_id
                                        "
                                        class="form-select"
                                        required
                                        @change="
                                            onAssignmentChange
                                        "
                                    >

                                        <option value="">

                                            {{
                                                loadingAssignments
                                                    ? 'Loading assignments...'
                                                    : 'Select Assignment'
                                            }}

                                        </option>


                                        <option
                                            v-for="
                                                assignment in studentAssignments
                                            "
                                            :key="assignment.id"
                                            :value="assignment.id"
                                        >

                                            {{
                                                assignmentText(
                                                    assignment
                                                )
                                            }}

                                        </option>

                                    </select>


                                    <small
                                        v-if="
                                            formErrors
                                                .transport_assignment_id
                                        "
                                        class="text-danger"
                                    >
                                        {{
                                            formErrors
                                                .transport_assignment_id
                                        }}
                                    </small>

                                </div>


                                <!-- ROUTE DISPLAY -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Route
                                    </label>


                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="
                                            selectedAssignment
                                                ?.route?.name || ''
                                        "
                                        readonly
                                    >

                                </div>


                                <!-- STOP DISPLAY -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Stop
                                    </label>


                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="
                                            selectedAssignment
                                                ?.stop?.stop_name || ''
                                        "
                                        readonly
                                    >

                                </div>


                                <!-- AMOUNT -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Monthly Amount
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

                                </div>


                                <!-- DISCOUNT -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Discount
                                    </label>


                                    <input
                                        v-model.number="
                                            form.discount
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                    >

                                </div>


                                <!-- CYCLE -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Billing Cycle
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.billing_cycle
                                        "
                                        class="form-select"
                                        required
                                    >

                                        <option value="Monthly">
                                            Monthly
                                        </option>

                                        <option value="Quarterly">
                                            Quarterly
                                        </option>

                                        <option value="Half Yearly">
                                            Half Yearly
                                        </option>

                                        <option value="Yearly">
                                            Yearly
                                        </option>

                                        <option value="One Time">
                                            One Time
                                        </option>

                                    </select>

                                </div>


                                <!-- START DATE -->
                                <div class="col-md-4">

                                    <label class="form-label">

                                        Start Date
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>


                                    <input
                                        v-model="
                                            form.start_date
                                        "
                                        type="date"
                                        class="form-control"
                                        required
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
                                        ? 'Update'
                                        : 'Save'
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

                        <h5 class="modal-title">
                            Transport Fee Details
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
                            v-else-if="selectedFee"
                            class="row g-4"
                        >

                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Student
                                </div>

                                <strong>
                                    {{
                                        studentName(
                                            selectedFee.student_data
                                        )
                                    }}
                                </strong>

                                <div class="small text-muted">

                                    {{
                                        selectedFee.student_data
                                            ?.admission_no ||
                                        '-'
                                    }}

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Route
                                </div>

                                <strong>
                                    {{
                                        selectedFee.route?.name ||
                                        '-'
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Stop
                                </div>

                                <strong>
                                    {{
                                        selectedFee.stop?.stop_name ||
                                        '-'
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <div class="text-muted small">
                                    Billing Cycle
                                </div>

                                {{
                                    selectedFee.billing_cycle
                                }}

                            </div>


                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Amount
                                </div>

                                <strong>
                                    ₹
                                    {{
                                        formatAmount(
                                            selectedFee.amount
                                        )
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Discount
                                </div>

                                ₹
                                {{
                                    formatAmount(
                                        selectedFee.discount
                                    )
                                }}

                            </div>


                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Status
                                </div>

                                <span
                                    class="badge"
                                    :class="
                                        selectedFee.status === 'Active'
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        selectedFee.status
                                    }}
                                </span>

                            </div>


                            <div class="col-md-4">

                                <div class="text-muted small">
                                    Start Date
                                </div>

                                {{
                                    formatDate(
                                        selectedFee.start_date
                                    )
                                }}

                            </div>


                            <div class="col-md-4">

                                <div class="text-muted small">
                                    End Date
                                </div>

                                {{
                                    formatDate(
                                        selectedFee.end_date
                                    )
                                }}

                            </div>


                            <div class="col-md-12">

                                <div class="text-muted small">
                                    Remarks
                                </div>

                                {{
                                    selectedFee.remarks ||
                                    '-'
                                }}

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


        <!-- =====================================================
             GENERATE MONTHLY FEES MODAL
        ====================================================== -->

        <div
            v-if="showGenerateModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-md modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="generateFees"
                    >

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title">
                                    Generate Transport Fees
                                </h5>

                                <small class="text-muted">
                                    Generate student transport dues
                                </small>

                            </div>


                            <button
                                type="button"
                                class="btn-close"
                                :disabled="generating"
                                @click="
                                    showGenerateModal = false
                                "
                            ></button>

                        </div>


                        <div class="modal-body">

                            <div
                                v-if="generateError"
                                class="alert alert-danger"
                            >
                                {{ generateError }}
                            </div>


                            <!-- MONTH -->
                            <div class="mb-3">

                                <label class="form-label">

                                    Month
                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input
                                    v-model="
                                        generateForm.month
                                    "
                                    type="month"
                                    class="form-control"
                                    required
                                >

                            </div>


                            <!-- DUE DAY -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Due Day
                                </label>


                                <input
                                    v-model.number="
                                        generateForm.due_day
                                    "
                                    type="number"
                                    min="1"
                                    max="28"
                                    class="form-control"
                                    placeholder="e.g. 10"
                                >


                                <small class="text-muted">
                                    Leave empty to use the last day of the month.
                                </small>

                            </div>


                            <div class="alert alert-info mb-0">

                                <i
                                    class="bi bi-info-circle me-1"
                                ></i>

                                Existing fees for the selected month
                                will not be duplicated.

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="generating"
                                @click="
                                    showGenerateModal = false
                                "
                            >
                                Cancel
                            </button>


                            <button
                                type="submit"
                                class="btn btn-success"
                                :disabled="generating"
                            >

                                <span
                                    v-if="generating"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                <i
                                    v-else
                                    class="bi bi-calendar-check me-1"
                                ></i>

                                Generate Fees

                            </button>

                        </div>

                    </form>

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

const fees = ref([]);

const students = ref([]);

const routes = ref([]);

const assignments = ref([]);

const studentAssignments = ref([]);

const selectedAssignment = ref(null);

const selectedFee = ref(null);

const loading = ref(false);

const saving = ref(false);

const generating = ref(false);

const loadingAssignments = ref(false);

const viewLoading = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const generateError = ref('');

const formErrors = ref({});

const showModal = ref(false);

const showViewModal = ref(false);

const showGenerateModal = ref(false);

const editing = ref(false);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    student_id: '',

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
| FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    student_id: '',

    transport_assignment_id: '',

    amount: 0,

    discount: 0,

    start_date: '',

    end_date: '',

    billing_cycle: 'Monthly',

    status: 'Active',

    remarks: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| GENERATE FORM
|--------------------------------------------------------------------------
*/

const generateForm = ref({

    month:
        new Date()
            .toISOString()
            .substring(0, 7),

    due_day: ''

});


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

                '/api/transport/fees/options',

                getAuthConfig()

            );


        const data =
            response.data?.data ||
            {};


        students.value =
            Array.isArray(
                data.students
            )
                ? data.students
                : [];


        routes.value =
            Array.isArray(
                data.routes
            )
                ? data.routes
                : [];


        assignments.value =
            Array.isArray(
                data.assignments
            )
                ? data.assignments
                : [];


    } catch (err) {

        console.error(
            'Transport fee options error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load transport fee options.';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD FEES
|--------------------------------------------------------------------------
*/

const loadFees = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/transport/fees',

                {

                    ...getAuthConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        student_id:
                            filters.value.student_id ||
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


        fees.value =
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
            'Transport fee loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load transport fees.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| STUDENT CHANGE
|--------------------------------------------------------------------------
*/

const onStudentChange = async () => {

    form.value.transport_assignment_id = '';

    selectedAssignment.value = null;

    studentAssignments.value = [];


    if (!form.value.student_id) {

        return;

    }


    await loadStudentAssignments(
        form.value.student_id
    );

};


/*
|--------------------------------------------------------------------------
| LOAD STUDENT ASSIGNMENTS
|--------------------------------------------------------------------------
*/

const loadStudentAssignments = async (
    studentId
) => {

    loadingAssignments.value = true;


    try {

        const response =
            await axios.get(

                `/api/transport/fees/student/${studentId}/assignments`,

                getAuthConfig()

            );


        studentAssignments.value =
            Array.isArray(
                response.data?.data
            )
                ? response.data.data
                : [];


    } catch (err) {

        console.error(
            'Student assignments error:',
            err
        );


        studentAssignments.value = [];


        formError.value =
            err.response?.data?.message ||
            'Unable to load student assignments.';

    } finally {

        loadingAssignments.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| ASSIGNMENT CHANGE
|--------------------------------------------------------------------------
*/

const onAssignmentChange = () => {

    selectedAssignment.value =
        studentAssignments.value.find(
            assignment =>
                Number(assignment.id) ===
                Number(
                    form.value.transport_assignment_id
                )
        ) || null;


    if (selectedAssignment.value) {

        /*
        |--------------------------------------------------------------------------
        | Use stop fare as default when available
        |--------------------------------------------------------------------------
        */

        const stop =
            selectedAssignment.value.stop;


        if (
            stop &&
            Number(form.value.amount) === 0 &&
            Number(stop.fare) > 0
        ) {

            form.value.amount =
                Number(stop.fare);

        }

    }

};


/*
|--------------------------------------------------------------------------
| ASSIGNMENT TEXT
|--------------------------------------------------------------------------
*/

const assignmentText = (
    assignment
) => {

    const route =
        assignment.route?.name ||
        'Route';


    const stop =
        assignment.stop?.stop_name ||
        'All Stops';


    const vehicle =
        assignment.vehicle?.vehicle_no ||
        'Vehicle';


    const driver =
        assignment.driver?.name ||
        'Driver';


    return `${route} / ${stop} / ${vehicle} / ${driver}`;

};


/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedFee.value = null;

    selectedAssignment.value = null;

    studentAssignments.value = [];

    form.value =
        emptyForm();

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
    fee
) => {

    editing.value = true;

    selectedFee.value = fee;

    formErrors.value = {};

    formError.value = '';


    form.value = {

        student_id:
            fee.student_id ||
            '',

        transport_assignment_id:
            fee.transport_assignment_id ||
            '',

        amount:
            Number(
                fee.amount ||
                0
            ),

        discount:
            Number(
                fee.discount ||
                0
            ),

        start_date:
            formatDateInput(
                fee.start_date
            ),

        end_date:
            formatDateInput(
                fee.end_date
            ),

        billing_cycle:
            fee.billing_cycle ||
            'Monthly',

        status:
            fee.status ||
            'Active',

        remarks:
            fee.remarks ||
            ''

    };


    showModal.value = true;


    await loadStudentAssignments(
        form.value.student_id
    );


    selectedAssignment.value =
        studentAssignments.value.find(
            assignment =>
                Number(assignment.id) ===
                Number(
                    form.value.transport_assignment_id
                )
        ) || null;

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

const saveFee = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';

    saving.value = true;


    try {

        const payload = {

            student_id:
                Number(
                    form.value.student_id
                ),

            transport_assignment_id:
                Number(
                    form.value.transport_assignment_id
                ),

            amount:
                Number(
                    form.value.amount ||
                    0
                ),

            discount:
                Number(
                    form.value.discount ||
                    0
                ),

            start_date:
                form.value.start_date,

            end_date:
                form.value.end_date ||
                null,

            billing_cycle:
                form.value.billing_cycle,

            status:
                form.value.status,

            remarks:
                form.value.remarks ||
                null

        };


        let response;


        if (
            editing.value &&
            selectedFee.value
        ) {

            response =
                await axios.put(

                    `/api/transport/fees/${selectedFee.value.id}`,

                    payload,

                    getAuthConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/transport/fees',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? 'Transport fee updated successfully.'
                    : 'Transport fee created successfully.'
            );


        showModal.value = false;


        await loadFees(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (err) {

        console.error(
            'Transport fee save error:',
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
                'Unable to save transport fee.';

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

const viewFee = async (
    id
) => {

    showViewModal.value = true;

    viewLoading.value = true;

    selectedFee.value = null;


    try {

        const response =
            await axios.get(

                `/api/transport/fees/${id}`,

                getAuthConfig()

            );


        selectedFee.value =
            response.data?.data ||
            null;

    } catch (err) {

        console.error(
            'Transport fee view error:',
            err
        );


        showViewModal.value = false;


        error.value =
            err.response?.data?.message ||
            'Unable to load transport fee.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteFee = async (
    fee
) => {

    if (
        !confirm(
            'Are you sure you want to delete this transport fee configuration?'
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/transport/fees/${fee.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Transport fee deleted successfully.';


        await loadFees(
            pagination.value.current_page
        );


        if (
            fees.value.length === 0 &&
            pagination.value.current_page > 1
        ) {

            await loadFees(
                pagination.value.current_page - 1
            );

        }


    } catch (err) {

        console.error(
            'Transport fee delete error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete transport fee.';

    }

};


/*
|--------------------------------------------------------------------------
| GENERATE
|--------------------------------------------------------------------------
*/

const generateFees = async () => {

    generateError.value = '';

    error.value = '';

    success.value = '';

    generating.value = true;


    try {

        const payload = {

            month:
                generateForm.value.month,

            due_day:
                generateForm.value.due_day
                    ? Number(
                        generateForm.value.due_day
                    )
                    : null

        };


        const response =
            await axios.post(

                '/api/transport/fees/generate',

                payload,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Transport fees generated successfully.';


        showGenerateModal.value = false;


        await loadFees(1);


    } catch (err) {

        console.error(
            'Generate transport fees error:',
            err
        );


        generateError.value =
            err.response?.data?.message ||
            'Unable to generate transport fees.';

    } finally {

        generating.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| STUDENT NAME
|--------------------------------------------------------------------------
*/

const studentName = (
    student
) => {

    if (!student) {

        return '-';

    }


    return [
        student.first_name,
        student.last_name
    ]
        .filter(Boolean)
        .join(' ');

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
| RESET
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        student_id: '',

        route_id: '',

        status: '',

        per_page: 20

    };


    loadFees(1);

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


    loadFees(page);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadOptions();

        await loadFees(1);

    }
);



const onTenantChanged = () => { loadFees(1); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>