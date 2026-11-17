<template>
  <div>
    <!-- All Children Summary View (Visible if multiple children) -->
    <div v-if="allChildren.length > 1" class="mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i>All Children Overview
          </h5>
          <small class="text-muted">Summary of all {{ allChildren.length }} children enrolled under your guardian account</small>
        </div>
      </div>

      <div class="row g-3">
        <div v-for="ch in allChildren" :key="ch.id" class="col-md-6 col-lg-4">
          <div
            class="card border rounded-4 p-3 bg-white h-100 shadow-sm transition-all"
            :class="{ 'border-primary border-2 shadow': Number(ch.id) === Number(activeChild?.id) }"
          >
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="d-flex align-items-center gap-2">
                <div
                  class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                  :class="Number(ch.id) === Number(activeChild?.id) ? 'bg-primary text-white' : 'bg-primary-subtle text-primary'"
                  style="width: 40px; height: 40px;"
                >
                  {{ ch.name?.charAt(0) || 'C' }}
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark">{{ ch.name }}</h6>
                  <small class="text-muted">{{ ch.class?.name || ch.class_name || 'Class' }} - {{ ch.section?.name || ch.section_name || 'A' }} &bull; Roll #{{ ch.roll_no || '1' }}</small>
                </div>
              </div>
              <span v-if="Number(ch.id) === Number(activeChild?.id)" class="badge bg-primary rounded-pill px-2 py-1 small">
                ACTIVE
              </span>
            </div>

            <div class="bg-light rounded-3 p-2 my-2 small">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Attendance:</span>
                <strong class="text-success">{{ ch.attendance_rate }}%</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Fee Due:</span>
                <strong :class="ch.pending_balance > 0 ? 'text-danger' : 'text-success'">
                  {{ ch.pending_balance > 0 ? '₹' + Number(ch.pending_balance).toLocaleString('en-IN') : 'Cleared' }}
                </strong>
              </div>
            </div>

            <button
              v-if="Number(ch.id) !== Number(activeChild?.id)"
              class="btn btn-sm btn-outline-primary rounded-pill w-100 mt-2"
              @click="switchChildProfile(ch)"
            >
              <i class="bi bi-arrow-left-right me-1"></i> Switch to {{ ch.name?.split(' ')[0] }}
            </button>
            <div v-else class="text-center small text-primary fw-semibold mt-2">
              <i class="bi bi-check-circle-fill me-1"></i> Currently Viewing
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Child Hero Banner -->
    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-4 mb-4">
      <div class="row align-items-center g-3">
        <div class="col-md-8 d-flex align-items-center gap-3">
          <div class="bg-white text-primary rounded-circle p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 68px; height: 68px;">
            <i class="bi bi-person-fill fs-1"></i>
          </div>
          <div>
            <div class="badge bg-white text-primary rounded-pill px-3 py-1 mb-1 fw-bold small">
              PARENT PORTAL &bull; {{ activeChild?.school?.name || 'School ERP' }}
            </div>
            <h4 class="fw-bold mb-1">{{ activeChild?.name || 'Student' }}</h4>
            <p class="mb-0 text-white-50 small">
              Admission No: <strong class="text-white">{{ activeChild?.admission_no || '—' }}</strong> | 
              Class: <strong class="text-white">{{ activeChild?.class?.name || 'Class 1' }}</strong> - 
              Section: <strong class="text-white">{{ activeChild?.section?.name || 'A' }}</strong> |
              Roll: <strong class="text-white">#{{ activeChild?.roll_no || '1' }}</strong>
            </p>
          </div>
        </div>
        <div class="col-md-4 text-md-end">
          <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold shadow-sm">
            <i class="bi bi-calendar-event me-1"></i> {{ currentDate }}
          </span>
        </div>
      </div>
    </div>

    <!-- Active Child Quick Stats Grid -->
    <div class="row g-3 mb-4">
      <!-- Attendance % -->
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Attendance Rate</small>
            <span class="badge bg-success-subtle text-success rounded-pill px-2">This Term</span>
          </div>
          <h3 class="fw-bold mb-1 text-success">{{ stats.attendance_percentage }}%</h3>
          <small class="text-muted">{{ stats.present_days }} Present / {{ stats.total_days }} Tracked</small>
          <div class="mt-2">
            <router-link to="/parent/attendance" class="text-primary small text-decoration-none fw-semibold">
              Attendance Log &rarr;
            </router-link>
          </div>
        </div>
      </div>

      <!-- Pending Fee Due -->
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Fee Balance</small>
            <span class="badge rounded-pill px-2" :class="stats.pending_balance > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success'">
              {{ stats.pending_balance > 0 ? 'Due' : 'Cleared' }}
            </span>
          </div>
          <h3 class="fw-bold mb-1" :class="stats.pending_balance > 0 ? 'text-danger' : 'text-success'">
            ₹{{ Number(stats.pending_balance).toLocaleString('en-IN') }}
          </h3>
          <small class="text-muted">Paid: ₹{{ Number(stats.paid_fee).toLocaleString('en-IN') }}</small>
          <div class="mt-2">
            <router-link to="/parent/fees" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-0" style="font-size: 0.75rem;" v-if="stats.pending_balance > 0">
              <i class="bi bi-wallet2 me-1"></i> Pay Now
            </router-link>
            <router-link to="/parent/fees" class="text-muted small text-decoration-none" v-else>
              View Receipts &rarr;
            </router-link>
          </div>
        </div>
      </div>

      <!-- Upcoming Exam -->
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Upcoming Exam</small>
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2">Academic</span>
          </div>
          <h5 class="fw-bold mb-1 text-dark text-truncate" :title="stats.upcoming_exam?.name || 'Unit Assessment'">
            {{ stats.upcoming_exam?.name || 'Term Evaluation' }}
          </h5>
          <small class="text-muted d-block text-truncate">
            {{ stats.upcoming_exam?.subject ? stats.upcoming_exam.subject + ' &bull; ' : '' }}{{ stats.upcoming_exam?.date || 'Scheduled Soon' }}
          </small>
          <div class="mt-2">
            <router-link to="/parent/results" class="text-primary small text-decoration-none fw-semibold">
              Report Cards &rarr;
            </router-link>
          </div>
        </div>
      </div>

      <!-- Latest Notice -->
      <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Latest Notice</small>
            <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2">Circular</span>
          </div>
          <h6 class="fw-bold mb-1 text-dark text-truncate" :title="latestNoticeTitle">
            {{ latestNoticeTitle }}
          </h6>
          <small class="text-muted d-block">{{ latestNoticeDate }}</small>
          <div class="mt-2">
            <router-link to="/parent/notices" class="text-primary small text-decoration-none fw-semibold">
              Read Notices &rarr;
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Child Content Row: Recent Attendance & Homework -->
    <div class="row g-4 mb-4">
      <!-- Recent Attendance Table -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
          <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">
              <i class="bi bi-calendar-check text-primary me-2"></i>Recent Attendance Log
            </h6>
            <router-link to="/parent/attendance" class="btn btn-sm btn-link text-decoration-none">
              Full Calendar &rarr;
            </router-link>
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
                    <td class="ps-4 fw-semibold text-dark">{{ att.attendance_date }}</td>
                    <td>
                      <span class="badge rounded-pill text-capitalize px-2" :class="att.status === 'present' ? 'bg-success' : (att.status === 'late' ? 'bg-warning text-dark' : 'bg-danger')">
                        {{ att.status }}
                      </span>
                    </td>
                    <td class="small">{{ att.check_in || '08:00 AM' }}</td>
                    <td class="pe-4 text-muted small">{{ att.remarks || 'Regular' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Homework & Assignments -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
          <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">
              <i class="bi bi-journal-bookmark text-primary me-2"></i>Homework & Diary Updates
            </h6>
            <router-link to="/parent/timetable" class="btn btn-sm btn-link text-decoration-none">
              View All &rarr;
            </router-link>
          </div>
          <div class="card-body p-4 pt-1">
            <div v-if="recentHomework.length === 0" class="text-center py-4 text-muted small">
              <i class="bi bi-journal-check fs-2 text-success opacity-50 d-block mb-1"></i>
              No pending homework assignments. All up to date!
            </div>
            <div v-else class="d-flex flex-column gap-3">
              <div v-for="hw in recentHomework" :key="hw.id" class="p-3 border rounded-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small fw-semibold">
                    {{ hw.subject?.name || 'Subject' }}
                  </span>
                  <span class="badge bg-warning-subtle text-dark rounded-pill px-2 py-1 small">
                    Due: {{ hw.due_date }}
                  </span>
                </div>
                <h6 class="fw-bold text-dark mb-1">{{ hw.title }}</h6>
                <p class="text-muted small mb-0 text-truncate">{{ hw.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Shortcuts Row -->
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <router-link to="/parent/fees" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none text-center h-100 hover-lift">
          <div class="bg-primary-subtle text-primary rounded-circle mx-auto p-3 mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-wallet2 fs-4"></i>
          </div>
          <strong class="text-dark d-block">Pay School Fees</strong>
          <small class="text-muted">UPI QR, Card, Net Banking</small>
        </router-link>
      </div>
      <div class="col-6 col-md-3">
        <router-link to="/parent/results" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none text-center h-100 hover-lift">
          <div class="bg-success-subtle text-success rounded-circle mx-auto p-3 mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-award fs-4"></i>
          </div>
          <strong class="text-dark d-block">Report Cards</strong>
          <small class="text-muted">Term Exam Scores & PDF</small>
        </router-link>
      </div>
      <div class="col-6 col-md-3">
        <router-link to="/parent/leave-application" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none text-center h-100 hover-lift">
          <div class="bg-warning-subtle text-warning rounded-circle mx-auto p-3 mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-envelope-paper fs-4"></i>
          </div>
          <strong class="text-dark d-block">Apply for Leave</strong>
          <small class="text-muted">Submit & Track Approvals</small>
        </router-link>
      </div>
      <div class="col-6 col-md-3">
        <router-link to="/parent/timetable" class="card border-0 shadow-sm rounded-4 p-3 bg-white text-decoration-none text-center h-100 hover-lift">
          <div class="bg-info-subtle text-info-emphasis rounded-circle mx-auto p-3 mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-calendar3 fs-4"></i>
          </div>
          <strong class="text-dark d-block">Class Timetable</strong>
          <small class="text-muted">Daily Schedule & Homework</small>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import api from '@/api/axios'
import { useParentPortalStore } from '@/stores/parentPortal'

const parentStore = useParentPortalStore()

const props = defineProps({
  child: Object,
  allChildren: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['switch-child'])

const activeChild = computed(() => parentStore.activeChild || props.child || null)
const allChildren = computed(() => {
  return parentStore.children && parentStore.children.length > 0 
    ? parentStore.children 
    : (props.allChildren || [])
})

const stats = ref({
  attendance_percentage: 95.0,
  present_days: 21,
  absent_days: 1,
  total_days: 22,
  total_fee: 0,
  paid_fee: 0,
  pending_balance: 0,
  upcoming_exam: null,
})
const recentAttendance = ref([])
const upcomingDues = ref([])
const recentHomework = ref([])
const recentNotices = ref([])
const currentDate = new Date().toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })

const latestNoticeTitle = computed(() => {
  return recentNotices.value[0]?.title || 'Parent-Teacher Meeting & Term Circular'
})

const latestNoticeDate = computed(() => {
  const d = recentNotices.value[0]?.publish_date
  if (!d) return 'Published Recently'
  try {
    const dt = new Date(d)
    return isNaN(dt.getTime()) ? d : dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch (e) {
    return d
  }
})

watch(() => parentStore.activeChildId, (newId) => {
  if (newId) {
    loadDashboard(newId)
  }
})

async function loadDashboard(overrideChildId = null) {
  const studentId = overrideChildId || parentStore.activeChildId || props.child?.id || localStorage.getItem('portal_active_student_id')
  try {
    const res = await api.get('/parent/dashboard', {
      params: studentId ? { student_id: Number(studentId) } : {}
    })
    if (res.data.success) {
      stats.value = res.data.data.stats
      recentAttendance.value = res.data.data.recent_attendance || []
      upcomingDues.value = res.data.data.upcoming_dues || []
      recentHomework.value = res.data.data.recent_homework || []
      recentNotices.value = res.data.data.recent_notices || []
    }
  } catch (err) {
    console.error('Failed to load parent dashboard', err)
  }
}

function switchChildProfile(targetChild) {
  parentStore.setActiveChild(targetChild)
  emit('switch-child', targetChild)
  loadDashboard(targetChild.id)
}

onMounted(() => {
  if (parentStore.activeChildId) {
    loadDashboard(parentStore.activeChildId)
  } else {
    loadDashboard()
  }
})
</script>

<style scoped>
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
}
</style>