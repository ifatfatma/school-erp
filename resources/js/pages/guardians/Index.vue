<script setup>
import { reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useGuardianStore } from "@/stores/guardian";

const guardianStore = useGuardianStore();

const filters = reactive({

    search: "",

    guardian_type: "",

    status: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "photo", label: "Photo" },

    { key: "name", label: "Guardian" },

    { key: "guardian_type", label: "Type" },

    { key: "mobile", label: "Mobile" },

    { key: "email", label: "Email" },

    { key: "students", label: "Students" },

    { key: "status", label: "Status" },

    { key: "action", label: "Action" },

];

onMounted(() => {

    load();

});

async function load(){

    await guardianStore.fetch(filters);

}

async function remove(id){

    if(!confirm("Delete this guardian?")){

        return;

    }

    await guardianStore.delete(id);

    load();

}

function reset(){

    filters.search="";

    filters.guardian_type="";

    filters.status="";

    filters.page=1;

    load();

}
</script>

<template>

<div class="container-fluid">

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h4 class="mb-0">

Guardian List

</h4>

<RouterLink

class="btn btn-primary"

:to="{name:'guardians.create'}"

>

<i class="fas fa-plus me-1"></i>

Add Guardian

</RouterLink>

</div>

<div class="card-body">

<div class="row g-3 mb-4">

<div class="col-md-4">

<input

class="form-control"

placeholder="Search name / mobile"

v-model="filters.search"

/>

</div>

<div class="col-md-3">

<select

class="form-select"

v-model="filters.guardian_type"

>

<option value="">All Types</option>

<option>Father</option>

<option>Mother</option>

<option>Grandfather</option>

<option>Grandmother</option>

<option>Uncle</option>

<option>Aunt</option>

<option>Brother</option>

<option>Sister</option>

<option>Legal Guardian</option>

<option>Other</option>

</select>

</div>

<div class="col-md-2">

<select

class="form-select"

v-model="filters.status"

>

<option value="">All Status</option>

<option :value="1">

Active

</option>

<option :value="0">

Inactive

</option>

</select>

</div>

<div class="col-md-3">

<div class="d-flex gap-2">

<button

class="btn btn-primary"

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

</div>

<AppTable

:columns="columns"

:items="guardianStore.items"

:loading="guardianStore.loading"

>

<template #photo="{item}">

<img

v-if="item.photo_url"

:src="item.photo_url"

class="rounded-circle"

width="40"

height="40"

/>

<div

v-else

class="avatar-sm rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"

style="width:40px;height:40px"

>

{{ item.first_name?.charAt(0) }}

</div>

</template>

<template #name="{item}">

<strong>

{{ item.full_name }}

</strong>

<br>

<small class="text-muted">

{{ item.relation }}

</small>

</template>

<template #students="{item}">

<span class="badge bg-info">

{{ item.students?.length ?? 0 }}

</span>

</template>

<template #status="{item}">

<span

class="badge"

:class="item.status ? 'bg-success':'bg-danger'"

>

{{ item.status ? "Active":"Inactive" }}

</span>

</template>

<template #action="{item}">

<div class="btn-group">

<RouterLink

class="btn btn-sm btn-info"

:to="{

name:'guardians.show',

params:{id:item.id}

}"

>

<i class="bi bi-eye"></i>

</RouterLink>

<RouterLink

class="btn btn-sm btn-warning"

:to="{

name:'guardians.edit',

params:{id:item.id}

}"

>

<i class="bi bi-pencil"></i>

</RouterLink>

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