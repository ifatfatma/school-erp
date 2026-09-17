<template>
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h4 class="mb-1">
                Hostel Rooms
            </h4>

            <small class="text-muted">
                Manage hostel rooms and capacity
            </small>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            @click="openCreate"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Add Room
        </button>

    </div>


    <!-- Success -->
    <div
        v-if="success"
        class="alert alert-success alert-dismissible"
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
        class="alert alert-danger alert-dismissible"
    >
        {{ error }}

        <button
            type="button"
            class="btn-close"
            @click="error = ''"
        ></button>
    </div>


    <!-- Filters -->
    <div class="card mb-3">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <label class="form-label">
                        Search
                    </label>

                    <input
                        v-model="filters.search"
                        type="text"
                        class="form-control"
                        placeholder="Room, type or building..."
                        @keyup.enter="loadRooms(1)"
                    >

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Building
                    </label>

                    <select
                        v-model="filters.hostel_building_id"
                        class="form-select"
                        @change="loadRooms(1)"
                    >

                        <option value="">
                            All Buildings
                        </option>

                        <option
                            v-for="building in buildings"
                            :key="building.id"
                            :value="building.id"
                        >
                            {{ building.name }}
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        v-model="filters.status"
                        class="form-select"
                        @change="loadRooms(1)"
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


                <div class="col-md-3 d-flex align-items-end">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        @click="loadRooms(1)"
                    >
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


    <!-- Table -->
    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <strong>
                Room List
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

                            <th width="60">
                                #
                            </th>

                            <th>
                                Building
                            </th>

                            <th>
                                Room
                            </th>

                            <th>
                                Floor
                            </th>

                            <th>
                                Type
                            </th>

                            <th class="text-center">
                                Capacity
                            </th>

                            <th class="text-center">
                                Occupied
                            </th>

                            <th class="text-center">
                                Available
                            </th>

                            <th class="text-end">
                                Monthly Fee
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

                        <tr v-if="loading">

                            <td
                                colspan="11"
                                class="text-center py-5"
                            >
                                <div
                                    class="spinner-border text-primary"
                                ></div>

                                <div class="mt-2 text-muted">
                                    Loading rooms...
                                </div>
                            </td>

                        </tr>


                        <tr
                            v-else-if="rooms.length === 0"
                        >

                            <td
                                colspan="11"
                                class="text-center py-5 text-muted"
                            >
                                No hostel rooms found.
                            </td>

                        </tr>


                        <tr
                            v-for="(room, index) in rooms"
                            :key="room.id"
                        >

                            <td>
                                {{
                                    ((pagination.current_page - 1)
                                        * pagination.per_page)
                                    + index + 1
                                }}
                            </td>


                            <td>

                                <strong>
                                    {{ room.building?.name || '-' }}
                                </strong>

                                <small
                                    v-if="room.building?.code"
                                    class="d-block text-muted"
                                >
                                    {{ room.building.code }}
                                </small>

                            </td>


                            <td>
                                <strong>
                                    {{ room.room_no }}
                                </strong>
                            </td>


                            <td>
                                {{ room.floor_no }}
                            </td>


                            <td>
                                {{ room.room_type || '-' }}
                            </td>


                            <td class="text-center">
                                {{ room.capacity }}
                            </td>


                            <td class="text-center">

                                <span class="badge bg-warning text-dark">
                                    {{ room.occupied_beds }}
                                </span>

                            </td>


                            <td class="text-center">

                                <span class="badge bg-info text-dark">
                                    {{ room.available_beds }}
                                </span>

                            </td>


                            <td class="text-end">
                                {{ money(room.monthly_fee) }}
                            </td>


                            <td>

                                <span
                                    class="badge"
                                    :class="
                                        room.status
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        room.status
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>

                            </td>


                            <td class="text-center">

                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary me-1"
                                    title="Edit"
                                    @click="openEdit(room)"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-warning me-1"
                                    title="Change Status"
                                    :disabled="actionId === room.id"
                                    @click="toggleStatus(room)"
                                >
                                    <i class="bi bi-toggle-on"></i>
                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Delete"
                                    :disabled="actionId === room.id"
                                    @click="deleteRoom(room)"
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
            v-if="pagination.last_page > 1"
            class="card-footer"
        >

            <div class="d-flex justify-content-between align-items-center">

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
                        :disabled="pagination.current_page <= 1"
                        @click="
                            loadRooms(
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
                            pagination.last_page
                        "
                        @click="
                            loadRooms(
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


    <!-- Modal -->
    <div
        v-if="showModal"
        class="modal fade show d-block"
        style="background: rgba(0,0,0,.5)"
    >

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <form @submit.prevent="saveRoom">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            {{
                                editing
                                    ? 'Edit Hostel Room'
                                    : 'Add Hostel Room'
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

                            <!-- Building -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Building
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    v-model="form.hostel_building_id"
                                    class="form-select"
                                    :class="invalid('hostel_building_id')"
                                >

                                    <option value="">
                                        Select Building
                                    </option>

                                    <option
                                        v-for="building in buildings"
                                        :key="building.id"
                                        :value="building.id"
                                    >
                                        {{ building.name }}
                                        {{ building.code ? `(${building.code})` : '' }}
                                    </option>

                                </select>

                                <div class="invalid-feedback">
                                    {{
                                        formErrors.hostel_building_id
                                    }}
                                </div>

                            </div>


                            <!-- Room -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Room No.
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    v-model="form.room_no"
                                    type="text"
                                    class="form-control"
                                    :class="invalid('room_no')"
                                    placeholder="101"
                                >

                                <div class="invalid-feedback">
                                    {{ formErrors.room_no }}
                                </div>

                            </div>


                            <!-- Floor -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Floor
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    v-model.number="form.floor_no"
                                    type="number"
                                    min="1"
                                    class="form-control"
                                    :class="invalid('floor_no')"
                                >

                                <div class="invalid-feedback">
                                    {{ formErrors.floor_no }}
                                </div>

                            </div>


                            <!-- Type -->
                            <div class="col-md-4">

                                <label class="form-label">
                                    Room Type
                                </label>

                                <select
                                    v-model="form.room_type"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Type
                                    </option>

                                    <option value="Single">
                                        Single
                                    </option>

                                    <option value="Double">
                                        Double
                                    </option>

                                    <option value="Triple">
                                        Triple
                                    </option>

                                    <option value="Dormitory">
                                        Dormitory
                                    </option>

                                    <option value="Other">
                                        Other
                                    </option>

                                </select>

                            </div>


                            <!-- Capacity -->
                            <div class="col-md-2">

                                <label class="form-label">
                                    Capacity
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    v-model.number="form.capacity"
                                    type="number"
                                    min="1"
                                    class="form-control"
                                    :class="invalid('capacity')"
                                >

                                <div class="invalid-feedback">
                                    {{ formErrors.capacity }}
                                </div>

                            </div>


                            <!-- Occupied -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Occupied Beds
                                </label>

                                <input
                                    v-model.number="form.occupied_beds"
                                    type="number"
                                    min="0"
                                    :max="form.capacity"
                                    class="form-control"
                                    :class="invalid('occupied_beds')"
                                >

                                <div class="invalid-feedback">
                                    {{ formErrors.occupied_beds }}
                                </div>

                            </div>


                            <!-- Monthly Fee -->
                            <div class="col-md-3">

                                <label class="form-label">
                                    Monthly Fee
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    v-model.number="form.monthly_fee"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    class="form-control"
                                    :class="invalid('monthly_fee')"
                                >

                                <div class="invalid-feedback">
                                    {{ formErrors.monthly_fee }}
                                </div>

                            </div>


                            <!-- Remarks -->
                            <div class="col-md-12">

                                <label class="form-label">
                                    Remarks
                                </label>

                                <textarea
                                    v-model="form.remarks"
                                    class="form-control"
                                    rows="3"
                                ></textarea>

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
                                    ? 'Update Room'
                                    : 'Save Room'
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


const rooms = ref([]);
const buildings = ref([]);

const loading = ref(false);
const saving = ref(false);

const actionId = ref(null);

const error = ref('');
const success = ref('');

const showModal = ref(false);

const editing = ref(false);
const editingId = ref(null);

const formError = ref('');
const formErrors = ref({});


const filters = ref({
    search: '',
    hostel_building_id: '',
    status: '',
    per_page: 20
});


const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
    from: 0,
    to: 0
});


const emptyForm = () => ({
    hostel_building_id: '',
    room_no: '',
    floor_no: 1,
    room_type: '',
    capacity: 1,
    occupied_beds: 0,
    monthly_fee: 0,
    status: true,
    remarks: ''
});


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
        sessionStorage.getItem('token');


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
| Validation Class
|--------------------------------------------------------------------------
*/

const invalid = field => ({
    'is-invalid': !!formErrors.value[field]
});


/*
|--------------------------------------------------------------------------
| Load Buildings
|--------------------------------------------------------------------------
*/

const loadBuildings = async () => {

    try {

        const response =
            await axios.get(
                '/api/hostel/rooms/buildings',
                getAuthConfig()
            );


        buildings.value =
            response.data?.data || [];

    } catch (err) {

        console.error(
            'Building loading error:',
            err
        );

        error.value =
            err.response?.data?.message ||
            'Unable to load hostel buildings.';
    }
};


/*
|--------------------------------------------------------------------------
| Load Rooms
|--------------------------------------------------------------------------
*/

const loadRooms = async (page = 1) => {

    loading.value = true;
    error.value = '';


    try {

        const response =
            await axios.get(
                '/api/hostel/rooms',
                {
                    ...getAuthConfig(),

                    params: {
                        page,

                        search:
                            filters.value.search ||
                            undefined,

                        hostel_building_id:
                            filters.value.hostel_building_id ||
                            undefined,

                        status:
                            filters.value.status === ''
                                ? undefined
                                : filters.value.status,

                        per_page:
                            filters.value.per_page
                    }
                }
            );


        /*
        |--------------------------------------------------------------------------
        | Laravel Resource Collection Response
        |--------------------------------------------------------------------------
        */

        rooms.value =
            response.data?.data || [];


        const meta =
            response.data?.meta || {};


        pagination.value = {

            current_page:
                Number(
                    meta.current_page || 1
                ),

            last_page:
                Number(
                    meta.last_page || 1
                ),

            per_page:
                Number(
                    meta.per_page ||
                    filters.value.per_page
                ),

            total:
                Number(
                    meta.total || 0
                ),

            from:
                Number(
                    meta.from || 0
                ),

            to:
                Number(
                    meta.to || 0
                )
        };


    } catch (err) {

        console.error(
            'Room loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load hostel rooms.';

    } finally {

        loading.value = false;
    }
};


/*
|--------------------------------------------------------------------------
| Create
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
| Edit
|--------------------------------------------------------------------------
*/

const openEdit = room => {

    editing.value = true;

    editingId.value =
        room.id;


    form.value = {

        hostel_building_id:
            room.hostel_building_id,

        room_no:
            room.room_no || '',

        floor_no:
            Number(room.floor_no || 1),

        room_type:
            room.room_type || '',

        capacity:
            Number(room.capacity || 1),

        occupied_beds:
            Number(
                room.occupied_beds || 0
            ),

        monthly_fee:
            Number(
                room.monthly_fee || 0
            ),

        status:
            Boolean(room.status),

        remarks:
            room.remarks || ''
    };


    formErrors.value = {};
    formError.value = '';

    showModal.value = true;
};


const closeModal = () => {

    if (saving.value) {
        return;
    }

    showModal.value = false;
};


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveRoom = async () => {

    saving.value = true;

    formErrors.value = {};
    formError.value = '';

    try {

        const payload = {
            ...form.value,

            hostel_building_id:
                Number(
                    form.value.hostel_building_id
                ),

            floor_no:
                Number(form.value.floor_no),

            capacity:
                Number(form.value.capacity),

            occupied_beds:
                Number(
                    form.value.occupied_beds
                ),

            monthly_fee:
                Number(
                    form.value.monthly_fee
                ),

            status:
                Boolean(form.value.status)
        };


        let response;


        if (editing.value) {

            response =
                await axios.put(
                    `/api/hostel/rooms/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/hostel/rooms',
                    payload,
                    getAuthConfig()
                );
        }


        success.value =
            response.data?.message ||
            'Room saved successfully.';


        showModal.value = false;


        await loadRooms(
            pagination.value.current_page
        );


    } catch (err) {

        if (
            err.response?.status === 422
        ) {

            const errors =
                err.response.data?.errors ||
                {};


            Object.keys(errors)
                .forEach(key => {

                    formErrors.value[key] =
                        Array.isArray(errors[key])
                            ? errors[key][0]
                            : errors[key];
                });


            formError.value =
                'Please correct the form errors.';

        } else {

            formError.value =
                err.response?.data?.message ||
                'Unable to save room.';
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

const toggleStatus = async room => {

    if (
        !confirm(
            `Change status of room "${room.room_no}"?`
        )
    ) {
        return;
    }


    actionId.value =
        room.id;


    try {

        const response =
            await axios.patch(
                `/api/hostel/rooms/${room.id}/status`,
                {},
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Status updated successfully.';


        await loadRooms(
            pagination.value.current_page
        );


    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to update status.';

    } finally {

        actionId.value = null;
    }
};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteRoom = async room => {

    if (
        !confirm(
            `Delete room "${room.room_no}"?`
        )
    ) {
        return;
    }


    actionId.value =
        room.id;


    try {

        const response =
            await axios.delete(
                `/api/hostel/rooms/${room.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Room deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            rooms.value.length === 1 &&
            page > 1
        ) {
            page--;
        }


        await loadRooms(page);


    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete room.';

    } finally {

        actionId.value = null;
    }
};


const resetFilters = () => {

    filters.value = {
        search: '',
        hostel_building_id: '',
        status: '',
        per_page: 20
    };


    loadRooms(1);
};


const money = value => {

    return Number(value || 0)
        .toLocaleString(
            undefined,
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );
};


onMounted(async () => {

    await Promise.all([
        loadBuildings(),
        loadRooms(1)
    ]);
});

</script>