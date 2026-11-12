<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";

import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useAcademicSessionStore } from "@/stores/academicSession";
import { useSectionStore } from "@/stores/section";
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const router = useRouter();

const studentStore = useStudentStore();
const classStore = useClassStore();
const AcademicSessionStore = useAcademicSessionStore();
const sectionStore = useSectionStore();

const loading = ref(false);

const form = reactive({

    admission_no: "",

    roll_no: "",

    first_name: "",

    last_name: "",

    gender: "",

    dob: "",

    admission_date: new Date().toISOString().substring(0,10),

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

    status: true,

});

onMounted(async () => {

    await Promise.all([

        classStore.fetch({ per_page: 1000 }),
        AcademicSessionStore.load({ per_page: 1000 }),
        sectionStore.load({ per_page: 1000 }),

    ]);

    if (!form.academic_session_id && AcademicSessionStore.sessions.length > 0) {
        const currentSession = AcademicSessionStore.sessions.find(s => s.is_current) || AcademicSessionStore.sessions[0];
        if (currentSession) {
            form.academic_session_id = currentSession.id;
        }
    }

});

async function submit() {

    loading.value = true;

    studentStore.errors = {};

    try {

        const fd = new FormData();

        Object.keys(form).forEach(key => {

            if (form[key] !== null) {

                fd.append(key, form[key]);

            }

        });

        await studentStore.create(fd);

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
<AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Students', to: { name: 'students.index' } },
                { label: 'Create' }
            ]"
        />

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