<template>
  <div class="container-fluid py-3">
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Admissions & School Web' },
        { label: 'Applications' }
      ]"
    />

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">Online Admission Applications</h4>
        <small class="text-muted">Review public online applications, update statuses, and enroll students into ERP</small>
      </div>
      <div class="d-flex gap-2">
        <router-link to="/admissions/apply" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm">
          <i class="bi bi-box-arrow-up-right me-1"></i> Public Apply Form
        </router-link>
      </div>
    </div>

    <!-- Status Metric Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-primary': selectedStatus === '' }" @click="setStatusFilter('')">
          <small class="text-muted fw-semibold">Total Received</small>
          <h3 class="fw-bold text-dark my-1">{{ counts.all || 0 }}</h3>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-warning': selectedStatus === 'pending' }" @click="setStatusFilter('pending')">
          <small class="text-warning fw-semibold">Pending Review</small>
          <h3 class="fw-bold text-warning my-1">{{ counts.pending || 0 }}</h3>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-info': selectedStatus === 'under_review' }" @click="setStatusFilter('under_review')">
          <small class="text-info fw-semibold">Under Review</small>
          <h3 class="fw-bold text-info my-1">{{ counts.under_review || 0 }}</h3>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-primary': selectedStatus === 'approved' }" @click="setStatusFilter('approved')">
          <small class="text-primary fw-semibold">Approved</small>
          <h3 class="fw-bold text-primary my-1">{{ counts.approved || 0 }}</h3>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-success': selectedStatus === 'enrolled' }" @click="setStatusFilter('enrolled')">
          <small class="text-success fw-semibold">Enrolled</small>
          <h3 class="fw-bold text-success my-1">{{ counts.enrolled || 0 }}</h3>
        </div>
      </div>
      <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center cursor-pointer" :class="{ 'border border-2 border-danger': selectedStatus === 'rejected' }" @click="setStatusFilter('rejected')">
          <small class="text-danger fw-semibold">Rejected</small>
          <h3 class="fw-bold text-danger my-1">{{ counts.rejected || 0 }}</h3>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
      <div class="row g-2 align-items-center">
        <!-- School Selector (Super Admin) -->
        <div v-if="isSuperAdmin && allSchools.length > 0" class="col-md-3">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-primary"><i class="bi bi-buildings"></i></span>
            <select v-model="selectedSchoolId" class="form-select border-start-0 fw-semibold" @change="onSchoolChange">
              <option value="">All Schools</option>
              <option v-for="s in allSchools" :key="'sch-'+s.id" :value="s.id">
                {{ s.name }} ({{ s.code }})
              </option>
            </select>
          </div>
        </div>

        <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-4' : 'col-md-5'">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input
              type="text"
              v-model="search"
              class="form-control border-start-0"
              placeholder="Search by Application No, Student Name, Mobile..."
              @input="debounceSearch"
            >
          </div>
        </div>

        <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-3' : 'col-md-4'">
          <select v-model="selectedClass" class="form-select" @change="loadApplications(1)">
            <option value="">All Classes</option>
            <option v-for="c in classOptions" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-2' : 'col-md-3'" class="text-md-end">
          <button class="btn btn-outline-secondary rounded-pill btn-sm px-3 w-100" @click="resetFilters">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Applications Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">App. Number</th>
              <th v-if="isSuperAdmin">School / Campus</th>
              <th>Applicant Name</th>
              <th>Class</th>
              <th>Father's Name</th>
              <th>Mobile</th>
              <th>Applied Date</th>
              <th>Status</th>
              <th class="pe-4 text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="isSuperAdmin ? 9 : 8" class="text-center py-5">
                <span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading applications...
              </td>
            </tr>
            <tr v-else-if="applications.length === 0">
              <td :colspan="isSuperAdmin ? 9 : 8" class="text-center py-5 text-muted">
                No admission applications found matching your criteria.
              </td>
            </tr>
            <tr v-for="app in applications" :key="app.id">
              <td class="ps-4 fw-bold font-monospace text-primary">{{ app.application_no }}</td>
              <td v-if="isSuperAdmin">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                  <i class="bi bi-building me-1"></i> {{ app.school?.name || 'School' }}
                </span>
                <small v-if="app.school?.code" class="text-muted d-block font-monospace">({{ app.school.code }})</small>
              </td>
              <td>
                <div class="fw-semibold">{{ app.first_name }} {{ app.last_name || '' }}</div>
                <small class="text-muted text-capitalize">{{ app.gender }} &bull; DOB: {{ app.dob || '-' }}</small>
              </td>
              <td><span class="badge bg-light text-dark">{{ app.class?.name || '-' }}</span></td>
              <td>{{ app.father_name || '-' }}</td>
              <td>{{ app.mobile }}</td>
              <td class="small text-muted">{{ formatDate(app.created_at) }}</td>
              <td>
                <span class="badge rounded-pill text-capitalize" :class="statusBadge(app.status)">
                  {{ app.status.replace('_', ' ') }}
                </span>
              </td>
              <td class="pe-4 text-end">
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-info" title="View Application Details" @click="openViewModal(app)">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button class="btn btn-outline-secondary" title="Update Status & Notes" @click="openStatusModal(app)">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    v-if="app.status !== 'enrolled'"
                    class="btn btn-outline-success"
                    title="Enroll as Student"
                    @click="openEnrollModal(app)"
                  >
                    <i class="bi bi-person-check-fill"></i> Enroll
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="d-flex justify-content-between align-items-center p-3 border-top small text-muted">
        <div>Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries</div>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === 1" @click="loadApplications(pagination.current_page - 1)">
            Prev
          </button>
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === pagination.last_page" @click="loadApplications(pagination.current_page + 1)">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">Application Details: {{ activeApp?.application_no }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="activeApp">
            <div class="row g-3">
              <div class="col-12" v-if="isSuperAdmin && activeApp.school">
                <small class="text-muted d-block">Institution / Campus</small>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-semibold">
                  <i class="bi bi-building me-1"></i> {{ activeApp.school.name }} (Code: {{ activeApp.school.code }})
                </span>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Student Full Name</small>
                <strong>{{ activeApp.first_name }} {{ activeApp.last_name || '' }}</strong>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Class Applied</small>
                <strong>{{ activeApp.class?.name || '-' }}</strong>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Current Status</small>
                <span class="badge text-capitalize" :class="statusBadge(activeApp.status)">{{ activeApp.status }}</span>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Gender</small>
                <span class="text-capitalize">{{ activeApp.gender }}</span>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Date of Birth</small>
                <span>{{ activeApp.dob || '-' }}</span>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Blood Group</small>
                <span>{{ activeApp.blood_group || '-' }}</span>
              </div>
              <div class="col-md-3">
                <small class="text-muted d-block">Aadhaar Card No.</small>
                <span class="font-monospace">{{ activeApp.aadhaar_no || '-' }}</span>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Contact Mobile</small>
                <strong>{{ activeApp.mobile }}</strong>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Email</small>
                <span>{{ activeApp.email || '-' }}</span>
              </div>
              <div class="col-12">
                <small class="text-muted d-block">Residential Address</small>
                <span>{{ activeApp.address || '-' }}</span>
              </div>

              <div class="col-12"><hr class="my-2"></div>

              <div class="col-md-4">
                <small class="text-muted d-block">Father's Name</small>
                <strong>{{ activeApp.father_name || '-' }}</strong>
              </div>
              <div class="col-md-4">
                <small class="text-muted d-block">Father's Mobile</small>
                <span>{{ activeApp.father_mobile || '-' }}</span>
              </div>
              <div class="col-md-4">
                <small class="text-muted d-block">Father's Occupation</small>
                <span>{{ activeApp.father_occupation || '-' }}</span>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Mother's Name</small>
                <span>{{ activeApp.mother_name || '-' }}</span>
              </div>
              <div class="col-md-6">
                <small class="text-muted d-block">Mother's Mobile</small>
                <span>{{ activeApp.mother_mobile || '-' }}</span>
              </div>

              <div class="col-12" v-if="activeApp.previous_school"><hr class="my-2"></div>
              <div class="col-md-6" v-if="activeApp.previous_school">
                <small class="text-muted d-block">Previous School</small>
                <span>{{ activeApp.previous_school }}</span>
              </div>
              <div class="col-md-3" v-if="activeApp.previous_class">
                <small class="text-muted d-block">Previous Class</small>
                <span>{{ activeApp.previous_class }}</span>
              </div>
              <div class="col-md-3" v-if="activeApp.previous_marks">
                <small class="text-muted d-block">Previous Marks / Grade</small>
                <span>{{ activeApp.previous_marks }}</span>
              </div>

              <div class="col-12" v-if="activeApp.admin_notes"><hr class="my-2"></div>
              <div class="col-12" v-if="activeApp.admin_notes">
                <small class="text-muted d-block">Admin Notes / Remarks</small>
                <div class="p-2 bg-light rounded text-muted small">{{ activeApp.admin_notes }}</div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">Update Application Status</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="activeApp">
            <div class="mb-3">
              <label class="form-label fw-semibold">Application Status <span class="text-danger">*</span></label>
              <select v-model="statusForm.status" class="form-select" required>
                <option value="pending">Pending</option>
                <option value="under_review">Under Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Admissions Office Notes / Remarks</label>
              <textarea v-model="statusForm.admin_notes" class="form-control" rows="3" placeholder="Add remarks or next steps for the applicant"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary rounded-pill px-4" @click="saveStatus">Update Status</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enroll Student Modal -->
    <div class="modal fade" id="enrollModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-success text-white border-0 py-3">
            <h5 class="modal-title fw-bold"><i class="bi bi-person-check-fill me-2"></i> Enroll Student Into ERP</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="activeApp">
            <p class="small text-muted mb-3">
              This will officially register <strong>{{ activeApp.first_name }} {{ activeApp.last_name || '' }}</strong> as a student in Class <strong>{{ activeApp.class?.name }}</strong>, allocate an Admission Number, and create Portal credentials.
            </p>

            <div class="mb-3">
              <label class="form-label fw-semibold">Class Section <span class="text-danger">*</span></label>
              <select v-model="enrollForm.section_id" class="form-select" required>
                <option value="" disabled>-- Select Section --</option>
                <option v-for="sec in availableSections" :key="sec.id" :value="sec.id">{{ sec.name }}</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Roll Number (Optional)</label>
              <input type="text" v-model="enrollForm.roll_no" class="form-control" placeholder="Leave blank to auto-generate">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Admission Date</label>
              <input type="date" v-model="enrollForm.admission_date" class="form-control">
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="userAcc" v-model="enrollForm.create_user_account">
              <label class="form-check-label small" for="userAcc">
                Automatically create Student Portal login account (Default password: <code>12345678</code>)
              </label>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success rounded-pill px-4" :disabled="enrolling || !enrollForm.section_id" @click="confirmEnroll">
              <span v-if="enrolling" class="spinner-border spinner-border-sm me-1"></span>
              {{ enrolling ? 'Enrolling...' : 'Confirm & Enroll' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const isSuperAdmin = computed(() => {
  const u = authStore.user;
  return u?.user_type === 'super_admin' || u?.roles?.some(r => {
    const roleName = typeof r === 'string' ? r : (r.name || '');
    return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin';
  });
});

const applications = ref([])
const counts = ref({})
const allSchools = ref([])
const selectedSchoolId = ref('')
const classOptions = ref([])
const availableSections = ref([])
const loading = ref(false)
const enrolling = ref(false)
const selectedStatus = ref('')
const selectedClass = ref('')
const search = ref('')
const activeApp = ref(null)

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
})

const statusForm = reactive({
  status: 'pending',
  admin_notes: '',
})

const enrollForm = reactive({
  section_id: '',
  roll_no: '',
  admission_date: new Date().toISOString().split('T')[0],
  create_user_account: true,
})

let searchTimeout = null
function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadApplications(1)
  }, 400)
}

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadMetadata() {
  try {
    const res = await axios.get('/api/public/admission-metadata')
    if (res.data.success) {
      classOptions.value = res.data.classes || []
    }
  } catch (err) {
    console.error('Failed to load metadata', err)
  }
}

async function loadApplications(page = 1) {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/admissions/applications', {
      params: {
        page,
        school_id: selectedSchoolId.value || undefined,
        status: selectedStatus.value || undefined,
        class_id: selectedClass.value || undefined,
        search: search.value || undefined,
      },
      ...getAuthConfig()
    })

    if (res.data.success) {
      applications.value = res.data.data.data || []
      counts.value = res.data.counts || {}
      pagination.current_page = res.data.data.current_page
      pagination.last_page = res.data.data.last_page
      pagination.total = res.data.data.total
      pagination.from = res.data.data.from
      pagination.to = res.data.data.to

      if (Array.isArray(res.data.all_schools) && res.data.all_schools.length > 0) {
        allSchools.value = res.data.all_schools
      }
      if (Array.isArray(res.data.classes)) {
        classOptions.value = res.data.classes
      }
    }
  } catch (err) {
    console.error('Failed to load applications', err)
  } finally {
    loading.value = false
  }
}

function onSchoolChange() {
  selectedClass.value = ''
  loadApplications(1)
}

function setStatusFilter(status) {
  selectedStatus.value = status
  loadApplications(1)
}

function resetFilters() {
  selectedSchoolId.value = ''
  selectedStatus.value = ''
  selectedClass.value = ''
  search.value = ''
  loadApplications(1)
}

function statusBadge(status) {
  switch (status) {
    case 'pending': return 'bg-warning text-dark'
    case 'under_review': return 'bg-info text-white'
    case 'approved': return 'bg-primary text-white'
    case 'enrolled': return 'bg-success text-white'
    case 'rejected': return 'bg-danger text-white'
    default: return 'bg-secondary'
  }
}

function formatDate(dt) {
  if (!dt) return '-'
  return new Date(dt).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}

function openViewModal(app) {
  activeApp.value = app
  const modal = new window.bootstrap.Modal(document.getElementById('viewModal'))
  modal.show()
}

function openStatusModal(app) {
  activeApp.value = app
  statusForm.status = app.status
  statusForm.admin_notes = app.admin_notes || ''
  const modal = new window.bootstrap.Modal(document.getElementById('statusModal'))
  modal.show()
}

async function saveStatus() {
  if (!activeApp.value) return
  try {
    const res = await axios.post(`/api/admin/admissions/applications/${activeApp.value.id}/status`, statusForm, getAuthConfig())
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Status Updated', timer: 1200, showConfirmButton: false })
      const modal = window.bootstrap.Modal.getInstance(document.getElementById('statusModal'))
      if (modal) modal.hide()
      loadApplications(pagination.current_page)
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to update status' })
  }
}

function openEnrollModal(app) {
  activeApp.value = app
  enrollForm.section_id = ''
  enrollForm.roll_no = ''
  
  // Find sections for this class
  const cls = classOptions.value.find(c => c.id === app.class_id)
  availableSections.value = cls?.sections || [{ id: 1, name: 'A' }]
  if (availableSections.value.length > 0) {
    enrollForm.section_id = availableSections.value[0].id
  }

  const modal = new window.bootstrap.Modal(document.getElementById('enrollModal'))
  modal.show()
}

async function confirmEnroll() {
  if (!activeApp.value || !enrollForm.section_id) return
  enrolling.value = true
  try {
    const res = await axios.post(`/api/admin/admissions/applications/${activeApp.value.id}/enroll`, enrollForm, getAuthConfig())
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Student Enrolled!',
        text: res.data.message,
      })
      const modal = window.bootstrap.Modal.getInstance(document.getElementById('enrollModal'))
      if (modal) modal.hide()
      loadApplications(pagination.current_page)
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Enrollment Error', text: err.response?.data?.message || 'Failed to enroll student' })
  } finally {
    enrolling.value = false
  }
}

onMounted(() => {
  loadMetadata()
  loadApplications(1)
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
