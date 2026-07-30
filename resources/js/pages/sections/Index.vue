<template>

    <AppCard title="Sections">

        <template #header-action>

            <AppButton
                text="Add Section"
                icon="bi bi-plus-circle"
                @click="createSection"
            />

        </template>

        <div class="row mb-3">

            <div class="col-md-6">
                <AppSearch
                    v-model="search"
                    placeholder="Search Section..."
                    @search="load"
                />
            </div>

            <div class="col-md-3">

                <AppSelect
                    v-model="filters.class_id"
                    :options="classOptions"
                    option-label="name"
                    option-value="id"
                    placeholder="All Classes"
                    @change="load"
                />

            </div>

        </div>

        <AppLoader v-if="store.loading" />

        <div
            v-else
            class="table-responsive"
        >

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">

                    <tr>

                        <th width="70">#</th>

                        <th>Class</th>

                        <th>Section</th>

                        <th>Capacity</th>

                        <th>Status</th>

                        <th width="170">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="(item,index) in store.items"
                        :key="item.id"
                    >

                        <td>
                            {{ index + 1 }}
                        </td>

                        <td>
                            {{ item.class_name }}
                        </td>

                        <td>
                            {{ item.name }}
                        </td>

                        <td>
                            {{ item.capacity }}
                        </td>

                        <td>

                            <AppBadge
                                :type="item.status ? 'success' : 'danger'"
                            >
                                {{ item.status ? 'Active' : 'Inactive' }}
                            </AppBadge>

                        </td>

                        <td>
<div class="d-flex gap-2">
    <div class="btn-group btn-group-sm">
                            <AppButton
                                color="warning"
                                size="sm"
                                icon="bi bi-pencil"
                                @click="editSection(item.id)"
                            />

                            <AppButton
                                color="danger"
                                size="sm"
                                icon="bi bi-trash"
                                @click="removeSection(item.id)"
                            />
</div>
</div>
                        </td>

                    </tr>

                    <tr v-if="!store.items.length">

                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            No sections found.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        <AppPagination
            :pagination="store.pagination"
            @change="load"
        />

    </AppCard>

</template>
<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

import { useSectionStore } from "@/stores/section";
import classApi from "@/api/class";

import AppButton from "@/components/ui/AppButton.vue";
import AppCard from "@/components/ui/AppCard.vue";
import AppSearch from "@/components/ui/AppSearch.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBadge from "@/components/ui/AppBadge.vue";
import AppLoader from "@/components/ui/AppLoader.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

const router = useRouter();
const store = useSectionStore();

const search = ref("");

const filters = reactive({
    class_id: "",
});

const classOptions = ref([]);

async function load(page = 1) {
    await store.load({
        page,
        search: search.value,
        class_id: filters.class_id,
    });
}

async function loadClasses() {
    const { data } = await classApi.getAll({
        per_page: 1000,
    });

    classOptions.value = data.data;
}

function createSection() {
    router.push({
        name: "sections.create",
    });
}

function editSection(id) {
    router.push({
        name: "sections.edit",
        params: { id },
    });
}

async function removeSection(id) {

    const result = await Swal.fire({
        title: "Delete Section?",
        text: "This record will be deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Delete",
        confirmButtonColor: "#dc3545",
    });

    if (!result.isConfirmed) {
        return;
    }

    await store.delete(id);

    await load();

    Swal.fire(
        "Deleted!",
        "Section deleted successfully.",
        "success"
    );
}

onMounted(async () => {

    await loadClasses();

    await load();

});
</script>