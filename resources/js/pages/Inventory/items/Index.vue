<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Items
                </h4>

                <small class="text-muted">
                    Manage inventory items
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Item
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
                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Name, SKU or barcode..."
                            @keyup.enter="loadItems(1)"
                        >

                    </div>


                    <!-- Category -->
                    <div class="col-md-2">

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


                    <!-- Unit -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Unit
                        </label>

                        <select
                            v-model="
                                filters.inventory_unit_id
                            "
                            class="form-select"
                        >

                            <option value="">
                                All Units
                            </option>

                            <option
                                v-for="unit in units"
                                :key="unit.id"
                                :value="unit.id"
                            >
                                {{ unit.name }}
                                {{
                                    unit.code
                                        ? `(${unit.code})`
                                        : ''
                                }}
                            </option>

                        </select>

                    </div>


                    <!-- Status -->
                    <div class="col-md-2">

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


                    <!-- Buttons -->
                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-2"
                            :disabled="loading"
                            @click="loadItems(1)"
                        >
                            <i class="bi bi-search"></i>
                            Search
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetFilters"
                            title="Reset"
                        >
                            <i
                                class="bi bi-arrow-counterclockwise"
                            ></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Items Table -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Inventory Item List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-bordered table-hover mb-0 align-middle"
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
                                    Category
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Barcode
                                </th>

                                <th class="text-end">
                                    Stock
                                </th>

                                <th class="text-end">
                                    Cost
                                </th>

                                <th class="text-end">
                                    Sale
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="150"
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

                                    <div
                                        class="text-muted mt-2"
                                    >
                                        Loading inventory items...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    items.length === 0
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


                            <!-- Records -->
                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in items"
                                :key="item.id"
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


                                <!-- Item -->
                                <td>

                                    <strong>
                                        {{ item.name }}
                                    </strong>

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

                                    <small
                                        v-if="
                                            item.unit?.code
                                        "
                                        class="text-muted"
                                    >
                                        ({{ item.unit.code }})
                                    </small>

                                </td>


                                <!-- Supplier -->
                                <td>

                                    {{
                                        item.supplier?.name ||
                                        '-'
                                    }}

                                </td>


                                <!-- SKU -->
                                <td>

                                    {{ item.sku || '-' }}

                                </td>


                                <!-- Barcode -->
                                <td>

                                    {{ item.barcode || '-' }}

                                </td>


                                <!-- Stock -->
                                <td class="text-end">

                                    {{
                                        formatNumber(
                                            item.opening_stock
                                        )
                                    }}

                                    <div
                                        v-if="
                                            Number(
                                                item.opening_stock
                                            ) <=
                                            Number(
                                                item.reorder_level
                                            )
                                        "
                                        class="small text-danger"
                                    >
                                        Low Stock
                                    </div>

                                </td>


                                <!-- Cost -->
                                <td class="text-end">

                                    {{
                                        formatNumber(
                                            item.cost_price
                                        )
                                    }}

                                </td>


                                <!-- Sale -->
                                <td class="text-end">

                                    {{
                                        formatNumber(
                                            item.sale_price
                                        )
                                    }}

                                </td>


                                <!-- Status -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            item.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >

                                        {{
                                            item.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}

                                    </span>

                                </td>


                                <!-- Actions -->
                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        :disabled="
                                            actionId === item.id
                                        "
                                        @click="
                                            openEdit(item)
                                        "
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        :disabled="
                                            actionId === item.id
                                        "
                                        @click="
                                            toggleStatus(item)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === item.id
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
                                            actionId === item.id
                                        "
                                        @click="
                                            deleteItem(item)
                                        "
                                    >

                                        <i
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

            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveItem"
                    >

                        <!-- Modal Header -->
                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Inventory Item'
                                        : 'Add Inventory Item'
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

                            <!-- Error -->
                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Item Name -->
                                <div class="col-md-6">

                                    <label class="form-label">

                                        Item Name

                                        <span
                                            class="text-danger"
                                        >
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
                                        placeholder="Enter item name"
                                    >


                                    <div
                                        v-if="formErrors.name"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.name }}
                                    </div>

                                </div>


                                <!-- Category -->
                                <div class="col-md-3">

                                    <label class="form-label">

                                        Category

                                        <span
                                            class="text-danger"
                                        >
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.inventory_category_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.inventory_category_id
                                        }"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option
                                            v-for="
                                                category in categories
                                            "
                                            :key="
                                                category.id
                                            "
                                            :value="
                                                category.id
                                            "
                                        >
                                            {{ category.name }}
                                        </option>

                                    </select>


                                    <div
                                        v-if="
                                            formErrors.inventory_category_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.inventory_category_id
                                        }}
                                    </div>

                                </div>


                                <!-- Unit -->
                                <div class="col-md-3">

                                    <label class="form-label">

                                        Unit

                                        <span
                                            class="text-danger"
                                        >
                                            *
                                        </span>

                                    </label>


                                    <select
                                        v-model="
                                            form.inventory_unit_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.inventory_unit_id
                                        }"
                                    >

                                        <option value="">
                                            Select Unit
                                        </option>

                                        <option
                                            v-for="
                                                unit in units
                                            "
                                            :key="
                                                unit.id
                                            "
                                            :value="
                                                unit.id
                                            "
                                        >

                                            {{ unit.name }}

                                            {{
                                                unit.code
                                                    ? `(${unit.code})`
                                                    : ''
                                            }}

                                        </option>

                                    </select>


                                    <div
                                        v-if="
                                            formErrors.inventory_unit_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.inventory_unit_id
                                        }}
                                    </div>

                                </div>


                                <!-- Supplier -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Supplier
                                    </label>


                                    <select
                                        v-model="
                                            form.inventory_supplier_id
                                        "
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.inventory_supplier_id
                                        }"
                                    >

                                        <option value="">
                                            Select Supplier
                                        </option>

                                        <option
                                            v-for="
                                                supplier in suppliers
                                            "
                                            :key="
                                                supplier.id
                                            "
                                            :value="
                                                supplier.id
                                            "
                                        >
                                            {{ supplier.name }}
                                        </option>

                                    </select>


                                    <div
                                        v-if="
                                            formErrors.inventory_supplier_id
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.inventory_supplier_id
                                        }}
                                    </div>

                                </div>


                                <!-- SKU -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        SKU
                                    </label>


                                    <input
                                        v-model="form.sku"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.sku
                                        }"
                                        placeholder="SKU"
                                    >


                                    <div
                                        v-if="formErrors.sku"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.sku }}
                                    </div>

                                </div>


                                <!-- Barcode -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Barcode
                                    </label>


                                    <input
                                        v-model="form.barcode"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.barcode
                                        }"
                                        placeholder="Barcode"
                                    >


                                    <div
                                        v-if="formErrors.barcode"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.barcode }}
                                    </div>

                                </div>


                                <!-- Opening Stock -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Opening Stock
                                    </label>


                                    <input
                                        v-model="
                                            form.opening_stock
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.opening_stock
                                        }"
                                    >


                                    <div
                                        v-if="
                                            formErrors.opening_stock
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.opening_stock
                                        }}
                                    </div>

                                </div>


                                <!-- Reorder Level -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Reorder Level
                                    </label>


                                    <input
                                        v-model="
                                            form.reorder_level
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.reorder_level
                                        }"
                                    >


                                    <div
                                        v-if="
                                            formErrors.reorder_level
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.reorder_level
                                        }}
                                    </div>

                                </div>


                                <!-- Cost Price -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Cost Price
                                    </label>


                                    <input
                                        v-model="
                                            form.cost_price
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.cost_price
                                        }"
                                    >


                                    <div
                                        v-if="
                                            formErrors.cost_price
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.cost_price
                                        }}
                                    </div>

                                </div>


                                <!-- Sale Price -->
                                <div class="col-md-3">

                                    <label class="form-label">
                                        Sale Price
                                    </label>


                                    <input
                                        v-model="
                                            form.sale_price
                                        "
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.sale_price
                                        }"
                                    >


                                    <div
                                        v-if="
                                            formErrors.sale_price
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.sale_price
                                        }}
                                    </div>

                                </div>


                                <!-- Status -->
                                <div class="col-md-3">

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
                                        ? 'Update Item'
                                        : 'Save Item'
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

const items =
    ref([]);

const categories =
    ref([]);

const units =
    ref([]);

const suppliers =
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

        inventory_category_id: '',

        inventory_unit_id: '',

        inventory_supplier_id: '',

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
| Empty Form
|--------------------------------------------------------------------------
*/

const emptyForm =
    () => ({

        inventory_category_id: '',

        inventory_unit_id: '',

        inventory_supplier_id: '',

        name: '',

        sku: '',

        barcode: '',

        opening_stock: 0,

        reorder_level: 0,

        cost_price: 0,

        sale_price: 0,

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

                Accept:
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
| Format Number
|--------------------------------------------------------------------------
*/

const formatNumber =
    (value) => {

        const number =
            Number(value || 0);

        return number.toLocaleString(
            undefined,
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );

    };


/*
|--------------------------------------------------------------------------
| Load Form Data
|--------------------------------------------------------------------------
*/

const loadFormData = async () => {

    try {

        const config = getAuthConfig();

        const [
            categoryResponse,
            unitResponse,
            supplierResponse
        ] = await Promise.all([

            axios.get(
                '/api/inventory/categories',
                {
                    ...config,
                    params: {
                        per_page: 1000
                    }
                }
            ),

            axios.get(
                '/api/inventory/units',
                {
                    ...config,
                    params: {
                        per_page: 1000
                    }
                }
            ),

            axios.get(
                '/api/inventory/suppliers',
                {
                    ...config,
                    params: {
                        per_page: 1000
                    }
                }
            )

        ]);


        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        const categoryData =
            categoryResponse.data?.data;

        categories.value =
            Array.isArray(categoryData)
                ? categoryData
                : (
                    categoryData?.data ||
                    []
                );


        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        const unitData =
            unitResponse.data?.data;

        units.value =
            Array.isArray(unitData)
                ? unitData
                : (
                    unitData?.data ||
                    []
                );


        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */

        const supplierData =
            supplierResponse.data?.data;

        suppliers.value =
            Array.isArray(supplierData)
                ? supplierData
                : (
                    supplierData?.data ||
                    []
                );


        console.log(
            'Inventory Categories:',
            categories.value
        );

        console.log(
            'Inventory Units:',
            units.value
        );

        console.log(
            'Inventory Suppliers:',
            suppliers.value
        );


    } catch (err) {

        console.error(
            'Unable to load inventory dropdown data:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load inventory categories, units or suppliers.';

    }

};


/*
|--------------------------------------------------------------------------
| Load Items
|--------------------------------------------------------------------------
*/

const loadItems =
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

                inventory_category_id:
                    filters.value
                        .inventory_category_id ||
                    undefined,

                inventory_unit_id:
                    filters.value
                        .inventory_unit_id ||
                    undefined,

                inventory_supplier_id:
                    filters.value
                        .inventory_supplier_id ||
                    undefined,

                status:
                    filters.value.status === ''
                        ? undefined
                        : filters.value.status

            };


            const response =
                await axios.get(

                    '/api/inventory/items',

                    {

                        ...getAuthConfig(),

                        params

                    }

                );


            const result =
                response.data?.data;


            items.value =
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
                'Inventory item loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load inventory items.';

        } finally {

            loading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Open Create
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
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit =
    (item) => {

        editing.value = true;

        editingId.value =
            item.id;


        form.value = {

            inventory_category_id:
                item.inventory_category_id ||
                '',

            inventory_unit_id:
                item.inventory_unit_id ||
                '',

            inventory_supplier_id:
                item.inventory_supplier_id ||
                '',

            name:
                item.name ||
                '',

            sku:
                item.sku ||
                '',

            barcode:
                item.barcode ||
                '',

            opening_stock:
                item.opening_stock ??
                0,

            reorder_level:
                item.reorder_level ??
                0,

            cost_price:
                item.cost_price ??
                0,

            sale_price:
                item.sale_price ??
                0,

            status:
                Boolean(
                    item.status
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
| Save Item
|--------------------------------------------------------------------------
*/

const saveItem =
    async () => {

        if (saving.value) {

            return;

        }


        saving.value = true;

        formErrors.value = {};

        formError.value = '';

        error.value = '';

        success.value = '';


        try {

            const payload = {

                inventory_category_id:
                    form.value.inventory_category_id,

                inventory_unit_id:
                    form.value.inventory_unit_id,

                inventory_supplier_id:
                    form.value.inventory_supplier_id ||
                    null,

                name:
                    form.value.name,

                sku:
                    form.value.sku ||
                    null,

                barcode:
                    form.value.barcode ||
                    null,

                opening_stock:
                    form.value.opening_stock || 0,

                reorder_level:
                    form.value.reorder_level || 0,

                cost_price:
                    form.value.cost_price || 0,

                sale_price:
                    form.value.sale_price || 0,

                status:
                    form.value.status

            };


            let response;


            if (editing.value) {

                response =
                    await axios.put(

                        `/api/inventory/items/${editingId.value}`,

                        payload,

                        getAuthConfig()

                    );

            } else {

                response =
                    await axios.post(

                        '/api/inventory/items',

                        payload,

                        getAuthConfig()

                    );

            }


            success.value =
                response.data?.message ||
                'Inventory item saved successfully.';


            showModal.value = false;


            await loadItems(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory item save error:',
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
                    'Unable to save inventory item.';

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
        item
    ) => {

        const newStatus =
            !Boolean(
                item.status
            );


        if (
            !window.confirm(

                `Change "${item.name}" status to ${
                    newStatus
                        ? 'Active'
                        : 'Inactive'
                }?`

            )
        ) {

            return;

        }


        actionId.value =
            item.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.post(

                    `/api/inventory/items/${item.id}/status`,

                    {

                        status:
                            newStatus

                    },

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory item status updated successfully.';


            await loadItems(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Inventory item status error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to update inventory item status.';

        } finally {

            actionId.value = null;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete Item
|--------------------------------------------------------------------------
*/

const deleteItem =
    async (
        item
    ) => {

        if (
            !window.confirm(

                `Delete inventory item "${item.name}"?`

            )
        ) {

            return;

        }


        actionId.value =
            item.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(

                    `/api/inventory/items/${item.id}`,

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Inventory item deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                items.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadItems(page);


        } catch (err) {

            console.error(
                'Delete inventory item error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete inventory item.';

        } finally {

            actionId.value = null;

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

            inventory_category_id: '',

            inventory_unit_id: '',

            inventory_supplier_id: '',

            status: '',

            per_page: 20

        };


        loadItems(1);

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


        loadItems(page);

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadFormData();

        await loadItems(1);

    }
);

</script>