<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useLeaveTypeStore } from "@/stores/leaveType";

const route = useRoute();
const router = useRouter();
const store = useLeaveTypeStore();

const loading = ref(true);
const item = ref({});

const load = async () => {
    loading.value = true;

    try {
        item.value = await store.get(route.params.id);
    } finally {
        loading.value = false;
    }
};

onMounted(load);
</script>

<template>

    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Leave Type Details
                </h5>

                <button
                    class="btn btn-secondary"
                    @click="router.back()"
                >
                    Back
                </button>

            </div>

            <div
                class="card-body"
                v-if="!loading"
            >

                <table class="table table-bordered">

                    <tr>
                        <th width="220">Leave Type</th>
                        <td>{{ item.name }}</td>
                    </tr>

                    <tr>
                        <th>Description</th>
                        <td>{{ item.description || "-" }}</td>
                    </tr>

                    <tr>
                        <th>Maximum Days</th>
                        <td>{{ item.max_days }}</td>
                    </tr>

                    <tr>
                        <th>Paid Leave</th>
                        <td>

                            <span
                                class="badge"
                                :class="item.is_paid ? 'bg-success' : 'bg-warning'"
                            >
                                {{ item.is_paid_text }}
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>

                            <span
                                class="badge"
                                :class="item.status ? 'bg-success' : 'bg-danger'"
                            >
                                {{ item.status_text }}
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ item.created_at }}</td>
                    </tr>

                    <tr>
                        <th>Updated At</th>
                        <td>{{ item.updated_at }}</td>
                    </tr>

                </table>

            </div>

            <div
                v-else
                class="card-body text-center py-5"
            >
                Loading...
            </div>

        </div>

    </div>

</template>