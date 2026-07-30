<template>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        :class="column.class"
                    >
                        {{ column.label }}
                    </th>
                </tr>
            </thead>

            <tbody>
                <!-- Loading -->
                <tr v-if="loading">
                    <td :colspan="columns.length" class="text-center py-4">
                        Loading...
                    </td>
                </tr>

                <!-- No Data -->
                <tr v-else-if="items.length === 0">
                    <td :colspan="columns.length" class="text-center py-4">
                        {{ emptyText }}
                    </td>
                </tr>

                <!-- Data -->
                <tr
                    v-else
                    v-for="(item, index) in items"
                    :key="item.id ?? index"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                    >
                        <!-- Custom Slot -->
                        <slot
                            :name="column.key"
                            :item="item"
                            :value="item[column.key]"
                            :index="index"
                        >
                            {{ item[column.key] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    columns: {
        type: Array,
        required: true
    },

    items: {
        type: Array,
        default: () => []
    },

    loading: {
        type: Boolean,
        default: false
    },

    emptyText: {
        type: String,
        default: 'No records found.'
    }
});
</script>