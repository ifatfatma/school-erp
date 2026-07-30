<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h3>Subject Details</h3>

            <div>

                <RouterLink
                    :to="{ name:'subjects.edit', params:{ id: route.params.id } }"
                    class="btn btn-warning me-2"
                >
                    <i class="bi bi-pencil"></i>
                    Edit
                </RouterLink>

                <RouterLink
                    :to="{ name:'subjects.index' }"
                    class="btn btn-secondary"
                >
                    Back
                </RouterLink>

            </div>

        </div>

        <div class="card">

            <div
                class="card-body"
                v-if="!loading && item"
            >

                <table class="table table-bordered">

                    <tbody>

                        <tr>
                            <th width="220">ID</th>
                            <td>{{ item.id }}</td>
                        </tr>

                        <tr>
                            <th>Subject Name</th>
                            <td>{{ item.name }}</td>
                        </tr>

                        <tr>
                            <th>Subject Code</th>
                            <td>{{ item.code }}</td>
                        </tr>

                        <tr>
                            <th>Subject Type</th>
                            <td>{{ item.type }}</td>
                        </tr>

                        <tr>
                            <th>Description</th>
                            <td>
                                {{ item.description || '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>

                            <td>

                                <span
                                    class="badge"
                                    :class="item.status ? 'bg-success' : 'bg-danger'"
                                >
                                    {{ item.status ? 'Active' : 'Inactive' }}
                                </span>

                            </td>

                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>{{ item.created_at }}</td>
                        </tr>

                        <tr>
                            <th>Updated At</th>
                            <td>{{ item.updated_at }}</td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div
                v-else
                class="text-center p-5"
            >
                Loading...
            </div>

        </div>

    </div>
</template>

<script setup>

import { onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

import { useSubject } from '@/composables/useSubject'

const route = useRoute()

const {

    loading,

    item,

    fetchItem

} = useSubject()

onMounted(() => {

    fetchItem(route.params.id)

})

</script>