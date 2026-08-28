<template>
    <div class="container-fluid">

        <!-- ========================================================= -->
        <!-- PAGE HEADER -->
        <!-- ========================================================= -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Stock In
                </h4>

                <small class="text-muted">
                    Manage incoming inventory stock
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Stock In
            </button>

        </div>


        <!-- ========================================================= -->
        <!-- SUCCESS -->
        <!-- ========================================================= -->

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


        <!-- ========================================================= -->
        <!-- ERROR -->
        <!-- ========================================================= -->

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


        <!-- ========================================================= -->
        <!-- FILTERS -->
        <!-- ========================================================= -->

        <div class="card mb-3">

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
                            placeholder="Stock In No / Invoice / Supplier"
                            @keyup.enter="loadStockIns(1)"
                        >

                    </div>


                    <!-- Supplier -->

                    <div class="col-md-2">

                        <label class="form-label">
                            Supplier
                        </label>

                        <select
                            v-model="
                                filters.inventory_supplier_id
                            "
                            class="form-select"
                        >

                            <option value="">
                                All Suppliers
                            </option>

                            <option
                                v-for="supplier in suppliers"
                                :key="supplier.id"
                                :value="supplier.id"
                            >
                                {{ supplier.name }}
                                {{
                                    supplier.code
                                        ? ` (${supplier.code})`
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
                                All Status
                            </option>

                            <option value="Draft">
                                Draft
                            </option>

                            <option value="Received">
                                Received
                            </option>

                            <option value="Cancelled">
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <!-- From Date -->

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


                    <!-- To Date -->

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
                        class="col-md-1 d-flex align-items-end"
                    >

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            title="Search"
                            @click="loadStockIns(1)"
                        >
                            <i class="bi bi-search"></i>
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


        <!-- ========================================================= -->
        <!-- LIST -->
        <!-- ========================================================= -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Stock In List
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
                                    Stock In No.
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Invoice No.
                                </th>

                                <th class="text-end">
                                    Subtotal
                                </th>

                                <th class="text-end">
                                    Discount
                                </th>

                                <th class="text-end">
                                    Tax
                                </th>

                                <th class="text-end">
                                    Total
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
                                    colspan="11"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading stock entries...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-else-if="
                                    stockIns.length === 0
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-box-seam fs-2 d-block mb-2"
                                    ></i>

                                    No stock entries found.

                                </td>

                            </tr>


                            <!-- Records -->

                            <tr
                                v-for="(
                                    stockIn,
                                    index
                                ) in stockIns"
                                :key="stockIn.id"
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

                                    <strong>
                                        {{ stockIn.stock_in_no }}
                                    </strong>

                                </td>


                                <td>
                                    {{
                                        formatDate(
                                            stockIn.stock_in_date
                                        )
                                    }}
                                </td>


                                <td>

                                    {{
                                        stockIn.supplier?.name ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    {{
                                        stockIn.invoice_no ||
                                        '-'
                                    }}

                                </td>


                                <td class="text-end">

                                    {{
                                        money(
                                            stockIn.subtotal
                                        )
                                    }}

                                </td>


                                <td class="text-end">

                                    {{
                                        money(
                                            stockIn.discount
                                        )
                                    }}

                                </td>


                                <td class="text-end">

                                    {{
                                        money(
                                            stockIn.tax
                                        )
                                    }}

                                </td>


                                <td class="text-end">

                                    <strong>
                                        {{
                                            money(
                                                stockIn.total_amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                stockIn.status
                                            )
                                        "
                                    >
                                        {{ stockIn.status }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- View -->

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info me-1"
                                        title="View"
                                        @click="
                                            viewStockIn(
                                                stockIn.id
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-eye"
                                        ></i>

                                    </button>


                                    <!-- Edit -->

                                    <button
                                        v-if="
                                            stockIn.status !==
                                            'Received'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                stockIn.id
                                            )
                                        "
                                    >

                                        <i
                                            class="bi bi-pencil"
                                        ></i>

                                    </button>


                                    <!-- Delete -->

                                    <button
                                        v-if="
                                            stockIn.status !==
                                            'Received'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            stockIn.id
                                        "
                                        @click="
                                            deleteStockIn(
                                                stockIn
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                stockIn.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-trash"
                                        ></i>

                                    </button>

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
                                    pagination.current_page -
                                    1
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

        </div>


        <!-- ========================================================= -->
        <!-- ADD / EDIT MODAL -->
        <!-- ========================================================= -->

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
                        @submit.prevent="saveStockIn"
                    >

                        <!-- Header -->

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Inventory Stock In'
                                        : 'Add Inventory Stock In'
                                }}

                            </h5>


                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeModal"
                            ></button>

                        </div>


                        <!-- Body -->

                        <div class="modal-body">

                            <!-- Error -->

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <!-- ================================================= -->
                            <!-- BASIC INFORMATION -->
                            <!-- ================================================= -->

                            <div class="row g-3 mb-3">

                                <!-- Date -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Stock In Date
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.stock_in_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.stock_in_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.stock_in_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.stock_in_date
                                        }}
                                    </div>

                                </div>


                                <!-- Supplier -->

                                <div class="col-md-4">

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
                                            :key="supplier.id"
                                            :value="supplier.id"
                                        >

                                            {{ supplier.name }}

                                            {{
                                                supplier.code
                                                    ? ` (${supplier.code})`
                                                    : ''
                                            }}

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


                                <!-- Invoice -->

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Invoice No.
                                    </label>

                                    <input
                                        v-model="
                                            form.invoice_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Supplier invoice number"
                                    >

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

                                        <option value="Draft">
                                            Draft
                                        </option>

                                        <option value="Received">
                                            Received
                                        </option>

                                        <option value="Cancelled">
                                            Cancelled
                                        </option>

                                    </select>

                                </div>


                                <!-- Remarks -->

                                <div class="col-md-8">

                                    <label class="form-label">
                                        Remarks
                                    </label>

                                    <input
                                        v-model="form.remarks"
                                        type="text"
                                        class="form-control"
                                        placeholder="Optional remarks"
                                    >

                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- ITEMS -->
                            <!-- ================================================= -->

                            <div class="card border mb-3">

                                <div
                                    class="card-header d-flex justify-content-between align-items-center"
                                >

                                    <strong>
                                        Stock In Items
                                    </strong>


                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        @click="addItem"
                                    >

                                        <i
                                            class="bi bi-plus-lg me-1"
                                        ></i>

                                        Add Item

                                    </button>

                                </div>


                                <div class="card-body p-0">

                                    <div class="table-responsive">

                                        <table
                                            class="table table-bordered mb-0 align-middle"
                                        >

                                            <thead
                                                class="table-light"
                                            >

                                                <tr>

                                                    <th
                                                        style="
                                                            min-width:270px
                                                        "
                                                    >
                                                        Item
                                                    </th>

                                                    <th
                                                        style="
                                                            width:110px
                                                        "
                                                    >
                                                        Qty
                                                    </th>

                                                    <th
                                                        style="
                                                            width:130px
                                                        "
                                                    >
                                                        Unit Price
                                                    </th>

                                                    <th
                                                        style="
                                                            width:120px
                                                        "
                                                    >
                                                        Discount
                                                    </th>

                                                    <th
                                                        style="
                                                            width:120px
                                                        "
                                                    >
                                                        Tax
                                                    </th>

                                                    <th
                                                        style="
                                                            width:140px
                                                        "
                                                        class="text-end"
                                                    >
                                                        Total
                                                    </th>

                                                    <th
                                                        width="55"
                                                    >
                                                    </th>

                                                </tr>

                                            </thead>


                                            <tbody>

                                                <tr
                                                    v-for="(
                                                        row,
                                                        index
                                                    ) in form.items"
                                                    :key="row.key"
                                                >

                                                    <!-- Item -->

                                                    <td>

                                                        <select
                                                            v-model="
                                                                row.inventory_item_id
                                                            "
                                                            class="form-select"
                                                            :class="{
                                                                'is-invalid':
                                                                    formErrors[
                                                                        `items.${index}.inventory_item_id`
                                                                    ]
                                                            }"
                                                            @change="
                                                                itemChanged(
                                                                    row
                                                                )
                                                            "
                                                        >

                                                            <option value="">
                                                                Select Item
                                                            </option>

                                                            <option
                                                                v-for="
                                                                    item in items
                                                                "
                                                                :key="
                                                                    item.id
                                                                "
                                                                :value="
                                                                    item.id
                                                                "
                                                            >

                                                                {{
                                                                    item.name
                                                                }}

                                                                {{
                                                                    item.sku
                                                                        ? ` - ${item.sku}`
                                                                        : ''
                                                                }}

                                                            </option>

                                                        </select>

                                                        <div
                                                            v-if="
                                                                formErrors[
                                                                    `items.${index}.inventory_item_id`
                                                                ]
                                                            "
                                                            class="invalid-feedback"
                                                        >
                                                            {{
                                                                formErrors[
                                                                    `items.${index}.inventory_item_id`
                                                                ]
                                                            }}
                                                        </div>

                                                    </td>


                                                    <!-- Quantity -->

                                                    <td>

                                                        <input
                                                            v-model.number="
                                                                row.quantity
                                                            "
                                                            type="number"
                                                            min="0.01"
                                                            step="0.01"
                                                            class="form-control"
                                                            @input="
                                                                calculateRow(
                                                                    row
                                                                )
                                                            "
                                                        >

                                                    </td>


                                                    <!-- Unit Price -->

                                                    <td>

                                                        <input
                                                            v-model.number="
                                                                row.unit_price
                                                            "
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            class="form-control"
                                                            @input="
                                                                calculateRow(
                                                                    row
                                                                )
                                                            "
                                                        >

                                                    </td>


                                                    <!-- Discount -->

                                                    <td>

                                                        <input
                                                            v-model.number="
                                                                row.discount
                                                            "
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            class="form-control"
                                                            @input="
                                                                calculateRow(
                                                                    row
                                                                )
                                                            "
                                                        >

                                                    </td>


                                                    <!-- Tax -->

                                                    <td>

                                                        <input
                                                            v-model.number="
                                                                row.tax
                                                            "
                                                            type="number"
                                                            min="0"
                                                            step="0.01"
                                                            class="form-control"
                                                            @input="
                                                                calculateRow(
                                                                    row
                                                                )
                                                            "
                                                        >

                                                    </td>


                                                    <!-- Total -->

                                                    <td
                                                        class="text-end"
                                                    >

                                                        <strong>
                                                            {{
                                                                money(
                                                                    row.total
                                                                )
                                                            }}
                                                        </strong>

                                                    </td>


                                                    <!-- Delete -->

                                                    <td
                                                        class="text-center"
                                                    >

                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-danger"
                                                            :disabled="
                                                                form.items.length <=
                                                                1
                                                            "
                                                            @click="
                                                                removeItem(
                                                                    index
                                                                )
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

                            </div>


                            <!-- ================================================= -->
                            <!-- TOTALS -->
                            <!-- ================================================= -->

                            <div class="row justify-content-end">

                                <div class="col-md-5">

                                    <table
                                        class="table table-bordered"
                                    >

                                        <tbody>

                                            <tr>

                                                <th>
                                                    Subtotal
                                                </th>

                                                <td
                                                    class="text-end"
                                                >
                                                    {{
                                                        money(
                                                            calculatedSubtotal
                                                        )
                                                    }}
                                                </td>

                                            </tr>


                                            <tr>

                                                <th>
                                                    Discount
                                                </th>

                                                <td>

                                                    <input
                                                        v-model.number="
                                                            form.discount
                                                        "
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        class="form-control text-end"
                                                    >

                                                </td>

                                            </tr>


                                            <tr>

                                                <th>
                                                    Tax
                                                </th>

                                                <td>

                                                    <input
                                                        v-model.number="
                                                            form.tax
                                                        "
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        class="form-control text-end"
                                                    >

                                                </td>

                                            </tr>


                                            <tr>

                                                <th>
                                                    Total Amount
                                                </th>

                                                <td
                                                    class="text-end"
                                                >

                                                    <strong
                                                        class="fs-5"
                                                    >
                                                        {{
                                                            money(
                                                                calculatedTotal
                                                            )
                                                        }}
                                                    </strong>

                                                </td>

                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>


                        <!-- Footer -->

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
                                    form.items.length === 0
                                "
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update Stock In'
                                        : 'Save Stock In'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- VIEW MODAL -->
        <!-- ========================================================= -->

        <div
            v-if="showViewModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Stock In Details
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

                        <!-- Loading -->

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


                        <!-- Details -->

                        <div
                            v-else-if="
                                selectedStockIn
                            "
                        >

                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Stock In No.
                                    </small>

                                    <div>
                                        <strong>
                                            {{
                                                selectedStockIn.stock_in_no
                                            }}
                                        </strong>
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Stock In Date
                                    </small>

                                    <div>
                                        {{
                                            formatDate(
                                                selectedStockIn.stock_in_date
                                            )
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Supplier
                                    </small>

                                    <div>
                                        {{
                                            selectedStockIn.supplier?.name ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Status
                                    </small>

                                    <div>

                                        <span
                                            class="badge"
                                            :class="
                                                statusClass(
                                                    selectedStockIn.status
                                                )
                                            "
                                        >
                                            {{
                                                selectedStockIn.status
                                            }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Invoice No.
                                    </small>

                                    <div>
                                        {{
                                            selectedStockIn.invoice_no ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-9">

                                    <small class="text-muted">
                                        Remarks
                                    </small>

                                    <div>
                                        {{
                                            selectedStockIn.remarks ||
                                            '-'
                                        }}
                                    </div>

                                </div>

                            </div>


                            <!-- Items -->

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered align-middle"
                                >

                                    <thead
                                        class="table-light"
                                    >

                                        <tr>

                                            <th>
                                                #
                                            </th>

                                            <th>
                                                Item
                                            </th>

                                            <th>
                                                SKU
                                            </th>

                                            <th class="text-end">
                                                Qty
                                            </th>

                                            <th class="text-end">
                                                Unit Price
                                            </th>

                                            <th class="text-end">
                                                Discount
                                            </th>

                                            <th class="text-end">
                                                Tax
                                            </th>

                                            <th class="text-end">
                                                Total
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr
                                            v-for="(
                                                row,
                                                index
                                            ) in (
                                                selectedStockIn.items ||
                                                []
                                            )"
                                            :key="row.id"
                                        >

                                            <td>
                                                {{ index + 1 }}
                                            </td>

                                            <td>

                                                {{
                                                    row.item?.name ||
                                                    '-'
                                                }}

                                            </td>

                                            <td>

                                                {{
                                                    row.item?.sku ||
                                                    '-'
                                                }}

                                            </td>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    number(
                                                        row.quantity
                                                    )
                                                }}
                                            </td>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        row.unit_price
                                                    )
                                                }}
                                            </td>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        row.discount
                                                    )
                                                }}
                                            </td>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        row.tax
                                                    )
                                                }}
                                            </td>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        row.total
                                                    )
                                                }}
                                            </td>

                                        </tr>


                                        <tr
                                            v-if="
                                                !selectedStockIn.items ||
                                                selectedStockIn.items.length === 0
                                            "
                                        >

                                            <td
                                                colspan="8"
                                                class="text-center text-muted"
                                            >
                                                No items found.
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <!-- Totals -->

                            <div
                                class="row justify-content-end mt-3"
                            >

                                <div class="col-md-4">

                                    <table
                                        class="table table-bordered"
                                    >

                                        <tr>

                                            <th>
                                                Subtotal
                                            </th>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        selectedStockIn.subtotal
                                                    )
                                                }}
                                            </td>

                                        </tr>


                                        <tr>

                                            <th>
                                                Discount
                                            </th>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        selectedStockIn.discount
                                                    )
                                                }}
                                            </td>

                                        </tr>


                                        <tr>

                                            <th>
                                                Tax
                                            </th>

                                            <td
                                                class="text-end"
                                            >
                                                {{
                                                    money(
                                                        selectedStockIn.tax
                                                    )
                                                }}
                                            </td>

                                        </tr>


                                        <tr>

                                            <th>
                                                Total
                                            </th>

                                            <td
                                                class="text-end"
                                            >

                                                <strong>
                                                    {{
                                                        money(
                                                            selectedStockIn.total_amount
                                                        )
                                                    }}
                                                </strong>

                                            </td>

                                        </tr>

                                    </table>

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

const stockIns = ref([]);

const suppliers = ref([]);

const items = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const showModal = ref(false);

const showViewModal = ref(false);

const viewLoading = ref(false);

const selectedStockIn = ref(null);

const editing = ref(false);

const editingId = ref(null);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


let rowKey = 1;


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    inventory_supplier_id: '',

    status: '',

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
| EMPTY ITEM
|--------------------------------------------------------------------------
*/

const emptyItem = () => ({

    key: rowKey++,

    inventory_item_id: '',

    quantity: 1,

    unit_price: 0,

    discount: 0,

    tax: 0,

    total: 0

});


/*
|--------------------------------------------------------------------------
| EMPTY FORM
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    inventory_supplier_id: '',

    stock_in_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    invoice_no: '',

    discount: 0,

    tax: 0,

    remarks: '',

    status: 'Received',

    items: [
        emptyItem()
    ]

});


const form = ref(
    emptyForm()
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
| STATUS CLASS
|--------------------------------------------------------------------------
*/

const statusClass = (status) => {

    switch (status) {

        case 'Received':

            return 'bg-success';


        case 'Cancelled':

            return 'bg-danger';


        case 'Draft':

            return 'bg-warning text-dark';


        default:

            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD FORM DATA
|--------------------------------------------------------------------------
|
| We first try the dedicated form-data endpoint.
| If that fails, we directly load the existing
| suppliers/items APIs.
|
*/

const loadFormData = async () => {

    try {

        const config =
            getAuthConfig();


        /*
        |--------------------------------------------------------------------------
        | First attempt
        |--------------------------------------------------------------------------
        */

        try {

            const response =
                await axios.get(
                    '/api/inventory/stock-ins/form-data',
                    config
                );


            const data =
                response.data?.data ||
                {};


            suppliers.value =
                data.suppliers ||
                [];


            items.value =
                data.items ||
                [];


            if (
                suppliers.value.length > 0 ||
                items.value.length > 0
            ) {

                console.log(
                    'Stock In form data loaded.',
                    {
                        suppliers:
                            suppliers.value,
                        items:
                            items.value
                    }
                );

                return;

            }

        } catch (formDataError) {

            console.warn(
                'Stock In form-data API failed. Loading directly.',
                formDataError
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Direct supplier + item APIs
        |--------------------------------------------------------------------------
        */

        const [
            supplierResponse,
            itemResponse
        ] = await Promise.all([

            axios.get(
                '/api/inventory/suppliers',
                {
                    ...config,

                    params: {
                        page: 1,
                        per_page: 1000
                    }
                }
            ),

            axios.get(
                '/api/inventory/items',
                {
                    ...config,

                    params: {
                        page: 1,
                        per_page: 1000
                    }
                }
            )

        ]);


        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */

        const supplierData =
            supplierResponse.data?.data;


        if (
            Array.isArray(
                supplierData
            )
        ) {

            suppliers.value =
                supplierData;

        } else {

            suppliers.value =
                supplierData?.data ||
                [];

        }


        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */

        const itemData =
            itemResponse.data?.data;


        if (
            Array.isArray(
                itemData
            )
        ) {

            items.value =
                itemData;

        } else {

            items.value =
                itemData?.data ||
                [];

        }


        console.log(
            'STOCK IN SUPPLIERS:',
            suppliers.value
        );

        console.log(
            'STOCK IN ITEMS:',
            items.value
        );


    } catch (err) {

        console.error(
            'Stock In form data error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load suppliers or inventory items.';

    }

};


/*
|--------------------------------------------------------------------------
| LOAD STOCK INS
|--------------------------------------------------------------------------
*/

const loadStockIns = async (
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

            inventory_supplier_id:
                filters.value
                    .inventory_supplier_id ||
                undefined,

            status:
                filters.value.status ||
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

                '/api/inventory/stock-ins',

                {

                    ...getAuthConfig(),

                    params

                }

            );


        const result =
            response.data?.data;


        stockIns.value =
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
            'Stock In loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stock entries.';

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

    editing.value = false;

    editingId.value = null;

    form.value =
        emptyForm();

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    showModal.value = true;

};


/*
|--------------------------------------------------------------------------
| OPEN EDIT
|--------------------------------------------------------------------------
*/

const openEdit = async (
    id
) => {

    try {

        error.value = '';

        formError.value = '';

        formErrors.value = {};


        const response =
            await axios.get(

                `/api/inventory/stock-ins/${id}`,

                getAuthConfig()

            );


        const stockIn =
            response.data?.data;


        if (!stockIn) {

            throw new Error(
                'Stock In data not found.'
            );

        }


        editing.value = true;

        editingId.value =
            stockIn.id;


        form.value = {

            inventory_supplier_id:
                stockIn.inventory_supplier_id ||
                '',

            stock_in_date:
                stockIn.stock_in_date
                    ? String(
                        stockIn.stock_in_date
                    ).substring(0, 10)
                    : '',

            invoice_no:
                stockIn.invoice_no ||
                '',

            discount:
                Number(
                    stockIn.discount ||
                    0
                ),

            tax:
                Number(
                    stockIn.tax ||
                    0
                ),

            remarks:
                stockIn.remarks ||
                '',

            status:
                stockIn.status ||
                'Draft',

            items:
                (
                    stockIn.items ||
                    []
                ).map(
                    row => ({

                        key:
                            rowKey++,

                        inventory_item_id:
                            row.inventory_item_id,

                        quantity:
                            Number(
                                row.quantity ||
                                0
                            ),

                        unit_price:
                            Number(
                                row.unit_price ||
                                0
                            ),

                        discount:
                            Number(
                                row.discount ||
                                0
                            ),

                        tax:
                            Number(
                                row.tax ||
                                0
                            ),

                        total:
                            Number(
                                row.total ||
                                0
                            )

                    })
                )

        };


        if (
            form.value.items.length === 0
        ) {

            form.value.items = [
                emptyItem()
            ];

        }


        showModal.value = true;


    } catch (err) {

        console.error(
            'Stock In edit error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stock entry.';

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

};


/*
|--------------------------------------------------------------------------
| ADD ITEM
|--------------------------------------------------------------------------
*/

const addItem = () => {

    form.value.items.push(
        emptyItem()
    );

};


/*
|--------------------------------------------------------------------------
| REMOVE ITEM
|--------------------------------------------------------------------------
*/

const removeItem = (
    index
) => {

    if (
        form.value.items.length <= 1
    ) {

        return;

    }


    form.value.items.splice(
        index,
        1
    );

};


/*
|--------------------------------------------------------------------------
| ITEM CHANGED
|--------------------------------------------------------------------------
*/

const itemChanged = (
    row
) => {

    const selected =
        items.value.find(

            item =>
                Number(item.id) ===
                Number(
                    row.inventory_item_id
                )

        );


    if (!selected) {

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Default cost price
    |--------------------------------------------------------------------------
    */

    if (
        !row.unit_price ||
        Number(row.unit_price) === 0
    ) {

        row.unit_price =
            Number(
                selected.cost_price ||
                0
            );

    }


    calculateRow(
        row
    );

};


/*
|--------------------------------------------------------------------------
| CALCULATE ROW
|--------------------------------------------------------------------------
*/

const calculateRow = (
    row
) => {

    const quantity =
        Number(
            row.quantity || 0
        );


    const unitPrice =
        Number(
            row.unit_price || 0
        );


    const discount =
        Number(
            row.discount || 0
        );


    const tax =
        Number(
            row.tax || 0
        );


    const subtotal =
        quantity *
        unitPrice;


    row.total =
        Math.max(
            0,
            subtotal -
            discount +
            tax
        );

};


/*
|--------------------------------------------------------------------------
| CALCULATED SUBTOTAL
|--------------------------------------------------------------------------
*/

const calculatedSubtotal =
    computed(() => {

        return form.value.items.reduce(

            (
                total,
                row
            ) => {

                const quantity =
                    Number(
                        row.quantity ||
                        0
                    );


                const unitPrice =
                    Number(
                        row.unit_price ||
                        0
                    );


                row.total =
                    Math.max(
                        0,

                        (
                            quantity *
                            unitPrice
                        ) -

                        Number(
                            row.discount ||
                            0
                        ) +

                        Number(
                            row.tax ||
                            0
                        )
                    );


                return total +
                    (
                        quantity *
                        unitPrice
                    );

            },

            0

        );

    });


/*
|--------------------------------------------------------------------------
| CALCULATED TOTAL
|--------------------------------------------------------------------------
*/

const calculatedTotal =
    computed(() => {

        const subtotal =
            calculatedSubtotal.value;


        const discount =
            Number(
                form.value.discount ||
                0
            );


        const tax =
            Number(
                form.value.tax ||
                0
            );


        return Math.max(
            0,
            subtotal -
            discount +
            tax
        );

    });


/*
|--------------------------------------------------------------------------
| SAVE STOCK IN
|--------------------------------------------------------------------------
*/

const saveStockIn = async () => {

    if (saving.value) {

        return;

    }


    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';


    /*
    |--------------------------------------------------------------------------
    | Frontend Validation
    |--------------------------------------------------------------------------
    */

    if (
        !form.value.stock_in_date
    ) {

        formError.value =
            'Stock In date is required.';

        return;

    }


    if (
        form.value.items.length === 0
    ) {

        formError.value =
            'Please add at least one item.';

        return;

    }


    for (
        const row of form.value.items
    ) {

        if (
            !row.inventory_item_id
        ) {

            formError.value =
                'Please select an inventory item for every row.';

            return;

        }


        if (
            Number(row.quantity) <= 0
        ) {

            formError.value =
                'Quantity must be greater than zero.';

            return;

        }


        if (
            Number(row.unit_price) < 0
        ) {

            formError.value =
                'Unit price cannot be negative.';

            return;

        }

    }


    saving.value = true;


    try {

        const payload = {

            inventory_supplier_id:
                form.value.inventory_supplier_id ||
                null,

            stock_in_date:
                form.value.stock_in_date,

            invoice_no:
                form.value.invoice_no ||
                null,

            discount:
                Number(
                    form.value.discount ||
                    0
                ),

            tax:
                Number(
                    form.value.tax ||
                    0
                ),

            remarks:
                form.value.remarks ||
                null,

            status:
                form.value.status,

            items:
                form.value.items.map(
                    row => ({

                        inventory_item_id:
                            Number(
                                row.inventory_item_id
                            ),

                        quantity:
                            Number(
                                row.quantity
                            ),

                        unit_price:
                            Number(
                                row.unit_price
                            ),

                        discount:
                            Number(
                                row.discount ||
                                0
                            ),

                        tax:
                            Number(
                                row.tax ||
                                0
                            )

                    })
                )

        };


        let response;


        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if (editing.value) {

            response =
                await axios.put(

                    `/api/inventory/stock-ins/${editingId.value}`,

                    payload,

                    getAuthConfig()

                );

        }


        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        else {

            response =
                await axios.post(

                    '/api/inventory/stock-ins',

                    payload,

                    getAuthConfig()

                );

        }


        success.value =
            response.data?.message ||
            'Stock In saved successfully.';


        showModal.value = false;


        await loadStockIns(
            pagination.value.current_page
        );


    } catch (err) {

        console.error(
            'Stock In save error:',
            err
        );


        console.error(
            'Response:',
            err.response?.data
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
                'Unable to save stock entry.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW STOCK IN
|--------------------------------------------------------------------------
*/

const viewStockIn = async (
    id
) => {

    showViewModal.value = true;

    viewLoading.value = true;

    selectedStockIn.value = null;


    try {

        const response =
            await axios.get(

                `/api/inventory/stock-ins/${id}`,

                getAuthConfig()

            );


        selectedStockIn.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'View Stock In error:',
            err
        );


        showViewModal.value = false;


        error.value =
            err.response?.data?.message ||
            'Unable to load stock details.';

    } finally {

        viewLoading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/

const deleteStockIn = async (
    stockIn
) => {

    if (
        !window.confirm(
            `Delete stock entry "${stockIn.stock_in_no}"?`
        )
    ) {

        return;

    }


    actionId.value =
        stockIn.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(

                `/api/inventory/stock-ins/${stockIn.id}`,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Stock In deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            stockIns.value.length === 1 &&
            page > 1
        ) {

            page--;

        }


        await loadStockIns(
            page
        );


    } catch (err) {

        console.error(
            'Delete Stock In error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to delete stock entry.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| RESET FILTERS
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        inventory_supplier_id: '',

        status: '',

        from_date: '',

        to_date: '',

        per_page: 20

    };


    loadStockIns(1);

};


/*
|--------------------------------------------------------------------------
| CHANGE PAGE
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


    loadStockIns(
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

        await loadFormData();

        await loadStockIns(1);

    }
);

</script>