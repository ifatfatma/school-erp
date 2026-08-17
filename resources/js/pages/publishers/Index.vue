<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Publishers</h4>
                <small class="text-muted">
                    Manage library book publishers
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Publisher
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
                            placeholder="Search name, code, email or phone..."
                            @keyup.enter="loadPublishers(1)"
                        >

                    </div>


                    <div class="col-md-3">

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


                    <div class="col-md-4 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadPublishers(1)"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
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


        <!-- Publisher List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Publisher List
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
                                    Publisher
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Contact
                                </th>

                                <th>
                                    Website
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="180"
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
                                    colspan="7"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading publishers...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    publishers.length === 0
                                "
                            >

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-building fs-2 d-block mb-2"
                                    ></i>

                                    No publishers found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    publisher,
                                    index
                                ) in publishers"
                                :key="publisher.id"
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
                                        {{ publisher.name }}
                                    </strong>

                                    <small
                                        v-if="publisher.address"
                                        class="d-block text-muted text-truncate"
                                        style="max-width: 280px"
                                    >
                                        {{ publisher.address }}
                                    </small>

                                </td>


                                <td>
                                    {{ publisher.code || '-' }}
                                </td>


                                <td>

                                    <div>
                                        {{ publisher.email || '-' }}
                                    </div>

                                    <small
                                        v-if="publisher.phone"
                                        class="text-muted"
                                    >
                                        {{ publisher.phone }}
                                    </small>

                                </td>


                                <td>

                                    <a
                                        v-if="publisher.website"
                                        :href="publisher.website"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Visit
                                    </a>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            publisher.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            publisher.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(publisher)
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
                                            actionId === publisher.id
                                        "
                                        @click="
                                            toggleStatus(publisher)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === publisher.id
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
                                            actionId === publisher.id
                                        "
                                        @click="
                                            deletePublisher(publisher)
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


        <!-- Add / Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="savePublisher"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Publisher'
                                        : 'Add Publisher'
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

                                <!-- Name -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Publisher Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="Publisher name"
                                    >

                                    <div
                                        v-if="formErrors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.name }}
                                    </div>

                                </div>


                                <!-- Code -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Publisher Code
                                    </label>

                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="PUB-001"
                                    >

                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
                                    </div>

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
                                        placeholder="publisher@example.com"
                                    >

                                    <div
                                        v-if="formErrors.email"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.email }}
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
                                        :class="{
                                            'is-invalid':
                                                formErrors.phone
                                        }"
                                        placeholder="Phone number"
                                    >

                                    <div
                                        v-if="formErrors.phone"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.phone }}
                                    </div>

                                </div>


                                <!-- Website -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Website
                                    </label>

                                    <input
                                        v-model="form.website"
                                        type="url"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.website
                                        }"
                                        placeholder="https://example.com"
                                    >

                                    <div
                                        v-if="formErrors.website"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.website }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-6">

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


                                <!-- Address -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <textarea
                                        v-model="form.address"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Publisher address..."
                                    ></textarea>

                                </div>


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="form.description"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Publisher description..."
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

const publishers = ref([]);

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

    email: '',

    phone: '',

    address: '',

    website: '',

    description: '',

    status: true

});


const form = ref(
    emptyForm()
);


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
| Load Publishers
|--------------------------------------------------------------------------
*/

const loadPublishers = async (
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

            status:
                filters.value.status === ''
                    ? undefined
                    : filters.value.status

        };


        const response =
            await axios.get(
                '/api/publishers',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        publishers.value =
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
            'Publisher loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load publishers.';

    } finally {

        loading.value = false;

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
    publisher
) => {

    editing.value = true;

    editingId.value =
        publisher.id;


    form.value = {

        name:
            publisher.name ||
            '',

        code:
            publisher.code ||
            '',

        email:
            publisher.email ||
            '',

        phone:
            publisher.phone ||
            '',

        address:
            publisher.address ||
            '',

        website:
            publisher.website ||
            '',

        description:
            publisher.description ||
            '',

        status:
            Boolean(
                publisher.status
            )

    };


    formErrors.value = {};

    formError.value = '';

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

};


/*
|--------------------------------------------------------------------------
| Save Publisher
|--------------------------------------------------------------------------
*/

const savePublisher = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            name:
                form.value.name,

            code:
                form.value.code ||
                null,

            email:
                form.value.email ||
                null,

            phone:
                form.value.phone ||
                null,

            address:
                form.value.address ||
                null,

            website:
                form.value.website ||
                null,

            description:
                form.value.description ||
                null,

            status:
                form.value.status

        };


        let response;


        if (editing.value) {

            response =
                await axios.put(
                    `/api/publishers/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/publishers',
                    payload,
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            'Publisher saved successfully.';


        showModal.value = false;


        await loadPublishers(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            'Publisher save error:',
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
                'Unable to save publisher.';

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
    publisher
) => {

    const newStatus =
        !Boolean(
            publisher.status
        );


    if (
        !window.confirm(
            `Change "${publisher.name}" status to ${
                newStatus
                    ? 'Active'
                    : 'Inactive'
            }?`
        )
    ) {
        return;
    }


    actionId.value =
        publisher.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.post(
                `/api/publishers/${publisher.id}/status`,
                {
                    status:
                        newStatus
                },
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Publisher status updated successfully.';


        await loadPublishers(
            pagination.value.current_page
        );

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to update publisher status.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete Publisher
|--------------------------------------------------------------------------
*/

const deletePublisher = async (
    publisher
) => {

    if (
        !window.confirm(
            `Delete publisher "${publisher.name}"?`
        )
    ) {
        return;
    }


    actionId.value =
        publisher.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(
                `/api/publishers/${publisher.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Publisher deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            publishers.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadPublishers(
            page
        );

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete publisher.';

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

        status: '',

        per_page: 20

    };


    loadPublishers(1);

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


    loadPublishers(page);

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadPublishers(1);

});

</script>