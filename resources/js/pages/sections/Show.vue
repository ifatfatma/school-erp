
<script setup>
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import AppCard from "@/components/ui/AppCard.vue";
import AppButton from "@/components/ui/AppButton.vue";
import AppLoader from "@/components/ui/AppLoader.vue";
import AppBadge from "@/components/ui/AppBadge.vue";

import { useSectionStore } from "@/stores/section";

const route = useRoute();
const router = useRouter();

const store = useSectionStore();

async function load() {
    await store.find(route.params.id);
}

function editSection() {
    router.push({
        name: "sections.edit",
        params: {
            id: route.params.id,
        },
    });
}

function goBack() {
    router.push({
        name: "sections.index",
    });
}

onMounted(load);
</script>

<template>

    <AppLoader v-if="store.loading" />

    <AppCard
        v-else
        title="Section Details"
    >

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Class</label>

                <div class="form-control bg-light">
                    {{ store.item?.class_name }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Section Name</label>

                <div class="form-control bg-light">
                    {{ store.item?.name }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Capacity</label>

                <div class="form-control bg-light">
                    {{ store.item?.capacity }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Status</label>

                <div class="mt-2">

                    <AppBadge
                        :type="store.item?.status ? 'success' : 'danger'"
                    >
                        {{ store.item?.status ? 'Active' : 'Inactive' }}
                    </AppBadge>

                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Created At</label>

                <div class="form-control bg-light">
                    {{ store.item?.created_at }}
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="fw-bold">Updated At</label>

                <div class="form-control bg-light">
                    {{ store.item?.updated_at }}
                </div>
            </div>

        </div>

        <template #footer>

            <div class="d-flex justify-content-end gap-2">

                <AppButton
                    text="Back"
                    color="secondary"
                    icon="bi bi-arrow-left"
                    @click="goBack"
                />

                <AppButton
                    text="Edit"
                    color="warning"
                    icon="bi bi-pencil"
                    @click="editSection"
                />

            </div>

        </template>

    </AppCard>

</template>