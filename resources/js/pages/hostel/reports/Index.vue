<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Reports</h4>
            <small class="text-muted">Analytical reports on room occupancy, residents, fee collection, attendance & maintenance</small>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm" @click="printReport">
            <i class="bi bi-printer me-1"></i> Print Report
        </button>
    </div>

    <!-- Report Tabs -->
    <ul class="nav nav-pills mb-3 bg-white p-2 rounded shadow-sm">
        <li class="nav-item">
            <button :class="['nav-link', activeTab === 'occupancy' ? 'active' : '']" @click="switchTab('occupancy')">
                <i class="bi bi-door-open me-1"></i> Room Occupancy
            </button>
        </li>
        <li class="nav-item">
            <button :class="['nav-link', activeTab === 'students' ? 'active' : '']" @click="switchTab('students')">
                <i class="bi bi-people me-1"></i> Resident Students
            </button>
        </li>
        <li class="nav-item">
            <button :class="['nav-link', activeTab === 'fees' ? 'active' : '']" @click="switchTab('fees')">
                <i class="bi bi-cash-coin me-1"></i> Fee Collection
            </button>
        </li>
        <li class="nav-item">
            <button :class="['nav-link', activeTab === 'attendance' ? 'active' : '']" @click="switchTab('attendance')">
                <i class="bi bi-calendar-check me-1"></i> Attendance
            </button>
        </li>
        <li class="nav-item">
            <button :class="['nav-link', activeTab === 'complaints' ? 'active' : '']" @click="switchTab('complaints')">
                <i class="bi bi-megaphone me-1"></i> Complaints & Repairs
            </button>
        </li>
    </ul>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted">Generating report data...</div>
    </div>

    <div v-else id="printArea">
        <!-- 1. Occupancy Report -->
        <div v-if="activeTab === 'occupancy'">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-light">
                        <small class="text-muted">Total Rooms</small>
                        <div class="h4 fw-bold mt-1">{{ summary.total_rooms || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-primary bg-opacity-10">
                        <small class="text-primary fw-semibold">Total Bed Capacity</small>
                        <div class="h4 fw-bold text-primary mt-1">{{ summary.total_capacity || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
                        <small class="text-success fw-semibold">Occupied Beds</small>
                        <div class="h4 fw-bold text-success mt-1">{{ summary.total_occupied || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-info bg-opacity-10">
                        <small class="text-info fw-semibold">Available Beds</small>
                        <div class="h4 fw-bold text-info mt-1">{{ summary.total_available || 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Building</th>
                                <th>Room No</th>
                                <th>Floor</th>
                                <th>Room Type</th>
                                <th>Capacity</th>
                                <th>Occupied</th>
                                <th>Available</th>
                                <th>Monthly Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(r, idx) in reportData" :key="r.id">
                                <td>{{ idx + 1 }}</td>
                                <td><strong>{{ r.building?.name || '-' }}</strong></td>
                                <td>Room {{ r.room_no }}</td>
                                <td>Floor {{ r.floor_no }}</td>
                                <td>{{ r.room_type || 'Standard' }}</td>
                                <td>{{ r.capacity }}</td>
                                <td class="text-danger fw-semibold">{{ r.occupied_beds }}</td>
                                <td class="text-success fw-semibold">{{ Math.max(0, r.capacity - r.occupied_beds) }}</td>
                                <td>₹{{ Number(r.monthly_fee || 0).toLocaleString() }}</td>
                                <td>
                                    <span :class="['badge', r.status ? 'bg-success' : 'bg-secondary']">
                                        {{ r.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. Students Report -->
        <div v-if="activeTab === 'students'">
            <div class="card mb-3">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Active Hostel Residents ({{ reportData.length }})</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Adm No</th>
                                <th>Class</th>
                                <th>Building / Room / Bed</th>
                                <th>Admission Date</th>
                                <th>Food Pref</th>
                                <th>Emergency Contact</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s, idx) in reportData" :key="s.id">
                                <td>{{ idx + 1 }}</td>
                                <td><strong>{{ s.student?.first_name }} {{ s.student?.last_name }}</strong></td>
                                <td><code>{{ s.student?.admission_no }}</code></td>
                                <td>{{ s.student?.class?.name || '-' }}</td>
                                <td>
                                    <div>{{ s.room?.building?.name || '-' }}</div>
                                    <small class="text-muted">Room {{ s.room?.room_no }} • Bed {{ s.bed?.bed_no || '-' }}</small>
                                </td>
                                <td>{{ s.admission_date || '-' }}</td>
                                <td class="text-uppercase small">{{ s.food_preference || 'both' }}</td>
                                <td>
                                    <div class="small">{{ s.emergency_contact_name }}</div>
                                    <small class="text-muted">{{ s.emergency_contact_phone }}</small>
                                </td>
                                <td>
                                    <span :class="['badge text-capitalize', s.status === 'active' ? 'bg-success' : 'bg-secondary']">
                                        {{ s.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Fee Collection Report -->
        <div v-if="activeTab === 'fees'">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-light">
                        <small class="text-muted">Total Invoiced</small>
                        <div class="h4 fw-bold mt-1">₹{{ Number(summary.total_billed || 0).toLocaleString() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
                        <small class="text-success fw-semibold">Total Collected</small>
                        <div class="h4 fw-bold text-success mt-1">₹{{ Number(summary.total_collected || 0).toLocaleString() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-warning bg-opacity-10">
                        <small class="text-warning fw-semibold">Total Due</small>
                        <div class="h4 fw-bold text-warning mt-1">₹{{ Number(summary.total_due || 0).toLocaleString() }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-info bg-opacity-10">
                        <small class="text-info fw-semibold">Total Records</small>
                        <div class="h4 fw-bold text-info mt-1">{{ reportData.length }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Student</th>
                                <th>Fee Type</th>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Paid</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="f in reportData" :key="f.id">
                                <td><code>{{ f.invoice_no }}</code></td>
                                <td>{{ f.student?.first_name }} {{ f.student?.last_name }}</td>
                                <td>{{ f.fee_type }}</td>
                                <td>{{ f.month || '-' }}</td>
                                <td>₹{{ Number(f.amount || 0).toLocaleString() }}</td>
                                <td class="text-success fw-semibold">₹{{ Number(f.paid_amount || 0).toLocaleString() }}</td>
                                <td>{{ f.due_date || '-' }}</td>
                                <td>
                                    <span :class="['badge text-uppercase', f.status === 'paid' ? 'bg-success' : 'bg-warning text-dark']">
                                        {{ f.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. Attendance Report -->
        <div v-if="activeTab === 'attendance'">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-light">
                        <small class="text-muted">Total Marked</small>
                        <div class="h4 fw-bold mt-1">{{ summary.total_records || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
                        <small class="text-success fw-semibold">Present</small>
                        <div class="h4 fw-bold text-success mt-1">{{ summary.present || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-danger bg-opacity-10">
                        <small class="text-danger fw-semibold">Absent</small>
                        <div class="h4 fw-bold text-danger mt-1">{{ summary.absent || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-warning bg-opacity-10">
                        <small class="text-warning fw-semibold">Leave / Late</small>
                        <div class="h4 fw-bold text-warning mt-1">{{ (summary.leave || 0) + (summary.late || 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Adm No</th>
                                <th>Room</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="att in reportData" :key="att.id">
                                <td>{{ att.attendance_date }}</td>
                                <td><strong>{{ att.student?.first_name }} {{ att.student?.last_name }}</strong></td>
                                <td><code>{{ att.student?.admission_no }}</code></td>
                                <td>Room {{ att.room?.room_no || '-' }}</td>
                                <td>
                                    <span :class="['badge text-capitalize', att.status === 'present' ? 'bg-success' : (att.status === 'absent' ? 'bg-danger' : 'bg-warning text-dark')]">
                                        {{ att.status }}
                                    </span>
                                </td>
                                <td><span class="text-muted small">{{ att.remarks || '-' }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Complaints Report -->
        <div v-if="activeTab === 'complaints'">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-light">
                        <small class="text-muted">Total Complaints</small>
                        <div class="h4 fw-bold mt-1">{{ summary.total || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-danger bg-opacity-10">
                        <small class="text-danger fw-semibold">Pending</small>
                        <div class="h4 fw-bold text-danger mt-1">{{ summary.pending || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-primary bg-opacity-10">
                        <small class="text-primary fw-semibold">In Progress</small>
                        <div class="h4 fw-bold text-primary mt-1">{{ summary.in_progress || 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
                        <small class="text-success fw-semibold">Resolved / Closed</small>
                        <div class="h4 fw-bold text-success mt-1">{{ (summary.resolved || 0) + (summary.closed || 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Student</th>
                                <th>Room</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Resolution Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="cp in reportData" :key="cp.id">
                                <td><strong>{{ cp.title }}</strong></td>
                                <td>{{ cp.category }}</td>
                                <td>{{ cp.student?.first_name }} {{ cp.student?.last_name }}</td>
                                <td>Room {{ cp.room?.room_no || '-' }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ cp.priority }}</span></td>
                                <td>
                                    <span :class="['badge text-capitalize', cp.status === 'resolved' ? 'bg-success' : (cp.status === 'pending' ? 'bg-danger' : 'bg-warning text-dark')]">
                                        {{ cp.status.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td><span class="text-muted small">{{ cp.resolution_notes || '-' }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const activeTab = ref('occupancy');
const loading = ref(false);
const reportData = ref([]);
const summary = ref({});

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadReport = async () => {
    loading.value = true;
    try {
        let endpoint = `/api/hostel/reports/${activeTab.value}`;
        const res = await axios.get(endpoint, getAuthConfig());
        if (res.data?.success) {
            reportData.value = res.data.data || [];
            summary.value = res.data.summary || {};
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const switchTab = (tab) => {
    activeTab.value = tab;
    loadReport();
};

const printReport = () => {
    window.print();
};

onMounted(() => {
    loadReport();
});
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
