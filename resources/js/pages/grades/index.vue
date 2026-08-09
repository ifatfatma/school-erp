<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

import { useGradeStore } from "@/stores/grade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";

const router = useRouter();
const store = useGradeStore();

const search = ref("");

async function loadGrades() {
    await store.fetch({
        search: search.value,
        per_page: 15,
    });
}

function createGrade() {
    router.push({
        name: "grades.create",
    });
}

function editGrade(id) {
    router.push({
        name: "grades.edit",
        params: {
            id,
        },
    });
}

async function deleteGrade(id) {

    if (!confirm("Are you sure you want to delete this grade?")) {
        return;
    }

    try {

        await store.remove(id);

        await loadGrades();

    } catch (error) {

        console.error("Failed to delete grade:", error);

    }
}

onMounted(() => {
    loadGrades();
});
</script>

<template>

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Examination' },
            { label: 'Grade System' }
        ]"
    />

    <div class="card">

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Grade System
            </h4>

            <AppButton
                text="Add Grade"
                @click="createGrade"
            />

        </div>

        <div class="card-body">

            <!-- Search -->
            <div class="row mb-3">

                <div class="col-md-4">

                    <input
                        v-model="search"
                        type="text"
                        class="form-control"
                        placeholder="Search grade..."
                        @keyup.enter="loadGrades"
                    />

                </div>

                <div class="col-md-2">

                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="loadGrades"
                    >
                        Search
                    </button>

                </div>

            </div>

            <!-- Loading -->
            <div
                v-if="store.loading"
                class="text-center py-4"
            >
                Loading...
            </div>

            <!-- Table -->
            <div
                v-else
                class="table-responsive"
            >

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Grade
                            </th>

                            <th>
                                Minimum %
                            </th>

                            <th>
                                Maximum %
                            </th>

                            <th>
                                Grade Point
                            </th>

                            <th>
                                Result
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="160">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="(grade, index) in store.items"
                            :key="grade.id"
                        >

                            <td>
                                {{ index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ grade.name }}
                                </strong>
                            </td>

                            <td>
                                {{ grade.min_percentage }}%
                            </td>

                            <td>
                                {{ grade.max_percentage }}%
                            </td>

                            <td>
                                {{ grade.grade_point }}
                            </td>

                            <td>

                                <span
                                    :class="
                                        grade.is_pass
                                            ? 'badge bg-success'
                                            : 'badge bg-danger'
                                    "
                                >
                                    {{
                                        grade.is_pass
                                            ? 'Pass'
                                            : 'Fail'
                                    }}
                                </span>

                            </td>

                            <td>

                                <span
                                    :class="
                                        grade.status
                                            ? 'badge bg-success'
                                            : 'badge bg-secondary'
                                    "
                                >
                                    {{
                                        grade.status
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>

                            </td>

                            <td>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary me-1"
                                    @click="editGrade(grade.id)"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    @click="deleteGrade(grade.id)"
                                >
                                    Delete
                                </button>

                            </td>

                        </tr>

                        <tr v-if="!store.items.length">

                            <td
                                colspan="8"
                                class="text-center text-muted py-4"
                            >
                                No grades found.
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</template>