<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter, RouterLink } from "vue-router";
import timetableApi from "@/api/timetable";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

const route = useRoute();
const router = useRouter();

const loading = ref(false);
const timetable = ref({});

onMounted(() => {
    loadTimetable();
});

async function loadTimetable() {
    loading.value = true;

    try {
        const { data } = await timetableApi.get(route.params.id);
        timetable.value = data.data ?? data;
    } catch (error) {
        console.error("Failed to load timetable:", error);
        router.push({ name: "timetables.index" });
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="container-fluid">
        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Timetables', to: { name: 'timetables.index' } },
                { label: 'Timetable Details' }
            ]"
        />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Timetable Details</h5>

                <div class="d-flex gap-2">
                    <RouterLink
                        v-if="timetable.id"
                        :to="{ name: 'timetables.edit', params: { id: timetable.id } }"
                        class="btn btn-warning"
                    >
                        <i class="bi bi-pencil me-1"></i> Edit
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'timetables.index' }"
                        class="btn btn-secondary"
                    >
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </RouterLink>
                </div>
            </div>

            <div class="card-body" v-if="loading">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary me-2"></div>
                    Loading timetable details...
                </div>
            </div>

            <div class="card-body" v-else>
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th width="220">Class</th>
                            <td><strong>{{ timetable.class_name || timetable.class?.name || '-' }}</strong></td>
                        </tr>

                        <tr>
                            <th>Section</th>
                            <td>{{ timetable.section_name || timetable.section?.name || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Subject</th>
                            <td>{{ timetable.subject_name || timetable.subject?.name || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Teacher</th>
                            <td>{{ timetable.teacher_name || timetable.teacher?.name || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Day</th>
                            <td>
                                <span class="badge bg-primary fs-7">
                                    {{ timetable.day }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Time Slot</th>
                            <td>
                                <strong>{{ timetable.start_time }}</strong> to <strong>{{ timetable.end_time }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <th>Room / Classroom</th>
                            <td>{{ timetable.room || timetable.room_no || 'Not Assigned' }}</td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td>
                                <span
                                    class="badge"
                                    :class="timetable.status ? 'bg-success' : 'bg-danger'"
                                >
                                    {{ timetable.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>{{ timetable.created_at || '-' }}</td>
                        </tr>

                        <tr>
                            <th>Last Updated</th>
                            <td>{{ timetable.updated_at || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
