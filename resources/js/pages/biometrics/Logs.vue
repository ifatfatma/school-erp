<template>
  <div class="biometric-logs">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-1">
          <i class="bi bi-clock-history text-primary me-2"></i>Biometric Attendance Audit Logs
        </h5>
        <small class="text-muted">Real-time audit stream of facial scans, optical fingerprint verifications, and device push records.</small>
      </div>
      <div class="d-flex align-items-center flex-wrap gap-2">
        <!-- School Switcher for Super Admin -->
        <div v-if="isSuperAdmin && allSchools.length > 0" class="d-flex align-items-center gap-1">
          <label class="small text-muted text-nowrap fw-semibold">School:</label>
          <select v-model="selectedSchoolId" class="form-select form-select-sm" style="min-width: 170px;" @change="onSchoolChange">
            <option v-for="sch in allSchools" :key="sch.id" :value="sch.id">
              {{ sch.name }} ({{ sch.code }})
            </option>
          </select>
        </div>
        <span v-else class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
          <i class="bi bi-building me-1"></i>{{ activeSchoolName }}
        </span>

        <button class="btn btn-outline-secondary rounded-pill btn-sm" @click="fetchLogs(1)">
          <i class="bi bi-arrow-clockwise me-1"></i> Refresh Stream
        </button>
        <button class="btn btn-outline-success rounded-pill btn-sm" @click="exportCSV">
          <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-md-3">
          <input type="date" v-model="filterDate" class="form-control" @change="fetchLogs(1)" />
        </div>

        <div class="col-md-2">
          <select v-model="filterRole" class="form-select" @change="fetchLogs(1)">
            <option value="all">All Roles</option>
            <option value="student">Students</option>
            <option value="teacher">Faculty Staff</option>
          </select>
        </div>

        <div class="col-md-2">
          <select v-model="filterMethod" class="form-select" @change="fetchLogs(1)">
            <option value="all">All Methods</option>
            <option value="face">Face Recognition</option>
            <option value="fingerprint">Fingerprint / PIN</option>
            <option value="rfid_card">RFID Smart Card</option>
          </select>
        </div>

        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input
              type="text"
              v-model="search"
              class="form-control border-start-0"
              placeholder="Search person or roll no..."
              @input="debounceSearch"
            />
          </div>
        </div>

        <div class="col-md-2 text-md-end">
          <button class="btn btn-outline-secondary w-100 rounded-pill" @click="resetFilters">
            <i class="bi bi-x-circle me-1"></i> Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted text-uppercase">
            <tr>
              <th class="ps-4">Timestamp</th>
              <th>Person</th>
              <th>Role</th>
              <th>Verification Method</th>
              <th>Confidence</th>
              <th>Status</th>
              <th class="pe-4">Terminal Device</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                <span class="text-muted">Loading audit trail...</span>
              </td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-2 d-block mb-2 text-muted"></i>
                No biometric punches recorded for the selected criteria.
              </td>
            </tr>
            <tr v-for="log in logs" :key="log.id">
              <td class="ps-4 font-monospace small">
                <span class="fw-semibold text-dark">{{ log.punch_date }}</span>
                <span class="text-primary ms-2">{{ formatTime(log.punch_time) }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img
                    :src="log.person_photo || defaultAvatar"
                    class="rounded-circle border"
                    style="width: 36px; height: 36px; object-fit: cover;"
                    alt=""
                  />
                  <div>
                    <strong class="d-block text-dark">{{ log.person_name }}</strong>
                    <small class="text-muted font-monospace">{{ log.person_code }}</small>
                  </div>
                </div>
              </td>
              <td>
                <span v-if="log.person_type === 'student'" class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  Student
                </span>
                <span v-else class="badge bg-success-subtle text-success border border-success-subtle">
                  Faculty Staff
                </span>
              </td>
              <td>
                <span v-if="log.verification_method === 'face'" class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                  <i class="bi bi-camera-video me-1"></i> AI Face Match
                </span>
                <span v-else-if="log.verification_method === 'fingerprint'" class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  <i class="bi bi-fingerprint me-1"></i> Fingerprint / PIN
                </span>
                <span v-else class="badge bg-secondary-subtle text-secondary border">
                  <i class="bi bi-credit-card me-1"></i> RFID Card
                </span>
              </td>
              <td>
                <span class="badge bg-light text-dark border font-monospace">
                  {{ log.confidence_score ? (log.confidence_score + '%') : '99.0%' }}
                </span>
              </td>
              <td>
                <span v-if="log.attendance_status === 'Present'" class="badge bg-success rounded-pill px-3 py-1">
                  Present
                </span>
                <span v-else-if="log.attendance_status === 'Late'" class="badge bg-warning text-dark rounded-pill px-3 py-1">
                  Late Arrival
                </span>
                <span v-else class="badge bg-secondary rounded-pill px-3 py-1">
                  {{ log.attendance_status || 'Logged' }}
                </span>
              </td>
              <td class="pe-4 small text-muted">
                {{ log.device_name || 'WebCam AI Kiosk' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="d-flex justify-content-between align-items-center p-3 border-top small text-muted">
        <div>Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} punches</div>
        <div class="btn-group btn-group-sm">
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === 1" @click="fetchLogs(pagination.current_page - 1)">
            Prev
          </button>
          <button class="btn btn-outline-secondary" :disabled="pagination.current_page === pagination.last_page" @click="fetchLogs(pagination.current_page + 1)">
            Next
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/api/axios'

const isSuperAdmin = ref(false)
const allSchools = ref([])
const selectedSchoolId = ref('')
const activeSchoolName = ref('School ERP')

const logs = ref([])
const loading = ref(false)
const filterDate = ref(new Date().toISOString().split('T')[0])
const filterRole = ref('all')
const filterMethod = ref('all')
const search = ref('')

const defaultAvatar = 'https://ui-avatars.com/api/?background=2563eb&color=fff&name=User'

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
})

let searchTimeout = null
function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchLogs(1)
  }, 400)
}

async function loadOverview() {
  try {
    const res = await api.get('/biometrics/overview', {
      params: { school_id: selectedSchoolId.value || undefined }
    })
    if (res.data.success) {
      activeSchoolName.value = res.data.school?.name || 'School ERP'
      if (res.data.is_super_admin && Array.isArray(res.data.all_schools)) {
        isSuperAdmin.value = true
        allSchools.value = res.data.all_schools
        if (!selectedSchoolId.value && res.data.school?.id) {
          selectedSchoolId.value = res.data.school.id
        }
      }
    }
  } catch (err) {
    console.error('Failed to load biometric overview', err)
  }
}

function onSchoolChange() {
  loadOverview()
  fetchLogs(1)
}

async function fetchLogs(page = 1) {
  loading.value = true
  try {
    const res = await api.get('/biometrics/logs', {
      params: {
        page,
        date: filterDate.value || undefined,
        person_type: filterRole.value !== 'all' ? filterRole.value : undefined,
        method: filterMethod.value !== 'all' ? filterMethod.value : undefined,
        search: search.value || undefined,
        school_id: selectedSchoolId.value || undefined,
      },
    })
    if (res.data.success) {
      logs.value = res.data.data.data || []
      pagination.current_page = res.data.data.current_page
      pagination.last_page = res.data.data.last_page
      pagination.total = res.data.data.total
      pagination.from = res.data.data.from
      pagination.to = res.data.data.to
    }
  } catch (err) {
    console.error('Failed to load biometric logs', err)
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  filterDate.value = new Date().toISOString().split('T')[0]
  filterRole.value = 'all'
  filterMethod.value = 'all'
  search.value = ''
  fetchLogs(1)
}

function formatTime(t) {
  if (!t) return ''
  const parts = t.split(':')
  if (parts.length >= 2) {
    let hour = parseInt(parts[0], 10)
    const ampm = hour >= 12 ? 'PM' : 'AM'
    hour = hour % 12 || 12
    return `${hour}:${parts[1]} ${ampm}`
  }
  return t
}

function exportCSV() {
  if (logs.value.length === 0) return
  const headers = ['Date', 'Time', 'Person', 'Code', 'Role', 'Method', 'Confidence', 'Status', 'Device']
  const rows = logs.value.map(l => [
    l.punch_date,
    l.punch_time,
    `"${l.person_name}"`,
    l.person_code,
    l.person_type,
    l.verification_method,
    l.confidence_score + '%',
    l.attendance_status,
    `"${l.device_name || ''}"`,
  ])

  const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `biometric_logs_${filterDate.value}.csv`
  a.click()
  window.URL.revokeObjectURL(url)
}

onMounted(() => {
  loadOverview()
  fetchLogs(1)
})
</script>
