<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

import { useResultGradeStore } from "@/stores/resultGrade";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppInput from "@/components/ui/AppInput.vue";

const router = useRouter();
const store = useResultGradeStore();

const search = ref("");

async function load() {
    await store.fetch({
        search: search.value,
        per_page: 100,
    });
}

function create() {
    router.push({
        name: "result-grades.create",
    });
}

function edit(id) {
    router.push({
        name: "result-grades.edit",
        params: {
            id,
        },
    });
}

async function remove(id) {

    if (!confirm("Are you sure you want to delete this grade?")) {
        return;
    }

    try {

        await store.remove(id);

        await load();

    } catch (error) {

        console.error(
            "Failed to delete grade:",
            error
        );

    }
}

onMounted(() => {
    load();
});
</script>

<template>

    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Result Grades' }
        ]"
    />

    <div class="card">

        <div
            class="card-header d-flex justify-content-between align-items-center"
        >

            <h4 class="mb-0">
                Result Grade System
            </h4>

            <AppButton
                text="Add Grade"
                @click="create"
            />

        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">

                    <AppInput
                        v-model="search"
                        label="Search"
                        placeholder="Search grade..."
                        @keyup.enter="load"
                    />

                </div>

                <div class="col-md-2 d-flex align-items-end">

                    <AppButton
                        text="Search"
                        @click="load"
                    />

                </div>

            </div>

            <div class="table-responsive">

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
                                Percentage
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

                            <th width="150">
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
                                {{ grade.min_percentage }}
                                -
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
                                    class="btn btn-sm btn-primary me-1"
                                    @click="edit(grade.id)"
                                >
                                    Edit
                                </button>

                                <button
                                    class="btn btn-sm btn-danger"
                                    @click="remove(grade.id)"
                                >
                                    Delete
                                </button>

                            </td>

                        </tr>

                        <tr v-if="!store.loading && !store.items.length">

                            <td
                                colspan="7"
                                class="text-center text-muted"
                            >
                                No result grades found.
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</template>