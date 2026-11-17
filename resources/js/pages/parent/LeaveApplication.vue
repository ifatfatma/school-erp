<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">Student Leave Applications</h4>
        <small class="text-muted">Apply for student leave on behalf of {{ childName }} and monitor administrative approvals</small>
      </div>
      <div>
        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2" @click="showApplyModal = true">
          <i class="bi bi-plus-circle-fill"></i>
          <span>Apply for Leave</span>
        </button>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Pending Requests</small>
          <h3 class="fw-bold my-2" :class="pendingCount > 0 ? 'text-warning' : 'text-muted'">{{ pendingCount }}</h3>
          <small class="text-muted">Under school administration review</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Approved Leaves</small>
          <h3 class="fw-bold text-success my-2">{{ approvedCount }}</h3>
          <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Officially sanctioned</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Days Applied</small>
          <h3 class="fw-bold text-primary my-2">{{ totalDaysCount }}</h3>
          <small class="text-muted">Days requested this academic session</small>
        </div>
      </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Leave History & Status for {{ childName }}</h6>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Leave Type</th>
              <th>From Date</th>
              <th>To Date</th>
              <th>Duration</th>
              <th>Reason</th>
              <th>Status</th>
              <th class="pe-4 text-end">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="leaves.length === 0">
              <td colspan="7" class="text-center py-5 text-muted small">
                <i class="bi bi-envelope-paper text-muted fs-1 d-block mb-2"></i>
                No leave applications submitted yet for this child.
              </td>
            </tr>
            <tr v-for="l in leaves" :key="l.id">
              <td class="ps-4 fw-semibold text-dark">
                <i class="bi bi-tag me-1 text-primary"></i> {{ l.leave_type }}
              </td>
              <td class="small">{{ formatDate(l.from_date) }}</td>
              <td class="small">{{ formatDate(l.to_date) }}</td>
              <td><span class="badge bg-light text-dark border">{{ l.total_days }} Day(s)</span></td>
              <td class="small text-muted text-truncate" style="max-width: 250px;" :title="l.reason">
                {{ l.reason }}
              </td>
              <td>
                <span
                  class="badge rounded-pill text-capitalize px-3 py-1"
                  :class="l.status === 'Approved' ? 'bg-success' : (l.status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')"
                >
                  <i class="bi me-1" :class="l.status === 'Approved' ? 'bi-check-circle' : (l.status === 'Rejected' ? 'bi-x-circle' : 'bi-clock')"></i>
                  {{ l.status }}
                </span>
              </td>
              <td class="pe-4 text-end small text-muted">
                {{ l.remarks || (l.status === 'Pending' ? 'In Review' : 'No remarks') }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Apply Leave Modal -->
    <div
      v-if="showApplyModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.6); z-index: 1050;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <h5 class="modal-title fw-bold mb-0">
              <i class="bi bi-envelope-paper-fill me-2"></i>Apply for Student Leave
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showApplyModal = false" :disabled="isSubmitting"></button>
          </div>

          <form @submit.prevent="submitLeave">
            <div class="modal-body p-4">
              <div class="bg-light p-2 rounded-3 mb-3 border small">
                Applying for: <strong class="text-primary">{{ childName }}</strong>
                ({{ child?.class?.name || 'Class' }} - {{ child?.section?.name || 'A' }})
              </div>

              <!-- Leave Type -->
              <div class="mb-3">
                <label class="form-label small fw-semibold">Leave Type <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" v-model="form.leave_type" required>
                  <option value="Sick Leave">Sick Leave / Illness</option>
                  <option value="Medical Emergency">Medical Emergency / Doctor Appointment</option>
                  <option value="Casual Leave">Casual Leave / Urgent Work</option>
                  <option value="Family Event">Family Function / Wedding</option>
                  <option value="Out of Town">Traveling / Out of Town</option>
                  <option value="Other">Other Reason</option>
                </select>
              </div>

              <!-- Date Range -->
              <div class="row g-2 mb-3">
                <div class="col-6">
                  <label class="form-label small fw-semibold">From Date <span class="text-danger">*</span></label>
                  <input
                    type="date"
                    class="form-control form-control-sm"
                    v-model="form.from_date"
                    :min="minDate"
                    required
                    @change="calculateDays"
                  />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">To Date <span class="text-danger">*</span></label>
                  <input
                    type="date"
                    class="form-control form-control-sm"
                    v-model="form.to_date"
                    :min="form.from_date || minDate"
                    required
                    @change="calculateDays"
                  />
                </div>
              </div>

              <!-- Calculated Days Banner -->
              <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-light border mb-3 small">
                <span>Calculated Absence Duration:</span>
                <span class="badge bg-primary fs-6">{{ calculatedDays }} Day(s)</span>
              </div>

              <!-- Reason -->
              <div class="mb-3">
                <label class="form-label small fw-semibold">Detailed Reason <span class="text-danger">*</span></label>
                <textarea
                  class="form-control form-control-sm"
                  rows="3"
                  placeholder="Explain the reason for leave clearly for the class teacher..."
                  v-model="form.reason"
                  required
                ></textarea>
              </div>
            </div>

            <div class="modal-footer bg-light p-3 d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary rounded-pill px-3" @click="showApplyModal = false" :disabled="isSubmitting">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>
                <span>{{ isSubmitting ? 'Submitting...' : 'Submit Leave Application' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const leaves = ref([])
const child = ref(null)
const showApplyModal = ref(false)
const isSubmitting = ref(false)

const childName = computed(() => child.value?.full_name || child.value?.name || 'Selected Child')
const minDate = new Date().toISOString().split('T')[0]

const form = ref({
  leave_type: 'Sick Leave',
  from_date: minDate,
  to_date: minDate,
  reason: '',
})

const calculatedDays = ref(1)

const pendingCount = computed(() => leaves.value.filter(l => l.status === 'Pending').length)
const approvedCount = computed(() => leaves.value.filter(l => l.status === 'Approved').length)
const totalDaysCount = computed(() => leaves.value.reduce((sum, l) => sum + Number(l.total_days || 0), 0))

function calculateDays() {
  if (!form.value.from_date || !form.value.to_date) {
    calculatedDays.value = 1
    return
  }
  const from = new Date(form.value.from_date)
  const to = new Date(form.value.to_date)
  if (to < from) {
    form.value.to_date = form.value.from_date
    calculatedDays.value = 1
    return
  }
  const diffTime = Math.abs(to - from)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
  calculatedDays.value = diffDays
}

function formatDate(dateStr) {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return isNaN(d.getTime()) ? dateStr : d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
  } catch (e) {
    return dateStr
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
      ...(studentId ? { student_id: Number(studentId) } : {}),
      ...extraParams
    }
  }
}

async function loadLeaves(overrideChildId = null) {
  const studentId = overrideChildId || localStorage.getItem('portal_active_student_id')
  try {
    const res = await axios.get('/api/parent/leave-applications', getAuthConfig(studentId ? { student_id: Number(studentId) } : {}))
    if (res.data.success) {
      leaves.value = res.data.data.leaves || []
      child.value = res.data.data.child
    }
  } catch (err) {
    console.error('Failed to load parent leaves', err)
  }
}

async function submitLeave() {
  isSubmitting.value = true
  const studentId = localStorage.getItem('portal_active_student_id')
  try {
    const res = await axios.post('/api/parent/leave-applications', {
      ...form.value,
      ...(studentId ? { student_id: Number(studentId) } : {})
    }, getAuthConfig())
    if (res.data.success) {
      showApplyModal.value = false
      Swal.fire({
        icon: 'success',
        title: 'Application Submitted!',
        text: 'Your leave application has been sent to the class teacher and administration.',
      })
      form.value = {
        leave_type: 'Sick Leave',
        from_date: minDate,
        to_date: minDate,
        reason: '',
      }
      calculatedDays.value = 1
      await loadLeaves()
    }
  } catch (err) {
    console.error('Leave submission error', err)
    Swal.fire({
      icon: 'error',
      title: 'Failed to Submit',
      text: err.response?.data?.message || 'Could not submit leave application.',
    })
  } finally {
    isSubmitting.value = false
  }
}

function handleChildSwitch(e) {
  loadLeaves(e.detail?.id)
}

onMounted(() => {
  loadLeaves()
  window.addEventListener('parent-child-switched', handleChildSwitch)
})

onUnmounted(() => {
  window.removeEventListener('parent-child-switched', handleChildSwitch)
})
</script>