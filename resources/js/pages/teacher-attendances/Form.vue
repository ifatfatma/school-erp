<script setup>
import { computed } from "vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    teachers: {
        type: Array,
        default: () => [],
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    "submit",
    "cancel",
]);

const statuses = computed(() => [
    "Present",
    "Absent",
    "Late",
    "Half Day",
    "Leave",
]);
</script>

<template>
    <form @submit.prevent="$emit('submit')">

        <div class="row">

            <!-- Teacher -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Teacher
                    <span class="text-danger">*</span>
                </label>

                <select
                    v-model="form.teacher_id"
                    class="form-select"
                >
                    <option value="">Select Teacher</option>

                    <option
                        v-for="teacher in teachers"
                        :key="teacher.id"
                        :value="teacher.id"
                    >
                        {{ teacher.name }}
                    </option>
                </select>

                <div
                    v-if="errors.teacher_id"
                    class="text-danger mt-1"
                >
                    {{ errors.teacher_id[0] }}
                </div>
            </div>

            <!-- Attendance Date -->
            <div class="col-md-6 mb-3">
                <label class="form-label">
                    Attendance Date
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="date"
                    class="form-control"
                    v-model="form.attendance_date"
                />

                <div
                    v-if="errors.attendance_date"
                    class="text-danger mt-1"
                >
                    {{ errors.attendance_date[0] }}
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
                >
                    <option
                        v-for="status in statuses"
                        :key="status"
                        :value="status"
                    >
                        {{ status }}
                    </option>
                </select>

                <div
                    v-if="errors.status"
                    class="text-danger mt-1"
                >
                    {{ errors.status[0] }}
                </div>
            </div>

            <!-- Check In -->
            <div class="col-md-3 mb-3">
                <label class="form-label">
                    Check In
                </label>

                <input
                    type="time"
                    class="form-control"
                    v-model="form.check_in"
                />

                <div
                    v-if="errors.check_in"
                    class="text-danger mt-1"
                >
                    {{ errors.check_in[0] }}
                </div>
            </div>

            <!-- Check Out -->
            <div class="col-md-3 mb-3">
                <label class="form-label">
                    Check Out
                </label>

                <input
                    type="time"
                    class="form-control"
                    v-model="form.check_out"
                />

                <div
                    v-if="errors.check_out"
                    class="text-danger mt-1"
                >
                    {{ errors.check_out[0] }}
                </div>
            </div>

            <!-- Remarks -->
            <div class="col-12 mb-3">
                <label class="form-label">
                    Remarks
                </label>

                <textarea
                    rows="4"
                    class="form-control"
                    v-model="form.remarks"
                ></textarea>

                <div
                    v-if="errors.remarks"
                    class="text-danger mt-1"
                >
                    {{ errors.remarks[0] }}
                </div>
            </div>

        </div>

        <div class="d-flex gap-2 mt-4">

            <button
                class="btn btn-primary"
                type="submit"
                :disabled="loading"
            >
                {{ loading ? "Saving..." : "Save" }}
            </button>

            <button
                type="button"
                class="btn btn-secondary"
                @click="$emit('cancel')"
            >
                Cancel
            </button>

        </div>

    </form>
</template>