<template>
    <div class="empty-state text-center py-5">

        <div class="empty-icon mb-3">
            <slot name="icon">
                <i :class="icon"></i>
            </slot>
        </div>

        <h4 class="fw-bold mb-2">
            {{ title }}
        </h4>

        <p class="text-muted mb-4">
            {{ description }}
        </p>

        <slot>
            <button
                v-if="buttonText"
                class="btn btn-primary"
                @click="$emit('action')"
            >
                {{ buttonText }}
            </button>
        </slot>

    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: 'Nothing here yet'
    },

    description: {
        type: String,
        default: 'There is no data available.'
    },

    icon: {
        type: String,
        default: 'bi bi-inbox'
    },

    buttonText: {
        type: String,
        default: ''
    }
});

defineEmits(['action']);
</script>

<style scoped>
.empty-state {
    border: 1px dashed #dee2e6;
    border-radius: 12px;
    background: #fafafa;
    padding: 40px 20px;
}

.empty-icon {
    font-size: 60px;
    color: #6c757d;
}

.empty-state h4 {
    color: #343a40;
}

.empty-state p {
    max-width: 450px;
    margin: auto;
}
</style>