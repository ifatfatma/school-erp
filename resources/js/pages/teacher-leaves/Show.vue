<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useLeaveRequestStore } from "@/stores/leaveRequest";

const route = useRoute();
const router = useRouter();
const store = useLeaveRequestStore();

const loading = ref(true);
const item = ref({});

const loadData = async () => {
    loading.value = true;

    try {
        item.value = await store.get(route.params.id);
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);
</script>

<template>

<div class="container-fluid">

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Leave Request Details
            </h5>

            <RouterLink
                :to="{ name:'teacher-leaves.index' }"
                class="btn btn-secondary"
            >
                Back
            </RouterLink>

        </div>

        <div
            class="card-body"
            v-if="!loading"
        >

            <table class="table table-bordered">

                <tbody>

                    <tr>
                        <th width="220">Teacher</th>
                        <td>{{ item.teacher?.name }}</td>
                    </tr>

                    <tr>
                        <th>Leave Type</th>
                        <td>{{ item.leave_type?.name }}</td>
                    </tr>

                    <tr>
                        <th>From Date</th>
                        <td>{{ item.from_date }}</td>
                    </tr>

                    <tr>
                        <th>To Date</th>
                        <td>{{ item.to_date }}</td>
                    </tr>

                    <tr>
                        <th>Total Days</th>
                        <td>{{ item.total_days }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>

                            <span
                                class="badge"
                                :class="{
                                    'bg-warning': item.status==0,
                                    'bg-success': item.status==1,
                                    'bg-danger': item.status==2,
                                    'bg-secondary': item.status==3
                                }"
                            >
                                {{ item.status_text }}
                            </span>

                        </td>
                    </tr>

                    <tr>
                        <th>Reason</th>
                        <td>{{ item.reason }}</td>
                    </tr>

                    <tr>
                        <th>Remarks</th>
                        <td>{{ item.remarks || '-' }}</td>
                    </tr>

                    <tr>
                        <th>Attachment</th>

                        <td>

                            <a
                                v-if="item.attachment_url"
                                :href="item.attachment_url"
                                target="_blank"
                                class="btn btn-sm btn-primary"
                            >
                                View Attachment
                            </a>

                            <span v-else>
                                -
                            </span>

                        </td>

                    </tr>

                    <tr>
                        <th>Approved By</th>
                        <td>{{ item.approved_user?.name || '-' }}</td>
                    </tr>

                    <tr>
                        <th>Approved At</th>
                        <td>{{ item.approved_at || '-' }}</td>
                    </tr>

                    <tr>
                        <th>Created By</th>
                        <td>{{ item.created_user?.name || '-' }}</td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ item.created_at }}</td>
                    </tr>

                    <tr>
                        <th>Updated By</th>
                        <td>{{ item.updated_user?.name || '-' }}</td>
                    </tr>

                    <tr>
                        <th>Updated At</th>
                        <td>{{ item.updated_at }}</td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div
            v-else
            class="card-body text-center"
        >
            Loading...
        </div>

    </div>

</div>

</template>