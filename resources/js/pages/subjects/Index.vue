<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Subjects</h3>

            <RouterLink
                :to="{ name: 'subjects.create' }"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-circle"></i>
                Add Subject
            </RouterLink>
        </div>

        <!-- Search -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-5">

                        <input
                            v-model="filters.search"
                            @keyup.enter="search"
                            class="form-control"
                            placeholder="Search name or code..."
                        >

                    </div>

                    <div class="col-md-3">

                        <select
                            v-model="filters.status"
                            class="form-select"
                        >
                            <option value="">All Status</option>
                            <option :value="1">Active</option>
                            <option :value="0">Inactive</option>
                        </select>

                    </div>

                    <div class="col-md-4">

                        <button
                            class="btn btn-primary me-2"
                            @click="search"
                        >
                            Search
                        </button>

                        <button
                            class="btn btn-secondary"
                            @click="resetFilters"
                        >
                            Reset
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
                    >
                        <i class="bi bi-eye"></i>
                    </RouterLink>

                    <RouterLink
                        :to="{ name:'subjects.edit',params:{id:item.id} }"
                        class="btn btn-warning"
                    >
                        <i class="bi bi-pencil"></i>
                    </RouterLink>

                    <button
                        class="btn btn-danger"
                        @click="confirmDelete(item.id)"
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

import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Swal from 'sweetalert2'

import AppTable from '@/components/ui/AppTable.vue'
import AppPagination from '@/components/ui/AppPagination.vue'

import {useSubject} from '@/composables/useSubject'

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

const columns = [

    {
        key:'id',
        label:'ID'
    },

    {
        key:'code',
        label:'Code'
    },

    {
        key:'name',
        label:'Subject Name'
    },

    {
        key:'type',
        label:'Type'
    },

    {
        key:'status',
        label:'Status'
    },

    {
        key:'actions',
        label:'Action'
    }

]

const confirmDelete = async(id)=>{

    const result = await Swal.fire({

        title:'Delete Subject?',

        text:'This action cannot be undone.',

        icon:'warning',

        showCancelButton:true,

        confirmButtonText:'Delete'

    })

    if(!result.isConfirmed){

        return

    }

    await deleteItem(id)

    Swal.fire(

        'Deleted',

        'Subject deleted successfully.',

        'success'

    )

    fetchItems()

}

onMounted(()=>{

    fetchItems()

})

</script>