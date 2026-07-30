<template>
    <div class="text-center py-5">
        <div class="mb-3">
            <i :class="icon" style="font-size: 3rem;"></i>
        </div>

        <h5 class="mb-2">
            {{ title }}
        </h5>

        <p class="text-muted mb-3">
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
        default: 'No Data Found'
    },

    description: {
        type: String,
        default: 'There are no records to display.'
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