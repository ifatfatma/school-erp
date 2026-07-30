<template>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ title }}</h5>

        <slot name="header-action"></slot>
    </div>

    <div class="card-body">
        <slot></slot>
    </div>

    <div
        v-if="$slots.footer"
        class="card-footer"
    >
        <slot name="footer"></slot>
    </div>
</div>
</template>

<script setup>
defineProps({
    title: String,
});
</script>