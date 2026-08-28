<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Stock Ledger
                </h4>

                <small class="text-muted">
                    View all inventory stock movements
                </small>
            </div>

            <button
                type="button"
                class="btn btn-outline-primary"
                :disabled="loading"
                @click="loadMovements(1)"
            >
                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                <i
                    v-else
                    class="bi bi-arrow-clockwise me-1"
                ></i>

                Refresh
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
             CURRENT STOCK SUMMARY
        ========================================================== -->

        <div class="row g-3 mb-3">

            <div class="col-md-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="text-muted small">
                                    Total Movements
                                </div>

                                <div class="fs-4 fw-bold">
                                    {{ pagination.total }}
                                </div>

                            </div>

                            <div class="text-primary fs-2">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="text-muted small">
                                    Selected Item Stock
                                </div>

                                <div class="fs-4 fw-bold">

                                    {{
                                        selectedItemStock !== null
                                            ? number(selectedItemStock)
                                            : '-'
                                    }}

                                </div>

                            </div>

                            <div class="text-success fs-2">
                                <i class="bi bi-box-seam"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <div class="text-muted small">
                                    Selected Item
                                </div>

                                <div class="fw-bold mt-1">

                                    {{
                                        selectedItemName ||
                                        'All Items'
                                    }}

                                </div>

                            </div>

                            <div class="text-info fs-2">
                                <i class="bi bi-box"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

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
                            placeholder="Movement no / item / SKU"
                            @keyup.enter="applyFilters"
                        >

                    </div>


                    <!-- Item -->

                    <div class="col-md-3">

                        <label class="form-label">
                            Item
                        </label>

                        <select
                            v-model="filters.inventory_item_id"
                            class="form-select"
                            @change="itemFilterChanged"
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


                    <!-- Movement Type -->

                    <div class="col-md-2">

                        <label class="form-label">
                            Movement Type
                        </label>

                        <select
                            v-model="filters.movement_type"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="IN">
                                IN
                            </option>

                            <option value="OUT">
                                OUT
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
                            v-model="filters.from_date"
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
                            v-model="filters.to_date"
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
                            @click="applyFilters"
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
             LEDGER TABLE
        ========================================================== -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Stock Movement Ledger
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
                                    Total
                                </th>

                                <th class="text-end">
                                    Before
                                </th>

                                <th class="text-end">
                                    After
                                </th>

                                <th>
                                    Reference
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
                                    colspan="12"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading stock movements...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-else-if="
                                    movements.length === 0
                                "
                            >

                                <td
                                    colspan="12"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-inboxes fs-2 d-block mb-2"
                                    ></i>

                                    No stock movements found.

                                </td>

                            </tr>


                            <!-- Records -->

                            <tr
                                v-for="(
                                    movement,
                                    index
                                ) in movements"
                                :key="movement.id"
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
                                            movement.movement_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            movement.movement_no
                                        }}
                                    </strong>

                                </td>


                                <!-- Item -->

                                <td>

                                    <div>

                                        <strong>
                                            {{
                                                movement.item?.name ||
                                                '-'
                                            }}
                                        </strong>

                                    </div>

                                    <small
                                        v-if="
                                            movement.item?.sku
                                        "
                                        class="text-muted"
                                    >
                                        SKU:
                                        {{
                                            movement.item.sku
                                        }}
                                    </small>

                                </td>


                                <!-- Type -->

                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            movementTypeClass(
                                                movement.movement_type
                                            )
                                        "
                                    >

                                        {{
                                            movementTypeLabel(
                                                movement.movement_type
                                            )
                                        }}

                                    </span>

                                </td>


                                <!-- Quantity -->

                                <td
                                    class="text-end"
                                >

                                    <strong>
                                        {{
                                            number(
                                                movement.quantity
                                            )
                                        }}
                                    </strong>

                                </td>


                                <!-- Unit price -->

                                <td
                                    class="text-end"
                                >

                                    {{
                                        money(
                                            movement.unit_price
                                        )
                                    }}

                                </td>


                                <!-- Total -->

                                <td
                                    class="text-end"
                                >

                                    {{
                                        money(
                                            movement.total_amount
                                        )
                                    }}

                                </td>


                                <!-- Before -->

                                <td
                                    class="text-end"
                                >

                                    {{
                                        number(
                                            movement.stock_before
                                        )
                                    }}

                                </td>


                                <!-- After -->

                                <td
                                    class="text-end"
                                >

                                    <strong>
                                        {{
                                            number(
                                                movement.stock_after
                                            )
                                        }}
                                    </strong>

                                </td>


                                <!-- Reference -->

                                <td>

                                    <div>
                                        {{
                                            movement.reference_type ||
                                            '-'
                                        }}
                                    </div>

                                    <small
                                        v-if="
                                            movement.reference_id
                                        "
                                        class="text-muted"
                                    >
                                        #{{ movement.reference_id }}
                                    </small>

                                </td>


                                <!-- Action -->

                                <td
                                    class="text-center"
                                >

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info"
                                        title="View"
                                        @click="
                                            viewMovement(
                                                movement.id
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
                            Stock Movement Details
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
                                selectedMovement
                            "
                        >

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Movement No.
                                    </label>

                                    <div class="fw-bold">

                                        {{
                                            selectedMovement.movement_no
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
                                                selectedMovement.movement_date
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-4">

                                    <label class="text-muted small">
                                        Movement Type
                                    </label>

                                    <div>

                                        <span
                                            class="badge"
                                            :class="
                                                movementTypeClass(
                                                    selectedMovement.movement_type
                                                )
                                            "
                                        >

                                            {{
                                                movementTypeLabel(
                                                    selectedMovement.movement_type
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
                                            selectedMovement.item?.name ||
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
                                            selectedMovement.item?.sku ||
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
                                                selectedMovement.quantity
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
                                                selectedMovement.unit_price
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
                                                selectedMovement.stock_before
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
                                                selectedMovement.stock_after
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Reference Type
                                    </label>

                                    <div>

                                        {{
                                            selectedMovement.reference_type ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Reference ID
                                    </label>

                                    <div>

                                        {{
                                            selectedMovement.reference_id ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-12">

                                    <label class="text-muted small">
                                        Remarks
                                    </label>

                                    <div>

                                        {{
                                            selectedMovement.remarks ||
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
                                            selectedMovement.creator?.name ||
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
    onMounted,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const movements = ref([]);

const items = ref([]);

const loading = ref(false);

const error = ref('');

const success = ref('');

const selectedItemStock = ref(null);

const selectedItemName = ref('');

const showViewModal = ref(false);

const viewLoading = ref(false);

const selectedMovement = ref(null);


/*
|--------------------------------------------------------------------------
| Filters
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
| Auth
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
| Number
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
| Money
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
| Date
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
| Movement Type Label
|--------------------------------------------------------------------------
*/

const movementTypeLabel = (
    type
) => {

    switch (type) {

        case 'IN':
            return 'IN';

        case 'OUT':
            return 'OUT';

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
| Movement Type Class
|--------------------------------------------------------------------------
*/

const movementTypeClass = (
    type
) => {

    switch (type) {

        case 'IN':
            return 'bg-success';

        case 'OUT':
            return 'bg-danger';

        case 'ADJUSTMENT_IN':
            return 'bg-info';

        case 'ADJUSTMENT_OUT':
            return 'bg-warning text-dark';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| Load Items
|--------------------------------------------------------------------------
*/

const loadItems = async () => {

    try {

        const response =
            await axios.get(

                '/api/inventory/stock-movements/items',

                getAuthConfig()

            );


        const data =
            response.data?.data;


        if (
            Array.isArray(data)
        ) {

            items.value = data;

        } else {

            items.value =
                data?.data ||
                [];

        }


    } catch (err) {

        console.error(
            'Unable to load inventory items:',
            err
        );


        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        |
        | If the stock movement items endpoint isn't available,
        | use the normal inventory items API.
        |
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

                items.value = data;

            } else {

                items.value =
                    data?.data ||
                    [];

            }

        } catch (fallbackError) {

            console.error(
                'Inventory item fallback failed:',
                fallbackError
            );

        }

    }

};


/*
|--------------------------------------------------------------------------
| Load Movements
|--------------------------------------------------------------------------
*/

const loadMovements = async (
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

            inventory_item_id:
                filters.value.inventory_item_id ||
                undefined,

            movement_type:
                filters.value.movement_type ||
                undefined,

            from_date:
                filters.value.from_date ||
                undefined,

            to_date:
                filters.value.to_date ||
                undefined

        };


        const response =
            await axios.get(

                '/api/inventory/stock-movements',

                {

                    ...getAuthConfig(),

                    params

                }

            );


        const result =
            response.data?.data;


        movements.value =
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
            'Stock movement loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stock movements.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Item Filter Changed
|--------------------------------------------------------------------------
*/

const itemFilterChanged = async () => {

    const selected =
        items.value.find(

            item =>
                Number(item.id) ===
                Number(
                    filters.value.inventory_item_id
                )

        );


    selectedItemName.value =
        selected?.name ||
        '';


    if (
        !filters.value.inventory_item_id
    ) {

        selectedItemStock.value =
            null;

    } else {

        /*
        |--------------------------------------------------------------------------
        | Get current stock from item API if available
        |--------------------------------------------------------------------------
        */

        if (
            selected?.current_stock !==
            undefined
        ) {

            selectedItemStock.value =
                Number(
                    selected.current_stock
                );

        } else {

            selectedItemStock.value =
                null;

        }

    }


    await loadMovements(1);

};


/*
|--------------------------------------------------------------------------
| Apply Filters
|--------------------------------------------------------------------------
*/

const applyFilters = () => {

    loadMovements(1);

};


/*
|--------------------------------------------------------------------------
| Reset Filters
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


    selectedItemStock.value =
        null;

    selectedItemName.value =
        '';


    loadMovements(1);

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


    loadMovements(
        page
    );

};


/*
|--------------------------------------------------------------------------
| View Movement
|--------------------------------------------------------------------------
*/

const viewMovement = async (
    id
) => {

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedMovement.value =
        null;


    try {

        const response =
            await axios.get(

                `/api/inventory/stock-movements/${id}`,

                getAuthConfig()

            );


        selectedMovement.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Movement view error:',
            err
        );


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load movement details.';

    } finally {

        viewLoading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadItems();

        await loadMovements(1);

    }
);

</script>