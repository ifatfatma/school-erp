<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Lost / Damaged Books</h4>
                <small class="text-muted">
                    Manage lost and damaged library books
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreateModal"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Report Lost / Damaged
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
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Reports
                        </small>

                        <h3 class="mb-0">
                            {{ statistics.total }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Lost
                        </small>

                        <h3 class="mb-0 text-danger">
                            {{ statistics.lost }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Damaged
                        </small>

                        <h3 class="mb-0 text-warning">
                            {{ statistics.damaged }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Pending Amount
                        </small>

                        <h3 class="mb-0 text-primary">
                            ₹{{ money(statistics.pending_amount) }}
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
                            placeholder="Report no, member, book, barcode..."
                            @keyup.enter="loadRecords(1)"
                        >

                    </div>


                    <!-- Type -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Type
                        </label>

                        <select
                            v-model="filters.type"
                            class="form-select"
                        >
                            <option value="">
                                All
                            </option>

                            <option value="Lost">
                                Lost
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>
                        </select>

                    </div>


                    <!-- Charge -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Charge Status
                        </label>

                        <select
                            v-model="
                                filters.charge_status
                            "
                            class="form-select"
                        >
                            <option value="">
                                All
                            </option>

                            <option value="Pending">
                                Pending
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
                            v-model="
                                filters.from_date
                            "
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
                            v-model="
                                filters.to_date
                            "
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- Buttons -->
                    <div class="col-md-12">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadRecords(1)"
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


        <!-- Records -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Lost / Damaged Records
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Report No.
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Member
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Report Date
                                </th>

                                <th>
                                    Book Value
                                </th>

                                <th>
                                    Charge
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

                            <!-- Loading -->
                            <tr v-if="loading">

                                <td
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading records...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    records.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-journal-x fs-2 d-block mb-2"
                                    ></i>

                                    No records found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in records"
                                :key="item.id"
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
                                        {{ item.report_no }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            item.type === 'Lost'
                                                ? 'bg-danger'
                                                : 'bg-warning text-dark'
                                        "
                                    >
                                        {{ item.type }}
                                    </span>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            memberName(
                                                item.library_member
                                            )
                                        }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            item.library_member
                                                ?.member_no ||
                                            '-'
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            item.book?.title ||
                                            '-'
                                        }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            item.book_copy
                                                ?.accession_no ||
                                            item.book_copy
                                                ?.barcode ||
                                            '-'
                                        }}
                                    </small>

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            item.report_date
                                        )
                                    }}
                                </td>


                                <td>
                                    ₹{{ money(item.book_value) }}
                                </td>


                                <td>
                                    ₹{{ money(item.charge_amount) }}
                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            chargeClass(
                                                item.charge_status
                                            )
                                        "
                                    >
                                        {{
                                            item.charge_status
                                        }}
                                    </span>

                                    <small
                                        class="d-block mt-1"
                                    >
                                        {{
                                            item.resolution
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="View"
                                        @click="
                                            viewRecord(item)
                                        "
                                    >
                                        <i
                                            class="bi bi-eye"
                                        ></i>
                                    </button>


                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        title="Update"
                                        @click="
                                            openEditModal(item)
                                        "
                                    >
                                        <i
                                            class="bi bi-pencil"
                                        ></i>
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- Pagination -->
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


        <!-- Create / Edit Modal -->
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
                        @submit.prevent="
                            saveRecord
                        "
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                {{
                                    editingId
                                        ? 'Update Lost / Damaged Record'
                                        : 'Report Lost / Damaged Book'
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

                                <!-- Type -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Type
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.type"
                                        class="form-select"
                                        :disabled="!!editingId"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Lost">
                                            Lost
                                        </option>

                                        <option value="Damaged">
                                            Damaged
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.type
                                        "
                                        class="text-danger small mt-1"
                                    >
                                        {{ formErrors.type }}
                                    </div>

                                </div>


                                <!-- Report Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Report Date
                                    </label>

                                    <input
                                        v-model="
                                            form.report_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Report No -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Report No.
                                    </label>

                                    <input
                                        v-model="
                                            form.report_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Auto generated"
                                        :disabled="!!editingId"
                                    >

                                </div>


                                <!-- Book Issue -->
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
                                        @change="
                                            issueChanged
                                        "
                                    >

                                        <option value="">
                                            Select Book Issue
                                        </option>

                                        <option
                                            v-for="issue in issues"
                                            :key="issue.id"
                                            :value="issue.id"
                                        >

                                            {{ issue.issue_no }}
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
                                        <span
                                            v-if="
                                                !form.book_issue_id
                                            "
                                            class="text-danger"
                                        >
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
                                            v-for="
                                                member in members
                                            "
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
                                        v-model="
                                            form.book_id
                                        "
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


                                <!-- Book Copy -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Book Copy
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        v-model="
                                            form.book_copy_id
                                        "
                                        class="form-select"
                                        :disabled="
                                            !!form.book_issue_id ||
                                            !!editingId
                                        "
                                        @change="
                                            copyChanged
                                        "
                                    >

                                        <option value="">
                                            Select Book Copy
                                        </option>

                                        <option
                                            v-for="
                                                copy in filteredCopies
                                            "
                                            :key="copy.id"
                                            :value="copy.id"
                                        >

                                            {{
                                                copy.accession_no ||
                                                copy.barcode ||
                                                `Copy #${copy.id}`
                                            }}

                                        </option>

                                    </select>

                                </div>


                                <!-- Book Value -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Book Value
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                form.book_value
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            @input="
                                                calculateCharge
                                            "
                                        >

                                    </div>

                                </div>


                                <!-- Charge -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Charge Amount
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                form.charge_amount
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            :disabled="!!editingId"
                                        >

                                    </div>

                                </div>


                                <!-- Charge Status -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Charge Status
                                    </label>

                                    <select
                                        v-model="
                                            form.charge_status
                                        "
                                        class="form-select"
                                        :disabled="
                                            !editingId
                                        "
                                    >

                                        <option value="Pending">
                                            Pending
                                        </option>

                                        <option value="Paid">
                                            Paid
                                        </option>

                                        <option value="Waived">
                                            Waived
                                        </option>

                                    </select>

                                </div>


                                <!-- Resolution -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Resolution
                                    </label>

                                    <select
                                        v-model="
                                            form.resolution
                                        "
                                        class="form-select"
                                        :disabled="
                                            !editingId
                                        "
                                    >

                                        <option value="Pending">
                                            Pending
                                        </option>

                                        <option value="Replacement">
                                            Replacement
                                        </option>

                                        <option value="Payment">
                                            Payment
                                        </option>

                                        <option value="Waived">
                                            Waived
                                        </option>

                                    </select>

                                </div>


                                <!-- Resolved Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Resolved Date
                                    </label>

                                    <input
                                        v-model="
                                            form.resolved_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :disabled="
                                            !editingId
                                        "
                                    >

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
                                        placeholder="Describe the loss or damage..."
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
                                        : 'Save Report'
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
                            Lost / Damaged Details
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
                            v-if="selectedRecord"
                            class="row g-3"
                        >

                            <div class="col-md-4">

                                <small class="text-muted">
                                    Report No.
                                </small>

                                <h5>
                                    {{
                                        selectedRecord.report_no
                                    }}
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
                                            selectedRecord.type ===
                                            'Lost'
                                                ? 'bg-danger'
                                                : 'bg-warning text-dark'
                                        "
                                    >
                                        {{
                                            selectedRecord.type
                                        }}
                                    </span>
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Report Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRecord.report_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Member
                                </small>

                                <strong class="d-block">
                                    {{
                                        memberName(
                                            selectedRecord.library_member
                                        )
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedRecord
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
                                        selectedRecord
                                            .book
                                            ?.title ||
                                        '-'
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedRecord
                                            .book_copy
                                            ?.accession_no ||
                                        selectedRecord
                                            .book_copy
                                            ?.barcode ||
                                        '-'
                                    }}
                                </small>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Book Value
                                </small>

                                <h5>
                                    ₹{{
                                        money(
                                            selectedRecord.book_value
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Charge
                                </small>

                                <h5>
                                    ₹{{
                                        money(
                                            selectedRecord.charge_amount
                                        )
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Charge Status
                                </small>

                                <div>
                                    <span
                                        class="badge"
                                        :class="
                                            chargeClass(
                                                selectedRecord
                                                    .charge_status
                                            )
                                        "
                                    >
                                        {{
                                            selectedRecord
                                                .charge_status
                                        }}
                                    </span>
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Resolution
                                </small>

                                <div>
                                    {{
                                        selectedRecord.resolution
                                    }}
                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Resolved Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRecord
                                                .resolved_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Description
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedRecord
                                            .description ||
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
                                        selectedRecord
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

import {
    computed,
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const records = ref([]);

const issues = ref([]);

const members = ref([]);

const books = ref([]);

const copies = ref([]);

const loading = ref(false);

const saving = ref(false);

const loadingOptions = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showFormModal = ref(false);

const showViewModal = ref(false);

const selectedRecord = ref(null);

const editingId = ref(null);


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    lost: 0,

    damaged: 0,

    pending_charges: 0,

    paid_charges: 0,

    waived_charges: 0,

    pending_amount: 0,

    paid_amount: 0,

    total_charge: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    type: '',

    charge_status: '',

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
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    book_issue_id: '',

    library_member_id: '',

    book_id: '',

    book_copy_id: '',

    report_no: '',

    report_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    type: '',

    description: '',

    book_value: 0,

    charge_amount: 0,

    charge_status: 'Pending',

    resolution: 'Pending',

    resolved_date: '',

    remarks: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Auth
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
| Filtered Copies
|--------------------------------------------------------------------------
*/

const filteredCopies = computed(() => {

    if (!form.value.book_id) {

        return copies.value;

    }


    return copies.value.filter(
        copy =>
            String(copy.book_id) ===
            String(form.value.book_id)
    );

});


/*
|--------------------------------------------------------------------------
| Load Records
|--------------------------------------------------------------------------
*/

const loadRecords = async (
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

            type:
                filters.value.type ||
                undefined,

            charge_status:
                filters.value.charge_status ||
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
                '/api/lost-damaged-books',
                {
                    ...getAuthConfig(),
                    params
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
            'Lost/damaged loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load lost/damaged records.';

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
                '/api/lost-damaged-books/statistics',
                getAuthConfig()
            );


        const data =
            response.data?.data ||
            {};


        statistics.value = {

            total:
                Number(
                    data.total ||
                    0
                ),

            lost:
                Number(
                    data.lost ||
                    0
                ),

            damaged:
                Number(
                    data.damaged ||
                    0
                ),

            pending_charges:
                Number(
                    data.pending_charges ||
                    0
                ),

            paid_charges:
                Number(
                    data.paid_charges ||
                    0
                ),

            waived_charges:
                Number(
                    data.waived_charges ||
                    0
                ),

            pending_amount:
                Number(
                    data.pending_amount ||
                    0
                ),

            paid_amount:
                Number(
                    data.paid_amount ||
                    0
                ),

            total_charge:
                Number(
                    data.total_charge ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Statistics error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Form Options
|--------------------------------------------------------------------------
*/

const loadOptions = async () => {

    loadingOptions.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | Issues
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

        } catch (e) {

            console.warn(
                'Book issues could not be loaded.',
                e
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

        } catch (e) {

            console.warn(
                'Library members could not be loaded.',
                e
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

        } catch (e) {

            console.warn(
                'Books could not be loaded.',
                e
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Copies
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await axios.get(
                    '/api/book-copies',
                    {
                        ...getAuthConfig(),

                        params: {
                            per_page: 500
                        }

                    }
                );


            const result =
                response.data?.data;


            copies.value =
                result?.data ||
                result ||
                [];

        } catch (e) {

            console.warn(
                'Book copies could not be loaded.',
                e
            );

        }

    } finally {

        loadingOptions.value = false;

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
    item
) => {

    editingId.value =
        item.id;


    form.value = {

        book_issue_id:
            item.book_issue_id || '',

        library_member_id:
            item.library_member_id || '',

        book_id:
            item.book_id || '',

        book_copy_id:
            item.book_copy_id || '',

        report_no:
            item.report_no || '',

        report_date:
            dateValue(
                item.report_date
            ),

        type:
            item.type || '',

        description:
            item.description || '',

        book_value:
            Number(
                item.book_value || 0
            ),

        charge_amount:
            Number(
                item.charge_amount || 0
            ),

        charge_status:
            item.charge_status ||
            'Pending',

        resolution:
            item.resolution ||
            'Pending',

        resolved_date:
            dateValue(
                item.resolved_date
            ),

        remarks:
            item.remarks || ''

    };


    formErrors.value = {};

    formError.value = '';

    showFormModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Close Form
|--------------------------------------------------------------------------
*/

const closeFormModal = () => {

    if (saving.value) {
        return;
    }


    showFormModal.value = false;

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
     * Try to get book value.
     */

    const book =
        books.value.find(
            item =>
                String(item.id) ===
                String(
                    form.value.book_id
                )
        );


    const copy =
        copies.value.find(
            item =>
                String(item.id) ===
                String(
                    form.value.book_copy_id
                )
        );


    form.value.book_value =
        Number(
            copy?.book_value ||
            copy?.purchase_price ||
            copy?.price ||
            book?.book_value ||
            book?.purchase_price ||
            book?.price ||
            0
        );


    calculateCharge();

};


/*
|--------------------------------------------------------------------------
| Copy Changed
|--------------------------------------------------------------------------
*/

const copyChanged = () => {

    const copy =
        copies.value.find(
            item =>
                String(item.id) ===
                String(
                    form.value.book_copy_id
                )
        );


    if (!copy) {
        return;
    }


    form.value.book_id =
        copy.book_id ||
        form.value.book_id;


    form.value.book_value =
        Number(
            copy.book_value ||
            copy.purchase_price ||
            copy.price ||
            form.value.book_value ||
            0
        );


    calculateCharge();

};


/*
|--------------------------------------------------------------------------
| Calculate Charge
|--------------------------------------------------------------------------
*/

const calculateCharge = () => {

    const value =
        Number(
            form.value.book_value
        ) || 0;


    if (
        form.value.type === 'Lost'
    ) {

        form.value.charge_amount =
            value;

        return;

    }


    if (
        form.value.type === 'Damaged'
    ) {

        form.value.charge_amount =
            Math.round(
                (
                    value * 0.50 +
                    Number.EPSILON
                ) * 100
            ) / 100;

    }

};


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveRecord = async () => {

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
                    `/api/lost-damaged-books/${editingId.value}`,
                    {

                        description:
                            form.value.description,

                        charge_amount:
                            Number(
                                form.value.charge_amount
                            ),

                        charge_status:
                            form.value.charge_status,

                        resolution:
                            form.value.resolution,

                        resolved_date:
                            form.value.resolved_date ||
                            null,

                        remarks:
                            form.value.remarks ||
                            null

                    },
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/lost-damaged-books',
                    {

                        book_issue_id:
                            form.value.book_issue_id ||
                            null,

                        library_member_id:
                            form.value.library_member_id ||
                            null,

                        book_id:
                            form.value.book_id ||
                            null,

                        book_copy_id:
                            form.value.book_copy_id,

                        report_no:
                            form.value.report_no ||
                            null,

                        report_date:
                            form.value.report_date,

                        type:
                            form.value.type,

                        description:
                            form.value.description ||
                            null,

                        book_value:
                            Number(
                                form.value.book_value
                            ) || 0,

                        charge_amount:
                            Number(
                                form.value.charge_amount
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
            (
                editingId.value
                    ? 'Record updated successfully.'
                    : 'Lost/damaged report created successfully.'
            );


        showFormModal.value =
            false;


        await Promise.all([

            loadRecords(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Save lost/damaged error:',
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
                'Unable to save record.';

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

const viewRecord = (
    item
) => {

    selectedRecord.value =
        item;

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


    loadRecords(page);

};


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        type: '',

        charge_status: '',

        from_date: '',

        to_date: '',

        per_page: 20

    };


    loadRecords(1);

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


    if (
        member.member_type ===
        'Student'
    ) {

        return (
            member.student?.name ||
            member.student?.student_name ||
            (
                member.student?.first_name
                    ? `${member.student.first_name} ${
                        member.student.last_name || ''
                    }`.trim()
                    : member.member_no
            )
        );

    }


    return (
        member.staff?.name ||
        member.staff?.staff_name ||
        (
            member.staff?.first_name
                ? `${member.staff.first_name} ${
                    member.staff.last_name || ''
                }`.trim()
                : member.member_no
        )
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
| Charge Badge
|--------------------------------------------------------------------------
*/

const chargeClass = (
    status
) => {

    switch (status) {

        case 'Paid':
            return 'bg-success';

        case 'Waived':
            return 'bg-secondary';

        case 'Pending':
        default:
            return 'bg-warning text-dark';

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

            loadRecords(1),

            loadStatistics()

        ]);

    }
);

</script>