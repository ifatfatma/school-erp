<template>

            <div class="row">

                <!-- Class Name -->
                <div class="col-md-6 mb-3">
                    <AppInput
                        label="Class Name"
                        v-model="form.name"
                        placeholder="Enter class name"
                        :required="true"
                        :error="errors.name ? errors.name[0] : ''"
                    />
                </div>

                <!-- Numeric Name -->
                <div class="col-md-6 mb-3">
                    <AppInput
                        label="Numeric Name"
                        type="number"
                        v-model="form.numeric_name"
                        placeholder="Example: 1"
                        :error="errors.numeric_name ? errors.numeric_name[0] : ''"
                    />
                </div>

                <!-- Remarks -->
                <div class="col-md-12 mb-3">
                    <AppTextarea
                        label="Remarks"
                        :rows="4"
                        v-model="form.remarks"
                        placeholder="Remarks..."
                        :error="errors.remarks ? errors.remarks[0] : ''"
                    />
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        class="form-select"
                        v-model="form.status"
                    >
                        <option :value="1">
                            Active
                        </option>

                        <option :value="0">
                            Inactive
                        </option>

                    </select>

                    <div
                        class="text-danger small mt-1"
                        v-if="errors.status"
                    >
                        {{ errors.status[0] }}
                    </div>

                </div>

            </div>

    
</template>

<script setup>

import AppInput from '@/components/ui/AppInput.vue'
import AppTextarea from '@/components/ui/AppTextarea.vue'

defineProps({

    form: {
        type: Object,
        required: true
    },

    errors: {
        type: Object,
        default: () => ({})
    }

})

</script>