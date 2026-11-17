<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">Homework & Daily Assignment Diary</h4>
        <small class="text-muted">Track assigned daily homework, task instructions, submission deadlines, and teacher feedback</small>
      </div>
      <div class="d-flex gap-2">
        <div class="btn-group shadow-sm" role="group">
          <button
            type="button"
            class="btn btn-sm"
            :class="filterTab === 'pending' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="filterTab = 'pending'"
          >
            <i class="bi bi-clock-history me-1"></i> Due / Pending ({{ pendingList.length }})
          </button>
          <button
            type="button"
            class="btn btn-sm"
            :class="filterTab === 'past' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="filterTab = 'past'"
          >
            <i class="bi bi-check2-circle me-1"></i> Past / Completed ({{ pastList.length }})
          </button>
          <button
            type="button"
            class="btn btn-sm"
            :class="filterTab === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="filterTab = 'all'"
          >
            All ({{ allList.length }})
          </button>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Pending Assignments</small>
          <h3 class="fw-bold my-2" :class="pendingList.length > 0 ? 'text-warning' : 'text-success'">
            {{ pendingList.length }}
          </h3>
          <small :class="pendingList.length > 0 ? 'text-warning fw-semibold' : 'text-muted'">
            {{ pendingList.length > 0 ? 'Requires student attention' : 'All assignments submitted' }}
          </small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Past / Archive</small>
          <h3 class="fw-bold text-muted my-2">{{ pastList.length }}</h3>
          <small class="text-muted">Completed previous tasks</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Tracked</small>
          <h3 class="fw-bold text-primary my-2">{{ allList.length }}</h3>
          <small class="text-muted">Total coursework entries</small>
        </div>
      </div>
    </div>

    <!-- Homework List -->
    <div class="row g-3">
      <div v-if="filteredList.length === 0" class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
          <i class="bi bi-journal-check text-success fs-1 mb-2"></i>
          <h6 class="fw-bold text-dark">No homework assignments found</h6>
          <p class="text-muted small mb-0">No active homework entries for the selected category.</p>
        </div>
      </div>

      <div v-for="hw in filteredList" :key="hw.id" class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between position-relative overflow-hidden">
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
                {{ isOverdue(hw.due_date) ? 'Overdue' : 'Due ' + formatDueLabel(hw.due_date) }}
              </span>
            </div>

            <h5 class="fw-bold text-dark mb-2">{{ hw.title }}</h5>
            <p class="text-muted small text-truncate-3 mb-3">
              {{ hw.description }}
            </p>
          </div>

          <div class="border-top pt-3 mt-2">
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
              <span><i class="bi bi-calendar-event me-1"></i>Assigned: {{ hw.assigned_date }}</span>
              <span><i class="bi bi-hourglass-split me-1"></i>Due: <strong>{{ hw.due_date }}</strong></span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="small text-muted text-truncate" style="max-width: 170px;">
                <i class="bi bi-person me-1"></i>{{ hw.teacher ? `${hw.teacher.first_name} ${hw.teacher.last_name || ''}` : 'Subject Teacher' }}
              </span>
              <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="viewHomework(hw)">
                <i class="bi bi-eye me-1"></i> Details
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Homework Details Modal -->
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
                  <strong>Resource Attachment</strong>
                  <small class="d-block text-muted">Supporting worksheet or reference document</small>
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

const filterTab = ref('pending')
const allList = ref([])
const pendingList = ref([])
const pastList = ref([])
const selectedHw = ref(null)

const filteredList = computed(() => {
  if (filterTab.value === 'pending') return pendingList.value
  if (filterTab.value === 'past') return pastList.value
  return allList.value
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadHomework() {
  try {
    const res = await axios.get('/api/portal/homework', getAuthConfig())
    if (res.data.success) {
      allList.value = res.data.data.all || []
      pendingList.value = res.data.data.pending || []
      pastList.value = res.data.data.past || []
    }
  } catch (err) {
    console.error('Failed to load homework', err)
  }
}

function isOverdue(dueDate) {
  if (!dueDate) return false
  const today = new Date().toISOString().split('T')[0]
  return dueDate < today
}

function formatDueLabel(dueDate) {
  if (!dueDate) return 'Soon'
  return dueDate
}

function viewHomework(hw) {
  selectedHw.value = hw
}

function handleStudentSwitch() {
  loadHomework()
}

onMounted(() => {
  loadHomework()
  window.addEventListener('portal-student-switched', handleStudentSwitch)
})

onUnmounted(() => {
  window.removeEventListener('portal-student-switched', handleStudentSwitch)
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