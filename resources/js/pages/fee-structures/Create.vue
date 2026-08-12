<script setup>

import {
    reactive,
    ref
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

const saving = ref(false);

const errors = reactive({});


const form = reactive({

    academic_session_id: "",

    class_id: "",

    fee_type_id: "",

    amount: "",

    effective_from: "",

    effective_to: "",

    due_day: "",

    late_fee: 0,

    status: true,

});


const save = async () => {

    saving.value = true;

    Object.keys(errors).forEach(
        key => delete errors[key]
    );


    try {

        await api.post(
            "/fee-structures",
            form
        );


        router.push({
            name: "fee-structures.index"
        });


    } catch (error) {

        console.error(
            "Fee structure save error:",
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
                "Unable to save fee structure."
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
                label: 'Fee Structures',
                to: {
                    name: 'fee-structures.index'
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
                Create Fee Structure
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
                            name: 'fee-structures.index'
                        })
                    "
                />

                <AppButton
                    text="Save"
                    :loading="saving"
                    @click="save"
                />

            </div>

        </div>

    </div>

</template>