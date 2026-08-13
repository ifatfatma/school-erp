<script setup>

import {
    onMounted,
    ref,
    watch
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

const sessions = ref([]);

const classes = ref([]);

const sections = ref([]);

const feeTypes = ref([]);

const feeStructures = ref([]);

const dues = ref([]);


/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const loadingSections = ref(false);

const loadingSummary = ref(false);


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    academic_session_id: "",

    class_id: "",

    section_id: "",

    fee_type_id: "",

    fee_structure_id: "",

    period: "",

    status: "",

    search: "",

});


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const summary = ref({

    total_amount: 0,

    total_discount: 0,

    total_late_fee: 0,

    total_net_amount: 0,

    total_paid: 0,

    total_balance: 0,

    pending: 0,

    partial: 0,

    paid: 0,

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

});


/*
|--------------------------------------------------------------------------
| Load Sessions
|--------------------------------------------------------------------------
*/

const loadSessions = async () => {

    try {

        const { data } = await api.get(
            "/academic-sessions/active"
        );

        sessions.value =
            data.data || [];

    } catch (error) {

        console.error(
            "Sessions load error:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Classes
|--------------------------------------------------------------------------
*/

const loadClasses = async () => {

    try {

        const { data } = await api.get(
            "/classes/active"
        );

        classes.value =
            data.data || [];

    } catch (error) {

        console.error(
            "Classes load error:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Fee Types
|--------------------------------------------------------------------------
*/

const loadFeeTypes = async () => {

    try {

        const { data } = await api.get(
            "/fee-types",
            {
                params: {
                    per_page: 1000
                }
            }
        );

        feeTypes.value =
            data.data?.data ||
            data.data ||
            [];

    } catch (error) {

        console.error(
            "Fee types load error:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Sections
|--------------------------------------------------------------------------
*/

const loadSections = async () => {

    sections.value = [];

    filters.value.section_id = "";

    if (!filters.value.class_id) {
        return;
    }

    loadingSections.value = true;

    try {

        const { data } = await api.get(
            "/sections",
            {
                params: {
                    class_id:
                        filters.value.class_id
                }
            }
        );

        sections.value =
            data.data?.data ||
            data.data ||
            [];

    } catch (error) {

        console.error(
            "Sections load error:",
            error
        );

    } finally {

        loadingSections.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Fee Structures
|--------------------------------------------------------------------------
*/

const loadFeeStructures = async () => {

    feeStructures.value = [];

    filters.value.fee_structure_id = "";

    const params = {
        per_page: 1000,
        status: 1
    };


    if (filters.value.academic_session_id) {

        params.academic_session_id =
            filters.value.academic_session_id;

    }


    if (filters.value.class_id) {

        params.class_id =
            filters.value.class_id;

    }


    if (filters.value.fee_type_id) {

        params.fee_type_id =
            filters.value.fee_type_id;

    }


    try {

        const { data } = await api.get(
            "/fee-structures",
            {
                params
            }
        );

        feeStructures.value =
            data.data?.data ||
            data.data ||
            [];

    } catch (error) {

        console.error(
            "Fee structures load error:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Summary
|--------------------------------------------------------------------------
*/

const loadSummary = async () => {

    loadingSummary.value = true;

    try {

        const params = {};

        if (
            filters.value.academic_session_id
        ) {

            params.academic_session_id =
                filters.value.academic_session_id;

        }


        if (filters.value.class_id) {

            params.class_id =
                filters.value.class_id;

        }


        if (filters.value.section_id) {

            params.section_id =
                filters.value.section_id;

        }


        const { data } = await api.get(
            "/fee-dues/summary",
            {
                params
            }
        );


        summary.value = {

            total_amount:
                data.data?.total_amount || 0,

            total_discount:
                data.data?.total_discount || 0,

            total_late_fee:
                data.data?.total_late_fee || 0,

            total_net_amount:
                data.data?.total_net_amount || 0,

            total_paid:
                data.data?.total_paid || 0,

            total_balance:
                data.data?.total_balance || 0,

            pending:
                data.data?.pending || 0,

            partial:
                data.data?.partial || 0,

            paid:
                data.data?.paid || 0

        };

    } catch (error) {

        console.error(
            "Fee dues summary error:",
            error
        );

    } finally {

        loadingSummary.value = false;

    }

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


        if (
            filters.value.academic_session_id
        ) {

            params.academic_session_id =
                filters.value.academic_session_id;

        }


        if (filters.value.class_id) {

            params.class_id =
                filters.value.class_id;

        }


        if (filters.value.section_id) {

            params.section_id =
                filters.value.section_id;

        }


        if (filters.value.fee_type_id) {

            params.fee_type_id =
                filters.value.fee_type_id;

        }


        if (filters.value.fee_structure_id) {

            params.fee_structure_id =
                filters.value.fee_structure_id;

        }


        if (filters.value.period) {

            params.period =
                filters.value.period;

        }


        if (filters.value.status) {

            params.status =
                filters.value.status;

        }


        if (filters.value.search) {

            params.search =
                filters.value.search;

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
            "Fee dues load error:",
            error
        );

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const search = async () => {

    await loadDues(1);

    await loadSummary();

};


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const reset = async () => {

    filters.value = {

        academic_session_id: "",

        class_id: "",

        section_id: "",

        fee_type_id: "",

        fee_structure_id: "",

        period: "",

        status: "",

        search: "",

    };


    feeStructures.value = [];

    sections.value = [];


    await loadDues(1);

    await loadSummary();

};


/*
|--------------------------------------------------------------------------
| View Due
|--------------------------------------------------------------------------
*/

const viewDue = (
    id
) => {

    /*
     * Keep this ready for the Fee Due
     * details screen.
     */

    console.log(
        "View fee due:",
        id
    );

};


/*
|--------------------------------------------------------------------------
| Collect Fee
|--------------------------------------------------------------------------
*/

const collectFee = (
    due
) => {

    /*
     * This will connect to Fee Collection.
     */

    console.log(
        "Collect fee:",
        due
    );

};


/*
|--------------------------------------------------------------------------
| Formatting
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


const studentName = (
    student
) => {

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


const statusClass = (
    status
) => {

    switch (status) {

        case "Paid":
            return "bg-success";

        case "Partial":
            return "bg-info";

        case "Cancelled":
            return "bg-danger";

        case "Pending":
        default:
            return "bg-warning text-dark";

    }

};


/*
|--------------------------------------------------------------------------
| Watch Class
|--------------------------------------------------------------------------
*/

watch(
    () => filters.value.class_id,
    async () => {

        await loadSections();

        await loadFeeStructures();

    }
);


/*
|--------------------------------------------------------------------------
| Watch Academic Session
|--------------------------------------------------------------------------
*/

watch(
    () => filters.value.academic_session_id,
    async () => {

        await loadFeeStructures();

    }
);


/*
|--------------------------------------------------------------------------
| Watch Fee Type
|--------------------------------------------------------------------------
*/

watch(
    () => filters.value.fee_type_id,
    async () => {

        await loadFeeStructures();

    }
);


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await Promise.all([

        loadSessions(),

        loadClasses(),

        loadFeeTypes(),

    ]);


    await loadDues();

    await loadSummary();

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
                label: 'Fee Dues'
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
                Fee Dues
            </h3>

            <div class="text-muted">
                View and manage student fee dues.
            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- SUMMARY -->
    <!-- ========================================================= -->

    <div class="row mb-4">

        <div class="col-md-3 mb-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Total Fees
                    </div>

                    <h4 class="mb-0">

                        ₹{{
                            formatAmount(
                                summary.total_net_amount
                            )
                        }}

                    </h4>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Collected
                    </div>

                    <h4 class="text-success mb-0">

                        ₹{{
                            formatAmount(
                                summary.total_paid
                            )
                        }}

                    </h4>

                    <small class="text-muted">

                        {{ summary.paid }} Paid

                    </small>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Outstanding
                    </div>

                    <h4 class="text-danger mb-0">

                        ₹{{
                            formatAmount(
                                summary.total_balance
                            )
                        }}

                    </h4>

                </div>

            </div>

        </div>


        <div class="col-md-3 mb-3">

            <div class="card h-100">

                <div class="card-body">

                    <div class="text-muted">
                        Pending Dues
                    </div>

                    <h4 class="mb-0">

                        {{ summary.pending }}

                    </h4>

                    <small class="text-muted">

                        {{ summary.partial }}
                        Partial

                    </small>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- FILTERS -->
    <!-- ========================================================= -->

    <div class="card mb-4">

        <div class="card-header">

            <h5 class="mb-0">
                Filter Fee Dues
            </h5>

        </div>


        <div class="card-body">

            <div class="row">

                <!-- Academic Session -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Academic Session
                    </label>

                    <select
                        v-model="
                            filters.academic_session_id
                        "
                        class="form-select"
                    >

                        <option value="">
                            All Sessions
                        </option>

                        <option
                            v-for="
                                session in sessions
                            "
                            :key="session.id"
                            :value="session.id"
                        >

                            {{
                                session.name ||
                                session.session_year ||
                                session.year ||
                                "-"
                            }}

                        </option>

                    </select>

                </div>


                <!-- Class -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Class
                    </label>

                    <select
                        v-model="
                            filters.class_id
                        "
                        class="form-select"
                    >

                        <option value="">
                            All Classes
                        </option>

                        <option
                            v-for="
                                item in classes
                            "
                            :key="item.id"
                            :value="item.id"
                        >

                            {{ item.name }}

                        </option>

                    </select>

                </div>


                <!-- Section -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Section
                    </label>

                    <select
                        v-model="
                            filters.section_id
                        "
                        class="form-select"
                        :disabled="
                            !filters.class_id ||
                            loadingSections
                        "
                    >

                        <option value="">

                            {{
                                loadingSections
                                    ? 'Loading...'
                                    : 'All Sections'
                            }}

                        </option>

                        <option
                            v-for="
                                section in sections
                            "
                            :key="section.id"
                            :value="section.id"
                        >

                            {{ section.name }}

                        </option>

                    </select>

                </div>


                <!-- Fee Type -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Fee Type
                    </label>

                    <select
                        v-model="
                            filters.fee_type_id
                        "
                        class="form-select"
                    >

                        <option value="">
                            All Fee Types
                        </option>

                        <option
                            v-for="
                                type in feeTypes
                            "
                            :key="type.id"
                            :value="type.id"
                        >

                            {{ type.name }}

                        </option>

                    </select>

                </div>


                <!-- Fee Structure -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Fee Structure
                    </label>

                    <select
                        v-model="
                            filters.fee_structure_id
                        "
                        class="form-select"
                    >

                        <option value="">
                            All Fee Structures
                        </option>

                        <option
                            v-for="
                                structure in feeStructures
                            "
                            :key="structure.id"
                            :value="structure.id"
                        >

                            {{
                                structure.fee_type?.name ||
                                'Fee'
                            }}

                            -

                            ₹{{
                                formatAmount(
                                    structure.amount
                                )
                            }}

                        </option>

                    </select>

                </div>


                <!-- Period -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Period
                    </label>

                    <input
                        v-model="
                            filters.period
                        "
                        type="text"
                        class="form-control"
                        placeholder="August 2026"
                        @keyup.enter="search"
                    />

                </div>


                <!-- Status -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        v-model="
                            filters.status
                        "
                        class="form-select"
                    >

                        <option value="">
                            All Status
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

                        <option value="Cancelled">
                            Cancelled
                        </option>

                    </select>

                </div>


                <!-- Search -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Student Search
                    </label>

                    <input
                        v-model="
                            filters.search
                        "
                        type="text"
                        class="form-control"
                        placeholder="Name / Admission No"
                        @keyup.enter="search"
                    />

                </div>

            </div>


            <div>

                <button
                    type="button"
                    class="btn btn-primary me-2"
                    @click="search"
                >

                    Search

                </button>


                <button
                    type="button"
                    class="btn btn-secondary"
                    @click="reset"
                >

                    Reset

                </button>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- DUES TABLE -->
    <!-- ========================================================= -->

    <div class="card">

        <div class="card-header">

            <div
                class="d-flex justify-content-between align-items-center"
            >

                <h5 class="mb-0">
                    Student Fee Dues
                </h5>

                <span class="text-muted">

                    Total:
                    {{ pagination.total }}

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

                            <th width="55">
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
                                Due Date
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Discount
                            </th>

                            <th>
                                Late Fee
                            </th>

                            <th>
                                Net
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
                                colspan="16"
                                class="text-center py-5"
                            >

                                <div
                                    class="spinner-border"
                                ></div>

                                <div class="mt-2 text-muted">
                                    Loading fee dues...
                                </div>

                            </td>

                        </tr>


                        <!-- Empty -->

                        <tr
                            v-else-if="
                                !dues.length
                            "
                        >

                            <td
                                colspan="16"
                                class="text-center text-muted py-5"
                            >

                                No fee dues found.

                            </td>

                        </tr>


                        <!-- Rows -->

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

                                {{
                                    due.due_date ||
                                    "-"
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        due.amount
                                    )
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        due.discount
                                    )
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        due.late_fee
                                    )
                                }}

                            </td>


                            <td>

                                <strong>

                                    ₹{{
                                        formatAmount(
                                            due.net_amount
                                        )
                                    }}

                                </strong>

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
                                    :class="
                                        statusClass(
                                            due.status
                                        )
                                    "
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
                                    class="btn btn-sm btn-outline-primary me-1"
                                    @click="
                                        viewDue(
                                            due.id
                                        )
                                    "
                                >

                                    View

                                </button>


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
                                        collectFee(due)
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


        <!-- ===================================================== -->
        <!-- PAGINATION -->
        <!-- ===================================================== -->

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

                    Showing page
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


                    <span>

                        Page
                        {{ pagination.current_page }}
                        of
                        {{ pagination.last_page }}

                    </span>


                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary ms-2"
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

</template>