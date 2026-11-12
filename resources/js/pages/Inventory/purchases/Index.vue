<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">
                    Inventory Purchases
                </h4>

                <small class="text-muted">
                    Manage inventory purchases
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Purchase
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

                    <div class="col-md-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            v-model="filters.search"
                            type="text"
                            class="form-control"
                            placeholder="Purchase No / Invoice No / Supplier"
                            @keyup.enter="loadPurchases(1)"
                        >

                    </div>


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
                            </option>

                        </select>

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


                    <div class="col-md-1 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadPurchases(1)"
                            title="Search"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            :disabled="loading"
                            @click="resetFilters"
                            title="Reset"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- Purchase List -->
        <div class="card">

            <div
                class="card-header d-flex justify-content-between align-items-center"
            >

                <strong>
                    Purchase List
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
                                    Purchase No.
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
                                        Loading purchases...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    purchases.length === 0
                                "
                            >

                                <td
                                    colspan="11"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-cart-x fs-2 d-block mb-2"
                                    ></i>

                                    No purchases found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    purchase,
                                    index
                                ) in purchases"
                                :key="purchase.id"
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


                                <td>

                                    <strong>
                                        {{ purchase.purchase_no }}
                                    </strong>

                                </td>


                                <td>
                                    {{ formatDate(purchase.purchase_date) }}
                                </td>


                                <td>
                                    {{
                                        purchase.supplier?.name ||
                                        '-'
                                    }}
                                </td>


                                <td>
                                    {{ purchase.invoice_no || '-' }}
                                </td>


                                <td class="text-end">
                                    {{ money(purchase.subtotal) }}
                                </td>


                                <td class="text-end">
                                    {{ money(purchase.discount) }}
                                </td>


                                <td class="text-end">
                                    {{ money(purchase.tax) }}
                                </td>


                                <td class="text-end">

                                    <strong>
                                        {{ money(purchase.total_amount) }}
                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                purchase.status
                                            )
                                        "
                                    >
                                        {{ purchase.status }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- View -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-info me-1"
                                        title="View"
                                        @click="
                                            viewPurchase(
                                                purchase.id
                                            )
                                        "
                                    >
                                        <i class="bi bi-eye"></i>
                                    </button>


                                    <!-- Edit -->
                                    <button
                                        v-if="
                                            purchase.status !==
                                            'Received'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(
                                                purchase.id
                                            )
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Delete -->
                                    <button
                                        v-if="
                                            purchase.status !==
                                            'Received'
                                        "
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId ===
                                            purchase.id
                                        "
                                        @click="
                                            deletePurchase(
                                                purchase
                                            )
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId ===
                                                purchase.id
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


        <!-- Add / Edit Modal -->
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
                        @submit.prevent="savePurchase"
                    >

                        <!-- Header -->
                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Inventory Purchase'
                                        : 'Add Inventory Purchase'
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

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <!-- Purchase Information -->
                            <div class="row g-3 mb-3">

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Purchase Date
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="
                                            form.purchase_date
                                        "
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.purchase_date
                                        }"
                                    >

                                    <div
                                        v-if="
                                            formErrors.purchase_date
                                        "
                                        class="invalid-feedback"
                                    >
                                        {{
                                            formErrors.purchase_date
                                        }}
                                    </div>

                                </div>


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


                                <div class="col-md-8">

                                    <label class="form-label">
                                        Notes
                                    </label>

                                    <input
                                        v-model="form.notes"
                                        type="text"
                                        class="form-control"
                                        placeholder="Optional notes"
                                    >

                                </div>

                            </div>


                            <!-- Items -->
                            <div
                                class="card border mb-3"
                            >

                                <div
                                    class="card-header d-flex justify-content-between align-items-center"
                                >

                                    <strong>
                                        Purchase Items
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

                                    <div
                                        class="table-responsive"
                                    >

                                        <table
                                            class="table table-bordered mb-0 align-middle"
                                        >

                                            <thead
                                                class="table-light"
                                            >

                                                <tr>

                                                    <th
                                                        style="min-width:260px"
                                                    >
                                                        Item
                                                    </th>

                                                    <th
                                                        style="width:110px"
                                                    >
                                                        Qty
                                                    </th>

                                                    <th
                                                        style="width:130px"
                                                    >
                                                        Unit Price
                                                    </th>

                                                    <th
                                                        style="width:120px"
                                                    >
                                                        Discount
                                                    </th>

                                                    <th
                                                        style="width:120px"
                                                    >
                                                        Tax
                                                    </th>

                                                    <th
                                                        style="width:140px"
                                                        class="text-end"
                                                    >
                                                        Total
                                                    </th>

                                                    <th
                                                        width="50"
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


                                                    <!-- Remove -->
                                                    <td
                                                        class="text-center"
                                                    >

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


                                                <tr
                                                    v-if="
                                                        form.items.length === 0
                                                    "
                                                >

                                                    <td
                                                        colspan="7"
                                                        class="text-center text-muted py-4"
                                                    >
                                                        No purchase items.
                                                    </td>

                                                </tr>

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>


                            <!-- Totals -->
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
                                                        @input="
                                                            calculateGrandTotal
                                                        "
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
                                                        @input="
                                                            calculateGrandTotal
                                                        "
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
                                        ? 'Update Purchase'
                                        : 'Save Purchase'
                                }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <!-- View Modal -->
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
                            Purchase Details
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

                        </div>


                        <div
                            v-else-if="selectedPurchase"
                        >

                            <div class="row mb-3">

                                <div class="col-md-3">

                                    <strong>
                                        Purchase No.
                                    </strong>

                                    <div>
                                        {{
                                            selectedPurchase.purchase_no
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <strong>
                                        Purchase Date
                                    </strong>

                                    <div>
                                        {{
                                            formatDate(
                                                selectedPurchase.purchase_date
                                            )
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <strong>
                                        Supplier
                                    </strong>

                                    <div>
                                        {{
                                            selectedPurchase.supplier?.name ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <strong>
                                        Status
                                    </strong>

                                    <div>

                                        <span
                                            class="badge"
                                            :class="
                                                statusClass(
                                                    selectedPurchase.status
                                                )
                                            "
                                        >
                                            {{
                                                selectedPurchase.status
                                            }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="row mb-3">

                                <div class="col-md-3">

                                    <strong>
                                        Invoice No.
                                    </strong>

                                    <div>
                                        {{
                                            selectedPurchase.invoice_no ||
                                            '-'
                                        }}
                                    </div>

                                </div>


                                <div class="col-md-9">

                                    <strong>
                                        Notes
                                    </strong>

                                    <div>
                                        {{
                                            selectedPurchase.notes ||
                                            '-'
                                        }}
                                    </div>

                                </div>

                            </div>


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
                                                selectedPurchase.items ||
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

                                            <td class="text-end">
                                                {{
                                                    number(
                                                        row.quantity
                                                    )
                                                }}
                                            </td>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        row.unit_price
                                                    )
                                                }}
                                            </td>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        row.discount
                                                    )
                                                }}
                                            </td>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        row.tax
                                                    )
                                                }}
                                            </td>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        row.total
                                                    )
                                                }}
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div
                                class="row justify-content-end"
                            >

                                <div class="col-md-4">

                                    <table
                                        class="table table-bordered"
                                    >

                                        <tr>

                                            <th>
                                                Subtotal
                                            </th>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        selectedPurchase.subtotal
                                                    )
                                                }}
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Discount
                                            </th>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        selectedPurchase.discount
                                                    )
                                                }}
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Tax
                                            </th>

                                            <td class="text-end">
                                                {{
                                                    money(
                                                        selectedPurchase.tax
                                                    )
                                                }}
                                            </td>

                                        </tr>

                                        <tr>

                                            <th>
                                                Total
                                            </th>

                                            <td class="text-end">

                                                <strong>
                                                    {{
                                                        money(
                                                            selectedPurchase.total_amount
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
| State
|--------------------------------------------------------------------------
*/

const purchases =
    ref([]);

const suppliers =
    ref([]);

const items =
    ref([]);

const loading =
    ref(false);

const saving =
    ref(false);

const actionId =
    ref(null);

const showModal =
    ref(false);

const showViewModal =
    ref(false);

const viewLoading =
    ref(false);

const selectedPurchase =
    ref(null);

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

        inventory_supplier_id: '',

        status: '',

        from_date: '',

        to_date: '',

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
| Row Key
|--------------------------------------------------------------------------
*/

let rowKey = 1;


/*
|--------------------------------------------------------------------------
| Empty Item
|--------------------------------------------------------------------------
*/

const emptyItem =
    () => ({

        key:
            rowKey++,

        inventory_item_id:
            '',

        quantity:
            1,

        unit_price:
            0,

        discount:
            0,

        tax:
            0,

        total:
            0

    });


/*
|--------------------------------------------------------------------------
| Empty Form
|--------------------------------------------------------------------------
*/

const emptyForm =
    () => ({

        inventory_supplier_id:
            '',

        purchase_date:
            new Date()
                .toISOString()
                .substring(0, 10),

        invoice_no:
            '',

        discount:
            0,

        tax:
            0,

        notes:
            '',

        status:
            'Received',

        items: [
            emptyItem()
        ]

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
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) {
        config.headers['X-School-Id'] = activeSchoolId;
    }


        return config;

    };


/*
|--------------------------------------------------------------------------
| Number
|--------------------------------------------------------------------------
*/

const number =
    (value) => {

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

const money =
    (value) => {

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

const formatDate =
    (value) => {

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
| Status Class
|--------------------------------------------------------------------------
*/

const statusClass =
    (status) => {

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
| Load Form Data
|--------------------------------------------------------------------------
*/

const loadFormData = async () => {

    try {

        const config = getAuthConfig();

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

        let supplierData =
            supplierResponse.data?.data;


        if (Array.isArray(supplierData)) {

            suppliers.value =
                supplierData;

        } else {

            suppliers.value =
                supplierData?.data || [];

        }


        /*
        |--------------------------------------------------------------------------
        | Inventory Items
        |--------------------------------------------------------------------------
        */

        let itemData =
            itemResponse.data?.data;


        if (Array.isArray(itemData)) {

            items.value =
                itemData;

        } else {

            items.value =
                itemData?.data || [];

        }


        console.log(
            'PURCHASE SUPPLIERS:',
            suppliers.value
        );

        console.log(
            'PURCHASE ITEMS:',
            items.value
        );


    } catch (err) {

        console.error(
            'Unable to load purchase form data:',
            err
        );

        console.error(
            'Response:',
            err.response?.data
        );

        error.value =
            err.response?.data?.message ||
            'Unable to load suppliers or inventory items.';

    }

};

/*
|--------------------------------------------------------------------------
| Load Purchases
|--------------------------------------------------------------------------
*/

const loadPurchases =
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

                    '/api/inventory/purchases',

                    {

                        ...getAuthConfig(),

                        params

                    }

                );


            const result =
                response.data?.data;


            purchases.value =
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
                'Purchase loading error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load purchases.';

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

        error.value = '';

        showModal.value = true;

    };


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEdit =
    async (
        id
    ) => {

        try {

            error.value = '';

            formError.value = '';

            formErrors.value = {};


            const response =
                await axios.get(

                    `/api/inventory/purchases/${id}`,

                    getAuthConfig()

                );


            const purchase =
                response.data?.data;


            if (!purchase) {

                throw new Error(
                    'Purchase data not found.'
                );

            }


            editing.value = true;

            editingId.value =
                purchase.id;


            form.value = {

                inventory_supplier_id:
                    purchase.inventory_supplier_id ||
                    '',

                purchase_date:
                    purchase.purchase_date
                        ? String(
                            purchase.purchase_date
                        ).substring(0, 10)
                        : '',

                invoice_no:
                    purchase.invoice_no ||
                    '',

                discount:
                    Number(
                        purchase.discount ||
                        0
                    ),

                tax:
                    Number(
                        purchase.tax ||
                        0
                    ),

                notes:
                    purchase.notes ||
                    '',

                status:
                    purchase.status ||
                    'Draft',

                items:
                    (
                        purchase.items ||
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
                'Purchase edit error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to load purchase.';

        }

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
| Add Item
|--------------------------------------------------------------------------
*/

const addItem =
    () => {

        form.value.items.push(
            emptyItem()
        );

    };


/*
|--------------------------------------------------------------------------
| Remove Item
|--------------------------------------------------------------------------
*/

const removeItem =
    (index) => {

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
| Item Changed
|--------------------------------------------------------------------------
*/

const itemChanged =
    (row) => {

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
        | Use current cost price as
        | default purchase price.
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
| Calculate Row
|--------------------------------------------------------------------------
*/

const calculateRow =
    (row) => {

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
| Calculated Subtotal
|--------------------------------------------------------------------------
*/

const calculatedSubtotal =
    computed(() => {

        return form.value.items.reduce(

            (
                total,
                row
            ) => {

                calculateRow(
                    row
                );

                return total +
                    (
                        Number(
                            row.quantity || 0
                        ) *
                        Number(
                            row.unit_price || 0
                        )
                    );

            },

            0

        );

    });


/*
|--------------------------------------------------------------------------
| Calculated Total
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
| Grand Total Trigger
|--------------------------------------------------------------------------
*/

const calculateGrandTotal =
    () => {

        /*
        | Computed properties automatically
        | recalculate when form values change.
        */

        return calculatedTotal.value;

    };


/*
|--------------------------------------------------------------------------
| Save Purchase
|--------------------------------------------------------------------------
*/

const savePurchase =
    async () => {

        if (saving.value) {

            return;

        }


        formError.value = '';

        formErrors.value = {};

        error.value = '';

        success.value = '';


        /*
        |--------------------------------------------------------------------------
        | Basic Frontend Validation
        |--------------------------------------------------------------------------
        */

        if (
            !form.value.purchase_date
        ) {

            formError.value =
                'Purchase date is required.';

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

                purchase_date:
                    form.value.purchase_date,

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

                notes:
                    form.value.notes ||
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


            if (editing.value) {

                response =
                    await axios.put(

                        `/api/inventory/purchases/${editingId.value}`,

                        payload,

                        getAuthConfig()

                    );

            } else {

                response =
                    await axios.post(

                        '/api/inventory/purchases',

                        payload,

                        getAuthConfig()

                    );

            }


            success.value =
                response.data?.message ||
                'Purchase saved successfully.';


            showModal.value = false;


            await loadPurchases(
                pagination.value.current_page
            );


        } catch (err) {

            console.error(
                'Purchase save error:',
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
                    'Unable to save purchase.';

            }

        } finally {

            saving.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| View Purchase
|--------------------------------------------------------------------------
*/

const viewPurchase =
    async (
        id
    ) => {

        showViewModal.value = true;

        viewLoading.value = true;

        selectedPurchase.value = null;


        try {

            const response =
                await axios.get(

                    `/api/inventory/purchases/${id}`,

                    getAuthConfig()

                );


            selectedPurchase.value =
                response.data?.data ||
                null;

        } catch (err) {

            console.error(
                'View purchase error:',
                err
            );


            showViewModal.value = false;


            error.value =
                err.response?.data?.message ||
                'Unable to load purchase details.';

        } finally {

            viewLoading.value = false;

        }

    };


/*
|--------------------------------------------------------------------------
| Delete Purchase
|--------------------------------------------------------------------------
*/

const deletePurchase =
    async (
        purchase
    ) => {

        if (
            !window.confirm(

                `Delete purchase "${purchase.purchase_no}"?`

            )
        ) {

            return;

        }


        actionId.value =
            purchase.id;

        error.value = '';

        success.value = '';


        try {

            const response =
                await axios.delete(

                    `/api/inventory/purchases/${purchase.id}`,

                    getAuthConfig()

                );


            success.value =
                response.data?.message ||
                'Purchase deleted successfully.';


            let page =
                pagination.value.current_page;


            if (
                purchases.value.length === 1 &&
                page > 1
            ) {

                page--;

            }


            await loadPurchases(
                page
            );

        } catch (err) {

            console.error(
                'Delete purchase error:',
                err
            );


            error.value =
                err.response?.data?.message ||
                'Unable to delete purchase.';

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

            inventory_supplier_id: '',

            status: '',

            from_date: '',

            to_date: '',

            per_page: 20

        };


        loadPurchases(1);

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


        loadPurchases(
            page
        );

    };


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await loadFormData();

        await loadPurchases(1);

    }
);



const onTenantChanged = () => { loadPurchases(1); };
window.addEventListener('tenant-switched', onTenantChanged);
if (typeof onBeforeUnmount === 'function') {
    onBeforeUnmount(() => window.removeEventListener('tenant-switched', onTenantChanged));
}
</script>