<script setup>
import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

const props = defineProps({
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

        <!-- Grade Name -->
        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.name"
                label="Grade"
                placeholder="Example: A+"
                :error="errors.name?.[0]"
            />

        </div>

        <!-- Grade Point -->
        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.grade_point"
                label="Grade Point"
                type="number"
                step="0.01"
                min="0"
                max="20"
                placeholder="Example: 10"
                :error="errors.grade_point?.[0]"
            />

        </div>

        <!-- Minimum Percentage -->
        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.min_percentage"
                label="Minimum Percentage"
                type="number"
                step="0.01"
                min="0"
                max="100"
                placeholder="Example: 90"
                :error="errors.min_percentage?.[0]"
            />

        </div>

        <!-- Maximum Percentage -->
        <div class="col-md-6 mb-3">

            <AppInput
                v-model="form.max_percentage"
                label="Maximum Percentage"
                type="number"
                step="0.01"
                min="0"
                max="100"
                placeholder="Example: 100"
                :error="errors.max_percentage?.[0]"
            />

        </div>

        <!-- Result -->
        <div class="col-md-6 mb-3">

            <AppSelect
                v-model="form.is_pass"
                label="Result"
                :options="[
                    {
                        id: true,
                        name: 'Pass'
                    },
                    {
                        id: false,
                        name: 'Fail'
                    }
                ]"
                option-label="name"
                option-value="id"
                :error="errors.is_pass?.[0]"
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
                rows="3"
                placeholder="Grade description"
            ></textarea>

            <div
                v-if="errors.description?.[0]"
                class="text-danger small mt-1"
            >
                {{ errors.description[0] }}
            </div>

        </div>

    </div>

</template>