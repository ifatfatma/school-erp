<template>
  <div class="container-fluid py-3">
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Admissions & School Web' },
        { label: 'Website Inquiries' }
      ]"
    />

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <div>
        <h4 class="fw-bold mb-1">Website Inquiries & Messages</h4>
        <small class="text-muted">Review incoming prospective messages submitted through the school website contact form</small>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- School Selector for Super Admin -->
        <div v-if="isSuperAdmin && allSchools.length > 0" class="d-flex align-items-center gap-2 bg-white border rounded-pill px-3 py-1 shadow-sm">
          <i class="bi bi-buildings text-primary"></i>
          <span class="small text-muted fw-semibold">School:</span>
          <select v-model="selectedSchoolId" class="form-select form-select-sm border-0 bg-transparent fw-bold text-primary pe-4 py-0" style="width: auto; cursor: pointer;" @change="onSchoolChange">
            <option value="">All Schools</option>
            <option v-for="s in allSchools" :key="'inq-sch-'+s.id" :value="s.id">
              {{ s.name }} ({{ s.code }})
            </option>
          </select>
        </div>

        <select v-model="selectedStatus" class="form-select form-select-sm rounded-pill shadow-sm" style="width: auto;" @change="loadInquiries(1)">
          <option value="">All Statuses</option>
          <option value="unread">Unread Only</option>
          <option value="read">Read</option>
          <option value="responded">Responded</option>
        </select>
      </div>
    </div>

    <!-- Inquiries Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Sender Name</th>
              <th v-if="isSuperAdmin">School / Campus</th>
              <th>Contact Details</th>
              <th>Subject</th>
              <th>Message Snippet</th>
              <th>Received Date</th>
              <th>Status</th>
              <th class="pe-4 text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td :colspan="isSuperAdmin ? 8 : 7" class="text-center py-5">
                <span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading inquiries...
              </td>
            </tr>
            <tr v-else-if="inquiries.length === 0">
              <td :colspan="isSuperAdmin ? 8 : 7" class="text-center py-5 text-muted">
                No website inquiries found.
              </td>
            </tr>
            <tr v-for="inq in inquiries" :key="inq.id" :class="{ 'fw-semibold': inq.status === 'unread' }">
              <td class="ps-4 text-dark">{{ inq.name }}</td>
              <td v-if="isSuperAdmin">
                <span v-if="inq.school" class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                  <i class="bi bi-building me-1"></i> {{ inq.school.name }}
                  <small class="text-muted font-monospace">({{ inq.school.code }})</small>
                </span>
                <span v-else class="badge bg-secondary-subtle text-secondary border">Global / SaaS Platform</span>
              </td>
              <td>
                <div class="small">{{ inq.email }}</div>
                <small class="text-muted">{{ inq.phone || '-' }}</small>
              </td>
              <td>{{ inq.subject || 'General Inquiry' }}</td>
              <td class="text-truncate" style="max-width: 250px;">
                <small class="text-muted">{{ inq.message }}</small>
              </td>
              <td class="small text-muted">{{ formatDate(inq.created_at) }}</td>
              <td>
                <span class="badge rounded-pill text-capitalize" :class="statusBadge(inq.status)">
                  {{ inq.status }}
                </span>
              </td>
              <td class="pe-4 text-end">
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-info" title="View Full Message" @click="viewInquiry(inq)">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button
                    v-if="inq.status !== 'responded'"
                    class="btn btn-outline-success"
                    title="Mark Responded"
                    @click="updateStatus(inq, 'responded')"
                  >
                    <i class="bi bi-check-lg"></i>
                  </button>
                  <button class="btn btn-outline-danger" title="Delete" @click="deleteInq(inq)">
                    <i class="bi bi-trash"></i>
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
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === 1" @click="loadInquiries(pagination.current_page - 1)">
            Prev
          </button>
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === pagination.last_page" @click="loadInquiries(pagination.current_page + 1)">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- View Message Modal -->
    <div class="modal fade" id="inqModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">Website Inquiry</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4" v-if="activeInq">
            <div class="mb-3" v-if="isSuperAdmin && activeInq.school">
              <small class="text-muted d-block">Institution / Campus</small>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-semibold">
                <i class="bi bi-building me-1"></i> {{ activeInq.school.name }} (Code: {{ activeInq.school.code }})
              </span>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Sender Name</small>
              <strong>{{ activeInq.name }}</strong>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <small class="text-muted d-block">Email</small>
                <a :href="`mailto:${activeInq.email}`" class="text-decoration-none">{{ activeInq.email }}</a>
              </div>
              <div class="col-6">
                <small class="text-muted d-block">Phone</small>
                <span>{{ activeInq.phone || '-' }}</span>
              </div>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Subject</small>
              <strong>{{ activeInq.subject || 'General Inquiry' }}</strong>
            </div>
            <div class="mb-3">
              <small class="text-muted d-block">Message</small>
              <div class="p-3 bg-light rounded-3 text-dark small" style="white-space: pre-wrap;">
                {{ activeInq.message }}
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2">
              <small class="text-muted">Received: {{ formatDate(activeInq.created_at) }}</small>
              <span class="badge text-capitalize" :class="statusBadge(activeInq.status)">{{ activeInq.status }}</span>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            <button
              v-if="activeInq && activeInq.status !== 'responded'"
              type="button"
              class="btn btn-success rounded-pill px-4"
              @click="updateStatus(activeInq, 'responded')"
            >
              <i class="bi bi-check-lg me-1"></i> Mark Responded
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

const inquiries = ref([])
const allSchools = ref([])
const selectedSchoolId = ref('')
const loading = ref(false)
const selectedStatus = ref('')
const activeInq = ref(null)

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadInquiries(page = 1) {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/admissions/inquiries', {
      params: {
        page,
        school_id: selectedSchoolId.value || undefined,
        status: selectedStatus.value || undefined,
      },
      ...getAuthConfig()
    })
    if (res.data.success) {
      inquiries.value = res.data.data.data || []
      pagination.current_page = res.data.data.current_page
      pagination.last_page = res.data.data.last_page
      pagination.total = res.data.data.total
      pagination.from = res.data.data.from
      pagination.to = res.data.data.to

      if (Array.isArray(res.data.all_schools) && res.data.all_schools.length > 0) {
        allSchools.value = res.data.all_schools
      }
    }
  } catch (err) {
    console.error('Failed to load inquiries', err)
  } finally {
    loading.value = false
  }
}

function onSchoolChange() {
  loadInquiries(1)
}

function statusBadge(status) {
  switch (status) {
    case 'unread': return 'bg-warning text-dark'
    case 'read': return 'bg-info text-white'
    case 'responded': return 'bg-success text-white'
    default: return 'bg-secondary'
  }
}

function formatDate(dt) {
  if (!dt) return '-'
  return new Date(dt).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })
}

function viewInquiry(inq) {
  activeInq.value = inq
  if (inq.status === 'unread') {
    updateStatus(inq, 'read', false)
  }
  const modal = new window.bootstrap.Modal(document.getElementById('inqModal'))
  modal.show()
}

async function updateStatus(inq, status, reload = true) {
  try {
    const res = await axios.post(`/api/admin/admissions/inquiries/${inq.id}/status`, { status }, getAuthConfig())
    if (res.data.success) {
      inq.status = status
      if (reload) {
        Swal.fire({ icon: 'success', title: 'Status Updated', timer: 1000, showConfirmButton: false })
        const modal = window.bootstrap.Modal.getInstance(document.getElementById('inqModal'))
        if (modal) modal.hide()
        loadInquiries(pagination.current_page)
      }
    }
  } catch (err) {
    console.error('Failed to update status', err)
  }
}

async function deleteInq(inq) {
  const result = await Swal.fire({
    title: 'Delete Inquiry?',
    text: `Are you sure you want to delete message from ${inq.name}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/admin/admissions/inquiries/${inq.id}`, getAuthConfig())
      Swal.fire({ icon: 'success', title: 'Deleted', timer: 1000, showConfirmButton: false })
      loadInquiries(pagination.current_page)
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete inquiry' })
    }
  }
}

onMounted(() => {
  loadInquiries(1)
})
</script>
