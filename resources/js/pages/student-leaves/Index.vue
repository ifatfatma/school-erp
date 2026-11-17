<template>
  <div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <AppBreadcrumb
      :items="[
        { text: 'Dashboard', to: '/dashboard' },
        { text: 'Students', to: '/students' },
        { text: 'Student Leaves', active: true }
      ]"
    />

    <!-- Header & Summary Cards -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1 text-dark">
          <i class="bi bi-envelope-paper text-primary me-2"></i>Student Leave Applications
        </h4>
        <small class="text-muted">Review, approve, or reject leave requests submitted by parents and guardians</small>
      </div>
      <div>
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" @click="fetchData">
          <i class="bi bi-arrow-clockwise me-1" :class="{ 'spin': isLoading }"></i> Refresh
        </button>
      </div>
    </div>

    <!-- Summary Stats Counters -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 cursor-pointer" :class="{ 'border-primary border-2': filterStatus === 'all' }" @click="setStatusFilter('all')">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Total Requests</small>
              <h3 class="fw-bold mb-0 text-dark">{{ summary.total }}</h3>
            </div>
            <div class="bg-primary-subtle text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-files fs-5"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 cursor-pointer" :class="{ 'border-warning border-2': filterStatus === 'Pending' }" @click="setStatusFilter('Pending')">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Pending Review</small>
              <h3 class="fw-bold mb-0 text-warning">{{ summary.pending }}</h3>
            </div>
            <div class="bg-warning-subtle text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-clock-history fs-5"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 cursor-pointer" :class="{ 'border-success border-2': filterStatus === 'Approved' }" @click="setStatusFilter('Approved')">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Approved</small>
              <h3 class="fw-bold mb-0 text-success">{{ summary.approved }}</h3>
            </div>
            <div class="bg-success-subtle text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-check-circle-fill fs-5"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 cursor-pointer" :class="{ 'border-danger border-2': filterStatus === 'Rejected' }" @click="setStatusFilter('Rejected')">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem;">Rejected</small>
              <h3 class="fw-bold mb-0 text-danger">{{ summary.rejected }}</h3>
            </div>
            <div class="bg-danger-subtle text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
              <i class="bi bi-x-circle-fill fs-5"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 p-3">
      <div class="row g-2 align-items-center">
        <!-- Search Input -->
        <div class="col-md-4">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input
              type="text"
              class="form-control bg-light border-start-0"
              placeholder="Search student, adm no, or reason..."
              v-model="searchQuery"
              @input="debouncedSearch"
            />
            <button v-if="searchQuery" class="btn btn-light border" @click="clearSearch">
              <i class="bi bi-x"></i>
            </button>
          </div>
        </div>

        <!-- Class Filter -->
        <div class="col-md-3">
          <select class="form-select form-select-sm bg-light" v-model="filterClass" @change="fetchData">
            <option value="">All Classes</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">
              {{ c.name }}
            </option>
          </select>
        </div>

        <!-- Status Filter Tabs -->
        <div class="col-md-5 d-flex justify-content-md-end gap-1">
          <div class="btn-group btn-group-sm" role="group">
            <button
              type="button"
              class="btn"
              :class="filterStatus === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
              @click="setStatusFilter('all')"
            >
              All
            </button>
            <button
              type="button"
              class="btn position-relative"
              :class="filterStatus === 'Pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'"
              @click="setStatusFilter('Pending')"
            >
              Pending
              <span v-if="summary.pending > 0" class="badge rounded-pill bg-danger ms-1">
                {{ summary.pending }}
              </span>
            </button>
            <button
              type="button"
              class="btn"
              :class="filterStatus === 'Approved' ? 'btn-success' : 'btn-outline-success'"
              @click="setStatusFilter('Approved')"
            >
              Approved
            </button>
            <button
              type="button"
              class="btn"
              :class="filterStatus === 'Rejected' ? 'btn-danger' : 'btn-outline-danger'"
              @click="setStatusFilter('Rejected')"
            >
              Rejected
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted text-uppercase" style="font-size: 0.72rem;">
            <tr>
              <th class="ps-4">#</th>
              <th>Student</th>
              <th>Class & Section</th>
              <th>Guardian / Contact</th>
              <th>Leave Details</th>
              <th>Dates & Duration</th>
              <th>Status</th>
              <th>Remarks / Reviewer</th>
              <th class="pe-4 text-center" style="min-width: 140px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="9" class="text-center py-5">
                <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                <span class="text-muted small">Loading leave applications...</span>
              </td>
            </tr>

            <tr v-else-if="applications.length === 0">
              <td colspan="9" class="text-center py-5 text-muted small">
                <i class="bi bi-inbox fs-2 text-muted opacity-50 d-block mb-2"></i>
                No leave applications match the selected criteria.
              </td>
            </tr>

            <tr v-for="(app, index) in applications" :key="app.id">
              <!-- Index -->
              <td class="ps-4 text-muted small">
                {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}
              </td>

              <!-- Student -->
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div
                    class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold"
                    style="width: 36px; height: 36px; font-size: 0.85rem;"
                  >
                    {{ app.student?.first_name?.charAt(0) || 'S' }}
                  </div>
                  <div>
                    <strong class="text-dark d-block">
                      {{ app.student ? (app.student.first_name + ' ' + (app.student.last_name || '')).trim() : 'Unknown Student' }}
                    </strong>
                    <small class="text-muted">Adm: {{ app.student?.admission_no || '—' }} &bull; Roll: #{{ app.student?.roll_no || '—' }}</small>
                  </div>
                </div>
              </td>

              <!-- Class & Section -->
              <td>
                <span class="badge bg-light text-dark border">
                  {{ app.student?.class?.name || 'Class' }}
                  <span v-if="app.student?.section?.name">- {{ app.student.section.name }}</span>
                </span>
              </td>

              <!-- Guardian -->
              <td>
                <div class="small">
                  <div class="fw-semibold text-dark">{{ app.guardian ? (app.guardian.first_name + ' ' + (app.guardian.last_name || '')).trim() : 'Parent' }}</div>
                  <div class="text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-telephone me-1"></i>{{ app.guardian?.mobile || 'No contact' }}
                  </div>
                </div>
              </td>

              <!-- Leave Details -->
              <td>
                <div class="small">
                  <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2 py-1 mb-1">
                    {{ app.leave_type }}
                  </span>
                  <div class="text-muted text-truncate" style="max-width: 200px;" :title="app.reason">
                    {{ app.reason || 'No description provided' }}
                  </div>
                </div>
              </td>

              <!-- Dates & Duration -->
              <td>
                <div class="small">
                  <strong class="text-dark d-block">{{ formatDate(app.from_date) }} &rarr; {{ formatDate(app.to_date) }}</strong>
                  <span class="badge bg-light text-dark border">{{ app.total_days }} Day(s)</span>
                </div>
              </td>

              <!-- Status -->
              <td>
                <span
                  class="badge rounded-pill text-capitalize px-3 py-1"
                  :class="app.status === 'Approved' ? 'bg-success' : (app.status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')"
                >
                  <i class="bi me-1" :class="app.status === 'Approved' ? 'bi-check-circle-fill' : (app.status === 'Rejected' ? 'bi-x-circle-fill' : 'bi-clock')"></i>
                  {{ app.status }}
                </span>
              </td>

              <!-- Remarks / Reviewer -->
              <td>
                <div class="small text-muted" style="max-width: 170px;">
                  <div class="text-truncate" :title="app.remarks">{{ app.remarks || '—' }}</div>
                  <div v-if="app.reviewer" class="text-muted" style="font-size: 0.72rem;">
                    By: {{ app.reviewer.name }}
                  </div>
                </div>
              </td>

              <!-- Action Buttons -->
              <td class="pe-4 text-center">
                <div class="d-inline-flex gap-1">
                  <!-- View Details Modal Trigger -->
                  <button
                    class="btn btn-sm btn-outline-info rounded-circle p-1"
                    style="width: 30px; height: 30px;"
                    title="View Full Application Details"
                    @click="viewDetails(app)"
                  >
                    <i class="bi bi-eye"></i>
                  </button>

                  <!-- Approve Button -->
                  <button
                    v-if="app.status !== 'Approved'"
                    class="btn btn-sm btn-outline-success rounded-circle p-1"
                    style="width: 30px; height: 30px;"
                    title="Approve Leave"
                    @click="promptApprove(app)"
                  >
                    <i class="bi bi-check-lg"></i>
                  </button>

                  <!-- Reject Button -->
                  <button
                    v-if="app.status !== 'Rejected'"
                    class="btn btn-sm btn-outline-danger rounded-circle p-1"
                    style="width: 30px; height: 30px;"
                    title="Reject Leave"
                    @click="promptReject(app)"
                  >
                    <i class="bi bi-x-lg"></i>
                  </button>

                  <!-- Delete Button -->
                  <button
                    class="btn btn-sm btn-outline-secondary rounded-circle p-1"
                    style="width: 30px; height: 30px;"
                    title="Delete Application"
                    @click="deleteApp(app)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div v-if="pagination.total > pagination.per_page" class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <small class="text-muted">
          Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} applications
        </small>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
            <button class="page-link" @click="changePage(pagination.current_page - 1)">Previous</button>
          </li>
          <li class="page-item active">
            <span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span>
          </li>
          <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
            <button class="page-link" @click="changePage(pagination.current_page + 1)">Next</button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Application Details Modal -->
    <div
      v-if="selectedApp"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.6); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-envelope-paper-fill fs-5"></i>
              <div>
                <h5 class="modal-title fw-bold mb-0">Student Leave Details</h5>
                <small class="text-white-50">Reference ID: #{{ selectedApp.id }}</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="selectedApp = null"></button>
          </div>

          <div class="modal-body p-4">
            <!-- Student Header Banner -->
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-3">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px;">
                {{ selectedApp.student?.first_name?.charAt(0) || 'S' }}
              </div>
              <div class="overflow-hidden">
                <h6 class="fw-bold text-dark mb-0">
                  {{ selectedApp.student ? (selectedApp.student.first_name + ' ' + (selectedApp.student.last_name || '')).trim() : 'Student' }}
                </h6>
                <small class="text-muted">
                  Class: <strong>{{ selectedApp.student?.class?.name || '—' }} ({{ selectedApp.student?.section?.name || 'A' }})</strong> &bull;
                  Adm No: <strong>{{ selectedApp.student?.admission_no || '—' }}</strong> &bull;
                  Roll: <strong>#{{ selectedApp.student?.roll_no || '—' }}</strong>
                </small>
              </div>
            </div>

            <!-- Details List -->
            <div class="row g-2 mb-3 small">
              <div class="col-sm-5 text-muted">Leave Type:</div>
              <div class="col-sm-7 fw-semibold text-dark">{{ selectedApp.leave_type }}</div>

              <div class="col-sm-5 text-muted">Duration:</div>
              <div class="col-sm-7 fw-semibold text-dark">
                {{ formatDate(selectedApp.from_date) }} to {{ formatDate(selectedApp.to_date) }} ({{ selectedApp.total_days }} Days)
              </div>

              <div class="col-sm-5 text-muted">Applied By Guardian:</div>
              <div class="col-sm-7 fw-semibold text-dark">
                {{ selectedApp.guardian ? (selectedApp.guardian.first_name + ' ' + (selectedApp.guardian.last_name || '')).trim() : 'Parent / Guardian' }}
                <span v-if="selectedApp.guardian?.mobile" class="text-muted d-block font-monospace">
                  <i class="bi bi-telephone me-1"></i>{{ selectedApp.guardian.mobile }}
                </span>
              </div>

              <div class="col-sm-5 text-muted">Current Status:</div>
              <div class="col-sm-7">
                <span
                  class="badge rounded-pill px-3 py-1"
                  :class="selectedApp.status === 'Approved' ? 'bg-success' : (selectedApp.status === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark')"
                >
                  {{ selectedApp.status }}
                </span>
              </div>

              <div class="col-sm-5 text-muted">Admin Remarks:</div>
              <div class="col-sm-7 text-dark">{{ selectedApp.remarks || 'None' }}</div>
            </div>

            <!-- Reason Box -->
            <div class="mb-3">
              <label class="form-label small fw-bold text-muted mb-1">Reason for Leave:</label>
              <div class="p-3 bg-light rounded-3 border small" style="white-space: pre-wrap; line-height: 1.6;">
                {{ selectedApp.reason || 'No description provided.' }}
              </div>
            </div>

            <!-- Medical/Document Attachment if available -->
            <div v-if="selectedApp.attachment" class="alert alert-light border d-flex align-items-center justify-content-between p-2 rounded-3 mb-0">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-paperclip fs-5 text-primary"></i>
                <small class="fw-semibold">Attached Doctor / Parent Certificate</small>
              </div>
              <a :href="selectedApp.attachment" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> View Document
              </a>
            </div>
          </div>

          <div class="modal-footer bg-light p-3 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary rounded-pill px-3" @click="selectedApp = null">
              Close
            </button>
            <div class="d-flex gap-2">
              <button
                v-if="selectedApp.status !== 'Approved'"
                type="button"
                class="btn btn-success rounded-pill px-3"
                @click="promptApprove(selectedApp); selectedApp = null"
              >
                <i class="bi bi-check-lg me-1"></i> Approve
              </button>
              <button
                v-if="selectedApp.status !== 'Rejected'"
                type="button"
                class="btn btn-danger rounded-pill px-3"
                @click="promptReject(selectedApp); selectedApp = null"
              >
                <i class="bi bi-x-lg me-1"></i> Reject
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const applications = ref([])
const classes = ref([])
const isLoading = ref(false)
const searchQuery = ref('')
const filterClass = ref('')
const filterStatus = ref('all')
const selectedApp = ref(null)

const summary = reactive({
  total: 0,
  pending: 0,
  approved: 0,
  rejected: 0,
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 1,
  to: 1,
})

let debounceTimeout = null

function getAuthConfig(params = {}) {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` },
    params,
  }
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

async function loadClasses() {
  try {
    const res = await axios.get('/api/classes', getAuthConfig({ per_page: 1000 }))
    if (res.data.data) {
      classes.value = Array.isArray(res.data.data) ? res.data.data : res.data.data.data || []
    }
  } catch (err) {
    console.error('Failed to load classes', err)
  }
}

async function fetchData(page = 1) {
  isLoading.value = true
  try {
    const params = {
      page,
      per_page: pagination.per_page,
      search: searchQuery.value || undefined,
      class_id: filterClass.value || undefined,
      status: filterStatus.value !== 'all' ? filterStatus.value : undefined,
    }

    const res = await axios.get('/api/student-leaves', getAuthConfig(params))
    if (res.data.success) {
      const paginated = res.data.data
      applications.value = paginated.data || []
      pagination.current_page = paginated.current_page || 1
      pagination.last_page = paginated.last_page || 1
      pagination.per_page = paginated.per_page || 15
      pagination.total = paginated.total || 0
      pagination.from = paginated.from || 1
      pagination.to = paginated.to || applications.value.length

      if (res.data.summary) {
        summary.total = res.data.summary.total || 0
        summary.pending = res.data.summary.pending || 0
        summary.approved = res.data.summary.approved || 0
        summary.rejected = res.data.summary.rejected || 0
      }
    }
  } catch (err) {
    console.error('Failed to load student leave applications', err)
  } finally {
    isLoading.value = false
  }
}

function setStatusFilter(status) {
  filterStatus.value = status
  fetchData(1)
}

function debouncedSearch() {
  clearTimeout(debounceTimeout)
  debounceTimeout = setTimeout(() => {
    fetchData(1)
  }, 350)
}

function clearSearch() {
  searchQuery.value = ''
  fetchData(1)
}

function changePage(page) {
  if (page >= 1 && page <= pagination.last_page) {
    fetchData(page)
  }
}

function viewDetails(app) {
  selectedApp.value = app
}

async function promptApprove(app) {
  const { value: remarks } = await Swal.fire({
    title: 'Approve Leave Application?',
    text: `Sanction leave for ${app.student ? app.student.first_name : 'student'} from ${formatDate(app.from_date)} to ${formatDate(app.to_date)} (${app.total_days} Days)`,
    input: 'text',
    inputLabel: 'Administrative Remarks (Optional)',
    inputValue: 'Sanctioned by School Administration',
    showCancelButton: true,
    confirmButtonColor: '#198754',
    confirmButtonText: 'Yes, Approve Leave',
    cancelButtonText: 'Cancel',
  })

  if (remarks !== undefined) {
    try {
      const res = await axios.post(`/api/student-leaves/${app.id}/approve`, { remarks }, getAuthConfig())
      if (res.data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Leave Approved',
          text: 'The student leave application has been sanctioned.',
          timer: 2000,
          showConfirmButton: false,
        })
        fetchData(pagination.current_page)
      }
    } catch (err) {
      console.error('Failed to approve leave', err)
      Swal.fire({
        icon: 'error',
        title: 'Action Failed',
        text: 'Unable to approve leave application. Please try again.',
      })
    }
  }
}

async function promptReject(app) {
  const { value: remarks } = await Swal.fire({
    title: 'Reject Leave Application?',
    text: `Decline leave request for ${app.student ? app.student.first_name : 'student'}`,
    input: 'text',
    inputLabel: 'Rejection Reason / Remarks (Required)',
    inputPlaceholder: 'e.g. Examinations in progress / Insufficient attendance',
    inputValidator: (value) => {
      if (!value) {
        return 'Please provide a reason for declining the leave application!'
      }
    },
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Yes, Reject Leave',
    cancelButtonText: 'Cancel',
  })

  if (remarks) {
    try {
      const res = await axios.post(`/api/student-leaves/${app.id}/reject`, { remarks }, getAuthConfig())
      if (res.data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Leave Rejected',
          text: 'The student leave application has been declined.',
          timer: 2000,
          showConfirmButton: false,
        })
        fetchData(pagination.current_page)
      }
    } catch (err) {
      console.error('Failed to reject leave', err)
      Swal.fire({
        icon: 'error',
        title: 'Action Failed',
        text: 'Unable to reject leave application. Please try again.',
      })
    }
  }
}

async function deleteApp(app) {
  const result = await Swal.fire({
    title: 'Delete Application?',
    text: 'Are you sure you want to delete this leave record? This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Delete',
  })

  if (result.isConfirmed) {
    try {
      const res = await axios.delete(`/api/student-leaves/${app.id}`, getAuthConfig())
      if (res.data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Deleted',
          text: 'Leave record removed successfully.',
          timer: 1800,
          showConfirmButton: false,
        })
        fetchData(pagination.current_page)
      }
    } catch (err) {
      console.error('Failed to delete leave application', err)
      Swal.fire({
        icon: 'error',
        title: 'Delete Failed',
        text: 'Unable to delete leave record.',
      })
    }
  }
}

onMounted(() => {
  loadClasses()
  fetchData(1)
})
</script>

<style scoped>
.spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
.cursor-pointer {
  cursor: pointer;
}
</style>
