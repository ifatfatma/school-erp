<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Book Issue
                </h4>

                <small class="text-muted">
                    Issue books to library members
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Issue Book
            </button>

        </div>


        <!-- Messages -->
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

            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Issues
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
                            Currently Issued
                        </small>

                        <h3 class="mb-0 text-primary">
                            {{ statistics.issued }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-2">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Returned
                        </small>

                        <h3 class="mb-0 text-success">
                            {{ statistics.returned }}
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
                            Lost
                        </small>

                        <h3 class="mb-0 text-warning">
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

                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Issue no, member, book, ISBN or barcode..."
                            @keyup.enter="loadIssues(1)"
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
                                All
                            </option>

                            <option value="Issued">
                                Issued
                            </option>

                            <option value="Returned">
                                Returned
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
                            @click="loadIssues(1)"
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


        <!-- Issue List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <strong>
                    Issue History
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
                                    Issue No.
                                </th>

                                <th>
                                    Member
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Copy
                                </th>

                                <th>
                                    Issue Date
                                </th>

                                <th>
                                    Due Date
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="100"
                                    class="text-center"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading issues...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    issues.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-journal-x fs-2 d-block mb-2"
                                    ></i>

                                    No book issues found.

                                </td>

                            </tr>


                            <tr
                                v-for="(
                                    issue,
                                    index
                                ) in issues"
                                :key="issue.id"
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
                                        {{ issue.issue_no }}
                                    </strong>
                                </td>


                                <td>

                                    <strong>
                                        {{
                                            memberName(
                                                issue.library_member
                                            )
                                        }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            issue.library_member
                                                ?.member_no
                                        }}
                                    </small>

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            issue.book?.title ||
                                            '-'
                                        }}
                                    </strong>

                                    <small
                                        v-if="
                                            issue.book?.isbn
                                        "
                                        class="d-block text-muted"
                                    >
                                        ISBN:
                                        {{ issue.book.isbn }}
                                    </small>

                                </td>


                                <td>

                                    {{
                                        issue.book_copy
                                            ?.accession_no ||
                                        issue.book_copy
                                            ?.barcode ||
                                        '-'
                                    }}

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            issue.issue_date
                                        )
                                    }}
                                </td>


                                <td>

                                    <span
                                        :class="{
                                            'text-danger fw-bold':
                                                isOverdue(issue)
                                        }"
                                    >
                                        {{
                                            formatDate(
                                                issue.due_date
                                            )
                                        }}
                                    </span>

                                    <small
                                        v-if="
                                            isOverdue(issue)
                                        "
                                        class="d-block text-danger"
                                    >
                                        {{ daysOverdue(issue) }}
                                        day(s) overdue
                                    </small>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                issue
                                            )
                                        "
                                    >
                                        {{
                                            displayStatus(
                                                issue
                                            )
                                        }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="View"
                                        @click="
                                            viewIssue(issue)
                                        "
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>


                                    <button
                                        v-if="
                                            ![
                                                'Issued',
                                                'Overdue'
                                            ].includes(
                                                issue.status
                                            )
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId === issue.id
                                        "
                                        @click="
                                            deleteIssue(issue)
                                        "
                                    >
                                        <i class="bi bi-trash"></i>
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


        <!-- Issue Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="issueBook"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Issue Book
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

                                <!-- Member -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Library Member
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.library_member_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.library_member_id
                                        }"
                                        @change="
                                            memberChanged
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

                                    <div
                                        v-if="
                                            formErrors.library_member_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.library_member_id
                                        }}
                                    </div>

                                </div>


                                <!-- Member info -->
                                <div
                                    v-if="selectedMember"
                                    class="col-md-12"
                                >

                                    <div
                                        class="alert alert-light border mb-0"
                                    >

                                        <div class="row">

                                            <div class="col-md-4">
                                                <strong>
                                                    Member:
                                                </strong>
                                                {{
                                                    memberName(
                                                        selectedMember
                                                    )
                                                }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>
                                                    Limit:
                                                </strong>
                                                {{
                                                    selectedMember.issue_limit
                                                }}
                                            </div>

                                            <div class="col-md-3">
                                                <strong>
                                                    Active:
                                                </strong>
                                                {{
                                                    selectedMember.status
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                            </div>

                                            <div class="col-md-2">

                                                <span
                                                    class="badge"
                                                    :class="
                                                        memberValid(
                                                            selectedMember
                                                        )
                                                            ? 'bg-success'
                                                            : 'bg-danger'
                                                    "
                                                >
                                                    {{
                                                        memberValid(
                                                            selectedMember
                                                        )
                                                            ? 'Valid'
                                                            : 'Invalid'
                                                    }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Book -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Book
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.book_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.book_id
                                        }"
                                        @change="
                                            bookChanged
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
                                            {{
                                                book.title
                                            }}
                                            <span
                                                v-if="book.isbn"
                                            >
                                                -
                                                {{ book.isbn }}
                                            </span>
                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.book_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.book_id }}
                                    </div>

                                </div>


                                <!-- Copy -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Book Copy
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="
                                            form.book_copy_id
                                        "
                                        class="form-select"
                                        :disabled="
                                            !form.book_id ||
                                            loadingCopies
                                        "
                                        :class="{
                                            'is-invalid':
                                                formErrors.book_copy_id
                                        }"
                                    >

                                        <option value="">
                                            {{
                                                loadingCopies
                                                    ? 'Loading copies...'
                                                    : 'Select Available Copy'
                                            }}
                                        </option>

                                        <option
                                            v-for="copy in copies"
                                            :key="copy.id"
                                            :value="copy.id"
                                        >

                                            {{
                                                copy.accession_no ||
                                                copy.barcode
                                            }}

                                            <template
                                                v-if="
                                                    copy.barcode &&
                                                    copy.accession_no
                                                "
                                            >
                                                -
                                                {{ copy.barcode }}
                                            </template>

                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.book_copy_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.book_copy_id
                                        }}
                                    </div>

                                </div>


                                <!-- Issue Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Issue Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.issue_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.issue_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.issue_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.issue_date
                                        }}
                                    </div>

                                </div>


                                <!-- Due Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Due Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.due_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.due_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.due_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.due_date }}
                                    </div>

                                </div>


                                <!-- Notes -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Notes
                                    </label>

                                    <textarea
                                        v-model="
                                            form.notes
                                        "
                                        class="form-control"
                                        rows="3"
                                        placeholder="Notes..."
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
                                    !form.library_member_id ||
                                    !form.book_id ||
                                    !form.book_copy_id
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                Issue Book

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
                            Book Issue Details
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
                            v-if="selectedIssue"
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100">

                                    <small class="text-muted">
                                        Issue Number
                                    </small>

                                    <h5>
                                        {{
                                            selectedIssue.issue_no
                                        }}
                                    </h5>

                                    <small class="text-muted">
                                        Member
                                    </small>

                                    <div>
                                        {{
                                            memberName(
                                                selectedIssue.library_member
                                            )
                                        }}
                                    </div>

                                    <small class="text-muted">
                                        Member No.
                                    </small>

                                    <div>
                                        {{
                                            selectedIssue
                                                .library_member
                                                ?.member_no ||
                                            '-'
                                        }}
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100">

                                    <small class="text-muted">
                                        Book
                                    </small>

                                    <h5>
                                        {{
                                            selectedIssue.book?.title ||
                                            '-'
                                        }}
                                    </h5>

                                    <small class="text-muted">
                                        Copy
                                    </small>

                                    <div>
                                        {{
                                            selectedIssue.book_copy
                                                ?.accession_no ||
                                            selectedIssue.book_copy
                                                ?.barcode ||
                                            '-'
                                        }}
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Issue Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedIssue.issue_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Due Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedIssue.due_date
                                        )
                                    }}
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
                                                selectedIssue
                                            )
                                        "
                                    >
                                        {{
                                            displayStatus(
                                                selectedIssue
                                            )
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Notes
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedIssue.notes ||
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

const issues = ref([]);

const members = ref([]);

const books = ref([]);

const copies = ref([]);

const loading = ref(false);

const loadingCopies = ref(false);

const saving = ref(false);

const actionId = ref(null);

const showModal = ref(false);

const showViewModal = ref(false);

const selectedIssue = ref(null);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/
const formatDate = (value) => {

    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};
const statistics = ref({

    total: 0,

    issued: 0,

    returned: 0,

    overdue: 0,

    lost: 0,

    damaged: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    status: '',

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

    library_member_id: '',

    book_id: '',

    book_copy_id: '',

    issue_no: '',

    issue_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    due_date:
        getDefaultDueDate(),

    notes: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Selected Member
|--------------------------------------------------------------------------
*/

const selectedMember = computed(() => {

    return members.value.find(
        member =>
            String(member.id) ===
            String(
                form.value.library_member_id
            )
    ) || null;

});


/*
|--------------------------------------------------------------------------
| Selected Book
|--------------------------------------------------------------------------
*/

const selectedBook = computed(() => {

    return books.value.find(
        book =>
            String(book.id) ===
            String(
                form.value.book_id
            )
    ) || null;

});


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
| Default Due Date
|--------------------------------------------------------------------------
*/

function getDefaultDueDate()
{
    const date =
        new Date();

    date.setDate(
        date.getDate() + 14
    );

    return date
        .toISOString()
        .substring(
            0,
            10
        );
}


/*
|--------------------------------------------------------------------------
| Load Issues
|--------------------------------------------------------------------------
*/

const loadIssues = async (
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

            status:
                filters.value.status ||
                undefined,

            overdue:
                filters.value.overdue === ''
                    ? undefined
                    : filters.value.overdue

        };


        const response =
            await axios.get(
                '/api/book-issues',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        issues.value =
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
            'Book issue loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load book issues.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Members
|--------------------------------------------------------------------------
*/

const loadMembers = async () => {

    try {

        const response =
            await axios.get(
                '/api/library-members',
                {
                    ...getAuthConfig(),
                    params: {
                        per_page: 100,
                        status: 1,
                        expired: 0
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

        console.error(
            'Member loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Books
|--------------------------------------------------------------------------
*/

const loadBooks = async () => {

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

        console.error(
            'Book loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Available Copies
|--------------------------------------------------------------------------
*/

const loadCopies = async () => {

    copies.value = [];

    form.value.book_copy_id = '';

    if (!form.value.book_id) {
        return;
    }


    loadingCopies.value = true;


    try {

        /*
         * This endpoint should return only
         * copies belonging to the selected book.
         */

        const response =
            await axios.get(
                '/api/book-copies',
                {
                    ...getAuthConfig(),
                    params: {

                        book_id:
                            form.value.book_id,

                        status:
                            'Available',

                        per_page:
                            100

                    }
                }
            );


        const result =
            response.data?.data;


        copies.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.error(
            'Book copy loading error:',
            err
        );


        formError.value =
            err.response?.data?.message ||
            'Unable to load available book copies.';

    } finally {

        loadingCopies.value = false;

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
                '/api/book-issues/statistics',
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

            issued:
                Number(
                    data.issued ||
                    0
                ),

            returned:
                Number(
                    data.returned ||
                    0
                ),

            overdue:
                Number(
                    data.overdue ||
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
                )

        };

    } catch (err) {

        console.error(
            'Issue statistics error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    form.value =
        emptyForm();

    copies.value = [];

    formErrors.value = {};

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Close Modal
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
| Member Changed
|--------------------------------------------------------------------------
*/

const memberChanged = () => {

    formErrors.value.library_member_id =
        undefined;

};


/*
|--------------------------------------------------------------------------
| Book Changed
|--------------------------------------------------------------------------
*/

const bookChanged = () => {

    form.value.book_copy_id = '';

    loadCopies();

};


/*
|--------------------------------------------------------------------------
| Issue Book
|--------------------------------------------------------------------------
*/

const issueBook = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            library_member_id:
                form.value.library_member_id,

            book_id:
                form.value.book_id,

            book_copy_id:
                form.value.book_copy_id,

            issue_no:
                form.value.issue_no ||
                null,

            issue_date:
                form.value.issue_date,

            due_date:
                form.value.due_date,

            notes:
                form.value.notes ||
                null

        };


        const response =
            await axios.post(
                '/api/book-issues',
                payload,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book issued successfully.';


        showModal.value = false;


        await Promise.all([

            loadIssues(
                pagination.value.current_page
            ),

            loadStatistics(),

            loadMembers(),

            loadBooks()

        ]);

    } catch (err) {

        console.error(
            'Book issue error:',
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
                'Unable to issue book.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Issue
|--------------------------------------------------------------------------
*/

const viewIssue = async (
    issue
) => {

    selectedIssue.value =
        issue;

    showViewModal.value =
        true;

};


/*
|--------------------------------------------------------------------------
| Delete Issue
|--------------------------------------------------------------------------
*/

const deleteIssue = async (
    issue
) => {

    if (
        !window.confirm(
            `Delete issue "${issue.issue_no}"?`
        )
    ) {
        return;
    }


    actionId.value =
        issue.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(
                `/api/book-issues/${issue.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book issue deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            issues.value.length === 1 &&
            page > 1
        ) {
            page--;
        }


        await Promise.all([

            loadIssues(page),

            loadStatistics()

        ]);

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete book issue.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        status: '',

        overdue: '',

        per_page: 20

    };


    loadIssues(1);

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


    loadIssues(page);

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
| Member Valid
|--------------------------------------------------------------------------
*/

const memberValid = (
    member
) => {

    if (!member) {
        return false;
    }


    if (!member.status) {
        return false;
    }


    if (
        member.expiry_date &&
        new Date(
            member.expiry_date
        ) <
        new Date(
            new Date()
                .setHours(
                    0,
                    0,
                    0,
                    0
                )
        )
    ) {

        return false;

    }


    return true;

};


/*
|--------------------------------------------------------------------------
| Overdue
|--------------------------------------------------------------------------
*/

const isOverdue = (
    issue
) => {

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


    if (!issue.due_date) {
        return false;
    }


    const due =
        new Date(
            issue.due_date
        );


    const today =
        new Date();


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
| Days Overdue
|--------------------------------------------------------------------------
*/

const daysOverdue = (
    issue
) => {

    if (
        !isOverdue(issue)
    ) {
        return 0;
    }


    const due =
        new Date(
            issue.due_date
        );


    const today =
        new Date();


    today.setHours(
        0,
        0,
        0,
        0
    );


    return Math.floor(
        (
            today - due
        ) /
        (
            1000 *
            60 *
            60 *
            24
        )
    );

};


/*
|--------------------------------------------------------------------------
| Display Status
|--------------------------------------------------------------------------
*/

const displayStatus = (
    issue
) => {

    if (
        isOverdue(issue) &&
        [
            'Issued',
            'Overdue'
        ].includes(
            issue.status
        )
    ) {

        return 'Overdue';

    }


    return issue.status;

};


/*
|--------------------------------------------------------------------------
| Status Class
|--------------------------------------------------------------------------
*/

const statusClass = (
    issue
) => {

    const status =
        displayStatus(
            issue
        );


    switch (status) {

        case 'Issued':
            return 'bg-primary';

        case 'Returned':
            return 'bg-success';

        case 'Overdue':
            return 'bg-danger';

        case 'Lost':
            return 'bg-warning text-dark';

        case 'Damaged':
            return 'bg-secondary';

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

            loadIssues(1),

            loadStatistics(),

            loadMembers(),

            loadBooks()

        ]);

    }
);

</script>