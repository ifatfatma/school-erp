<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";

import Form from "./Form.vue";
import { useExamScheduleStore } from "@/stores/examSchedule";

const router = useRouter();
const scheduleStore = useExamScheduleStore();

const form = reactive({

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

async function save() {

    try {

        await scheduleStore.create(form);

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

    @submit="save"

    @cancel="cancel"

/>

</template>