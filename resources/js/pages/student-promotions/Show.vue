<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { RouterLink } from "vue-router";

import { useStudentPromotionStore } from "@/stores/studentPromotion";

const route = useRoute();
const router = useRouter();

const promotionStore = useStudentPromotionStore();

const promotion = ref(null);

onMounted(async () => {

    promotion.value = await promotionStore.find(route.params.id);

});
</script>

<template>

<div class="container-fluid">

<div class="card shadow-sm">

<div class="card-header d-flex justify-content-between align-items-center">

<h4 class="mb-0">
Promotion Details
</h4>

<div>

<button
class="btn btn-secondary me-2"
@click="router.back()"
>
Back
</button>

</div>

</div>

<div
class="card-body"
v-if="promotion"
>

<div class="row">

<div class="col-md-6">

<table class="table table-bordered">

<tr>
<th width="180">Student</th>
<td>{{ promotion.student.full_name }}</td>
</tr>

<tr>
<th>Admission No</th>
<td>{{ promotion.student.admission_no }}</td>
</tr>

<tr>
<th>Roll No</th>
<td>{{ promotion.student.roll_no }}</td>
</tr>

<tr>
<th>Promotion Type</th>
<td>

<span
class="badge"
:class="'bg-' + promotion.promotion_badge"
>

{{ promotion.promotion_type }}

</span>

</td>
</tr>

<tr>
<th>Promotion Date</th>
<td>{{ promotion.promotion_date }}</td>
</tr>

<tr>
<th>Remarks</th>
<td>{{ promotion.remarks || "-" }}</td>
</tr>

</table>

</div>

<div class="col-md-6">

<table class="table table-bordered">

<tr class="table-primary">

<th colspan="2">

Previous Information

</th>

</tr>

<tr>
<th width="180">Session</th>
<td>{{ promotion.from_session.name }}</td>
</tr>

<tr>
<th>Class</th>
<td>{{ promotion.from_class.name }}</td>
</tr>

<tr>
<th>Section</th>
<td>{{ promotion.from_section?.name ?? "-" }}</td>
</tr>

<tr class="table-success">

<th colspan="2">

Promoted To

</th>

</tr>

<tr>
<th>Session</th>
<td>{{ promotion.to_session.name }}</td>
</tr>

<tr>
<th>Class</th>
<td>{{ promotion.to_class.name }}</td>
</tr>

<tr>
<th>Section</th>
<td>{{ promotion.to_section?.name ?? "-" }}</td>
</tr>

</table>

</div>

</div>

<div class="mt-4">

<RouterLink
class="btn btn-primary"
:to="{ name:'student-promotions.index' }"
>

Back to List

</RouterLink>

</div>

</div>

</div>

</div>

</template>