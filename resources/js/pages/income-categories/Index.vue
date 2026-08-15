<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Income Categories
                </h4>

                <small class="text-muted">
                    Manage categories for school income
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Income Category
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

                    <!-- Search -->
                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Search name, code or description..."
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


                    <!-- Search -->
                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            :disabled="loading"
                            @click="loadCategories(1)"
                        >

                            <span
                                v-if="loading"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            <i
                                v-else
                                class="bi bi-search me-1"
                            ></i>

                            Search

                        </button>

                    </div>


                    <!-- Reset -->
                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-outline-secondary w-100"
                            :disabled="loading"
                            @click="resetFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Summary -->
        <div class="row g-3 mb-3">

            <div class="col-md-4">

                <div class="card border-start border-primary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Categories
                        </small>

                        <h4 class="mb-0">
                            {{ pagination.total }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-start border-success border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Active
                        </small>

                        <h4 class="mb-0 text-success">
                            {{ activeCount }}
                        </h4>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-start border-secondary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Inactive
                        </small>

                        <h4 class="mb-0 text-secondary">
                            {{ inactiveCount }}
                        </h4>

                    </div>

                </div>

            </div>

        </div>


        <!-- Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Income Category List
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
                                    Name
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

                                <th>
                                    Created
                                </th>

                                <th
                                    width="170"
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
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-inbox fs-2 d-block mb-2"
                                    ></i>

                                    No income categories found.

                                </td>

                            </tr>


                            <!-- Data -->
                            <tr
                                v-else
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

                                    <span
                                        v-if="category.code"
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ category.code }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        -
                                    </span>

                                </td>


                                <td>

                                    <span
                                        v-if="
                                            category.description
                                        "
                                    >
                                        {{
                                            truncate(
                                                category.description
                                            )
                                        }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
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


                                <td>

                                    {{
                                        formatDate(
                                            category.created_at
                                        )
                                    }}

                                </td>


                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                category
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm me-1"
                                        :class="
                                            category.status
                                                ? 'btn-outline-warning'
                                                : 'btn-outline-success'
                                        "
                                        :title="
                                            category.status
                                                ? 'Deactivate'
                                                : 'Activate'
                                        "
                                        :disabled="
                                            actionId ===
                                            category.id
                                        "
                                        @click="
                                            toggleStatus(
                                                category
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                    category.id &&
                                                actionType ===
                                                    'status'
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            :class="
                                                category.status
                                                    ? 'bi bi-pause'
                                                    : 'bi bi-check-lg'
                                            "
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            category.id
                                        "
                                        @click="
                                            deleteCategory(
                                                category
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                    category.id &&
                                                actionType ===
                                                    'delete'
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


        <!-- Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5);"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="
                            saveCategory
                        "
                    >

                        <!-- Modal Header -->
                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Income Category'
                                        : 'Add Income Category'
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

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Name -->
                                <div class="col-md-8">

                                    <label class="form-label">

                                        Name
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="
                                            form.name
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.name
                                        }"
                                        placeholder="e.g. Donation"
                                    >

                                    <div
                                        v-if="
                                            formErrors.name
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.name
                                        }}
                                    </div>

                                </div>


                                <!-- Code -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Code
                                    </label>

                                    <input
                                        v-model="
                                            form.code
                                        "
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="e.g. DON"
                                    >

                                    <div
                                        v-if="
                                            formErrors.code
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.code
                                        }}
                                    </div>

                                </div>


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="
                                            form.description
                                        "
                                        rows="4"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.description
                                        }"
                                        placeholder="Enter description..."
                                    ></textarea>

                                    <div
                                        v-if="
                                            formErrors.description
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.description
                                        }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-12">

                                    <div class="form-check form-switch">

                                        <input
                                            id="incomeCategoryStatus"
                                            v-model="
                                                form.status
                                            "
                                            class="form-check-input"
                                            type="checkbox"
                                        >

                                        <label
                                            class="form-check-label"
                                            for="incomeCategoryStatus"
                                        >
                                            Active
                                        </label>

                                    </div>

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
    computed,
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

const actionType =
    ref('');

const error =
    ref('');

const success =
    ref('');

const formError =
    ref('');

const formErrors =
    ref({});

const showModal =
    ref(false);

const editing =
    ref(false);

const editingId =
    ref(null);


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
| Form
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
*/

const getAuthConfig =
    () => {

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
                    '/api/income-categories',
                    {
                        ...getAuthConfig(),
                        params
                    }
                );


            const result =
                response.data?.data;


            categories.value =
                result?.data || [];


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
                'Income category error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load income categories.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const activeCount =
    computed(() => {

        return categories.value.filter(
            category =>
                Boolean(
                    category.status
                )
        ).length;

    });


const inactiveCount =
    computed(() => {

        return categories.value.filter(
            category =>
                !Boolean(
                    category.status
                )
        ).length;

    });


/*
|--------------------------------------------------------------------------
| Create
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
| Edit
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
                    Boolean(
                        form.value.status
                    )

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/income-categories/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        '/api/income-categories',
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                'Income category saved successfully.';


            showModal.value = false;


            await loadCategories(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Save income category error:',
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
                    'Unable to save income category.';

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


        actionId.value =
            category.id;

        actionType.value =
            'status';

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(
                    `/api/income-categories/${category.id}/status`,
                    {
                        status: newStatus
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Category status updated successfully.';


            await loadCategories(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Status error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update category status.';

        } finally {

            actionId.value = null;

            actionType.value = '';

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
                `Delete "${category.name}"?`
            )
        ) {

            return;

        }


        actionId.value =
            category.id;

        actionType.value =
            'delete';

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(
                    `/api/income-categories/${category.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Income category deleted successfully.';


            let page =
                pagination.value.current_page;


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
                'Delete category error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete income category.';

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


        loadCategories(page);

    };


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const truncate =
    (
        text,
        length = 60
    ) => {

        if (!text) {
            return '';
        }


        return text.length > length
            ? text.substring(
                0,
                length
            ) + '...'
            : text;

    };


const formatDate =
    (date) => {

        if (!date) {
            return '-';
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
            'en-IN',
            {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }
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