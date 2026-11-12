<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-primary"><i class="bi bi-journal-bookmark-fill me-2"></i>Library Dashboard</h4>
            <small class="text-muted">Overview of library circulation, collection capacity, members, and fine revenue</small>
        </div>
        <div>
            <button class="btn btn-outline-primary btn-sm me-2" @click="loadData">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
            <router-link to="/book-issues" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-journal-plus me-1"></i> Issue Book
            </router-link>
            <router-link to="/book-returns" class="btn btn-success btn-sm">
                <i class="bi bi-journal-arrow-down me-1"></i> Return Book
            </router-link>
        </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted">Loading library dashboard...</div>
    </div>

    <div v-else>
        <!-- Stat Cards Row 1 -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Books & Titles</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.total_books || 0 }} <span class="fs-6 text-muted fw-normal">Titles</span>
                                </div>
                                <small class="text-muted">{{ stats.counts?.total_copies || 0 }} total physical copies</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                <i class="bi bi-book-half fs-3"></i>
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
                                <div class="text-muted small text-uppercase fw-semibold">Copies Availability</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.available_copies || 0 }} <span class="fs-6 text-success fw-normal">Available</span>
                                </div>
                                <small class="text-muted">
                                    {{ stats.counts?.issued_copies || 0 }} currently issued / out
                                </small>
                            </div>
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                <i class="bi bi-check2-circle fs-3"></i>
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
                                <div class="text-muted small text-uppercase fw-semibold">Library Members</div>
                                <div class="h3 mb-0 fw-bold text-dark mt-1">
                                    {{ stats.counts?.total_members || 0 }}
                                </div>
                                <small class="text-info fw-semibold">{{ stats.counts?.active_members || 0 }} active cards</small>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                                <i class="bi bi-person-badge fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Overdue Circulation</div>
                                <div class="h3 mb-0 fw-bold text-danger mt-1">
                                    {{ stats.counts?.overdue_issues || 0 }}
                                </div>
                                <small class="text-danger fw-semibold">Books overdue for return</small>
                            </div>
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                                <i class="bi bi-alarm fs-3"></i>
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

        <!-- Circulation & Fines Row -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Circulation Summary</h6>
                        <router-link to="/book-issues" class="btn btn-sm btn-outline-primary">Issue Book</router-link>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 my-2">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small">Total Issued</div>
                                    <div class="h4 fw-bold text-dark mt-1">{{ stats.counts?.active_issues || 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <div class="text-success small fw-semibold">Total Returned</div>
                                    <div class="h4 fw-bold text-success mt-1">{{ stats.counts?.total_returned || 0 }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-info bg-opacity-10 rounded">
                                    <div class="text-info small fw-semibold">Total Renewals</div>
                                    <div class="h4 fw-bold text-info mt-1">{{ stats.counts?.total_renewals || 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2 text-success"></i>Fines & Penalty Collection</h6>
                        <router-link to="/fine-collections" class="btn btn-sm btn-outline-success">Manage Collections</router-link>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 my-2">
                            <div class="col-4">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small">Total Fines</div>
                                    <div class="h5 fw-bold text-dark mt-1">₹{{ Number(stats.fines?.total_billed || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <div class="text-success small fw-semibold">Collected</div>
                                    <div class="h5 fw-bold text-success mt-1">₹{{ Number(stats.fines?.total_collected || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <div class="text-warning small fw-semibold">Pending</div>
                                    <div class="h5 fw-bold text-warning mt-1">₹{{ Number(stats.fines?.pending || 0).toLocaleString() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Lists -->
        <div class="row g-3">
            <!-- Recent Issues -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-plus me-2 text-primary"></i>Recent Issues</h6>
                        <router-link to="/book-issues" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.recent_issues" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;">{{ item.book?.title }}</div>
                                    <small class="text-muted">
                                        {{ item.library_member?.student ? (item.library_member.student.first_name + ' ' + item.library_member.student.last_name) : (item.library_member?.staff ? (item.library_member.staff.first_name + ' ' + item.library_member.staff.last_name) : 'Member') }}
                                        • Barcode: {{ item.book_copy?.barcode || '-' }}
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ item.issue_date }}</span>
                            </li>
                            <li v-if="!stats.recent_issues?.length" class="list-group-item text-center text-muted py-4">No recent issues</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Overdue Books -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-danger"></i>Overdue Books</h6>
                        <router-link to="/book-issues" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.overdue_list" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 200px;">{{ item.book?.title }}</div>
                                    <small class="text-muted">
                                        {{ item.library_member?.student ? (item.library_member.student.first_name + ' ' + item.library_member.student.last_name) : 'Member' }}
                                    </small>
                                </div>
                                <span class="badge bg-danger rounded-pill">Due {{ item.due_date }}</span>
                            </li>
                            <li v-if="!stats.overdue_list?.length" class="list-group-item text-center text-muted py-4">No overdue books</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Collections -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-success"></i>Recent Collections</h6>
                        <router-link to="/fine-collections" class="small text-decoration-none">View All</router-link>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li v-for="item in stats.recent_collections" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold text-dark">{{ item.fine?.fine_no }} ({{ item.fine?.fine_type }})</div>
                                    <small class="text-muted">
                                        {{ item.member?.student ? (item.member.student.first_name + ' ' + item.member.student.last_name) : 'Member' }}
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill">₹{{ Number(item.amount || 0).toLocaleString() }}</span>
                            </li>
                            <li v-if="!stats.recent_collections?.length" class="list-group-item text-center text-muted py-4">No recent collections</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import axios from 'axios';

const loading = ref(true);
const stats = ref({});

const quickModules = [
    { title: 'Categories', route: '/book-categories', icon: 'bi bi-tags', bgClass: 'bg-primary bg-opacity-10', textClass: 'text-primary' },
    { title: 'Authors', route: '/authors', icon: 'bi bi-pen', bgClass: 'bg-info bg-opacity-10', textClass: 'text-info' },
    { title: 'Publishers', route: '/publishers', icon: 'bi bi-buildings', bgClass: 'bg-secondary bg-opacity-10', textClass: 'text-secondary' },
    { title: 'Books', route: '/books', icon: 'bi bi-book', bgClass: 'bg-success bg-opacity-10', textClass: 'text-success' },
    { title: 'Book Copies', route: '/book-copies', icon: 'bi bi-upc-scan', bgClass: 'bg-dark bg-opacity-10', textClass: 'text-dark' },
    { title: 'Members', route: '/library-members', icon: 'bi bi-people-fill', bgClass: 'bg-warning bg-opacity-10', textClass: 'text-warning' },
    { title: 'Book Issues', route: '/book-issues', icon: 'bi bi-journal-plus', bgClass: 'bg-primary bg-opacity-10', textClass: 'text-primary' },
    { title: 'Book Returns', route: '/book-returns', icon: 'bi bi-journal-arrow-down', bgClass: 'bg-success bg-opacity-10', textClass: 'text-success' },
    { title: 'Renewals', route: '/book-renewals', icon: 'bi bi-arrow-repeat', bgClass: 'bg-info bg-opacity-10', textClass: 'text-info' },
    { title: 'Lost/Damaged', route: '/lost-damaged-books', icon: 'bi bi-exclamation-octagon', bgClass: 'bg-danger bg-opacity-10', textClass: 'text-danger' },
    { title: 'Fines', route: '/library-fines', icon: 'bi bi-wallet2', bgClass: 'bg-warning bg-opacity-10', textClass: 'text-warning' },
    { title: 'Collection', route: '/fine-collections', icon: 'bi bi-receipt', bgClass: 'bg-success bg-opacity-10', textClass: 'text-success' },
];

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || localStorage.getItem('access_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    const activeSchoolId = localStorage.getItem('active_school_id');
    if (activeSchoolId) config.headers['X-School-Id'] = activeSchoolId;
    return config;
};

const loadData = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/library/dashboard', getAuthConfig());
        if (res.data?.success) {
            stats.value = res.data.data;
        }
    } catch (e) {
        console.error('Failed to load library dashboard:', e);
    } finally {
        loading.value = false;
    }
};

const onTenantChanged = () => {
    loadData();
};

onMounted(() => {
    loadData();
    window.addEventListener('tenant-switched', onTenantChanged);
});

onBeforeUnmount(() => {
    window.removeEventListener('tenant-switched', onTenantChanged);
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
