<script setup>
import { reactive, onMounted } from "vue";
import { useRouter } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useExamTypeStore } from "@/stores/examType";

const router = useRouter();
const store = useExamTypeStore();

const filters = reactive({

    search: "",

    status: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "name", label: "Name" },

    { key: "code", label: "Code" },

    { key: "description", label: "Description" },

    { key: "status", label: "Status" },

    { key: "action", label: "Action" },

];

onMounted(() => {

    load();

});

async function load(page = 1) {

    filters.page = page;

    await store.fetch(filters);

}

function reset() {

    filters.search = "";

    filters.status = "";

    filters.page = 1;

    load();

}

function create() {

    router.push({

        name: "exam-types.create"

    });

}

function edit(id) {

    router.push({

        name: "exam-types.edit",

        params: { id }

    });

}

async function remove(id) {

    if (!confirm("Delete this Exam Type?")) {

        return;

    }

    await store.delete(id);

    load(filters.page);

}
</script>

<template>

<div class="container-fluid py-2">

<AppBreadcrumb
    :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Exam Types' }
    ]"
/>

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h4 class="mb-0">

Exam Types

</h4>

<button
class="btn btn-primary"
@click="create"
>

<i class="bi bi-plus"></i>

Add Exam Type

</button>

</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-5">

<input
class="form-control"
placeholder="Search Name / Code"
v-model="filters.search"
/>

</div>

<div class="col-md-3">

<select
class="form-select"
v-model="filters.status"
>

<option value="">

All Status

</option>

<option :value="1">

Active

</option>

<option :value="0">

Inactive

</option>

</select>

</div>

<div class="col-md-4">

<button
class="btn btn-primary me-2"
@click="load"
>

Search

</button>

<button
class="btn btn-secondary"
@click="reset"
>

Reset

</button>

</div>

</div>

<AppTable

:columns="columns"

:items="store.items"

:loading="store.loading"

:pagination="store.pagination"

@page-change="load"

>

<template #status="{ item }">

<span

class="badge"

:class="item.status ? 'bg-success' : 'bg-danger'"

>

{{ item.status_label }}

</span>

</template>

<template #description="{ item }">

{{ item.description || "-" }}

</template>

<template #action="{ item }">

<div class="btn-group">

<button

class="btn btn-sm btn-warning"

@click="edit(item.id)"

>

<i class="bi bi-pencil"></i>

</button>

<button

class="btn btn-sm btn-danger"

@click="remove(item.id)"

>

<i class="bi bi-trash"></i>

</button>

</div>

</template>

</AppTable>

</div>

</div>

</div>

</template>