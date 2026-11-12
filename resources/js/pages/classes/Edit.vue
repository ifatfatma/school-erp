<template>
    <div class="container-fluid">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Classes', to: { name: 'classes.index' } },
                { label: 'Edit Class' }
            ]"
        />

        <div class="card">

            <div class="card-header">
                <h4 class="mb-0">Edit Class</h4>
            </div>

            <div class="card-body">

                <div
                    v-if="loading"
                    class="text-center py-5"
                >
                    <div class="spinner-border text-primary"></div>
                </div>

                <ClassForm
                    v-else
                    :form="form"
                    :errors="errors"
                />

            </div>

            <div class="card-footer text-end">

                <RouterLink
                    :to="{ name: 'classes.index' }"
                    class="btn btn-secondary me-2"
                >
                    Cancel
                </RouterLink>

                <button
                    class="btn btn-primary"
                    :disabled="loading"
                    @click="update"
                >

                    <span
                        v-if="loading"
                        class="spinner-border spinner-border-sm me-2"
                    ></span>

                    Update

                </button>

            </div>

        </div>

    </div>
</template>

<script setup>

import { reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

import useClass from '@/composables/useSchoolClass'

import ClassForm from '@/components/classes/ClassForm.vue'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const route = useRoute()
const router = useRouter()

const {

    loading,

    errors,

    fetchItem,

    updateItem

} = useClass()

const form = reactive({

    name: '',

    numeric_name: '',

    remarks: '',

    status: 1

})

async function loadClass() {

    try {

        const res = await fetchItem(route.params.id)
        const data = res?.data || res
        form.name = data.name
        form.numeric_name = data.numeric_name
        form.remarks = data.remarks
        form.status = data.status ? 1 : 0;

    } catch (error) {

        console.error(error)

    }

}

async function update() {

    try {

        await updateItem(route.params.id, form)

        await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Class updated successfully.'
        })

        router.push({
            name: 'classes.index'
        })

    } catch (error) {

        console.error(error)

    }

}

onMounted(() => {

    loadClass()

})

</script>