<template>
    <div class="mb-3">
        <label v-if="label" class="form-label">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>

        <textarea
            :id="id"
            :name="name"
            :rows="rows"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :class="[
                'form-control',
                error ? 'is-invalid' : ''
            ]"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>

        <div v-if="error" class="invalid-feedback">
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
    placeholder: {
        type: String,
        default: ''
    },
    rows: {
        type: Number,
        default: 4
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