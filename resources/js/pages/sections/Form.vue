<script setup>
import { ref, onMounted } from "vue";

import AppInput from "@/components/ui/AppInput.vue";
import AppSelect from "@/components/ui/AppSelect.vue";

import classApi from "@/api/class";

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
});

const classOptions = ref([]);

const statusOptions = [
    {
        label: "Active",
        value: true,
    },
    {
        label: "Inactive",
        value: false,
    },
];

const loadingClasses = ref(false);

async function loadClasses() {
    loadingClasses.value = true;

    try {
        const { data } = await classApi.getAll({
            per_page: 1000,
            status: true,
        });
        //console.log(data.data);

        classOptions.value = data.data ?? [];
    } catch (error) {
        console.error(error);
    } finally {
        loadingClasses.value = false;
    }
}

onMounted(loadClasses);
</script>

<template>
    <div class="row">
        
        <!-- Class -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.class_id"
                label="Class"
                placeholder="Select Class"
                :options="classOptions"
                option-label="name"
                option-value="id"
                :loading="loadingClasses"
                :error="errors.class_id?.[0]"
                required
            />
        </div>

        <!-- Section Name -->
        <div class="col-md-6 mb-3">
            <AppInput
                v-model="form.name"
                label="Section Name"
                placeholder="Enter Section Name"
                :error="errors.name?.[0]"
                required
            />
        </div>

        <!-- Capacity -->
        <div class="col-md-6 mb-3">
            <AppInput
                v-model="form.capacity"
                label="Capacity"
                type="number"
                min="0"
                placeholder="Enter Capacity"
                :error="errors.capacity?.[0]"
            />
        </div>

        <!-- Status -->
        <div class="col-md-6 mb-3">
            <AppSelect
                v-model="form.status"
                label="Status"
                :options="statusOptions"
                option-label="label"
                option-value="value"
                :error="errors.status?.[0]"
                required
            />
        </div>

    </div>
</template>