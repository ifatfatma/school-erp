<template>
    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Add Subject</h3>

            <RouterLink
                :to="{ name:'subjects.index' }"
                class="btn btn-secondary"
            >
                Back
            </RouterLink>
        </div>

        <div class="card">

            <div class="card-body">

                <form @submit.prevent="saveSubject">

                    <div class="row">

                        <!-- Subject Name -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Subject Name
                            </label>

                            <input
                                v-model="form.name"
                                class="form-control"
                                type="text"
                            >

                            <small
                                class="text-danger"
                                v-if="errors.name"
                            >
                                {{ errors.name[0] }}
                            </small>

                        </div>

                        <!-- Subject Code -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Subject Code
                            </label>

                            <input
                                v-model="form.code"
                                class="form-control"
                                type="text"
                            >

                            <small
                                class="text-danger"
                                v-if="errors.code"
                            >
                                {{ errors.code[0] }}
                            </small>

                        </div>

                        <!-- Type -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Subject Type
                            </label>

                            <select
                                v-model="form.type"
                                class="form-select"
                            >
                                <option value="Theory">Theory</option>
                                <option value="Practical">Practical</option>
                                <option value="Both">Both</option>
                            </select>

                            <small
                                class="text-danger"
                                v-if="errors.type"
                            >
                                {{ errors.type[0] }}
                            </small>

                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                v-model="form.status"
                                class="form-select"
                            >
                                <option :value="1">
                                    Active
                                </option>

                                <option :value="0">
                                    Inactive
                                </option>

                            </select>

                            <small
                                class="text-danger"
                                v-if="errors.status"
                            >
                                {{ errors.status[0] }}
                            </small>

                        </div>

                        <!-- Description -->

                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea
                                v-model="form.description"
                                class="form-control"
                                :rows="4"
                            ></textarea>

                            <small
                                class="text-danger"
                                v-if="errors.description"
                            >
                                {{ errors.description[0] }}
                            </small>

                        </div>

                    </div>

                    <hr>

                    <button
                        class="btn btn-primary"
                        :disabled="loading"
                    >
                        {{ loading ? 'Saving...' : 'Save Subject' }}
                    </button>

                    <RouterLink
                        :to="{name:'subjects.index'}"
                        class="btn btn-secondary ms-2"
                    >
                        Cancel
                    </RouterLink>

                </form>

            </div>

        </div>

    </div>
</template>

<script setup>

import { reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

import { useSubject } from '@/composables/useSubject'

const router = useRouter()

const loading = ref(false)

const {

    createItem,

    errors

} = useSubject()

const form = reactive({

    name: '',

    code: '',

    type: 'Theory',

    description: '',

    status: 1

})

const saveSubject = async () => {

    loading.value = true

    try{

        await createItem(form)

        await Swal.fire({

            icon:'success',

            title:'Success',

            text:'Subject created successfully.'

        })

        router.push({

            name:'subjects.index'

        })

    }catch(e){

        console.log(e)

    }finally{

        loading.value=false

    }

}

</script>