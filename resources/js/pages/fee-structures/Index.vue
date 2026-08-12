<script setup>

import {
    onMounted,
    ref
} from "vue";

import {
    useRouter
} from "vue-router";

import AppBreadcrumb
    from "@/components/ui/AppBreadcrumb.vue";

import AppButton
    from "@/components/ui/AppButton.vue";

import api
    from "@/api/axios";


const router = useRouter();

const items = ref([]);

const loading = ref(false);

const search = ref("");

const filters = ref({
    academic_session_id: "",
    class_id: "",
    fee_type_id: "",
    status: "",
});

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
});


/*
|--------------------------------------------------------------------------
| Load Fee Structures
|--------------------------------------------------------------------------
*/

const load = async (page = 1) => {

    loading.value = true;

    try {

        const params = {
            page,
            per_page: pagination.value.per_page,
        };

        if (filters.value.academic_session_id) {
            params.academic_session_id =
                filters.value.academic_session_id;
        }

        if (filters.value.class_id) {
            params.class_id =
                filters.value.class_id;
        }

        if (filters.value.fee_type_id) {
            params.fee_type_id =
                filters.value.fee_type_id;
        }

        if (filters.value.status !== "") {
            params.status =
                filters.value.status;
        }

        const { data } = await api.get(
            "/fee-structures",
            { params }
        );

        items.value =
            data.data?.data ?? [];

        pagination.value = {
            current_page:
                data.data?.current_page ?? 1,

            last_page:
                data.data?.last_page ?? 1,

            per_page:
                data.data?.per_page ??
                pagination.value.per_page,

            total:
                data.data?.total ?? 0,
        };

    } catch (error) {

        console.error(
            "Fee structure load error:",
            error
        );

        alert(
            error.response?.data?.message ??
            "Unable to load fee structures."
        );

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const remove = async (item) => {

    if (
        !confirm(
            `Delete fee structure for "${item.fee_type?.name ?? 'this fee'}"?`
        )
    ) {
        return;
    }

    try {

        await api.delete(
            `/fee-structures/${item.id}`
        );

        await load(
            pagination.value.current_page
        );

    } catch (error) {

        console.error(
            "Fee structure delete error:",
            error
        );

        alert(
            error.response?.data?.message ??
            "Unable to delete fee structure."
        );

    }

};


/*
|--------------------------------------------------------------------------
| Format Amount
|--------------------------------------------------------------------------
*/

const formatAmount = (amount) => {

    return Number(amount ?? 0).toLocaleString(
        "en-IN",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }
    );

};


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

    filters.value = {
        academic_session_id: "",
        class_id: "",
        fee_type_id: "",
        status: "",
    };

    load(1);

};


onMounted(() => {

    load();

});

</script>


<template>

    <AppBreadcrumb
        :items="[
            {
                label: 'Dashboard',
                to: '/'
            },
            {
                label: 'Fee & Accounts'
            },
            {
                label: 'Fee Structures'
            }
        ]"
    />


    <div class="card">

        <!-- Header -->

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Fee Structures
            </h4>


            <AppButton
                text="Add Fee Structure"
                @click="
                    router.push({
                        name: 'fee-structures.create'
                    })
                "
            />

        </div>


        <div class="card-body">


            <!-- Filters -->

            <div class="row mb-3">

                <div class="col-md-3">

                    <input
                        v-model="filters.academic_session_id"
                        type="number"
                        class="form-control"
                        placeholder="Academic Session ID"
                    />

                </div>


                <div class="col-md-3">

                    <input
                        v-model="filters.class_id"
                        type="number"
                        class="form-control"
                        placeholder="Class ID"
                    />

                </div>


                <div class="col-md-3">

                    <input
                        v-model="filters.fee_type_id"
                        type="number"
                        class="form-control"
                        placeholder="Fee Type ID"
                    />

                </div>


                <div class="col-md-3">

                    <select
                        v-model="filters.status"
                        class="form-select"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option value="1">
                            Active
                        </option>

                        <option value="0">
                            Inactive
                        </option>

                    </select>

                </div>

            </div>


            <div class="mb-3">

                <button
                    type="button"
                    class="btn btn-primary me-2"
                    @click="load(1)"
                >
                    Search
                </button>


                <button
                    type="button"
                    class="btn btn-secondary"
                    @click="resetFilters"
                >
                    Reset
                </button>

            </div>


            <!-- Table -->

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover align-middle"
                >

                    <thead>

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Academic Session
                            </th>

                            <th>
                                Class
                            </th>

                            <th>
                                Fee Type
                            </th>

                            <th>
                                Frequency
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Due Day
                            </th>

                            <th>
                                Late Fee
                            </th>

                            <th>
                                Status
                            </th>

                            <th
                                width="180"
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
                                    class="spinner-border"
                                    role="status"
                                ></div>

                                <div class="mt-2 text-muted">
                                    Loading...
                                </div>

                            </td>

                        </tr>


                        <!-- Empty -->

                        <tr
                            v-else-if="
                                !items.length
                            "
                        >

                            <td
                                colspan="10"
                                class="text-center py-5 text-muted"
                            >

                                No fee structures found.

                            </td>

                        </tr>


                        <!-- Data -->

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


                            <td>

                                {{
                                    item.academic_session?.name ??
                                    item.academic_session?.session_year ??
                                    item.academic_session_id ??
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    item.class?.name ??
                                    item.class_id ??
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    item.fee_type?.name ??
                                    "-"
                                }}

                            </td>


                            <td>

                                {{
                                    item.fee_type?.frequency ??
                                    "-"
                                }}

                            </td>


                            <td>

                                ₹
                                {{
                                    formatAmount(
                                        item.amount
                                    )
                                }}

                            </td>


                            <td>

                                <span
                                    v-if="item.due_day"
                                >
                                    {{ item.due_day }}
                                </span>

                                <span
                                    v-else
                                    class="text-muted"
                                >
                                    -
                                </span>

                            </td>


                            <td>

                                ₹
                                {{
                                    formatAmount(
                                        item.late_fee
                                    )
                                }}

                            </td>


                            <td>

                                <span
                                    :class="
                                        item.status
                                            ? 'badge bg-success'
                                            : 'badge bg-danger'
                                    "
                                >

                                    {{
                                        item.status
                                            ? 'Active'
                                            : 'Inactive'
                                    }}

                                </span>

                            </td>


                            <td
                                class="text-center"
                            >

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary me-1"
                                    @click="
                                        router.push({
                                            name:
                                                'fee-structures.edit',
                                            params: {
                                                id:
                                                    item.id
                                            }
                                        })
                                    "
                                >
                                    Edit
                                </button>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    @click="
                                        remove(item)
                                    "
                                >
                                    Delete
                                </button>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- Pagination -->

            <div
                v-if="
                    pagination.last_page > 1
                "
                class="d-flex justify-content-between align-items-center mt-3"
            >

                <div class="text-muted">

                    Total:
                    {{ pagination.total }}

                </div>


                <div>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary me-2"
                        :disabled="
                            pagination.current_page <= 1
                        "
                        @click="
                            load(
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
                        class="btn btn-sm btn-outline-primary ms-2"
                        :disabled="
                            pagination.current_page >=
                            pagination.last_page
                        "
                        @click="
                            load(
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

</template>