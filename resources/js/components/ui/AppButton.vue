<script setup>
defineProps({
    text: {
        type: String,
        default: "",
    },
    icon: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: "primary",
    },
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        class="btn"
        :class="'btn-' + color"
        :disabled="loading"
    >
        <i
            v-if="icon"
            :class="icon"
            class="me-2"
        ></i>

        <span v-if="!loading">{{ text }}</span>

        <span v-else>
            <span class="spinner-border spinner-border-sm me-2"></span>
            Loading...
        </span>
    </button>
</template>