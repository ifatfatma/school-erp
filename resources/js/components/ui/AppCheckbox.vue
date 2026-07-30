<template>
    <div class="form-check">
        <input
            :id="id"
            :name="name"
            type="checkbox"
            class="form-check-input"
            :checked="modelValue"
            :disabled="disabled"
            @change="$emit('update:modelValue', $event.target.checked)"
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
        type: Boolean,
        default: false
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