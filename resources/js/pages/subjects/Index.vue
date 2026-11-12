<template>
    <div class="container-fluid py-2">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Subjects' }
            ]"
        />

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Subjects</h3>

            <RouterLink
                :to="{ name: 'subjects.create' }"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle me-1"></i>
                Add Subject
            </RouterLink>
        </div>

        <!-- Search -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <!-- School Filter (Super Admin) -->
                    <div class="col-md-3" v-if="isSuperAdmin">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-primary">
                                <i class="bi bi-buildings"></i>
                            </span>
                            <select
                                v-model="filters.school_id"
                                class="form-select border-start-0 fw-semibold"
                                @change="search"
                            >
                                <option :value="''">All Schools</option>
                                <option
                                    v-for="s in allSchools"
                                    :key="'sbj-sch-' + s.id"
                                    :value="s.id"
                                >
                                    {{ s.name }} ({{ s.code }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div :class="isSuperAdmin ? 'col-md-3' : 'col-md-5'">

                        <input
                            v-model="filters.search"
                            @keyup.enter="search"
                            class="form-control"
                            placeholder="Search name or code..."
                        >

                    </div>

                    <div :class="isSuperAdmin ? 'col-md-2' : 'col-md-3'">

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

                    <div :class="isSuperAdmin ? 'col-md-4' : 'col-md-4'">

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

        <!-- Table -->

        <AppTable
            :columns="columns"
            :items="items"
            :loading="loading"
        >

            <template #school="{ item }">
                <span v-if="item.school" class="badge bg-light text-dark border">
                    <i class="bi bi-building me-1"></i>{{ item.school.name }} ({{ item.school.code }})
                </span>
                <span v-else class="text-muted small">Global</span>
            </template>

            <template #status="{ item }">

                <span
                    class="badge"
                    :class="item.status ? 'bg-success' : 'bg-danger'"
                >
                    {{ item.status ? 'Active' : 'Inactive' }}
                </span>

            </template>

            <template #actions="{ item }">

                <div class="btn-group btn-group-sm">

                    <RouterLink
                        :to="{ name:'subjects.show',params:{id:item.id} }"
                        class="btn btn-info"
                        title="View Subject"
                    >
                        <i class="bi bi-eye"></i>
                    </RouterLink>

                    <RouterLink
                        :to="{ name:'subjects.edit',params:{id:item.id} }"
                        class="btn btn-warning"
                        title="Edit Subject"
                    >
                        <i class="bi bi-pencil"></i>
                    </RouterLink>

                    <button
                        class="btn btn-danger"
                        @click="confirmDelete(item.id)"
                        title="Delete Subject"
                    >
                        <i class="bi bi-trash"></i>
                    </button>

                </div>

            </template>

        </AppTable>

        <!-- Pagination -->

        <AppPagination
            :pagination="pagination"
            @page-change="changePage"
        />

    </div>
</template>

<script setup>

import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { RouterLink } from 'vue-router'
import Swal from 'sweetalert2'
import axios from '@/api/axios'

import AppTable from '@/components/ui/AppTable.vue'
import AppPagination from '@/components/ui/AppPagination.vue'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

import { useSubject } from '@/composables/useSubject'
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

const {

    loading,

    items,

    pagination,

    filters,

    fetchItems,

    deleteItem,

    search,

    resetFilters,

    changePage

} = useSubject()

filters.school_id = ''

const columns = computed(() => {
    const cols = [
        {
            key: 'id',
            label: 'ID'
        },
        {
            key: 'code',
            label: 'Code'
        },
        {
            key: 'name',
            label: 'Subject Name'
        },
    ]

    if (isSuperAdmin.value) {
        cols.push({
            key: 'school',
            label: 'School / Campus'
        })
    }

    cols.push(
        {
            key: 'type',
            label: 'Type'
        },
        {
            key: 'status',
            label: 'Status'
        },
        {
            key: 'actions',
            label: 'Action'
        }
    )

    return cols
})

async function loadSchools() {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get('/tenants')
            if (res.data?.data?.length) {
                allSchools.value = res.data.data
            }
        } catch (e) {}
    }
}

function onReset() {
    filters.school_id = ''
    resetFilters()
}

function handleTenantSwitched(e) {
    if (e?.detail?.school_id && e.detail.school_id !== 'all') {
        filters.school_id = Number(e.detail.school_id)
    } else {
        filters.school_id = ''
    }
    fetchItems()
}

const confirmDelete = async (id) => {

    const result = await Swal.fire({

        title: 'Delete Subject?',

        text: 'This action cannot be undone.',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonText: 'Delete'

    })

    if (!result.isConfirmed) {

        return

    }

    try {

        await deleteItem(id)

        Swal.fire(
            'Deleted!',
            'Subject deleted successfully.',
            'success'
        )

        fetchItems()

    } catch (error) {

        Swal.fire(
            'Error!',
            'Failed to delete subject.',
            'error'
        )

    }

}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile()
    }
    await Promise.all([
        loadSchools(),
        fetchItems()
    ])
    window.addEventListener('tenant-switched', handleTenantSwitched)
})

onBeforeUnmount(() => {
    window.removeEventListener('tenant-switched', handleTenantSwitched)
})
</script>
