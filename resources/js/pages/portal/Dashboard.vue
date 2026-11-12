<template>
  <div>
    <!-- Welcome Header Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4 mb-4">
      <div class="row align-items-center g-3">
        <div class="col-md-8 d-flex align-items-center gap-3">
          <div class="bg-white text-primary rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 64px; height: 64px;">
            <i class="bi bi-person-fill fs-2"></i>
          </div>
          <div>
            <h4 class="fw-bold mb-1">Welcome back, {{ student?.full_name || 'Student' }}!</h4>
            <p class="mb-0 text-white-50 small">
              Admission No: <strong class="text-white">{{ student?.admission_no }}</strong> | 
              Class: <strong class="text-white">{{ student?.class?.name || 'Class 1' }}</strong> - 
              Section: <strong class="text-white">{{ student?.section?.name || 'A' }}</strong> |
              Roll: <strong class="text-white">#{{ student?.roll_no || '1' }}</strong>
            </p>
          </div>
        </div>
        <div class="col-md-4 text-md-end">
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">
            <i class="bi bi-calendar-event me-1"></i> {{ currentDate }}
          </span>
        </div>
      </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase">Attendance</small>
            <span class="badge bg-success-subtle text-success rounded-pill px-2">This Month</span>
          </div>
          <h3 class="fw-bold mb-0 text-success">{{ stats.attendance_percentage }}%</h3>
          <small class="text-muted">{{ stats.present_days }} present / {{ stats.total_attendance_days }} days</small>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase">Fee Balance</small>
            <span class="badge bg-warning-subtle text-dark rounded-pill px-2">Dues</span>
          </div>
          <h3 class="fw-bold mb-0 text-dark">₹{{ Number(stats.pending_balance).toLocaleString('en-IN') }}</h3>
          <small class="text-muted">Paid: ₹{{ Number(stats.paid_fee).toLocaleString('en-IN') }}</small>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase">Library Books</small>
            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2">Active</span>
          </div>
          <h3 class="fw-bold mb-0 text-primary">{{ stats.active_books_count }}</h3>
          <small class="text-muted">Books currently issued</small>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase">Academic Status</small>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2">Active</span>
          </div>
          <h3 class="fw-bold mb-0 text-dark">Regular</h3>
          <small class="text-muted">Session 2026-27</small>
        </div>
      </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
      <!-- Recent Attendance -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
          <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-calendar2-check me-2 text-primary"></i> Recent Attendance Log</h6>
            <router-link to="/portal/attendance" class="btn btn-sm btn-link text-decoration-none">Full History &rarr;</router-link>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                  <tr>
                    <th class="ps-4">Date</th>
                    <th>Status</th>
                    <th>Check In</th>
                    <th class="pe-4">Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="recentAttendance.length === 0">
                    <td colspan="4" class="text-center py-4 text-muted small">No recent attendance logged yet.</td>
                  </tr>
                  <tr v-for="att in recentAttendance" :key="att.id">
                    <td class="ps-4 fw-semibold">{{ att.attendance_date }}</td>
                    <td>
                      <span class="badge rounded-pill text-capitalize" :class="att.status === 'present' ? 'bg-success' : (att.status === 'late' ? 'bg-warning text-dark' : 'bg-danger')">
                        {{ att.status }}
                      </span>
                    </td>
                    <td class="small">{{ att.check_in || '08:00 AM' }}</td>
                    <td class="pe-4 small text-muted">{{ att.remarks || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Fee Dues -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
          <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i> Pending Fee Dues</h6>
            <router-link to="/portal/fees" class="btn btn-sm btn-link text-decoration-none">View All Dues &rarr;</router-link>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                  <tr>
                    <th class="ps-4">Fee Description</th>
                    <th>Due Date</th>
                    <th>Amount</th>
                    <th class="pe-4">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="upcomingDues.length === 0">
                    <td colspan="4" class="text-center py-4 text-success small">
                      <i class="bi bi-check-circle me-1"></i> All fees are currently up to date!
                    </td>
                  </tr>
                  <tr v-for="due in upcomingDues" :key="due.id">
                    <td class="ps-4 fw-semibold">{{ due.fee_type?.name || due.period || 'Tuition Fee' }}</td>
                    <td class="small text-muted">{{ due.due_date }}</td>
                    <td class="fw-bold">₹{{ Number(due.balance_amount).toLocaleString('en-IN') }}</td>
                    <td class="pe-4">
                      <span class="badge rounded-pill bg-warning text-dark text-capitalize">{{ due.status }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const student = ref(null)
const stats = ref({
  attendance_percentage: 95.0,
  total_attendance_days: 22,
  present_days: 21,
  absent_days: 1,
  total_fee: 0,
  paid_fee: 0,
  pending_balance: 0,
  active_books_count: 0,
})
const recentAttendance = ref([])
const upcomingDues = ref([])
const currentDate = new Date().toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadDashboard() {
  try {
    const res = await axios.get('/api/portal/dashboard', getAuthConfig())
    if (res.data.success) {
      student.value = res.data.data.student
      stats.value = res.data.data.stats
      recentAttendance.value = res.data.data.recent_attendance || []
      upcomingDues.value = res.data.data.upcoming_dues || []
    }
  } catch (err) {
    console.error('Failed to load portal dashboard', err)
  }
}

onMounted(() => {
  loadDashboard()
})
</script>
