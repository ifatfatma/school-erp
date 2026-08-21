<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Library Fines</h4>
                <small class="text-muted">
                    Manage overdue, lost, damaged and other library fines
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreateModal"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Fine
            </button>
        </div>


        <!-- Alerts -->
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


        <!-- Statistics -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Fines
                        </small>

                        <h3 class="mb-0">
                            {{ statistics.total }}
                        </h3>

                        <small class="text-muted">
                            ₹{{ money(statistics.total_fine) }}
                        </small>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Pending
                        </small>

                        <h3 class="mb-0 text-danger">
                            {{ statistics.pending }}
                        </h3>

                        <small class="text-danger">
                            ₹{{ money(statistics.pending_amount) }}
                        </small>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Partially Paid
                        </small>

                        <h3 class="mb-0 text-warning">
                            {{ statistics.partially_paid }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Collected
                        </small>

                        <h3 class="mb-0 text-success">
                            ₹{{ money(statistics.collected_amount) }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>


        <!-- Filters -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Fine no, member, book, issue..."
                            @keyup.enter="loadFines(1)"
                        >

                    </div>


                    <!-- Fine Type -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Fine Type
                        </label>

                        <select
                            v-model="filters.fine_type"
                            class="form-select"
                        >
                            <option value="">
                                All
                            </option>

                            <option value="Overdue">
                                Overdue
                            </option>

                            <option value="Lost">
                                Lost
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>

                            <option value="Other">
                                Other
                            </option>
                        </select>

                    </div>


                    <!-- Status -->
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

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="Partially Paid">
                                Partially Paid
                            </option>

                            <option value="Paid">
                                Paid
                            </option>

                            <option value="Waived">
                                Waived
                            </option>
                        </select>

                    </div>


                    <!-- From -->
                    <div class="col-md-2">

                        <label class="form-label">
                            From
                        </label>

                        <input
                            v-model="filters.from_date"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- To -->
                    <div class="col-md-2">

                        <label class="form-label">
                            To
                        </label>

                        <input
                            v-model="filters.to_date"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-12">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadFines(1)"
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


        <!-- Fine Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Fine Records
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Fine No.
                                </th>

                                <th>
                                    Member
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Fine
                                </th>

                                <th>
                                    Waiver
                                </th>

                                <th>
                                    Payable
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="100">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr v-if="loading">

                                <td
                                    colspan="11"
                                    class="text-center py-5"
                                >

                                    <div class="spinner-border text-primary"></div>

                                    <div class="text-muted mt-2">
                                        Loading fines...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    fines.length === 0
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-cash-stack fs-2 d-block mb-2"
                                    ></i>

                                    No fines found.

                                </td>

                            </tr>


                            <tr
                                v-for="(
                                    fine,
                                    index
                                ) in fines"
                                :key="fine.id"
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
                                        {{ fine.fine_no }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            fine.book_issue
                                                ?.issue_no ||
                                            '-'
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            memberName(
                                                fine.library_member
                                            )
                                        }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            fine.library_member
                                                ?.member_no ||
                                            '-'
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            fine.book?.title ||
                                            '-'
                                        }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            fine.book_copy
                                                ?.accession_no ||
                                            fine.book_copy
                                                ?.barcode ||
                                            '-'
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            fineTypeClass(
                                                fine.fine_type
                                            )
                                        "
                                    >
                                        {{ fine.fine_type }}
                                    </span>

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            fine.fine_date
                                        )
                                    }}
                                </td>


                                <td>
                                    ₹{{ money(fine.fine_amount) }}
                                </td>


                                <td>
                                    ₹{{ money(fine.waiver_amount) }}
                                </td>


                                <td>
                                    <strong>
                                        ₹{{
                                            money(
                                                fine.payable_amount
                                            )
                                        }}
                                    </strong>
                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                fine.status
                                            )
                                        "
                                    >
                                        {{ fine.status }}
                                    </span>

                                </td>


                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="View"
                                        @click="
                                            viewFine(fine)
                                        "
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>


                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        title="Edit"
                                        @click="
                                            openEditModal(fine)
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- Pagination -->
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


        <!-- Add / Edit Modal -->
        <div
            v-if="showFormModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveFine"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                {{
                                    editingId
                                        ? 'Update Library Fine'
                                        : 'Add Library Fine'
                                }}
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="
                                    closeFormModal
                                "
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

                                <!-- Fine Type -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fine Type
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.fine_type"
                                        class="form-select"
                                        :disabled="!!editingId"
                                        @change="
                                            calculateFine
                                        "
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Overdue">
                                            Overdue
                                        </option>

                                        <option value="Lost">
                                            Lost
                                        </option>

                                        <option value="Damaged">
                                            Damaged
                                        </option>

                                        <option value="Other">
                                            Other
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.fine_type"
                                        class="text-danger small"
                                    >
                                        {{ formErrors.fine_type }}
                                    </div>

                                </div>


                                <!-- Fine Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fine Date
                                    </label>

                                    <input
                                        v-model="form.fine_date"
                                        type="date"
                                        class="form-control"
                                        :disabled="!!editingId"
                                    >

                                </div>


                                <!-- Fine No -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fine No.
                                    </label>

                                    <input
                                        v-model="form.fine_no"
                                        type="text"
                                        class="form-control"
                                        placeholder="Auto generated"
                                        disabled
                                    >

                                </div>


                                <!-- Issue -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Book Issue
                                    </label>

                                    <select
                                        v-model="
                                            form.book_issue_id
                                        "
                                        class="form-select"
                                        :disabled="!!editingId"
                                        @change="issueChanged"
                                    >

                                        <option value="">
                                            Select Book Issue
                                        </option>

                                        <option
                                            v-for="issue in issues"
                                            :key="issue.id"
                                            :value="issue.id"
                                        >

                                            {{
                                                issue.issue_no ||
                                                `Issue #${issue.id}`
                                            }}

                                            -
                                            {{
                                                memberName(
                                                    issue.library_member
                                                )
                                            }}

                                            -
                                            {{
                                                issue.book?.title ||
                                                '-'
                                            }}

                                        </option>

                                    </select>

                                </div>


                                <!-- Member -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Library Member
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        v-model="
                                            form.library_member_id
                                        "
                                        class="form-select"
                                        :disabled="
                                            !!form.book_issue_id ||
                                            !!editingId
                                        "
                                    >

                                        <option value="">
                                            Select Member
                                        </option>

                                        <option
                                            v-for="member in members"
                                            :key="member.id"
                                            :value="member.id"
                                        >

                                            {{
                                                member.member_no
                                            }}
                                            -
                                            {{
                                                memberName(
                                                    member
                                                )
                                            }}

                                        </option>

                                    </select>

                                </div>


                                <!-- Book -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Book
                                    </label>

                                    <select
                                        v-model="form.book_id"
                                        class="form-select"
                                        :disabled="
                                            !!form.book_issue_id ||
                                            !!editingId
                                        "
                                    >

                                        <option value="">
                                            Select Book
                                        </option>

                                        <option
                                            v-for="book in books"
                                            :key="book.id"
                                            :value="book.id"
                                        >
                                            {{ book.title }}
                                        </option>

                                    </select>

                                </div>


                                <!-- Overdue Days -->
                                <div
                                    v-if="
                                        form.fine_type ===
                                        'Overdue'
                                    "
                                    class="col-md-4"
                                >

                                    <label class="form-label">
                                        Overdue Days
                                    </label>

                                    <input
                                        v-model.number="
                                            form.overdue_days
                                        "
                                        type="number"
                                        min="0"
                                        class="form-control"
                                        :disabled="!!editingId"
                                        @input="
                                            calculateFine
                                        "
                                    >

                                </div>


                                <!-- Fine Per Day -->
                                <div
                                    v-if="
                                        form.fine_type ===
                                        'Overdue'
                                    "
                                    class="col-md-4"
                                >

                                    <label class="form-label">
                                        Fine / Day
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                form.fine_per_day
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            :disabled="!!editingId"
                                            @input="
                                                calculateFine
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- Fine Amount -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Fine Amount
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                form.fine_amount
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            :disabled="
                                                !!editingId ||
                                                form.fine_type ===
                                                    'Overdue'
                                            "
                                            @input="
                                                calculatePayable
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- Waiver -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Waiver Amount
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                form.waiver_amount
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            @input="
                                                calculatePayable
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- Payable -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Payable Amount
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            :value="
                                                form.payable_amount
                                            "
                                            type="text"
                                            class="form-control fw-bold"
                                            readonly
                                        >

                                    </div>

                                </div>


                                <!-- Description -->
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
                                        placeholder="Fine description..."
                                    ></textarea>

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
                                @click="
                                    closeFormModal
                                "
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
                                    editingId
                                        ? 'Update'
                                        : 'Save Fine'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- View Modal -->
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
                            Fine Details
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
                            v-if="selectedFine"
                            class="row g-3"
                        >

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Fine No.
                                </small>

                                <h5>
                                    {{ selectedFine.fine_no }}
                                </h5>
                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Type
                                </small>

                                <div>
                                    <span
                                        class="badge"
                                        :class="
                                            fineTypeClass(
                                                selectedFine.fine_type
                                            )
                                        "
                                    >
                                        {{
                                            selectedFine.fine_type
                                        }}
                                    </span>
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Status
                                </small>

                                <div>
                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                selectedFine.status
                                            )
                                        "
                                    >
                                        {{
                                            selectedFine.status
                                        }}
                                    </span>
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Member
                                </small>

                                <strong class="d-block">
                                    {{
                                        memberName(
                                            selectedFine.library_member
                                        )
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedFine
                                            .library_member
                                            ?.member_no ||
                                        '-'
                                    }}
                                </small>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Book
                                </small>

                                <strong class="d-block">
                                    {{
                                        selectedFine.book?.title ||
                                        '-'
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedFine.book_copy
                                            ?.accession_no ||
                                        selectedFine.book_copy
                                            ?.barcode ||
                                        '-'
                                    }}
                                </small>

                            </div>


                            <div class="col-md-3">

                                <small class="text-muted">
                                    Fine
                                </small>

                                <h5>
                                    ₹{{
                                        money(
                                            selectedFine.fine_amount
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-3">

                                <small class="text-muted">
                                    Waiver
                                </small>

                                <h5>
                                    ₹{{
                                        money(
                                            selectedFine.waiver_amount
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-3">

                                <small class="text-muted">
                                    Payable
                                </small>

                                <h5>
                                    ₹{{
                                        money(
                                            selectedFine.payable_amount
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-3">

                                <small class="text-muted">
                                    Collected
                                </small>

                                <h5 class="text-success">
                                    ₹{{
                                        money(
                                            selectedFine.collected_amount
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Remaining
                                </small>

                                <h4 class="text-danger">
                                    ₹{{
                                        money(
                                            remainingAmount(
                                                selectedFine
                                            )
                                        )
                                    }}
                                </h4>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Description
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedFine.description ||
                                        '-'
                                    }}
                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Remarks
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedFine.remarks ||
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
| State
|--------------------------------------------------------------------------
*/

const fines = ref([]);

const issues = ref([]);

const members = ref([]);

const books = ref([]);

const loading = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showFormModal = ref(false);

const showViewModal = ref(false);

const selectedFine = ref(null);

const editingId = ref(null);


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    pending: 0,

    partially_paid: 0,

    paid: 0,

    waived: 0,

    total_fine: 0,

    total_waiver: 0,

    total_payable: 0,

    pending_amount: 0,

    collected_amount: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    fine_type: '',

    status: '',

    from_date: '',

    to_date: '',

    per_page: 20

});


/*
|--------------------------------------------------------------------------
| Pagination
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
| Empty Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    library_member_id: '',

    book_issue_id: '',

    book_id: '',

    book_copy_id: '',

    lost_damaged_book_id: '',

    fine_no: '',

    fine_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    fine_type: '',

    description: '',

    overdue_days: 0,

    fine_per_day: 5,

    fine_amount: 0,

    waiver_amount: 0,

    payable_amount: 0,

    status: 'Pending',

    remarks: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Auth Config
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

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
| Load Fines
|--------------------------------------------------------------------------
*/

const loadFines = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const params = {

            page,

            per_page:
                filters.value.per_page,

            search:
                filters.value.search ||
                undefined,

            fine_type:
                filters.value.fine_type ||
                undefined,

            status:
                filters.value.status ||
                undefined,

            from_date:
                filters.value.from_date ||
                undefined,

            to_date:
                filters.value.to_date ||
                undefined

        };


        const response =
            await axios.get(
                '/api/library-fines',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        fines.value =
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
                    20
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
            'Library fines error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load library fines.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Statistics
|--------------------------------------------------------------------------
*/

const loadStatistics = async () => {

    try {

        const response =
            await axios.get(
                '/api/library-fines/statistics',
                getAuthConfig()
            );


        const data =
            response.data?.data ||
            {};


        statistics.value = {

            total:
                Number(data.total || 0),

            pending:
                Number(data.pending || 0),

            partially_paid:
                Number(
                    data.partially_paid ||
                    0
                ),

            paid:
                Number(data.paid || 0),

            waived:
                Number(data.waived || 0),

            total_fine:
                Number(
                    data.total_fine ||
                    0
                ),

            total_waiver:
                Number(
                    data.total_waiver ||
                    0
                ),

            total_payable:
                Number(
                    data.total_payable ||
                    0
                ),

            pending_amount:
                Number(
                    data.pending_amount ||
                    0
                ),

            collected_amount:
                Number(
                    data.collected_amount ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Fine statistics error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Options
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    /*
    |--------------------------------------------------------------------------
    | Book Issues
    |--------------------------------------------------------------------------
    */

    try {

        const response =
            await axios.get(
                '/api/book-issues',
                {
                    ...getAuthConfig(),

                    params: {
                        status: 'Issued',
                        per_page: 100
                    }

                }
            );


        const result =
            response.data?.data;


        issues.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.warn(
            'Unable to load book issues.',
            err
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Members
    |--------------------------------------------------------------------------
    */

    try {

        const response =
            await axios.get(
                '/api/library-members',
                {
                    ...getAuthConfig(),

                    params: {
                        per_page: 100
                    }

                }
            );


        const result =
            response.data?.data;


        members.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.warn(
            'Unable to load library members.',
            err
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Books
    |--------------------------------------------------------------------------
    */

    try {

        const response =
            await axios.get(
                '/api/books',
                {
                    ...getAuthConfig(),

                    params: {
                        per_page: 100
                    }

                }
            );


        const result =
            response.data?.data;


        books.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.warn(
            'Unable to load books.',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreateModal = async () => {

    editingId.value = null;

    form.value =
        emptyForm();

    formErrors.value = {};

    formError.value = '';

    showFormModal.value = true;


    if (
        !issues.value.length &&
        !members.value.length
    ) {

        await loadOptions();

    }

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEditModal = (
    fine
) => {

    editingId.value =
        fine.id;


    form.value = {

        library_member_id:
            fine.library_member_id ||
            '',

        book_issue_id:
            fine.book_issue_id ||
            '',

        book_id:
            fine.book_id ||
            '',

        book_copy_id:
            fine.book_copy_id ||
            '',

        lost_damaged_book_id:
            fine.lost_damaged_book_id ||
            '',

        fine_no:
            fine.fine_no ||
            '',

        fine_date:
            dateValue(
                fine.fine_date
            ),

        fine_type:
            fine.fine_type ||
            '',

        description:
            fine.description ||
            '',

        overdue_days:
            Number(
                fine.overdue_days ||
                0
            ),

        fine_per_day:
            Number(
                fine.fine_per_day ||
                0
            ),

        fine_amount:
            Number(
                fine.fine_amount ||
                0
            ),

        waiver_amount:
            Number(
                fine.waiver_amount ||
                0
            ),

        payable_amount:
            Number(
                fine.payable_amount ||
                0
            ),

        status:
            fine.status ||
            'Pending',

        remarks:
            fine.remarks ||
            ''

    };


    formErrors.value = {};

    formError.value = '';

    showFormModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Close
|--------------------------------------------------------------------------
*/

const closeFormModal = () => {

    if (saving.value) {
        return;
    }


    showFormModal.value =
        false;

};


/*
|--------------------------------------------------------------------------
| Issue Changed
|--------------------------------------------------------------------------
*/

const issueChanged = () => {

    const issue =
        issues.value.find(
            item =>
                String(item.id) ===
                String(
                    form.value.book_issue_id
                )
        );


    if (!issue) {
        return;
    }


    form.value.library_member_id =
        issue.library_member_id ||
        '';


    form.value.book_id =
        issue.book_id ||
        '';


    form.value.book_copy_id =
        issue.book_copy_id ||
        '';


    /*
    |--------------------------------------------------------------------------
    | Calculate overdue days
    |--------------------------------------------------------------------------
    */

    if (
        form.value.fine_type ===
        'Overdue'
    ) {

        calculateFine();

    }

};


/*
|--------------------------------------------------------------------------
| Calculate Fine
|--------------------------------------------------------------------------
*/

const calculateFine = () => {

    if (
        form.value.fine_type !==
        'Overdue'
    ) {

        calculatePayable();

        return;

    }


    const issue =
        issues.value.find(
            item =>
                String(item.id) ===
                String(
                    form.value.book_issue_id
                )
        );


    if (!issue) {

        calculatePayable();

        return;

    }


    if (!issue.due_date) {

        calculatePayable();

        return;

    }


    const dueDate =
        new Date(
            issue.due_date
        );


    const fineDate =
        form.value.fine_date
            ? new Date(
                form.value.fine_date
            )
            : new Date();


    dueDate.setHours(
        0,
        0,
        0,
        0
    );

    fineDate.setHours(
        0,
        0,
        0,
        0
    );


    const difference =
        fineDate.getTime() -
        dueDate.getTime();


    const days =
        Math.max(
            0,
            Math.floor(
                difference /
                (
                    1000 *
                    60 *
                    60 *
                    24
                )
            )
        );


    form.value.overdue_days =
        days;


    form.value.fine_amount =
        Math.round(
            (
                days *
                Number(
                    form.value.fine_per_day
                )
            +
                Number.EPSILON
            ) * 100
        ) / 100;


    calculatePayable();

};


/*
|--------------------------------------------------------------------------
| Calculate Payable
|--------------------------------------------------------------------------
*/

const calculatePayable = () => {

    const fine =
        Number(
            form.value.fine_amount
        ) || 0;


    let waiver =
        Number(
            form.value.waiver_amount
        ) || 0;


    waiver =
        Math.max(
            0,
            Math.min(
                waiver,
                fine
            )
        );


    form.value.waiver_amount =
        waiver;


    form.value.payable_amount =
        Math.max(
            0,
            Math.round(
                (
                    fine -
                    waiver +
                    Number.EPSILON
                ) * 100
            ) / 100
        );

};


/*
|--------------------------------------------------------------------------
| Save Fine
|--------------------------------------------------------------------------
*/

const saveFine = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        let response;


        if (editingId.value) {

            response =
                await axios.put(
                    `/api/library-fines/${editingId.value}`,
                    {

                        description:
                            form.value.description ||
                            null,

                        fine_amount:
                            Number(
                                form.value.fine_amount
                            ) || 0,

                        fine_per_day:
                            Number(
                                form.value.fine_per_day
                            ) || 0,

                        overdue_days:
                            Number(
                                form.value.overdue_days
                            ) || 0,

                        waiver_amount:
                            Number(
                                form.value.waiver_amount
                            ) || 0,

                        status:
                            form.value.status,

                        remarks:
                            form.value.remarks ||
                            null

                    },
                    getAuthConfig()
                );

        } else {

            /*
            |--------------------------------------------------------------------------
            | Create
            |--------------------------------------------------------------------------
            */

            response =
                await axios.post(
                    '/api/library-fines',
                    {

                        library_member_id:
                            form.value.library_member_id,

                        book_issue_id:
                            form.value.book_issue_id ||
                            null,

                        book_id:
                            form.value.book_id ||
                            null,

                        book_copy_id:
                            form.value.book_copy_id ||
                            null,

                        lost_damaged_book_id:
                            form.value
                                .lost_damaged_book_id ||
                            null,

                        fine_no:
                            form.value.fine_no ||
                            null,

                        fine_date:
                            form.value.fine_date,

                        fine_type:
                            form.value.fine_type,

                        description:
                            form.value.description ||
                            null,

                        overdue_days:
                            Number(
                                form.value.overdue_days
                            ) || 0,

                        fine_per_day:
                            Number(
                                form.value.fine_per_day
                            ) || 0,

                        fine_amount:
                            Number(
                                form.value.fine_amount
                            ) || 0,

                        waiver_amount:
                            Number(
                                form.value.waiver_amount
                            ) || 0,

                        remarks:
                            form.value.remarks ||
                            null

                    },
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            'Fine saved successfully.';


        showFormModal.value =
            false;


        await Promise.all([

            loadFines(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Save fine error:',
            err
        );


        if (
            err.response?.status === 422
        ) {

            formErrors.value =
                Object.fromEntries(

                    Object.entries(
                        err.response.data?.errors ||
                        {}
                    ).map(
                        ([key, value]) => [

                            key,

                            Array.isArray(value)
                                ? value[0]
                                : value

                        ]
                    )

                );


            formError.value =
                err.response.data?.message ||
                'Please correct the validation errors.';

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save fine.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const viewFine = (
    fine
) => {

    selectedFine.value =
        fine;

    showViewModal.value =
        true;

};


/*
|--------------------------------------------------------------------------
| Pagination
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


    loadFines(page);

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        fine_type: '',

        status: '',

        from_date: '',

        to_date: '',

        per_page: 20

    };


    loadFines(1);

};


/*
|--------------------------------------------------------------------------
| Member Name
|--------------------------------------------------------------------------
*/

const memberName = (
    member
) => {

    if (!member) {
        return '-';
    }


    return (
        member.student?.name ||
        member.student?.student_name ||
        member.staff?.name ||
        member.staff?.staff_name ||
        (
            member.student?.first_name
                ? `${member.student.first_name} ${
                    member.student.last_name || ''
                }`.trim()
                : ''
        ) ||
        (
            member.staff?.first_name
                ? `${member.staff.first_name} ${
                    member.staff.last_name || ''
                }`.trim()
                : ''
        ) ||
        member.member_no ||
        '-'
    );

};


/*
|--------------------------------------------------------------------------
| Date
|--------------------------------------------------------------------------
*/

const dateValue = (
    date
) => {

    if (!date) {
        return '';
    }


    return String(
        date
    ).substring(
        0,
        10
    );

};


const formatDate = (
    date
) => {

    if (!date) {
        return '-';
    }


    return String(
        date
    ).substring(
        0,
        10
    );

};


/*
|--------------------------------------------------------------------------
| Money
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Remaining Amount
|--------------------------------------------------------------------------
*/

const remainingAmount = (
    fine
) => {

    const payable =
        Number(
            fine?.payable_amount ||
            0
        );


    const collected =
        Number(
            fine?.collected_amount ||
            0
        );


    return Math.max(
        0,
        payable -
        collected
    );

};


/*
|--------------------------------------------------------------------------
| Fine Type Badge
|--------------------------------------------------------------------------
*/

const fineTypeClass = (
    type
) => {

    switch (type) {

        case 'Overdue':
            return 'bg-warning text-dark';

        case 'Lost':
            return 'bg-danger';

        case 'Damaged':
            return 'bg-warning text-dark';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    switch (status) {

        case 'Paid':
            return 'bg-success';

        case 'Waived':
            return 'bg-secondary';

        case 'Partially Paid':
            return 'bg-warning text-dark';

        case 'Pending':
        default:
            return 'bg-danger';

    }

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([

            loadFines(1),

            loadStatistics()

        ]);

    }
);

</script>