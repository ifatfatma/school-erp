<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div
            class="d-flex justify-content-between align-items-center mb-3"
        >

            <div>
                <h4 class="mb-1">
                    Inventory Dashboard
                </h4>

                <small class="text-muted">
                    Current stock and inventory summary
                </small>
            </div>

            <button
                type="button"
                class="btn btn-outline-primary"
                :disabled="loading || summaryLoading"
                @click="refresh"
            >

                <span
                    v-if="loading || summaryLoading"
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
             ALERT
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
             SUMMARY CARDS
        ========================================================== -->

        <div class="row g-3 mb-4">

            <!-- Total Items -->

            <div class="col-xl-3 col-md-6">

                <div class="card h-100">

                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <div class="text-muted small">
                                    Total Items
                                </div>

                                <div class="fs-3 fw-bold">
                                    {{ summary.total_items }}
                                </div>

                            </div>

                            <div class="fs-1 text-primary">
                                <i class="bi bi-box-seam"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Current Stock -->

            <div class="col-xl-3 col-md-6">

                <div class="card h-100">

                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <div class="text-muted small">
                                    Current Stock
                                </div>

                                <div class="fs-3 fw-bold text-success">
                                    {{ number(summary.current_stock) }}
                                </div>

                            </div>

                            <div class="fs-1 text-success">
                                <i class="bi bi-boxes"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Low Stock -->

            <div class="col-xl-3 col-md-6">

                <div class="card h-100">

                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <div class="text-muted small">
                                    Low Stock
                                </div>

                                <div class="fs-3 fw-bold text-warning">
                                    {{ summary.low_stock_items }}
                                </div>

                            </div>

                            <div class="fs-1 text-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Out of Stock -->

            <div class="col-xl-3 col-md-6">

                <div class="card h-100">

                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <div>

                                <div class="text-muted small">
                                    Out of Stock
                                </div>

                                <div class="fs-3 fw-bold text-danger">
                                    {{ summary.out_of_stock_items }}
                                </div>

                            </div>

                            <div class="fs-1 text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             MOVEMENT SUMMARY
        ========================================================== -->

        <div class="row g-3 mb-4">

            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <div class="text-muted small">
                            Opening Stock
                        </div>

                        <div class="fs-5 fw-bold">
                            {{ number(summary.opening_stock) }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <div class="text-muted small">
                            Stock In
                        </div>

                        <div class="fs-5 fw-bold text-success">
                            +{{ number(summary.stock_in) }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <div class="text-muted small">
                            Stock Out
                        </div>

                        <div class="fs-5 fw-bold text-danger">
                            -{{ number(summary.stock_out) }}
                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="card">

                    <div class="card-body">

                        <div class="text-muted small">
                            Adjustments
                        </div>

                        <div class="fs-5 fw-bold">

                            <span class="text-success">
                                +{{ number(summary.adjustment_in) }}
                            </span>

                            <span class="mx-1">
                                /
                            </span>

                            <span class="text-danger">
                                -{{ number(summary.adjustment_out) }}
                            </span>

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
                    Inventory Search
                </strong>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    <!-- Search -->

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Item name / SKU / barcode"
                            @keyup.enter="applyFilters"
                        >

                    </div>


                    <!-- Category -->

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="
                                filters.inventory_category_id
                            "
                            class="form-select"
                        >

                            <option value="">
                                All Categories
                            </option>

                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >

                                {{ category.name }}

                            </option>

                        </select>

                    </div>


                    <!-- Stock Status -->

                    <div class="col-lg-3 col-md-6">

                        <label class="form-label">
                            Stock Status
                        </label>

                        <select
                            v-model="filters.stock_status"
                            class="form-select"
                        >

                            <option value="">
                                All Stock
                            </option>

                            <option value="in_stock">
                                In Stock
                            </option>

                            <option value="low_stock">
                                Low Stock
                            </option>

                            <option value="out_of_stock">
                                Out of Stock
                            </option>

                        </select>

                    </div>


                    <!-- Buttons -->

                    <div
                        class="col-lg-2 col-md-6 d-flex align-items-end gap-2"
                    >

                        <button
                            type="button"
                            class="btn btn-primary flex-grow-1"
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
                            title="Reset"
                            @click="resetFilters"
                        >

                            <i
                                class="bi bi-arrow-counterclockwise"
                            ></i>

                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             CURRENT STOCK TABLE
        ========================================================== -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <div>

                    <strong>
                        Current Stock
                    </strong>

                </div>

                <span class="text-muted">
                    {{ pagination.total }} items
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
                                    Item
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th class="text-end">
                                    Opening
                                </th>

                                <th class="text-end">
                                    Stock In
                                </th>

                                <th class="text-end">
                                    Stock Out
                                </th>

                                <th class="text-end">
                                    Adjustment
                                </th>

                                <th class="text-end">
                                    Current Stock
                                </th>

                                <th class="text-end">
                                    Reorder Level
                                </th>

                                <th>
                                    Status
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
                                        Loading inventory...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-else-if="
                                    inventory.length === 0
                                "
                            >

                                <td
                                    colspan="12"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-box-seam fs-2 d-block mb-2"
                                    ></i>

                                    No inventory items found.

                                </td>

                            </tr>


                            <!-- Data -->

                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in inventory"
                                :key="item.id"
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


                                <!-- Item -->

                                <td>

                                    <div class="fw-semibold">

                                        {{ item.name }}

                                    </div>

                                    <small
                                        v-if="item.barcode"
                                        class="text-muted"
                                    >

                                        Barcode:
                                        {{ item.barcode }}

                                    </small>

                                </td>


                                <!-- SKU -->

                                <td>

                                    {{
                                        item.sku ||
                                        '-'
                                    }}

                                </td>


                                <!-- Category -->

                                <td>

                                    {{
                                        item.category?.name ||
                                        '-'
                                    }}

                                </td>


                                <!-- Unit -->

                                <td>

                                    {{
                                        item.unit?.name ||
                                        '-'
                                    }}

                                </td>


                                <!-- Opening -->

                                <td
                                    class="text-end"
                                >

                                    {{
                                        number(
                                            item.opening_stock
                                        )
                                    }}

                                </td>


                                <!-- Stock In -->

                                <td
                                    class="text-end text-success"
                                >

                                    +{{
                                        number(
                                            item.stock_in
                                        )
                                    }}

                                </td>


                                <!-- Stock Out -->

                                <td
                                    class="text-end text-danger"
                                >

                                    -{{
                                        number(
                                            item.stock_out
                                        )
                                    }}

                                </td>


                                <!-- Adjustment -->

                                <td
                                    class="text-end"
                                >

                                    <span
                                        class="text-success"
                                    >
                                        +{{
                                            number(
                                                item.adjustment_in
                                            )
                                        }}
                                    </span>

                                    <span class="mx-1">
                                        /
                                    </span>

                                    <span
                                        class="text-danger"
                                    >
                                        -{{
                                            number(
                                                item.adjustment_out
                                            )
                                        }}
                                    </span>

                                </td>


                                <!-- Current -->

                                <td
                                    class="text-end"
                                >

                                    <strong
                                        class="fs-6"
                                    >

                                        {{
                                            number(
                                                item.current_stock
                                            )
                                        }}

                                    </strong>

                                </td>


                                <!-- Reorder -->

                                <td
                                    class="text-end"
                                >

                                    {{
                                        number(
                                            item.reorder_level
                                        )
                                    }}

                                </td>


                                <!-- Status -->

                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            stockStatusClass(
                                                item.stock_status
                                            )
                                        "
                                    >

                                        {{
                                            item.stock_status ||
                                            'In Stock'
                                        }}

                                    </span>

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


                        <span
                            class="mx-2"
                        >

                            Page
                            {{ pagination.current_page }}
                            of
                            {{ pagination.last_page }}

                        </span>


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

    </div>
</template>


<script setup>

import { onMounted, onBeforeUnmount,
    ref
} from 'vue';

import axios from 'axios';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const inventory = ref([]);

const categories = ref([]);

const loading = ref(false);

const summaryLoading = ref(false);

const error = ref('');


/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const summary = ref({

    total_items: 0,

    total_categories: 0,

    opening_stock: 0,

    stock_in: 0,

    stock_out: 0,

    adjustment_in: 0,

    adjustment_out: 0,

    current_stock: 0,

    low_stock_items: 0,

    out_of_stock_items: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    inventory_category_id: '',

    stock_status: '',

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
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) {
        config.headers['X-School-Id'] = activeSchoolId;
    }


    return config;

};


/*
|--------------------------------------------------------------------------
| Number Formatter
|--------------------------------------------------------------------------
*/

const number = (
    value
) => {

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
| Load Summary
|--------------------------------------------------------------------------
*/

const loadSummary = async () => {

    summaryLoading.value = true;


    try {

        const response =
            await axios.get(

                '/api/inventory/dashboard/summary',

                getAuthConfig()

            );


        if (
            response.data?.success
        ) {

            summary.value =
                {
                    ...summary.value,
                    ...(response.data.data || {})
                };

        }


    } catch (err) {

        console.error(
            'Inventory summary error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load inventory summary.';

    } finally {

        summaryLoading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Categories
|--------------------------------------------------------------------------
*/

const loadCategories = async () => {

    try {

        const response =
            await axios.get(

                '/api/inventory/dashboard/categories',

                getAuthConfig()

            );


        const data =
            response.data?.data;


        if (
            Array.isArray(data)
        ) {

            categories.value =
                data;

        } else {

            categories.value =
                data?.data ||
                [];

        }


    } catch (err) {

        console.error(
            'Inventory categories error:',
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

                    '/api/inventory/categories',

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


            categories.value =
                Array.isArray(data)
                    ? data
                    : data?.data || [];


        } catch (fallbackError) {

            console.error(
                'Category fallback error:',
                fallbackError
            );

        }

    }

};


/*
|--------------------------------------------------------------------------
| Load Inventory
|--------------------------------------------------------------------------
*/

const loadInventory = async (
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

            inventory_category_id:
                filters.value
                    .inventory_category_id ||
                undefined

        };


        const response =
            await axios.get(

                '/api/inventory/dashboard',

                {

                    ...getAuthConfig(),

                    params

                }

            );


        const result =
            response.data?.data;


        let rows =
            result?.data ||
            [];


        /*
        |--------------------------------------------------------------------------
        | Client-side stock status filter
        |--------------------------------------------------------------------------
        */

        if (
            filters.value.stock_status
        ) {

            rows =
                rows.filter(
                    item => {

                        const stock =
                            Number(
                                item.current_stock ||
                                0
                            );

                        const reorder =
                            Number(
                                item.reorder_level ||
                                0
                            );


                        if (
                            filters.value.stock_status ===
                            'out_of_stock'
                        ) {

                            return stock <= 0;

                        }


                        if (
                            filters.value.stock_status ===
                            'low_stock'
                        ) {

                            return (
                                stock > 0 &&
                                stock <= reorder
                            );

                        }


                        if (
                            filters.value.stock_status ===
                            'in_stock'
                        ) {

                            return stock > reorder;

                        }


                        return true;

                    }
                );

        }


        inventory.value =
            rows;


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
            'Inventory dashboard error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load inventory.';

    } finally {

        loading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| Stock Status Class
|--------------------------------------------------------------------------
*/

const stockStatusClass = (
    status
) => {

    switch (status) {

        case 'Out of Stock':

            return 'bg-danger';

        case 'Low Stock':

            return 'bg-warning text-dark';

        case 'In Stock':

            return 'bg-success';

        default:

            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| Apply Filters
|--------------------------------------------------------------------------
*/

const applyFilters = () => {

    loadInventory(1);

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        inventory_category_id: '',

        stock_status: '',

        per_page: 20

    };


    loadInventory(1);

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


    loadInventory(
        page
    );

};


/*
|--------------------------------------------------------------------------
| Refresh
|--------------------------------------------------------------------------
*/

const refresh = async () => {

    await Promise.all([
        loadSummary(),
        loadInventory(
            pagination.value.current_page
        ),
        loadCategories()
    ]);

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([
            loadSummary(),
            loadCategories(),
            loadInventory(1)
        ]);

    }
);



const onTenantChanged = () => { refresh(); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>