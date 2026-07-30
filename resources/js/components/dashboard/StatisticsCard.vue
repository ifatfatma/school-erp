<template>

<div class="container-fluid">

    <div class="row g-3">

        <div
            class="col-xl-3 col-lg-4 col-md-6"
            v-for="card in cards"
            :key="card.title"
        >

            <StatisticsCard
                :title="card.title"
                :value="card.value"
                :icon="card.icon"
            />

        </div>

    </div>

</div>

</template>

<script setup>

import { computed,onMounted } from "vue";

import StatisticsCard from "@/components/dashboard/StatisticsCard.vue";

import { useDashboardStore } from "@/stores/dashboard";

const dashboard=useDashboardStore();

onMounted(()=>{

    dashboard.load();

});

const cards=computed(()=>[

    {

        title:"Students",

        value:dashboard.statistics.students,

        icon:"bi bi-mortarboard-fill"

    },

    {

        title:"Teachers",

        value:dashboard.statistics.teachers,

        icon:"bi bi-person-workspace"

    },

    {

        title:"Parents",

        value:dashboard.statistics.parents,

        icon:"bi bi-people-fill"

    },

    {

        title:"Classes",

        value:dashboard.statistics.classes,

        icon:"bi bi-building"

    },

    {

        title:"Attendance",

        value:dashboard.statistics.attendance+"%",

        icon:"bi bi-check-circle"

    },

    {

        title:"Today's Fees",

        value:"₹ "+dashboard.statistics.today_fee,

        icon:"bi bi-cash-stack"

    },

    {

        title:"Pending Fees",

        value:"₹ "+dashboard.statistics.pending_fee,

        icon:"bi bi-wallet2"

    },

    {

        title:"Exams",

        value:dashboard.statistics.exams,

        icon:"bi bi-journal-text"

    }

]);

</script>