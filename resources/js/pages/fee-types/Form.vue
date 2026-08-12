<script setup>

import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

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

const frequencies = [

    {
        id: "One Time",
        name: "One Time"
    },

    {
        id: "Monthly",
        name: "Monthly"
    },

    {
        id: "Quarterly",
        name: "Quarterly"
    },

    {
        id: "Half Yearly",
        name: "Half Yearly"
    },

    {
        id: "Yearly",
        name: "Yearly"
    }

];

</script>


<template>

    <div class="row">

        <!-- Name -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.name"
                label="Fee Type Name"
                placeholder="e.g. Tuition Fee"
                :error="errors.name?.[0]"
            />

        </div>


        <!-- Code -->

        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.code"
                label="Code"
                placeholder="e.g. TUITION"
                :error="errors.code?.[0]"
            />

        </div>


        <!-- Frequency -->

        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.frequency"
                label="Frequency"
                :options="frequencies"
                option-label="name"
                option-value="id"
                :error="errors.frequency?.[0]"
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
                :error="errors.status?.[0]"
            />

        </div>


        <!-- Description -->

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Description
            </label>

            <textarea
                v-model="form.description"
                class="form-control"
                rows="4"
                placeholder="Enter description"
            ></textarea>

            <div
                v-if="errors.description"
                class="text-danger small mt-1"
            >
                {{ errors.description[0] }}
            </div>

        </div>

    </div>

</template>