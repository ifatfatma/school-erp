<template>
    <div class="container-fluid">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Stock Out
                </h4>

                <small class="text-muted">
                    Issue inventory items and manage stock-out records
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                New Stock Out
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

                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Stock Out No / Reference / Reason"
                            @keyup.enter="loadStockOuts(1)"
                        >

                    </div>


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

                            <option value="Completed">
                                Completed
                            </option>

                            <option value="Cancelled">
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div
                        class="col-md-2 d-flex align-items-end gap-2"
                    >

                        <button
                            type="button"
                            class="btn btn-primary flex-grow-1"
                            :disabled="loading"
                            @click="loadStockOuts(1)"
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
             TABLE
        ========================================================== -->

        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Stock Out Records
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
                                    Stock Out No.
                                </th>

                                <th>
                                    Reference
                                </th>

                                <th>
                                    Items
                                </th>

                                <th class="text-end">
                                    Subtotal
                                </th>

                                <th class="text-end">
                                    Total
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Created By
                                </th>

                                <th
                                    width="80"
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
                                    colspan="10"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading stock-out records...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->

                            <tr
                                v-else-if="
                                    stockOuts.length === 0
                                "
                            >

                                <td
                                    colspan="10"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-box-arrow-up fs-2 d-block mb-2"
                                    ></i>

                                    No stock-out records found.

                                </td>

                            </tr>


                            <!-- Records -->

                            <tr
                                v-for="(
                                    stockOut,
                                    index
                                ) in stockOuts"
                                :key="stockOut.id"
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
                                            stockOut.stock_out_date
                                        )
                                    }}

                                </td>


                                <td>

                                    <strong>
                                        {{
                                            stockOut.stock_out_no
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    {{
                                        stockOut.reference_no ||
                                        '-'
                                    }}

                                </td>


                                <td>

                                    <span
                                        class="badge bg-info"
                                    >
                                        {{
                                            stockOut.items?.length ||
                                            0
                                        }}
                                    </span>

                                    item(s)

                                </td>


                                <td
                                    class="text-end"
                                >

                                    {{
                                        money(
                                            stockOut.subtotal
                                        )
                                    }}

                                </td>


                                <td
                                    class="text-end"
                                >

                                    <strong>
                                        {{
                                            money(
                                                stockOut.total_amount
                                            )
                                        }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                stockOut.status
                                            )
                                        "
                                    >

                                        {{
                                            stockOut.status ||
                                            '-'
                                        }}

                                    </span>

                                </td>


                                <td>

                                    {{
                                        stockOut.creator?.name ||
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
                                            viewStockOut(
                                                stockOut.id
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


                        <span class="mx-2">

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


        <!-- =========================================================
             CREATE MODAL
        ========================================================== -->

        <div
            v-if="showCreateModal"
            class="modal fade show d-block"
            style="background: rgba(0,0,0,.5)"
        >

            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <form
                        @submit.prevent="saveStockOut"
                    >

                        <!-- Header -->

                        <div class="modal-header">

                            <div>

                                <h5 class="modal-title mb-1">
                                    New Stock Out
                                </h5>

                                <small class="text-muted">
                                    Issue one or more inventory items
                                </small>

                            </div>


                            <button
                                type="button"
                                class="btn-close"
                                :disabled="saving"
                                @click="closeCreateModal"
                            ></button>

                        </div>


                        <!-- Body -->

                        <div class="modal-body">

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >

                                {{ formError }}

                            </div>


                            <!-- =====================================
                                 HEADER FIELDS
                            ====================================== -->

                            <div class="row g-3 mb-4">

                                <!-- Date -->

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Stock Out Date
                                        <span class="text-danger">
                                            *
                                        </span>
                                    </label>

                                    <input
                                        v-model="
                                            form.stock_out_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.stock_out_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.stock_out_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.stock_out_date
                                        }}
                                    </div>

                                </div>


                                <!-- Supplier -->

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Supplier
                                    </label>

                                    <select
                                        v-model="
                                            form.inventory_supplier_id
                                        "
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select Supplier
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


                                <!-- Reference -->

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Reference No.
                                    </label>

                                    <input
                                        v-model="
                                            form.reference_no
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Reference number"
                                    >

                                </div>


                                <!-- Reason -->

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Reason
                                    </label>

                                    <input
                                        v-model="
                                            form.reason
                                        "
                                        type="text"
                                        class="form-control"
                                        placeholder="Issued / damaged / consumption"
                                    >

                                </div>

                            </div>


                            <!-- =====================================
                                 ITEMS
                            ====================================== -->

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <h6 class="mb-0">
                                    Items
                                </h6>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    @click="addItem"
                                >

                                    <i
                                        class="bi bi-plus-lg me-1"
                                    ></i>

                                    Add Item

                                </button>

                            </div>


                            <div class="table-responsive">

                                <table
                                    class="table table-bordered align-middle"
                                >

                                    <thead class="table-light">

                                        <tr>

                                            <th width="30">
                                                #
                                            </th>

                                            <th style="min-width: 280px;">
                                                Item
                                            </th>

                                            <th width="130">
                                                Available
                                            </th>

                                            <th width="130">
                                                Quantity
                                            </th>

                                            <th width="140">
                                                Unit Price
                                            </th>

                                            <th width="130">
                                                Discount
                                            </th>

                                            <th width="120">
                                                Tax
                                            </th>

                                            <th width="150">
                                                Total
                                            </th>

                                            <th width="50">
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

                                            <td>
                                                {{ index + 1 }}
                                            </td>


                                            <!-- Item -->

                                            <td>

                                                <select
                                                    v-model="
                                                        row.inventory_item_id
                                                    "
                                                    class="form-select"
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
                                                        v-for="item in availableItemsForRow(row)"
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


                                                <small
                                                    v-if="
                                                        row.itemError
                                                    "
                                                    class="text-danger"
                                                >

                                                    {{ row.itemError }}

                                                </small>

                                            </td>


                                            <!-- Available -->

                                            <td>

                                                <span
                                                    class="fw-bold"
                                                    :class="
                                                        row.current_stock <= 0
                                                            ? 'text-danger'
                                                            : 'text-success'
                                                    "
                                                >

                                                    {{
                                                        row.inventory_item_id
                                                            ? number(
                                                                row.current_stock
                                                            )
                                                            : '-'
                                                    }}

                                                </span>

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
                                                        validateRow(
                                                            row
                                                        )
                                                    "
                                                >

                                                <small
                                                    v-if="
                                                        row.quantityError
                                                    "
                                                    class="text-danger"
                                                >

                                                    {{
                                                        row.quantityError
                                                    }}

                                                </small>

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

                                            <td class="text-end">

                                                <strong>
                                                    {{
                                                        money(
                                                            row.total
                                                        )
                                                    }}
                                                </strong>

                                            </td>


                                            <!-- Remove -->

                                            <td class="text-center">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    :disabled="
                                                        form.items.length === 1
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


                            <!-- =====================================
                                 TOTALS
                            ====================================== -->

                            <div class="row justify-content-end mt-3">

                                <div class="col-md-5">

                                    <div class="card bg-light">

                                        <div class="card-body">

                                            <div
                                                class="d-flex justify-content-between mb-2"
                                            >

                                                <span>
                                                    Items Subtotal
                                                </span>

                                                <strong>
                                                    {{
                                                        money(
                                                            calculatedSubtotal
                                                        )
                                                    }}
                                                </strong>

                                            </div>


                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2"
                                            >

                                                <span>
                                                    Discount
                                                </span>

                                                <input
                                                    v-model.number="
                                                        form.discount
                                                    "
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="form-control form-control-sm"
                                                    style="width: 130px"
                                                >

                                            </div>


                                            <div
                                                class="d-flex justify-content-between align-items-center mb-2"
                                            >

                                                <span>
                                                    Tax
                                                </span>

                                                <input
                                                    v-model.number="
                                                        form.tax
                                                    "
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    class="form-control form-control-sm"
                                                    style="width: 130px"
                                                >

                                            </div>


                                            <hr>


                                            <div
                                                class="d-flex justify-content-between"
                                            >

                                                <strong>
                                                    Grand Total
                                                </strong>

                                                <strong class="fs-5">
                                                    {{
                                                        money(
                                                            grandTotal
                                                        )
                                                    }}
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================
                                 REMARKS
                            ====================================== -->

                            <div class="row mt-3">

                                <div class="col-md-12">

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

                            </div>

                        </div>


                        <!-- Footer -->

                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                :disabled="saving"
                                @click="
                                    closeCreateModal
                                "
                            >
                                Cancel
                            </button>


                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="
                                    saving ||
                                    hasQuantityErrors
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

                                Save Stock Out

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
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >

                <div class="modal-content">

                    <div class="modal-header">

                        <div>

                            <h5 class="modal-title">
                                Stock Out Details
                            </h5>

                            <small
                                v-if="selectedStockOut"
                                class="text-muted"
                            >

                                {{
                                    selectedStockOut.stock_out_no
                                }}

                            </small>

                        </div>


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
                                Loading details...
                            </div>

                        </div>


                        <div
                            v-else-if="
                                selectedStockOut
                            "
                        >

                            <!-- Header information -->

                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Stock Out No.
                                    </label>

                                    <div class="fw-bold">

                                        {{
                                            selectedStockOut.stock_out_no
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Date
                                    </label>

                                    <div>

                                        {{
                                            formatDate(
                                                selectedStockOut.stock_out_date
                                            )
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Reference
                                    </label>

                                    <div>

                                        {{
                                            selectedStockOut.reference_no ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <label class="text-muted small">
                                        Status
                                    </label>

                                    <div>

                                        <span
                                            class="badge"
                                            :class="
                                                statusClass(
                                                    selectedStockOut.status
                                                )
                                            "
                                        >

                                            {{
                                                selectedStockOut.status
                                            }}

                                        </span>

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Reason
                                    </label>

                                    <div>

                                        {{
                                            selectedStockOut.reason ||
                                            '-'
                                        }}

                                    </div>

                                </div>


                                <div class="col-md-6">

                                    <label class="text-muted small">
                                        Created By
                                    </label>

                                    <div>

                                        {{
                                            selectedStockOut.creator?.name ||
                                            '-'
                                        }}

                                    </div>

                                </div>

                            </div>


                            <!-- Items -->

                            <div class="table-responsive">

                                <table
                                    class="table table-bordered"
                                >

                                    <thead class="table-light">

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
                                                Quantity
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
                                            ) in selectedStockOut.items || []"
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

                                                <strong>
                                                    {{
                                                        money(
                                                            row.total
                                                        )
                                                    }}
                                                </strong>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <!-- Totals -->

                            <div class="row justify-content-end">

                                <div class="col-md-5">

                                    <div class="d-flex justify-content-between mb-2">

                                        <span>
                                            Subtotal
                                        </span>

                                        <strong>
                                            {{
                                                money(
                                                    selectedStockOut.subtotal
                                                )
                                            }}
                                        </strong>

                                    </div>


                                    <div class="d-flex justify-content-between mb-2">

                                        <span>
                                            Discount
                                        </span>

                                        <strong>
                                            {{
                                                money(
                                                    selectedStockOut.discount
                                                )
                                            }}
                                        </strong>

                                    </div>


                                    <div class="d-flex justify-content-between mb-2">

                                        <span>
                                            Tax
                                        </span>

                                        <strong>
                                            {{
                                                money(
                                                    selectedStockOut.tax
                                                )
                                            }}
                                        </strong>

                                    </div>


                                    <hr>


                                    <div class="d-flex justify-content-between">

                                        <strong>
                                            Total
                                        </strong>

                                        <strong class="fs-5">
                                            {{
                                                money(
                                                    selectedStockOut.total_amount
                                                )
                                            }}
                                        </strong>

                                    </div>

                                </div>

                            </div>


                            <div class="mt-3">

                                <label class="text-muted small">
                                    Remarks
                                </label>

                                <div>

                                    {{
                                        selectedStockOut.remarks ||
                                        '-'
                                    }}

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

const stockOuts = ref([]);

const items = ref([]);

const suppliers = ref([]);

const loading = ref(false);

const saving = ref(false);

const viewLoading = ref(false);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});

const showCreateModal = ref(false);

const showViewModal = ref(false);

const selectedStockOut = ref(null);


/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    from_date: '',

    to_date: '',

    status: '',

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

let rowKey = 1;


const emptyRow = () => ({

    key: rowKey++,

    inventory_item_id: '',

    quantity: 1,

    unit_price: 0,

    discount: 0,

    tax: 0,

    total: 0,

    current_stock: 0,

    quantityError: '',

    itemError: ''

});


const getEmptyForm = () => ({

    stock_out_date:
        new Date()
            .toISOString()
            .substring(0, 10),

    inventory_supplier_id: '',

    reference_no: '',

    reason: '',

    discount: 0,

    tax: 0,

    remarks: '',

    items: [
        emptyRow()
    ]

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
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) {
        config.headers['X-School-Id'] = activeSchoolId;
    }


    return config;

};


/*
|--------------------------------------------------------------------------
| NUMBER
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
| MONEY
|--------------------------------------------------------------------------
*/

const money = (
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
| DATE
|--------------------------------------------------------------------------
*/

const formatDate = (
    value
) => {

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
| STATUS
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    switch (status) {

        case 'Completed':

            return 'bg-success';

        case 'Cancelled':

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

                '/api/inventory/stock-outs/items',

                getAuthConfig()

            );


        const data =
            response.data?.data;


        items.value =
            Array.isArray(data)
                ? data
                : data?.data || [];


    } catch (err) {

        console.warn(
            'Stock-out item endpoint failed. Trying inventory items.',
            err
        );


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


            items.value =
                Array.isArray(data)
                    ? data
                    : data?.data || [];


        } catch (fallbackError) {

            console.error(
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
| LOAD SUPPLIERS
|--------------------------------------------------------------------------
*/

const loadSuppliers = async () => {

    try {

        const response =
            await axios.get(

                '/api/inventory/stock-outs/suppliers',

                getAuthConfig()

            );


        const data =
            response.data?.data;


        suppliers.value =
            Array.isArray(data)
                ? data
                : data?.data || [];


    } catch (err) {

        console.warn(
            'Stock-out supplier endpoint failed. Trying supplier API.',
            err
        );


        try {

            const response =
                await axios.get(

                    '/api/inventory/suppliers',

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


            suppliers.value =
                Array.isArray(data)
                    ? data
                    : data?.data || [];


        } catch (fallbackError) {

            console.error(
                fallbackError
            );

        }

    }

};


/*
|--------------------------------------------------------------------------
| LOAD STOCK OUTS
|--------------------------------------------------------------------------
*/

const loadStockOuts = async (
    page = 1
) => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(

                '/api/inventory/stock-outs',

                {

                    ...getAuthConfig(),

                    params: {

                        page,

                        per_page:
                            filters.value.per_page,

                        search:
                            filters.value.search ||
                            undefined,

                        from_date:
                            filters.value.from_date ||
                            undefined,

                        to_date:
                            filters.value.to_date ||
                            undefined,

                        status:
                            filters.value.status ||
                            undefined

                    }

                }

            );


        const result =
            response.data?.data;


        stockOuts.value =
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
            'Stock-out loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load stock-out records.';

    } finally {

        loading.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| OPEN CREATE
|--------------------------------------------------------------------------
*/

const openCreate = async () => {

    form.value =
        getEmptyForm();

    formErrors.value = {};

    formError.value = '';

    success.value = '';

    error.value = '';

    showCreateModal.value = true;


    if (
        items.value.length === 0
    ) {

        await loadItems();

    }


    if (
        suppliers.value.length === 0
    ) {

        await loadSuppliers();

    }

};


/*
|--------------------------------------------------------------------------
| CLOSE CREATE
|--------------------------------------------------------------------------
*/

const closeCreateModal = () => {

    if (saving.value) {

        return;

    }


    showCreateModal.value =
        false;

};


/*
|--------------------------------------------------------------------------
| AVAILABLE ITEMS FOR ROW
|--------------------------------------------------------------------------
*/

const availableItemsForRow = (
    row
) => {

    return items.value.filter(
        item => {

            return !form.value.items.some(
                otherRow =>

                    otherRow !== row &&

                    Number(
                        otherRow.inventory_item_id
                    ) ===
                    Number(item.id)

            );

        }
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

    const item =
        items.value.find(

            item =>
                Number(item.id) ===
                Number(
                    row.inventory_item_id
                )

        );


    if (!item) {

        row.current_stock = 0;

        row.unit_price = 0;

        row.itemError = '';

        row.quantityError = '';

        calculateRow(row);

        return;

    }


    row.current_stock =
        Number(
            item.current_stock ||
            0
        );


    row.unit_price =
        Number(
            item.cost_price ||
            0
        );


    row.itemError = '';

    validateRow(row);

    calculateRow(row);

};


/*
|--------------------------------------------------------------------------
| VALIDATE ROW
|--------------------------------------------------------------------------
*/

const validateRow = (
    row
) => {

    row.quantityError = '';

    row.itemError = '';


    if (
        !row.inventory_item_id
    ) {

        return;

    }


    const quantity =
        Number(
            row.quantity ||
            0
        );


    if (
        quantity <= 0
    ) {

        row.quantityError =
            'Quantity must be greater than zero.';

        return;

    }


    if (
        quantity >
        Number(
            row.current_stock ||
            0
        )
    ) {

        row.quantityError =
            `Only ${number(row.current_stock)} available.`;

    }

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
            row.quantity ||
            0
        );

    const unitPrice =
        Number(
            row.unit_price ||
            0
        );

    const discount =
        Number(
            row.discount ||
            0
        );

    const tax =
        Number(
            row.tax ||
            0
        );


    const gross =
        quantity *
        unitPrice;


    let total =
        gross -
        discount +
        tax;


    if (
        total < 0
    ) {

        total = 0;

    }


    row.total =
        total;

};


/*
|--------------------------------------------------------------------------
| ADD ITEM
|--------------------------------------------------------------------------
*/

const addItem = () => {

    form.value.items.push(
        emptyRow()
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
| SUBTOTAL
|--------------------------------------------------------------------------
*/

const calculatedSubtotal =
    computed(() => {

        return form.value.items.reduce(
            (
                total,
                row
            ) => {

                calculateRow(row);

                return (
                    total +
                    Number(
                        row.total ||
                        0
                    )
                );

            },
            0
        );

    });


/*
|--------------------------------------------------------------------------
| GRAND TOTAL
|--------------------------------------------------------------------------
*/

const grandTotal =
    computed(() => {

        const subtotal =
            Number(
                calculatedSubtotal.value ||
                0
            );

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


        let total =
            subtotal -
            discount +
            tax;


        if (
            total < 0
        ) {

            total = 0;

        }


        return total;

    });


/*
|--------------------------------------------------------------------------
| QUANTITY ERRORS
|--------------------------------------------------------------------------
*/

const hasQuantityErrors =
    computed(() => {

        return form.value.items.some(
            row => {

                validateRow(row);

                return (
                    !!row.quantityError ||
                    !!row.itemError
                );

            }
        );

    });


/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/

const saveStockOut = async () => {

    formError.value = '';

    formErrors.value = {};

    error.value = '';

    success.value = '';


    /*
    |--------------------------------------------------------------------------
    | Validate item
    |--------------------------------------------------------------------------
    */

    if (
        form.value.items.length === 0
    ) {

        formError.value =
            'Please add at least one item.';

        return;

    }


    let invalid = false;


    form.value.items.forEach(
        row => {

            validateRow(row);

            if (
                row.quantityError ||
                row.itemError ||
                !row.inventory_item_id
            ) {

                invalid = true;

            }

        }
    );


    if (invalid) {

        formError.value =
            'Please correct the item errors before saving.';

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Prepare payload
    |--------------------------------------------------------------------------
    */

    const payload = {

        stock_out_date:
            form.value.stock_out_date,

        inventory_supplier_id:
            form.value.inventory_supplier_id
                ? Number(
                    form.value.inventory_supplier_id
                )
                : null,

        reference_no:
            form.value.reference_no ||
            null,

        reason:
            form.value.reason ||
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
                        )

                })
            )

    };


    saving.value = true;


    try {

        const response =
            await axios.post(

                '/api/inventory/stock-outs',

                payload,

                getAuthConfig()

            );


        success.value =
            response.data?.message ||
            'Stock out saved successfully.';


        showCreateModal.value =
            false;


        /*
        |--------------------------------------------------------------------------
        | Refresh item stock and list
        |--------------------------------------------------------------------------
        */

        await loadItems();

        await loadStockOuts(
            1
        );


    } catch (err) {

        console.error(
            'Stock-out save error:',
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
                'Unable to save stock out.';

        }

    } finally {

        saving.value =
            false;

    }

};


/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

const viewStockOut = async (
    id
) => {

    showViewModal.value =
        true;

    viewLoading.value =
        true;

    selectedStockOut.value =
        null;


    try {

        const response =
            await axios.get(

                `/api/inventory/stock-outs/${id}`,

                getAuthConfig()

            );


        selectedStockOut.value =
            response.data?.data ||
            null;


    } catch (err) {

        console.error(
            'Stock-out view error:',
            err
        );


        showViewModal.value =
            false;


        error.value =
            err.response?.data?.message ||
            'Unable to load stock-out details.';

    } finally {

        viewLoading.value =
            false;

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

        from_date: '',

        to_date: '',

        status: '',

        per_page: 20

    };


    loadStockOuts(
        1
    );

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


    loadStockOuts(
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

        await Promise.all([

            loadItems(),

            loadSuppliers(),

            loadStockOuts(1)

        ]);

    }
);



const onTenantChanged = () => { loadStockOuts(1); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>