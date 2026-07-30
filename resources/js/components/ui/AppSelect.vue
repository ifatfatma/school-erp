<template>
    <div class="mb-3">
        <label v-if="label" class="form-label">
            {{ label }}
        </label>

        <select
            class="form-select"
            :value="modelValue"
            @change="updateValue"
        >
            <option value="">
                {{ placeholder }}
            </option>

            <option
                v-for="option in options"
                :key="option[optionValue]"
                :value="option[optionValue]"
            >
                {{ option[optionLabel] }}
            </option>
        </select>

        <small
            v-if="error"
            class="text-danger"
        >
            {{ error }}
        </small>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: [String, Number, Boolean],

    label: String,

    placeholder: {
        type: String,
        default: "Select",
    },

    error: String,

    options: {
        type: Array,
        default: () => [],
    },

    optionLabel: {
        type: String,
        default: "label",
    },

    optionValue: {
        type: String,
        default: "value",
    },
});

const emit = defineEmits(["update:modelValue"]);

function updateValue(event) {
    let value = event.target.value;

    if (value === "true") value = true;
    else if (value === "false") value = false;
    else if (!isNaN(value) && value !== "") value = Number(value);

    emit("update:modelValue", value);
}
</script>