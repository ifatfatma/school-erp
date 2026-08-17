<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Book Return
                </h4>

                <small class="text-muted">
                    Manage returned books and overdue fines
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openReturnModal"
            >
                <i class="bi bi-arrow-return-left me-1"></i>
                Return Book
            </button>

        </div>


        <!-- Success -->
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


        <!-- Error -->
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

            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Total Returns
                        </small>

                        <h3 class="mb-0">
                            {{ statistics.total }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Overdue
                        </small>

                        <h3 class="mb-0 text-danger">
                            {{ statistics.overdue }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Fine Pending
                        </small>

                        <h3 class="mb-0 text-warning">
                            {{ statistics.fine_pending }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Fine Paid
                        </small>

                        <h3 class="mb-0 text-success">
                            {{ statistics.fine_paid }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

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


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Damaged
                        </small>

                        <h3 class="mb-0 text-secondary">
                            {{ statistics.damaged }}
                        </h3>

                    </div>
                </div>

            </div>

        </div>


        <!-- Filters -->
        <div class="card mb-3">

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
                            placeholder="Return no, issue no, member, book..."
                            @keyup.enter="loadReturns(1)"
                        >

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Condition
                        </label>

                        <select
                            v-model="
                                filters.book_condition
                            "
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Good">
                                Good
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>

                            <option value="Lost">
                                Lost
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Fine
                        </label>

                        <select
                            v-model="
                                filters.fine_status
                            "
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="No Fine">
                                No Fine
                            </option>

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="Paid">
                                Paid
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            Overdue
                        </label>

                        <select
                            v-model="filters.overdue"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="1">
                                Overdue Only
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadReturns(1)"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Return List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <strong>
                    Return History
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
                                    Return No.
                                </th>

                                <th>
                                    Issue No.
                                </th>

                                <th>
                                    Member
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Return Date
                                </th>

                                <th>
                                    Overdue
                                </th>

                                <th>
                                    Fine
                                </th>

                                <th>
                                    Condition
                                </th>

                                <th
                                    width="90"
                                    class="text-center"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr v-if="loading">

                                <td
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading returns...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    returns.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-arrow-return-left fs-2 d-block mb-2"
                                    ></i>

                                    No book returns found.

                                </td>

                            </tr>


                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in returns"
                                :key="item.id"
                            >

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


                                <td>

                                    <strong>
                                        {{ item.return_no }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        item.book_issue
                                            ?.issue_no ||
                                        '-'
                                    }}

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
                                            item.return_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            Number(
                                                item.overdue_days
                                            ) > 0
                                        "
                                        class="badge bg-danger"
                                    >
                                        {{
                                            item.overdue_days
                                        }}
                                        day(s)
                                    </span>

                                    <span
                                        v-else
                                        class="badge bg-success"
                                    >
                                        No Overdue
                                    </span>

                                </td>


                                <td>

                                    <strong
                                        v-if="
                                            Number(
                                                item.fine_amount
                                            ) > 0
                                        "
                                        class="text-danger"
                                    >
                                        ₹{{
                                            Number(
                                                item.fine_amount
                                            ).toFixed(2)
                                        }}
                                    </strong>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        No Fine
                                    </span>

                                    <small
                                        v-if="
                                            Number(
                                                item.fine_amount
                                            ) > 0
                                        "
                                        class="d-block"
                                    >
                                        <span
                                            class="badge"
                                            :class="
                                                item.fine_status === 'Paid'
                                                    ? 'bg-success'
                                                    : 'bg-warning text-dark'
                                            "
                                        >
                                            {{
                                                item.fine_status
                                            }}
                                        </span>
                                    </small>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            conditionClass(
                                                item.book_condition
                                            )
                                        "
                                    >
                                        {{
                                            item.book_condition
                                        }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View"
                                        @click="
                                            viewReturn(item)
                                        "
                                    >
                                        <i class="bi bi-eye"></i>
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


        <!-- Return Modal -->
        <div
            v-if="showReturnModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="processReturn"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Return Book
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeReturnModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <!-- Issue Search -->
                            <div class="row g-3">

                                <div class="col-md-12">

                                    <label class="form-label">
                                        Active Book Issue
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.book_issue_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.book_issue_id
                                        }"
                                        @change="
                                            issueChanged
                                        "
                                    >

                                        <option value="">
                                            Select issued book
                                        </option>

                                        <option
                                            v-for="issue in activeIssues"
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

                                    <div
                                        v-if="
                                            formErrors.book_issue_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.book_issue_id
                                        }}
                                    </div>

                                </div>


                                <!-- Issue Information -->
                                <div
                                    v-if="selectedIssue"
                                    class="col-md-12"
                                >

                                    <div
                                        class="border rounded p-3"
                                    >

                                        <div class="row g-3">

                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Member
                                                </small>

                                                <strong class="d-block">
                                                    {{
                                                        memberName(
                                                            selectedIssue.library_member
                                                        )
                                                    }}
                                                </strong>

                                                <small>
                                                    {{
                                                        selectedIssue
                                                            .library_member
                                                            ?.member_no ||
                                                        '-'
                                                    }}
                                                </small>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Book
                                                </small>

                                                <strong class="d-block">
                                                    {{
                                                        selectedIssue
                                                            .book
                                                            ?.title ||
                                                        '-'
                                                    }}
                                                </strong>

                                                <small>
                                                    {{
                                                        selectedIssue
                                                            .book_copy
                                                            ?.accession_no ||
                                                        selectedIssue
                                                            .book_copy
                                                            ?.barcode ||
                                                        '-'
                                                    }}
                                                </small>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="text-muted">
                                                    Due Date
                                                </small>

                                                <strong
                                                    class="d-block"
                                                    :class="{
                                                        'text-danger':
                                                            isIssueOverdue(
                                                                selectedIssue
                                                            )
                                                    }"
                                                >
                                                    {{
                                                        formatDate(
                                                            selectedIssue.due_date
                                                        )
                                                    }}
                                                </strong>

                                                <small
                                                    v-if="
                                                        isIssueOverdue(
                                                            selectedIssue
                                                        )
                                                    "
                                                    class="text-danger"
                                                >
                                                    Currently overdue
                                                </small>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Return Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Return Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.return_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.return_date
                                        }"
                                        @change="
                                            calculatePreview
                                        "
                                    >

                                    <div
                                        v-if="
                                            formErrors.return_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.return_date
                                        }}
                                    </div>

                                </div>


                                <!-- Condition -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Book Condition
                                    </label>

                                    <select
                                        v-model="
                                            form.book_condition
                                        "
                                        class="form-select"
                                    >

                                        <option value="Good">
                                            Good
                                        </option>

                                        <option value="Damaged">
                                            Damaged
                                        </option>

                                        <option value="Lost">
                                            Lost
                                        </option>

                                    </select>

                                </div>


                                <!-- Preview -->
                                <div
                                    v-if="selectedIssue"
                                    class="col-md-12"
                                >

                                    <div
                                        class="alert"
                                        :class="
                                            preview.overdue_days > 0
                                                ? 'alert-warning'
                                                : 'alert-success'
                                        "
                                    >

                                        <div class="row text-center">

                                            <div class="col-md-4">

                                                <small class="d-block">
                                                    Overdue Days
                                                </small>

                                                <strong class="fs-5">
                                                    {{
                                                        preview.overdue_days
                                                    }}
                                                </strong>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="d-block">
                                                    Fine Per Day
                                                </small>

                                                <strong class="fs-5">
                                                    ₹{{
                                                        finePerDay
                                                    }}
                                                </strong>

                                            </div>


                                            <div class="col-md-4">

                                                <small class="d-block">
                                                    Fine Amount
                                                </small>

                                                <strong
                                                    class="fs-5"
                                                >
                                                    ₹{{
                                                        preview.fine_amount
                                                            .toFixed(2)
                                                    }}
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Fine Status -->
                                <div
                                    v-if="
                                        preview.fine_amount > 0
                                    "
                                    class="col-md-6"
                                >

                                    <label class="form-label">
                                        Fine Status
                                    </label>

                                    <select
                                        v-model="
                                            form.fine_status
                                        "
                                        class="form-select"
                                    >

                                        <option value="Pending">
                                            Pending
                                        </option>

                                        <option value="Paid">
                                            Paid
                                        </option>

                                    </select>

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
                                        rows="3"
                                        placeholder="Return remarks..."
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
                                    closeReturnModal
                                "
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="
                                    saving ||
                                    !form.book_issue_id
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                Return Book

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
                            Return Details
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
                            v-if="selectedReturn"
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <small class="text-muted">
                                        Return No.
                                    </small>

                                    <h5>
                                        {{
                                            selectedReturn.return_no
                                        }}
                                    </h5>

                                    <small class="text-muted">
                                        Issue No.
                                    </small>

                                    <div>
                                        {{
                                            selectedReturn
                                                .book_issue
                                                ?.issue_no ||
                                            '-'
                                        }}
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="border rounded p-3">

                                    <small class="text-muted">
                                        Member
                                    </small>

                                    <h5>
                                        {{
                                            memberName(
                                                selectedReturn.library_member
                                            )
                                        }}
                                    </h5>

                                    <small>
                                        {{
                                            selectedReturn
                                                .library_member
                                                ?.member_no ||
                                            '-'
                                        }}
                                    </small>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Book
                                </small>

                                <strong class="d-block">
                                    {{
                                        selectedReturn
                                            .book
                                            ?.title ||
                                        '-'
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Copy
                                </small>

                                <strong class="d-block">
                                    {{
                                        selectedReturn
                                            .book_copy
                                            ?.accession_no ||
                                        selectedReturn
                                            .book_copy
                                            ?.barcode ||
                                        '-'
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Due Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedReturn.due_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Return Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedReturn.return_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Condition
                                </small>

                                <div>

                                    <span
                                        class="badge"
                                        :class="
                                            conditionClass(
                                                selectedReturn
                                                    .book_condition
                                            )
                                        "
                                    >
                                        {{
                                            selectedReturn
                                                .book_condition
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Overdue Days
                                </small>

                                <h5>
                                    {{
                                        selectedReturn.overdue_days
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Fine
                                </small>

                                <h5 class="text-danger">
                                    ₹{{
                                        Number(
                                            selectedReturn
                                                .fine_amount
                                        ).toFixed(2)
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Fine Status
                                </small>

                                <div>

                                    <span
                                        class="badge"
                                        :class="
                                            selectedReturn.fine_status === 'Paid'
                                                ? 'bg-success'
                                                : selectedReturn.fine_status === 'Pending'
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            selectedReturn
                                                .fine_status
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Remarks
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedReturn.remarks ||
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

const returns = ref([]);

const activeIssues = ref([]);

const loading = ref(false);

const loadingIssues = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showReturnModal = ref(false);

const showViewModal = ref(false);

const selectedIssue = ref(null);

const selectedReturn = ref(null);


/*
|--------------------------------------------------------------------------
| Fine
|--------------------------------------------------------------------------
*/

const finePerDay = 5;


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    overdue: 0,

    fine_pending: 0,

    fine_paid: 0,

    fine_amount: 0,

    lost: 0,

    damaged: 0,

    good: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    book_condition: '',

    fine_status: '',

    overdue: '',

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

    return_no: '',

    return_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    book_condition: 'Good',

    fine_status: 'Pending',

    remarks: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Preview
|--------------------------------------------------------------------------
*/

const preview = ref({

    overdue_days: 0,

    fine_amount: 0

});


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
| Load Returns
|--------------------------------------------------------------------------
*/

const loadReturns = async (
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

            book_condition:
                filters.value.book_condition ||
                undefined,

            fine_status:
                filters.value.fine_status ||
                undefined,

            overdue:
                filters.value.overdue === ''
                    ? undefined
                    : filters.value.overdue

        };


        const response =
            await axios.get(
                '/api/book-returns',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        returns.value =
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
            'Book return loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load book returns.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Active Issues
|--------------------------------------------------------------------------
*/

const loadActiveIssues = async () => {

    loadingIssues.value = true;


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


        activeIssues.value =
            result?.data ||
            result ||
            [];


        /*
         * Also load overdue issues because
         * their status may already be Overdue.
         */

        const overdueResponse =
            await axios.get(
                '/api/book-issues/overdue',
                getAuthConfig()
            );


        const overdue =
            overdueResponse.data?.data ||
            [];


        const map =
            new Map();


        activeIssues.value.forEach(
            item => map.set(
                item.id,
                item
            )
        );


        overdue.forEach(
            item => map.set(
                item.id,
                item
            )
        );


        activeIssues.value =
            Array.from(
                map.values()
            );

    } catch (err) {

        console.error(
            'Active issue loading error:',
            err
        );


        formError.value =
            err.response?.data?.message ||
            'Unable to load active book issues.';

    } finally {

        loadingIssues.value = false;

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
                '/api/book-returns/statistics',
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

            overdue:
                Number(
                    data.overdue ||
                    0
                ),

            fine_pending:
                Number(
                    data.fine_pending ||
                    0
                ),

            fine_paid:
                Number(
                    data.fine_paid ||
                    0
                ),

            fine_amount:
                Number(
                    data.fine_amount ||
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

            good:
                Number(
                    data.good ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Return statistics error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Open Return Modal
|--------------------------------------------------------------------------
*/

const openReturnModal = async () => {

    form.value =
        emptyForm();

    selectedIssue.value =
        null;

    preview.value = {

        overdue_days: 0,

        fine_amount: 0

    };

    formErrors.value = {};

    formError.value = '';

    showReturnModal.value =
        true;


    await loadActiveIssues();

};


/*
|--------------------------------------------------------------------------
| Close Return Modal
|--------------------------------------------------------------------------
*/

const closeReturnModal = () => {

    if (saving.value) {
        return;
    }

    showReturnModal.value =
        false;

};


/*
|--------------------------------------------------------------------------
| Issue Changed
|--------------------------------------------------------------------------
*/

const issueChanged = () => {

    selectedIssue.value =
        activeIssues.value.find(
            issue =>
                String(issue.id) ===
                String(
                    form.value.book_issue_id
                )
        ) || null;


    formErrors.value = {};

    calculatePreview();

};


/*
|--------------------------------------------------------------------------
| Calculate Preview
|--------------------------------------------------------------------------
*/

const calculatePreview = () => {

    if (
        !selectedIssue.value ||
        !form.value.return_date
    ) {

        preview.value = {

            overdue_days: 0,

            fine_amount: 0

        };

        return;
    }


    const dueDate =
        new Date(
            selectedIssue.value.due_date
        );


    const returnDate =
        new Date(
            form.value.return_date
        );


    dueDate.setHours(
        0,
        0,
        0,
        0
    );


    returnDate.setHours(
        0,
        0,
        0,
        0
    );


    let overdueDays = 0;


    if (
        returnDate > dueDate
    ) {

        overdueDays =
            Math.floor(
                (
                    returnDate -
                    dueDate
                ) /
                (
                    1000 *
                    60 *
                    60 *
                    24
                )
            );

    }


    const fine =
        overdueDays *
        finePerDay;


    preview.value = {

        overdue_days:
            overdueDays,

        fine_amount:
            fine

    };


    if (fine > 0) {

        if (
            form.value.fine_status ===
            'No Fine'
        ) {

            form.value.fine_status =
                'Pending';

        }

    } else {

        form.value.fine_status =
            'No Fine';

    }

};


/*
|--------------------------------------------------------------------------
| Process Return
|--------------------------------------------------------------------------
*/

const processReturn = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            book_issue_id:
                form.value.book_issue_id,

            return_no:
                form.value.return_no ||
                null,

            return_date:
                form.value.return_date,

            book_condition:
                form.value.book_condition,

            fine_status:
                preview.value.fine_amount > 0
                    ? form.value.fine_status
                    : 'No Fine',

            remarks:
                form.value.remarks ||
                null

        };


        const response =
            await axios.post(
                '/api/book-returns',
                payload,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book returned successfully.';


        showReturnModal.value =
            false;


        await Promise.all([

            loadReturns(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Book return error:',
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
                'Please correct the errors.';

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to return book.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Return
|--------------------------------------------------------------------------
*/

const viewReturn = (
    item
) => {

    selectedReturn.value =
        item;

    showViewModal.value =
        true;

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        book_condition: '',

        fine_status: '',

        overdue: '',

        per_page: 20

    };


    loadReturns(1);

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


    loadReturns(page);

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
| Issue Overdue
|--------------------------------------------------------------------------
*/

const isIssueOverdue = (
    issue
) => {

    if (!issue?.due_date) {
        return false;
    }


    if (
        [
            'Returned',
            'Lost',
            'Damaged'
        ].includes(
            issue.status
        )
    ) {

        return false;

    }


    const due =
        new Date(
            issue.due_date
        );


    const today =
        new Date();


    due.setHours(
        0,
        0,
        0,
        0
    );


    today.setHours(
        0,
        0,
        0,
        0
    );


    return due < today;

};


/*
|--------------------------------------------------------------------------
| Date Format
|--------------------------------------------------------------------------
*/

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
| Condition Class
|--------------------------------------------------------------------------
*/

const conditionClass = (
    condition
) => {

    switch (condition) {

        case 'Good':
            return 'bg-success';

        case 'Damaged':
            return 'bg-warning text-dark';

        case 'Lost':
            return 'bg-danger';

        default:
            return 'bg-secondary';

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

            loadReturns(1),

            loadStatistics()

        ]);

    }
);

</script>