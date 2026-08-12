<script setup>

import {
    computed,
    onMounted,
    ref,
    watch
} from "vue";

import AppBreadcrumb
    from "@/components/ui/AppBreadcrumb.vue";

import AppButton
    from "@/components/ui/AppButton.vue";

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

const feeStructures = ref([]);

const students = ref([]);

const assignments = ref([]);


/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const loadingStudents = ref(false);

const loadingSections = ref(false);

const loadingStructures = ref(false);

const assigning = ref(false);


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({

    academic_session_id: "",

    class_id: "",

    section_id: "",

    fee_structure_id: "",

    fee_date: "",

    due_date: "",

    period: "",

    student_ids: [],

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    academic_session_id: "",

    class_id: "",

    section_id: "",

    fee_structure_id: "",

    period: "",

});


/*
|--------------------------------------------------------------------------
| Errors
|--------------------------------------------------------------------------
*/

const errors = ref({});


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
| Selected Fee Structure
|--------------------------------------------------------------------------
*/

const selectedStructure = computed(() => {

    if (!form.value.fee_structure_id) {
        return null;
    }

    return feeStructures.value.find(
        item =>
            Number(item.id) ===
            Number(form.value.fee_structure_id)
    ) || null;

});


/*
|--------------------------------------------------------------------------
| All Students Selected
|--------------------------------------------------------------------------
*/

const allSelected = computed(() => {

    if (!students.value.length) {
        return false;
    }

    return students.value.every(
        student =>
            form.value.student_ids.includes(
                student.id
            )
    );

});


/*
|--------------------------------------------------------------------------
| Format Amount
|--------------------------------------------------------------------------
*/

const formatAmount = (amount) => {

    return Number(amount || 0).toLocaleString(
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

    const firstName =
        student.first_name || "";

    const lastName =
        student.last_name || "";

    const name =
        `${firstName} ${lastName}`.trim();

    return name || "-";

};


/*
|--------------------------------------------------------------------------
| Academic Session Name
|--------------------------------------------------------------------------
*/

const sessionName = (session) => {

    if (!session) {
        return "-";
    }

    if (session.name) {
        return session.name;
    }

    if (session.session_year) {
        return session.session_year;
    }

    if (session.year) {
        return session.year;
    }

    return "-";

};


/*
|--------------------------------------------------------------------------
| Load Academic Sessions
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
            "Academic sessions load error:",
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
| Load Sections
|--------------------------------------------------------------------------
*/

const loadSections = async () => {

    sections.value = [];

    form.value.section_id = "";

    if (!form.value.class_id) {
        return;
    }

    loadingSections.value = true;

    try {

        const { data } = await api.get(
            "/sections",
            {
                params: {
                    class_id: form.value.class_id
                }
            }
        );

        sections.value =
            data.data?.data ||
            data.data ||
            [];

        console.log(
            "Sections loaded:",
            sections.value
        );

    } catch (error) {

        console.error(
            "Sections load error:",
            error
        );

        sections.value = [];

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

    form.value.fee_structure_id = "";

    if (
        !form.value.academic_session_id ||
        !form.value.class_id
    ) {
        return;
    }

    loadingStructures.value = true;

    try {

        const { data } = await api.get(
            "/fee-structures",
            {
                params: {

                    academic_session_id:
                        form.value.academic_session_id,

                    class_id:
                        form.value.class_id,

                    status: 1,

                    per_page: 1000

                }
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

    } finally {

        loadingStructures.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Students
|--------------------------------------------------------------------------
*/

const loadStudents = async () => {

    students.value = [];

    form.value.student_ids = [];

    if (
        !form.value.academic_session_id ||
        !form.value.class_id
    ) {
        return;
    }

    loadingStudents.value = true;

    try {

        const params = {

            academic_session_id:
                form.value.academic_session_id,

            class_id:
                form.value.class_id

        };


        if (form.value.section_id) {

            params.section_id =
                form.value.section_id;

        }


        const { data } = await api.get(
            "/fee-assignments/students",
            {
                params
            }
        );


        students.value =
            data.data || [];

    } catch (error) {

        console.error(
            "Students load error:",
            error
        );

    } finally {

        loadingStudents.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Assignments
|--------------------------------------------------------------------------
*/

const loadAssignments = async (
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


        if (filters.value.fee_structure_id) {

            params.fee_structure_id =
                filters.value.fee_structure_id;

        }


        if (filters.value.period) {

            params.period =
                filters.value.period;

        }


        const { data } = await api.get(
            "/fee-assignments",
            {
                params
            }
        );


        assignments.value =
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
            "Fee assignments load error:",
            error
        );

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Select / Unselect Student
|--------------------------------------------------------------------------
*/

const toggleStudent = (
    studentId
) => {

    const index =
        form.value.student_ids.indexOf(
            studentId
        );


    if (index === -1) {

        form.value.student_ids.push(
            studentId
        );

    } else {

        form.value.student_ids.splice(
            index,
            1
        );

    }

};


/*
|--------------------------------------------------------------------------
| Select All
|--------------------------------------------------------------------------
*/

const toggleAll = () => {

    if (allSelected.value) {

        form.value.student_ids = [];

        return;

    }


    form.value.student_ids =
        students.value.map(
            student => student.id
        );

};


/*
|--------------------------------------------------------------------------
| Assign Fee
|--------------------------------------------------------------------------
*/

const assignFee = async () => {

    errors.value = {};


    if (
        !form.value.academic_session_id
    ) {

        errors.value.academic_session_id = [
            "Academic session is required."
        ];

        return;

    }


    if (!form.value.class_id) {

        errors.value.class_id = [
            "Class is required."
        ];

        return;

    }


    if (!form.value.fee_structure_id) {

        errors.value.fee_structure_id = [
            "Fee structure is required."
        ];

        return;

    }


    if (!form.value.fee_date) {

        errors.value.fee_date = [
            "Fee date is required."
        ];

        return;

    }


    if (!form.value.period) {

        errors.value.period = [
            "Fee period is required."
        ];

        return;

    }


    if (!form.value.student_ids.length) {

        errors.value.student_ids = [
            "Please select at least one student."
        ];

        return;

    }


    assigning.value = true;


    try {

        const { data } = await api.post(
            "/fee-assignments/assign",
            form.value
        );


        alert(
            data.message ||
            "Fee assigned successfully."
        );


        form.value.student_ids = [];


        await loadStudents();

        await loadAssignments(1);

    } catch (error) {

        console.error(
            "Fee assignment error:",
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
                "Unable to assign fee."
            );

        }

    } finally {

        assigning.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Delete Assignment
|--------------------------------------------------------------------------
*/

const removeAssignment = async (
    item
) => {

    const name =
        studentName(item.student);


    if (
        !confirm(
            `Delete fee assignment for ${name}?`
        )
    ) {

        return;

    }


    try {

        await api.delete(
            `/fee-assignments/${item.id}`
        );


        await loadAssignments(
            pagination.value.current_page
        );

    } catch (error) {

        console.error(
            "Fee assignment delete error:",
            error
        );


        alert(
            error.response?.data?.message ||
            "Unable to delete fee assignment."
        );

    }

};


/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

const resetForm = () => {

    form.value = {

        academic_session_id: "",

        class_id: "",

        section_id: "",

        fee_structure_id: "",

        fee_date: "",

        due_date: "",

        period: "",

        student_ids: []

    };


    sections.value = [];

    feeStructures.value = [];

    students.value = [];

    errors.value = {};

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        academic_session_id: "",

        class_id: "",

        section_id: "",

        fee_structure_id: "",

        period: ""

    };


    loadAssignments(1);

};


/*
|--------------------------------------------------------------------------
| Watch Academic Session
|--------------------------------------------------------------------------
*/

watch(
    () => form.value.academic_session_id,
    async () => {

        await loadFeeStructures();

        await loadStudents();

    }
);


/*
|--------------------------------------------------------------------------
| Watch Class
|--------------------------------------------------------------------------
*/

watch(
    () => form.value.class_id,
    async () => {

        await loadSections();

        await loadFeeStructures();

        await loadStudents();

    }
);


/*
|--------------------------------------------------------------------------
| Watch Section
|--------------------------------------------------------------------------
*/

watch(
    () => form.value.section_id,
    async () => {

        await loadStudents();

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
        loadClasses()
    ]);


    await loadAssignments();

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
                label: 'Fee Assignments'
            }
        ]"
    />


    <!-- ========================================================= -->
    <!-- ASSIGN FEE -->
    <!-- ========================================================= -->

    <div class="card mb-4">

        <div class="card-header">

            <h4 class="mb-0">
                Assign Fee
            </h4>

        </div>


        <div class="card-body">

            <div class="row">

                <!-- Academic Session -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Academic Session

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <select
                        v-model="
                            form.academic_session_id
                        "
                        class="form-select"
                    >

                        <option value="">
                            Select Academic Session
                        </option>


                        <option
                            v-for="
                                session in sessions
                            "
                            :key="session.id"
                            :value="session.id"
                        >

                            {{ sessionName(session) }}

                        </option>

                    </select>


                    <div
                        v-if="
                            errors.academic_session_id
                        "
                        class="text-danger small mt-1"
                    >

                        {{
                            errors.academic_session_id[0]
                        }}

                    </div>

                </div>


                <!-- Class -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Class

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <select
                        v-model="form.class_id"
                        class="form-select"
                    >

                        <option value="">
                            Select Class
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


                    <div
                        v-if="errors.class_id"
                        class="text-danger small mt-1"
                    >

                        {{ errors.class_id[0] }}

                    </div>

                </div>


                <!-- Section -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Section
                    </label>


                    <select
                        v-model="form.section_id"
                        class="form-select"
                        :disabled="
                            !form.class_id ||
                            loadingSections
                        "
                    >

                        <option value="">

                            {{
                                loadingSections
                                    ? 'Loading sections...'
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


                <!-- Fee Structure -->

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Fee Structure

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <select
                        v-model="
                            form.fee_structure_id
                        "
                        class="form-select"
                        :disabled="
                            !form.class_id ||
                            loadingStructures
                        "
                    >

                        <option value="">

                            {{
                                loadingStructures
                                    ? 'Loading fee structures...'
                                    : 'Select Fee Structure'
                            }}

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


                    <div
                        v-if="
                            errors.fee_structure_id
                        "
                        class="text-danger small mt-1"
                    >

                        {{
                            errors.fee_structure_id[0]
                        }}

                    </div>

                </div>


                <!-- Period -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">

                        Fee Period

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <input
                        v-model="form.period"
                        type="text"
                        class="form-control"
                        placeholder="August 2026"
                    />


                    <div
                        v-if="errors.period"
                        class="text-danger small mt-1"
                    >

                        {{ errors.period[0] }}

                    </div>

                </div>


                <!-- Fee Date -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">

                        Fee Date

                        <span class="text-danger">
                            *
                        </span>

                    </label>


                    <input
                        v-model="form.fee_date"
                        type="date"
                        class="form-control"
                    />


                    <div
                        v-if="errors.fee_date"
                        class="text-danger small mt-1"
                    >

                        {{ errors.fee_date[0] }}

                    </div>

                </div>


                <!-- Due Date -->

                <div class="col-md-3 mb-3">

                    <label class="form-label">
                        Due Date
                    </label>


                    <input
                        v-model="form.due_date"
                        type="date"
                        class="form-control"
                    />

                </div>

            </div>


            <!-- Selected Structure -->

            <div
                v-if="selectedStructure"
                class="alert alert-info py-2"
            >

                <strong>

                    {{
                        selectedStructure.fee_type?.name ||
                        'Fee'
                    }}

                </strong>


                <span class="mx-2">
                    |
                </span>


                Amount:

                <strong>

                    ₹{{
                        formatAmount(
                            selectedStructure.amount
                        )
                    }}

                </strong>


                <template
                    v-if="
                        Number(
                            selectedStructure.late_fee || 0
                        ) > 0
                    "
                >

                    <span class="mx-2">
                        |
                    </span>


                    Late Fee:

                    <strong>

                        ₹{{
                            formatAmount(
                                selectedStructure.late_fee
                            )
                        }}

                    </strong>

                </template>

            </div>


            <!-- Students -->

            <div class="border rounded">

                <div
                    class="p-3 bg-light d-flex justify-content-between align-items-center"
                >

                    <div>

                        <strong>
                            Students
                        </strong>


                        <span
                            v-if="students.length"
                            class="text-muted ms-2"
                        >

                            ({{ students.length }})

                        </span>

                    </div>


                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        :disabled="
                            !students.length ||
                            loadingStudents
                        "
                        @click="toggleAll"
                    >

                        {{
                            allSelected
                                ? 'Unselect All'
                                : 'Select All'
                        }}

                    </button>

                </div>


                <!-- Loading -->

                <div
                    v-if="loadingStudents"
                    class="text-center py-4"
                >

                    <div
                        class="spinner-border spinner-border-sm"
                    ></div>


                    <span class="ms-2">
                        Loading students...
                    </span>

                </div>


                <!-- Empty -->

                <div
                    v-else-if="!students.length"
                    class="text-center text-muted py-4"
                >

                    Select academic session and
                    class to load students.

                </div>


                <!-- Student Table -->

                <div
                    v-else
                    class="table-responsive"
                >

                    <table
                        class="table table-bordered mb-0"
                    >

                        <thead>

                            <tr>

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Roll No
                                </th>

                                <th>
                                    Admission No
                                </th>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Section
                                </th>

                                <th
                                    width="80"
                                    class="text-center"
                                >
                                    Select
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="
                                    (
                                        student,
                                        index
                                    ) in students
                                "
                                :key="student.id"
                            >

                                <td>
                                    {{ index + 1 }}
                                </td>


                                <td>
                                    {{
                                        student.roll_no ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    {{
                                        student.admission_no ||
                                        "-"
                                    }}
                                </td>


                                <td>
                                    {{ studentName(student) }}
                                </td>


                                <td>

                                    <template
                                        v-if="
                                            student.section?.name
                                        "
                                    >

                                        {{
                                            student.section.name
                                        }}

                                    </template>


                                    <template
                                        v-else
                                    >

                                        {{
                                            sections.find(
                                                section =>
                                                    Number(
                                                        section.id
                                                    ) ===
                                                    Number(
                                                        student.section_id
                                                    )
                                            )?.name ||
                                            "-"
                                        }}

                                    </template>

                                </td>


                                <td
                                    class="text-center"
                                >

                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        :checked="
                                            form.student_ids.includes(
                                                student.id
                                            )
                                        "
                                        @change="
                                            toggleStudent(
                                                student.id
                                            )
                                        "
                                    />

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <div
                v-if="errors.student_ids"
                class="text-danger small mt-1"
            >

                {{ errors.student_ids[0] }}

            </div>


            <!-- Buttons -->

            <div class="text-end mt-4">

                <button
                    type="button"
                    class="btn btn-secondary me-2"
                    @click="resetForm"
                >

                    Reset

                </button>


                <AppButton
                    text="Assign Fee"
                    :loading="assigning"
                    @click="assignFee"
                />

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- EXISTING ASSIGNMENTS -->
    <!-- ========================================================= -->

    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Fee Assignments
            </h4>

        </div>


        <div class="card-body">

            <!-- Filters -->

            <div class="row mb-3">

                <div class="col-md-3">

                    <input
                        v-model="
                            filters.period
                        "
                        type="text"
                        class="form-control"
                        placeholder="Search Period"
                        @keyup.enter="
                            loadAssignments(1)
                        "
                    />

                </div>


                <div class="col-md-auto">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        @click="
                            loadAssignments(1)
                        "
                    >
                        Search
                    </button>


                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="resetFilters"
                    >
                        Reset
                    </button>

                </div>

            </div>


            <!-- Table -->

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover align-middle"
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
                                Fee Type
                            </th>

                            <th>
                                Period
                            </th>

                            <th>
                                Amount
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
                                width="100"
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
                                colspan="10"
                                class="text-center py-5"
                            >

                                <div
                                    class="spinner-border"
                                ></div>


                                <div class="mt-2 text-muted">
                                    Loading...
                                </div>

                            </td>

                        </tr>


                        <!-- Empty -->

                        <tr
                            v-else-if="
                                !assignments.length
                            "
                        >

                            <td
                                colspan="10"
                                class="text-center text-muted py-5"
                            >

                                No fee assignments found.

                            </td>

                        </tr>


                        <!-- Rows -->

                        <tr
                            v-for="
                                (
                                    item,
                                    index
                                ) in assignments
                            "
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

                                {{ studentName(item.student) }}

                            </td>


                            <td>

                                {{
                                    item.student?.admission_no ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    item.fee_type?.name ||
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    item.period ||
                                    "-"
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        item.net_amount
                                    )
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        item.paid_amount
                                    )
                                }}

                            </td>


                            <td>

                                ₹{{
                                    formatAmount(
                                        item.balance_amount
                                    )
                                }}

                            </td>


                            <td>

                                <span
                                    class="badge"
                                    :class="{
                                        'bg-warning text-dark':
                                            item.status === 'Pending',

                                        'bg-info':
                                            item.status === 'Partial',

                                        'bg-success':
                                            item.status === 'Paid',

                                        'bg-danger':
                                            item.status === 'Cancelled'
                                    }"
                                >

                                    {{
                                        item.status ||
                                        "Pending"
                                    }}

                                </span>

                            </td>


                            <td
                                class="text-center"
                            >

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    :disabled="
                                        Number(
                                            item.paid_amount || 0
                                        ) > 0
                                    "
                                    @click="
                                        removeAssignment(
                                            item
                                        )
                                    "
                                >

                                    Delete

                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Pagination -->

            <div
                v-if="
                    pagination.last_page > 1
                "
                class="d-flex justify-content-between align-items-center mt-3"
            >

                <div class="text-muted">

                    Total:
                    {{ pagination.total }}

                </div>


                <div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary me-2"
                        :disabled="
                            pagination.current_page <= 1
                        "
                        @click="
                            loadAssignments(
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
                            loadAssignments(
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