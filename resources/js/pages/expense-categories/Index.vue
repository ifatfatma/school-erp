<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Expense Categories
                </h4>

                <small class="text-muted">
                    Manage expense categories
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


        <!-- Error -->
        <div
            v-if="error"
            class="alert alert-danger"
        >
            {{ error }}
        </div>


        <!-- Success -->
        <div
            v-if="success"
            class="alert alert-success"
        >
            {{ success }}
        </div>


        <!-- Filters -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Category name / code"
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


                    <!-- Per Page -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Per Page
                        </label>

                        <select
                            v-model="filters.per_page"
                            class="form-select"
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

                </div>


                <div class="mt-3">

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        :disabled="loading"
                        @click="loadCategories(1)"
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


        <!-- List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Expense Category List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="70">
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

                                    <div class="mt-2 text-muted">
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
                                    class="text-center py-5 text-muted"
                                >
                                    No expense categories found.
                                </td>

                            </tr>


                            <!-- Records -->
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
                                            category.description
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
                                                : 'bg-danger'
                                        "
                                    >

                                        {{
                                            category.status
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
                                            openEdit(
                                                category
                                            )
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Toggle -->
                                    <button
                                        type="button"
                                        class="btn btn-sm"
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
                                                category.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi"
                                            :class="
                                                category.status
                                                    ? 'bi-toggle-off'
                                                    : 'bi-toggle-on'
                                            "
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger ms-1"
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


        <!-- Add / Edit Modal -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0, 0, 0, 0.5);"
        >

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form @submit.prevent="saveCategory">

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Expense Category'
                                        : 'Add Expense Category'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                @click="closeModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <!-- Form Error -->
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

                                        Category Name
                                        <span class="text-danger">
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
                                        placeholder="e.g. Electricity"
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
                                        Code
                                    </label>

                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="e.g. ELEC"
                                    >

                                    <div
                                        v-if="formErrors.code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.code }}
                                    </div>

                                </div>


                                <!-- Description -->
                                <div class="col-md-8">

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
                                        placeholder="Enter description"
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
                                        ? 'Update Category'
                                        : 'Save Category'
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
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const categories = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const error = ref("");

const success = ref("");

const formError = ref("");

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: "",

    status: "",

    per_page: 20

});


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    name: "",

    code: "",

    description: "",

    status: true

});


const form = ref(
    emptyForm()
);


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
| Authentication
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

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
            Accept: "application/json"
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

const loadCategories = async (
    page = 1
) => {

    loading.value = true;

    error.value = "";


    try {

        const params = {

            page,

            per_page:
                filters.value.per_page,

            search:
                filters.value.search ||
                undefined,

            status:
                filters.value.status === ""
                    ? undefined
                    : filters.value.status

        };


        const response =
            await axios.get(
                "/api/expense-categories",
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
                result?.current_page || 1,

            last_page:
                result?.last_page || 1,

            per_page:
                result?.per_page ||
                filters.value.per_page,

            total:
                result?.total || 0,

            from:
                result?.from || 0,

            to:
                result?.to || 0

        };

    } catch (err) {

        console.error(
            "Expense category list error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to load expense categories.";

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

    formError.value = "";

    error.value = "";

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit = (
    category
) => {

    editing.value = true;

    editingId.value =
        category.id;


    form.value = {

        name:
            category.name || "",

        code:
            category.code || "",

        description:
            category.description || "",

        status:
            Boolean(
                category.status
            )

    };


    formErrors.value = {};

    formError.value = "";

    error.value = "";

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
| Save
|--------------------------------------------------------------------------
*/

const saveCategory = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = "";

    error.value = "";

    success.value = "";


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

            response = await axios.put(
                `/api/expense-categories/${editingId.value}`,
                payload,
                getAuthConfig()
            );

        } else {

            response = await axios.post(
                "/api/expense-categories",
                payload,
                getAuthConfig()
            );

        }


        success.value =
            response.data?.message ||
            (
                editing.value
                    ? "Expense category updated successfully."
                    : "Expense category created successfully."
            );


        showModal.value = false;


        await loadCategories(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Save expense category error:",
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
                "Please correct the errors.";

        } else {

            formError.value =
                err.response?.data?.message ||
                "Unable to save expense category.";

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
    category
) => {

    const action =
        category.status
            ? "deactivate"
            : "activate";


    if (
        !window.confirm(
            `Are you sure you want to ${action} "${category.name}"?`
        )
    ) {

        return;

    }


    actionId.value =
        category.id;

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.patch(
                `/api/expense-categories/${category.id}/toggle-status`,
                {},
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Status updated successfully.";


        await loadCategories(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            "Toggle category error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to update category status.";

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteCategory = async (
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

    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(
                `/api/expense-categories/${category.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            "Expense category deleted successfully.";


        let page =
            pagination.value.current_page;


        if (
            categories.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadCategories(page);

    } catch (err) {

        console.error(
            "Delete category error:",
            err
        );


        error.value =
            err.response?.data?.message ||
            "Unable to delete expense category.";

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

        search: "",

        status: "",

        per_page: 20

    };


    loadCategories(1);

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
        page > pagination.value.last_page
    ) {

        return;

    }


    loadCategories(page);

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadCategories(1);

});

</script>