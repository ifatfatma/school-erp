<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useTeacherAttendanceStore } from "@/stores/teacherAttendance";

const route = useRoute();
const router = useRouter();

const attendanceStore = useTeacherAttendanceStore();

const loading = ref(false);

onMounted(async () => {

    loading.value = true;

    try {

        await attendanceStore.fetchItem(route.params.id);

    } catch (e) {

        console.error(e);

    } finally {

        loading.value = false;

    }

});

function badgeClass(status) {

    switch (status) {

        case "Present":
            return "bg-success";

        case "Absent":
            return "bg-danger";

        case "Late":
            return "bg-warning";

        case "Half Day":
            return "bg-info";

        case "Leave":
            return "bg-secondary";

        default:
            return "bg-light text-dark";
    }

}
</script>

<template>

<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h5>Teacher Attendance Details</h5>

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

            <tbody>

                <tr>
                    <th width="220">Teacher</th>
                    <td>
                        {{ attendanceStore.item.teacher?.name }}
                    </td>
                </tr>

                <tr>
                    <th>Attendance Date</th>
                    <td>
                        {{ attendanceStore.item.attendance_date }}
                    </td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>

                        <span
                            class="badge"
                            :class="badgeClass(attendanceStore.item.status)"
                        >
                            {{ attendanceStore.item.status }}
                        </span>

                    </td>
                </tr>

                <tr>
                    <th>Check In</th>
                    <td>
                        {{ attendanceStore.item.check_in || "-" }}
                    </td>
                </tr>

                <tr>
                    <th>Check Out</th>
                    <td>
                        {{ attendanceStore.item.check_out || "-" }}
                    </td>
                </tr>

                <tr>
                    <th>Remarks</th>
                    <td>
                        {{ attendanceStore.item.remarks || "-" }}
                    </td>
                </tr>

                <tr>
                    <th>Created At</th>
                    <td>
                        {{ attendanceStore.item.created_at }}
                    </td>
                </tr>

                <tr>
                    <th>Updated At</th>
                    <td>
                        {{ attendanceStore.item.updated_at }}
                    </td>
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

</template>