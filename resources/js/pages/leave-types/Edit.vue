<script setup>
import { reactive, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useLeaveTypeStore } from "@/stores/leaveType";
import Form from "./Form.vue";

const route = useRoute();
const router = useRouter();
const store = useLeaveTypeStore();

const loading = reactive({
    page: true,
});

const form = reactive({
    name: "",
    description: "",
    max_days: 0,
    is_paid: true,
    status: true,
});

const load = async () => {
    loading.page = true;

    try {
        const data = await store.get(route.params.id);

        form.name = data.name;
        form.description = data.description;
        form.max_days = data.max_days;
        form.is_paid = data.is_paid;
        form.status = data.status;
    } finally {
        loading.page = false;
    }
};

const submit = async () => {
    try {

        await store.update(route.params.id, form);

        if (window.$toast) {
            window.$toast.success("Leave Type updated successfully.");
        }

        router.push({
            name: "leave-types.index",
        });

    } catch (e) {
        console.error(e);
    }
};

onMounted(load);
</script>

<template>

    <div class="container-fluid">

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    Edit Leave Type
                </h5>
            </div>

            <div class="card-body">

                <div
                    v-if="loading.page"
                    class="text-center py-5"
                >
                    Loading...
                </div>

                <Form
                    v-else
                    :form="form"
                    :errors="store.errors"
                    @submit="submit"
                />

            </div>

        </div>

    </div>

</template>