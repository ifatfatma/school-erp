<template>
    <div class="container-fluid">
        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Classes' },
            ]"
        />

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Classes</h3>
            <RouterLink
                :to="{ name: 'classes.create' }"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle me-1"></i> Add Class
            </RouterLink>
        </div>

        <!-- Filter Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <!-- School Filter (Super Admin) -->
                    <div class="col-md-3" v-if="isSuperAdmin && allSchools.length > 0">
                        <select
                            v-model="filters.school_id"
                            class="form-select fw-semibold"
                            @change="search"
                        >
                            <option value="">All Schools</option>
                            <option
                                v-for="s in allSchools"
                                :key="'cls-sch-' + s.id"
                                :value="s.id"
                            >
                                {{ s.name }} ({{ s.code }})
                            </option>
                        </select>
                    </div>

                    <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-3' : 'col-md-5'">
                        <input
                            v-model="filters.search"
                            @keyup.enter="search"
                            class="form-control"
                            placeholder="Search class name or numeric name..."
                        >
                    </div>

                    <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-2' : 'col-md-3'">
                        <select
                            v-model="filters.status"
                            class="form-select"
                            @change="search"
                        >
                            <option value="">All Status</option>
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>
                    </div>

                    <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-4' : 'col-md-4'">
                        <button
                            class="btn btn-primary me-2"
                            @click="search"
                        >
                            <i class="bi bi-search me-1"></i> Search
                        </button>

                        <button
                            class="btn btn-secondary"
                            @click="onReset"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <AppTable
                    :columns="columns"
                    :items="items"
                    :loading="loading"
                >
                    <template #school="{ item }">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                            <i class="bi bi-building me-1"></i> {{ item.school?.name || 'School' }}
                        </span>
                        <small v-if="item.school?.code" class="text-muted d-block font-monospace">
                            ({{ item.school.code }})
                        </small>
                    </template>
                    <template #status="{ item }">
                        <span
                            class="badge"
                            :class="item.status ? 'bg-success' : 'bg-danger'"
                        >
                            {{ item.status ? 'Active' : 'Inactive' }}
                        </span>
                    </template>

                    <template #action="{ item }">
                        <div class="btn-group btn-group-sm">
                            <RouterLink
                                :to="{ name: 'classes.show', params: { id: item.id } }"
                                class="btn btn-info text-white"
                                title="View Class"
                            >
                                <i class="bi bi-eye"></i>
                            </RouterLink>

                            <RouterLink
                                :to="{ name: 'classes.edit', params: { id: item.id } }"
                                class="btn btn-warning text-dark"
                                title="Edit Class"
                            >
                                <i class="bi bi-pencil"></i>
                            </RouterLink>

                            <button
                                @click="remove(item.id)"
                                class="btn btn-danger"
                                title="Delete Class"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </template>
                </AppTable>
            </div>
        </div>

        <div class="mt-3">
            <AppPagination
                :pagination="pagination"
                @page-change="changePage"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Swal from 'sweetalert2'
import axios from '@/api/axios'

import AppTable from '@/components/ui/AppTable.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

import useSchoolClass from '@/composables/useSchoolClass'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const isSuperAdmin = computed(() => {
    const u = authStore.user
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '')
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin'
    })
})

const allSchools = ref([])

const columns = computed(() => {
    const cols = [
        { key: 'name', label: 'Class Name' },
    ]

    if (isSuperAdmin.value) {
        cols.push({ key: 'school', label: 'School / Campus' })
    }

    cols.push(
        { key: 'numeric_name', label: 'Numeric Name' },
        { key: 'status', label: 'Status' },
        { key: 'action', label: 'Action' }
    )

    return cols
})

const {
    items,
    loading,
    pagination,
    filters,
    fetchItems,
    deleteItem,
    search,
    resetFilters,
    changePage
} = useSchoolClass()

async function loadSchools() {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get('/tenants')
            if (res.data?.data?.length) {
                allSchools.value = res.data.data
                return
            }
        } catch (e) {}

        try {
            const res2 = await axios.get('/public/saas/schools')
            if (res2.data?.data?.length) {
                allSchools.value = res2.data.data
            }
        } catch (e2) {}
    }
}

function onReset() {
    filters.school_id = ''
    resetFilters()
}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile()
    }
    await Promise.all([
        loadSchools(),
        fetchItems()
    ])
})

async function remove(id) {
    const result = await Swal.fire({
        title: 'Delete Class?',
        text: 'All sections and associations for this class may be affected.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    })

    if (!result.isConfirmed) {
        return
    }

    try {
        await deleteItem(id)
        Swal.fire('Deleted!', 'Class has been deleted successfully.', 'success')
        fetchItems()
    } catch (error) {
        Swal.fire('Error', error.response?.data?.message || 'Failed to delete class.', 'error')
    }
}
</script>
