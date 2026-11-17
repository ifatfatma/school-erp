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
                    v-if="hasValidLink(item, index)"
                    :to="item.to"
                    class="text-decoration-none"
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
import { RouterLink, useRouter } from 'vue-router';

const props = defineProps({
    items: {
        type: Array,
        default: () => []
    }
});

const router = useRouter();

function hasValidLink(item, index) {
    if (!item.to || index === props.items.length - 1) {
        return false;
    }
    if (typeof item.to === 'string') {
        return true;
    }
    if (typeof item.to === 'object' && item.to.name) {
        return router && typeof router.hasRoute === 'function' ? router.hasRoute(item.to.name) : true;
    }
    return true;
}
</script>
