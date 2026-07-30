<template>
    <div class="form-check">
        <input
            :id="id"
            :name="name"
            type="radio"
            class="form-check-input"
            :value="value"
            :checked="modelValue === value"
            :disabled="disabled"
            @change="$emit('update:modelValue', value)"
        />

        <label
            v-if="label"
            class="form-check-label"
            :for="id"
        >
            {{ label }}
        </label>

        <div v-if="error" class="text-danger small mt-1">
            {{ error }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        default: null
    },
    value: {
        type: [String, Number, Boolean],
        required: true
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
        required: true
    },
    disabled: {
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