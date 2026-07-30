<template>
    <div class="position-relative">

        <div class="input-group">

            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>

            <input
                ref="searchInput"
                type="text"
                class="form-control"
                :placeholder="placeholder"
                :value="modelValue"
                @input="onInput"
                @keyup.enter="searchNow"
            />

            <button
                v-if="modelValue"
                class="btn btn-outline-secondary"
                type="button"
                @click="clearSearch"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>

        <div
            v-if="loading"
            class="position-absolute top-50 end-0 translate-middle-y pe-5"
        >
            <div
                class="spinner-border spinner-border-sm text-primary"
                role="status"
            ></div>
        </div>

    </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from "vue";

const props = defineProps({

    modelValue: {
        type: String,
        default: "",
    },

    placeholder: {
        type: String,
        default: "Search...",
    },

    debounce: {
        type: Number,
        default: 500,
    },

    loading: {
        type: Boolean,
        default: false,
    },

    autofocus: {
        type: Boolean,
        default: false,
    }

});

const emit = defineEmits([
    "update:modelValue",
    "search",
    "clear",
]);

const searchInput = ref(null);

let timer = null;

watch(
    () => props.autofocus,
    (value) => {
        if (value && searchInput.value) {
            searchInput.value.focus();
        }
    },
    { immediate: true }
);

function onInput(event) {

    const value = event.target.value;

    emit("update:modelValue", value);

    clearTimeout(timer);

    timer = setTimeout(() => {

        emit("search", value);

    }, props.debounce);

}

function searchNow() {

    clearTimeout(timer);

    emit("search", props.modelValue);

}

function clearSearch() {

    clearTimeout(timer);

    emit("update:modelValue", "");

    emit("clear");

    emit("search", "");

}

onBeforeUnmount(() => {

    clearTimeout(timer);

});
</script>