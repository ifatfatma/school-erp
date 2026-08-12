<script setup>

import {
    onMounted,
    reactive,
    ref
} from "vue";

import {
    useRoute,
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


const route = useRoute();

const router = useRouter();

const loading = ref(false);

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


const load = async () => {

    loading.value = true;

    try {

        const { data } = await api.get(
            `/fee-structures/${route.params.id}`
        );


        const item = data.data;


        form.academic_session_id =
            item.academic_session_id ?? "";

        form.class_id =
            item.class_id ?? "";

        form.fee_type_id =
            item.fee_type_id ?? "";

        form.amount =
            item.amount ?? "";

        form.effective_from =
            item.effective_from ?? "";

        form.effective_to =
            item.effective_to ?? "";

        form.due_day =
            item.due_day ?? "";

        form.late_fee =
            item.late_fee ?? 0;

        form.status =
            Boolean(item.status);


    } catch (error) {

        console.error(
            "Fee structure load error:",
            error
        );

        alert(
            error.response?.data?.message ??
            "Unable to load fee structure."
        );

        router.push({
            name: "fee-structures.index"
        });

    } finally {

        loading.value = false;

    }

};


const update = async () => {

    saving.value = true;

    Object.keys(errors).forEach(
        key => delete errors[key]
    );


    try {

        await api.put(
            `/fee-structures/${route.params.id}`,
            form
        );


        router.push({
            name: "fee-structures.index"
        });


    } catch (error) {

        console.error(
            "Fee structure update error:",
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
                "Unable to update fee structure."
            );

        }

    } finally {

        saving.value = false;

    }

};


onMounted(() => {

    load();

});

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
                label: 'Edit'
            }
        ]"
    />


    <div class="card">

        <div class="card-header">

            <h4 class="mb-0">
                Edit Fee Structure
            </h4>

        </div>


        <div class="card-body">

            <div
                v-if="loading"
                class="text-center py-5"
            >

                <div class="spinner-border"></div>

                <div class="mt-2">
                    Loading...
                </div>

            </div>


            <template v-else>

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
                                name:
                                    'fee-structures.index'
                            })
                        "
                    />

                    <AppButton
                        text="Update"
                        :loading="saving"
                        @click="update"
                    />

                </div>

            </template>

        </div>

    </div>

</template>