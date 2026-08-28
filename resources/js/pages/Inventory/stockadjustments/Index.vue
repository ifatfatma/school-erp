<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Stock Adjustment
                </h4>

                <small class="text-muted">
                    Increase or decrease inventory stock manually
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                New Adjustment
            </button>

        </div>


        <!-- =========================================================
             ALERTS
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
             FILTERS
        ========================================================== -->

        <div class="card mb-3">

            <div class="card-header">
                <strong>
                    Filters
                </strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->

                    <div class="col-md-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Movement No / Item / SKU"
                            @keyup.enter="loadAdjustments(1)"
                        >

                    </div>


                    <!-- Item -->

                    <div class="col-md-3">

                        <label class="form-label">
                            Item
                        </label>

                        <select
                            v-model="
                                filters.inventory_item_id
                            "
                            class="form-select"
                        >

                            <option value="">
                                All Items
                            </option>

                            <option
                                v-for="item in items"
                                :key="item.id"
                                :value="item.id"
                            >

                                {{ item.name }}

                                {{
                                    item.sku
                                        ? ` - ${item.sku}`
                                        : ''
                                }}

                            </option>

                        </select>

                    </div>


                    <!-- Type -->

                    <div class="col-md-2">

                        <label class="form-label">
                            Adjustment Type
                        </label>

                        <select
                            v-model="
                                filters.movement_type
                            "
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="ADJUSTMENT_IN">
                                Adjustment IN
                            </option>

                            <option value="ADJUSTMENT_OUT">
                                Adjustment OUT
                            </option>

                        </select>

                    </div>


                    <!-- From -->

                    <div class="col-md-2">

                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            v-model="
                                filters.from_date
                            "
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- To -->

                    <div class="col-md-2">

                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            v-model="
                                filters.to_date
                            "
                            type="date"
                            class="form-control"
                        >

                    </div>


                    <!-- Buttons -->

                    <div
                        class="col-md-12 d-flex justify-content-end gap-2"
                    >

                        <button
                            type="button"
                            class="btn btn-primary"
                            :disabled="loading"
                            @click="loadAdjustments(1)"
                        >

                            <i class="bi bi-search me-1"></i>

                            Search

                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetFilters"
                        >

                            <i
                                class="bi bi-arrow-counterclockwise me-1"
                            ></i>

                            Reset

                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             TABLE
        ========================================================== -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Adjustment History
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover align-middle mb-0"
                    >

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Movement No.
                                </th>

                                <th>
                                    Item
                                </th>

                                <th>
                                    Type
                                </th>

                                <th class="text-end">
                                    Quantity
                                </th>

                                <th class="text-end">
                                    Unit Price
                                </th>

                                <th class="text-end">
                                    Stock Before
                                </th>

                                <th class="text-end">
                                    Stock After
                                </th>

                                <th>
                                    Reason / Remarks
                                </th>

                                <th
                                    width="70"
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
                                    colspan="11"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading adjustments...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-else-if="
                                    adjustments.length === 0
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-sliders fs-2 d-block mb-2"
                                    ></i>

                                    No stock adjustments found.

                                </td>

                            </tr>


                            <!-- Records -->

                            <tr
                                v-for="(
                                    adjustment,
                                    index
                                ) in adjustments"
                                :key="adjustment.id"
                            >

                                <td>

                                    {{
                                        (
                                            pagination.current_page -
                                            1
                                        ) *
                                        pagination.per_page +
                                        index +
                                        1
                                    }}

                                </td>


                                <td>

                                    {{
                                        formatDate(
                                            adjustment.movement_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            adjustment.movement_no
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <div class="fw-semibold">

                                        {{
                                            adjustment.item?.name ||
                                            '-'
                                        }}

                                    </div>

                                    <small
                                        v-if="
                                            adjustment.item?.sku
                                        "
                                        class="text-muted"
                                    >

                                        SKU:
                                        {{
                                            adjustment.item.sku
                                        }}

                                    </small>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            typeClass(
                                                adjustment.movement_type
                                            )
                                        "
                                    >

                                        {{
                                            typeLabel(
                                                adjustment.movement_type
                                            )
                                        }}

                                    </span>

                                </td>


                                <td
                                    class="text-end"
                                >

                                    <strong>
                                        {{
                                            number(
                                                adjustment.quantity
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td
                                    class="text-end"
                                >

                                    {{
                                        money(
                                            adjustment.unit_price
                                        )
                                    }}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    {{
                                        number(
                                            adjustment.stock_before
                                        )
                                    }}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    <strong>
                                        {{
                                            number(
                                                adjustment.stock_after
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        adjustment.remarks ||
                                        '-'
                                    }}

                                </td>


                                <td
                                    class="text-center"
                                >

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info"
                                        title="View"
                                        @click="
                                            viewAdjustment(
                                                adjustment.id
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-eye"
                                        ></i>

                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =====================================================
                 PAGINATION
            ====================================================== -->

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


        <!-- =========================================================
             CREATE MODAL
        ========================================================== -->

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
                        @submit.prevent="saveAdjustment"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                New Stock Adjustment
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
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

                                <!-- Item -->

                                <div class="col-md-7">

                                    <label class="form-label">
                                        Item
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        v-model="
                                            form.inventory_item_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.inventory_item_id
                                        }"
                                        @change="
                                            itemChanged
                                        "
                                    >

                                        <option value="">
                                            Select Inventory Item
                                        </option>

                                        <option
                                            v-for="item in items"
                                            :key="item.id"
                                            :value="item.id"
                                        >

                                            {{ item.name }}

                                            {{
                                                item.sku
                                                    ? ` - ${item.sku}`
                                                    : ''
                                            }}

                                        </option>

                                    </select>

                                    <div
                                        v-if="
                                            formErrors.inventory_item_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.inventory_item_id
                                        }}
                                    </div>

                                </div>


                                <!-- Current Stock -->

                                <div class="col-md-5">

                                    <label class="form-label">
                                        Current Stock
                                    </label>

                                    <div
                                        class="form-control bg-light fw-bold"
                                    >

                                        {{
                                            currentStock !== null
                                                ? number(
                                                    currentStock
                                                )
                                                : '-'
                                        }}

                                    </div>

                                </div>


                                <!-- Type -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Adjustment Type
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <select
                                        v-model="
                                            form.adjustment_type
                                        "
                                        class="form-select"
                                    >

                                        <option value="ADJUSTMENT_IN">
                                            Adjustment IN
                                        </option>

                                        <option value="ADJUSTMENT_OUT">
                                            Adjustment OUT
                                        </option>

                                    </select>

                                </div>


                                <!-- Quantity -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Quantity
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model.number="
                                            form.quantity
                                        "
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.quantity ||
                                                quantityError
                                        }"
                                        @input="
                                            validateQuantity
                                        "
                                    >

                                    <div
                                        v-if="quantityError"
                                        class="text-danger small mt-1"
                                    >
                                        {{ quantityError }}
                                    </div>

                                    <div
                                        v-else-if="
                                            formErrors.quantity
                                        "
                                        class="invalid-feedback d-block"
                                    >
                                        {{
                                            formErrors.quantity
                                        }}
                                    </div>

                                </div>


                                <!-- Unit Price -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Unit Price
                                    </label>

                                    <input
                                        v-model.number="
                                            form.unit_price
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                    >

                                </div>


                                <!-- Date -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Adjustment Date
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.movement_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.movement_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.movement_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.movement_date
                                        }}
                                    </div>

                                </div>


                                <!-- Reason -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Reason
                                    </label>

                                    <input
                                        v-model="
                                            form.reason
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Physical count correction"
                                    >

                                </div>


                                <!-- Remarks -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Remarks
                                    </label>

                                    <textarea
                                        v-model="
                                            form.remarks
                                        "
                                        class="form-control"
                                        rows="3"
                                        placeholder="Additional remarks"
                                    ></textarea>

                                </div>


                                <!-- Preview -->

                                <div
                                    v-if="
                                        form.inventory_item_id &&
                                        Number(form.quantity) > 0
                                    "
                                    class="col-md-6"
                                >

                                    <div class="card bg-light">

                                        <div class="card-body">

                                            <div class="small text-muted">
                                                Stock Preview
                                            </div>

                                            <div class="d-flex justify-content-between mt-2">

                                                <span>
                                                    Before
                                                </span>

                                                <strong>
                                                    {{
                                                        number(
                                                            currentStock || 0
                                                        )
                                                    }}
                                                </strong>

                                            </div>


                                            <div class="d-flex justify-content-between">

                                                <span>
                                                    Adjustment
                                                </span>

                                                <strong
                                                    :class="
                                                        form.adjustment_type ===
                                                        'ADJUSTMENT_IN'
                                                            ? 'text-success'
                                                            : 'text-danger'
                                                    "
                                                >

                                                    {{
                                                        form.adjustment_type ===
                                                        'ADJUSTMENT_IN'
                                                            ? '+'
                                                            : '-'
                                                    }}

                                                    {{
                                                        number(
                                                            form.quantity
                                                        )
                                                    }}

                                                </strong>

                                            </div>


                                            <hr class="my-2">


                                            <div class="d-flex justify-content-between">

                                                <span>
                                                    After
                                                </span>

                                                <strong class="fs-5">

                                                    {{
                                                        number(
                                                            previewStock
                                                        )
                                                    }}

                                                </strong>

                                            </div>

                                        </div>

                                    </div>

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
                                :disabled="
                                    saving ||
                                    !!quantityError
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                <i
                                    v-else
                                    class="bi bi-check-lg me-1"
                                ></i>

                                Save Adjustment

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- =========================================================
             VIEW MODAL
        ========================================================== -->

        <div
            v-if="showViewModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-lg modal-dialog-centered"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Adjustment Details
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            @click="
                                showViewModal = false
                            "
                        ></button>

                    </div>


                    <div class="modal-body">

                        <div
                            v-if="viewLoading"
                            class="text-center py-5"
                        >

                            <div
                                class="spinner-border text-primary"
                            ></div>

                            <div class="text-muted mt-2">
                                Loading...
                            </div>

                        </div>


                        <div
                            v-else-if="
                                selectedAdjustment
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Movement No.
                                    </label>

                                    <div class="fw-bold">
                                        {{
                                            selectedAdjustment.movement_no
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Date
                                    </label>

                                    <div>
                                        {{
                                            formatDate(
                                                selectedAdjustment.movement_date
                                            )
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Type
                                    </label>

                                    <div>

                                        <span
                                            class="badge"
                                            :class="
                                                typeClass(
                                                    selectedAdjustment.movement_type
                                                )
                                            "
                                        >

                                            {{
                                                typeLabel(
                                                    selectedAdjustment.movement_type
                                                )
                                            }}

                                        </span>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Item
                                    </label>

                                    <div class="fw-bold">

                                        {{
                                            selectedAdjustment.item?.name ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        SKU
                                    </label>

                                    <div>

                                        {{
                                            selectedAdjustment.item?.sku ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Quantity
                                    </label>

                                    <div class="fw-bold">

                                        {{
                                            number(
                                                selectedAdjustment.quantity
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Unit Price
                                    </label>

                                    <div>

                                        {{
                                            money(
                                                selectedAdjustment.unit_price
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Stock Before
                                    </label>

                                    <div>

                                        {{
                                            number(
                                                selectedAdjustment.stock_before
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Stock After
                                    </label>

                                    <div class="fw-bold">

                                        {{
                                            number(
                                                selectedAdjustment.stock_after
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-12">

                                    <label class="text-muted small">
                                        Remarks
                                    </label>

                                    <div>

                                        {{
                                            selectedAdjustment.remarks ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-12">

                                    <label class="text-muted small">
                                        Created By
                                    </label>

                                    <div>

                                        {{
                                            selectedAdjustment.creator?.name ||
                                            '-'
                                        }}

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            @click="
                                showViewModal = false
                            "
                        >
                            Close
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
| STATE
|--------------------------------------------------------------------------
*/

const adjustments = ref([]);

const items = ref([]);

const loading = ref(false);

const saving = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const quantityError = ref('');

const currentStock = ref(null);

const showModal = ref(false);

const showViewModal = ref(false);

const viewLoading = ref(false);

const selectedAdjustment = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    inventory_item_id: '',

    movement_type: '',

    from_date: '',

    to_date: '',

    per_page: 20

});


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

const getEmptyForm = () => ({

    inventory_item_id: '',

    adjustment_type: 'ADJUSTMENT_IN',

    quantity: 1,

    unit_price: 0,

    movement_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    reason: '',

    remarks: ''

});


const form = ref(
    getEmptyForm()
);


/*
|--------------------------------------------------------------------------
| AUTH CONFIG
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
| NUMBER
|--------------------------------------------------------------------------
*/

const number = (value) => {

    return Number(
        value || 0
    ).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| MONEY
|--------------------------------------------------------------------------
*/

const money = (value) => {

    return Number(
        value || 0
    ).toLocaleString(
        undefined,
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

const formatDate = (value) => {

    if (!value) {

        return '-';

    }


    const parts =
        String(value)
            .substring(0, 10)
            .split('-');


    if (
        parts.length !== 3
    ) {

        return value;

    }


    return `${parts[2]}-${parts[1]}-${parts[0]}`;

};


/*
|--------------------------------------------------------------------------
| TYPE LABEL
|--------------------------------------------------------------------------
*/

const typeLabel = (
    type
) => {

    switch (type) {

        case 'ADJUSTMENT_IN':

            return 'Adjustment IN';

        case 'ADJUSTMENT_OUT':

            return 'Adjustment OUT';

        default:

            return type || '-';

    }

};


/*
|--------------------------------------------------------------------------
| TYPE CLASS
|--------------------------------------------------------------------------
*/

const typeClass = (
    type
) => {

    switch (type) {

        case 'ADJUSTMENT_IN':

            return 'bg-success';

        case 'ADJUSTMENT_OUT':

            return 'bg-danger';

        default:

            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD ITEMS
|--------------------------------------------------------------------------
*/

const loadItems = async () => {

    try {

        const response =
            await axios.get(

                '/api/inventory/stock-adjustments/items',

                getAuthConfig()

            );


        const data =
            response.data?.data;


        if (
            Array.isArray(data)
        ) {

            items.value =
                data;

        } else {

            items.value =
                data?.data ||
                [];

        }


    } catch (err) {

        console.warn(
            'Adjustment items API failed. Trying inventory items API.',
            err
        );


        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await axios.get(

                    '/api/inventory/items',

                    {

                        ...getAuthConfig(),

                        params: {

                            page: 1,

                            per_page: 1000

                        }

                    }

                );


            const data =
                response.data?.data;


            if (
                Array.isArray(data)
            ) {

                items.value =
                    data;

            } else {

                items.value =
                    data?.data ||
                    [];

            }

        } catch (fallbackError) {

            console.error(
                'Unable to load inventory items:',
                fallbackError
            );


            error.value =
                fallbackError.response?.data?.message ||
                'Unable to load inventory items.';

        }

    }

};


/*
|--------------------------------------------------------------------------
| LOAD ADJUSTMENTS
|--------------------------------------------------------------------------
*/

const loadAdjustments = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/inventory/stock-adjustments',

                {

                    ...getAuthConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        inventory_item_id:
                            filters.value
                                .inventory_item_id ||
                            undefined,

                        movement_type:
                            filters.value
                                .movement_type ||
                            undefined,

                        from_date:
                            filters.value
                                .from_date ||
                            undefined,

                        to_date:
                            filters.value
                                .to_date ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        adjustments.value =
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
            'Adjustment loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stock adjustments.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    form.value =
        getEmptyForm();

    formErrors.value = {};

    formError.value = '';

    quantityError.value = '';

    currentStock.value = null;

    showModal.value = true;

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

};


/*
|--------------------------------------------------------------------------
| ITEM CHANGED
|--------------------------------------------------------------------------
*/

const itemChanged = () => {

    const selected =
        items.value.find(

            item =>
                Number(item.id) ===
                Number(
                    form.value.inventory_item_id
                )

        );


    if (!selected) {

        currentStock.value =
            null;

        form.value.unit_price =
            0;

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Current stock
    |--------------------------------------------------------------------------
    */

    if (
        selected.current_stock !==
        undefined
    ) {

        currentStock.value =
            Number(
                selected.current_stock
            );

    } else {

        currentStock.value =
            Number(
                selected.opening_stock ||
                0
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Default price
    |--------------------------------------------------------------------------
    */

    form.value.unit_price =
        Number(
            selected.cost_price ||
            0
        );


    validateQuantity();

};


/*
|--------------------------------------------------------------------------
| QUANTITY VALIDATION
|--------------------------------------------------------------------------
*/

const validateQuantity = () => {

    quantityError.value = '';


    const quantity =
        Number(
            form.value.quantity ||
            0
        );


    if (
        quantity <= 0
    ) {

        quantityError.value =
            'Quantity must be greater than zero.';

        return;

    }


    if (
        form.value.adjustment_type ===
            'ADJUSTMENT_OUT' &&
        currentStock.value !== null &&
        quantity >
            Number(
                currentStock.value
            )
    ) {

        quantityError.value =
            `Quantity cannot exceed available stock (${number(currentStock.value)}).`;

    }

};


/*
|--------------------------------------------------------------------------
| PREVIEW STOCK
|--------------------------------------------------------------------------
*/

const previewStock = computed(() => {

    const stock =
        Number(
            currentStock.value ||
            0
        );


    const quantity =
        Number(
            form.value.quantity ||
            0
        );


    if (
        form.value.adjustment_type ===
        'ADJUSTMENT_OUT'
    ) {

        return stock - quantity;

    }


    return stock + quantity;

});


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveAdjustment = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';


    validateQuantity();


    if (
        quantityError.value
    ) {

        formError.value =
            quantityError.value;

        return;

    }


    if (
        !form.value.inventory_item_id
    ) {

        formError.value =
            'Please select an inventory item.';

        return;

    }


    saving.value = true;


    try {

        const payload = {

            inventory_item_id:
                Number(
                    form.value.inventory_item_id
                ),

            adjustment_type:
                form.value.adjustment_type,

            quantity:
                Number(
                    form.value.quantity
                ),

            unit_price:
                Number(
                    form.value.unit_price ||
                    0
                ),

            movement_date:
                form.value.movement_date,

            reason:
                form.value.reason ||
                null,

            remarks:
                form.value.remarks ||
                null

        };


        const response =
            await axios.post(

                '/api/inventory/stock-adjustments',

                payload,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Stock adjustment saved successfully.';


        showModal.value =
            false;


        await loadItems();

        await loadAdjustments(
            1
        );


    } catch (err) {

        console.error(
            'Save adjustment error:',
            err
        );


        if (
            err.response?.status ===
            422
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
                'Unable to save stock adjustment.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

const viewAdjustment = async (
    id
) => {

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedAdjustment.value =
        null;


    try {

        /*
        |--------------------------------------------------------------------------
        | Use existing stock movement show endpoint
        |--------------------------------------------------------------------------
        */

        const response =
            await axios.get(

                `/api/inventory/stock-movements/${id}`,

                getAuthConfig()

            );


        selectedAdjustment.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'View adjustment error:',
            err
        );


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load adjustment details.';

    } finally {

        viewLoading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        inventory_item_id: '',

        movement_type: '',

        from_date: '',

        to_date: '',

        per_page: 20

    };


    loadAdjustments(1);

};


/*
|--------------------------------------------------------------------------
| PAGE
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


    loadAdjustments(
        page
    );

};


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadItems();

        await loadAdjustments(
            1
        );

    }
);

</script>