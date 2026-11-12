<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

import Form from "./Form.vue";

import { useGuardianStore } from "@/stores/guardian";
import { useStudentStore } from "@/stores/student";

const router = useRouter();

const guardianStore = useGuardianStore();
const studentStore = useStudentStore();

const form = reactive({

    school_id: localStorage.getItem("school_id") ?? "",

    guardian_type: "Father",

    first_name: "",

    last_name: "",

    gender: "",

    dob: "",

    mobile: "",

    alternate_mobile: "",

    email: "",

    occupation: "",

    annual_income: "",

    education: "",

    aadhaar_no: "",

    pan_no: "",

    relation: "",

    address: "",

    city: "",

    state: "",

    country: "India",

    pincode: "",

    student_ids: [],

    primary_student_id: "",

    photo: null,

    status: 1,

});

onMounted(async () => {

    guardianStore.clearErrors();

    await studentStore.fetch({

        per_page: 1000,

        status: 1,

    });

});

async function submit() {

    try {

        await guardianStore.create(form);

        Swal.fire({
            icon: "success",
            title: "Created!",
            text: "Guardian created successfully.",
            timer: 1500,
            showConfirmButton: false,
        });

        router.push({
            name: "guardians.index",
        });

    } catch (error) {

        console.error(error);
        Swal.fire({
            icon: "error",
            title: "Creation Failed",
            text: error.response?.data?.message || "Please check the form for errors.",
        });

    }

}

function cancel() {

    router.push({
        name: "guardians.index",
    });

}
</script>

<template>

<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            <Form

                :form="form"

                :students="studentStore.items"

                :errors="guardianStore.errors"

                :loading="guardianStore.saving"

                @submit="submit"

                @cancel="cancel"

            />

        </div>

    </div>

</div>

</template>