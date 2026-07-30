<template>
    <div class="mb-3">
        <label
            v-if="label"
            :for="id"
            class="form-label"
        >
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>

        <input
            :id="id"
            :name="name"
            type="date"
            class="form-control"
            :class="{ 'is-invalid': error }"
            :value="modelValue"
            :min="min"
            :max="max"
            :disabled="disabled"
            :readonly="readonly"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <div
            v-if="error"
            class="invalid-feedback"
        >
            {{ error }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    min: {
        type: String,
        default: ''
    },
    max: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: ''
    }
});

defineEmits(['update:modelValue']);
</script>