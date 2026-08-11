<script setup>
import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

defineProps({
    form: {
        type: Object,
        required: true,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>

    <div class="row">

        <!-- Grade -->
        <div class="col-md-4 mb-3">

            <AppInput
                v-model="form.name"
                label="Grade"
                placeholder="A+"
                :error="errors.name?.[0]"
            />

        </div>

        <!-- Minimum -->
        <div class="col-md-4 mb-3">

            <AppInput
                v-model="form.min_percentage"
                label="Minimum Percentage"
                type="number"
                step="0.01"
                min="0"
                max="100"
                :error="errors.min_percentage?.[0]"
            />

        </div>

        <!-- Maximum -->
        <div class="col-md-4 mb-3">

            <AppInput
                v-model="form.max_percentage"
                label="Maximum Percentage"
                type="number"
                step="0.01"
                min="0"
                max="100"
                :error="errors.max_percentage?.[0]"
            />

        </div>

        <!-- Grade Point -->
        <div class="col-md-4 mb-3">

            <AppInput
                v-model="form.grade_point"
                label="Grade Point"
                type="number"
                step="0.01"
                min="0"
                :error="errors.grade_point?.[0]"
            />

        </div>

        <!-- Result -->
        <div class="col-md-4 mb-3">

            <AppSelect
                v-model="form.is_pass"
                label="Result"
                :options="[
                    { id: true, name: 'Pass' },
                    { id: false, name: 'Fail' }
                ]"
                option-label="name"
                option-value="id"
                :error="errors.is_pass?.[0]"
            />

        </div>

        <!-- Status -->
        <div class="col-md-4 mb-3">

            <AppSelect
                v-model="form.status"
                label="Status"
                :options="[
                    { id: true, name: 'Active' },
                    { id: false, name: 'Inactive' }
                ]"
                option-label="name"
                option-value="id"
                :error="errors.status?.[0]"
            />

        </div>

        <!-- Description -->
        <div class="col-md-12 mb-3">

            <AppInput
                v-model="form.description"
                label="Description"
                type="textarea"
                :error="errors.description?.[0]"
            />

        </div>

    </div>

</template>