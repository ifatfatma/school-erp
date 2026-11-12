<template>
    <div class="container-fluid">
        <AppBreadcrumb
            :items="[
                { label: 'Dashboard', to: '/' },
                { label: 'Reports Hub' },
            ]"
        />

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">
                    <i class="bi bi-graph-up text-primary me-2"></i> Reports & Analytics Hub
                </h3>
                <p class="text-muted mb-0 small">
                    Access institutional reports across financial, academic, and facility management modules.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div
                v-for="rep in availableReports"
                :key="rep.route"
                class="col-12 col-sm-6 col-lg-4 col-xl-3"
            >
                <div class="card h-100 shadow-sm border-0 report-card transition-all">
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div
                                class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center"
                                :class="rep.bgClass"
                                style="width: 52px; height: 52px;"
                            >
                                <i :class="rep.icon + ' fs-4'"></i>
                            </div>
                            <div>
                                <span class="badge bg-light text-secondary border small">{{ rep.category }}</span>
                                <h5 class="card-title fw-bold text-dark mb-0 mt-1">{{ rep.title }}</h5>
                            </div>
                        </div>

                        <p class="card-text text-muted small flex-grow-1 mb-4">
                            {{ rep.description }}
                        </p>

                        <RouterLink
                            :to="rep.route"
                            class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
                        >
                            <span>Open Report</span>
                            <i class="bi bi-arrow-right"></i>
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const reports = [
    {
        title: 'Fee Reports',
        category: 'Finance',
        icon: 'bi bi-cash-stack',
        bgClass: 'bg-primary',
        description: 'Comprehensive breakdown of fee collections, outstanding student dues, waivers, and discount records.',
        route: '/fee-reports',
        permission: 'fee-reports.index'
    },
    {
        title: 'Expense Reports',
        category: 'Finance',
        icon: 'bi bi-wallet2',
        bgClass: 'bg-danger',
        description: 'Detailed analysis of school expenditures, category summaries, vendor payments, and recurring operational costs.',
        route: '/expense-reports',
        permission: 'expense_reports.view'
    },
    {
        title: 'Income Reports',
        category: 'Finance',
        icon: 'bi bi-currency-dollar',
        bgClass: 'bg-success',
        description: 'Track non-tuition revenue streams, institutional donations, grant funds, and auxiliary income.',
        route: '/income-reports',
        permission: 'income_reports.view'
    },
    {
        title: 'Student Report Cards',
        category: 'Academics',
        icon: 'bi bi-award',
        bgClass: 'bg-warning text-dark',
        description: 'Academic grade transcripts, term-wise student performance reports, exam marks, and GPA rankings.',
        route: '/report-cards',
        permission: 'report_card.view'
    },
    {
        title: 'Library Reports',
        category: 'Facilities',
        icon: 'bi bi-book',
        bgClass: 'bg-info',
        description: 'Library book circulation statistics, active loans, overdue returns, and fine collection ledgers.',
        route: '/library-reports',
        permission: 'library_reports.view'
    },
    {
        title: 'Transport Reports',
        category: 'Logistics',
        icon: 'bi bi-bus-front',
        bgClass: 'bg-secondary',
        description: 'Vehicle fleet management, route passenger density, fuel consumption, and vehicle maintenance audits.',
        route: '/transport/reports',
        permission: 'transport_reports.view'
    },
    {
        title: 'Hostel Reports',
        category: 'Facilities',
        icon: 'bi bi-buildings',
        bgClass: 'bg-dark',
        description: 'Room occupancy rates, room allocations, student hostel enrollments, and warden duty logs.',
        route: '/hostel/reports',
        permission: 'hostel_reports.view'
    }
]

const availableReports = computed(() => {
    return reports.filter(r => {
        if (!r.permission) return true
        if (authStore.user?.user_type === 'super_admin') return true
        return authStore.hasPermission ? authStore.hasPermission(r.permission) : true
    })
})
</script>

<style scoped>
.report-card {
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.report-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}
</style>
