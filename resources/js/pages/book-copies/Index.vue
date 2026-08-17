<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4 class="mb-1">Book Copies</h4>

                <small class="text-muted">
                    Manage physical library book copies
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Book Copy
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


        <!-- Statistics -->
        <div class="row g-3 mb-3">

            <div class="col-md-3">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Total Copies
                        </small>

                        <h3 class="mb-0">
                            {{ statistics.total }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-3">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Available
                        </small>

                        <h3 class="mb-0 text-success">
                            {{ statistics.available }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-3">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Issued
                        </small>

                        <h3 class="mb-0 text-primary">
                            {{ statistics.issued }}
                        </h3>

                    </div>
                </div>

            </div>


            <div class="col-md-3">

                <div class="card">
                    <div class="card-body">

                        <small class="text-muted">
                            Lost / Damaged
                        </small>

                        <h3 class="mb-0 text-danger">
                            {{
                                statistics.lost +
                                statistics.damaged
                            }}
                        </h3>

                    </div>
                </div>

            </div>

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
                            placeholder="Barcode, accession, copy or book..."
                            @keyup.enter="loadCopies(1)"
                        >

                    </div>


                    <!-- Book -->
                    <div class="col-md-3">

                        <label class="form-label">
                            Book
                        </label>

                        <select
                            v-model="filters.book_id"
                            class="form-select"
                        >

                            <option value="">
                                All Books
                            </option>

                            <option
                                v-for="book in books"
                                :key="book.id"
                                :value="book.id"
                            >
                                {{ book.title }}
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

                            <option value="Available">
                                Available
                            </option>

                            <option value="Issued">
                                Issued
                            </option>

                            <option value="Reserved">
                                Reserved
                            </option>

                            <option value="Lost">
                                Lost
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>

                            <option value="Maintenance">
                                Maintenance
                            </option>

                        </select>

                    </div>


                    <!-- Condition -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Condition
                        </label>

                        <select
                            v-model="filters.condition"
                            class="form-select"
                        >

                            <option value="">
                                All
                            </option>

                            <option value="New">
                                New
                            </option>

                            <option value="Good">
                                Good
                            </option>

                            <option value="Fair">
                                Fair
                            </option>

                            <option value="Damaged">
                                Damaged
                            </option>

                            <option value="Lost">
                                Lost
                            </option>

                        </select>

                    </div>


                    <!-- Buttons -->
                    <div class="col-md-1 d-flex align-items-end">

                        <button
                            type="button"
                            class="btn btn-primary me-1"
                            :disabled="loading"
                            @click="loadCopies(1)"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            @click="resetFilters"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- List -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Book Copy List
                </strong>

                <span class="text-muted">
                    {{ pagination.total }} records
                </span>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="55">
                                    #
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Accession
                                </th>

                                <th>
                                    Barcode
                                </th>

                                <th>
                                    Location
                                </th>

                                <th>
                                    Purchase
                                </th>

                                <th>
                                    Condition
                                </th>

                                <th>
                                    Status
                                </th>

                                <th
                                    width="160"
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
                                    colspan="9"
                                    class="text-center py-5"
                                >

                                    <div
                                        class="spinner-border text-primary"
                                    ></div>

                                    <div class="text-muted mt-2">
                                        Loading book copies...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    copies.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-bookshelf fs-2 d-block mb-2"
                                    ></i>

                                    No book copies found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    copy,
                                    index
                                ) in copies"
                                :key="copy.id"
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


                                <!-- Book -->
                                <td>

                                    <strong>
                                        {{ copy.book?.title || '-' }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{
                                            copy.book?.book_code ||
                                            ''
                                        }}
                                    </small>

                                </td>


                                <!-- Accession -->
                                <td>

                                    <strong>
                                        {{ copy.accession_no }}
                                    </strong>

                                    <small
                                        v-if="copy.copy_no"
                                        class="d-block text-muted"
                                    >
                                        Copy:
                                        {{ copy.copy_no }}
                                    </small>

                                </td>


                                <!-- Barcode -->
                                <td>

                                    <span
                                        v-if="copy.barcode"
                                        class="font-monospace"
                                    >
                                        {{ copy.barcode }}
                                    </span>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <!-- Location -->
                                <td>

                                    <div>
                                        Shelf:
                                        {{ copy.shelf_no || '-' }}
                                    </div>

                                    <small class="text-muted">
                                        Rack:
                                        {{ copy.rack_no || '-' }}
                                    </small>

                                </td>


                                <!-- Purchase -->
                                <td>

                                    <div>
                                        {{
                                            formatPrice(
                                                copy.purchase_price
                                            )
                                        }}
                                    </div>

                                    <small
                                        v-if="copy.purchase_date"
                                        class="text-muted"
                                    >
                                        {{
                                            formatDate(
                                                copy.purchase_date
                                            )
                                        }}
                                    </small>

                                </td>


                                <!-- Condition -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            conditionClass(
                                                copy.condition
                                            )
                                        "
                                    >
                                        {{ copy.condition }}
                                    </span>

                                </td>


                                <!-- Status -->
                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            statusClass(
                                                copy.status
                                            )
                                        "
                                    >
                                        {{ copy.status }}
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
                                            copy.status === 'Issued'
                                        "
                                        @click="
                                            openEdit(copy)
                                        "
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <!-- Status -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="Change Status"
                                        :disabled="
                                            actionId === copy.id ||
                                            copy.status === 'Issued'
                                        "
                                        @click="
                                            changeStatus(copy)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === copy.id
                                            "
                                            class="spinner-border spinner-border-sm"
                                        ></span>

                                        <i
                                            v-else
                                            class="bi bi-arrow-repeat"
                                        ></i>

                                    </button>


                                    <!-- Delete -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete"
                                        :disabled="
                                            actionId === copy.id ||
                                            copy.status === 'Issued'
                                        "
                                        @click="
                                            deleteCopy(copy)
                                        "
                                    >
                                        <i class="bi bi-trash"></i>
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

            <div class="modal-dialog modal-lg modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="saveCopy"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">

                                {{
                                    editing
                                        ? 'Edit Book Copy'
                                        : 'Add Book Copy'
                                }}

                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                @click="closeModal"
                            ></button>

                        </div>


                        <div class="modal-body">

                            <div
                                v-if="formError"
                                class="alert alert-danger"
                            >
                                {{ formError }}
                            </div>


                            <div class="row g-3">

                                <!-- Book -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Book
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.book_id"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.book_id
                                        }"
                                        :disabled="
                                            editing &&
                                            currentCopyStatus === 'Issued'
                                        "
                                    >

                                        <option value="">
                                            Select Book
                                        </option>

                                        <option
                                            v-for="book in books"
                                            :key="book.id"
                                            :value="book.id"
                                        >
                                            {{ book.title }}
                                            -
                                            {{ book.book_code }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.book_id"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.book_id }}
                                    </div>

                                </div>


                                <!-- Copy No -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Copy No.
                                    </label>

                                    <input
                                        v-model="form.copy_no"
                                        type="text"
                                        class="form-control"
                                        placeholder="001"
                                    >

                                </div>


                                <!-- Accession -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Accession No.
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.accession_no"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.accession_no
                                        }"
                                        placeholder="LIB-00001"
                                    >

                                    <div
                                        v-if="formErrors.accession_no"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.accession_no }}
                                    </div>

                                </div>


                                <!-- Barcode -->
                                <div class="col-md-6">

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
                                        placeholder="89000001"
                                    >

                                    <div
                                        v-if="formErrors.barcode"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.barcode }}
                                    </div>

                                </div>


                                <!-- Shelf -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Shelf No.
                                    </label>

                                    <input
                                        v-model="form.shelf_no"
                                        type="text"
                                        class="form-control"
                                        placeholder="S-01"
                                    >

                                </div>


                                <!-- Rack -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Rack No.
                                    </label>

                                    <input
                                        v-model="form.rack_no"
                                        type="text"
                                        class="form-control"
                                        placeholder="R-01"
                                    >

                                </div>


                                <!-- Purchase Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Purchase Date
                                    </label>

                                    <input
                                        v-model="form.purchase_date"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.purchase_date
                                        }"
                                    >

                                    <div
                                        v-if="formErrors.purchase_date"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.purchase_date }}
                                    </div>

                                </div>


                                <!-- Purchase Price -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Purchase Price
                                    </label>

                                    <input
                                        v-model="form.purchase_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.purchase_price
                                        }"
                                        placeholder="0.00"
                                    >

                                    <div
                                        v-if="formErrors.purchase_price"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.purchase_price }}
                                    </div>

                                </div>


                                <!-- Condition -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Condition
                                    </label>

                                    <select
                                        v-model="form.condition"
                                        class="form-select"
                                    >

                                        <option value="New">
                                            New
                                        </option>

                                        <option value="Good">
                                            Good
                                        </option>

                                        <option value="Fair">
                                            Fair
                                        </option>

                                        <option value="Damaged">
                                            Damaged
                                        </option>

                                        <option value="Lost">
                                            Lost
                                        </option>

                                    </select>

                                </div>


                                <!-- Status -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <select
                                        v-model="form.status"
                                        class="form-select"
                                        :disabled="
                                            editing &&
                                            currentCopyStatus === 'Issued'
                                        "
                                    >

                                        <option value="Available">
                                            Available
                                        </option>

                                        <option value="Reserved">
                                            Reserved
                                        </option>

                                        <option value="Lost">
                                            Lost
                                        </option>

                                        <option value="Damaged">
                                            Damaged
                                        </option>

                                        <option value="Maintenance">
                                            Maintenance
                                        </option>

                                        <option
                                            v-if="
                                                currentCopyStatus === 'Issued'
                                            "
                                            value="Issued"
                                        >
                                            Issued
                                        </option>

                                    </select>

                                </div>


                                <!-- Notes -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Notes
                                    </label>

                                    <textarea
                                        v-model="form.notes"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Additional notes..."
                                    ></textarea>

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
                                :disabled="saving"
                            >

                                <span
                                    v-if="saving"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>

                                {{
                                    editing
                                        ? 'Update'
                                        : 'Save'
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

const copies = ref([]);

const books = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const currentCopyStatus = ref('');

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const statistics = ref({

    total: 0,

    available: 0,

    issued: 0,

    reserved: 0,

    lost: 0,

    damaged: 0,

    maintenance: 0

});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    book_id: '',

    status: '',

    condition: '',

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
| Form
|--------------------------------------------------------------------------
*/

const emptyForm = () => ({

    book_id: '',

    accession_no: '',

    barcode: '',

    copy_no: '',

    shelf_no: '',

    rack_no: '',

    purchase_date: '',

    purchase_price: 0,

    condition: 'New',

    status: 'Available',

    notes: ''

});


const form = ref(
    emptyForm()
);


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
| Load Copies
|--------------------------------------------------------------------------
*/

const loadCopies = async (
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

            book_id:
                filters.value.book_id ||
                undefined,

            status:
                filters.value.status ||
                undefined,

            condition:
                filters.value.condition ||
                undefined

        };


        const response =
            await axios.get(
                '/api/book-copies',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        copies.value =
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
            'Book copy loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load book copies.';

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Load Books
|--------------------------------------------------------------------------
*/

const loadBooks = async () => {

    try {

        const response =
            await axios.get(
                '/api/books',
                {
                    ...getAuthConfig(),
                    params: {
                        per_page: 100
                    }
                }
            );


        const result =
            response.data?.data;


        books.value =
            result?.data ||
            [];

    } catch (err) {

        console.error(
            'Book loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Statistics
|--------------------------------------------------------------------------
*/

const loadStatistics = async () => {

    try {

        const response =
            await axios.get(
                '/api/book-copies/statistics',
                getAuthConfig()
            );


        statistics.value = {

            total:
                Number(
                    response.data?.data?.total ||
                    0
                ),

            available:
                Number(
                    response.data?.data?.available ||
                    0
                ),

            issued:
                Number(
                    response.data?.data?.issued ||
                    0
                ),

            reserved:
                Number(
                    response.data?.data?.reserved ||
                    0
                ),

            lost:
                Number(
                    response.data?.data?.lost ||
                    0
                ),

            damaged:
                Number(
                    response.data?.data?.damaged ||
                    0
                ),

            maintenance:
                Number(
                    response.data?.data?.maintenance ||
                    0
                )

        };

    } catch (err) {

        console.error(
            'Statistics loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreate = () => {

    editing.value = false;

    editingId.value = null;

    currentCopyStatus.value = '';

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

const openEdit = (
    copy
) => {

    editing.value = true;

    editingId.value =
        copy.id;

    currentCopyStatus.value =
        copy.status;


    form.value = {

        book_id:
            copy.book_id ||
            copy.book?.id ||
            '',

        accession_no:
            copy.accession_no ||
            '',

        barcode:
            copy.barcode ||
            '',

        copy_no:
            copy.copy_no ||
            '',

        shelf_no:
            copy.shelf_no ||
            '',

        rack_no:
            copy.rack_no ||
            '',

        purchase_date:
            copy.purchase_date
                ? String(
                    copy.purchase_date
                ).substring(
                    0,
                    10
                )
                : '',

        purchase_price:
            copy.purchase_price ||
            0,

        condition:
            copy.condition ||
            'New',

        status:
            copy.status ||
            'Available',

        notes:
            copy.notes ||
            ''

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

const closeModal = () => {

    if (saving.value) {
        return;
    }

    showModal.value = false;

};


/*
|--------------------------------------------------------------------------
| Save Copy
|--------------------------------------------------------------------------
*/

const saveCopy = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            book_id:
                form.value.book_id,

            accession_no:
                form.value.accession_no,

            barcode:
                form.value.barcode ||
                null,

            copy_no:
                form.value.copy_no ||
                null,

            shelf_no:
                form.value.shelf_no ||
                null,

            rack_no:
                form.value.rack_no ||
                null,

            purchase_date:
                form.value.purchase_date ||
                null,

            purchase_price:
                form.value.purchase_price ||
                0,

            condition:
                form.value.condition,

            status:
                form.value.status,

            notes:
                form.value.notes ||
                null

        };


        let response;


        if (editing.value) {

            response =
                await axios.put(
                    `/api/book-copies/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/book-copies',
                    payload,
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            'Book copy saved successfully.';


        showModal.value = false;


        await Promise.all([

            loadCopies(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        console.error(
            'Book copy save error:',
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
                'Unable to save book copy.';

        }

    } finally {

        saving.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Change Status
|--------------------------------------------------------------------------
*/

const changeStatus = async (
    copy
) => {

    if (
        copy.status === 'Issued'
    ) {

        error.value =
            'Issued copies must be processed through Book Return.';

        return;

    }


    const statuses = [

        'Available',

        'Reserved',

        'Lost',

        'Damaged',

        'Maintenance'

    ];


    const currentIndex =
        statuses.indexOf(
            copy.status
        );


    const nextStatus =
        statuses[
            currentIndex >= 0
                ? (
                    currentIndex + 1
                ) % statuses.length
                : 0
        ];


    if (
        !window.confirm(
            `Change copy "${copy.accession_no}" from ${copy.status} to ${nextStatus}?`
        )
    ) {
        return;
    }


    actionId.value =
        copy.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.post(
                `/api/book-copies/${copy.id}/status`,
                {
                    status:
                        nextStatus
                },
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book copy status updated successfully.';


        await Promise.all([

            loadCopies(
                pagination.value.current_page
            ),

            loadStatistics()

        ]);

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to change book copy status.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteCopy = async (
    copy
) => {

    if (
        !window.confirm(
            `Delete book copy "${copy.accession_no}"?`
        )
    ) {
        return;
    }


    actionId.value =
        copy.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(
                `/api/book-copies/${copy.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book copy deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            copies.value.length === 1 &&
            page > 1
        ) {
            page--;
        }


        await Promise.all([

            loadCopies(page),

            loadStatistics()

        ]);

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete book copy.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {

        search: '',

        book_id: '',

        status: '',

        condition: '',

        per_page: 20

    };


    loadCopies(1);

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


    loadCopies(page);

};


/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

const statusClass = (
    status
) => {

    switch (status) {

        case 'Available':
            return 'bg-success';

        case 'Issued':
            return 'bg-primary';

        case 'Reserved':
            return 'bg-warning text-dark';

        case 'Lost':
            return 'bg-danger';

        case 'Damaged':
            return 'bg-danger';

        case 'Maintenance':
            return 'bg-secondary';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| Condition Badge
|--------------------------------------------------------------------------
*/

const conditionClass = (
    condition
) => {

    switch (condition) {

        case 'New':
            return 'bg-success';

        case 'Good':
            return 'bg-primary';

        case 'Fair':
            return 'bg-warning text-dark';

        case 'Damaged':
            return 'bg-danger';

        case 'Lost':
            return 'bg-danger';

        default:
            return 'bg-secondary';

    }

};


/*
|--------------------------------------------------------------------------
| Price
|--------------------------------------------------------------------------
*/

const formatPrice = (
    price
) => {

    return Number(
        price || 0
    ).toFixed(2);

};


/*
|--------------------------------------------------------------------------
| Date
|--------------------------------------------------------------------------
*/

const formatDate = (
    date
) => {

    if (!date) {
        return '-';
    }


    return String(
        date
    ).substring(
        0,
        10
    );

};


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([

            loadBooks(),

            loadCopies(1),

            loadStatistics()

        ]);

    }
);

</script>