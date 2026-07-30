<template>
    <div class="mb-3">
        <label v-if="label" class="form-label">
            {{ label }}
        </label>

        <input
            :type="type"
            class="form-control"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <small v-if="error" class="text-danger">
            {{ error }}
        </small>
    </div>
</template>

<script setup>
defineProps({
    modelValue: [String, Number],
    label: String,
    placeholder: String,
    error: String,
    type: {
        type: String,
        default: "text",
    },
});

defineEmits(["update:modelValue"]);
</script>