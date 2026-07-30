<template>

<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">

        <h3>Academic Sessions</h3>

        <RouterLink
            to="/academic-sessions/create"
            class="btn btn-primary"
        >
            Add Session
        </RouterLink>

    </div>

    <table class="table table-bordered">

        <thead>

            <tr>

                <th>Name</th>

                <th>Start</th>

                <th>End</th>

                <th>Status</th>

                <th>Current</th>

                <th width="180">
                    Action
                </th>

            </tr>

        </thead>

        <tbody>

            <tr
                v-for="item in store.sessions"
                :key="item.id"
            >

                <td>{{ item.name }}</td>

                <td>{{ item.start_date }}</td>

                <td>{{ item.end_date }}</td>

                <td>
                    {{ item.status ? 'Active' : 'Inactive' }}
                </td>

                <td>
                    {{ item.is_current ? 'Yes' : 'No' }}
                </td>

                <td>

                    <RouterLink
                        :to="`/academic-sessions/${item.id}/edit`"
                        class="btn btn-warning btn-sm"
                    >
                        Edit
                    </RouterLink>

                    <button
                        class="btn btn-danger btn-sm ms-2"
                        @click="remove(item.id)"
                    >
                        Delete
                    </button>

                </td>

            </tr>

        </tbody>

    </table>

</div>

</template>

<script setup>

import { onMounted } from "vue";

import { useAcademicSessionStore } from "@/stores/academicSession";

const store = useAcademicSessionStore();

onMounted(() => {

    store.load();

});

async function remove(id) {

    if (!confirm("Delete this academic session?")) {
        return;
    }

    await store.delete(id);

    await store.load();

}

</script>