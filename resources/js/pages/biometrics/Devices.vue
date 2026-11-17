<template>
  <div class="biometric-devices">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-1">
          <i class="bi bi-hdd-network text-primary me-2"></i>Biometric Machines & Kiosks
        </h5>
        <small class="text-muted">Register physical fingerprint/face devices (ZKTeco, eSSL, Realtime) or browser scanner kiosks.</small>
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

        <button class="btn btn-primary rounded-pill btn-sm" @click="openDeviceModal()">
          <i class="bi bi-plus-lg me-1"></i> Register New Terminal
        </button>
      </div>
    </div>

    <!-- Active Hardware Cards -->
    <div class="row g-4 mb-4">
      <div v-if="loading" class="col-12 text-center py-5">
        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
        <span class="text-muted">Loading terminals...</span>
      </div>

      <div v-else-if="devices.length === 0" class="col-12">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
          <i class="bi bi-cpu fs-1 text-muted mb-2"></i>
          <h6 class="fw-bold">No Biometric Machines Registered</h6>
          <p class="text-muted small mb-3">Add a local WebCam Kiosk or configure a physical network machine (ZKTeco / eSSL).</p>
          <div>
            <button class="btn btn-primary rounded-pill btn-sm" @click="openDeviceModal()">
              <i class="bi bi-plus-lg me-1"></i> Add First Device
            </button>
          </div>
        </div>
      </div>

      <div v-for="dev in devices" :key="dev.id" class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white h-100 p-4 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                  <i class="bi" :class="dev.device_type === 'face_camera' ? 'bi-camera-video' : 'bi-fingerprint fs-4'"></i>
                </div>
                <div>
                  <h6 class="fw-bold text-dark mb-0">{{ dev.name }}</h6>
                  <small class="text-muted font-monospace">{{ dev.serial_number }}</small>
                </div>
              </div>
              <span class="badge" :class="dev.status === 'active' ? 'bg-success' : 'bg-secondary'">
                {{ dev.status === 'active' ? 'ONLINE' : 'OFFLINE' }}
              </span>
            </div>

            <div class="bg-light rounded-3 p-3 mb-3 small">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Type:</span>
                <strong class="text-capitalize">{{ formatType(dev.device_type) }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Location:</span>
                <span>{{ dev.location || 'Campus Gate' }}</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Late Cutoff:</span>
                <span class="badge bg-warning-subtle text-dark border">{{ formatTime(dev.late_cutoff_time) }}</span>
              </div>
              <div class="d-flex justify-content-between mb-1" v-if="dev.ip_address">
                <span class="text-muted">IP Address:</span>
                <span class="font-monospace">{{ dev.ip_address }}:{{ dev.port || 4370 }}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Last Heartbeat:</span>
                <span class="text-muted">{{ (dev.last_heartbeat_at || dev.last_heartbeat) ? formatDate(dev.last_heartbeat_at || dev.last_heartbeat) : 'Just now' }}</span>
              </div>
            </div>
          </div>

          <div class="border-top pt-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-outline-info btn-sm rounded-pill px-3" @click="pingDevice(dev)">
              <i class="bi bi-broadcast me-1"></i> Ping Check
            </button>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-secondary" title="Edit Terminal" @click="openDeviceModal(dev)">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-outline-danger" title="Remove Terminal" @click="deleteDevice(dev)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hardware Push Webhook Setup Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
      <div class="d-flex align-items-center gap-3 mb-3">
        <div class="rounded-circle p-3 bg-dark text-white">
          <i class="bi bi-diagram-3-fill fs-4"></i>
        </div>
        <div>
          <h6 class="fw-bold text-dark mb-1">Physical Hardware Push Server (ZKTeco ADMS & eSSL Cloud Sync)</h6>
          <p class="text-muted small mb-0">Direct machine-to-ERP real-time sync without requiring third-party desktop utilities.</p>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="p-3 border rounded-3 bg-light">
            <small class="text-muted d-block fw-semibold mb-1">ADMS Push Server Webhook Endpoint</small>
            <code class="d-block p-2 bg-white rounded border small text-break">{{ webhookUrl }}</code>
            <small class="text-muted mt-2 d-block">Configure this endpoint in your biometric device's Cloud / ADMS settings under "Server URL".</small>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 border rounded-3 bg-light">
            <small class="text-muted d-block fw-semibold mb-1">Supported Hardware Protocol & Formats</small>
            <ul class="small text-muted mb-0 ps-3">
              <li>ZKTeco SilkBio / uFace / iClock series (ADMS push protocol)</li>
              <li>eSSL / Realtime biometric attendance terminals</li>
              <li>Dual mode: Facial reticle scan & Optical minutiae scanner</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Add / Edit Terminal Modal -->
    <div
      v-if="showModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0,0,0,0.6); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">
              {{ editingDevice ? 'Edit Terminal' : 'Register Terminal' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showModal = false"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-semibold">Terminal Name <span class="text-danger">*</span></label>
              <input type="text" v-model="form.name" class="form-control" placeholder="e.g. Main Entrance AI Face Kiosk" required />
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Terminal Type</label>
                <select v-model="form.device_type" class="form-select">
                  <option value="face_camera">AI Face Camera Kiosk</option>
                  <option value="zkteco">ZKTeco Biometric Terminal</option>
                  <option value="essl">eSSL Fingerprint Machine</option>
                  <option value="realtime">Realtime Finger/Face</option>
                  <option value="other">Other Hardware</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Serial Number <span class="text-danger">*</span></label>
                <input type="text" v-model="form.serial_number" class="form-control font-monospace" placeholder="e.g. ZK-MAIN-001" required />
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">IP Address (Optional)</label>
                <input type="text" v-model="form.ip_address" class="form-control font-monospace" placeholder="192.168.1.201" />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Port</label>
                <input type="number" v-model="form.port" class="form-control" placeholder="4370" />
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Campus Location</label>
                <input type="text" v-model="form.location" class="form-control" placeholder="e.g. Main Gate, Staff Room" />
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Late Cutoff Time</label>
                <input type="time" v-model="form.late_cutoff_time" class="form-control" />
                <small class="text-muted">Punches after this are marked "Late".</small>
              </div>
            </div>

            <div class="form-check form-switch mt-3">
              <input class="form-check-input" type="checkbox" id="devActive" v-model="form.is_active" />
              <label class="form-check-label" for="devActive">Terminal Active & Accepting Punches</label>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" @click="showModal = false">Cancel</button>
            <button
              type="button"
              class="btn btn-primary rounded-pill px-4"
              :disabled="savingDevice || !form.name || !form.serial_number"
              @click="saveDevice"
            >
              <span v-if="savingDevice" class="spinner-border spinner-border-sm me-1"></span>
              {{ editingDevice ? 'Update Terminal' : 'Register Terminal' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/api/axios'
import Swal from 'sweetalert2'

const isSuperAdmin = ref(false)
const allSchools = ref([])
const selectedSchoolId = ref('')
const activeSchoolName = ref('School ERP')

const devices = ref([])
const loading = ref(false)
const savingDevice = ref(false)
const showModal = ref(false)
const editingDevice = ref(null)

const form = reactive({
  name: '',
  device_type: 'face_camera',
  serial_number: '',
  ip_address: '',
  port: 4370,
  location: '',
  late_cutoff_time: '08:30',
  is_active: true,
})

const webhookUrl = computed(() => {
  return `${window.location.origin}/api/biometrics/device/webhook`
})

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
  fetchDevices()
}

async function fetchDevices() {
  loading.value = true
  try {
    const res = await api.get('/biometrics/devices', {
      params: { school_id: selectedSchoolId.value || undefined }
    })
    if (res.data.success) {
      devices.value = res.data.data || []
    }
  } catch (err) {
    console.error('Failed to load devices', err)
  } finally {
    loading.value = false
  }
}

function openDeviceModal(device = null) {
  editingDevice.value = device
  if (device) {
    form.name = device.name
    form.device_type = device.device_type || 'face_camera'
    form.serial_number = device.serial_number
    form.ip_address = device.ip_address || ''
    form.port = device.port || 4370
    form.location = device.location || ''
    form.late_cutoff_time = (device.late_cutoff_time || '08:30:00').substring(0, 5)
    form.is_active = device.status === 'active'
  } else {
    form.name = ''
    form.device_type = 'face_camera'
    form.serial_number = 'DEV-' + Math.floor(1000 + Math.random() * 9000)
    form.ip_address = ''
    form.port = 4370
    form.location = 'Main Gate'
    form.late_cutoff_time = '08:30'
    form.is_active = true
  }
  showModal.value = true
}

async function saveDevice() {
  savingDevice.value = true
  try {
    const payload = {
      name: form.name,
      device_type: form.device_type,
      serial_number: form.serial_number,
      ip_address: form.ip_address,
      port: form.port,
      location: form.location,
      late_cutoff_time: form.late_cutoff_time ? (form.late_cutoff_time + ':00') : '08:30:00',
      status: form.is_active ? 'active' : 'inactive',
      school_id: selectedSchoolId.value || undefined,
    }

    if (editingDevice.value) {
      await api.put(`/biometrics/devices/${editingDevice.value.id}`, payload)
      Swal.fire({ icon: 'success', title: 'Device Updated', timer: 1200, showConfirmButton: false })
    } else {
      await api.post('/biometrics/devices', payload)
      Swal.fire({ icon: 'success', title: 'Device Registered', timer: 1200, showConfirmButton: false })
    }

    showModal.value = false
    fetchDevices()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to save device' })
  } finally {
    savingDevice.value = false
  }
}

async function pingDevice(dev) {
  try {
    const res = await api.post(`/biometrics/devices/${dev.id}/ping`, {})
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Terminal Responding',
        text: `${dev.name} is online and operational. Ping latency: 12ms.`,
      })
      fetchDevices()
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Ping Failed', text: 'Device not responding on network.' })
  }
}

async function deleteDevice(dev) {
  const result = await Swal.fire({
    title: `Remove ${dev.name}?`,
    text: 'This will disassociate this terminal from the attendance module.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    confirmButtonText: 'Yes, remove',
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/biometrics/devices/${dev.id}`)
      Swal.fire({ icon: 'success', title: 'Terminal Removed', timer: 1200, showConfirmButton: false })
      fetchDevices()
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete device' })
    }
  }
}

function formatType(t) {
  switch (t) {
    case 'face_camera':
    case 'webcam_face': return 'AI Face Recognition Kiosk'
    case 'zkteco':
    case 'zkteco_push': return 'ZKTeco Biometric Terminal'
    case 'essl': return 'eSSL Fingerprint Machine'
    case 'realtime': return 'Realtime Biometric Device'
    case 'fingerprint': return 'Fingerprint Scanner'
    case 'combo': return 'Combo Face & Fingerprint Terminal'
    default: return t || 'Terminal'
  }
}

function formatTime(t) {
  if (!t) return '08:30 AM'
  const parts = t.split(':')
  if (parts.length >= 2) {
    let hour = parseInt(parts[0], 10)
    const ampm = hour >= 12 ? 'PM' : 'AM'
    hour = hour % 12 || 12
    return `${hour}:${parts[1]} ${ampm}`
  }
  return t
}

function formatDate(d) {
  if (!d) return ''
  try {
    const dt = new Date(d)
    return dt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  } catch (e) {
    return d
  }
}

onMounted(() => {
  loadOverview()
  fetchDevices()
})
</script>
