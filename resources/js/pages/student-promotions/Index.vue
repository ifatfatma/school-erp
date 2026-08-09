<script setup>
import { reactive, onMounted } from "vue";
import { RouterLink } from "vue-router";

import AppTable from "@/components/ui/AppTable.vue";

import { useStudentPromotionStore } from "@/stores/studentPromotion";
import { useAcademicSessionStore } from "@/stores/academicSession";

const promotionStore = useStudentPromotionStore();
const sessionStore = useAcademicSessionStore();
const filters = reactive({

    search: "",

    to_session_id: "",

    promotion_type: "",

    page: 1,

    per_page: 15,

});

const columns = [

    { key: "student", label: "Student" },

    { key: "from", label: "From" },

    { key: "to", label: "To" },

    { key: "promotion_date", label: "Promotion Date" },

    { key: "promotion_type", label: "Type" },

    { key: "action", label: "Action" },

];

onMounted(async () => {

    await sessionStore.load({
        per_page: 1000
    });

    load();

});

async function load() {

    await promotionStore.fetch(filters);

}

async function remove(id) {

    if (!confirm("Delete promotion history?")) {
        return;
    }

    await promotionStore.delete(id);

    load();

}

function reset() {

    filters.search = "";

    filters.to_session_id = "";

    filters.promotion_type = "";

    load();

}
</script>

<template>

<div class="container-fluid">

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between">

<h4>Promotion History</h4>

<RouterLink
class="btn btn-primary"
:to="{ name: 'student-promotions.bulk' }"
>

Bulk Promotion

</RouterLink>

</div>

<div class="card-body">

<div class="row g-3 mb-4">

<div class="col-md-4">

<input
class="form-control"
placeholder="Search Student"
v-model="filters.search"
/>

</div>

<div class="col-md-3">
<select
class="form-select"
v-model="filters.to_session_id"
>

<option value="">
All Sessions
</option>

<option
v-for="item in sessionStore.sessions"
:key="item.id"
:value="item.id"
>

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-3">

<select
class="form-select"
v-model="filters.promotion_type"
>

<option value="">
All Types
</option>

<option>Promoted</option>

<option>Repeated</option>

<option>Transferred</option>

<option>Graduated</option>

<option>Dropped</option>

</select>

</div>

<div class="col-md-2">

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
:items="promotionStore.items"
:loading="promotionStore.loading"
>

<template #student="{ item }">

<strong>

{{ item.student.full_name }}

</strong>

<br>

<small>

{{ item.student.admission_no }}

</small>

</template>

<template #from="{ item }">

{{ item.from_session.name }}

<br>

<small>

{{ item.from_class.name }}

{{ item.from_section?.name }}

</small>

</template>

<template #to="{ item }">

{{ item.to_session.name }}

<br>

<small>

{{ item.to_class.name }}

{{ item.to_section?.name }}

</small>

</template>

<template #promotion_type="{ item }">

<span
class="badge"
:class="'bg-' + item.promotion_badge"
>

{{ item.promotion_type }}

</span>

</template>

<template #action="{ item }">

<div class="btn-group">

<RouterLink
class="btn btn-info btn-sm"
:to="{
name:'student-promotions.show',
params:{id:item.id}
}"
>

<i class="bi bi-eye"></i>

</RouterLink>

<button
class="btn btn-danger btn-sm"
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