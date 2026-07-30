<template>
    <div class="container-fluid">

        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Classes', to: { name: 'classes.index' } },
                { label: 'Create' }
            ]"
        />

        <div class="card">

            <div class="card-header">
                <h4 class="mb-0">Create Class</h4>
            </div>

            <div class="card-body">

                <ClassForm
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
                    @click="save"
                >

                    <span
                        v-if="loading"
                        class="spinner-border spinner-border-sm me-2"
                    ></span>

                    Save

                </button>

            </div>

        </div>

    </div>
</template>

<script setup>

import { reactive } from 'vue'
import { useRouter } from 'vue-router'

import useClass from '@/composables/useSchoolClass'

import ClassForm from '@/components/classes/ClassForm.vue'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const router = useRouter()

const {

    loading,

    errors,

    createItem

} = useClass()

const form = reactive({

    name: '',

    numeric_name: '',

    remarks: '',

    status: 1

})

async function save(){

    try{

        await createItem(form)

        alert('Class created successfully.')

        router.push({
            name:'classes.index'
        })

    }
    catch(error){

        console.log(error)

    }

}

</script>