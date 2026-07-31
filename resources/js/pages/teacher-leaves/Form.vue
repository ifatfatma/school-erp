<script setup>
import { computed, watch } from "vue";

const props = defineProps({
    form: Object,
    errors: {
        type: Object,
        default: () => ({})
    },
    teachers: {
        type: Array,
        default: () => []
    },
    leaveTypes: {
        type: Array,
        default: () => []
    },
    isEdit: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(["submit"]);

const calculateDays = () => {

    if (!props.form.from_date || !props.form.to_date) {
        props.form.total_days = 0;
        return;
    }

    const from = new Date(props.form.from_date);
    const to = new Date(props.form.to_date);

    const diff = Math.ceil(
        (to - from) / (1000 * 60 * 60 * 24)
    );

    props.form.total_days = diff >= 0 ? diff + 1 : 0;
};

watch(
    () => [props.form.from_date, props.form.to_date],
    calculateDays
);

const submit = () => {
    emit("submit");
};
</script>

<template>

<form @submit.prevent="submit">

    <div class="row">

        <!-- Teacher -->

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Teacher
            </label>

            <select
                class="form-select"
                v-model="form.teacher_id"
            >
                <option value="">
                    Select Teacher
                </option>

                <option
                    v-for="teacher in teachers"
                    :key="teacher.id"
                    :value="teacher.id"
                >
                    {{ teacher.name }}
                </option>

            </select>

            <div class="text-danger small">
                {{ errors.teacher_id?.[0] }}
            </div>

        </div>

        <!-- Leave Type -->

        <div class="col-md-6 mb-3">

            <label class="form-label">
                Leave Type
            </label>

            <select
                class="form-select"
                v-model="form.leave_type_id"
            >
                <option value="">
                    Select Leave Type
                </option>

                <option
                    v-for="type in leaveTypes"
                    :key="type.id"
                    :value="type.id"
                >
                    {{ type.name }}
                </option>

            </select>

            <div class="text-danger small">
                {{ errors.leave_type_id?.[0] }}
            </div>

        </div>

        <!-- From -->

        <div class="col-md-3 mb-3">

            <label class="form-label">
                From Date
            </label>

            <input
                type="date"
                class="form-control"
                v-model="form.from_date"
            >

            <div class="text-danger small">
                {{ errors.from_date?.[0] }}
            </div>

        </div>

        <!-- To -->

        <div class="col-md-3 mb-3">

            <label class="form-label">
                To Date
            </label>

            <input
                type="date"
                class="form-control"
                v-model="form.to_date"
            >

            <div class="text-danger small">
                {{ errors.to_date?.[0] }}
            </div>

        </div>

        <!-- Total Days -->

        <div class="col-md-2 mb-3">

            <label class="form-label">
                Total Days
            </label>

            <input
                class="form-control"
                v-model="form.total_days"
                readonly
            >

        </div>

        <!-- Attachment -->

        <div class="col-md-4 mb-3">

            <label class="form-label">
                Attachment
            </label>

            <input
                type="file"
                class="form-control"
                @change="form.attachment = $event.target.files[0]"
            >

            <div class="text-danger small">
                {{ errors.attachment?.[0] }}
            </div>

        </div>

        <!-- Reason -->

        <div class="col-md-12 mb-3">

            <label class="form-label">
                Reason
            </label>

            <textarea
                rows="4"
                class="form-control"
                v-model="form.reason"
            ></textarea>

            <div class="text-danger small">
                {{ errors.reason?.[0] }}
            </div>

        </div>

        <!-- Edit Only -->

        <template v-if="isEdit">

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Status
                </label>

                <select
                    class="form-select"
                    v-model="form.status"
                >
                    <option :value="0">Pending</option>
                    <option :value="1">Approved</option>
                    <option :value="2">Rejected</option>
                    <option :value="3">Cancelled</option>
                </select>

            </div>

            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Remarks
                </label>

                <textarea
                    rows="3"
                    class="form-control"
                    v-model="form.remarks"
                ></textarea>

            </div>

        </template>

    </div>

    <button
        class="btn btn-primary"
        type="submit"
    >
        Save
    </button>

</form>

</template>