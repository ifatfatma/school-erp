<script setup>
import { reactive } from "vue";
import { useRouter } from "vue-router";
import { useLeaveTypeStore } from "@/stores/leaveType";
import Form from "./Form.vue";

const router = useRouter();
const store = useLeaveTypeStore();

const form = reactive({
    name: "",
    description: "",
    max_days: 0,
    is_paid: true,
    status: true,
});

const submit = async () => {
    try {

        await store.create(form);

        if (window.$toast) {
            window.$toast.success("Leave Type created successfully.");
        }

        router.push({ name: "leave-types.index" });

    } catch (e) {
        console.error(e);
    }
};
</script>

<template>
    <div class="container-fluid">

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">Create Leave Type</h5>
            </div>

            <div class="card-body">

                <Form
                    :form="form"
                    :errors="store.errors"
                    @submit="submit"
                />

            </div>

        </div>

    </div>
</template>