<template>
    <Teleport to="body">
        <div
            v-if="modelValue"
            class="modal fade show d-block"
            tabindex="-1"
            @click.self="close"
        >
            <div :class="['modal-dialog', sizeClass, centered ? 'modal-dialog-centered' : '']">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <slot name="title">
                                {{ title }}
                            </slot>
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            @click="close"
                        ></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="showFooter"
                        class="modal-footer"
                    >
                        <slot name="footer">

                            <button
                                type="button"
                                class="btn btn-secondary"
                                @click="close"
                            >
                                {{ cancelText }}
                            </button>

                            <button
                                type="button"
                                class="btn btn-primary"
                                :disabled="loading"
                                @click="$emit('save')"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>

                                {{ saveText }}
                            </button>

                        </slot>
                    </div>

                </div>
            </div>
        </div>

        <div
            v-if="modelValue"
            class="modal-backdrop fade show"
        ></div>
    </Teleport>
</template>

<script setup>
import { computed, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },

    title: {
        type: String,
        default: "",
    },

    size: {
        type: String,
        default: "md", // sm | md | lg | xl
    },

    centered: {
        type: Boolean,
        default: true,
    },

    showFooter: {
        type: Boolean,
        default: true,
    },

    loading: {
        type: Boolean,
        default: false,
    },

    saveText: {
        type: String,
        default: "Save",
    },

    cancelText: {
        type: String,
        default: "Cancel",
    },

    closeOnEsc: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits([
    "update:modelValue",
    "close",
    "save",
]);

const sizeClass = computed(() => {
    switch (props.size) {
        case "sm":
            return "modal-sm";
        case "lg":
            return "modal-lg";
        case "xl":
            return "modal-xl";
        default:
            return "";
    }
});

function close() {
    emit("update:modelValue", false);
    emit("close");
}

function handleEscape(event) {
    if (props.closeOnEsc && event.key === "Escape") {
        close();
    }
}

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            document.body.classList.add("modal-open");
            document.body.style.overflow = "hidden";
            window.addEventListener("keydown", handleEscape);
        } else {
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            window.removeEventListener("keydown", handleEscape);
        }
    },
    { immediate: true }
);
</script>