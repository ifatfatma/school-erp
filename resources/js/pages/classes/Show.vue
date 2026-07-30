<template>
    <div class="container-fluid">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Classes', to: { name: 'classes.index' } },
                { label: 'View Class' }
            ]"
        />

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">
                    Class Details
                </h4>

                <RouterLink
                    :to="{ name: 'classes.index' }"
                    class="btn btn-secondary"
                >
                    Back
                </RouterLink>

            </div>

            <div
                class="card-body"
                 v-if="!loading && item"
            >

                <table class="table table-bordered">

                    <tbody>

                        <tr>
                            <th width="220">Class Name</th>
                            <td>{{ item.data.name }}</td>
                        </tr>

                        <tr>
                            <th>Numeric Name</th>
                            <td>{{ item.data.numeric_name }}</td>
                        </tr>

                        <tr>
                            <th>Remarks</th>
                            <td>{{ item.data.remarks || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>

                                <span
                                    class="badge"
                                    :class="item.data.status ? 'bg-success' : 'bg-danger'"
                                >
                                    {{ item.data.status ? 'Active' : 'Inactive' }}
                                </span>

                            </td>
                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>{{ formatDate(item.data.created_at) }}</td>
                        </tr>

                        <tr>
                            <th>Updated At</th>
                            <td>{{ formatDate(item.data.updated_at) }}</td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div
                v-else
                class="text-center p-5"
            >

                <div class="spinner-border text-primary"></div>

            </div>

        </div>

    </div>
</template>

<script setup>

import { onMounted } from 'vue'
import { useRoute } from 'vue-router'

import useSchoolClass from '@/composables/useSchoolClass'

import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const route = useRoute()

const {

    loading,
    item,
    fetchItem

} = useSchoolClass()

onMounted(() => {
    console.log(route.params.id)
    fetchItem(route.params.id)

})

function formatDate(date) {

    if (!date) return '-'

    return new Date(date).toLocaleString()

}

</script>