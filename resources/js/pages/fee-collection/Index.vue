<script setup>

import {
    onMounted,
    ref
} from "vue";

import AppBreadcrumb
    from "@/components/ui/AppBreadcrumb.vue";

import api
    from "@/api/axios";


/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

const dues = ref([]);

const selectedDue = ref(null);

const loading = ref(false);

const loadingDue = ref(false);

const saving = ref(false);

const errors = ref({});


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const search = ref("");

const status = ref("");


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pagination = ref({

    current_page: 1,

    last_page: 1,

    per_page: 20,

    total: 0

});


/*
|--------------------------------------------------------------------------
| Collection Form
|--------------------------------------------------------------------------
*/

const form = ref({

    student_fee_due_id: "",

    payment_date: "",

    amount: "",

    discount: 0,

    payment_method: "Cash",

    transaction_no: "",

    remarks: ""

});


/*
|--------------------------------------------------------------------------
| Format Amount
|--------------------------------------------------------------------------
*/

const formatAmount = (amount) => {

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


/*
|--------------------------------------------------------------------------
| Student Name
|--------------------------------------------------------------------------
*/

const studentName = (student) => {

    if (!student) {
        return "-";
    }

    if (student.full_name) {
        return student.full_name;
    }

    const first =
        student.first_name || "";

    const last =
        student.last_name || "";

    const name =
        `${first} ${last}`.trim();

    return name || "-";

};


/*
|--------------------------------------------------------------------------
| Load Dues
|--------------------------------------------------------------------------
*/

const loadDues = async (
    page = 1
) => {

    loading.value = true;

    try {

        const params = {

            page,

            per_page:
                pagination.value.per_page

        };


        if (search.value) {

            params.search =
                search.value;

        }


        if (status.value) {

            params.status =
                status.value;

        }


        const { data } = await api.get(
            "/fee-dues",
            {
                params
            }
        );


        dues.value =
            data.data?.data ||
            [];


        pagination.value = {

            current_page:
                data.data?.current_page || 1,

            last_page:
                data.data?.last_page || 1,

            per_page:
                data.data?.per_page ||
                pagination.value.per_page,

            total:
                data.data?.total || 0

        };

    } catch (error) {

        console.error(
            "Fee collection dues error:",
            error
        );

        alert(
            error.response?.data?.message ||
            "Unable to load fee dues."
        );

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Select Due
|--------------------------------------------------------------------------
*/

const selectDue = (due) => {

    selectedDue.value = due;

    errors.value = {};

    form.value = {

        student_id:
            due.student_id ||
            due.student?.id ||
            "",

        student_fee_due_id:
            due.id,

        payment_date:
            new Date()
                .toISOString()
                .substring(0, 10),

        amount:
            Number(
                due.balance_amount || 0
            ),

        discount: 0,

        payment_method: "Cash",

        transaction_no: "",

        remarks: ""

    };

};


/*
|--------------------------------------------------------------------------
| Close Collection
|--------------------------------------------------------------------------
*/

const closeCollection = () => {

    selectedDue.value = null;

    errors.value = {};

};


/*
|--------------------------------------------------------------------------
| Collect Payment
|--------------------------------------------------------------------------
*/

const collectPayment = async () => {

    errors.value = {};


    if (!form.value.student_fee_due_id) {

        errors.value.student_fee_due_id = [
            "Fee due is required."
        ];

        return;

    }


    if (!form.value.payment_date) {

        errors.value.payment_date = [
            "Payment date is required."
        ];

        return;

    }


    const amount =
        Number(form.value.amount || 0);


    const discount =
        Number(form.value.discount || 0);


    const balance =
        Number(
            selectedDue.value?.balance_amount ||
            0
        );


    if (amount <= 0) {

        errors.value.amount = [
            "Payment amount must be greater than zero."
        ];

        return;

    }


    /*
     * Do not allow payment greater
     * than outstanding balance.
     */

    if (amount > balance) {

        errors.value.amount = [
            `Payment cannot exceed outstanding balance of ₹${formatAmount(balance)}.`
        ];

        return;

    }


    if (
        discount < 0 ||
        discount > balance
    ) {

        errors.value.discount = [
            "Invalid discount amount."
        ];

        return;

    }


    saving.value = true;


    try {

const payload = {

    student_id:
        form.value.student_id,

    payment_date:
        form.value.payment_date,

    amount,

    payment_method:
        form.value.payment_method,

    items: [

        {
            student_fee_due_id:
                form.value.student_fee_due_id,

            amount
        }

    ],

    remarks:
        form.value.remarks ||
        null

};


        const { data } = await api.post(
            "/fee-collection/collect",
            payload
        );


        alert(
            data.message ||
            "Payment collected successfully."
        );


        closeCollection();

        await loadDues(
            pagination.value.current_page
        );

    } catch (error) {

        console.error(
            "Fee collection error:",
            error
        );


        if (
            error.response?.status === 422
        ) {

            errors.value =
                error.response.data.errors ||
                {};


            if (
                error.response.data.message &&
                Object.keys(
                    errors.value
                ).length === 0
            ) {

                alert(
                    error.response.data.message
                );

            }

        } else {

            alert(
                error.response?.data?.message ||
                "Unable to collect payment."
            );

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const searchDues = () => {

    loadDues(1);

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadDues();

});

</script>


<template>

    <AppBreadcrumb
        :items="[
            {
                label: 'Dashboard',
                to: '/'
            },
            {
                label: 'Fee & Accounts'
            },
            {
                label: 'Fee Collection'
            }
        ]"
    />


    <!-- ========================================================= -->
    <!-- HEADER -->
    <!-- ========================================================= -->

    <div
        class="d-flex justify-content-between align-items-center mb-3"
    >

        <div>

            <h3 class="mb-1">
                Fee Collection
            </h3>

            <div class="text-muted">
                Collect student fee payments.
            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- SEARCH -->
    <!-- ========================================================= -->

    <div class="card mb-4">

        <div class="card-body">

            <div class="row align-items-end">

                <div class="col-md-5">

                    <label class="form-label">
                        Search Student
                    </label>

                    <input
                        v-model="search"
                        type="text"
                        class="form-control"
                        placeholder="Student name / admission no"
                        @keyup.enter="searchDues"
                    />

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        v-model="status"
                        class="form-select"
                    >

                        <option value="">
                            Pending & Partial
                        </option>

                        <option value="Pending">
                            Pending
                        </option>

                        <option value="Partial">
                            Partial
                        </option>

                        <option value="Paid">
                            Paid
                        </option>

                    </select>

                </div>


                <div class="col-md-4">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        @click="searchDues"
                    >
                        Search
                    </button>


                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="
                            search = '';
                            status = '';
                            searchDues();
                        "
                    >
                        Reset
                    </button>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- DUES -->
    <!-- ========================================================= -->

    <div class="card">

        <div class="card-header">

            <div
                class="d-flex justify-content-between align-items-center"
            >

                <h5 class="mb-0">
                    Outstanding Fees
                </h5>

                <span class="text-muted">
                    Total: {{ pagination.total }}
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover align-middle mb-0"
                >

                    <thead>

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Admission No
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Section
                            </th>

                            <th>
                                Fee Type
                            </th>

                            <th>
                                Period
                            </th>

                            <th>
                                Net Amount
                            </th>

                            <th>
                                Paid
                            </th>

                            <th>
                                Balance
                            </th>

                            <th>
                                Status
                            </th>

                            <th
                                width="120"
                                class="text-center"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr v-if="loading">

                            <td
                                colspan="12"
                                class="text-center py-5"
                            >

                                <div
                                    class="spinner-border"
                                ></div>

                                <div class="mt-2">
                                    Loading...
                                </div>

                            </td>

                        </tr>


                        <tr
                            v-else-if="
                                !dues.length
                            "
                        >

                            <td
                                colspan="12"
                                class="text-center text-muted py-5"
                            >

                                No outstanding fees found.

                            </td>

                        </tr>


                        <tr
                            v-for="
                                (
                                    due,
                                    index
                                ) in dues
                            "
                            :key="due.id"
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

                                    {{
                                        studentName(
                                            due.student
                                        )
                                    }}

                                </strong>

                            </td>


                            <td>

                                {{
                                    due.student?.admission_no ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    due.class?.name ||
                                    due.student?.class?.name ||
                                    due.class_id ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    due.section?.name ||
                                    due.student?.section?.name ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    due.fee_type?.name ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    due.period ||
                                    "-"
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        due.net_amount
                                    )
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        due.paid_amount
                                    )
                                }}

                            </td>


                            <td>

                                <strong
                                    class="text-danger"
                                >

                                    ₹{{
                                        formatAmount(
                                            due.balance_amount
                                        )
                                    }}

                                </strong>

                            </td>


                            <td>

                                <span
                                    class="badge"
                                    :class="{
                                        'bg-warning text-dark':
                                            due.status === 'Pending',

                                        'bg-info':
                                            due.status === 'Partial',

                                        'bg-success':
                                            due.status === 'Paid',

                                        'bg-danger':
                                            due.status === 'Cancelled'
                                    }"
                                >

                                    {{
                                        due.status ||
                                        "Pending"
                                    }}

                                </span>

                            </td>


                            <td
                                class="text-center"
                            >

                                <button
                                    type="button"
                                    class="btn btn-sm btn-success"
                                    :disabled="
                                        due.status === 'Paid' ||
                                        due.status === 'Cancelled' ||
                                        Number(
                                            due.balance_amount || 0
                                        ) <= 0
                                    "
                                    @click="
                                        selectDue(due)
                                    "
                                >

                                    Collect

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

                <div class="text-muted">

                    Page
                    {{ pagination.current_page }}
                    of
                    {{ pagination.last_page }}

                </div>


                <div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary me-2"
                        :disabled="
                            pagination.current_page <= 1
                        "
                        @click="
                            loadDues(
                                pagination.current_page - 1
                            )
                        "
                    >
                        Previous
                    </button>


                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        :disabled="
                            pagination.current_page >=
                            pagination.last_page
                        "
                        @click="
                            loadDues(
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


    <!-- ========================================================= -->
    <!-- COLLECTION MODAL -->
    <!-- ========================================================= -->

    <div
        v-if="selectedDue"
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
                        Collect Fee
                    </h5>


                    <button
                        type="button"
                        class="btn-close"
                        @click="
                            closeCollection
                        "
                    ></button>

                </div>


                <div class="modal-body">

                    <!-- Student -->

                    <div
                        class="alert alert-light border"
                    >

                        <div class="row">

                            <div class="col-md-6">

                                <strong>
                                    Student
                                </strong>

                                <div>

                                    {{
                                        studentName(
                                            selectedDue.student
                                        )
                                    }}

                                </div>

                            </div>


                            <div class="col-md-3">

                                <strong>
                                    Fee
                                </strong>

                                <div>

                                    {{
                                        selectedDue.fee_type?.name ||
                                        "-"
                                    }}

                                </div>

                            </div>


                            <div class="col-md-3">

                                <strong>
                                    Period
                                </strong>

                                <div>

                                    {{
                                        selectedDue.period ||
                                        "-"
                                    }}

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Amount Summary -->

                    <div class="row mb-3">

                        <div class="col-md-4">

                            <div class="border rounded p-3">

                                <small
                                    class="text-muted"
                                >
                                    Net Amount
                                </small>

                                <h5 class="mb-0">

                                    ₹{{
                                        formatAmount(
                                            selectedDue.net_amount
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded p-3">

                                <small
                                    class="text-muted"
                                >
                                    Paid
                                </small>

                                <h5
                                    class="mb-0 text-success"
                                >

                                    ₹{{
                                        formatAmount(
                                            selectedDue.paid_amount
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="border rounded p-3">

                                <small
                                    class="text-muted"
                                >
                                    Balance
                                </small>

                                <h5
                                    class="mb-0 text-danger"
                                >

                                    ₹{{
                                        formatAmount(
                                            selectedDue.balance_amount
                                        )
                                    }}

                                </h5>

                            </div>

                        </div>

                    </div>


                    <div
                        v-if="loadingDue"
                        class="text-center mb-3"
                    >

                        <div
                            class="spinner-border spinner-border-sm"
                        ></div>

                        Loading latest balance...

                    </div>


                    <div class="row">

                        <!-- Payment Date -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Payment Date

                                <span
                                    class="text-danger"
                                >
                                    *
                                </span>

                            </label>


                            <input
                                v-model="
                                    form.payment_date
                                "
                                type="date"
                                class="form-control"
                            />


                            <div
                                v-if="
                                    errors.payment_date
                                "
                                class="text-danger small"
                            >

                                {{
                                    errors.payment_date[0]
                                }}

                            </div>

                        </div>


                        <!-- Amount -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Payment Amount

                                <span
                                    class="text-danger"
                                >
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
                            />


                            <small
                                class="text-muted"
                            >

                                Maximum:

                                ₹{{
                                    formatAmount(
                                        selectedDue.balance_amount
                                    )
                                }}

                            </small>


                            <div
                                v-if="errors.amount"
                                class="text-danger small"
                            >

                                {{ errors.amount[0] }}

                            </div>

                        </div>


                        <!-- Discount -->

                        <div class="col-md-6 mb-3">

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
                            />


                            <div
                                v-if="errors.discount"
                                class="text-danger small"
                            >

                                {{ errors.discount[0] }}

                            </div>

                        </div>


                        <!-- Payment Method -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Payment Method

                                <span
                                    class="text-danger"
                                >
                                    *
                                </span>

                            </label>


                           <select
    v-model="form.payment_method"
    class="form-select"
>

    <option value="Cash">
        Cash
    </option>

    <option value="Card">
        Card
    </option>

    <option value="UPI">
        UPI
    </option>

    <option value="Bank Transfer">
        Bank Transfer
    </option>

    <option value="Cheque">
        Cheque
    </option>

</select>

                        </div>


                        <!-- Transaction -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Transaction / Reference No.
                            </label>


                            <input
                                v-model="
                                    form.transaction_no
                                "
                                type="text"
                                class="form-control"
                                placeholder="Optional"
                            />

                        </div>


                        <!-- Remarks -->

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Remarks
                            </label>


                            <input
                                v-model="
                                    form.remarks
                                "
                                type="text"
                                class="form-control"
                                placeholder="Optional"
                            />

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        :disabled="saving"
                        @click="
                            closeCollection
                        "
                    >

                        Cancel

                    </button>


                    <button
                        type="button"
                        class="btn btn-success"
                        :disabled="saving"
                        @click="
                            collectPayment
                        "
                    >

                        <span
                            v-if="saving"
                            class="spinner-border spinner-border-sm me-1"
                        ></span>

                        {{
                            saving
                                ? 'Processing...'
                                : 'Collect Payment'
                        }}

                    </button>

                </div>

            </div>

        </div>

    </div>

</template>