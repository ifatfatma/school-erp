<template>
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Payment History
                </h4>

                <small class="text-muted">
                    View and manage collected fee payments
                </small>
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
                            placeholder="Receipt no / Student / Admission no"
                            @keyup.enter="loadPayments(1)"
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

                            <option value="Success">
                                Success
                            </option>

                            <option value="Pending">
                                Pending
                            </option>

                            <option value="Cancelled">
                                Cancelled
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

                </div>


                <!-- Buttons -->
                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadPayments(1)"
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


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>


        <!-- Payment List -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Payment History
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-hover table-bordered mb-0"
                    >

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
                                    Method
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="150"
                                    class="text-center"
                                >
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
                                        Loading payments...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="payments.length === 0"
                            >

                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >
                                    No payment records found.
                                </td>

                            </tr>


                            <!-- Data -->
                            <tr
                                v-for="(payment, index) in payments"
                                v-else
                                :key="payment.id"
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
                                        {{ payment.receipt_no }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        payment.student?.name
                                        || "-"
                                    }}

                                </td>


                                <td>

                                    {{
                                        payment.student?.admission_no
                                        || "-"
                                    }}

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            payment.payment_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <span
                                        class="badge bg-secondary"
                                    >
                                        {{
                                            payment.payment_method
                                        }}
                                    </span>

                                </td>


                                <td class="text-end">

                                    <strong>
                                        ₹{{
                                            formatAmount(
                                                payment.total_amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                payment.status
                                            )
                                        "
                                    >
                                        {{ payment.status }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="View Payment"
                                        @click="
                                            viewPayment(
                                                payment.id
                                            )
                                        "
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>


                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="View Receipt"
                                        @click="
                                            viewReceipt(
                                                payment.id
                                            )
                                        "
                                    >
                                        <i class="bi bi-receipt"></i>
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
                                pagination.current_page <= 1 ||
                                loading
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
                                pagination.current_page >=
                                pagination.last_page ||
                                loading
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


        <!-- Payment Details Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Payment Details
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
                                Loading payment details...
                            </div>

                        </div>


                        <template v-else-if="selectedPayment">

                            <!-- Header Information -->
                            <div class="row g-3 mb-4">

                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Receipt No
                                    </small>

                                    <div class="fw-bold">
                                        {{
                                            selectedPayment.receipt_no
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Student
                                    </small>

                                    <div class="fw-bold">
                                        {{
        selectedPayment.student?.name
        || selectedPayment.student?.student_name
        || selectedPayment.student?.full_name
        || selectedPayment.student?.first_name
        || "-"
    }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Admission No
                                    </small>

                                    <div class="fw-bold">
                                        {{
                                            selectedPayment.student?.admission_no
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
                                                selectedPayment.payment_date
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
                                            selectedPayment.payment_method
                                            || "-"
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
                                                    selectedPayment.status
                                                )
                                            "
                                        >
                                            {{
                                                selectedPayment.status
                                            }}
                                        </span>

                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Transaction No
                                    </small>

                                    <div>
                                        {{
                                            selectedPayment.transaction_no
                                            || "-"
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Bank Name
                                    </small>

                                    <div>
                                        {{
                                            selectedPayment.bank_name
                                            || "-"
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <small class="text-muted">
                                        Cheque No
                                    </small>

                                    <div>
                                        {{
                                            selectedPayment.cheque_no
                                            || "-"
                                        }}
                                    </div>

                                </div>

                            </div>


                            <!-- Payment Items -->
                            <h6 class="mb-2">
                                Payment Items
                            </h6>


                            <div class="table-responsive">

                                <table
                                    class="table table-sm table-bordered"
                                >

                                    <thead class="table-light">

                                        <tr>

                                            <th>
                                                Fee Due
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
                                            v-for="item in (
                                                selectedPayment.items || []
                                            )"
                                            :key="item.id"
                                        >

                                            <td>
                                                #{{ item.student_fee_due_id }}
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


                                        <tr
                                            v-if="
                                                !selectedPayment.items ||
                                                selectedPayment.items.length === 0
                                            "
                                        >

                                            <td
                                                colspan="4"
                                                class="text-center text-muted"
                                            >
                                                No payment items found.
                                            </td>

                                        </tr>

                                    </tbody>


                                    <tfoot>

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
                                                        selectedPayment.total_amount
                                                    )
                                                }}

                                            </th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>


                            <!-- Remarks -->
                            <div
                                v-if="selectedPayment.remarks"
                                class="mt-3"
                            >

                                <strong>
                                    Remarks:
                                </strong>

                                <div class="text-muted">
                                    {{ selectedPayment.remarks }}
                                </div>

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
                            v-if="selectedPayment"
                            type="button"
                            class="btn btn-primary"
                            @click="
                                viewReceipt(
                                    selectedPayment.id
                                )
                            "
                        >

                            <i class="bi bi-receipt me-1"></i>

                            View Receipt

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
| Authentication
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

    /*
     * Try the token names commonly used by the application.
     */
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

            Accept: "application/json",

            "Content-Type": "application/json"

        }

    };


    /*
     * Add Bearer token when available.
     */
    if (token) {

        config.headers.Authorization =
            `Bearer ${token}`;

    }


    return config;
};


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const payments = ref([]);

const loading = ref(false);

const error = ref("");

const showModal = ref(false);

const detailsLoading = ref(false);

const selectedPayment = ref(null);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    payment_method: "",

    status: "",

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
| Load Payments
|--------------------------------------------------------------------------
*/

const loadPayments = async (
    page = 1
) => {

    loading.value = true;

    error.value = "";


    try {

        const params = {

            page: page,

            per_page:
                filters.value.per_page,

            search:
                filters.value.search || undefined,

            payment_method:
                filters.value.payment_method || undefined,

            status:
                filters.value.status || undefined,

            date_from:
                filters.value.date_from || undefined,

            date_to:
                filters.value.date_to || undefined

        };


        const response = await axios.get(
            "/api/payment-history",
            {
                ...getAuthConfig(),

                params: params

            }
        );


        const result =
            response.data?.data;


        payments.value =
            result?.data || [];


        pagination.value = {

            current_page:
                result?.current_page || 1,

            last_page:
                result?.last_page || 1,

            per_page:
                result?.per_page || 20,

            total:
                result?.total || 0,

            from:
                result?.from || 0,

            to:
                result?.to || 0

        };


    } catch (err) {

        console.error(
            "Payment history error:",
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
                "Unable to load payment history.";

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Payment
|--------------------------------------------------------------------------
*/

const viewPayment = async (
    id
) => {

    showModal.value = true;

    detailsLoading.value = true;

    selectedPayment.value = null;

    error.value = "";


    try {

        const response = await axios.get(
            `/api/payment-history/${id}`,
            getAuthConfig()
        );


        selectedPayment.value =
            response.data?.data || null;


    } catch (err) {

        console.error(
            "Payment details error:",
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
                "Unable to load payment details.";

        }


        showModal.value = false;


    } finally {

        detailsLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| View Receipt
|--------------------------------------------------------------------------
*/

const viewReceipt = async (id) => {

    try {

        const config = getAuthConfig();

        const response = await axios.get(
            `/api/fee-collection/${id}/receipt`,
            {
                ...config,
                responseType: "blob"
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Create temporary browser URL
        |--------------------------------------------------------------------------
        */

        const blob = new Blob(
            [response.data],
            {
                type:
                    response.headers["content-type"]
                    || "application/pdf"
            }
        );


        const url =
            window.URL.createObjectURL(blob);


        /*
        |--------------------------------------------------------------------------
        | Open Receipt
        |--------------------------------------------------------------------------
        */

        window.open(
            url,
            "_blank"
        );


        /*
        |--------------------------------------------------------------------------
        | Cleanup
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            window.URL.revokeObjectURL(url);

        }, 60000);


    } catch (err) {

        console.error(
            "Receipt error:",
            err
        );


        if (
            err.response?.status === 401
        ) {

            error.value =
                "Authentication expired. Please login again.";

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Blob Error Response
        |--------------------------------------------------------------------------
        */

        if (
            err.response?.data instanceof Blob
        ) {

            try {

                const text =
                    await err.response.data.text();


                const data =
                    JSON.parse(text);


                error.value =
                    data.message
                    || "Unable to generate receipt.";

            } catch (e) {

                error.value =
                    "Unable to generate receipt.";

            }

        } else {

            error.value =
                err.response?.data?.message
                ||
                "Unable to generate receipt.";

        }

    }

};


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    showModal.value = false;

    selectedPayment.value = null;

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


    loadPayments(page);

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

        status: "",

        date_from: "",

        date_to: "",

        per_page: 20

    };


    loadPayments(1);

};


/*
|--------------------------------------------------------------------------
| Format Amount
|--------------------------------------------------------------------------
*/

const formatAmount = (
    amount
) => {

    const value =
        Number(amount || 0);


    return value.toLocaleString(
        "en-IN",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Status Class
|--------------------------------------------------------------------------
*/

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

    loadPayments(1);

});

</script>