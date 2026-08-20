<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Settings
                </h4>

                <small class="text-muted">
                    Manage school system settings
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                :disabled="saving"
                @click="saveSettings"
            >
                <span
                    v-if="saving"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                <i
                    v-else
                    class="bi bi-check-lg me-1"
                ></i>

                Save Settings
            </button>

        </div>


        <!-- Alert -->
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


        <div class="row">

            <!-- Sidebar -->
            <div class="col-md-3 mb-3">

                <div class="card">

                    <div class="card-header">
                        <strong>
                            Setting Groups
                        </strong>
                    </div>

                    <div class="list-group list-group-flush">

                        <button
                            v-for="item in groups"
                            :key="item.key"
                            type="button"
                            class="list-group-item list-group-item-action text-start"
                            :class="{
                                active:
                                    activeGroup === item.key
                            }"
                            @click="
                                changeGroup(item.key)
                            "
                        >

                            <i
                                :class="
                                    item.icon
                                "
                                class="me-2"
                            ></i>

                            {{ item.label }}

                        </button>

                    </div>

                </div>

            </div>


            <!-- Content -->
            <div class="col-md-9">

                <div class="card">

                    <div class="card-header">

                        <h5 class="mb-0">
                            {{ activeGroupLabel }}
                        </h5>

                    </div>


                    <div class="card-body">

                        <!-- Loading -->
                        <div
                            v-if="loading"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="text-muted mt-2">
                                Loading settings...
                            </div>

                        </div>


                        <!-- General -->
                        <template
                            v-else-if="
                                activeGroup ===
                                'general'
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Currency
                                    </label>

                                    <input
                                        v-model="
                                            settings.currency
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="INR"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Currency Symbol
                                    </label>

                                    <input
                                        v-model="
                                            settings.currency_symbol
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="₹"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Date Format
                                    </label>

                                    <select
                                        v-model="
                                            settings.date_format
                                        "
                                        class="form-select"
                                    >

                                        <option value="d-m-Y">
                                            DD-MM-YYYY
                                        </option>

                                        <option value="Y-m-d">
                                            YYYY-MM-DD
                                        </option>

                                        <option value="d/m/Y">
                                            DD/MM/YYYY
                                        </option>

                                        <option value="m/d/Y">
                                            MM/DD/YYYY
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Time Format
                                    </label>

                                    <select
                                        v-model="
                                            settings.time_format
                                        "
                                        class="form-select"
                                    >

                                        <option value="h:i A">
                                            12 Hour
                                        </option>

                                        <option value="H:i">
                                            24 Hour
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </template>


                        <!-- Library -->
                        <template
                            v-else-if="
                                activeGroup ===
                                'library'
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Overdue Fine Per Day
                                    </label>

                                    <div class="input-group">

                                        <span
                                            class="input-group-text"
                                        >
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                settings
                                                    .overdue_fine_per_day
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                        >

                                    </div>

                                    <small class="text-muted">
                                        Fine charged for each overdue day.
                                    </small>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Maximum Fine Amount
                                    </label>

                                    <div class="input-group">

                                        <span
                                            class="input-group-text"
                                        >
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                settings
                                                    .max_fine_amount
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                        >

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Renewal Limit
                                    </label>

                                    <input
                                        v-model.number="
                                            settings
                                                .renewal_limit
                                        "
                                        type="number"
                                        min="0"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Maximum number of times a book can be renewed.
                                    </small>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Default Issue Days
                                    </label>

                                    <input
                                        v-model.number="
                                            settings
                                                .issue_days
                                        "
                                        type="number"
                                        min="1"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Default number of days allowed for a book issue.
                                    </small>

                                </div>

                            </div>

                        </template>


                        <!-- Attendance -->
                        <template
                            v-else-if="
                                activeGroup ===
                                'attendance'
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Late After
                                    </label>

                                    <input
                                        v-model="
                                            settings.late_after
                                        "
                                        type="time"
                                        class="form-control"
                                    >

                                    <small class="text-muted">
                                        Students arriving after this time can be marked late.
                                    </small>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Working Days
                                    </label>

                                    <input
                                        v-model.number="
                                            settings.working_days
                                        "
                                        type="number"
                                        min="1"
                                        max="7"
                                        class="form-control"
                                    >

                                </div>

                            </div>

                        </template>


                        <!-- Fees -->
                        <template
                            v-else-if="
                                activeGroup ===
                                'fees'
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Late Fee Per Day
                                    </label>

                                    <div class="input-group">

                                        <span
                                            class="input-group-text"
                                        >
                                            ₹
                                        </span>

                                        <input
                                            v-model.number="
                                                settings
                                                    .late_fee_per_day
                                            "
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                        >

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="form-label">
                                        Invoice Prefix
                                    </label>

                                    <input
                                        v-model="
                                            settings
                                                .invoice_prefix
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="INV"
                                    >

                                </div>

                            </div>

                        </template>


                        <!-- Notification -->
                        <template
                            v-else-if="
                                activeGroup ===
                                'notification'
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <div class="card bg-light">

                                        <div class="card-body">

                                            <div
                                                class="form-check form-switch"
                                            >

                                                <input
                                                    id="email_enabled"
                                                    v-model="
                                                        settings
                                                            .email_enabled
                                                    "
                                                    class="form-check-input"
                                                    type="checkbox"
                                                >

                                                <label
                                                    for="email_enabled"
                                                    class="form-check-label"
                                                >
                                                    <strong>
                                                        Email Notifications
                                                    </strong>
                                                </label>

                                            </div>

                                            <small class="text-muted">
                                                Enable system email notifications.
                                            </small>

                                        </div>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <div class="card bg-light">

                                        <div class="card-body">

                                            <div
                                                class="form-check form-switch"
                                            >

                                                <input
                                                    id="sms_enabled"
                                                    v-model="
                                                        settings
                                                            .sms_enabled
                                                    "
                                                    class="form-check-input"
                                                    type="checkbox"
                                                >

                                                <label
                                                    for="sms_enabled"
                                                    class="form-check-label"
                                                >
                                                    <strong>
                                                        SMS Notifications
                                                    </strong>
                                                </label>

                                            </div>

                                            <small class="text-muted">
                                                Enable system SMS notifications.
                                            </small>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </template>

                    </div>


                    <div class="card-footer text-end">

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="saving || loading"
                            @click="saveSettings"
                        >

                            <span
                                v-if="saving"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>

                            <i
                                v-else
                                class="bi bi-save me-1"
                            ></i>

                            Save {{ activeGroupLabel }}

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


/*
|--------------------------------------------------------------------------
| Groups
|--------------------------------------------------------------------------
*/

const groups = [

    {
        key: 'general',
        label: 'General Settings',
        icon: 'bi bi-gear'
    },

    {
        key: 'library',
        label: 'Library Settings',
        icon: 'bi bi-book'
    },

    {
        key: 'attendance',
        label: 'Attendance Settings',
        icon: 'bi bi-calendar-check'
    },

    {
        key: 'fees',
        label: 'Fee Settings',
        icon: 'bi bi-cash-stack'
    },

    {
        key: 'notification',
        label: 'Notification Settings',
        icon: 'bi bi-bell'
    }

];


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const activeGroup =
    ref('general');

const settings =
    ref({});

const loading =
    ref(false);

const saving =
    ref(false);

const success =
    ref('');

const error =
    ref('');


/*
|--------------------------------------------------------------------------
| Active Group Label
|--------------------------------------------------------------------------
*/

const activeGroupLabel =
    computed(() => {

        return (
            groups.find(
                item =>
                    item.key ===
                    activeGroup.value
            )?.label ||
            'Settings'
        );

    });


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
        sessionStorage.getItem('token') ||
        sessionStorage.getItem('auth_token') ||
        sessionStorage.getItem('access_token');


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
| Load Group
|--------------------------------------------------------------------------
*/

const loadGroup = async () => {

    loading.value = true;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.get(
                `/api/settings/group/${activeGroup.value}`,
                getAuthConfig()
            );


        settings.value =
            response.data?.data ||
            {};


        /*
        |--------------------------------------------------------------------------
        | Convert Values
        |--------------------------------------------------------------------------
        */

        normalizeSettings();

    } catch (err) {

        console.error(
            'Settings load error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load settings.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Normalize
|--------------------------------------------------------------------------
*/

const normalizeSettings = () => {

    /*
    |--------------------------------------------------------------------------
    | Numeric Values
    |--------------------------------------------------------------------------
    */

    const numericKeys = [

        'overdue_fine_per_day',

        'max_fine_amount',

        'renewal_limit',

        'issue_days',

        'working_days',

        'late_fee_per_day'

    ];


    numericKeys.forEach(
        key => {

            if (
                settings.value[key] !==
                undefined
            ) {

                settings.value[key] =
                    Number(
                        settings.value[key]
                    );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Boolean Values
    |--------------------------------------------------------------------------
    */

    const booleanKeys = [

        'email_enabled',

        'sms_enabled'

    ];


    booleanKeys.forEach(
        key => {

            if (
                settings.value[key] !==
                undefined
            ) {

                settings.value[key] =
                    Boolean(
                        settings.value[key]
                    );

            }

        }
    );

};


/*
|--------------------------------------------------------------------------
| Change Group
|--------------------------------------------------------------------------
*/

const changeGroup = async (
    group
) => {

    if (
        saving.value
    ) {
        return;
    }


    activeGroup.value =
        group;


    await loadGroup();

};


/*
|--------------------------------------------------------------------------
| Save Settings
|--------------------------------------------------------------------------
*/

const saveSettings = async () => {

    saving.value = true;

    error.value = '';

    success.value = '';


    try {

        const payload = {

            settings: {}

        };


        Object.entries(
            settings.value
        ).forEach(
            ([key, value]) => {

                payload.settings[key] = {

                    value,

                    type:
                        detectType(value)

                };

            }
        );


        const response =
            await axios.put(
                `/api/settings/group/${activeGroup.value}`,
                payload,
                getAuthConfig()
            );


        /*
        |--------------------------------------------------------------------------
        | Use returned values
        |--------------------------------------------------------------------------
        */

        settings.value =
            response.data?.data ||
            settings.value;


        normalizeSettings();


        success.value =
            response.data?.message ||
            'Settings saved successfully.';


    } catch (err) {

        console.error(
            'Settings save error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to save settings.';

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Detect Type
|--------------------------------------------------------------------------
*/

const detectType = (
    value
) => {

    if (
        typeof value ===
        'boolean'
    ) {
        return 'boolean';
    }


    if (
        typeof value ===
        'number'
    ) {

        return Number.isInteger(
            value
        )
            ? 'integer'
            : 'decimal';
    }


    if (
        Array.isArray(value) ||
        (
            value !== null &&
            typeof value ===
            'object'
        )
    ) {
        return 'json';
    }


    return 'string';

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadGroup();

    }
);

</script>