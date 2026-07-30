<script setup>
import { ref, watch } from "vue";

import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";
import AppButton from "@/components/ui/AppButton.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true,
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

const emit = defineEmits(["submit"]);

const photoPreview = ref(
    props.form.photo && typeof props.form.photo === "string"
        ? props.form.photo
        : null
);

watch(
    () => props.form.photo,
    (file) => {
        if (file instanceof File) {
            photoPreview.value = URL.createObjectURL(file);
        } else if (typeof file === "string") {
            photoPreview.value = file;
        }
    }
);

const genderOptions = [
    { id: "Male", name: "Male" },
    { id: "Female", name: "Female" },
    { id: "Other", name: "Other" },
];

const statusOptions = [
    { id: 1, name: "Active" },
    { id: 0, name: "Inactive" },
];

function onFileChange(e) {
    const file = e.target.files[0];

    if (file) {
        props.form.photo = file;
    }
}
</script>

<template>
    <form @submit.prevent="emit('submit')">

        <div class="row">

            <div class="col-md-6 mb-3">
                <AppInput
                    label="Employee ID"
                    v-model="form.employee_id"
                    :error="errors.employee_id?.[0]"
                    required
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppSelect
                    label="Status"
                    v-model="form.status"
                    :options="statusOptions"
                    option-label="name"
                    option-value="id"
                    :error="errors.status?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    label="First Name"
                    v-model="form.first_name"
                    :error="errors.first_name?.[0]"
                    required
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    label="Last Name"
                    v-model="form.last_name"
                    :error="errors.last_name?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppSelect
                    label="Gender"
                    v-model="form.gender"
                    :options="genderOptions"
                    option-label="name"
                    option-value="id"
                    :error="errors.gender?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    type="date"
                    label="Date of Birth"
                    v-model="form.date_of_birth"
                    :error="errors.date_of_birth?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    label="Phone"
                    v-model="form.phone"
                    :error="errors.phone?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    type="email"
                    label="Email"
                    v-model="form.email"
                    :error="errors.email?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    label="Qualification"
                    v-model="form.qualification"
                    :error="errors.qualification?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    type="number"
                    label="Experience (Years)"
                    v-model="form.experience"
                    :error="errors.experience?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <AppInput
                    type="date"
                    label="Joining Date"
                    v-model="form.joining_date"
                    :error="errors.joining_date?.[0]"
                />
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Photo</label>

                <input
                    type="file"
                    class="form-control"
                    accept="image/*"
                    @change="onFileChange"
                />

                <div
                    v-if="errors.photo"
                    class="text-danger mt-1"
                >
                    {{ errors.photo[0] }}
                </div>

                <div
                    v-if="photoPreview"
                    class="mt-2"
                >
                    <img
                        :src="photoPreview"
                        class="img-thumbnail"
                        style="max-width:150px"
                    />
                </div>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">
                    Address
                </label>

                <textarea
                    v-model="form.address"
                    class="form-control"
                    rows="4"
                ></textarea>

                <div
                    v-if="errors.address"
                    class="text-danger mt-1"
                >
                    {{ errors.address[0] }}
                </div>
            </div>

        </div>

        <AppButton
            type="submit"
            :loading="loading" 
            text="Save Teacher"
        >
            Save Teacher
        </AppButton>

    </form>
</template>