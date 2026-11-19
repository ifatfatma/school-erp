<template>
    <router-view />
    <!-- Global 24/7 School AI Support & WhatsApp Bot System -->
    <AiSupportBot />
</template>

<script setup>
import { onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import AiSupportBot from "@/components/common/AiSupportBot.vue";

const auth = useAuthStore();

onMounted(() => {
    auth.loadProfile();
});
</script>
