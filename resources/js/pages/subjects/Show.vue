<template>
    <div class="container-fluid py-2">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Subjects', to: { name: 'subjects.index' } },
                { label: 'Details' }
            ]"
        />

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
                v-if="!loading && subjectData"
            >

                <table class="table table-bordered">

                    <tbody>

                        <tr>
                            <th width="220">ID</th>
                            <td>{{ subjectData.id }}</td>
                        </tr>

                        <tr>
                            <th>Subject Name</th>
                            <td>{{ subjectData.name }}</td>
                        </tr>

                        <tr>
                            <th>Subject Code</th>
                            <td>{{ subjectData.code }}</td>
                        </tr>

                        <tr>
                            <th>Subject Type</th>
                            <td>{{ subjectData.type }}</td>
                        </tr>

                        <tr>
                            <th>Description</th>
                            <td>
                                {{ subjectData.description || '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Status</th>

                            <td>

                                <span
                                    class="badge"
                                    :class="subjectData.status ? 'bg-success' : 'bg-danger'"
                                >
                                    {{ subjectData.status ? 'Active' : 'Inactive' }}
                                </span>

                            </td>

                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>{{ subjectData.created_at }}</td>
                        </tr>

                        <tr>
                            <th>Updated At</th>
                            <td>{{ subjectData.updated_at }}</td>
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

import { computed, onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

import { useSubject } from '@/composables/useSubject'

const route = useRoute()

const {

    loading,

    item,

    fetchItem

} = useSubject()

const subjectData = computed(() => {
    if (!item.value) return null
    return item.value.data || item.value
})

onMounted(() => {

    fetchItem(route.params.id)

})

</script>