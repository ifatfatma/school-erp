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


const form = reactive({

    name: "",

    code: "",

    frequency: "Monthly",

    description: "",

    status: true,

});


const errors = reactive({});

const loading = ref(false);

const saving = ref(false);


/*
|--------------------------------------------------------------------------
| Load
|--------------------------------------------------------------------------
*/

const load = async () => {

    loading.value = true;


    try {

        const { data } = await api.get(
            `/fee-types/${route.params.id}`
        );


        const item = data.data;


        form.name =
            item.name ?? "";

        form.code =
            item.code ?? "";

        form.frequency =
            item.frequency ?? "Monthly";

        form.description =
            item.description ?? "";

        form.status =
            Boolean(item.status);


    } catch (error) {

        console.error(
            "Fee type load error:",
            error
        );

        alert(
            error.response?.data?.message ??
            "Unable to load fee type."
        );


        router.push({
            name: "fee-types.index"
        });

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

const update = async () => {

    saving.value = true;


    Object.keys(errors).forEach(
        key => delete errors[key]
    );


    try {

        await api.put(
            `/fee-types/${route.params.id}`,
            form
        );


        router.push({
            name: "fee-types.index"
        });


    } catch (error) {

        console.error(
            "Fee type update error:",
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
                "Unable to update fee type."
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
                label: 'Fee Types',
                to: {
                    name: 'fee-types.index'
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
                Edit Fee Type
            </h4>

        </div>


        <div class="card-body">

            <div
                v-if="loading"
                class="text-center py-5"
            >

                <div class="spinner-border"></div>

                <div class="mt-2 text-muted">
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
                                name: 'fee-types.index'
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