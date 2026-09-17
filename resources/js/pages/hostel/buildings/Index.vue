<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Hostel Buildings</h4>
                <small class="text-muted">
                    Manage hostel buildings
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
                :disabled="saving"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Building
            </button>
        </div>


        <!-- =========================================================
             SUCCESS MESSAGE
        ========================================================== -->
        <div
            v-if="success"
            class="alert alert-success alert-dismissible fade show"
            role="alert"
        >
            {{ success }}

            <button
                type="button"
                class="btn-close"
                @click="success = ''"
            ></button>
        </div>


        <!-- =========================================================
             ERROR MESSAGE
        ========================================================== -->
        <div
            v-if="error"
            class="alert alert-danger alert-dismissible fade show"
            role="alert"
        >
            {{ error }}

            <button
                type="button"
                class="btn-close"
                @click="error = ''"
            ></button>
        </div>


        <!-- =========================================================
             FILTERS
        ========================================================== -->
        <div class="card mb-3">
            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-md-5">
                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model.trim="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Building name, code, type or warden..."
                            @keyup.enter="searchBuildings"
                        >
                    </div>


                    <!-- Status -->
                    <div class="col-md-3">
                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="filters.status"
                            class="form-select"
                            @change="searchBuildings"
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


                    <!-- Per Page -->
                    <div class="col-md-2">
                        <label class="form-label">
                            Per Page
                        </label>

                        <select
                            v-model.number="filters.per_page"
                            class="form-select"
                            @change="searchBuildings"
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


                    <!-- Buttons -->
                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="searchBuildings"
                            title="Search"
                        >
                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm"
                            ></span>

                            <i
                                v-else
                                class="bi bi-search"
                            ></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetFilters"
                            title="Reset"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>
        </div>


        <!-- =========================================================
             BUILDING LIST
        ========================================================== -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <strong>
                    Building List
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

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Building
                                </th>

                                <th>
                                    Type
                                </th>

                                <th
                                    width="90"
                                    class="text-center"
                                >
                                    Floors
                                </th>

                                <th
                                    width="90"
                                    class="text-center"
                                >
                                    Rooms
                                </th>

                                <th
                                    width="90"
                                    class="text-center"
                                >
                                    Beds
                                </th>

                                <th>
                                    Warden
                                </th>

                                <th
                                    width="100"
                                    class="text-center"
                                >
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
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading buildings...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="buildings.length === 0"
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-building fs-2 d-block mb-2"
                                    ></i>

                                    No hostel buildings found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-else
                                v-for="(
                                    building,
                                    index
                                ) in buildings"
                                :key="building.id"
                            >

                                <!-- Number -->
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


                                <!-- Building -->
                                <td>

                                    <strong>
                                        {{ building.name }}
                                    </strong>

                                    <small
                                        v-if="building.code"
                                        class="d-block text-muted"
                                    >
                                        Code:
                                        {{ building.code }}
                                    </small>

                                </td>


                                <!-- Type -->
                                <td>
                                    {{ building.building_type || '-' }}
                                </td>


                                <!-- Floors -->
                                <td class="text-center">
                                    {{ building.total_floors ?? 0 }}
                                </td>


                                <!-- Rooms -->
                                <td class="text-center">
                                    {{ building.total_rooms ?? 0 }}
                                </td>


                                <!-- Beds -->
                                <td class="text-center">
                                    {{ building.total_beds ?? 0 }}
                                </td>


                                <!-- Warden -->
                                <td>

                                    <template v-if="building.warden_name">

                                        {{ building.warden_name }}

                                        <small
                                            v-if="building.warden_phone"
                                            class="d-block text-muted"
                                        >
                                            {{ building.warden_phone }}
                                        </small>

                                    </template>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <!-- Status -->
                                <td class="text-center">

                                    <span
                                        class="badge"
                                        :class="
                                            isActive(building)
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            isActive(building)
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
                                        :disabled="
                                            actionId === building.id ||
                                            saving
                                        "
                                        @click="openEdit(building)"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        :title="
                                            isActive(building)
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                        :disabled="
                                            actionId === building.id ||
                                            saving
                                        "
                                        @click="toggleStatus(building)"
                                    >

                                        <span
                                            v-if="
                                                actionId === building.id
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
                                            actionId === building.id ||
                                            saving
                                        "
                                        @click="deleteBuilding(building)"
                                    >
                                        <span
                                            v-if="
                                                actionId === building.id &&
                                                actionType === 'delete'
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-trash"
                                        ></i>
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =====================================================
                 PAGINATION
            ====================================================== -->
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


        <!-- =========================================================
             ADD / EDIT MODAL
        ========================================================== -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveBuilding"
                        novalidate
                    >

                        <!-- Modal Header -->
                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Hostel Building'
                                        : 'Add Hostel Building'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <!-- Modal Body -->
                        <div class="modal-body">

                            <!-- Form Error -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Building Name -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Building Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model.trim="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="e.g. Boys Hostel Block A"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.name }}
                                    </div>

                                </div>


                                <!-- Code -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Building Code
                                    </label>

                                    <input
                                        v-model.trim="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="e.g. BH-A"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
                                    </div>

                                </div>


                                <!-- Building Type -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Building Type
                                    </label>

                                    <select
                                        v-model="form.building_type"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.building_type
                                        }"
                                        :disabled="saving"
                                    >

                                        <option value="">
                                            Select Type
                                        </option>

                                        <option value="Boys Hostel">
                                            Boys Hostel
                                        </option>

                                        <option value="Girls Hostel">
                                            Girls Hostel
                                        </option>

                                        <option value="Staff Hostel">
                                            Staff Hostel
                                        </option>

                                        <option value="Other">
                                            Other
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.building_type"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.building_type }}
                                    </div>

                                </div>


                                <!-- Floors -->
                                <div class="col-md-2">

                                    <label class="form-label">
                                        Floors
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model.number="form.total_floors"
                                        type="number"
                                        min="1"
                                        step="1"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.total_floors
                                        }"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.total_floors"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.total_floors }}
                                    </div>

                                </div>


                                <!-- Rooms -->
                                <div class="col-md-2">

                                    <label class="form-label">
                                        Rooms
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model.number="form.total_rooms"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.total_rooms
                                        }"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.total_rooms"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.total_rooms }}
                                    </div>

                                </div>


                                <!-- Beds -->
                                <div class="col-md-2">

                                    <label class="form-label">
                                        Beds
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model.number="form.total_beds"
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.total_beds
                                        }"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.total_beds"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.total_beds }}
                                    </div>

                                </div>


                                <!-- Warden Name -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Warden Name
                                    </label>

                                    <input
                                        v-model.trim="form.warden_name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.warden_name
                                        }"
                                        placeholder="Warden name"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.warden_name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.warden_name }}
                                    </div>

                                </div>


                                <!-- Warden Phone -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Warden Phone
                                    </label>

                                    <input
                                        v-model.trim="form.warden_phone"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.warden_phone
                                        }"
                                        placeholder="Phone number"
                                        :disabled="saving"
                                    >

                                    <div
                                        v-if="formErrors.warden_phone"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.warden_phone }}
                                    </div>

                                </div>


                                <!-- Address -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea
                                        v-model.trim="form.address"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.address
                                        }"
                                        rows="3"
                                        placeholder="Hostel building address..."
                                        :disabled="saving"
                                    ></textarea>

                                    <div
                                        v-if="formErrors.address"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.address }}
                                    </div>

                                </div>


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model.trim="form.description"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.description
                                        }"
                                        rows="3"
                                        placeholder="Additional information..."
                                        :disabled="saving"
                                    ></textarea>

                                    <div
                                        v-if="formErrors.description"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.description }}
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
                                        :disabled="saving"
                                    >

                                        <option :value="true">
                                            Active
                                        </option>

                                        <option :value="false">
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        <!-- Modal Footer -->
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

const buildings = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const actionType = ref('');

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({
    search: '',
    status: '',
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
| Empty Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({
    name: '',
    code: '',
    building_type: '',
    total_floors: 1,
    total_rooms: 0,
    total_beds: 0,
    warden_name: '',
    warden_phone: '',
    address: '',
    description: '',
    status: true
});


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Authentication
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
| Status Helper
|--------------------------------------------------------------------------
*/

const isActive = (building) => {

    return (
        building?.status === true ||
        building?.status === 1 ||
        building?.status === '1'
    );

};


/*
|--------------------------------------------------------------------------
| Extract API Error
|--------------------------------------------------------------------------
*/

const getApiErrorMessage = (
    err,
    fallback
) => {

    return (
        err?.response?.data?.message ||
        err?.response?.data?.error ||
        fallback
    );

};


/*
|--------------------------------------------------------------------------
| Load Buildings
|--------------------------------------------------------------------------
*/

const loadBuildings = async (
    page = 1
) => {

    if (loading.value) {
        return;
    }


    loading.value = true;

    error.value = '';


    try {

        const params = {
            page: Number(page) || 1,

            per_page:
                Number(filters.value.per_page) || 20,

            search:
                filters.value.search
                    ? filters.value.search
                    : undefined,

            status:
                filters.value.status === ''
                    ? undefined
                    : filters.value.status
        };


        const response =
            await axios.get(
                '/api/hostel/buildings',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Expected Laravel response
        |--------------------------------------------------------------------------
        |
        | {
        |     success: true,
        |     data: {
        |         data: [],
        |         current_page: 1,
        |         last_page: 1,
        |         per_page: 20,
        |         total: 0,
        |         from: 0,
        |         to: 0
        |     }
        | }
        |
        |--------------------------------------------------------------------------
        */

        const result =
            response?.data?.data;


        const records =
            Array.isArray(result?.data)
                ? result.data
                : [];


        buildings.value =
            records.map((building) => ({
                ...building,

                status:
                    isActive(building)
            }));


        pagination.value = {

            current_page:
                Number(
                    result?.current_page ?? 1
                ),

            last_page:
                Number(
                    result?.last_page ?? 1
                ),

            per_page:
                Number(
                    result?.per_page ??
                    filters.value.per_page
                ),

            total:
                Number(
                    result?.total ?? 0
                ),

            from:
                Number(
                    result?.from ?? 0
                ),

            to:
                Number(
                    result?.to ?? 0
                )
        };


    } catch (err) {

        console.error(
            'Hostel building loading error:',
            err
        );


        error.value =
            getApiErrorMessage(
                err,
                'Unable to load hostel buildings.'
            );


        buildings.value = [];


    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

const searchBuildings = () => {

    loadBuildings(1);

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

    error.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = (
    building
) => {

    editing.value = true;

    editingId.value =
        building.id;


    form.value = {

        name:
            building.name ?? '',

        code:
            building.code ?? '',

        building_type:
            building.building_type ?? '',

        total_floors:
            Number(
                building.total_floors ?? 1
            ),

        total_rooms:
            Number(
                building.total_rooms ?? 0
            ),

        total_beds:
            Number(
                building.total_beds ?? 0
            ),

        warden_name:
            building.warden_name ?? '',

        warden_phone:
            building.warden_phone ?? '',

        address:
            building.address ?? '',

        description:
            building.description ?? '',

        status:
            isActive(building)
    };


    formErrors.value = {};

    formError.value = '';

    error.value = '';

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

    formErrors.value = {};

    formError.value = '';

};


/*
|--------------------------------------------------------------------------
| Convert Validation Errors
|--------------------------------------------------------------------------
*/

const setValidationErrors = (
    validationErrors
) => {

    formErrors.value =
        Object.fromEntries(

            Object.entries(
                validationErrors || {}
            ).map(
                ([key, value]) => [

                    key,

                    Array.isArray(value)
                        ? value[0]
                        : String(value)

                ]
            )

        );

};


/*
|--------------------------------------------------------------------------
| Save Building
|--------------------------------------------------------------------------
*/

const saveBuilding = async () => {

    if (saving.value) {
        return;
    }


    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        /*
        |--------------------------------------------------------------------------
        | Basic frontend validation
        |--------------------------------------------------------------------------
        */

        if (!form.value.name?.trim()) {

            formErrors.value.name =
                'Building name is required.';

            formError.value =
                'Please enter building name.';

            return;
        }


        const payload = {

            name:
                form.value.name.trim(),

            code:
                form.value.code?.trim() || null,

            building_type:
                form.value.building_type || null,

            total_floors:
                Math.max(
                    1,
                    Number(
                        form.value.total_floors
                    ) || 1
                ),

            total_rooms:
                Math.max(
                    0,
                    Number(
                        form.value.total_rooms
                    ) || 0
                ),

            total_beds:
                Math.max(
                    0,
                    Number(
                        form.value.total_beds
                    ) || 0
                ),

            warden_name:
                form.value.warden_name?.trim() || null,

            warden_phone:
                form.value.warden_phone?.trim() || null,

            address:
                form.value.address?.trim() || null,

            description:
                form.value.description?.trim() || null,

            status:
                Boolean(
                    form.value.status
                )
        };


        let response;


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if (editing.value) {

            response =
                await axios.put(

                    `/api/hostel/buildings/${editingId.value}`,

                    payload,

                    getAuthConfig()

                );


            success.value =
                response?.data?.message ||
                'Hostel building updated successfully.';


            showModal.value = false;


            /*
            |--------------------------------------------------------------------------
            | Stay on current page after edit
            |--------------------------------------------------------------------------
            */

            await loadBuildings(
                pagination.value.current_page
            );


        }


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        else {

            response =
                await axios.post(

                    '/api/hostel/buildings',

                    payload,

                    getAuthConfig()

                );


            success.value =
                response?.data?.message ||
                'Hostel building created successfully.';


            showModal.value = false;


            /*
            |--------------------------------------------------------------------------
            | IMPORTANT FIX
            |--------------------------------------------------------------------------
            |
            | Always go to page 1 after creating a new building.
            | This ensures the new record appears immediately when
            | backend ordering is latest-first.
            |
            |--------------------------------------------------------------------------
            */

            await loadBuildings(1);

        }


    } catch (err) {

        console.error(
            'Hostel building save error:',
            err
        );


        if (
            err?.response?.status === 422
        ) {

            setValidationErrors(
                err.response?.data?.errors
            );


            formError.value =
                err.response?.data?.message ||
                'Please correct the form errors.';


        } else {

            formError.value =
                getApiErrorMessage(
                    err,
                    'Unable to save hostel building.'
                );

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
    building
) => {

    if (actionId.value) {
        return;
    }


    const currentStatus =
        isActive(building);


    const newStatus =
        !currentStatus;


    if (
        !window.confirm(
            `Change "${building.name}" status to ${
                newStatus
                    ? 'Active'
                    : 'Inactive'
            }?`
        )
    ) {
        return;
    }


    actionId.value =
        building.id;

    actionType.value =
        'status';

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.patch(

                `/api/hostel/buildings/${building.id}/status`,

                {
                    status: newStatus
                },

                getAuthConfig()

            );


        success.value =
            response?.data?.message ||
            'Building status updated successfully.';


        await loadBuildings(
            pagination.value.current_page
        );


    } catch (err) {

        console.error(
            'Building status error:',
            err
        );


        error.value =
            getApiErrorMessage(
                err,
                'Unable to update building status.'
            );


    } finally {

        actionId.value = null;

        actionType.value = '';

    }

};


/*
|--------------------------------------------------------------------------
| Delete Building
|--------------------------------------------------------------------------
*/

const deleteBuilding = async (
    building
) => {

    if (actionId.value) {
        return;
    }


    if (
        !window.confirm(
            `Delete hostel building "${building.name}"?`
        )
    ) {
        return;
    }


    actionId.value =
        building.id;

    actionType.value =
        'delete';

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/hostel/buildings/${building.id}`,

                getAuthConfig()

            );


        success.value =
            response?.data?.message ||
            'Hostel building deleted successfully.';


        /*
        |--------------------------------------------------------------------------
        | If deleting the only item on a page,
        | go to previous page.
        |--------------------------------------------------------------------------
        */

        let page =
            pagination.value.current_page;


        if (
            buildings.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadBuildings(page);


    } catch (err) {

        console.error(
            'Building delete error:',
            err
        );


        error.value =
            getApiErrorMessage(
                err,
                'Unable to delete hostel building.'
            );


    } finally {

        actionId.value = null;

        actionType.value = '';

    }

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = async () => {

    if (loading.value) {
        return;
    }


    filters.value = {

        search: '',

        status: '',

        per_page: 20

    };


    await loadBuildings(1);

};


/*
|--------------------------------------------------------------------------
| Change Page
|--------------------------------------------------------------------------
*/

const changePage = (
    page
) => {

    const targetPage =
        Number(page);


    if (
        targetPage < 1 ||
        targetPage >
            pagination.value.last_page
    ) {
        return;
    }


    loadBuildings(
        targetPage
    );

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadBuildings(1);

    }
);

</script>