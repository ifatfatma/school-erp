<template>
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Permissions</h4>
                <small class="text-muted">
                    Manage system permissions
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Permission
            </button>
        </div>


        <!-- SUCCESS -->
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


        <!-- ERROR -->
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


        <!-- SEARCH -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search Permission
                        </label>

                        <input
                            v-model="search"
                            type="text"
                            class="form-control"
                            placeholder="Search permission name..."
                            @keyup.enter="loadPermissions(1)"
                        />

                    </div>


                    <div class="col-md-6 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadPermissions(1)"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetSearch"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- LIST -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Permission List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} permissions
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="70">
                                    #
                                </th>

                                <th>
                                    Permission Name
                                </th>

                                <th>
                                    Guard
                                </th>

                                <th>
                                    Created
                                </th>

                                <th width="180">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- LOADING -->
                            <tr v-if="loading">

                                <td
                                    colspan="5"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading permissions...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->
                            <tr
                                v-else-if="permissions.length === 0"
                            >

                                <td
                                    colspan="5"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-key fs-2 d-block mb-2"
                                    ></i>

                                    No permissions found.

                                </td>

                            </tr>


                            <!-- DATA -->
                            <tr
                                v-for="(permission, index) in permissions"
                                :key="permission.id"
                            >

                                <td>

                                    {{
                                        (
                                            (
                                                pagination.current_page -
                                                1
                                            ) *
                                            pagination.per_page
                                        ) +
                                        index +
                                        1
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{ permission.name }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge bg-light text-dark border"
                                    >
                                        {{
                                            permission.guard_name ||
                                            'sanctum'
                                        }}
                                    </span>

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            permission.created_at
                                        )
                                    }}

                                </td>


                                <td>

                                    <div class="d-flex gap-1">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit"
                                            @click="
                                                openEdit(permission)
                                            "
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete"
                                            @click="
                                                deletePermission(
                                                    permission
                                                )
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- PAGINATION -->
            <div
                v-if="pagination.last_page > 1"
                class="card-footer d-flex justify-content-between align-items-center"
            >

                <span class="text-muted">

                    Showing
                    {{ pagination.from || 0 }}
                    -
                    {{ pagination.to || 0 }}
                    of
                    {{ pagination.total }}

                </span>


                <div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary me-2"
                        :disabled="
                            pagination.current_page <= 1 ||
                            loading
                        "
                        @click="
                            loadPermissions(
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
                        class="btn btn-sm btn-outline-secondary ms-2"
                        :disabled="
                            pagination.current_page >=
                                pagination.last_page ||
                            loading
                        "
                        @click="
                            loadPermissions(
                                pagination.current_page + 1
                            )
                        "
                    >
                        Next
                    </button>

                </div>

            </div>

        </div>


        <!-- CREATE / EDIT MODAL -->
        <div
            v-if="showModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-md modal-dialog-centered"
            >

                <div class="modal-content">

                    <form @submit.prevent="savePermission">

                        <!-- HEADER -->
                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Permission'
                                        : 'Add Permission'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <!-- BODY -->
                        <div class="modal-body">

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="mb-3">

                                <label class="form-label">

                                    Permission Name
                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <input
                                    v-model.trim="form.name"
                                    type="text"
                                    class="form-control"
                                    placeholder="Example: user.view"
                                    maxlength="255"
                                    required
                                />

                                <small class="text-muted">
                                    Example:
                                    user.view,
                                    user.create,
                                    user.update,
                                    user.delete
                                </small>

                            </div>


                            <div class="mb-3">

                                <label class="form-label">
                                    Guard
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="sanctum"
                                    readonly
                                />

                            </div>

                        </div>


                        <!-- FOOTER -->
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
                                :disabled="
                                    saving ||
                                    !form.name.trim()
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update Permission'
                                        : 'Create Permission'
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
| STATE
|--------------------------------------------------------------------------
*/

const permissions = ref([]);

const loading = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const search = ref('');

const showModal = ref(false);

const editing = ref(false);

const selectedPermission = ref(null);


/*
|--------------------------------------------------------------------------
| PAGINATION
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
| FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    name: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| AUTH CONFIG
|--------------------------------------------------------------------------
*/

const authConfig = () => {

    const token =
        localStorage.getItem('token') ||
        localStorage.getItem('access_token') ||
        sessionStorage.getItem('token') ||
        sessionStorage.getItem('access_token');


    const config = {

        withCredentials: true,

        headers: {

            Accept:
                'application/json',

            'Content-Type':
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
| LOAD PERMISSIONS
|--------------------------------------------------------------------------
*/

const loadPermissions = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/administrator/permissions',

                {

                    ...authConfig(),

                    params: {

                        page,

                        per_page:
                            pagination.value.per_page,

                        search:
                            search.value.trim() ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        /*
        |--------------------------------------------------------------------------
        | PAGINATED RESPONSE
        |--------------------------------------------------------------------------
        */

        if (
            result &&
            Array.isArray(result.data)
        ) {

            permissions.value =
                result.data;


            pagination.value = {

                current_page:
                    Number(
                        result.current_page || 1
                    ),

                last_page:
                    Number(
                        result.last_page || 1
                    ),

                per_page:
                    Number(
                        result.per_page || 20
                    ),

                total:
                    Number(
                        result.total || 0
                    ),

                from:
                    Number(
                        result.from || 0
                    ),

                to:
                    Number(
                        result.to || 0
                    )

            };

        } else {

            /*
            |--------------------------------------------------------------------------
            | SIMPLE ARRAY RESPONSE
            |--------------------------------------------------------------------------
            */

            permissions.value =
                Array.isArray(result)
                    ? result
                    : [];

            pagination.value = {

                current_page: 1,

                last_page: 1,

                per_page:
                    permissions.value.length,

                total:
                    permissions.value.length,

                from:
                    permissions.value.length
                        ? 1
                        : 0,

                to:
                    permissions.value.length

            };

        }

    } catch (e) {

        console.error(
            'Load Permissions Error:',
            e
        );


        error.value =
            e.response?.data?.message ||
            'Unable to load permissions.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedPermission.value = null;

    form.value =
        emptyForm();

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| EDIT
|--------------------------------------------------------------------------
*/

const openEdit = (
    permission
) => {

    editing.value = true;

    selectedPermission.value =
        permission;

    form.value = {

        name:
            permission.name || ''

    };

    formError.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const savePermission = async () => {

    formError.value = '';

    error.value = '';

    success.value = '';


    if (!form.value.name.trim()) {

        formError.value =
            'Permission name is required.';

        return;

    }


    saving.value = true;


    try {

        const payload = {

            name:
                form.value.name.trim(),

            guard_name:
                'sanctum'

        };


        let response;


        if (
            editing.value &&
            selectedPermission.value
        ) {

            response =
                await axios.put(

                    `/api/administrator/permissions/${selectedPermission.value.id}`,

                    payload,

                    authConfig()

                );

        } else {

            response =
                await axios.post(

                    '/api/administrator/permissions',

                    payload,

                    authConfig()

                );

        }


        success.value =
            response.data?.message ||
            'Permission saved successfully.';


        showModal.value = false;


        await loadPermissions(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (e) {

        console.error(
            'Save Permission Error:',
            e
        );


        if (
            e.response?.status === 422
        ) {

            const errors =
                e.response?.data?.errors ||
                {};


            const firstError =
                Object.values(errors)[0];


            formError.value =
                Array.isArray(firstError)
                    ? firstError[0]
                    : (
                        firstError ||
                        e.response?.data?.message ||
                        'Validation failed.'
                    );

        } else {

            formError.value =
                e.response?.data?.message ||
                'Unable to save permission.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deletePermission = async (
    permission
) => {

    if (
        !confirm(
            `Delete permission "${permission.name}"?`
        )
    ) {

        return;

    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/administrator/permissions/${permission.id}`,

                authConfig()

            );


        success.value =
            response.data?.message ||
            'Permission deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            permissions.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadPermissions(page);


    } catch (e) {

        console.error(
            'Delete Permission Error:',
            e
        );


        error.value =
            e.response?.data?.message ||
            'Unable to delete permission.';

    }

};


/*
|--------------------------------------------------------------------------
| CLOSE MODAL
|--------------------------------------------------------------------------
*/

const closeModal = () => {

    if (saving.value) {
        return;
    }


    showModal.value = false;

    formError.value = '';

};


/*
|--------------------------------------------------------------------------
| RESET SEARCH
|--------------------------------------------------------------------------
*/

const resetSearch = () => {

    search.value = '';

    loadPermissions(1);

};


/*
|--------------------------------------------------------------------------
| DATE FORMAT
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

    if (!value) {
        return '-';
    }


    const date =
        new Date(value);


    if (
        Number.isNaN(
            date.getTime()
        )
    ) {

        return value;

    }


    return date.toLocaleDateString(
        'en-GB'
    );

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadPermissions(1);

});

</script>


<style scoped>

.table th {
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.modal {
    z-index: 1055;
}

.form-control {
    min-height: 38px;
}

</style>