<script setup>
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

    Object.assign(form, item);

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

<Form

    :form="form"

    :errors="scheduleStore.errors"

    :loading="scheduleStore.saving"

    @submit="update"

    @cancel="cancel"

/>

</template>