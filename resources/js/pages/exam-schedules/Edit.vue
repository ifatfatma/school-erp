<script setup>
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";
import { useExamScheduleStore } from "@/stores/examSchedule";

const route = useRoute();
const router = useRouter();

const scheduleStore = useExamScheduleStore();

const form = reactive({

    id: null,

    exam_id: "",

    class_id: "",

    section_id: "",

    subject_id: "",

    teacher_id: "",

    exam_date: "",

    start_time: "",

    end_time: "",

    room_no: "",

    full_marks: 100,

    pass_marks: 40,

    instructions: "",

    status: true,

});

onMounted(async () => {

    const item = await scheduleStore.find(route.params.id);

    if (item) {
        Object.assign(form, item);
        if (form.start_time && form.start_time.length > 5) {
            form.start_time = form.start_time.substring(0, 5);
        }
        if (form.end_time && form.end_time.length > 5) {
            form.end_time = form.end_time.substring(0, 5);
        }
    }

});

async function update() {

    try {

        await scheduleStore.update(form.id, form);

        router.push({
            name: "exam-schedules.index"
        });

    } catch (e) {

        console.error(e);

    }

}

function cancel() {

    router.push({
        name: "exam-schedules.index"
    });

}
</script>

<template>
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Exam Schedules', to: { name: 'exam-schedules.index' } },
            { label: 'Edit Schedule' }
        ]"
    />


<Form

    :form="form"

    :errors="scheduleStore.errors"

    :loading="scheduleStore.saving"

    @submit="update"

    @cancel="cancel"

/>

</template>