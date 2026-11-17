<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">Class Timetable & Homework Diary</h4>
        <small class="text-muted">Daily class schedules, subject instructors, and assigned homework for {{ childName }}</small>
      </div>
      <div class="btn-group shadow-sm" role="group">
        <button
          type="button"
          class="btn btn-sm"
          :class="mainTab === 'timetable' ? 'btn-primary' : 'btn-outline-secondary'"
          @click="mainTab = 'timetable'"
        >
          <i class="bi bi-calendar3 me-1"></i> Class Timetable
        </button>
        <button
          type="button"
          class="btn btn-sm"
          :class="mainTab === 'homework' ? 'btn-primary' : 'btn-outline-secondary'"
          @click="mainTab = 'homework'"
        >
          <i class="bi bi-journal-bookmark me-1"></i> Homework Diary ({{ homeworkList.length }})
        </button>
      </div>
    </div>

    <!-- TAB 1: WEEKLY CLASS TIMETABLE -->
    <div v-if="mainTab === 'timetable'">
      <!-- Day Selector Pills -->
      <div class="d-flex gap-2 overflow-auto pb-2 mb-4">
        <button
          v-for="day in days"
          :key="day"
          class="btn rounded-pill px-4 py-2 fw-semibold text-nowrap"
          :class="activeDay === day ? 'btn-primary shadow-sm' : 'btn-white bg-white text-muted border'"
          @click="activeDay = day"
        >
          {{ day }}
          <span v-if="timetable[day]?.length" class="badge bg-light text-dark ms-1 rounded-pill">
            {{ timetable[day].length }}
          </span>
        </button>
      </div>

      <!-- Schedule Table Card -->
      <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0 text-primary">
            <i class="bi bi-clock-history me-2"></i>{{ activeDay }}'s Class Periods
          </h6>
          <span class="badge bg-light text-muted border">
            Class: {{ child?.class?.name || 'Class 1' }} - Section: {{ child?.section?.name || 'A' }}
          </span>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
              <tr>
                <th class="ps-4">Period / Time</th>
                <th>Subject Name</th>
                <th>Subject Code</th>
                <th>Classroom / Room</th>
                <th class="pe-4">Teacher / Instructor</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="currentDaySchedule.length === 0">
                <td colspan="5" class="text-center py-5 text-muted small">
                  <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                  No class periods scheduled for {{ activeDay }}.
                </td>
              </tr>
              <tr v-for="(p, idx) in currentDaySchedule" :key="p.id || idx">
                <td class="ps-4">
                  <span class="badge bg-primary-subtle text-primary fw-bold font-monospace px-2 py-1">
                    {{ p.start_time }} - {{ p.end_time }}
                  </span>
                </td>
                <td class="fw-bold text-dark">{{ p.subject?.name || 'General Subject' }}</td>
                <td><span class="badge bg-light text-muted border">{{ p.subject?.code || 'SUB' }}</span></td>
                <td><span class="badge bg-light text-dark">{{ p.room_number || 'Room 101' }}</span></td>
                <td class="pe-4">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle text-primary"></i>
                    <span class="fw-semibold">{{ p.teacher ? `${p.teacher.first_name} ${p.teacher.last_name || ''}` : 'Class Instructor' }}</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: HOMEWORK DIARY -->
    <div v-if="mainTab === 'homework'">
      <!-- Filter Tabs -->
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="btn-group" role="group">
          <button
            type="button"
            class="btn btn-sm"
            :class="hwFilter === 'pending' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="hwFilter = 'pending'"
          >
            Due / Pending ({{ pendingHomework.length }})
          </button>
          <button
            type="button"
            class="btn btn-sm"
            :class="hwFilter === 'past' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="hwFilter = 'past'"
          >
            Past / Completed ({{ pastHomework.length }})
          </button>
          <button
            type="button"
            class="btn btn-sm"
            :class="hwFilter === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="hwFilter = 'all'"
          >
            All ({{ homeworkList.length }})
          </button>
        </div>
      </div>

      <div class="row g-3">
        <div v-if="filteredHomework.length === 0" class="col-12">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-journal-check text-success fs-1 mb-2"></i>
            <h6 class="fw-bold text-dark">No homework entries for this filter</h6>
            <p class="text-muted small mb-0">Check back regularly for updates from teachers.</p>
          </div>
        </div>

        <div v-for="hw in filteredHomework" :key="hw.id" class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-semibold">
                  {{ hw.subject?.name || 'General Subject' }}
                </span>
                <span
                  class="badge rounded-pill px-2 py-1"
                  :class="isOverdue(hw.due_date) ? 'bg-danger text-white' : 'bg-warning-subtle text-dark'"
                  style="font-size: 0.72rem;"
                >
                  {{ isOverdue(hw.due_date) ? 'Overdue' : 'Due: ' + hw.due_date }}
                </span>
              </div>

              <h5 class="fw-bold text-dark mb-2">{{ hw.title }}</h5>
              <p class="text-muted small text-truncate-3 mb-3">{{ hw.description }}</p>
            </div>

            <div class="border-top pt-3">
              <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span>Assigned: {{ hw.assigned_date }}</span>
                <span>Due: <strong>{{ hw.due_date }}</strong></span>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="small text-muted text-truncate" style="max-width: 170px;">
                  <i class="bi bi-person me-1"></i>{{ hw.teacher ? `${hw.teacher.first_name} ${hw.teacher.last_name || ''}` : 'Teacher' }}
                </span>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="viewHomework(hw)">
                  Details &rarr;
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Homework Detail Modal -->
    <div
      v-if="selectedHw"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.6); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-journal-text fs-4"></i>
              <div>
                <h5 class="modal-title fw-bold mb-0">{{ selectedHw.title }}</h5>
                <small class="text-white-50">{{ selectedHw.subject?.name }} &bull; Assigned by {{ selectedHw.teacher ? `${selectedHw.teacher.first_name} ${selectedHw.teacher.last_name || ''}` : 'Teacher' }}</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="selectedHw = null"></button>
          </div>

          <div class="modal-body p-4">
            <div class="row g-2 mb-3 bg-light rounded-3 p-3 small">
              <div class="col-sm-6">
                <strong>Assigned Date:</strong> {{ selectedHw.assigned_date }}
              </div>
              <div class="col-sm-6 text-sm-end">
                <strong>Due Date:</strong> <span class="fw-bold text-danger">{{ selectedHw.due_date }}</span>
              </div>
            </div>

            <h6 class="fw-bold mb-2">Assignment Brief & Instructions:</h6>
            <div class="p-3 border rounded-3 bg-white mb-3" style="white-space: pre-wrap; line-height: 1.6;">
              {{ selectedHw.description }}
            </div>

            <div v-if="selectedHw.attachment" class="alert alert-info d-flex align-items-center justify-content-between p-3 rounded-3 mb-0">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-paperclip fs-4"></i>
                <div>
                  <strong>Worksheet / Resource Attachment</strong>
                  <small class="d-block text-muted">Supporting reference or practice exercise file</small>
                </div>
              </div>
              <a :href="selectedHw.attachment" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-download me-1"></i> Download File
              </a>
            </div>
          </div>

          <div class="modal-footer bg-light p-3">
            <button type="button" class="btn btn-secondary rounded-pill px-4" @click="selectedHw = null">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const mainTab = ref('timetable')
const hwFilter = ref('pending')
const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
const activeDay = ref('Monday')
const timetable = ref({})
const homeworkList = ref([])
const pendingHomework = ref([])
const pastHomework = ref([])
const selectedHw = ref(null)
const child = ref(null)

const childName = computed(() => child.value?.full_name || child.value?.name || 'Selected Child')

const currentDaySchedule = computed(() => {
  return timetable.value[activeDay.value] || []
})

const filteredHomework = computed(() => {
  if (hwFilter.value === 'pending') return pendingHomework.value
  if (hwFilter.value === 'past') return pastHomework.value
  return homeworkList.value
})

function getAuthConfig(extraParams = {}) {
  const token = localStorage.getItem('token')
  const studentId = localStorage.getItem('portal_active_student_id')
  return {
    headers: {
      Authorization: `Bearer ${token}`,
      ...(studentId ? { 'X-Student-Id': String(studentId) } : {})
    },
    params: {
      ...(studentId ? { student_id: Number(studentId) } : {}),
      ...extraParams
    }
  }
}

async function loadData(overrideChildId = null) {
  const studentId = overrideChildId || localStorage.getItem('portal_active_student_id')
  const cfg = getAuthConfig(studentId ? { student_id: Number(studentId) } : {})
  try {
    const [ttRes, hwRes] = await Promise.all([
      axios.get('/api/parent/timetable', cfg),
      axios.get('/api/parent/homework', cfg),
    ])

    if (ttRes.data.success) {
      timetable.value = ttRes.data.data.timetable || {}
      child.value = ttRes.data.data.child
    }

    if (hwRes.data.success) {
      homeworkList.value = hwRes.data.data.all || []
      pendingHomework.value = hwRes.data.data.pending || []
      pastHomework.value = hwRes.data.data.past || []
    }
  } catch (err) {
    console.error('Failed to load parent timetable/homework', err)
  }
}

function isOverdue(dueDate) {
  if (!dueDate) return false
  const today = new Date().toISOString().split('T')[0]
  return dueDate < today
}

function viewHomework(hw) {
  selectedHw.value = hw
}

function handleChildSwitch(e) {
  loadData(e.detail?.id)
}

onMounted(() => {
  loadData()
  window.addEventListener('parent-child-switched', handleChildSwitch)
})

onUnmounted(() => {
  window.removeEventListener('parent-child-switched', handleChildSwitch)
})
</script>

<style scoped>
.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>