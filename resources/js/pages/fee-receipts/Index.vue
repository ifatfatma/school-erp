<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Fee Receipts
                </h4>

                <small class="text-muted">
                    View, print and download fee payment receipts
                </small>
            </div>

        </div>


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
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
                            placeholder="Receipt no / Student / Admission no"
                            @keyup.enter="loadReceipts(1)"
                        >

                    </div>


                    <!-- Payment Method -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            v-model="filters.payment_method"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Cash">
                                Cash
                            </option>

                            <option value="Card">
                                Card
                            </option>

                            <option value="Bank Transfer">
                                Bank Transfer
                            </option>

                            <option value="UPI">
                                UPI
                            </option>

                            <option value="Cheque">
                                Cheque
                            </option>

                            <option value="Online">
                                Online
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>


                    <!-- From Date -->
                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            v-model="filters.date_from"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- To Date -->
                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            v-model="filters.date_to"
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- Per Page -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Per Page
                        </label>

                        <select
                            v-model="filters.per_page"
                            class="form-select"
                        >

                            <option :value="10">
                                10
                            </option>

                            <option :value="20">
                                20
                            </option>

                            <option :value="50">
                                50
                            </option>

                            <option :value="100">
                                100
                            </option>

                        </select>

                    </div>

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadReceipts(1)"
                    >

                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-1"
                        ></span>

                        Search

                    </button>


                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        :disabled="loading"
                        @click="resetFilters"
                    >
                        Reset
                    </button>

                </div>

            </div>

        </div>


        <!-- Receipt List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Receipts
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Receipt No
                                </th>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Admission No
                                </th>

                                <th>
                                    Payment Date
                                </th>

                                <th>
                                    Payment Method
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="180" class="text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- Loading -->
                            <tr v-if="loading">

                                <td
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="mt-2 text-muted">
                                        Loading receipts...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr v-else-if="receipts.length === 0">

                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >

                                    No receipts found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-else
                                v-for="(receipt, index) in receipts"
                                :key="receipt.id"
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
                                        {{ receipt.receipt_no }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        receipt.student?.full_name
                                        || "-"
                                    }}

                                </td>


                                <td>

                                    {{
                                        receipt.student?.admission_no
                                        || "-"
                                    }}

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            receipt.payment_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <span class="badge bg-secondary">

                                        {{
                                            receipt.payment_method
                                        }}

                                    </span>

                                </td>


                                <td class="text-end">

                                    <strong>

                                        ₹{{
                                            formatAmount(
                                                receipt.total_amount
                                            )
                                        }}

                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                receipt.status
                                            )
                                        "
                                    >

                                        {{ receipt.status }}

                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- View -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="View Receipt"
                                        @click="
                                            viewReceipt(
                                                receipt.id
                                            )
                                        "
                                    >

                                        <i class="bi bi-eye"></i>

                                    </button>


                                    <!-- Print -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary me-1"
                                        title="Print Receipt"
                                        @click="
                                            printReceipt(
                                                receipt.id
                                            )
                                        "
                                    >

                                        <i class="bi bi-printer"></i>

                                    </button>


                                    <!-- Download -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-success"
                                        title="Download PDF"
                                        :disabled="
                                            downloadingId === receipt.id
                                        "
                                        @click="
                                            downloadReceipt(
                                                receipt.id,
                                                receipt.receipt_no
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                downloadingId === receipt.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-download"
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
                v-if="pagination.last_page > 1"
                class="card-footer"
            >

                <div class="d-flex justify-content-between align-items-center">

                    <div class="text-muted">

                        Showing
                        {{ pagination.from || 0 }}
                        to
                        {{ pagination.to || 0 }}
                        of
                        {{ pagination.total }}

                    </div>


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


        <!-- Receipt Details Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Fee Receipt
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            @click="closeModal"
                        ></button>

                    </div>


                    <div class="modal-body">

                        <!-- Loading -->
                        <div
                            v-if="detailsLoading"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="mt-2 text-muted">
                                Loading receipt...
                            </div>

                        </div>


                        <template v-else-if="selectedReceipt">

                            <!-- Receipt Header -->
                            <div class="text-center mb-4">

                                <h4 class="mb-1">
                                    FEE PAYMENT RECEIPT
                                </h4>

                                <div class="fw-bold">
                                    {{
                                        selectedReceipt.receipt_no
                                    }}
                                </div>

                            </div>


                            <!-- Student Details -->
                            <div class="row g-3 mb-4">

                                <div class="col-md-6">

                                    <small class="text-muted">
                                        Student Name
                                    </small>

                                    <div class="fw-bold">

                                        {{
                                            selectedReceipt.student?.full_name
                                            || "-"
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted">
                                        Admission No
                                    </small>

                                    <div class="fw-bold">

                                        {{
                                            selectedReceipt.student?.admission_no
                                            || "-"
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Payment Date
                                    </small>

                                    <div>

                                        {{
                                            formatDate(
                                                selectedReceipt.payment_date
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Payment Method
                                    </small>

                                    <div>

                                        {{
                                            selectedReceipt.payment_method
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
                                                    selectedReceipt.status
                                                )
                                            "
                                        >

                                            {{
                                                selectedReceipt.status
                                            }}

                                        </span>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted">
                                        Transaction No
                                    </small>

                                    <div>

                                        {{
                                            selectedReceipt.transaction_no
                                            || "-"
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <small class="text-muted">
                                        Bank Name
                                    </small>

                                    <div>

                                        {{
                                            selectedReceipt.bank_name
                                            || "-"
                                        }}

                                    </div>

                                </div>

                            </div>


                            <!-- Items -->
                            <div class="table-responsive">

                                <table class="table table-bordered">

                                    <thead class="table-light">

                                        <tr>

                                            <th>
                                                #
                                            </th>

                                            <th>
                                                Fee Type
                                            </th>

                                            <th>
                                                Fee Structure
                                            </th>

                                            <th class="text-end">
                                                Amount
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="(
                                                item,
                                                index
                                            ) in (
                                                selectedReceipt.items || []
                                            )"
                                            :key="item.id"
                                        >

                                            <td>
                                                {{ index + 1 }}
                                            </td>

                                            <td>

                                                {{
                                                    item.fee_due?.fee_type?.name
                                                    || "-"
                                                }}

                                            </td>

                                            <td>

                                                {{
                                                    item.fee_due?.fee_structure?.name
                                                    || "-"
                                                }}

                                            </td>

                                            <td class="text-end">

                                                ₹{{
                                                    formatAmount(
                                                        item.amount
                                                    )
                                                }}

                                            </td>

                                        </tr>


                                        <tr>

                                            <th
                                                colspan="3"
                                                class="text-end"
                                            >
                                                Total
                                            </th>

                                            <th class="text-end">

                                                ₹{{
                                                    formatAmount(
                                                        selectedReceipt.total_amount
                                                    )
                                                }}

                                            </th>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <!-- Remarks -->
                            <div
                                v-if="
                                    selectedReceipt.remarks
                                "
                                class="mt-3"
                            >

                                <strong>
                                    Remarks:
                                </strong>

                                {{ selectedReceipt.remarks }}

                            </div>

                        </template>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="closeModal"
                        >
                            Close
                        </button>


                        <button
                            v-if="selectedReceipt"
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="
                                printReceipt(
                                    selectedReceipt.id
                                )
                            "
                        >

                            <i class="bi bi-printer me-1"></i>

                            Print

                        </button>


                        <button
                            v-if="selectedReceipt"
                            type="button"
                            class="btn btn-success"
                            :disabled="
                                downloadingId ===
                                selectedReceipt.id
                            "
                            @click="
                                downloadReceipt(
                                    selectedReceipt.id,
                                    selectedReceipt.receipt_no
                                )
                            "
                        >

                            <i class="bi bi-download me-1"></i>

                            Download PDF

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
    onMounted
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const receipts = ref([]);

const loading = ref(false);

const error = ref("");

const showModal = ref(false);

const detailsLoading = ref(false);

const selectedReceipt = ref(null);

const downloadingId = ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    payment_method: "",

    date_from: "",

    date_to: "",

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
| Authentication
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

    const token =
        localStorage.getItem("token") ||
        localStorage.getItem("auth_token") ||
        localStorage.getItem("access_token") ||
        localStorage.getItem("authToken") ||
        sessionStorage.getItem("token") ||
        sessionStorage.getItem("auth_token") ||
        sessionStorage.getItem("access_token") ||
        sessionStorage.getItem("authToken");


    const config = {

        withCredentials: true,

        headers: {

            Accept: "application/json"

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
| Load Receipts
|--------------------------------------------------------------------------
*/

const loadReceipts = async (
    page = 1
) => {

    loading.value = true;

    error.value = "";


    try {

        const params = {

            page,

            per_page:
                filters.value.per_page,

            search:
                filters.value.search || undefined,

            payment_method:
                filters.value.payment_method || undefined,

            date_from:
                filters.value.date_from || undefined,

            date_to:
                filters.value.date_to || undefined

        };


        const response = await axios.get(
            "/api/fee-receipts",
            {
                ...getAuthConfig(),
                params
            }
        );


        const result =
            response.data?.data;


        receipts.value =
            result?.data || [];


        pagination.value = {

            current_page:
                result?.current_page || 1,

            last_page:
                result?.last_page || 1,

            per_page:
                result?.per_page ||
                filters.value.per_page,

            total:
                result?.total || 0,

            from:
                result?.from || 0,

            to:
                result?.to || 0

        };


    } catch (err) {

        console.error(
            "Fee receipts error:",
            err
        );


        if (
            err.response?.status === 401
        ) {

            error.value =
                "Authentication expired. Please login again.";

        } else {

            error.value =
                err.response?.data?.message ||
                "Unable to load receipts.";

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Receipt
|--------------------------------------------------------------------------
*/

const viewReceipt = async (
    id
) => {

    showModal.value = true;

    detailsLoading.value = true;

    selectedReceipt.value = null;

    error.value = "";


    try {

        const response = await axios.get(
            `/api/fee-receipts/${id}`,
            getAuthConfig()
        );


        selectedReceipt.value =
            response.data?.data || null;


    } catch (err) {

        console.error(
            "Receipt details error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load receipt.";


        showModal.value = false;

    } finally {

        detailsLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Print Receipt
|--------------------------------------------------------------------------
*/

const printReceipt = async (
    id
) => {

    try {

        const response = await axios.get(
            `/api/fee-receipts/${id}/pdf`,
            {
                ...getAuthConfig(),

                responseType: "blob"
            }
        );


        const blob = new Blob(
            [response.data],
            {
                type: "application/pdf"
            }
        );


        const url =
            window.URL.createObjectURL(blob);


        const printWindow =
            window.open(
                url,
                "_blank"
            );


        if (!printWindow) {

            error.value =
                "Please allow pop-ups to print the receipt.";

            window.URL.revokeObjectURL(url);

            return;

        }


        printWindow.onload = () => {

            printWindow.focus();

            printWindow.print();

        };


        setTimeout(() => {

            window.URL.revokeObjectURL(url);

        }, 60000);


    } catch (err) {

        console.error(
            "Print receipt error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to print receipt.";

    }

};


/*
|--------------------------------------------------------------------------
| Download PDF
|--------------------------------------------------------------------------
*/

const downloadReceipt = async (
    id,
    receiptNo = null
) => {

    downloadingId.value = id;

    error.value = "";


    try {

        const response = await axios.get(
            `/api/fee-receipts/${id}/download`,
            {
                ...getAuthConfig(),

                responseType: "blob"
            }
        );


        const blob = new Blob(
            [response.data],
            {
                type: "application/pdf"
            }
        );


        const url =
            window.URL.createObjectURL(blob);


        const link =
            document.createElement("a");


        link.href = url;


        link.download =
            (
                receiptNo
                ||
                `receipt-${id}`
            ) + ".pdf";


        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);


        setTimeout(() => {

            window.URL.revokeObjectURL(url);

        }, 1000);


    } catch (err) {

        console.error(
            "Download receipt error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to download receipt.";

    } finally {

        downloadingId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    showModal.value = false;

    selectedReceipt.value = null;

};


/*
|--------------------------------------------------------------------------
| Change Page
|--------------------------------------------------------------------------
*/

const changePage = (
    page
) => {

    if (
        page < 1 ||
        page > pagination.value.last_page
    ) {

        return;

    }


    loadReceipts(page);

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: "",

        payment_method: "",

        date_from: "",

        date_to: "",

        per_page: 20

    };


    loadReceipts(1);

};


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const formatAmount = (
    amount
) => {

    return Number(
        amount || 0
    ).toLocaleString(
        "en-IN",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


const formatDate = (
    date
) => {

    if (!date) {

        return "-";

    }


    const value =
        new Date(date);


    if (
        Number.isNaN(
            value.getTime()
        )
    ) {

        return date;

    }


    return value.toLocaleDateString(
        "en-IN",
        {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        }
    );

};


const statusClass = (
    status
) => {

    if (status === "Success") {

        return "bg-success";

    }


    if (status === "Pending") {

        return "bg-warning text-dark";

    }


    if (status === "Cancelled") {

        return "bg-danger";

    }


    return "bg-secondary";

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadReceipts(1);

});

</script>