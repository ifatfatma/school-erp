<template>

    <div class="container-fluid">

        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div
            class="d-flex justify-content-between align-items-center mb-3"
        >

            <div>

                <h4 class="mb-1">
                    Roles
                </h4>

                <small class="text-muted">
                    Manage user roles and permissions
                </small>

            </div>


            <button
                v-if="isSuperAdmin"
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >

                <i class="bi bi-plus-lg me-1"></i>

                Add Role

            </button>

        </div>


        <!-- ========================================================= -->
        <!-- SUCCESS -->
        <!-- ========================================================= -->

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


        <!-- ========================================================= -->
        <!-- ERROR -->
        <!-- ========================================================= -->

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


        <!-- ========================================================= -->
        <!-- SEARCH -->
        <!-- ========================================================= -->

        <div class="card mb-3">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <label class="form-label">
                            Search Role
                        </label>

                        <input
                            v-model="search"
                            type="text"
                            class="form-control"
                            placeholder="Search role name..."
                            @keyup.enter="loadRoles(1)"
                        />

                    </div>


                    <div
                        class="col-md-6 d-flex align-items-end"
                    >

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadRoles(1)"
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


        <!-- ========================================================= -->
        <!-- ROLE LIST -->
        <!-- ========================================================= -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between"
            >

                <strong>
                    Role List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} roles
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Role Name
                                </th>

                                <th>
                                    Guard
                                </th>

                                <th width="140">
                                    Permissions
                                </th>

                                <th width="230">
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
                                        Loading roles...
                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-else-if="
                                    roles.length === 0
                                "
                            >

                                <td
                                    colspan="5"
                                    class="text-center py-5 text-muted"
                                >
                                    No roles found.
                                </td>

                            </tr>


                            <!-- DATA -->

                            <tr
                                v-for="(
                                    role,
                                    index
                                ) in roles"
                                :key="role.id"
                            >

                                <!-- NUMBER -->

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


                                <!-- ROLE -->

                                <td>

                                    <strong>
                                        {{ role.name }}
                                    </strong>


                                    <span
                                        v-if="
                                            isProtectedRole(
                                                role
                                            )
                                        "
                                        class="badge bg-warning text-dark ms-2"
                                    >
                                        Protected
                                    </span>

                                </td>


                                <!-- GUARD -->

                                <td>

                                    <span
                                        class="badge bg-light text-dark border"
                                    >
                                        {{ role.guard_name }}
                                    </span>

                                </td>


                                <!-- PERMISSIONS -->

                                <td>

                                    <span
                                        class="badge bg-primary"
                                    >

                                        {{
                                            role.permissions_count ??
                                            role.permissions?.length ??
                                            0
                                        }}

                                    </span>

                                </td>


                                <!-- ================================================= -->
                                <!-- ACTIONS -->
                                <!-- ================================================= -->

                                <td>

                                    <template
                                        v-if="
                                            isSuperAdmin
                                        "
                                    >

                                        <!-- EDIT -->

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary me-1"
                                            title="Edit Role"
                                            @click="
                                                openEdit(
                                                    role
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-pencil"
                                            ></i>

                                        </button>


                                        <!-- PERMISSIONS -->

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success me-1"
                                            title="Manage Permissions"
                                            @click="
                                                openPermissions(
                                                    role
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-key"
                                            ></i>

                                        </button>


                                        <!-- DELETE -->

                                        <button
                                            v-if="
                                                !isProtectedRole(
                                                    role
                                                )
                                            "
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete Role"
                                            @click="
                                                deleteRole(
                                                    role
                                                )
                                            "
                                        >

                                            <i
                                                class="bi bi-trash"
                                            ></i>

                                        </button>

                                    </template>


                                    <span
                                        v-else
                                        class="text-muted"
                                    >
                                        No actions
                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- ===================================================== -->
            <!-- PAGINATION -->
            <!-- ===================================================== -->

            <div
                v-if="
                    pagination.last_page > 1
                "
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
                            pagination.current_page <=
                                1 ||
                            loading
                        "
                        @click="
                            loadRoles(
                                pagination.current_page -
                                1
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
                            loadRoles(
                                pagination.current_page +
                                1
                            )
                        "
                    >
                        Next
                    </button>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- ROLE MODAL -->
        <!-- ========================================================= -->

        <div
            v-if="showRoleModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveRole"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? "Edit Role"
                                        : "Add Role"
                                }}

                            </h5>


                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="
                                    closeRoleModal
                                "
                            ></button>

                        </div>


                        <div class="modal-body">

                            <!-- ERROR -->

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <!-- ROLE NAME -->

                            <div class="mb-3">

                                <label class="form-label">

                                    Role Name

                                    <span
                                        class="text-danger"
                                    >
                                        *
                                    </span>

                                </label>


                                <input
                                    v-model.trim="
                                        roleForm.name
                                    "
                                    type="text"
                                    class="form-control"
                                    maxlength="255"
                                    required
                                    :disabled="
                                        editing &&
                                        isProtectedRole(
                                            selectedRole
                                        )
                                    "
                                    placeholder="Example: Accountant"
                                />

                            </div>


                            <!-- GUARD -->

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


                            <!-- PERMISSIONS -->

                            <div>

                                <label class="form-label">
                                    Permissions
                                </label>


                                <div
                                    class="border rounded p-3"
                                    style="
                                        max-height:300px;
                                        overflow-y:auto;
                                    "
                                >

                                    <div
                                        v-if="
                                            permissions.length ===
                                            0
                                        "
                                        class="text-muted"
                                    >
                                        No permissions available.
                                    </div>


                                    <div
                                        v-for="
                                            permission in
                                            permissions
                                        "
                                        :key="
                                            permission.id
                                        "
                                        class="form-check mb-2"
                                    >

                                        <input
                                            :id="
                                                'role_permission_' +
                                                permission.id
                                            "
                                            v-model="
                                                roleForm.permission_ids
                                            "
                                            class="form-check-input"
                                            type="checkbox"
                                            :value="
                                                Number(
                                                    permission.id
                                                )
                                            "
                                        />


                                        <label
                                            class="form-check-label"
                                            :for="
                                                'role_permission_' +
                                                permission.id
                                            "
                                        >

                                            {{
                                                permission.name
                                            }}

                                        </label>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="saving"
                                @click="
                                    closeRoleModal
                                "
                            >
                                Cancel
                            </button>


                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="
                                    saving ||
                                    !roleForm.name.trim()
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>


                                {{
                                    editing
                                        ? "Update Role"
                                        : "Create Role"
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- PERMISSIONS MODAL -->
        <!-- ========================================================= -->

        <div
            v-if="showPermissionModal"
            class="modal fade show d-block"
            tabindex="-1"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">

                            Manage Permissions

                            <span
                                v-if="selectedRole"
                                class="text-primary"
                            >

                                -
                                {{ selectedRole.name }}

                            </span>

                        </h5>


                        <button
                            type="button"
                            class="btn-close"
                            :disabled="
                                savingPermissions
                            "
                            @click="
                                closePermissionModal
                            "
                        ></button>

                    </div>


                    <div class="modal-body">

                        <div
                            v-if="permissionError"
                            class="alert alert-danger"
                        >
                            {{ permissionError }}
                        </div>


                        <div
                            v-if="loadingPermissions"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="text-muted mt-2">
                                Loading permissions...
                            </div>

                        </div>


                        <template v-else>

                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >

                                <strong>
                                    Available Permissions
                                </strong>


                                <span
                                    class="text-muted"
                                >

                                    {{
                                        permissionForm
                                            .permission_ids
                                            .length
                                    }}

                                    selected

                                </span>

                            </div>


                            <div
                                class="border rounded p-3"
                                style="
                                    max-height:400px;
                                    overflow-y:auto;
                                "
                            >

                                <div
                                    v-if="
                                        permissions.length ===
                                        0
                                    "
                                    class="text-muted"
                                >
                                    No permissions found.
                                </div>


                                <div
                                    v-for="
                                        permission in
                                        permissions
                                    "
                                    :key="
                                        permission.id
                                    "
                                    class="form-check mb-2"
                                >

                                    <input
                                        :id="
                                            'permission_' +
                                            permission.id
                                        "
                                        v-model="
                                            permissionForm.permission_ids
                                        "
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="
                                            Number(
                                                permission.id
                                            )
                                        "
                                    />


                                    <label
                                        class="form-check-label"
                                        :for="
                                            'permission_' +
                                            permission.id
                                        "
                                    >

                                        {{
                                            permission.name
                                        }}

                                    </label>

                                </div>

                            </div>

                        </template>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            :disabled="
                                savingPermissions
                            "
                            @click="
                                closePermissionModal
                            "
                        >
                            Close
                        </button>


                        <button
                            type="button"
                            class="btn btn-success"
                            :disabled="
                                savingPermissions ||
                                loadingPermissions
                            "
                            @click="
                                savePermissions
                            "
                        >

                            <span
                                v-if="
                                    savingPermissions
                                "
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            Save Permissions

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
} from "vue";

import axios from "axios";


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const roles = ref([]);

const permissions = ref([]);

const currentUser = ref(null);

const loading = ref(false);

const saving = ref(false);

const loadingPermissions = ref(false);

const savingPermissions = ref(false);

const search = ref("");

const error = ref("");

const success = ref("");

const formError = ref("");

const permissionError = ref("");

const showRoleModal = ref(false);

const showPermissionModal = ref(false);

const editing = ref(false);

const selectedRole = ref(null);


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
| ROLE FORM
|--------------------------------------------------------------------------
*/

const emptyRoleForm = () => ({

    name: "",

    permission_ids: []

});


const roleForm = ref(
    emptyRoleForm()
);


/*
|--------------------------------------------------------------------------
| PERMISSION FORM
|--------------------------------------------------------------------------
*/

const permissionForm = ref({

    permission_ids: []

});


/*
|--------------------------------------------------------------------------
| AUTH CONFIG
|--------------------------------------------------------------------------
*/

const authConfig = () => {

    const token =
        localStorage.getItem("token") ||
        localStorage.getItem("access_token") ||
        sessionStorage.getItem("token") ||
        sessionStorage.getItem("access_token");


    const config = {

        withCredentials: true,

        headers: {

            Accept:
                "application/json"

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
| SUPER ADMIN
|--------------------------------------------------------------------------
*/

const isSuperAdmin = computed(() => {

    const user =
        currentUser.value;


    if (!user) {

        return false;

    }


    /*
    |--------------------------------------------------------------------------
    | USER TYPE
    |--------------------------------------------------------------------------
    */

    const userType =
        String(
            user.user_type || ""
        )
            .trim()
            .toLowerCase();


    if (
        userType === "super_admin" ||
        userType === "super admin"
    ) {

        return true;

    }


    /*
    |--------------------------------------------------------------------------
    | ROLE
    |--------------------------------------------------------------------------
    */

    const roles =
        Array.isArray(user.roles)
            ? user.roles
            : [];


    return roles.some(
        role =>
            String(
                role?.name || ""
            )
                .trim()
                .toLowerCase() ===
            "super admin"
    );

});


/*
|--------------------------------------------------------------------------
| LOAD CURRENT USER
|--------------------------------------------------------------------------
*/

const loadCurrentUser = async () => {

    /*
    |--------------------------------------------------------------------------
    | LOCAL USER FIRST
    |--------------------------------------------------------------------------
    */

    const storedUser =
        localStorage.getItem("user") ||
        localStorage.getItem("auth_user");


    if (storedUser) {

        try {

            currentUser.value =
                JSON.parse(
                    storedUser
                );

        } catch (e) {

            console.warn(
                "Invalid stored user.",
                e
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | API USER
    |--------------------------------------------------------------------------
    */

    try {

        const response =
            await axios.get(
                "/api/profile",
                authConfig()
            );


        if (
            response.data?.user
        ) {

            currentUser.value =
                response.data.user;


            localStorage.setItem(

                "user",

                JSON.stringify(
                    response.data.user
                )

            );

        }

    } catch (e) {

        console.error(
            "Unable to load profile:",
            e
        );

    }

};


/*
|--------------------------------------------------------------------------
| PROTECTED ROLE
|--------------------------------------------------------------------------
*/

const isProtectedRole = (
    role
) => {

    if (!role) {

        return false;

    }


    const name =
        typeof role === "string"
            ? role
            : role.name;


    return [

        "super admin",

        "school admin"

    ].includes(

        String(
            name || ""
        )
            .trim()
            .toLowerCase()

    );

};


/*
|--------------------------------------------------------------------------
| LOAD ROLES
|--------------------------------------------------------------------------
*/

const loadRoles = async (
    page = 1
) => {

    loading.value = true;

    error.value = "";


    try {

        const response =
            await axios.get(

                "/api/administrator/roles",

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


        if (
            result &&
            Array.isArray(
                result.data
            )
        ) {

            roles.value =
                result.data;


            pagination.value = {

                current_page:
                    Number(
                        result.current_page ||
                        1
                    ),

                last_page:
                    Number(
                        result.last_page ||
                        1
                    ),

                per_page:
                    Number(
                        result.per_page ||
                        20
                    ),

                total:
                    Number(
                        result.total ||
                        0
                    ),

                from:
                    Number(
                        result.from ||
                        0
                    ),

                to:
                    Number(
                        result.to ||
                        0
                    )

            };

        } else {

            roles.value =
                Array.isArray(result)
                    ? result
                    : [];

        }

    } catch (e) {

        console.error(
            "Load Roles Error:",
            e
        );


        error.value =
            e.response?.data?.message ||
            "Unable to load roles.";

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| LOAD PERMISSIONS
|--------------------------------------------------------------------------
*/

const loadPermissions = async () => {

    try {

        const response =
            await axios.get(

                "/api/administrator/permissions",

                {

                    ...authConfig(),

                    params: {

                        per_page: 100

                    }

                }

            );


        const result =
            response.data?.data;


        if (
            result &&
            Array.isArray(
                result.data
            )
        ) {

            permissions.value =
                result.data;

        } else {

            permissions.value =
                Array.isArray(result)
                    ? result
                    : [];

        }

    } catch (e) {

        console.error(
            "Load Permissions Error:",
            e
        );


        error.value =
            e.response?.data?.message ||
            "Unable to load permissions.";

    }

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    selectedRole.value = null;

    roleForm.value =
        emptyRoleForm();

    formError.value = "";

    showRoleModal.value = true;

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

const openEdit = async (
    role
) => {

    editing.value = true;

    selectedRole.value = role;

    formError.value = "";

    roleForm.value = {

        name:
            role.name || "",

        permission_ids: []

    };


    showRoleModal.value = true;


    try {

        const response =
            await axios.get(

                `/api/administrator/roles/${role.id}/permissions`,

                authConfig()

            );


        const data =
            response.data?.data;


        roleForm.value.permission_ids =
            Array.isArray(
                data?.selected
            )
                ? data.selected.map(
                    id => Number(id)
                )
                : [];

    } catch (e) {

        console.error(
            "Load Role Permissions Error:",
            e
        );


        formError.value =
            e.response?.data?.message ||
            "Unable to load role permissions.";

    }

};


/*
|--------------------------------------------------------------------------
| SAVE ROLE
|--------------------------------------------------------------------------
*/

const saveRole = async () => {

    formError.value = "";

    error.value = "";

    success.value = "";

    saving.value = true;


    try {

        const payload = {

            name:
                roleForm.value.name.trim(),

            guard_name:
                "sanctum",

            permission_ids:
                roleForm.value.permission_ids.map(
                    id => Number(id)
                )

        };


        let response;


        if (
            editing.value &&
            selectedRole.value
        ) {

            response =
                await axios.put(

                    `/api/administrator/roles/${selectedRole.value.id}`,

                    payload,

                    authConfig()

                );

        } else {

            response =
                await axios.post(

                    "/api/administrator/roles",

                    payload,

                    authConfig()

                );

        }


        success.value =
            response.data?.message ||
            "Role saved successfully.";


        showRoleModal.value =
            false;


        await loadRoles(
            pagination.value.current_page
        );


    } catch (e) {

        console.error(
            "Save Role Error:",
            e
        );


        if (
            e.response?.status === 422
        ) {

            const errors =
                e.response?.data?.errors ||
                {};

            const first =
                Object.values(errors)[0];


            formError.value =
                Array.isArray(first)
                    ? first[0]
                    : (
                        first ||
                        e.response?.data?.message ||
                        "Validation failed."
                    );

        } else {

            formError.value =
                e.response?.data?.message ||
                "Unable to save role.";

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| OPEN PERMISSIONS
|--------------------------------------------------------------------------
*/

const openPermissions = async (
    role
) => {

    selectedRole.value =
        role;

    permissionError.value = "";

    permissionForm.value = {

        permission_ids: []

    };


    showPermissionModal.value =
        true;

    loadingPermissions.value =
        true;


    try {

        if (
            permissions.value.length === 0
        ) {

            await loadPermissions();

        }


        const response =
            await axios.get(

                `/api/administrator/roles/${role.id}/permissions`,

                authConfig()

            );


        const data =
            response.data?.data;


        permissionForm.value.permission_ids =
            Array.isArray(
                data?.selected
            )
                ? data.selected.map(
                    id => Number(id)
                )
                : [];


    } catch (e) {

        console.error(
            "Open Permissions Error:",
            e
        );


        permissionError.value =
            e.response?.data?.message ||
            "Unable to load permissions.";

    } finally {

        loadingPermissions.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| SAVE PERMISSIONS
|--------------------------------------------------------------------------
*/

const savePermissions = async () => {

    if (!selectedRole.value) {

        return;

    }


    permissionError.value = "";

    savingPermissions.value =
        true;


    try {

        const response =
            await axios.put(

                `/api/administrator/roles/${selectedRole.value.id}/permissions`,

                {

                    permission_ids:
                        permissionForm.value.permission_ids.map(
                            id => Number(id)
                        )

                },

                authConfig()

            );


        success.value =
            response.data?.message ||
            "Permissions updated successfully.";


        showPermissionModal.value =
            false;


        await loadRoles(
            pagination.value.current_page
        );


    } catch (e) {

        console.error(
            "Save Permissions Error:",
            e
        );


        permissionError.value =
            e.response?.data?.message ||
            "Unable to update permissions.";

    } finally {

        savingPermissions.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE ROLE
|--------------------------------------------------------------------------
*/

const deleteRole = async (
    role
) => {

    if (
        isProtectedRole(role)
    ) {

        error.value =
            "This role is protected and cannot be deleted.";

        return;

    }


    const confirmed =
        window.confirm(
            `Are you sure you want to delete "${role.name}"?`
        );


    if (!confirmed) {

        return;

    }


    error.value = "";

    success.value = "";


    try {

        const response =
            await axios.delete(

                `/api/administrator/roles/${role.id}`,

                authConfig()

            );


        success.value =
            response.data?.message ||
            "Role deleted successfully.";


        let page =
            pagination.value.current_page;


        if (
            roles.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadRoles(
            page
        );


    } catch (e) {

        console.error(
            "Delete Role Error:",
            e
        );


        error.value =
            e.response?.data?.message ||
            "Unable to delete role.";

    }

};


/*
|--------------------------------------------------------------------------
| CLOSE ROLE MODAL
|--------------------------------------------------------------------------
*/

const closeRoleModal = () => {

    if (saving.value) {

        return;

    }


    showRoleModal.value =
        false;

    formError.value = "";

};


/*
|--------------------------------------------------------------------------
| CLOSE PERMISSION MODAL
|--------------------------------------------------------------------------
*/

const closePermissionModal = () => {

    if (
        savingPermissions.value
    ) {

        return;

    }


    showPermissionModal.value =
        false;

    permissionError.value = "";

};


/*
|--------------------------------------------------------------------------
| RESET SEARCH
|--------------------------------------------------------------------------
*/

const resetSearch = () => {

    search.value = "";

    loadRoles(1);

};


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    /*
    |--------------------------------------------------------------------------
    | VERY IMPORTANT:
    | Load authenticated user first.
    |--------------------------------------------------------------------------
    */

    await loadCurrentUser();


    /*
    |--------------------------------------------------------------------------
    | Load roles and permissions.
    |--------------------------------------------------------------------------
    */

    await Promise.all([

        loadPermissions(),

        loadRoles(1)

    ]);

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

</style>