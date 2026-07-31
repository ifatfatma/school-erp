<script setup>
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { useLeaveTypeStore } from "@/stores/leaveType";
import AppTable from "@/components/ui/AppTable.vue";

const store = useLeaveTypeStore();

const filters = ref({
    search: "",
    status: "",
    page: 1,
    per_page: 10,
});

const columns = [
    { key: "name", label: "Leave Type" },
    { key: "max_days", label: "Max Days" },
    { key: "is_paid", label: "Paid" },
    { key: "status", label: "Status" },
    { key: "actions", label: "Actions", class: "text-end" },
];

const load = async () => {
    await store.fetch(filters.value);
};

const search = () => {
    filters.value.page = 1;
    load();
};

const remove = async (id) => {
    if (!confirm("Are you sure you want to delete this leave type?")) {
        return;
    }

    try {
        await store.delete(id);

        if (window.$toast) {
            window.$toast.success("Leave Type deleted successfully.");
        }

        load();

    } catch (e) {
        console.error(e);

        if (window.$toast) {
            window.$toast.error("Unable to delete Leave Type.");
        }
    }
};

onMounted(load);
</script>

<template>
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Leave Types
                </h5>

                <RouterLink
                    :to="{ name: 'leave-types.create' }"
                    class="btn btn-primary"
                >
                    <i class="fas fa-plus"></i>
                    Add Leave Type
                </RouterLink>

            </div>

            <div class="card-body">

                <div class="row mb-3">

                    <div class="col-md-4">

                        <input
                            v-model="filters.search"
                            @keyup.enter="search"
                            class="form-control"
                            placeholder="Search..."
                        />

                    </div>

                    <div class="col-md-3">

                        <select
                            v-model="filters.status"
                            @change="search"
                            class="form-select"
                        >
                            <option value="">
                                All Status
                            </option>

                            <option :value="true">
                                Active
                            </option>

                            <option :value="false">
                                Inactive
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <button
                            class="btn btn-primary w-100"
                            @click="search"
                        >
                            Search
                        </button>

                    </div>

                </div>

                <AppTable
                    :columns="columns"
                    :items="store.items"
                    :loading="store.loading"
                >

                    <template #is_paid="{ item }">

                        <span
                            class="badge"
                            :class="item.is_paid ? 'bg-success' : 'bg-warning'"
                        >
                            {{ item.is_paid_text }}
                        </span>

                    </template>

                    <template #status="{ item }">

                        <span
                            class="badge"
                            :class="item.status ? 'bg-success' : 'bg-danger'"
                        >
                            {{ item.status_text }}
                        </span>

                    </template>

                    <template #actions="{ item }">

                        <div class="text-end">

                            <RouterLink
                                :to="{ name: 'leave-types.show', params: { id: item.id } }"
                                class="btn btn-sm btn-info me-1"
                            >
                                View
                            </RouterLink>

                            <RouterLink
                                :to="{ name: 'leave-types.edit', params: { id: item.id } }"
                                class="btn btn-sm btn-warning me-1"
                            >
                                Edit
                            </RouterLink>

                            <button
                                class="btn btn-sm btn-danger"
                                @click="remove(item.id)"
                            >
                                Delete
                            </button>

                        </div>

                    </template>

                </AppTable>

            </div>

        </div>

    </div>
</template>