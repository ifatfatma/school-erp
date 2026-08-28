<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Categories
                </h4>

                <small class="text-muted">
                    Manage inventory categories
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Category
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


        <!-- Search -->
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
                            placeholder="Search category name or code..."
                            @keyup.enter="loadCategories(1)"
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


                    <!-- Buttons -->
                    <div class="col-md-4 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadCategories(1)"
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


        <!-- Table -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Category List
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
                                    Category Name
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Description
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
                                    colspan="6"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div
                                        class="text-muted mt-2"
                                    >
                                        Loading categories...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    categories.length === 0
                                "
                            >

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-tags fs-2 d-block mb-2"
                                    ></i>

                                    No inventory categories found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    category,
                                    index
                                ) in categories"
                                :key="category.id"
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
                                        {{ category.name }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        category.code || '-'
                                    }}

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            category.description
                                        "
                                        class="text-muted"
                                    >
                                        {{
                                            category.description
                                        }}
                                    </span>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            category.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >

                                        {{
                                            category.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}

                                    </span>

                                </td>


                                <td
                                    class="text-center"
                                >

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        :disabled="
                                            actionId === category.id
                                        "
                                        @click="
                                            openEdit(category)
                                        "
                                    >
                                        <i
                                            class="bi bi-pencil"
                                        ></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        :disabled="
                                            actionId === category.id
                                        "
                                        @click="
                                            toggleStatus(category)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === category.id
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
                                            actionId === category.id
                                        "
                                        @click="
                                            deleteCategory(category)
                                        "
                                    >
                                        <i
                                            class="bi bi-trash"
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

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveCategory"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Inventory Category'
                                        : 'Add Inventory Category'
                                }}

                            </h5>


                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <!-- Form error -->
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

                                        Category Name

                                        <span
                                            class="text-danger"
                                        >
                                            *
                                        </span>

                                    </label>


                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="e.g. Stationery"
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
                                        Category Code
                                    </label>


                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="e.g. STAT"
                                    >


                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
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


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>


                                    <textarea
                                        v-model="form.description"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Category description..."
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

const categories =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const actionId =
    ref(null);

const showModal =
    ref(false);

const editing =
    ref(false);

const editingId =
    ref(null);

const error =
    ref('');

const success =
    ref('');

const formError =
    ref('');

const formErrors =
    ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters =
    ref({

        search: '',

        status: '',

        per_page: 20

    });


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pagination =
    ref({

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

const emptyForm =
    () => ({

        name: '',

        code: '',

        description: '',

        status: true

    });


const form =
    ref(
        emptyForm()
    );


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| Same authentication pattern as the working
| Book Categories module.
|
*/

const getAuthConfig =
    () => {

        const token =

            localStorage.getItem(
                'token'
            ) ||

            localStorage.getItem(
                'auth_token'
            ) ||

            localStorage.getItem(
                'access_token'
            ) ||

            localStorage.getItem(
                'authToken'
            ) ||

            sessionStorage.getItem(
                'token'
            ) ||

            sessionStorage.getItem(
                'auth_token'
            ) ||

            sessionStorage.getItem(
                'access_token'
            ) ||

            sessionStorage.getItem(
                'authToken'
            );


        const config = {

            withCredentials: true,

            headers: {

                Accept:
                    'application/json'

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
| Load Categories
|--------------------------------------------------------------------------
*/

const loadCategories =
    async (
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

                    '/api/inventory/categories',

                    {

                        ...getAuthConfig(),

                        params

                    }

                );


            const result =
                response.data?.data;


            categories.value =
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
                'Inventory category loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load inventory categories.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreate =
    () => {

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

const openEdit =
    (category) => {

        editing.value = true;

        editingId.value =
            category.id;


        form.value = {

            name:
                category.name ||
                '',

            code:
                category.code ||
                '',

            description:
                category.description ||
                '',

            status:
                Boolean(
                    category.status
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

const closeModal =
    () => {

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

const saveCategory =
    async () => {

        if (saving.value) {

            return;

        }


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

                        `/api/inventory/categories/${editingId.value}`,

                        payload,

                        getAuthConfig()

                    );

            } else {

                response =
                    await axios.post(

                        '/api/inventory/categories',

                        payload,

                        getAuthConfig()

                    );

            }


            success.value =
                response.data?.message ||
                'Inventory category saved successfully.';


            showModal.value = false;


            await loadCategories(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory category save error:',
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
                    'Unable to save inventory category.';

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

const toggleStatus =
    async (
        category
    ) => {

        const newStatus =
            !Boolean(
                category.status
            );


        if (
            !window.confirm(

                `Change "${category.name}" status to ${
                    newStatus
                        ? 'Active'
                        : 'Inactive'
                }?`

            )
        ) {

            return;

        }


        actionId.value =
            category.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(

                    `/api/inventory/categories/${category.id}/status`,

                    {

                        status:
                            newStatus

                    },

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory category status updated successfully.';


            await loadCategories(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory category status error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update inventory category status.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteCategory =
    async (
        category
    ) => {

        if (
            !window.confirm(

                `Delete inventory category "${category.name}"?`

            )
        ) {

            return;

        }


        actionId.value =
            category.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(

                    `/api/inventory/categories/${category.id}`,

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory category deleted successfully.';


            let page =
                pagination.value.current_page;


            /*
             * If deleting the last record
             * on the current page,
             * go back one page.
             */

            if (
                categories.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadCategories(
                page
            );


        } catch (err) {

            console.error(
                'Delete inventory category error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete inventory category.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters =
    () => {

        filters.value = {

            search: '',

            status: '',

            per_page: 20

        };


        loadCategories(1);

    };


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const changePage =
    (page) => {

        if (
            page < 1 ||
            page >
                pagination.value.last_page
        ) {

            return;

        }


        loadCategories(
            page
        );

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadCategories(1);

    }
);

</script>