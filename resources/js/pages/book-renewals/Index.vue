<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Book Renewal</h4>
                <small class="text-muted">
                    Manage library book renewals
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openRenewalModal"
            >
                <i class="bi bi-arrow-repeat me-1"></i>
                Renew Book
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

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Total Renewals
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
                            Today
                        </small>

                        <h3 class="mb-0 text-primary">
                            {{ statistics.today }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            This Month
                        </small>

                        <h3 class="mb-0 text-success">
                            {{ statistics.this_month }}
                        </h3>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <small class="text-muted">
                            Members
                        </small>

                        <h3 class="mb-0 text-info">
                            {{ statistics.members }}
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
                            placeholder="Renewal no, issue no, member, book..."
                            @keyup.enter="loadRenewals(1)"
                        >

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            v-model="filters.from_date"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            v-model="filters.to_date"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-3 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadRenewals(1)"
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


        <!-- Renewal List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Renewal History
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
                                    Renewal No.
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
                                    Old Due Date
                                </th>

                                <th>
                                    New Due Date
                                </th>

                                <th>
                                    Renewal
                                </th>

                                <th>
                                    Date
                                </th>

                                <th width="80">
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
                                        Loading renewals...
                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-else-if="
                                    renewals.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-arrow-repeat fs-2 d-block mb-2"
                                    ></i>

                                    No renewal records found.

                                </td>

                            </tr>


                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in renewals"
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
                                        {{ item.renewal_no }}
                                    </strong>
                                </td>


                                <td>
                                    {{
                                        item.book_issue?.issue_no ||
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
                                            item.old_due_date
                                        )
                                    }}
                                </td>


                                <td>

                                    <strong class="text-success">
                                        {{
                                            formatDate(
                                                item.new_due_date
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span class="badge bg-primary">
                                        #{{ item.renewal_count }}
                                    </span>

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            item.renewal_date
                                        )
                                    }}
                                </td>


                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="View"
                                        @click="viewRenewal(item)"
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


        <!-- Renewal Modal -->
        <div
            v-if="showRenewalModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form @submit.prevent="processRenewal">

                        <div class="modal-header">

                            <h5 class="modal-title">
                                Renew Book
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeRenewalModal"
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

                                <!-- Issue -->
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
                                        @change="issueChanged"
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


                                <!-- Loading -->
                                <div
                                    v-if="loadingIssues"
                                    class="col-md-12"
                                >

                                    <div class="text-muted">
                                        Loading active issues...
                                    </div>

                                </div>


                                <!-- Issue Details -->
                                <div
                                    v-if="selectedIssue"
                                    class="col-md-12"
                                >

                                    <div class="border rounded p-3">

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
                                                    Current Due Date
                                                </small>

                                                <strong
                                                    class="d-block"
                                                    :class="{
                                                        'text-danger':
                                                            isOverdue(
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
                                                        isOverdue(
                                                            selectedIssue
                                                        )
                                                    "
                                                    class="text-danger"
                                                >
                                                    Overdue
                                                </small>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Renewal Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Renewal Date
                                    </label>

                                    <input
                                        v-model="
                                            form.renewal_date
                                        "
                                        type="date"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Renewal Days -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Renewal Period
                                    </label>

                                    <div class="input-group">

                                        <input
                                            v-model.number="
                                                form.renewal_days
                                            "
                                            type="number"
                                            min="1"
                                            max="365"
                                            class="form-control"
                                            @input="
                                                calculatePreview
                                            "
                                        >

                                        <span class="input-group-text">
                                            Days
                                        </span>

                                    </div>

                                </div>


                                <!-- Renewal Count -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Maximum Renewals
                                    </label>

                                    <div class="form-control bg-light">
                                        {{ maxRenewals }}
                                    </div>

                                </div>


                                <!-- Date Preview -->
                                <div
                                    v-if="selectedIssue"
                                    class="col-md-12"
                                >

                                    <div class="row g-3">

                                        <div class="col-md-4">

                                            <div class="border rounded p-3">

                                                <small class="text-muted">
                                                    Current Due Date
                                                </small>

                                                <h5>
                                                    {{
                                                        formatDate(
                                                            selectedIssue.due_date
                                                        )
                                                    }}
                                                </h5>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div class="border rounded p-3">

                                                <small class="text-muted">
                                                    Renewal Period
                                                </small>

                                                <h5>
                                                    {{
                                                        form.renewal_days
                                                    }}
                                                    days
                                                </h5>

                                            </div>

                                        </div>


                                        <div class="col-md-4">

                                            <div
                                                class="border rounded p-3"
                                                :class="{
                                                    'border-success':
                                                        preview.new_due_date
                                                }"
                                            >

                                                <small class="text-muted">
                                                    New Due Date
                                                </small>

                                                <h5 class="text-success">
                                                    {{
                                                        preview.new_due_date
                                                            ? formatDate(
                                                                preview.new_due_date
                                                            )
                                                            : '-'
                                                    }}
                                                </h5>

                                            </div>

                                        </div>

                                    </div>

                                </div>


                                <!-- Warning -->
                                <div
                                    v-if="preview.warning"
                                    class="col-md-12"
                                >

                                    <div class="alert alert-warning mb-0">
                                        <i
                                            class="bi bi-exclamation-triangle me-1"
                                        ></i>

                                        {{ preview.warning }}
                                    </div>

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
                                        placeholder="Renewal remarks..."
                                    ></textarea>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="saving"
                                @click="closeRenewalModal"
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

                                <i
                                    v-else
                                    class="bi bi-arrow-repeat me-1"
                                ></i>

                                Renew Book

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
                            Renewal Details
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
                            v-if="selectedRenewal"
                            class="row g-3"
                        >

                            <div class="col-md-6">

                                <small class="text-muted">
                                    Renewal No.
                                </small>

                                <h5>
                                    {{
                                        selectedRenewal.renewal_no
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Issue No.
                                </small>

                                <h5>
                                    {{
                                        selectedRenewal
                                            .book_issue
                                            ?.issue_no ||
                                        '-'
                                    }}
                                </h5>

                            </div>


                            <div class="col-md-6">

                                <small class="text-muted">
                                    Member
                                </small>

                                <strong class="d-block">
                                    {{
                                        memberName(
                                            selectedRenewal
                                                .library_member
                                        )
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedRenewal
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
                                        selectedRenewal
                                            .book
                                            ?.title ||
                                        '-'
                                    }}
                                </strong>

                                <small>
                                    {{
                                        selectedRenewal
                                            .book_copy
                                            ?.accession_no ||
                                        selectedRenewal
                                            .book_copy
                                            ?.barcode ||
                                        '-'
                                    }}
                                </small>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Renewal Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRenewal
                                                .renewal_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Old Due Date
                                </small>

                                <div>
                                    {{
                                        formatDate(
                                            selectedRenewal
                                                .old_due_date
                                        )
                                    }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    New Due Date
                                </small>

                                <strong class="text-success d-block">
                                    {{
                                        formatDate(
                                            selectedRenewal
                                                .new_due_date
                                        )
                                    }}
                                </strong>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Renewal Count
                                </small>

                                <h5>
                                    #{{ selectedRenewal.renewal_count }}
                                </h5>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Renewal Days
                                </small>

                                <h5>
                                    {{
                                        renewalDays(
                                            selectedRenewal
                                        )
                                    }}
                                    days
                                </h5>

                            </div>


                            <div class="col-md-12">

                                <small class="text-muted">
                                    Remarks
                                </small>

                                <div class="border rounded p-2">
                                    {{
                                        selectedRenewal.remarks ||
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

const renewals = ref([]);

const activeIssues = ref([]);

const loading = ref(false);

const loadingIssues = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showRenewalModal = ref(false);

const showViewModal = ref(false);

const selectedIssue = ref(null);

const selectedRenewal = ref(null);


/*
|--------------------------------------------------------------------------
| Library Settings
|--------------------------------------------------------------------------
*/

const maxRenewals = 2;

const defaultRenewalDays = 14;


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    today: 0,

    this_month: 0,

    members: 0,

    books: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

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

    renewal_no: '',

    renewal_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    renewal_days:
        defaultRenewalDays,

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

    new_due_date: '',

    warning: ''

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
| Load Renewals
|--------------------------------------------------------------------------
*/

const loadRenewals = async (
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

            from_date:
                filters.value.from_date ||
                undefined,

            to_date:
                filters.value.to_date ||
                undefined

        };


        const response =
            await axios.get(
                '/api/book-renewals',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        renewals.value =
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
            'Renewal loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load renewal records.';

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
                '/api/book-renewals/statistics',
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

            today:
                Number(
                    data.today ||
                    0
                ),

            this_month:
                Number(
                    data.this_month ||
                    0
                ),

            members:
                Number(
                    data.members ||
                    0
                ),

            books:
                Number(
                    data.books ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Renewal statistics error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Active Issues
|--------------------------------------------------------------------------
*/

const loadActiveIssues = async () => {

    loadingIssues.value = true;

    formError.value = '';


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
        |--------------------------------------------------------------------------
        | Also load overdue issues
        |--------------------------------------------------------------------------
        */

        try {

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
                item =>
                    map.set(
                        item.id,
                        item
                    )
            );


            overdue.forEach(
                item =>
                    map.set(
                        item.id,
                        item
                    )
            );


            activeIssues.value =
                Array.from(
                    map.values()
                );

        } catch (e) {

            /*
             * Overdue endpoint may not exist.
             * The normal issue list is still usable.
             */

            console.warn(
                'Overdue issues could not be loaded.'
            );

        }

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
| Open Modal
|--------------------------------------------------------------------------
*/

const openRenewalModal = async () => {

    form.value =
        emptyForm();

    selectedIssue.value =
        null;

    preview.value = {

        new_due_date: '',

        warning: ''

    };

    formErrors.value = {};

    formError.value = '';

    showRenewalModal.value =
        true;


    await loadActiveIssues();

};


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeRenewalModal = () => {

    if (saving.value) {
        return;
    }

    showRenewalModal.value =
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
| Calculate New Due Date
|--------------------------------------------------------------------------
*/

const calculatePreview = () => {

    preview.value = {

        new_due_date: '',

        warning: ''

    };


    if (
        !selectedIssue.value ||
        !selectedIssue.value.due_date
    ) {
        return;
    }


    const dueDate =
        new Date(
            selectedIssue.value.due_date
        );


    const days =
        Number(
            form.value.renewal_days
        );


    if (
        !days ||
        days <= 0
    ) {

        preview.value.warning =
            'Renewal period must be greater than zero.';

        return;

    }


    dueDate.setDate(
        dueDate.getDate() +
        days
    );


    preview.value.new_due_date =
        dueDate
            .toISOString()
            .substring(
                0,
                10
            );


    /*
    |--------------------------------------------------------------------------
    | Renewal Count Warning
    |--------------------------------------------------------------------------
    */

    if (
        selectedIssue.value.renewal_count >=
        maxRenewals
    ) {

        preview.value.warning =
            `This book has reached the maximum renewal limit of ${maxRenewals}.`;

    }


    /*
    |--------------------------------------------------------------------------
    | Overdue Warning
    |--------------------------------------------------------------------------
    */

    if (
        isOverdue(
            selectedIssue.value
        )
    ) {

        preview.value.warning =
            'This book is overdue and may not be eligible for renewal.';

    }

};


/*
|--------------------------------------------------------------------------
| Process Renewal
|--------------------------------------------------------------------------
*/

const processRenewal = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            book_issue_id:
                form.value.book_issue_id,

            renewal_no:
                form.value.renewal_no ||
                null,

            renewal_date:
                form.value.renewal_date,

            renewal_days:
                Number(
                    form.value.renewal_days
                ),

            remarks:
                form.value.remarks ||
                null

        };


        const response =
            await axios.post(
                '/api/book-renewals',
                payload,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book renewed successfully.';


        showRenewalModal.value =
            false;


        await Promise.all([

            loadRenewals(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Book renewal error:',
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
                'Unable to renew book.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Renewal
|--------------------------------------------------------------------------
*/

const viewRenewal = (
    item
) => {

    selectedRenewal.value =
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

        from_date: '',

        to_date: '',

        per_page: 20

    };


    loadRenewals(1);

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


    loadRenewals(page);

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
| Overdue
|--------------------------------------------------------------------------
*/

const isOverdue = (
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
| Renewal Days
|--------------------------------------------------------------------------
*/

const renewalDays = (
    renewal
) => {

    if (
        !renewal?.old_due_date ||
        !renewal?.new_due_date
    ) {

        return 0;

    }


    const oldDate =
        new Date(
            renewal.old_due_date
        );


    const newDate =
        new Date(
            renewal.new_due_date
        );


    return Math.round(
        (
            newDate -
            oldDate
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
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([

            loadRenewals(1),

            loadStatistics()

        ]);

    }
);

</script>