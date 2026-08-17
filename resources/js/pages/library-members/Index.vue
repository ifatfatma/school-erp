<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Library Members
                </h4>

                <small class="text-muted">
                    Manage students and staff library memberships
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Member
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
                            Total
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
                            Active
                        </small>

                        <h3 class="mb-0 text-success">
                            {{ statistics.active }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Students
                        </small>

                        <h3 class="mb-0 text-primary">
                            {{ statistics.students }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Staff
                        </small>

                        <h3 class="mb-0 text-info">
                            {{ statistics.staff }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Inactive
                        </small>

                        <h3 class="mb-0 text-secondary">
                            {{ statistics.inactive }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-2">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Expired
                        </small>

                        <h3 class="mb-0 text-danger">
                            {{ statistics.expired }}
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
                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Member no, student, staff, phone or email..."
                            @keyup.enter="loadMembers(1)"
                        >

                    </div>


                    <!-- Type -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Member Type
                        </label>

                        <select
                            v-model="filters.member_type"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="Student">
                                Student
                            </option>

                            <option value="Staff">
                                Staff
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

                            <option value="1">
                                Active
                            </option>

                            <option value="0">
                                Inactive
                            </option>

                        </select>

                    </div>


                    <!-- Expiry -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Membership
                        </label>

                        <select
                            v-model="filters.expired"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="0">
                                Valid
                            </option>

                            <option value="1">
                                Expired
                            </option>

                        </select>

                    </div>


                    <!-- Search -->
                    <div class="col-md-1 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadMembers(1)"
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


        <!-- Member List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Member List
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
                                    Member
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Contact
                                </th>

                                <th>
                                    Membership
                                </th>

                                <th>
                                    Issue Limit
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="160"
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
                                    colspan="8"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading members...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    members.length === 0
                                "
                            >

                                <td
                                    colspan="8"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-people fs-2 d-block mb-2"
                                    ></i>

                                    No library members found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    member,
                                    index
                                ) in members"
                                :key="member.id"
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


                                <!-- Member -->
                                <td>

                                    <strong>
                                        {{ displayName(member) }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{ member.member_no }}
                                    </small>

                                </td>


                                <!-- Type -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            member.member_type === 'Student'
                                                ? 'bg-primary'
                                                : 'bg-info'
                                        "
                                    >
                                        {{ member.member_type }}
                                    </span>

                                </td>


                                <!-- Contact -->
                                <td>

                                    <div>
                                        {{ member.phone || '-' }}
                                    </div>

                                    <small
                                        v-if="member.email"
                                        class="text-muted"
                                    >
                                        {{ member.email }}
                                    </small>

                                </td>


                                <!-- Membership -->
                                <td>

                                    <div>
                                        {{
                                            formatDate(
                                                member.membership_date
                                            )
                                        }}
                                        -
                                        {{
                                            member.expiry_date
                                                ? formatDate(
                                                    member.expiry_date
                                                )
                                                : 'No Expiry'
                                        }}
                                    </div>

                                    <span
                                        v-if="
                                            isExpired(member)
                                        "
                                        class="badge bg-danger mt-1"
                                    >
                                        Expired
                                    </span>

                                    <span
                                        v-else
                                        class="badge bg-success mt-1"
                                    >
                                        Valid
                                    </span>

                                </td>


                                <!-- Issue Limit -->
                                <td>

                                    <span class="badge bg-secondary">
                                        {{
                                            member.issue_limit
                                        }}
                                        Books
                                    </span>

                                </td>


                                <!-- Status -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            member.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            member.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </span>

                                </td>


                                <!-- Actions -->
                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(member)
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        :disabled="
                                            actionId === member.id
                                        "
                                        @click="
                                            toggleStatus(member)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === member.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-toggle-on"
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId === member.id
                                        "
                                        @click="
                                            deleteMember(member)
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


        <!-- Add/Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="saveMember"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Library Member'
                                        : 'Add Library Member'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
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

                                <!-- Member No -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Member No.
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.member_no"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.member_no
                                        }"
                                        placeholder="LIB-STU-0001"
                                    >

                                    <div
                                        v-if="formErrors.member_no"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.member_no }}
                                    </div>

                                </div>


                                <!-- Member Type -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Member Type
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.member_type"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.member_type
                                        }"
                                        @change="memberTypeChanged"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Student">
                                            Student
                                        </option>

                                        <option value="Staff">
                                            Staff
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.member_type"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.member_type }}
                                    </div>

                                </div>


                                <!-- Student -->
                                <div
                                    v-if="
                                        form.member_type === 'Student'
                                    "
                                    class="col-md-12"
                                >

                                    <label class="form-label">
                                        Student
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.student_id"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.student_id
                                        }"
                                    >

                                        <option value="">
                                            Select Student
                                        </option>

                                        <option
                                            v-for="student in students"
                                            :key="student.id"
                                            :value="student.id"
                                        >
                                            {{
                                                student.name ||
                                                student.student_name ||
                                                (
                                                    student.first_name +
                                                    ' ' +
                                                    (
                                                        student.last_name || ''
                                                    )
                                                )
                                            }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.student_id"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.student_id }}
                                    </div>

                                </div>


                                <!-- Staff -->
                                <div
                                    v-if="
                                        form.member_type === 'Staff'
                                    "
                                    class="col-md-12"
                                >

                                    <label class="form-label">
                                        Staff
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.staff_id"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.staff_id
                                        }"
                                    >

                                        <option value="">
                                            Select Staff
                                        </option>

                                        <option
                                            v-for="staff in staffMembers"
                                            :key="staff.id"
                                            :value="staff.id"
                                        >
                                            {{
                                                staff.name ||
                                                staff.staff_name ||
                                                (
                                                    staff.first_name +
                                                    ' ' +
                                                    (
                                                        staff.last_name || ''
                                                    )
                                                )
                                            }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.staff_id"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.staff_id }}
                                    </div>

                                </div>


                                <!-- Phone -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="form-control"
                                        placeholder="Phone number"
                                    >

                                </div>


                                <!-- Email -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.email
                                        }"
                                        placeholder="Email address"
                                    >

                                    <div
                                        v-if="formErrors.email"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.email }}
                                    </div>

                                </div>


                                <!-- Membership Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Membership Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.membership_date"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.membership_date
                                        }"
                                    >

                                    <div
                                        v-if="formErrors.membership_date"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.membership_date }}
                                    </div>

                                </div>


                                <!-- Expiry Date -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Expiry Date
                                    </label>

                                    <input
                                        v-model="form.expiry_date"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.expiry_date
                                        }"
                                    >

                                    <div
                                        v-if="formErrors.expiry_date"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.expiry_date }}
                                    </div>

                                </div>


                                <!-- Issue Limit -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Issue Limit
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.issue_limit"
                                        type="number"
                                        min="1"
                                        max="100"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.issue_limit
                                        }"
                                    >

                                    <div
                                        v-if="formErrors.issue_limit"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.issue_limit }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="form.status"
                                        class="form-select"
                                    >

                                        <option :value="true">
                                            Active
                                        </option>

                                        <option :value="false">
                                            Inactive
                                        </option>

                                    </select>

                                </div>


                                <!-- Notes -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Notes
                                    </label>

                                    <textarea
                                        v-model="form.notes"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Additional notes..."
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
                                :disabled="saving"
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update'
                                        : 'Save'
                                }}

                            </button>

                        </div>

                    </form>

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

const members = ref([]);

const students = ref([]);

const staffMembers = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    active: 0,

    inactive: 0,

    students: 0,

    staff: 0,

    expired: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    member_type: '',

    status: '',

    expired: '',

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

    member_no: '',

    member_type: '',

    student_id: '',

    staff_id: '',

    phone: '',

    email: '',

    membership_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    expiry_date: '',

    issue_limit: 3,

    status: true,

    notes: ''

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
        localStorage.getItem('authToken') ||
        sessionStorage.getItem('token') ||
        sessionStorage.getItem('auth_token') ||
        sessionStorage.getItem('access_token') ||
        sessionStorage.getItem('authToken');


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
| Load Members
|--------------------------------------------------------------------------
*/

const loadMembers = async (
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

            member_type:
                filters.value.member_type ||
                undefined,

            status:
                filters.value.status === ''
                    ? undefined
                    : filters.value.status,

            expired:
                filters.value.expired === ''
                    ? undefined
                    : filters.value.expired

        };


        const response =
            await axios.get(
                '/api/library-members',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        members.value =
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
                    filters.value.per_page
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
            'Library member loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load library members.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Students
|--------------------------------------------------------------------------
|
| Adjust endpoint if your existing student API
| uses another route.
|
*/

const loadStudents = async () => {

    try {

        const response =
            await axios.get(
                '/api/students',
                {
                    ...getAuthConfig(),
                    params: {
                        per_page: 100
                    }
                }
            );


        const result =
            response.data?.data;


        students.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.error(
            'Student loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Staff
|--------------------------------------------------------------------------
|
| Adjust endpoint if your existing staff API
| uses another route.
|
*/

const loadStaff = async () => {

    try {

        const response =
            await axios.get(
                '/api/staff',
                {
                    ...getAuthConfig(),
                    params: {
                        per_page: 100
                    }
                }
            );


        const result =
            response.data?.data;


        staffMembers.value =
            result?.data ||
            result ||
            [];

    } catch (err) {

        console.error(
            'Staff loading error:',
            err
        );

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
                '/api/library-members/statistics',
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

            active:
                Number(
                    data.active ||
                    0
                ),

            inactive:
                Number(
                    data.inactive ||
                    0
                ),

            students:
                Number(
                    data.students ||
                    0
                ),

            staff:
                Number(
                    data.staff ||
                    0
                ),

            expired:
                Number(
                    data.expired ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Member statistics error:',
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

    editing.value = false;

    editingId.value = null;

    form.value =
        emptyForm();

    formErrors.value = {};

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = (
    member
) => {

    editing.value = true;

    editingId.value =
        member.id;


    form.value = {

        member_no:
            member.member_no ||
            '',

        member_type:
            member.member_type ||
            '',

        student_id:
            member.student_id ||
            '',

        staff_id:
            member.staff_id ||
            '',

        phone:
            member.phone ||
            '',

        email:
            member.email ||
            '',

        membership_date:
            member.membership_date
                ? String(
                    member.membership_date
                ).substring(
                    0,
                    10
                )
                : '',

        expiry_date:
            member.expiry_date
                ? String(
                    member.expiry_date
                ).substring(
                    0,
                    10
                )
                : '',

        issue_limit:
            member.issue_limit ||
            3,

        status:
            Boolean(
                member.status
            ),

        notes:
            member.notes ||
            ''

    };


    formErrors.value = {};

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Member Type Changed
|--------------------------------------------------------------------------
*/

const memberTypeChanged = () => {

    if (
        form.value.member_type === 'Student'
    ) {

        form.value.staff_id = '';

    }


    if (
        form.value.member_type === 'Staff'
    ) {

        form.value.student_id = '';

    }

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
| Save Member
|--------------------------------------------------------------------------
*/

const saveMember = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            member_no:
                form.value.member_no,

            member_type:
                form.value.member_type,

            student_id:
                form.value.member_type === 'Student'
                    ? form.value.student_id
                    : null,

            staff_id:
                form.value.member_type === 'Staff'
                    ? form.value.staff_id
                    : null,

            phone:
                form.value.phone ||
                null,

            email:
                form.value.email ||
                null,

            membership_date:
                form.value.membership_date,

            expiry_date:
                form.value.expiry_date ||
                null,

            issue_limit:
                form.value.issue_limit,

            status:
                form.value.status,

            notes:
                form.value.notes ||
                null

        };


        let response;


        if (editing.value) {

            response =
                await axios.put(
                    `/api/library-members/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/library-members',
                    payload,
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            'Library member saved successfully.';


        showModal.value = false;


        await Promise.all([

            loadMembers(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Library member save error:',
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
                'Please correct the form errors.';

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save library member.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Toggle Status
|--------------------------------------------------------------------------
*/

const toggleStatus = async (
    member
) => {

    const newStatus =
        !Boolean(
            member.status
        );


    if (
        !window.confirm(
            `Change "${displayName(member)}" status to ${
                newStatus
                    ? 'Active'
                    : 'Inactive'
            }?`
        )
    ) {
        return;
    }


    actionId.value =
        member.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.post(
                `/api/library-members/${member.id}/status`,
                {
                    status:
                        newStatus
                },
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Member status updated successfully.';


        await Promise.all([

            loadMembers(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to update member status.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete Member
|--------------------------------------------------------------------------
*/

const deleteMember = async (
    member
) => {

    if (
        !window.confirm(
            `Delete library member "${displayName(member)}"?`
        )
    ) {
        return;
    }


    actionId.value =
        member.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(
                `/api/library-members/${member.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Library member deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            members.value.length === 1 &&
            page > 1
        ) {
            page--;
        }


        await Promise.all([

            loadMembers(page),

            loadStatistics()

        ]);

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete library member.';

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

        member_type: '',

        status: '',

        expired: '',

        per_page: 20

    };


    loadMembers(1);

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


    loadMembers(page);

};


/*
|--------------------------------------------------------------------------
| Display Name
|--------------------------------------------------------------------------
*/

const displayName = (
    member
) => {

    if (
        member.member_type === 'Student'
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
| Expired
|--------------------------------------------------------------------------
*/

const isExpired = (
    member
) => {

    if (
        !member.expiry_date
    ) {
        return false;
    }


    const expiry =
        new Date(
            member.expiry_date
        );


    const today =
        new Date();


    today.setHours(
        0,
        0,
        0,
        0
    );


    return expiry < today;

};


/*
|--------------------------------------------------------------------------
| Date
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
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([

            loadMembers(1),

            loadStatistics(),

            loadStudents(),

            loadStaff()

        ]);

    }
);

</script>