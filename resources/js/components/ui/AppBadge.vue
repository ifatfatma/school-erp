<template>
    <span
        :class="badgeClass"
        :style="customStyle"
    >
        <i
            v-if="icon"
            :class="[icon, 'me-1']"
        ></i>

        <slot>
            {{ text }}
        </slot>
    </span>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    text: {
        type: String,
        default: "",
    },

    type: {
        type: String,
        default: "primary",
        validator: (value) => [
            "primary",
            "secondary",
            "success",
            "danger",
            "warning",
            "info",
            "light",
            "dark",
        ].includes(value),
    },

    pill: {
        type: Boolean,
        default: false,
    },

    icon: {
        type: String,
        default: "",
    },

    bgColor: {
        type: String,
        default: "",
    },

    textColor: {
        type: String,
        default: "",
    },
});

const badgeClass = computed(() => [
    "badge",
    `bg-${props.type}`,
    {
        "rounded-pill": props.pill,
    },
]);

const customStyle = computed(() => ({
    backgroundColor: props.bgColor || undefined,
    color: props.textColor || undefined,
}));
</script>