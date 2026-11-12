<template>
<div class="container-fluid py-2">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Hostel' },
            { label: 'Dashboard' }
        ]"
    />
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary"><i class="bi bi-house-door-fill me-2"></i>Hostel Dashboard</h4>
            <small class="text-muted">Overview of hostel capacity, occupancy, residents, and activities</small>
        </div>
        <div>
            <button class="btn btn-outline-primary btn-sm me-2" @click="loadData">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
            <router-link to="/hostel/allocations" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus-fill me-1"></i> Allocate Room
            </router-link>
        </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted">Loading hostel dashboard...</div>
    </div>

    <div v-else>
        <!-- Stat Cards Row 1 -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Hostels & Buildings</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.hostels || 0 }} <span class="fs-6 text-muted fw-normal">Hostels</span> / {{ stats.counts?.buildings || 0 }} <span class="fs-6 text-muted fw-normal">Bldgs</span>
                                </div>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                <i class="bi bi-building fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Bed Occupancy</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.occupied_beds || 0 }} / {{ stats.counts?.total_capacity || 0 }}
                                </div>
                                <small class="text-success fw-semibold">
                                    {{ stats.counts?.occupancy_rate || 0 }}% Occupied ({{ stats.counts?.available_beds || 0 }} Available)
                                </small>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                <i class="bi bi-door-open fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Hostel Residents</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.students || 0 }}
                                </div>
                                <small class="text-muted">Active resident students</small>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                                <i class="bi bi-people-fill fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Pending Complaints</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.pending_complaints || 0 }}
                                </div>
                                <small class="text-warning fw-semibold">Awaiting resolution</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Modules Grid -->
        <div class="row g-3 mb-4">
            <div class="col-md-2 col-4" v-for="mod in quickModules" :key="mod.route">
                <router-link :to="mod.route" class="card shadow-sm border-0 text-decoration-none text-center h-100 p-3 hover-lift">
                    <div :class="['mx-auto rounded-circle d-flex align-items-center justify-content-center mb-2', mod.bgClass]" style="width: 48px; height: 48px;">
                        <i :class="[mod.icon, 'fs-4', mod.textClass]"></i>
                    </div>
                    <div class="fw-semibold text-dark small">{{ mod.title }}</div>
                </router-link>
            </div>
        </div>

        <!-- Attendance & Fees Row -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check me-2 text-primary"></i>Today's Attendance</h6>
                        <router-link to="/hostel/attendance" class="btn btn-sm btn-outline-primary">Mark Attendance</router-link>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 my-2">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small">Total Marked</div>
                                    <div class="h4 fw-bold text-dark mt-1">{{ stats.attendance?.total_marked || 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <div class="text-success small fw-semibold">Present</div>
                                    <div class="h4 fw-bold text-success mt-1">{{ stats.attendance?.present || 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-danger bg-opacity-10 rounded">
                                    <div class="text-danger small fw-semibold">Absent</div>
                                    <div class="h4 fw-bold text-danger mt-1">{{ stats.attendance?.absent || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-success"></i>Monthly Hostel Fees</h6>
                        <router-link to="/hostel/fees" class="btn btn-sm btn-outline-success">Manage Fees</router-link>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 my-2">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small">Total Billed</div>
                                    <div class="h5 fw-bold text-dark mt-1">₹{{ Number(stats.fees?.total_billed || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <div class="text-success small fw-semibold">Collected</div>
                                    <div class="h5 fw-bold text-success mt-1">₹{{ Number(stats.fees?.total_collected || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <div class="text-warning small fw-semibold">Pending</div>
                                    <div class="h5 fw-bold text-warning mt-1">₹{{ Number(stats.fees?.pending || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Lists -->
        <div class="row g-3">
            <!-- Recent Allocations -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Recent Allocations</h6>
                        <router-link to="/hostel/allocations" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.recent_allocations" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark">{{ item.student?.first_name }} {{ item.student?.last_name }}</div>
                                    <small class="text-muted">Room {{ item.room?.room_no }} • Bed {{ item.bed?.bed_no || '-' }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ item.allocated_date }}</span>
                            </li>
                            <li v-if="!stats.recent_allocations?.length" class="list-group-item text-center text-muted py-4">No recent allocations</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Visitors -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-info"></i>Recent Visitors</h6>
                        <router-link to="/hostel/visitors" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.recent_visitors" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark">{{ item.visitor_name }} ({{ item.relation }})</div>
                                    <small class="text-muted">Visiting {{ item.student?.first_name }} {{ item.student?.last_name }}</small>
                                </div>
                                <span :class="['badge rounded-pill', item.status === 'inside' ? 'bg-success' : 'bg-secondary']">
                                    {{ item.status === 'inside' ? 'Inside' : 'Out' }}
                                </span>
                            </li>
                            <li v-if="!stats.recent_visitors?.length" class="list-group-item text-center text-muted py-4">No recent visitors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Complaints -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-chat-left-dots me-2 text-warning"></i>Recent Complaints</h6>
                        <router-link to="/hostel/complaints" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.recent_complaints" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">{{ item.title }}</div>
                                    <small class="text-muted">{{ item.category }} • Room {{ item.room?.room_no || '-' }}</small>
                                </div>
                                <span :class="['badge rounded-pill', item.status === 'resolved' ? 'bg-success' : (item.status === 'pending' ? 'bg-danger' : 'bg-warning')]">
                                    {{ item.status }}
                                </span>
                            </li>
                            <li v-if="!stats.recent_complaints?.length" class="list-group-item text-center text-muted py-4">No recent complaints</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue';

const loading = ref(true);
const stats = ref({});

const quickModules = [
    { title: 'Hostels', route: '/hostel/hostels', icon: 'bi bi-house', bgClass: 'bg-primary bg-opacity-10', textClass: 'text-primary' },
    { title: 'Buildings', route: '/hostel/buildings', icon: 'bi bi-building', bgClass: 'bg-info bg-opacity-10', textClass: 'text-info' },
    { title: 'Floors', route: '/hostel/floors', icon: 'bi bi-layers', bgClass: 'bg-secondary bg-opacity-10', textClass: 'text-secondary' },
    { title: 'Rooms', route: '/hostel/rooms', icon: 'bi bi-door-closed', bgClass: 'bg-success bg-opacity-10', textClass: 'text-success' },
    { title: 'Beds', route: '/hostel/beds', icon: 'bi bi-grid-3x3', bgClass: 'bg-dark bg-opacity-10', textClass: 'text-dark' },
    { title: 'Students', route: '/hostel/students', icon: 'bi bi-person-lines-fill', bgClass: 'bg-warning bg-opacity-10', textClass: 'text-warning' },
    { title: 'Allocations', route: '/hostel/allocations', icon: 'bi bi-key', bgClass: 'bg-primary bg-opacity-10', textClass: 'text-primary' },
    { title: 'Transfers', route: '/hostel/transfers', icon: 'bi bi-arrow-left-right', bgClass: 'bg-info bg-opacity-10', textClass: 'text-info' },
    { title: 'Fees', route: '/hostel/fees', icon: 'bi bi-cash-coin', bgClass: 'bg-success bg-opacity-10', textClass: 'text-success' },
    { title: 'Attendance', route: '/hostel/attendance', icon: 'bi bi-calendar2-check', bgClass: 'bg-danger bg-opacity-10', textClass: 'text-danger' },
    { title: 'Complaints', route: '/hostel/complaints', icon: 'bi bi-megaphone', bgClass: 'bg-warning bg-opacity-10', textClass: 'text-warning' },
    { title: 'Visitors', route: '/hostel/visitors', icon: 'bi bi-person-badge', bgClass: 'bg-secondary bg-opacity-10', textClass: 'text-secondary' },
];

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || localStorage.getItem('access_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadData = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/hostel/dashboard', getAuthConfig());
        if (res.data?.success) {
            stats.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to load hostel dashboard:', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadData();
});
</script>

<style scoped>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
