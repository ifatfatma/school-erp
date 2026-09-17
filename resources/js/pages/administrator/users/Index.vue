<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">Users</h4>

                <small class="text-muted">
                    Manage system users and their roles
                </small>
            </div>

            <button
                v-if="canCreate"
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add User
            </button>

        </div>


        <!-- =========================================================
             SUCCESS MESSAGE
        ========================================================== -->
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


        <!-- =========================================================
             ERROR MESSAGE
        ========================================================== -->
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


        <!-- =========================================================
             SEARCH
        ========================================================== -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <!-- SEARCH -->
                    <div class="col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="search"
                            type="text"
                            class="form-control"
                            placeholder="Name, username or email..."
                            @keyup.enter="loadUsers(1)"
                        >

                    </div>


                    <!-- STATUS -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            v-model="status"
                            class="form-select"
                            @change="loadUsers(1)"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option value="1">
                                Active
                            </option>

                            <option value="0">
                                Inactive
                            </option>

                        </select>

                    </div>


                    <!-- BUTTONS -->
                    <div class="col-md-3 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            @click="loadUsers(1)"
                        >
                            <i class="bi bi-search me-1"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetSearch"
                        >
                            Reset
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             USER TABLE
        ========================================================== -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    User List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} users
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle mb-0"
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
                                    Username
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    Roles
                                </th>

                                <th>
                                    Status
                                </th>

                                <th width="170">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- =================================================
                                 LOADING
                            ================================================== -->
                            <tr v-if="loading">

                                <td
                                    colspan="7"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                        role="status"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading users...
                                    </div>

                                </td>

                            </tr>


                            <!-- =================================================
                                 EMPTY
                            ================================================== -->
                            <tr v-else-if="users.length === 0">

                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >

                                    <i
                                        class="bi bi-people fs-2 d-block mb-2"
                                    ></i>

                                    No users found.

                                </td>

                            </tr>


                            <!-- =================================================
                                 USERS
                            ================================================== -->
                            <tr
                                v-for="(user, index) in users"
                                :key="user.id"
                            >

                                <!-- NUMBER -->
                                <td>

                                    {{
                                        ((pagination.current_page - 1)
                                        * pagination.per_page)
                                        + index
                                        + 1
                                    }}

                                </td>


                                <!-- NAME -->
                                <td>

                                    <strong>
                                        {{ user.name }}
                                    </strong>

                                </td>


                                <!-- USERNAME -->
                                <td>
                                    {{ user.username || '-' }}
                                </td>


                                <!-- EMAIL -->
                                <td>
                                    {{ user.email || '-' }}
                                </td>


                                <!-- ROLES -->
                                <td>

                                    <template
                                        v-if="
                                            user.roles &&
                                            user.roles.length
                                        "
                                    >

                                        <span
                                            v-for="role in user.roles"
                                            :key="
                                                'role-' +
                                                user.id +
                                                '-' +
                                                role.id
                                            "
                                            class="badge bg-primary me-1"
                                        >
                                            {{ role.name }}
                                        </span>

                                    </template>

                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        No Role
                                    </span>

                                </td>


                                <!-- STATUS -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            isActive(user)
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >

                                        {{
                                            isActive(user)
                                                ? 'Active'
                                                : 'Inactive'
                                        }}

                                    </span>

                                </td>


                                <!-- ACTIONS -->
                                <td>

                                    <div class="d-flex gap-1">

                                        <!-- ASSIGN ROLE -->
                                        <button
                                            v-if="canEdit"
                                            type="button"
                                            class="btn btn-sm btn-outline-success"
                                            title="Assign Roles"
                                            @click="openRoles(user)"
                                        >

                                            <i
                                                class="bi bi-shield-check"
                                            ></i>

                                        </button>


                                        <!-- EDIT -->
                                        <button
                                            v-if="canEdit"
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Edit User"
                                            @click="openEdit(user)"
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>

                                        </button>


                                        <!-- DELETE -->
                                        <button
                                            v-if="
                                                canDelete &&
                                                !isCurrentUser(user)
                                            "
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete User"
                                            @click="deleteUser(user)"
                                        >

                                            <i
                                                class="bi bi-trash"
                                            ></i>

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =========================================================
                 PAGINATION
            ========================================================== -->
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
                            loadUsers(
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
                            loadUsers(
                                pagination.current_page + 1
                            )
                        "
                    >
                        Next
                    </button>

                </div>

            </div>

        </div>


        <!-- =========================================================
             CREATE / EDIT USER MODAL
        ========================================================== -->
        <div
            v-if="showUserModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form @submit.prevent="saveUser">

                        <!-- HEADER -->
                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title mb-0">

                                    {{
                                        editing
                                            ? 'Edit User'
                                            : 'Add User'
                                    }}

                                </h5>

                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeUserModal"
                            ></button>

                        </div>


                        <!-- BODY -->
                        <div class="modal-body">

                            <!-- ERROR -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- NAME -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Name
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- USERNAME -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Username
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.username"
                                        type="text"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- EMAIL -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Email
                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="form-control"
                                        required
                                    >

                                </div>


                                <!-- STATUS -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="form.status"
                                        class="form-select"
                                    >

                                        <option :value="1">
                                            Active
                                        </option>

                                        <option :value="0">
                                            Inactive
                                        </option>

                                    </select>

                                </div>


                                <!-- PASSWORD -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Password

                                        <span
                                            v-if="!editing"
                                            class="text-danger"
                                        >
                                            *
                                        </span>

                                    </label>

                                    <input
                                        v-model="form.password"
                                        type="password"
                                        class="form-control"
                                        :required="!editing"
                                        autocomplete="new-password"
                                    >

                                    <small
                                        v-if="editing"
                                        class="text-muted"
                                    >
                                        Leave blank to keep current
                                        password.
                                    </small>

                                </div>


                                <!-- CONFIRM PASSWORD -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Confirm Password
                                    </label>

                                    <input
                                        v-model="
                                            form.password_confirmation
                                        "
                                        type="password"
                                        class="form-control"
                                        :required="
                                            !!form.password
                                        "
                                        autocomplete="new-password"
                                    >

                                </div>


                                <!-- ROLE -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Role
                                    </label>

                                    <select
                                        v-model="form.role_id"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Role
                                        </option>

                                        <option
                                            v-for="role in roles"
                                            :key="role.id"
                                            :value="role.id"
                                        >
                                            {{ role.name }}
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>


                        <!-- FOOTER -->
                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="saving"
                                @click="closeUserModal"
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
                                        ? 'Update User'
                                        : 'Create User'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =========================================================
             ASSIGN ROLES MODAL
        ========================================================== -->
        <div
            v-if="showRolesModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-md modal-dialog-centered"
            >

                <div class="modal-content">

                    <!-- HEADER -->
                    <div class="modal-header">

                        <div>

                            <h5 class="modal-title mb-0">
                                Assign Roles
                            </h5>

                            <small
                                v-if="selectedUser"
                                class="text-muted"
                            >
                                {{ selectedUser.name }}
                            </small>

                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            :disabled="savingRoles"
                            @click="closeRolesModal"
                        ></button>

                    </div>


                    <!-- BODY -->
                    <div class="modal-body">

                        <div
                            v-if="rolesError"
                            class="alert alert-danger"
                        >
                            {{ rolesError }}
                        </div>


                        <!-- LOADING -->
                        <div
                            v-if="rolesLoading"
                            class="text-center py-4"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="text-muted mt-2">
                                Loading roles...
                            </div>

                        </div>


                        <!-- ROLES -->
                        <template v-else>

                            <div
                                v-if="roles.length === 0"
                                class="text-muted text-center py-3"
                            >
                                No roles available.
                            </div>


                            <div
                                v-for="role in roles"
                                :key="'assign-role-' + role.id"
                                class="form-check border rounded p-2 mb-2"
                            >

                                <input
                                    :id="
                                        'user-role-' +
                                        role.id
                                    "
                                    v-model="selectedRoleIds"
                                    class="form-check-input ms-0 me-2"
                                    type="checkbox"
                                    :value="Number(role.id)"
                                >

                                <label
                                    :for="
                                        'user-role-' +
                                        role.id
                                    "
                                    class="form-check-label"
                                >
                                    {{ role.name }}
                                </label>

                            </div>

                        </template>

                    </div>


                    <!-- FOOTER -->
                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            :disabled="savingRoles"
                            @click="closeRolesModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="
                                savingRoles ||
                                rolesLoading
                            "
                            @click="saveRoles"
                        >

                            <span
                                v-if="savingRoles"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            Save Roles

                        </button>

                    </div>

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

import { useAuthStore } from '@/stores/auth';


/*
|--------------------------------------------------------------------------
| AUTH STORE
|--------------------------------------------------------------------------
|
| Your login response contains:
|
| user.user_type = "super_admin"
|
| user.roles = [
|     {
|         name: "Super Admin",
|         guard_name: "sanctum"
|     }
| ]
|
| The Pinia auth store already stores:
|
| this.user = response.data.user
|
*/

const authStore = useAuthStore();


const currentUser = computed(() => {

    return authStore.user;

});


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const users = ref([]);

const roles = ref([]);

const loading = ref(false);

const saving = ref(false);

const rolesLoading = ref(false);

const savingRoles = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const rolesError = ref('');

const search = ref('');

const status = ref('');

const showUserModal = ref(false);

const showRolesModal = ref(false);

const editing = ref(false);

const selectedUser = ref(null);

const selectedRoleIds = ref([]);


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
| EMPTY FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    name: '',

    username: '',

    email: '',

    password: '',

    password_confirmation: '',

    status: 1,

    role_id: ''

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| PERMISSION CHECK
|--------------------------------------------------------------------------
*/

const hasPermission = (permission) => {

    const user = currentUser.value;


    if (!user) {
        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    |
    | Your actual login response contains:
    |
    | "user_type": "super_admin"
    |
    */

    if (
        user.user_type === 'super_admin' ||
        user.user_type === 'superadmin'
    ) {

        return true;

    }


    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN ROLE
    |--------------------------------------------------------------------------
    */

    const userRoles =
        Array.isArray(user.roles)
            ? user.roles
            : [];


    const isSuperAdmin =
        userRoles.some(role => {

            const roleName =
                typeof role === 'string'
                    ? role
                    : role?.name;

            return (
                roleName &&
                roleName.toLowerCase() === 'super admin'
            );

        });


    if (isSuperAdmin) {
        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | NORMAL PERMISSIONS
    |--------------------------------------------------------------------------
    */

    const permissions =
        Array.isArray(user.permissions)
            ? user.permissions
            : [];


    return permissions.some(permissionItem => {

        if (
            typeof permissionItem === 'string'
        ) {

            return (
                permissionItem === permission
            );

        }


        return (
            permissionItem?.name === permission
        );

    });

};


/*
|--------------------------------------------------------------------------
| USER PERMISSIONS
|--------------------------------------------------------------------------
*/

const canCreate = computed(() => {

    return hasPermission(
        'user.create'
    );

});


const canEdit = computed(() => {

    return hasPermission(
        'user.update'
    );

});


const canDelete = computed(() => {

    return hasPermission(
        'user.delete'
    );

});


/*
|--------------------------------------------------------------------------
| AXIOS CONFIG
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
| LOAD USERS
|--------------------------------------------------------------------------
*/

const loadUsers = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/administrator/users',

                {
                    ...authConfig(),

                    params: {

                        page: page,

                        per_page:
                            pagination.value.per_page,

                        search:
                            search.value.trim() ||
                            undefined,

                        status:
                            status.value !== ''
                                ? status.value
                                : undefined

                    }

                }

            );


        /*
        |--------------------------------------------------------------------------
        | EXPECTED RESPONSE
        |--------------------------------------------------------------------------
        |
        | {
        |     success: true,
        |     data: {
        |         data: [...]
        |         current_page: 1,
        |         ...
        |     }
        | }
        |
        */

        const result =
            response.data?.data;


        users.value =
            result?.data || [];


        pagination.value = {

            current_page:
                Number(
                    result?.current_page || 1
                ),

            last_page:
                Number(
                    result?.last_page || 1
                ),

            per_page:
                Number(
                    result?.per_page || 20
                ),

            total:
                Number(
                    result?.total || 0
                ),

            from:
                Number(
                    result?.from || 0
                ),

            to:
                Number(
                    result?.to || 0
                )

        };


    } catch (e) {

        console.error(
            'Load Users Error:',
            e
        );


        error.value =
            e.response?.data?.message ||
            'Unable to load users.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| LOAD ROLES
|--------------------------------------------------------------------------
*/

const loadRoles = async () => {

    try {

        const response =
            await axios.get(

                '/api/administrator/users/options',

                authConfig()

            );


        roles.value =
            response.data?.data?.roles ||
            [];


    } catch (e) {

        console.error(
            'Load Roles Error:',
            e
        );


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await axios.get(

                    '/api/administrator/roles',

                    {

                        ...authConfig(),

                        params: {
                            per_page: 100
                        }

                    }

                );


            roles.value =
                response.data?.data?.data ||
                [];


        } catch (roleError) {

            console.error(
                'Fallback Roles Error:',
                roleError
            );

            roles.value = [];

        }

    }

};


/*
|--------------------------------------------------------------------------
| CREATE USER
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedUser.value = null;

    form.value =
        emptyForm();

    formError.value = '';

    showUserModal.value = true;

};


/*
|--------------------------------------------------------------------------
| EDIT USER
|--------------------------------------------------------------------------
*/

const openEdit = async (
    user
) => {

    editing.value = true;

    selectedUser.value = user;

    formError.value = '';


    form.value = {

        name:
            user.name || '',

        username:
            user.username || '',

        email:
            user.email || '',

        password: '',

        password_confirmation: '',

        status:
            user.status !== undefined
                ? Number(user.status)
                : 1,

        role_id:
            user.roles?.[0]?.id ||
            ''

    };


    showUserModal.value = true;


    /*
    |--------------------------------------------------------------------------
    | Get fresh user data
    |--------------------------------------------------------------------------
    */

    try {

        const response =
            await axios.get(

                `/api/administrator/users/${user.id}`,

                authConfig()

            );


        const data =
            response.data?.data;


        if (data) {

            form.value.name =
                data.name || '';

            form.value.username =
                data.username || '';

            form.value.email =
                data.email || '';

            form.value.status =
                data.status !== undefined
                    ? Number(data.status)
                    : 1;

            form.value.role_id =
                data.roles?.[0]?.id ||
                '';

        }


    } catch (e) {

        console.error(
            'Load User Error:',
            e
        );

    }

};


/*
|--------------------------------------------------------------------------
| SAVE USER
|--------------------------------------------------------------------------
*/

const saveUser = async () => {

    saving.value = true;

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            name:
                form.value.name.trim(),

            username:
                form.value.username.trim(),

            email:
                form.value.email.trim(),

            status:
                Number(
                    form.value.status
                ),

            role_id:
                form.value.role_id
                    ? Number(
                        form.value.role_id
                    )
                    : null

        };


        /*
        |--------------------------------------------------------------------------
        | PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            form.value.password
        ) {

            payload.password =
                form.value.password;

            payload.password_confirmation =
                form.value.password_confirmation;

        }


        let response;


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if (
            editing.value &&
            selectedUser.value
        ) {

            response =
                await axios.put(

                    `/api/administrator/users/${selectedUser.value.id}`,

                    payload,

                    authConfig()

                );


        } else {


            /*
            |--------------------------------------------------------------------------
            | CREATE
            |--------------------------------------------------------------------------
            */

            response =
                await axios.post(

                    '/api/administrator/users',

                    payload,

                    authConfig()

                );

        }


        success.value =
            response.data?.message ||
            'User saved successfully.';


        showUserModal.value = false;


        await loadUsers(
            editing.value
                ? pagination.value.current_page
                : 1
        );


    } catch (e) {

        console.error(
            'Save User Error:',
            e
        );


        if (
            e.response?.status === 422
        ) {

            const errors =
                e.response.data?.errors ||
                {};


            const firstError =
                Object.values(errors)[0];


            formError.value =
                Array.isArray(firstError)
                    ? firstError[0]
                    : (
                        firstError ||
                        e.response.data?.message ||
                        'Validation failed.'
                    );


        } else {

            formError.value =
                e.response?.data?.message ||
                'Unable to save user.';

        }


    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| OPEN ROLES MODAL
|--------------------------------------------------------------------------
*/

const openRoles = async (
    user
) => {

    selectedUser.value = user;

    selectedRoleIds.value = [];

    rolesError.value = '';

    rolesLoading.value = true;

    showRolesModal.value = true;


    try {

        /*
        |--------------------------------------------------------------------------
        | LOAD AVAILABLE ROLES
        |--------------------------------------------------------------------------
        */

        await loadRoles();


        /*
        |--------------------------------------------------------------------------
        | LOAD USER ROLES
        |--------------------------------------------------------------------------
        */

        const response =
            await axios.get(

                `/api/administrator/users/${user.id}/roles`,

                authConfig()

            );


        const assignedRoles =
            response.data?.data?.roles ||
            [];


        selectedRoleIds.value =
            assignedRoles.map(
                role =>
                    Number(
                        role.id ??
                        role
                    )
            );


    } catch (e) {

        console.error(
            'Load User Roles Error:',
            e
        );


        /*
        |--------------------------------------------------------------------------
        | FALLBACK FROM TABLE ROW
        |--------------------------------------------------------------------------
        */

        selectedRoleIds.value =
            (user.roles || []).map(
                role =>
                    Number(
                        role.id
                    )
            );


        rolesError.value =
            e.response?.data?.message ||
            'Unable to load user roles.';

    } finally {

        rolesLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| SAVE USER ROLES
|--------------------------------------------------------------------------
*/

const saveRoles = async () => {

    if (!selectedUser.value) {
        return;
    }


    savingRoles.value = true;

    rolesError.value = '';


    try {

        const response =
            await axios.put(

                `/api/administrator/users/${selectedUser.value.id}/roles`,

                {

                    roles:
                        selectedRoleIds.value.map(
                            id =>
                                Number(id)
                        )

                },

                authConfig()

            );


        success.value =
            response.data?.message ||
            'User roles updated successfully.';


        showRolesModal.value = false;


        await loadUsers(
            pagination.value.current_page
        );


    } catch (e) {

        console.error(
            'Save Roles Error:',
            e
        );


        rolesError.value =
            e.response?.data?.message ||
            'Unable to update user roles.';

    } finally {

        savingRoles.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE USER
|--------------------------------------------------------------------------
*/

const deleteUser = async (
    user
) => {

    if (
        isCurrentUser(user)
    ) {

        error.value =
            'You cannot delete your own account.';

        return;

    }


    const confirmed =
        window.confirm(
            `Delete user "${user.name}"?`
        );


    if (!confirmed) {
        return;
    }


    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/administrator/users/${user.id}`,

                authConfig()

            );


        success.value =
            response.data?.message ||
            'User deleted successfully.';


        /*
        |--------------------------------------------------------------------------
        | If last row on current page was deleted,
        | go back one page.
        |--------------------------------------------------------------------------
        */

        let page =
            pagination.value.current_page;


        if (
            users.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadUsers(page);


    } catch (e) {

        console.error(
            'Delete User Error:',
            e
        );


        error.value =
            e.response?.data?.message ||
            'Unable to delete user.';

    }

};


/*
|--------------------------------------------------------------------------
| ACTIVE STATUS
|--------------------------------------------------------------------------
*/

const isActive = (
    user
) => {

    return (
        Number(
            user.status
        ) === 1
    );

};


/*
|--------------------------------------------------------------------------
| CURRENT USER
|--------------------------------------------------------------------------
*/

const isCurrentUser = (
    user
) => {

    return (
        Number(
            currentUser.value?.id
        ) ===
        Number(
            user.id
        )
    );

};


/*
|--------------------------------------------------------------------------
| CLOSE USER MODAL
|--------------------------------------------------------------------------
*/

const closeUserModal = () => {

    if (saving.value) {
        return;
    }


    showUserModal.value = false;

};


/*
|--------------------------------------------------------------------------
| CLOSE ROLES MODAL
|--------------------------------------------------------------------------
*/

const closeRolesModal = () => {

    if (savingRoles.value) {
        return;
    }


    showRolesModal.value = false;

};


/*
|--------------------------------------------------------------------------
| RESET SEARCH
|--------------------------------------------------------------------------
*/

const resetSearch = () => {

    search.value = '';

    status.value = '';

    loadUsers(1);

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await loadRoles();

    await loadUsers(1);

});

</script>