<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Authors
                </h4>

                <small class="text-muted">
                    Manage library book authors
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Author
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

                    <div class="col-md-5">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Search name, code, email or phone..."
                            @keyup.enter="loadAuthors(1)"
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
                            @click="loadAuthors(1)"
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


        <!-- Author Table -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Author List
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
                                    Author
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Phone
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

                                    <div class="spinner-border text-primary"></div>

                                    <div class="text-muted mt-2">
                                        Loading authors...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    authors.length === 0
                                "
                            >

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-person-x fs-2 d-block mb-2"
                                    ></i>

                                    No authors found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    author,
                                    index
                                ) in authors"
                                :key="author.id"
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
                                        {{ author.name }}
                                    </strong>

                                    <small
                                        v-if="author.biography"
                                        class="d-block text-muted text-truncate"
                                        style="max-width: 300px"
                                    >
                                        {{ author.biography }}
                                    </small>

                                </td>


                                <td>
                                    {{ author.code || '-' }}
                                </td>


                                <td>
                                    {{ author.email || '-' }}
                                </td>


                                <td>
                                    {{ author.phone || '-' }}
                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            author.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            author.status
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
                                            openEdit(author)
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
                                            actionId === author.id
                                        "
                                        @click="
                                            toggleStatus(author)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === author.id
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
                                            actionId === author.id
                                        "
                                        @click="
                                            deleteAuthor(author)
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
                        @submit.prevent="saveAuthor"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Author'
                                        : 'Add Author'
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
                                        Author Name
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
                                        placeholder="Author name"
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
                                        Author Code
                                    </label>

                                    <input
                                        v-model="form.code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.code
                                        }"
                                        placeholder="AUTH-001"
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
                                        placeholder="author@example.com"
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


                                <!-- Biography -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Biography
                                    </label>

                                    <textarea
                                        v-model="form.biography"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Author biography..."
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

const authors =
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
| Form
|--------------------------------------------------------------------------
*/

const emptyForm =
    () => ({

        name: '',

        code: '',

        email: '',

        phone: '',

        biography: '',

        status: true

    });


const form =
    ref(
        emptyForm()
    );


/*
|--------------------------------------------------------------------------
| Auth config
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
| Load Authors
|--------------------------------------------------------------------------
*/

const loadAuthors =
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
                    '/api/authors',
                    {
                        ...getAuthConfig(),

                        params
                    }
                );


            const result =
                response.data?.data;


            authors.value =
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
                'Author loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load authors.';

        } finally {

            loading.value = false;

        }

    };


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
    (author) => {

        editing.value = true;

        editingId.value =
            author.id;


        form.value = {

            name:
                author.name ||
                '',

            code:
                author.code ||
                '',

            email:
                author.email ||
                '',

            phone:
                author.phone ||
                '',

            biography:
                author.biography ||
                '',

            status:
                Boolean(
                    author.status
                )

        };


        formErrors.value = {};

        formError.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Close
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

const saveAuthor =
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

                email:
                    form.value.email ||
                    null,

                phone:
                    form.value.phone ||
                    null,

                biography:
                    form.value.biography ||
                    null,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(
                        `/api/authors/${editingId.value}`,
                        payload,
                        getAuthConfig()
                    );

            } else {

                response =
                    await axios.post(
                        '/api/authors',
                        payload,
                        getAuthConfig()
                    );

            }


            success.value =
                response.data?.message ||
                'Author saved successfully.';


            showModal.value = false;


            await loadAuthors(
                pagination.value.current_page
            );

        } catch (err) {

            console.error(
                'Author save error:',
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
                    'Unable to save author.';

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
        author
    ) => {

        const newStatus =
            !Boolean(
                author.status
            );


        if (
            !window.confirm(
                `Change "${author.name}" status to ${
                    newStatus
                        ? 'Active'
                        : 'Inactive'
                }?`
            )
        ) {
            return;
        }


        actionId.value =
            author.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(
                    `/api/authors/${author.id}/status`,
                    {
                        status:
                            newStatus
                    },
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Author status updated successfully.';


            await loadAuthors(
                pagination.value.current_page
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to update author status.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteAuthor =
    async (
        author
    ) => {

        if (
            !window.confirm(
                `Delete author "${author.name}"?`
            )
        ) {
            return;
        }


        actionId.value =
            author.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(
                    `/api/authors/${author.id}`,
                    getAuthConfig()
                );


            success.value =
                response.data?.message ||
                'Author deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                authors.value.length === 1 &&
                page > 1
            ) {
                page--;
            }


            await loadAuthors(
                page
            );

        } catch (err) {

            error.value =
                err.response?.data?.message ||
                'Unable to delete author.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Reset filters
|--------------------------------------------------------------------------
*/

const resetFilters =
    () => {

        filters.value = {

            search: '',

            status: '',

            per_page: 20

        };


        loadAuthors(1);

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

        loadAuthors(page);

    };


/*
|--------------------------------------------------------------------------
| Initial load
|--------------------------------------------------------------------------
*/

onMounted(
    () => {

        loadAuthors(1);

    }
);

</script>