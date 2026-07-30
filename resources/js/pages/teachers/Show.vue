<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import { useTeacherStore } from "@/stores/teacher";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppBadge from "@/components/ui/AppBadge.vue";

const route = useRoute();
const router = useRouter();

const store = useTeacherStore();

const teacher = ref(null);

async function loadTeacher() {
    try {

        teacher.value = await store.find(route.params.id);

    } catch (e) {

        console.error(e);

        router.push({
            name: "teachers.index",
        });

    }
}

function editTeacher() {
    router.push({
        name: "teachers.edit",
        params: {
            id: route.params.id,
        },
    });
}

function goBack() {
    router.push({
        name: "teachers.index",
    });
}

onMounted(loadTeacher);
</script>

<template>

    <AppBreadcrumb
        title="Teacher Details"
        :items="[
            { title: 'Teachers', to: { name: 'teachers.index' } },
            { title: 'Teacher Details' }
        ]"
    />

    <div
        v-if="teacher"
        class="card"
    >

        <div class="card-header d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                Teacher Information
            </h5>

            <div>

                <AppButton
                    class="me-2"
                    color="success" icon="bi bi-pencil" size="sm"
                    @click="editTeacher"
                >
                    
                </AppButton>

                <AppButton
                    variant="secondary"
                    color="secondary" icon="bi bi-arrow-left" size="sm"
                    @click="goBack"
                >
                    Back
                </AppButton>

            </div>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 text-center">

                    <img
                        v-if="teacher.photo"
                        :src="teacher.photo"
                        class="img-fluid rounded border mb-3"
                        style="max-height:220px"
                    >

                    <img
                        v-else
                        src="https://placehold.co/200x220?text=No+Photo"
                        class="img-fluid rounded border mb-3"
                    >

                </div>

                <div class="col-md-9">

                    <table class="table table-bordered">

                        <tbody>

                            <tr>
                                <th width="220">Employee ID</th>
                                <td>{{ teacher.employee_id }}</td>
                            </tr>

                            <tr>
                                <th>Full Name</th>
                                <td>{{ teacher.name }}</td>
                            </tr>

                            <tr>
                                <th>Gender</th>
                                <td>{{ teacher.gender }}</td>
                            </tr>

                            <tr>
                                <th>Date of Birth</th>
                                <td>{{ teacher.date_of_birth || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Phone</th>
                                <td>{{ teacher.phone || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td>{{ teacher.email || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Qualification</th>
                                <td>{{ teacher.qualification || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Experience</th>
                                <td>
                                    {{ teacher.experience }}
                                    <span v-if="teacher.experience">
                                        Year(s)
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <th>Joining Date</th>
                                <td>{{ teacher.joining_date || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Address</th>
                                <td>{{ teacher.address || "-" }}</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    <AppBadge
                                        :type="teacher.status ? 'success' : 'danger'"
                                    >
                                        {{ teacher.status ? 'Active' : 'Inactive' }}
                                    </AppBadge>

                                </td>
                            </tr>

                            <tr>
                                <th>Created At</th>
                                <td>{{ teacher.created_at }}</td>
                            </tr>

                            <tr>
                                <th>Updated At</th>
                                <td>{{ teacher.updated_at }}</td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</template>