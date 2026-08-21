<template>
    <div class="container-fluid py-3">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">
                    {{ t('Library Reports') }}
                </h4>

                <small class="text-muted">
                    {{ t('Library statistics and reports') }}
                </small>
            </div>

            <button
                class="btn btn-outline-primary"
                @click="loadDashboard"
                :disabled="loadingDashboard"
            >
                <i class="bi bi-arrow-clockwise me-1"></i>
                {{ t('Refresh') }}
            </button>
        </div>


        <!-- ========================================================= -->
        <!-- Dashboard Cards -->
        <!-- ========================================================= -->

        <div class="row g-3 mb-4">

            <div
                v-for="card in dashboardCards"
                :key="card.key"
                class="col-6 col-md-4 col-lg-3"
            >
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <div class="text-muted small">
                                    {{ card.label }}
                                </div>

                                <h4 class="mb-0 mt-2">
                                    {{ formatDashboardValue(card.key) }}
                                </h4>
                            </div>

                            <div
                                class="report-icon"
                                :class="card.iconClass"
                            >
                                <i :class="card.icon"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- Report Tabs -->
        <!-- ========================================================= -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white">

                <div class="d-flex flex-wrap gap-2">

                    <button
                        v-for="report in reports"
                        :key="report.key"
                        type="button"
                        class="btn btn-sm"
                        :class="
                            activeReport === report.key
                                ? 'btn-primary'
                                : 'btn-outline-primary'
                        "
                        @click="changeReport(report.key)"
                    >
                        <i
                            v-if="report.icon"
                            :class="report.icon + ' me-1'"
                        ></i>

                        {{ report.label }}
                    </button>

                </div>

            </div>


            <div class="card-body">


                <!-- ================================================= -->
                <!-- Filters -->
                <!-- ================================================= -->

                <div
                    v-if="activeReport !== 'dashboard'"
                    class="row g-2 mb-3"
                >

                    <!-- Search -->
                    <div
                        v-if="
                            [
                                'issued',
                                'returned',
                                'overdue'
                            ].includes(activeReport)
                        "
                        class="col-md-3"
                    >
                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            :placeholder="t('Search...')"
                            @keyup.enter="loadReport"
                        >
                    </div>


                    <!-- From Date -->
                    <div class="col-md-2">
                        <label class="form-label small mb-1">
                            {{ t('From Date') }}
                        </label>

                        <input
                            v-model="filters.from_date"
                            type="date"
                            class="form-control"
                        >
                    </div>


                    <!-- To Date -->
                    <div class="col-md-2">
                        <label class="form-label small mb-1">
                            {{ t('To Date') }}
                        </label>

                        <input
                            v-model="filters.to_date"
                            type="date"
                            class="form-control"
                        >
                    </div>


                    <!-- Lost / Damaged Type -->
                    <div
                        v-if="activeReport === 'lost_damaged'"
                        class="col-md-2"
                    >
                        <label class="form-label small mb-1">
                            {{ t('Type') }}
                        </label>

                        <select
                            v-model="filters.type"
                            class="form-select"
                        >
                            <option value="">
                                {{ t('All') }}
                            </option>

                            <option value="Lost">
                                {{ t('Lost') }}
                            </option>

                            <option value="Damaged">
                                {{ t('Damaged') }}
                            </option>
                        </select>
                    </div>


                    <!-- Lost / Damaged Status -->
                    <div
                        v-if="activeReport === 'lost_damaged'"
                        class="col-md-2"
                    >
                        <label class="form-label small mb-1">
                            {{ t('Status') }}
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                        >
                            <option value="">
                                {{ t('All') }}
                            </option>

                            <option value="Reported">
                                {{ t('Reported') }}
                            </option>

                            <option value="Resolved">
                                {{ t('Resolved') }}
                            </option>

                            <option value="Cancelled">
                                {{ t('Cancelled') }}
                            </option>
                        </select>
                    </div>


                    <!-- Fine Type -->
                    <div
                        v-if="activeReport === 'outstanding_fines'"
                        class="col-md-2"
                    >
                        <label class="form-label small mb-1">
                            {{ t('Fine Type') }}
                        </label>

                        <select
                            v-model="filters.fine_type"
                            class="form-select"
                        >
                            <option value="">
                                {{ t('All') }}
                            </option>

                            <option value="Overdue">
                                {{ t('Overdue') }}
                            </option>

                            <option value="Lost">
                                {{ t('Lost') }}
                            </option>

                            <option value="Damaged">
                                {{ t('Damaged') }}
                            </option>

                            <option value="Other">
                                {{ t('Other') }}
                            </option>
                        </select>
                    </div>


                    <!-- Payment Method -->
                    <div
                        v-if="activeReport === 'fine_collection'"
                        class="col-md-2"
                    >
                        <label class="form-label small mb-1">
                            {{ t('Payment Method') }}
                        </label>

                        <select
                            v-model="filters.payment_method"
                            class="form-select"
                        >
                            <option value="">
                                {{ t('All') }}
                            </option>

                            <option value="Cash">
                                {{ t('Cash') }}
                            </option>

                            <option value="Card">
                                {{ t('Card') }}
                            </option>

                            <option value="Bank">
                                {{ t('Bank') }}
                            </option>

                            <option value="Online">
                                {{ t('Online') }}
                            </option>

                            <option value="Other">
                                {{ t('Other') }}
                            </option>
                        </select>
                    </div>


                    <div class="col-md-auto align-self-end">

                        <button
                            class="btn btn-primary"
                            @click="loadReport"
                            :disabled="loadingReport"
                        >
                            <span
                                v-if="loadingReport"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            <i
                                v-else
                                class="bi bi-search me-1"
                            ></i>

                            {{ t('Search') }}
                        </button>

                    </div>


                    <div class="col-md-auto align-self-end">

                        <button
                            class="btn btn-outline-secondary"
                            @click="resetFilters"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            {{ t('Reset') }}
                        </button>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- Dashboard -->
                <!-- ================================================= -->

                <div
                    v-if="activeReport === 'dashboard'"
                    class="text-center py-5"
                >

                    <div
                        v-if="loadingDashboard"
                        class="spinner-border text-primary"
                    ></div>

                    <div
                        v-else-if="dashboardLoaded"
                        class="text-muted"
                    >
                        <i class="bi bi-bar-chart-line fs-2"></i>

                        <div class="mt-2">
                            {{ t('Library dashboard loaded successfully.') }}
                        </div>
                    </div>

                </div>


                <!-- ================================================= -->
                <!-- Report Table -->
                <!-- ================================================= -->

                <div
                    v-else
                    class="table-responsive"
                >

                    <table
                        class="table table-hover table-bordered align-middle mb-0"
                    >

                        <!-- ================= ISSUED ================= -->

                        <template v-if="activeReport === 'issued'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Issue No.') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Copy') }}</th>
                                    <th>{{ t('Issue Date') }}</th>
                                    <th>{{ t('Due Date') }}</th>
                                    <th>{{ t('Status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>
                                        {{ rowNumber(index) }}
                                    </td>

                                    <td>
                                        {{ item.issue_no || '-' }}
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                        <small
                                            v-if="item.library_member?.member_no"
                                            class="d-block text-muted"
                                        >
                                            {{ item.library_member.member_no }}
                                        </small>
                                    </td>

                                    <td>
                                        {{ item.book?.title || '-' }}

                                        <small
                                            v-if="item.book?.isbn"
                                            class="d-block text-muted"
                                        >
                                            ISBN:
                                            {{ item.book.isbn }}
                                        </small>
                                    </td>

                                    <td>
                                        {{
                                            item.book_copy?.accession_no ||
                                            item.book_copy?.barcode ||
                                            '-'
                                        }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.issue_date) }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.due_date) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-primary">
                                            {{ item.status || 'Issued' }}
                                        </span>
                                    </td>

                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= RETURNED ================= -->

                        <template v-else-if="activeReport === 'returned'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Issue No.') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Issue Date') }}</th>
                                    <th>{{ t('Return Date') }}</th>
                                    <th>{{ t('Status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        {{ item.issue_no || '-' }}
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                    </td>

                                    <td>
                                        {{ item.book?.title || '-' }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.issue_date) }}
                                    </td>

                                    <td>
                                        {{
                                            formatDate(
                                                item.return_date ||
                                                item.returned_at
                                            )
                                        }}
                                    </td>

                                    <td>
                                        <span class="badge bg-success">
                                            {{ item.status || 'Returned' }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= OVERDUE ================= -->

                        <template v-else-if="activeReport === 'overdue'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Issue No.') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Due Date') }}</th>
                                    <th>{{ t('Days Overdue') }}</th>
                                    <th>{{ t('Status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        {{ item.issue_no || '-' }}
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                    </td>

                                    <td>
                                        {{ item.book?.title || '-' }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.due_date) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            {{ daysOverdue(item.due_date) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-danger">
                                            {{ t('Overdue') }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= RENEWALS ================= -->

                        <template v-else-if="activeReport === 'renewals'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Old Due Date') }}</th>
                                    <th>{{ t('New Due Date') }}</th>
                                    <th>{{ t('Renewal Date') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        {{
                                            memberName(
                                                item.book_issue?.library_member
                                            )
                                        }}
                                    </td>

                                    <td>
                                        {{
                                            item.book_issue?.book?.title ||
                                            '-'
                                        }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.old_due_date) }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.new_due_date) }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.renewal_date) }}
                                    </td>
                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= LOST/DAMAGED ================= -->

                        <template v-else-if="activeReport === 'lost_damaged'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Type') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Reported Date') }}</th>
                                    <th>{{ t('Book Value') }}</th>
                                    <th>{{ t('Fine') }}</th>
                                    <th>{{ t('Status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        <span
                                            class="badge"
                                            :class="
                                                item.type === 'Lost'
                                                    ? 'bg-danger'
                                                    : 'bg-warning text-dark'
                                            "
                                        >
                                            {{ item.type || '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                    </td>

                                    <td>
                                        {{
                                            item.book_issue?.book?.title ||
                                            item.book_copy?.book?.title ||
                                            '-'
                                        }}
                                    </td>

                                    <td>
                                        {{ formatDate(item.reported_date) }}
                                    </td>

                                    <td>
                                        {{ money(item.book_value) }}
                                    </td>

                                    <td>
                                        {{ money(item.fine_amount) }}
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ item.status || '-' }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= OUTSTANDING FINES ================= -->

                        <template v-else-if="activeReport === 'outstanding_fines'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Fine Date') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Book') }}</th>
                                    <th>{{ t('Fine Type') }}</th>
                                    <th>{{ t('Amount') }}</th>
                                    <th>{{ t('Paid') }}</th>
                                    <th>{{ t('Due') }}</th>
                                    <th>{{ t('Status') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        {{ formatDate(item.fine_date) }}
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                    </td>

                                    <td>
                                        {{
                                            item.book_issue?.book?.title ||
                                            '-'
                                        }}
                                    </td>

                                    <td>
                                        {{ item.fine_type || '-' }}
                                    </td>

                                    <td>
                                        {{ money(item.amount) }}
                                    </td>

                                    <td>
                                        {{ money(item.paid_amount) }}
                                    </td>

                                    <td class="fw-bold text-danger">
                                        {{ money(item.due_amount) }}
                                    </td>

                                    <td>
                                        <span
                                            class="badge"
                                            :class="
                                                item.status === 'Partially Paid'
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-danger'
                                            "
                                        >
                                            {{ item.status || '-' }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </template>


                        <!-- ================= FINE COLLECTION ================= -->

                        <template v-else-if="activeReport === 'fine_collection'">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ t('Payment Date') }}</th>
                                    <th>{{ t('Member') }}</th>
                                    <th>{{ t('Fine Type') }}</th>
                                    <th>{{ t('Amount') }}</th>
                                    <th>{{ t('Payment Method') }}</th>
                                    <th>{{ t('Reference') }}</th>
                                    <th>{{ t('Collected By') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="(item, index) in rows"
                                    :key="item.id"
                                >
                                    <td>{{ rowNumber(index) }}</td>

                                    <td>
                                        {{ formatDate(item.payment_date) }}
                                    </td>

                                    <td>
                                        {{ memberName(item.library_member) }}
                                    </td>

                                    <td>
                                        {{
                                            item.library_fine?.fine_type ||
                                            '-'
                                        }}
                                    </td>

                                    <td class="fw-bold">
                                        {{ money(item.amount) }}
                                    </td>

                                    <td>
                                        {{ item.payment_method || '-' }}
                                    </td>

                                    <td>
                                        {{ item.reference_no || '-' }}
                                    </td>

                                    <td>
                                        {{
                                            item.collector?.name ||
                                            item.collector?.username ||
                                            '-'
                                        }}
                                    </td>

                                </tr>

                            </tbody>

                        </template>

                    </table>


                    <!-- Empty -->
                    <div
                        v-if="!loadingReport && rows.length === 0"
                        class="text-center text-muted py-5"
                    >
                        <i class="bi bi-inbox fs-1"></i>

                        <div class="mt-2">
                            {{ t('No records found.') }}
                        </div>
                    </div>


                    <!-- Loading -->
                    <div
                        v-if="loadingReport"
                        class="text-center py-5"
                    >
                        <div
                            class="spinner-border text-primary"
                        ></div>
                    </div>

                </div>


                <!-- ================================================= -->
                <!-- Pagination -->
                <!-- ================================================= -->

                <div
                    v-if="
                        activeReport !== 'dashboard' &&
                        pagination.total > 0
                    "
                    class="d-flex justify-content-between align-items-center mt-3"
                >

                    <div class="text-muted small">
                        {{ t('Showing') }}
                        {{ pagination.from || 0 }}
                        -
                        {{ pagination.to || 0 }}
                        {{ t('of') }}
                        {{ pagination.total }}
                    </div>

                    <div class="d-flex gap-1">

                        <button
                            class="btn btn-sm btn-outline-secondary"
                            :disabled="
                                pagination.current_page <= 1 ||
                                loadingReport
                            "
                            @click="
                                goToPage(
                                    pagination.current_page - 1
                                )
                            "
                        >
                            {{ t('Previous') }}
                        </button>

                        <button
                            class="btn btn-sm btn-outline-secondary"
                            :disabled="
                                pagination.current_page >=
                                pagination.last_page ||
                                loadingReport
                            "
                            @click="
                                goToPage(
                                    pagination.current_page + 1
                                )
                            "
                        >
                            {{ t('Next') }}
                        </button>

                    </div>

                </div>

            </div>
        </div>

    </div>
</template>


<script setup>

import {
    ref,
    reactive,
    computed,
    onMounted
} from 'vue';

import axios from 'axios';

/*
|--------------------------------------------------------------------------
| Authentication Config
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
| Translation
|--------------------------------------------------------------------------
*/

const t = (text) => {

    if (
        typeof window !== 'undefined' &&
        typeof window.__ === 'function'
    ) {
        return window.__(text);
    }

    return text;
};


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const activeReport = ref('dashboard');

const loadingDashboard = ref(false);

const loadingReport = ref(false);

const dashboardLoaded = ref(false);

const rows = ref([]);


const dashboard = reactive({

    books: 0,

    copies: 0,

    members: 0,

    issued: 0,

    returned: 0,

    overdue: 0,

    renewals: 0,

    lost: 0,

    damaged: 0,

    total_fines: 0,

    outstanding_fine: 0,

    fine_collected: 0

});


const pagination = reactive({

    total: 0,

    per_page: 20,

    current_page: 1,

    last_page: 1,

    from: 0,

    to: 0

});


const filters = reactive({

    search: '',

    from_date: '',

    to_date: '',

    type: '',

    status: '',

    fine_type: '',

    payment_method: ''

});


/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/

const reports = [

    {
        key: 'dashboard',
        label: 'Dashboard',
        icon: 'bi bi-speedometer2'
    },

    {
        key: 'issued',
        label: 'Issued Books',
        icon: 'bi bi-book'
    },

    {
        key: 'returned',
        label: 'Returned Books',
        icon: 'bi bi-arrow-return-left'
    },

    {
        key: 'overdue',
        label: 'Overdue Books',
        icon: 'bi bi-clock-history'
    },

    {
        key: 'renewals',
        label: 'Renewals',
        icon: 'bi bi-arrow-repeat'
    },

    {
        key: 'lost_damaged',
        label: 'Lost / Damaged',
        icon: 'bi bi-exclamation-triangle'
    },

    {
        key: 'outstanding_fines',
        label: 'Outstanding Fines',
        icon: 'bi bi-cash-stack'
    },

    {
        key: 'fine_collection',
        label: 'Fine Collection',
        icon: 'bi bi-wallet2'
    }

];


/*
|--------------------------------------------------------------------------
| Dashboard Cards
|--------------------------------------------------------------------------
*/

const dashboardCards = [

    {
        key: 'books',
        label: 'Total Books',
        icon: 'bi bi-book',
        iconClass: 'bg-primary-subtle text-primary'
    },

    {
        key: 'copies',
        label: 'Book Copies',
        icon: 'bi bi-collection',
        iconClass: 'bg-info-subtle text-info'
    },

    {
        key: 'members',
        label: 'Members',
        icon: 'bi bi-people',
        iconClass: 'bg-success-subtle text-success'
    },

    {
        key: 'issued',
        label: 'Issued',
        icon: 'bi bi-box-arrow-up-right',
        iconClass: 'bg-primary-subtle text-primary'
    },

    {
        key: 'returned',
        label: 'Returned',
        icon: 'bi bi-arrow-return-left',
        iconClass: 'bg-success-subtle text-success'
    },

    {
        key: 'overdue',
        label: 'Overdue',
        icon: 'bi bi-clock',
        iconClass: 'bg-danger-subtle text-danger'
    },

    {
        key: 'renewals',
        label: 'Renewals',
        icon: 'bi bi-arrow-repeat',
        iconClass: 'bg-warning-subtle text-warning'
    },

    {
        key: 'lost',
        label: 'Lost',
        icon: 'bi bi-exclamation-circle',
        iconClass: 'bg-danger-subtle text-danger'
    },

    {
        key: 'damaged',
        label: 'Damaged',
        icon: 'bi bi-tools',
        iconClass: 'bg-warning-subtle text-warning'
    },

    {
        key: 'outstanding_fine',
        label: 'Outstanding Fine',
        icon: 'bi bi-cash',
        iconClass: 'bg-danger-subtle text-danger'
    },

    {
        key: 'fine_collected',
        label: 'Fine Collected',
        icon: 'bi bi-cash-stack',
        iconClass: 'bg-success-subtle text-success'
    }

];


/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const reportTitle = computed(() => {

    const report = reports.find(
        item => item.key === activeReport.value
    );

    return report?.label || 'Library Reports';

});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

const loadDashboard = async () => {

    loadingDashboard.value = true;

    try {

        const response = await axios.get(
            '/api/library-reports/dashboard',
            getAuthConfig()
        );

        const data =
            response.data?.data || {};

        Object.keys(dashboard).forEach(key => {

            dashboard[key] =
                data[key] ?? 0;

        });

        dashboardLoaded.value = true;

    } catch (error) {

        console.error(
            'Unable to load library dashboard.',
            error
        );

        dashboardLoaded.value = false;

    } finally {

        loadingDashboard.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Report
|--------------------------------------------------------------------------
*/

const loadReport = async (
    page = 1
) => {

    if (
        activeReport.value ===
        'dashboard'
    ) {
        return;
    }

    loadingReport.value = true;

    try {

        const endpoint =
            getEndpoint(
                activeReport.value
            );

        const params = {

            page,

            per_page:
                pagination.per_page

        };


        Object.keys(filters).forEach(key => {

            if (
                filters[key] !== '' &&
                filters[key] !== null &&
                filters[key] !== undefined
            ) {
                params[key] =
                    filters[key];
            }

        });


        const response =
        await axios.get(
            endpoint,
            {
                ...getAuthConfig(),
                params
            }
        );


        const result =
            response.data?.data;


        /*
        |--------------------------------------------------------------------------
        | Pagination Response
        |--------------------------------------------------------------------------
        */

        if (
            result &&
            Array.isArray(result.data)
        ) {

            rows.value =
                result.data;

            updatePagination(
                result
            );

        } else {

            rows.value =
                Array.isArray(result)
                    ? result
                    : [];

            resetPagination();

        }

    } catch (error) {

        console.error(
            'Unable to load library report.',
            error
        );

        rows.value = [];

        resetPagination();

    } finally {

        loadingReport.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Endpoint
|--------------------------------------------------------------------------
*/

const getEndpoint = (
    report
) => {

    const endpoints = {

        issued:
            '/api/library-reports/issued-books',

        returned:
            '/api/library-reports/returned-books',

        overdue:
            '/api/library-reports/overdue-books',

        renewals:
            '/api/library-reports/renewals',

        lost_damaged:
            '/api/library-reports/lost-damaged',

        outstanding_fines:
            '/api/library-reports/outstanding-fines',

        fine_collection:
            '/api/library-reports/fine-collection'

    };

    return endpoints[report];

};


/*
|--------------------------------------------------------------------------
| Change Report
|--------------------------------------------------------------------------
*/

const changeReport = (
    report
) => {

    activeReport.value =
        report;

    rows.value = [];

    resetPagination();

    resetFilters();

    if (
        report ===
        'dashboard'
    ) {

        loadDashboard();

    } else {

        loadReport(1);

    }

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.search = '';

    filters.from_date = '';

    filters.to_date = '';

    filters.type = '';

    filters.status = '';

    filters.fine_type = '';

    filters.payment_method = '';

};


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const updatePagination = (
    data
) => {

    pagination.total =
        data.total || 0;

    pagination.per_page =
        data.per_page ||
        20;

    pagination.current_page =
        data.current_page ||
        1;

    pagination.last_page =
        data.last_page ||
        1;

    pagination.from =
        data.from || 0;

    pagination.to =
        data.to || 0;

};


const resetPagination = () => {

    pagination.total = 0;

    pagination.current_page = 1;

    pagination.last_page = 1;

    pagination.from = 0;

    pagination.to = 0;

};


const goToPage = (
    page
) => {

    if (
        page < 1 ||
        page >
        pagination.last_page
    ) {
        return;
    }

    loadReport(page);

};


const rowNumber = (
    index
) => {

    return (
        (pagination.current_page - 1) *
        pagination.per_page
    ) + index + 1;

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
                    : null
            ) ||
            member.name ||
            member.member_no ||
            '-'
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
                : null
        ) ||
        member.name ||
        member.member_no ||
        '-'
    );

};


/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

    if (!value) {
        return '-';
    }

    const date =
        new Date(value);

    if (
        Number.isNaN(
            date.getTime()
        )
    ) {
        return '-';
    }

    return date.toLocaleDateString(
        'en-GB',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

};


/*
|--------------------------------------------------------------------------
| Days Overdue
|--------------------------------------------------------------------------
*/

const daysOverdue = (
    dueDate
) => {

    if (!dueDate) {
        return 0;
    }

    const due =
        new Date(dueDate);

    const today =
        new Date();

    due.setHours(
        0, 0, 0, 0
    );

    today.setHours(
        0, 0, 0, 0
    );

    const diff =
        today.getTime() -
        due.getTime();

    return Math.max(
        0,
        Math.ceil(
            diff /
            (1000 * 60 * 60 * 24)
        )
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

    const number =
        Number(value || 0);

    return number.toFixed(2);

};


/*
|--------------------------------------------------------------------------
| Dashboard Value
|--------------------------------------------------------------------------
*/

const formatDashboardValue = (
    key
) => {

    const moneyKeys = [
        'total_fines',
        'outstanding_fine',
        'fine_collected'
    ];

    if (
        moneyKeys.includes(key)
    ) {

        return money(
            dashboard[key]
        );

    }

    return dashboard[key] ?? 0;

};


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadDashboard();

});

</script>


<style scoped>

.report-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 20px;
}

.table th {
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.card {
    border-radius: 10px;
}

</style>