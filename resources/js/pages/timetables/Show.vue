<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import timetableApi from "@/api/timetable";

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
        const { data } = await timetableApi.find(route.params.id);
        timetable.value = data.data ?? data;
    } catch (error) {
        console.error(error);
        router.push({ name: "timetables.index" });
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Timetable Details</h5>

                <RouterLink
                    :to="{ name: 'timetables.index' }"
                    class="btn btn-secondary">
                    Back
                </RouterLink>
            </div>

            <div class="card-body" v-if="loading">
                Loading...
            </div>

            <div class="card-body" v-else>

                <table class="table table-bordered">

                    <tr>
                        <th width="200">Class</th>
                        <td>{{ timetable.class?.name }}</td>
                    </tr>

                    <tr>
                        <th>Section</th>
                        <td>{{ timetable.section?.name }}</td>
                    </tr>

                    <tr>
                        <th>Subject</th>
                        <td>{{ timetable.subject?.name }}</td>
                    </tr>

                    <tr>
                        <th>Teacher</th>
                        <td>{{ timetable.teacher?.name }}</td>
                    </tr>

                    <tr>
                        <th>Day</th>
                        <td>{{ timetable.day }}</td>
                    </tr>

                    <tr>
                        <th>Start Time</th>
                        <td>{{ timetable.start_time }}</td>
                    </tr>

                    <tr>
                        <th>End Time</th>
                        <td>{{ timetable.end_time }}</td>
                    </tr>

                    <tr>
                        <th>Room</th>
                        <td>{{ timetable.room_no }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>
                            <span
                                class="badge"
                                :class="timetable.status ? 'bg-success' : 'bg-danger'">
                                {{ timetable.status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>

                </table>

            </div>

        </div>

    </div>
</template>