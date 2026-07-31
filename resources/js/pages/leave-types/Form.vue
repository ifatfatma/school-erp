<script setup>
import { computed } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["submit"]);

const submit = () => {
    emit("submit");
};

const getError = computed(() => (field) => {
    return props.errors?.[field]?.[0] ?? "";
});
</script>

<template>
    <form @submit.prevent="submit">

        <div class="row">

            <!-- Leave Type Name -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Leave Type <span class="text-danger">*</span>
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': getError('name') }"
                    placeholder="Enter Leave Type"
                />

                <div class="invalid-feedback">
                    {{ getError("name") }}
                </div>
            </div>

            <!-- Maximum Days -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Maximum Days <span class="text-danger">*</span>
                </label>

                <input
                    v-model="form.max_days"
                    type="number"
                    min="0"
                    class="form-control"
                    :class="{ 'is-invalid': getError('max_days') }"
                />

                <div class="invalid-feedback">
                    {{ getError("max_days") }}
                </div>
            </div>

            <!-- Description -->
            <div class="col-md-12 mb-3">
                <label class="form-label">
                    Description
                </label>

                <textarea
                    v-model="form.description"
                    rows="4"
                    class="form-control"
                    :class="{ 'is-invalid': getError('description') }"
                    placeholder="Description"
                ></textarea>

                <div class="invalid-feedback">
                    {{ getError("description") }}
                </div>
            </div>

            <!-- Paid Leave -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Paid Leave
                </label>

                <select
                    v-model="form.is_paid"
                    class="form-select"
                    :class="{ 'is-invalid': getError('is_paid') }"
                >
                    <option :value="true">Paid</option>
                    <option :value="false">Unpaid</option>
                </select>

                <div class="invalid-feedback">
                    {{ getError("is_paid") }}
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Status
                </label>

                <select
                    v-model="form.status"
                    class="form-select"
                    :class="{ 'is-invalid': getError('status') }"
                >
                    <option :value="true">Active</option>
                    <option :value="false">Inactive</option>
                </select>

                <div class="invalid-feedback">
                    {{ getError("status") }}
                </div>
            </div>

        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                Save
            </button>
        </div>

    </form>
</template>