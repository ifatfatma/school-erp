<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

import { useExamSubjectStore } from "@/stores/examSubject";
import { useExamStore } from "@/stores/exam";
import { useClassStore } from "@/stores/class";
import { useSubjectStore } from "@/stores/subject";

const router = useRouter();
const toast = useToast();

const store = useExamSubjectStore();
const examStore = useExamStore();
const classStore = useClassStore();
const subjectStore = useSubjectStore();

const filters = reactive({
    search: "",
    exam_id: "",
    class_id: "",
    subject_id: "",
    status: "",
    page: 1,
});

onMounted(async () => {

    await Promise.all([
        examStore.active(),
        classStore.active(),
        subjectStore.active()
    ]);

    loadData();

});

async function loadData(page = 1)
{
    filters.page = page;

    await store.fetch(filters);
}

function create()
{
    router.push({
        name: "exam-subjects.create"
    });
}

function edit(id)
{
    router.push({
        name: "exam-subjects.edit",
        params: { id }
    });
}

async function remove(id)
{
    if (!confirm("Delete this Exam Subject?"))
        return;

    try {

        await store.delete(id);

        toast.success("Deleted Successfully");

        loadData(filters.page);

    } catch {

        toast.error("Delete Failed");

    }
}

function resetFilters()
{
    filters.search = "";
    filters.exam_id = "";
    filters.class_id = "";
    filters.subject_id = "";
    filters.status = "";
    filters.page = 1;

    loadData();
}
</script>

<template>

<div class="container-fluid">

<div class="card">

<div class="card-header d-flex justify-content-between">

<h5>

Exam Subjects

</h5>

<button
class="btn btn-primary"
@click="create">

<i class="fas fa-plus"></i>

Create

</button>

</div>

<div class="card-body">

<div class="row mb-3">

<div class="col-md-3">

<input
class="form-control"
placeholder="Search..."
v-model="filters.search">

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filters.exam_id">

<option value="">All Exams</option>

<option
v-for="item in examStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filters.class_id">

<option value="">All Classes</option>

<option
v-for="item in classStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-2">

<select
class="form-select"
v-model="filters.subject_id">

<option value="">All Subjects</option>

<option
v-for="item in subjectStore.items"
:key="item.id"
:value="item.id">

{{ item.name }}

</option>

</select>

</div>

<div class="col-md-1">

<select
class="form-select"
v-model="filters.status">

<option value="">All</option>

<option :value="1">

Active

</option>

<option :value="0">

Inactive

</option>

</select>

</div>

<div class="col-md-2">

<button
class="btn btn-success me-2"
@click="loadData">

Search

</button>

<button
class="btn btn-secondary"
@click="resetFilters">

Reset

</button>

</div>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">

<tr>

<th>#</th>

<th>Exam</th>

<th>Class</th>

<th>Subject</th>

<th>Full</th>

<th>Pass</th>

<th>Theory</th>

<th>Practical</th>

<th>Viva</th>

<th>Project</th>

<th>Status</th>

<th width="120">

Action

</th>

</tr>

</thead>

<tbody>

<tr
v-for="(item,index) in store.items"
:key="item.id">

<td>

{{ index+1 }}

</td>

<td>

{{ item.exam?.name }}

</td>

<td>

{{ item.class?.name }}

</td>

<td>

{{ item.subject?.name }}

</td>

<td>

{{ item.full_marks }}

</td>

<td>

{{ item.pass_marks }}

</td>

<td>

{{ item.theory_marks }}

</td>

<td>

{{ item.practical_marks }}

</td>

<td>

{{ item.viva_marks }}

</td>

<td>

{{ item.project_marks }}

</td>

<td>

<span
class="badge"
:class="item.status
?'bg-success'
:'bg-danger'">

{{ item.status
?'Active'
:'Inactive' }}

</span>

</td>

<td>

<button
class="btn btn-warning btn-sm me-1"
@click="edit(item.id)">

<i class="bi bi-pencil"></i>

</button>

<button
class="btn btn-danger btn-sm"
@click="remove(item.id)">

<i class="bi bi-trash"></i>

</button>

</td>

</tr>

<tr
v-if="!store.items.length">

<td
colspan="12"
class="text-center">

No Records Found

</td>

</tr>

</tbody>

</table>

</div>

<div
class="d-flex justify-content-end mt-3"
v-if="store.pagination.last_page > 1">

<nav>

<ul class="pagination">

<li
v-for="page in store.pagination.last_page"
:key="page"
class="page-item"
:class="{active:page==store.pagination.current_page}">

<a
href="#"
class="page-link"
@click.prevent="loadData(page)">

{{ page }}

</a>

</li>

</ul>

</nav>

</div>

</div>

</div>

</div>

</template>