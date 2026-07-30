<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import { useTeacherStore } from "@/stores/teacher";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppSearch from "@/components/ui/AppSearch.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppTable from "@/components/ui/AppTable.vue";

const router = useRouter();
const store = useTeacherStore();

const filters = reactive({
    search: "",
    page: 1,
    per_page: 10,
});

const columns = [
    {
        key: "photo",
        label: "Photo",
        class: "text-center",
    },
    {
        key: "employee_id",
        label: "Employee ID",
    },
    {
        key: "name",
        label: "Teacher Name",
    },
    {
        key: "phone",
        label: "Phone",
    },
    {
        key: "email",
        label: "Email",
    },
    {
        key: "status",
        label: "Status",
        class: "text-center",
    },
    {
        key: "actions",
        label: "Actions",
        class: "text-center",
    },
];

async function loadTeachers() {
    await store.fetch(filters);
}

function onSearch() {
    filters.page = 1;
    loadTeachers();
}

function changePage(page) {
    filters.page = page;
    loadTeachers();
}

function createTeacher() {
    router.push({
        name: "teachers.create",
    });
}

function viewTeacher(id) {
    router.push({
        name: "teachers.show",
        params: { id },
    });
}

function editTeacher(id) {
    router.push({
        name: "teachers.edit",
        params: { id },
    });
}

async function deleteTeacher(id) {
    if (!confirm("Are you sure you want to delete this teacher?")) {
        return;
    }

    await store.delete(id);
    loadTeachers();
}

onMounted(loadTeachers);
</script>

<template>
    <div class="container">
    <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Teachers'},
            ]"
        />

    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <AppSearch
                v-model="filters.search"
                placeholder="Search teacher..."
                @search="onSearch"
                @keyup.enter="onSearch"
            />

            <AppButton @click="createTeacher" text="Add Teacher">
                Add Teacher
            </AppButton>

        </div>

        <div class="card-body">

            <AppTable
                :columns="columns"
                :items="store.items"
                :loading="store.loading"
            >

                <!-- Photo -->
                <template #photo="{ item }">

                    <img
                        v-if="item.photo"
                        :src="item.photo"
                        class="rounded-circle border"
                        style="width:50px;height:50px;object-fit:cover"
                    />

                    <span v-else>-</span>

                </template>

                <!-- Name -->
                <template #name="{ item }">

                    <a
                        href="#"
                        @click.prevent="viewTeacher(item.id)"
                    >
                        {{ item.name }}
                    </a>

                </template>

                <!-- Status -->
                <template #status="{ item }">

                    <AppBadge
                        :type="item.status ? 'success' : 'danger'"
                    >
                        {{ item.status ? "Active" : "Inactive" }}
                    </AppBadge>

                </template>

                <!-- Actions -->
                <template #actions="{ item }">

                    <div class="d-flex justify-content-center gap-2">
<div class="btn-group btn-group-sm">
                        <AppButton
                            size="sm"
                            color="info"
                            icon="bi bi-eye"
                            @click="viewTeacher(item.id)" 
                        >
                            View
                        </AppButton>

                        <AppButton
                            size="sm"
                            color="success"
                            icon="bi bi-pencil"
                            @click="editTeacher(item.id)" 
                        >
                            Edit
                        </AppButton>

                        <AppButton
                            size="sm"
                            icon="bi bi-trash"
                            variant="danger"
                            color="danger"
                            @click="deleteTeacher(item.id)"
                        >
                            Delete
                        </AppButton>

                    </div>
</div>
                </template>

            </AppTable>

        </div>

        <div class="card-footer">

            <AppPagination
                :meta="store.pagination"
                @change="changePage"
            />

        </div>

    </div>
    </div>
</template>