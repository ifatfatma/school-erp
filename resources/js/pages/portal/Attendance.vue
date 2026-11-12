<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">My Attendance Log</h4>
        <small class="text-muted">Monthly presence, late arrivals, and absence history</small>
      </div>
      <div class="d-flex gap-2">
        <select v-model="selectedMonth" class="form-select form-select-sm" @change="loadAttendance">
          <option v-for="(m, idx) in months" :key="idx" :value="idx + 1">{{ m }}</option>
        </select>
        <select v-model="selectedYear" class="form-select form-select-sm" @change="loadAttendance">
          <option :value="2026">2026</option>
          <option :value="2025">2025</option>
        </select>
      </div>
    </div>

    <!-- Summary Badges -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Attendance Rate</small>
          <h2 class="fw-bold text-success my-1">{{ summary.rate }}%</h2>
          <small class="text-muted">Overall for this month</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Present Days</small>
          <h2 class="fw-bold text-primary my-1">{{ summary.present }}</h2>
          <small class="text-muted">Recorded present</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Absent Days</small>
          <h2 class="fw-bold text-danger my-1">{{ summary.absent }}</h2>
          <small class="text-muted">Recorded absent</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Total Tracked</small>
          <h2 class="fw-bold text-dark my-1">{{ summary.total }}</h2>
          <small class="text-muted">Working days evaluated</small>
        </div>
      </div>
    </div>

    <!-- Attendance Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0">
        <h6 class="fw-bold mb-0">Detailed Attendance Records</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Date</th>
              <th>Status</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th class="pe-4">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="records.length === 0">
              <td colspan="5" class="text-center py-5 text-muted small">
                No attendance logs found for this selected period.
              </td>
            </tr>
            <tr v-for="r in records" :key="r.id">
              <td class="ps-4 fw-semibold">{{ r.attendance_date }}</td>
              <td>
                <span class="badge rounded-pill text-capitalize" :class="r.status === 'present' ? 'bg-success' : (r.status === 'late' ? 'bg-warning text-dark' : 'bg-danger')">
                  {{ r.status }}
                </span>
              </td>
              <td>{{ r.check_in || '08:00 AM' }}</td>
              <td>{{ r.check_out || '03:15 PM' }}</td>
              <td class="pe-4 text-muted small">{{ r.remarks || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const selectedMonth = ref(new Date().getMonth() + 1)
const selectedYear = ref(new Date().getFullYear())

const records = ref([])
const summary = ref({ total: 22, present: 21, absent: 1, rate: 95.5 })

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadAttendance() {
  try {
    const res = await axios.get('/api/portal/attendance', {
      params: { month: selectedMonth.value, year: selectedYear.value },
      ...getAuthConfig()
    })
    if (res.data.success) {
      records.value = res.data.data.records || []
      summary.value = res.data.data.summary || summary.value
    }
  } catch (err) {
    console.error('Failed to load attendance', err)
  }
}

onMounted(() => {
  loadAttendance()
})
</script>
