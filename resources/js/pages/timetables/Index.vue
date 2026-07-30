<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { useRouter } from "vue-router";

import { useTimetableStore } from "@/stores/timetable";

import AppButton from "@/components/ui/AppButton.vue";
import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

const router = useRouter();
const store = useTimetableStore();

const filters = reactive({
    search: "",
    class_id: "",
    section_id: "",
    teacher_id: "",
    subject_id: "",
    day: "",
    status: "",
    page: 1,
});

async function load() {
    await store.fetch(filters);
}

function create() {
    router.push({ name: "timetables.create" });
}

function edit(id) {
    router.push({
        name: "timetables.edit",
        params: { id },
    });
}

async function remove(id) {

    if (!confirm("Delete this timetable?")) {
        return;
    }

    await store.delete(id);

    load();
}

watch(
    () => filters.page,
    load
);

onMounted(load);
</script>

<template>

<div class="container-fluid">

    <AppBreadcrumb
        :items="[
            { label:'Dashboard', to:'/' },
            { label:'Timetables' }
        ]"
    />

    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <h4>Timetable</h4>

            <AppButton
                text="Create Timetable"
                @click="create"
            />

        </div>

        <div class="card-body">

            <div class="row mb-3">

                <div class="col-md-4">
                    <AppInput
                        v-model="filters.search"
                        placeholder="Search..."
                    />
                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>

                    <tr>

                        <th>Day</th>

                        <th>Class</th>

                        <th>Section</th>

                        <th>Subject</th>

                        <th>Teacher</th>

                        <th>Time</th>

                        <th>Room</th>

                        <th>Status</th>

                        <th width="150">
                            Action
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    <tr
                        v-for="item in store.items"
                        :key="item.id"
                    >

                        <td>{{ item.day }}</td>

                        <td>{{ item.class_name }}</td>

                        <td>{{ item.section_name }}</td>

                        <td>{{ item.subject_name }}</td>

                        <td>{{ item.teacher_name }}</td>

                        <td>

                            {{ item.start_time }}

                            -

                            {{ item.end_time }}

                        </td>

                        <td>{{ item.room }}</td>

                        <td>

                            <AppBadge
                                :type="item.status ? 'success' : 'danger'"
                            >
                                {{ item.status ? "Active" : "Inactive" }}
                            </AppBadge>

                        </td>

                        <td>

                            <AppButton
                                text="Edit"
                                size="sm"
                                @click="edit(item.id)"
                            />

                            <AppButton
                                text="Delete"
                                color="danger"
                                size="sm"
                                class="ms-2"
                                @click="remove(item.id)"
                            />

                        </td>

                    </tr>

                    <tr
                        v-if="!store.loading && store.items.length === 0"
                    >
                        <td colspan="9" class="text-center">
                            No timetable found.
                        </td>
                    </tr>

                    </tbody>

                </table>

            </div>

            <AppPagination
                :pagination="store.pagination"
                @change="page => filters.page = page"
            />

        </div>

    </div>

</div>

</template>