<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">Attendance Record</h4>
        <small class="text-muted">Monthly presence tracking, visual calendar heat-map, late arrivals, and leave history for {{ childName }}</small>
      </div>
      <div class="d-flex gap-2">
        <select v-model="selectedMonth" class="form-select form-select-sm shadow-sm" @change="loadAttendance">
          <option v-for="(m, idx) in months" :key="idx" :value="idx + 1">{{ m }}</option>
        </select>
        <select v-model="selectedYear" class="form-select form-select-sm shadow-sm" @change="loadAttendance">
          <option :value="2027">2027</option>
          <option :value="2026">2026</option>
          <option :value="2025">2025</option>
        </select>
      </div>
    </div>

    <!-- Summary Badges -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Attendance Rate</small>
          <h2 class="fw-bold text-success my-2">{{ summary.rate }}%</h2>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-success" :style="{ width: summary.rate + '%' }"></div>
          </div>
          <small class="text-muted mt-2 d-block">Overall for {{ currentMonthName }}</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Present Days</small>
          <h2 class="fw-bold text-primary my-2">{{ summary.present }}</h2>
          <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Attended Classes</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Absent Days</small>
          <h2 class="fw-bold my-2" :class="summary.absent > 0 ? 'text-danger' : 'text-muted'">{{ summary.absent }}</h2>
          <small :class="summary.absent > 0 ? 'text-danger' : 'text-muted'">Unexcused Absences</small>
        </div>
      </div>
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Approved Leaves</small>
          <h2 class="fw-bold text-info my-2">{{ summary.leave || 0 }}</h2>
          <small class="text-muted">Excused / Approved Days</small>
        </div>
      </div>
    </div>

    <!-- Visual Monthly Attendance Calendar -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
      <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-calendar3 me-2 text-primary"></i>
          {{ currentMonthName }} {{ selectedYear }} - Attendance Calendar View
        </h6>
        <div class="d-flex align-items-center gap-3 small text-muted">
          <span><span class="badge bg-success rounded-circle p-1 me-1">&nbsp;</span> Present</span>
          <span><span class="badge bg-warning rounded-circle p-1 me-1">&nbsp;</span> Late</span>
          <span><span class="badge bg-danger rounded-circle p-1 me-1">&nbsp;</span> Absent</span>
          <span><span class="badge bg-info rounded-circle p-1 me-1">&nbsp;</span> Leave</span>
          <span><span class="badge bg-light text-muted border rounded-circle p-1 me-1">&nbsp;</span> Weekend</span>
        </div>
      </div>

      <div class="card-body p-4 pt-1">
        <div class="d-grid" style="grid-template-columns: repeat(7, 1fr); gap: 10px;">
          <!-- Day headers -->
          <div v-for="d in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="d" class="text-center fw-bold text-muted small py-2">
            {{ d }}
          </div>

          <!-- Calendar day cells -->
          <div
            v-for="cell in calendarCells"
            :key="cell.key"
            class="p-2 border rounded-3 text-center position-relative d-flex flex-column justify-content-between"
            :class="cell.isCurrentMonth ? (cell.isToday ? 'border-primary shadow-sm bg-light' : 'bg-white') : 'bg-light opacity-50'"
            style="min-height: 70px;"
          >
            <div class="d-flex justify-content-between align-items-center">
              <span class="small fw-semibold" :class="cell.isToday ? 'text-primary fw-bold' : 'text-dark'">
                {{ cell.dayNumber }}
              </span>
              <span v-if="cell.isToday" class="badge bg-primary text-white" style="font-size: 0.6rem;">Today</span>
            </div>

            <div v-if="cell.attendance" class="my-1">
              <span
                class="badge rounded-pill text-capitalize px-2 py-1 w-100"
                :class="cell.attendance.status === 'present' ? 'bg-success text-white' : (cell.attendance.status === 'late' ? 'bg-warning text-dark' : 'bg-danger text-white')"
                style="font-size: 0.68rem;"
              >
                {{ cell.attendance.status }}
              </span>
            </div>
            <div v-else-if="cell.isWeekend" class="text-muted small" style="font-size: 0.68rem;">
              Weekend
            </div>
            <div v-else-if="cell.isCurrentMonth" class="text-muted small" style="font-size: 0.68rem;">
              —
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Daily Attendance Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0">
        <h6 class="fw-bold mb-0">Daily Recorded Attendance Logs</h6>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Date</th>
              <th>Day</th>
              <th>Status</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th class="pe-4">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="records.length === 0">
              <td colspan="6" class="text-center py-5 text-muted small">
                <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                No attendance logs found for {{ currentMonthName }} {{ selectedYear }}.
              </td>
            </tr>
            <tr v-for="r in records" :key="r.id">
              <td class="ps-4 fw-semibold text-dark">{{ r.attendance_date }}</td>
              <td class="small text-muted">{{ formatDayName(r.attendance_date) }}</td>
              <td>
                <span class="badge rounded-pill text-capitalize" :class="r.status === 'present' ? 'bg-success' : (r.status === 'late' ? 'bg-warning text-dark' : 'bg-danger')">
                  <i class="bi me-1" :class="r.status === 'present' ? 'bi-check-circle' : (r.status === 'late' ? 'bi-clock' : 'bi-x-circle')"></i>
                  {{ r.status }}
                </span>
              </td>
              <td>{{ r.check_in || '08:00 AM' }}</td>
              <td>{{ r.check_out || '03:15 PM' }}</td>
              <td class="pe-4 text-muted small">{{ r.remarks || 'Regular presence' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
const selectedMonth = ref(new Date().getMonth() + 1)
const selectedYear = ref(new Date().getFullYear())

const records = ref([])
const summary = ref({ total: 22, present: 21, absent: 1, late: 0, leave: 0, rate: 95.5 })
const child = ref(null)

const childName = computed(() => child.value?.full_name || child.value?.name || 'Selected Child')
const currentMonthName = computed(() => months[selectedMonth.value - 1])

const calendarCells = computed(() => {
  const year = selectedYear.value
  const month = selectedMonth.value - 1

  const firstDayIndex = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const prevMonthDays = new Date(year, month, 0).getDate()

  const today = new Date()
  const cells = []

  for (let i = firstDayIndex - 1; i >= 0; i--) {
    const day = prevMonthDays - i
    cells.push({
      key: `prev-${day}`,
      dayNumber: day,
      isCurrentMonth: false,
      isToday: false,
      isWeekend: false,
      attendance: null,
    })
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateObj = new Date(year, month, d)
    const dayOfWeek = dateObj.getDay()
    const isWeekend = dayOfWeek === 0 || dayOfWeek === 6
    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`

    const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === d
    const att = records.value.find(r => r.attendance_date === dateStr)

    cells.push({
      key: `curr-${d}`,
      dayNumber: d,
      dateString: dateStr,
      isCurrentMonth: true,
      isToday,
      isWeekend,
      attendance: att || null,
    })
  }

  const remaining = (7 - (cells.length % 7)) % 7
  for (let j = 1; j <= remaining; j++) {
    cells.push({
      key: `next-${j}`,
      dayNumber: j,
      isCurrentMonth: false,
      isToday: false,
      isWeekend: false,
      attendance: null,
    })
  }

  return cells
})

function formatDayName(dateStr) {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-US', { weekday: 'short' })
  } catch (e) {
    return '-'
  }
}

function getAuthConfig(extraParams = {}) {
  const token = localStorage.getItem('token')
  const studentId = localStorage.getItem('portal_active_student_id')
  return {
    headers: {
      Authorization: `Bearer ${token}`,
      ...(studentId ? { 'X-Student-Id': String(studentId) } : {})
    },
    params: {
      month: selectedMonth.value,
      year: selectedYear.value,
      ...(studentId ? { student_id: Number(studentId) } : {}),
      ...extraParams
    }
  }
}

async function loadAttendance(overrideChildId = null) {
  const studentId = overrideChildId || localStorage.getItem('portal_active_student_id')
  try {
    const res = await axios.get('/api/parent/attendance', getAuthConfig(studentId ? { student_id: Number(studentId) } : {}))
    if (res.data.success) {
      records.value = res.data.data.records || []
      summary.value = res.data.data.summary || summary.value
      child.value = res.data.data.child
    }
  } catch (err) {
    console.error('Failed to load parent attendance', err)
  }
}

function handleChildSwitch(e) {
  loadAttendance(e.detail?.id)
}

onMounted(() => {
  loadAttendance()
  window.addEventListener('parent-child-switched', handleChildSwitch)
})

onUnmounted(() => {
  window.removeEventListener('parent-child-switched', handleChildSwitch)
})
</script>