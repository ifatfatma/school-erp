<template>
    <div class="container">
         <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Classes'},
            ]"
        />


        <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Classes</h3>
       <RouterLink
                :to="{ name: 'classes.create' }"
                class="btn btn-primary"
            >
                <i class="fas fa-plus"></i> Add Class
        </RouterLink>
    </div>

        <AppTable
            :columns="columns"
            :items="items"
            :loading="loading"
        >

            <template #action="{ item }">
<div class="d-flex gap-2">
    <div class="btn-group btn-group-sm">
                <RouterLink
                    :to="{ name:'classes.show', params:{ id:item.id } }"
                    class="btn btn-sm btn-info"
                >
                    <i class="bi bi-eye"></i>
                </RouterLink>

                <RouterLink
                    :to="{ name:'classes.edit', params:{ id:item.id } }"
                    class="btn btn-sm btn-success"
                >
                    <i class="bi bi-pencil"></i>
                </RouterLink>

                <button @click="remove(item.id)" class="btn btn-sm btn-danger">
                    <i class="bi bi-trash"></i>
                </button>
                </div>
</div>
            </template>

        </AppTable>

    </div>
</template>
<script setup>
import { onMounted } from 'vue'

import AppTable from '@/components/ui/AppTable.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

import useSchoolClass from '@/composables/useSchoolClass'

const columns = [
    { key: 'name', label: 'Class Name' },
    { key: 'numeric_name', label: 'Numeric Name' },
    { key: 'status', label: 'Status' },
    { key: 'action', label: 'Action' }
]

const {
    items,
    loading,
    fetchItems,
    deleteItem
} = useSchoolClass()

onMounted(() => {
    fetchItems()
})

async function remove(id) {
    if (confirm('Delete this class?')) {
        await deleteItem(id)
        fetchItems()
    }
}
</script>
