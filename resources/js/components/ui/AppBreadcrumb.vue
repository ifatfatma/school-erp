<template>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li
                v-for="(item, index) in items"
                :key="index"
                class="breadcrumb-item"
                :class="{ active: index === items.length - 1 }"
                :aria-current="index === items.length - 1 ? 'page' : null"
            >
                <RouterLink
                    v-if="item.to && index !== items.length - 1"
                    :to="item.to"
                >
                    {{ item.label }}
                </RouterLink>

                <span v-else>
                    {{ item.label }}
                </span>
            </li>
        </ol>
    </nav>
</template>

<script setup>
defineProps({
    items: {
        type: Array,
        default: () => []
    }
});
</script>