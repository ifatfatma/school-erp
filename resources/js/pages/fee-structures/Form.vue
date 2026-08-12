<script setup>

import {
    onMounted,
    ref,
    watch
} from "vue";

import AppInput
    from "@/components/ui/AppInput.vue";

import AppSelect
    from "@/components/ui/AppSelect.vue";

import api
    from "@/api/axios";


const props = defineProps({

    form: {
        type: Object,
        required: true
    },

    errors: {
        type: Object,
        default: () => ({})
    }

});


const sessions = ref([]);

const classes = ref([]);

const feeTypes = ref([]);


/*
|--------------------------------------------------------------------------
| Load Sessions
|--------------------------------------------------------------------------
*/

const loadSessions = async () => {

    try {

        const { data } = await api.get(
            "/academic-sessions/active"
        );

        sessions.value =
            data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load academic sessions:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Classes
|--------------------------------------------------------------------------
*/

const loadClasses = async () => {

    try {

        const { data } = await api.get(
            "/classes/active"
        );

        classes.value =
            data.data ?? [];

    } catch (error) {

        console.error(
            "Failed to load classes:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Load Fee Types
|--------------------------------------------------------------------------
*/

const loadFeeTypes = async () => {

    try {

        const { data } = await api.get(
            "/fee-types",
            {
                params: {
                    status: 1,
                    per_page: 1000
                }
            }
        );

        feeTypes.value =
            data.data?.data ??
            data.data ??
            [];

    } catch (error) {

        console.error(
            "Failed to load fee types:",
            error
        );

    }

};


/*
|--------------------------------------------------------------------------
| Initialise
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadSessions();

    loadClasses();

    loadFeeTypes();

});

</script>


<template>

    <div class="row">

        <!-- Academic Session -->

        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.academic_session_id"
                label="Academic Session"
                :options="sessions"
                option-label="name"
                option-value="id"
                :error="
                    errors.academic_session_id?.[0]
                "
            />

        </div>


        <!-- Class -->

        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.class_id"
                label="Class"
                :options="classes"
                option-label="name"
                option-value="id"
                :error="
                    errors.class_id?.[0]
                "
            />

        </div>


        <!-- Fee Type -->

        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.fee_type_id"
                label="Fee Type"
                :options="feeTypes"
                option-label="name"
                option-value="id"
                :error="
                    errors.fee_type_id?.[0]
                "
            />

        </div>


        <!-- Amount -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.amount"
                label="Amount"
                type="number"
                min="0"
                step="0.01"
                :error="
                    errors.amount?.[0]
                "
            />

        </div>


        <!-- Effective From -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.effective_from"
                label="Effective From"
                type="date"
                :error="
                    errors.effective_from?.[0]
                "
            />

        </div>


        <!-- Effective To -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.effective_to"
                label="Effective To"
                type="date"
                :error="
                    errors.effective_to?.[0]
                "
            />

        </div>


        <!-- Due Day -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.due_day"
                label="Due Day"
                type="number"
                min="1"
                max="31"
                placeholder="Example: 10"
                :error="
                    errors.due_day?.[0]
                "
            />

        </div>


        <!-- Late Fee -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.late_fee"
                label="Late Fee"
                type="number"
                min="0"
                step="0.01"
                :error="
                    errors.late_fee?.[0]
                "
            />

        </div>


        <!-- Status -->

        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.status"
                label="Status"
                :options="[
                    {
                        id: true,
                        name: 'Active'
                    },
                    {
                        id: false,
                        name: 'Inactive'
                    }
                ]"
                option-label="name"
                option-value="id"
                :error="
                    errors.status?.[0]
                "
            />

        </div>

    </div>

</template>