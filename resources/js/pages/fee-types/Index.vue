<script setup>

import {
    onMounted,
    ref
} from "vue";

import AppBreadcrumb
    from "@/components/ui/AppBreadcrumb.vue";

import AppButton
    from "@/components/ui/AppButton.vue";

import api
    from "@/api/axios";


const items = ref([]);

const loading = ref(false);

const search = ref("");

const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 20,
    total: 0,
});


/*
|--------------------------------------------------------------------------
| Load
|--------------------------------------------------------------------------
*/

const load = async (
    page = 1
) => {

    loading.value = true;

    try {

        const { data } =
            await api.get(
                "/fee-types",
                {
                    params: {
                        search: search.value,
                        page,
                        per_page:
                            pagination.value.per_page,
                    }
                }
            );


        items.value =
            data.data?.data ?? [];


        if (data.data) {

            pagination.value = {
                current_page:
                    data.data.current_page,

                last_page:
                    data.data.last_page,

                per_page:
                    data.data.per_page,

                total:
                    data.data.total,
            };

        }

    } catch (error) {

        console.error(
            "Fee type load error:",
            error
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
            `Delete "${item.name}"?`
        )
    ) {
        return;
    }


    try {

        await api.delete(
            `/fee-types/${item.id}`
        );


        await load(
            pagination.value.current_page
        );

    } catch (error) {

        console.error(
            "Fee type delete error:",
            error
        );

        alert(
            error.response?.data?.message ??
            "Unable to delete fee type."
        );

    }

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
                label: 'Fee Types'
            }
        ]"
    />


    <div class="card">

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Fee Types
            </h4>


            <AppButton
                text="Add Fee Type"
                @click="
                    $router.push({
                        name: 'fee-types.create'
                    })
                "
            />

        </div>


        <div class="card-body">


            <!-- Search -->

            <div class="row mb-3">

                <div class="col-md-4">

                    <input
                        v-model="search"
                        type="text"
                        class="form-control"
                        placeholder="Search fee type..."
                        @keyup.enter="
                            load(1)
                        "
                    />

                </div>


                <div class="col-md-2">

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="load(1)"
                    >
                        Search
                    </button>

                </div>

            </div>


            <!-- Table -->

            <div class="table-responsive">

                <table
                    class="table table-bordered table-hover"
                >

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Frequency
                            </th>

                            <th>
                                Status
                            </th>

                            <th
                                class="text-center"
                            >
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-if="loading"
                        >

                            <td
                                colspan="6"
                                class="text-center py-4"
                            >

                                Loading...

                            </td>

                        </tr>


                        <tr
                            v-else-if="
                                !items.length
                            "
                        >

                            <td
                                colspan="6"
                                class="text-center py-4"
                            >

                                No fee types found.

                            </td>

                        </tr>


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
                                        pagination.current_page -
                                        1
                                    ) *
                                    pagination.per_page +
                                    index +
                                    1
                                }}
                            </td>


                            <td>
                                {{ item.name }}
                            </td>


                            <td>
                                {{ item.code || "-" }}
                            </td>


                            <td>
                                {{ item.frequency }}
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
                                        $router.push({
                                            name:
                                                'fee-types.edit',
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

                    Showing
                    {{ items.length }}
                    of
                    {{ pagination.total }}

                </div>


                <div>

                    <button
                        class="btn btn-sm btn-outline-primary me-1"
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
                        /
                        {{ pagination.last_page }}

                    </span>


                    <button
                        class="btn btn-sm btn-outline-primary"
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