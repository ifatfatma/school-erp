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
                <h4 class="mb-0">Class Details</h4>

                <div class="d-flex gap-2">
                    <RouterLink
                        v-if="classData?.id"
                        :to="{ name: 'classes.edit', params: { id: classData.id } }"
                        class="btn btn-warning"
                    >
                        <i class="bi bi-pencil me-1"></i> Edit Class
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'classes.index' }"
                        class="btn btn-secondary"
                    >
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </RouterLink>
                </div>
            </div>

            <div
                class="card-body"
                v-if="!loading && classData"
            >
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th width="220">Class Name</th>
                            <td><strong>{{ classData.name }}</strong></td>
                        </tr>

                        <tr>
                            <th>Numeric Name</th>
                            <td>{{ classData.numeric_name || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Remarks</th>
                            <td>{{ classData.remarks || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <span
                                    class="badge"
                                    :class="classData.status ? 'bg-success' : 'bg-danger'"
                                >
                                    {{ classData.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Sections</th>
                            <td>
                                <div v-if="classData.sections && classData.sections.length" class="d-flex flex-wrap gap-2">
                                    <span
                                        v-for="sec in classData.sections"
                                        :key="sec.id"
                                        class="badge bg-primary fs-7"
                                    >
                                        Section {{ sec.name }} (Capacity: {{ sec.capacity ?? '-' }})
                                    </span>
                                </div>
                                <span v-else class="text-muted">No sections assigned</span>
                            </td>
                        </tr>

                        <tr>
                            <th>Total Students</th>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ classData.students_count ?? 0 }} Students Enrolled
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>{{ classData.created_at ? formatDate(classData.created_at) : '-' }}</td>
                        </tr>

                        <tr>
                            <th>Updated At</th>
                            <td>{{ classData.updated_at ? formatDate(classData.updated_at) : '-' }}</td>
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
import { computed, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'
import useSchoolClass from '@/composables/useSchoolClass'

const route = useRoute()
const { item, loading, fetchItem } = useSchoolClass()

const classData = computed(() => {
    if (!item.value) return null
    return item.value.data || item.value
})

function formatDate(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return isNaN(d) ? dateStr : d.toLocaleDateString() + ' ' + d.toLocaleTimeString()
}

onMounted(() => {
    fetchItem(route.params.id)
})
</script>
