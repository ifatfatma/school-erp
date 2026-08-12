<script setup>

import {
    reactive
} from "vue";

import {
    useRouter
} from "vue-router";

import AppBreadcrumb
    from "@/components/ui/AppBreadcrumb.vue";

import AppButton
    from "@/components/ui/AppButton.vue";

import Form
    from "./Form.vue";

import api
    from "@/api/axios";


const router = useRouter();


const form = reactive({

    name: "",

    code: "",

    frequency: "Monthly",

    description: "",

    status: true,

});


const errors = reactive({});

const saving = reactive({
    value: false
});


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const save = async () => {

    saving.value = true;


    Object.keys(errors).forEach(
        key => delete errors[key]
    );


    try {

        await api.post(
            "/fee-types",
            form
        );


        router.push({
            name: "fee-types.index"
        });


    } catch (error) {

        console.error(
            "Fee type save error:",
            error
        );


        if (
            error.response?.status === 422
        ) {

            Object.assign(
                errors,
                error.response.data.errors ?? {}
            );

        } else {

            alert(
                error.response?.data?.message ??
                "Unable to save fee type."
            );

        }

    } finally {

        saving.value = false;

    }

};

</script>


<template>

    <AppBreadcrumb
        :items="[
            {
                label: 'Dashboard',
                to: '/'
            },
            {
                label: 'Fee Types',
                to: {
                    name: 'fee-types.index'
                }
            },
            {
                label: 'Create'
            }
        ]"
    />


    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Create Fee Type
            </h4>

        </div>


        <div class="card-body">

            <Form
                :form="form"
                :errors="errors"
            />


            <div class="text-end mt-4">

                <AppButton
                    text="Cancel"
                    class="btn-secondary me-2"
                    @click="
                        router.push({
                            name: 'fee-types.index'
                        })
                    "
                />

                <AppButton
                    text="Save"
                    :loading="saving.value"
                    @click="save"
                />

            </div>

        </div>

    </div>

</template>