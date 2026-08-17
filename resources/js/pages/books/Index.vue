<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Books</h4>
                <small class="text-muted">
                    Manage library books
                </small>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                @click="openCreate"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Add Book
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
                            placeholder="Title, code, ISBN or author..."
                            @keyup.enter="loadBooks(1)"
                        >

                    </div>


                    <!-- Category -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Category
                        </label>

                        <select
                            v-model="filters.category_id"
                            class="form-select"
                        >
                            <option value="">
                                All
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


                    <!-- Publisher -->
                    <div class="col-md-2">

                        <label class="form-label">
                            Publisher
                        </label>

                        <select
                            v-model="filters.publisher_id"
                            class="form-select"
                        >
                            <option value="">
                                All
                            </option>

                            <option
                                v-for="publisher in publishers"
                                :key="publisher.id"
                                :value="publisher.id"
                            >
                                {{ publisher.name }}
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
                            @click="loadBooks(1)"
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


        <!-- Books -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <strong>
                    Book List
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

                                <th width="60">
                                    #
                                </th>

                                <th>
                                    Book
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Author(s)
                                </th>

                                <th>
                                    Publisher
                                </th>

                                <th>
                                    ISBN
                                </th>

                                <th>
                                    Price
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
                                        Loading books...
                                    </div>

                                </td>

                            </tr>


                            <!-- Empty -->
                            <tr
                                v-else-if="
                                    books.length === 0
                                "
                            >

                                <td
                                    colspan="9"
                                    class="text-center text-muted py-5"
                                >

                                    <i
                                        class="bi bi-book fs-2 d-block mb-2"
                                    ></i>

                                    No books found.

                                </td>

                            </tr>


                            <!-- Records -->
                            <tr
                                v-for="(
                                    book,
                                    index
                                ) in books"
                                :key="book.id"
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
                                        {{ book.title }}
                                    </strong>

                                    <small
                                        class="d-block text-muted"
                                    >
                                        {{ book.book_code }}
                                    </small>

                                </td>


                                <td>
                                    {{ book.category?.name || '-' }}
                                </td>


                                <td>

                                    <span
                                        v-if="
                                            book.authors &&
                                            book.authors.length
                                        "
                                    >

                                        {{
                                            book.authors
                                                .map(
                                                    author =>
                                                        author.name
                                                )
                                                .join(', ')
                                        }}

                                    </span>

                                    <span v-else>
                                        -
                                    </span>

                                </td>


                                <td>
                                    {{ book.publisher?.name || '-' }}
                                </td>


                                <td>
                                    {{ book.isbn || '-' }}
                                </td>


                                <td>
                                    {{ formatPrice(book.price) }}
                                </td>


                                <td>

                                    <span
                                        class="badge"
                                        :class="
                                            book.status
                                                ? 'bg-success'
                                                : 'bg-secondary'
                                        "
                                    >
                                        {{
                                            book.status
                                                ? 'Active'
                                                : 'Inactive'
                                        }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    <!-- Edit -->
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary me-1"
                                        title="Edit"
                                        @click="
                                            openEdit(book)
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
                                            actionId === book.id
                                        "
                                        @click="
                                            toggleStatus(book)
                                        "
                                    >

                                        <span
                                            v-if="
                                                actionId === book.id
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
                                            actionId === book.id
                                        "
                                        @click="
                                            deleteBook(book)
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

            <div class="modal-dialog modal-xl modal-dialog-centered">

                <div class="modal-content">

                    <form
                        @submit.prevent="saveBook"
                    >

                        <div class="modal-header">

                            <h5 class="modal-title">
                                {{
                                    editing
                                        ? 'Edit Book'
                                        : 'Add Book'
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

                                <!-- Title -->
                                <div class="col-md-8">

                                    <label class="form-label">
                                        Book Title
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.title"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.title
                                        }"
                                        placeholder="Book title"
                                    >

                                    <div
                                        v-if="formErrors.title"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.title }}
                                    </div>

                                </div>


                                <!-- Book Code -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Book Code
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        v-model="form.book_code"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.book_code
                                        }"
                                        placeholder="BOOK-001"
                                    >

                                    <div
                                        v-if="formErrors.book_code"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.book_code }}
                                    </div>

                                </div>


                                <!-- Category -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Category
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.category_id"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.category_id
                                        }"
                                    >

                                        <option value="">
                                            Select Category
                                        </option>

                                        <option
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{ category.name }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.category_id"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.category_id }}
                                    </div>

                                </div>


                                <!-- Publisher -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Publisher
                                    </label>

                                    <select
                                        v-model="form.publisher_id"
                                        class="form-select"
                                        :class="{
                                            'is-invalid':
                                                formErrors.publisher_id
                                        }"
                                    >

                                        <option value="">
                                            Select Publisher
                                        </option>

                                        <option
                                            v-for="publisher in publishers"
                                            :key="publisher.id"
                                            :value="publisher.id"
                                        >
                                            {{ publisher.name }}
                                        </option>

                                    </select>

                                    <div
                                        v-if="formErrors.publisher_id"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.publisher_id }}
                                    </div>

                                </div>


                                <!-- Authors -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Author(s)
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        v-model="form.author_ids"
                                        class="form-select"
                                        multiple
                                        size="4"
                                        :class="{
                                            'is-invalid':
                                                formErrors.author_ids
                                        }"
                                    >

                                        <option
                                            v-for="author in authors"
                                            :key="author.id"
                                            :value="author.id"
                                        >
                                            {{ author.name }}
                                        </option>

                                    </select>

                                    <small class="text-muted">
                                        Hold Ctrl to select multiple authors.
                                    </small>

                                    <div
                                        v-if="formErrors.author_ids"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.author_ids }}
                                    </div>

                                </div>


                                <!-- ISBN -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        ISBN
                                    </label>

                                    <input
                                        v-model="form.isbn"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                formErrors.isbn
                                        }"
                                        placeholder="ISBN"
                                    >

                                    <div
                                        v-if="formErrors.isbn"
                                        class="invalid-feedback"
                                    >
                                        {{ formErrors.isbn }}
                                    </div>

                                </div>


                                <!-- Edition -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Edition
                                    </label>

                                    <input
                                        v-model="form.edition"
                                        type="text"
                                        class="form-control"
                                        placeholder="1st Edition"
                                    >

                                </div>


                                <!-- Language -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Language
                                    </label>

                                    <input
                                        v-model="form.language"
                                        type="text"
                                        class="form-control"
                                        placeholder="English"
                                    >

                                </div>


                                <!-- Publication Year -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Publication Year
                                    </label>

                                    <input
                                        v-model="form.publication_year"
                                        type="number"
                                        class="form-control"
                                        min="1000"
                                        :max="new Date().getFullYear() + 1"
                                        placeholder="2025"
                                    >

                                    <div
                                        v-if="formErrors.publication_year"
                                        class="text-danger small mt-1"
                                    >
                                        {{ formErrors.publication_year }}
                                    </div>

                                </div>


                                <!-- Pages -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Pages
                                    </label>

                                    <input
                                        v-model="form.pages"
                                        type="number"
                                        class="form-control"
                                        min="0"
                                        placeholder="300"
                                    >

                                    <div
                                        v-if="formErrors.pages"
                                        class="text-danger small mt-1"
                                    >
                                        {{ formErrors.pages }}
                                    </div>

                                </div>


                                <!-- Price -->
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Price
                                    </label>

                                    <input
                                        v-model="form.price"
                                        type="number"
                                        class="form-control"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                    >

                                    <div
                                        v-if="formErrors.price"
                                        class="text-danger small mt-1"
                                    >
                                        {{ formErrors.price }}
                                    </div>

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

                                        <option :value="true">
                                            Active
                                        </option>

                                        <option :value="false">
                                            Inactive
                                        </option>

                                    </select>

                                </div>


                                <!-- Description -->
                                <div class="col-md-12">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        v-model="form.description"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Book description..."
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

const books = ref([]);

const categories = ref([]);

const authors = ref([]);

const publishers = ref([]);

const loading = ref(false);

const saving = ref(false);

const actionId = ref(null);

const showModal = ref(false);

const editing = ref(false);

const editingId = ref(null);

const error = ref('');

const success = ref('');

const formError = ref('');

const formErrors = ref({});


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = ref({

    search: '',

    category_id: '',

    publisher_id: '',

    status: '',

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

    title: '',

    book_code: '',

    category_id: '',

    publisher_id: '',

    author_ids: [],

    isbn: '',

    edition: '',

    language: '',

    publication_year: '',

    pages: '',

    price: 0,

    description: '',

    status: true

});


const form = ref(
    emptyForm()
);


/*
|--------------------------------------------------------------------------
| Authentication
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
| Load Books
|--------------------------------------------------------------------------
*/

const loadBooks = async (
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

            category_id:
                filters.value.category_id ||
                undefined,

            publisher_id:
                filters.value.publisher_id ||
                undefined,

            status:
                filters.value.status === ''
                    ? undefined
                    : filters.value.status

        };


        const response =
            await axios.get(
                '/api/books',
                {
                    ...getAuthConfig(),
                    params
                }
            );


        const result =
            response.data?.data;


        books.value =
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
            'Book loading error:',
            err
        );


        error.value =
            err.response?.data?.message ||
            'Unable to load books.';

    } finally {

        loading.value = false;

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
                '/api/book-categories/active',
                getAuthConfig()
            );


        categories.value =
            response.data?.data ||
            [];

    } catch (err) {

        console.error(
            'Category loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Authors
|--------------------------------------------------------------------------
*/

const loadAuthors = async () => {

    try {

        const response =
            await axios.get(
                '/api/authors/active',
                getAuthConfig()
            );


        authors.value =
            response.data?.data ||
            [];

    } catch (err) {

        console.error(
            'Author loading error:',
            err
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Publishers
|--------------------------------------------------------------------------
*/

const loadPublishers = async () => {

    try {

        const response =
            await axios.get(
                '/api/publishers/active',
                getAuthConfig()
            );


        publishers.value =
            response.data?.data ||
            [];

    } catch (err) {

        console.error(
            'Publisher loading error:',
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
    book
) => {

    editing.value = true;

    editingId.value =
        book.id;


    form.value = {

        title:
            book.title ||
            '',

        book_code:
            book.book_code ||
            '',

        category_id:
            book.category_id ||
            book.category?.id ||
            '',

        publisher_id:
            book.publisher_id ||
            book.publisher?.id ||
            '',

        author_ids:
            (book.authors || [])
                .map(
                    author =>
                        author.id
                ),

        isbn:
            book.isbn ||
            '',

        edition:
            book.edition ||
            '',

        language:
            book.language ||
            '',

        publication_year:
            book.publication_year ||
            '',

        pages:
            book.pages ||
            '',

        price:
            book.price ||
            0,

        description:
            book.description ||
            '',

        status:
            Boolean(
                book.status
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

const closeModal = () => {

    if (saving.value) {
        return;
    }

    showModal.value = false;

};


/*
|--------------------------------------------------------------------------
| Save Book
|--------------------------------------------------------------------------
*/

const saveBook = async () => {

    saving.value = true;

    formErrors.value = {};

    formError.value = '';

    error.value = '';

    success.value = '';


    try {

        const payload = {

            title:
                form.value.title,

            book_code:
                form.value.book_code,

            category_id:
                form.value.category_id,

            publisher_id:
                form.value.publisher_id ||
                null,

            author_ids:
                form.value.author_ids,

            isbn:
                form.value.isbn ||
                null,

            edition:
                form.value.edition ||
                null,

            language:
                form.value.language ||
                null,

            publication_year:
                form.value.publication_year ||
                null,

            pages:
                form.value.pages ||
                null,

            price:
                form.value.price ||
                0,

            description:
                form.value.description ||
                null,

            status:
                form.value.status

        };


        let response;


        if (editing.value) {

            response =
                await axios.put(
                    `/api/books/${editingId.value}`,
                    payload,
                    getAuthConfig()
                );

        } else {

            response =
                await axios.post(
                    '/api/books',
                    payload,
                    getAuthConfig()
                );

        }


        success.value =
            response.data?.message ||
            'Book saved successfully.';


        showModal.value = false;


        await loadBooks(
            pagination.value.current_page
        );

    } catch (err) {

        console.error(
            'Book save error:',
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
                'Unable to save book.';

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

const toggleStatus = async (
    book
) => {

    const newStatus =
        !Boolean(
            book.status
        );


    if (
        !window.confirm(
            `Change "${book.title}" status to ${
                newStatus
                    ? 'Active'
                    : 'Inactive'
            }?`
        )
    ) {
        return;
    }


    actionId.value =
        book.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.post(
                `/api/books/${book.id}/status`,
                {
                    status:
                        newStatus
                },
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book status updated successfully.';


        await loadBooks(
            pagination.value.current_page
        );

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to update book status.';

    } finally {

        actionId.value = null;

    }

};


/*
|--------------------------------------------------------------------------
| Delete Book
|--------------------------------------------------------------------------
*/

const deleteBook = async (
    book
) => {

    if (
        !window.confirm(
            `Delete book "${book.title}"?`
        )
    ) {
        return;
    }


    actionId.value =
        book.id;

    error.value = '';

    success.value = '';


    try {

        const response =
            await axios.delete(
                `/api/books/${book.id}`,
                getAuthConfig()
            );


        success.value =
            response.data?.message ||
            'Book deleted successfully.';


        let page =
            pagination.value.current_page;


        if (
            books.value.length === 1 &&
            page > 1
        ) {
            page--;
        }


        await loadBooks(
            page
        );

    } catch (err) {

        error.value =
            err.response?.data?.message ||
            'Unable to delete book.';

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

        category_id: '',

        publisher_id: '',

        status: '',

        per_page: 20

    };


    loadBooks(1);

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


    loadBooks(page);

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
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(
    async () => {

        await Promise.all([

            loadCategories(),

            loadAuthors(),

            loadPublishers(),

            loadBooks(1)

        ]);

    }
);

</script>