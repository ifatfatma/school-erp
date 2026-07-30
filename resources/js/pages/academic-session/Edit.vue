<template>
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Edit Academic Session</h3>

            <RouterLink
                :to="{ name: 'academic-sessions.index' }"
                class="btn btn-secondary"
            >
                Back
            </RouterLink>
        </div>

        <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>

        <Form
            v-else
            :form="form"
            @submit="update"
        />

    </div>
</template>

<script setup>
import { reactive, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

import Form from "./Form.vue";
import { useAcademicSessionStore } from "@/stores/academicSession";

const route = useRoute();
const router = useRouter();

const store = useAcademicSessionStore();

const loading = ref(true);

const form = reactive({
    school_id: "",
    name: "",
    start_date: "",
    end_date: "",
    status: true,
    is_current: false,
});

onMounted(async () => {

    try {

        const session = await store.find(route.params.id);

        form.school_id = session.school_id;
        form.name = session.name;
        form.start_date = session.start_date;
        form.end_date = session.end_date;
        form.status = session.status;
        form.is_current = session.is_current;

    } finally {

        loading.value = false;

    }

});

async function update() {

    try {

        await store.update(route.params.id, form);

        alert("Academic Session updated successfully.");

        router.push({
            name: "academic-sessions.index"
        });

    } catch (error) {

        console.error(error);

        alert("Unable to update Academic Session.");

    }

}
</script>