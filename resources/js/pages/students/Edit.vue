<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";

import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useAcademicSessionStore } from "@/stores/academicSession";

const router = useRouter();
const route = useRoute();

const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const AcademicSessionStore = useAcademicSessionStore();

const loading = ref(false);

const form = reactive({
    admission_no: "",
    roll_no: "",

    first_name: "",
    last_name: "",

    gender: "",

    dob: "",

    admission_date: "",

    blood_group: "",
    religion: "",
    category: "",

    aadhaar_no: "",

    mobile: "",
    email: "",
    address: "",

    class_id: "",
    section_id: "",
    academic_session_id: "",

    father_name: "",
    father_mobile: "",
    father_occupation: "",

    mother_name: "",
    mother_mobile: "",

    guardian_name: "",
    guardian_mobile: "",

    photo: null,
    photo_url: "",
    status: 1,
});

onMounted(async () => {

    loading.value = true;

    try {

        await Promise.all([
            classStore.fetch(),
            AcademicSessionStore.load(),
            sectionStore.load(),
            studentStore.find(route.params.id),
        ]);

        const item = studentStore.item;

        Object.assign(form, {
            admission_no: item.admission_no,
            roll_no: item.roll_no,

            first_name: item.first_name,
            last_name: item.last_name,

            gender: item.gender,

            dob: item.dob,

            admission_date: item.admission_date,

            blood_group: item.blood_group,
            religion: item.religion,
            category: item.category,

            aadhaar_no: item.aadhaar_no,

            mobile: item.mobile,
            email: item.email,
            address: item.address,

            class_id: item.class_id,
            academic_session_id: item.academic_session_id,
            section_id: item.section_id,

            father_name: item.father_name,
            father_mobile: item.father_mobile,
            father_occupation: item.father_occupation,

            mother_name: item.mother_name,
            mother_mobile: item.mother_mobile,

            guardian_name: item.guardian_name,
            guardian_mobile: item.guardian_mobile,

            photo_url: item.photo_url,

            status: item.status==true?1:0,
        });


    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

});

async function submit() {

    loading.value = true;

    studentStore.errors = {};

    try {

        const fd = new FormData();

        Object.keys(form).forEach(key => {

            if (key === "photo_url") {
                return;
            }

            if (form[key] !== null) {
                fd.append(key, form[key]);
            }

        });

        await studentStore.update(
            route.params.id,
            fd
        );

        router.push({
            name: "students.index"
        });

    } catch (e) {

    console.log("Status:", e.response?.status);
    console.log("Response:", e.response?.data);
    console.log("Errors:", e.response?.data?.errors);

    console.error(e);

} finally {

        loading.value = false;

    }

}

function cancel() {
    router.back();
}
</script>

<template>

<div class="container-fluid">

            <Form
                :form="form"
                :academic_session="AcademicSessionStore.sessions"
                :classes="classStore.items"
                :sections="sectionStore.items"
                :errors="studentStore.errors"
                :loading="loading"
                @submit="submit"
                @cancel="cancel"
            />

        </div>



</template>