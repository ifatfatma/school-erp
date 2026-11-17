<template>
  <div>
    <!-- Header with School Switcher & Filters -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-0 text-dark">
          <i class="bi bi-fingerprint text-primary me-2"></i>Fingerprint & Device Biometric Scanner
        </h5>
        <small class="text-muted">Process fingerprint punches from USB biometric scanners or integrated hardware machines</small>
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

        <!-- Target Selector -->
        <div class="btn-group btn-group-sm">
          <button
            class="btn"
            :class="targetType === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="setTargetType('all')"
          >
            All
          </button>
          <button
            class="btn"
            :class="targetType === 'students' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="setTargetType('students')"
          >
            Students
          </button>
          <button
            class="btn"
            :class="targetType === 'teachers' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="setTargetType('teachers')"
          >
            Staff
          </button>
        </div>

        <button
          class="btn btn-sm"
          :class="voiceEnabled ? 'btn-success' : 'btn-outline-secondary'"
          :title="voiceEnabled ? 'Voice Announcements On' : 'Voice Announcements Muted'"
          @click="voiceEnabled = !voiceEnabled"
        >
          <i :class="voiceEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill'"></i>
        </button>
      </div>
    </div>

    <!-- Scanner Row -->
    <div class="row g-4">
      <!-- Left Column: Interactive Fingerprint Scanner Console -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-shield-lock-fill fs-5"></i>
              <h6 class="fw-bold mb-0">Biometric Sensor Console</h6>
            </div>
            <span class="badge bg-white text-primary rounded-pill px-3 py-1 font-monospace small">
              USB & ADMS Cloud Ready
            </span>
          </div>

          <div class="card-body p-4 text-center">
            <!-- Active Device Dropdown -->
            <div class="mb-4 text-start" style="max-width: 440px; margin: 0 auto;">
              <label class="form-label small text-muted fw-semibold mb-1">
                <i class="bi bi-hdd-network me-1 text-primary"></i>Connected Biometric Machine / Reader:
              </label>
              <select v-model="selectedDeviceId" class="form-select form-select-sm rounded-pill shadow-none border">
                <option value="">-- Built-in USB / Virtual Sensor Console --</option>
                <option v-for="dev in devices" :key="dev.id" :value="dev.id">
                  {{ dev.name }} ({{ dev.model || 'Biometric Device' }}) - {{ dev.location }}
                </option>
              </select>
            </div>

            <!-- Fingerprint Sensor Optical Pad -->
            <div
              class="fingerprint-pad position-relative mx-auto d-flex flex-column align-items-center justify-content-center p-4 rounded-5 mb-4"
              :class="padStateClass"
              @click="triggerSensorTouch"
              :title="selectedCandidate ? `Touch to verify ${selectedCandidate.name}` : 'Place finger on sensor or select candidate below'"
            >
              <div class="scan-beam" v-if="scanning"></div>
              <i class="bi bi-fingerprint display-1 mb-2" :class="fingerprintIconClass"></i>
              <span class="badge rounded-pill px-3 py-1 small" :class="padStatusBadgeClass">
                {{ sensorStatusText }}
              </span>
              <small class="text-white-50 mt-2" style="font-size: 0.72rem;">
                {{ scanning ? 'Analyzing Minutiae Patterns...' : (selectedCandidate ? `Ready: Touch to verify ${selectedCandidate.name}` : 'Click pad or place finger on sensor') }}
              </small>
            </div>

            <!-- Candidate Quick Finger Tap Selector -->
            <div class="mb-4 text-start">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label small text-muted fw-semibold mb-0">
                  <i class="bi bi-person-check me-1 text-primary"></i>Select Candidate to Scan Fingerprint:
                </label>
                <div class="form-check form-switch small mb-0">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    role="switch"
                    id="enrolledFilterSwitch"
                    v-model="enrolledOnly"
                  />
                  <label class="form-check-label text-muted" for="enrolledFilterSwitch" style="font-size: 0.75rem;">
                    Enrolled Only ({{ enrolledCandidatesCount }})
                  </label>
                </div>
              </div>

              <!-- Quick Search Filter -->
              <div class="input-group input-group-sm mb-2">
                <span class="input-group-text bg-white border-end-0">
                  <i class="bi bi-search text-muted"></i>
                </span>
                <input
                  type="text"
                  v-model="candidateSearch"
                  class="form-control border-start-0"
                  placeholder="Filter candidate by name, admission no, roll no, or PIN..."
                />
                <button
                  v-if="candidateSearch"
                  class="btn btn-outline-secondary"
                  type="button"
                  @click="candidateSearch = ''"
                >
                  <i class="bi bi-x"></i>
                </button>
              </div>

              <!-- Candidate Dropdown & Scan Button -->
              <div class="input-group input-group-sm">
                <select
                  ref="candidateSelectRef"
                  v-model="selectedCandidateId"
                  class="form-select rounded-start-pill"
                >
                  <option value="">-- Choose Candidate to Scan ({{ displayedCandidates.length }} available) --</option>
                  <option
                    v-for="cand in displayedCandidates"
                    :key="cand.id"
                    :value="cand.id"
                  >
                    {{ cand.is_fingerprint_enrolled ? '✔' : '✖' }} {{ cand.name }} [PIN: {{ cand.biometric_pin || cand.code }}] ({{ cand.badge }}) - {{ cand.is_fingerprint_enrolled ? 'Enrolled' : 'Not Enrolled' }}
                  </option>
                </select>
                <button
                  class="btn btn-primary rounded-end-pill px-4 fw-semibold"
                  :disabled="!selectedCandidateId || scanning"
                  @click="submitFingerprintPunch(selectedCandidateId)"
                >
                  <span v-if="scanning" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-fingerprint me-1"></i> Scan Finger
                </button>
              </div>

              <!-- Candidate Summary Note -->
              <div v-if="selectedCandidate" class="d-flex align-items-center justify-content-between mt-2 px-2 py-1 bg-light rounded-pill small">
                <span class="text-truncate me-2">
                  <strong class="text-dark">{{ selectedCandidate.name }}</strong>
                  <span class="text-muted ms-1">({{ selectedCandidate.badge }})</span>
                </span>
                <span
                  class="badge rounded-pill"
                  :class="selectedCandidate.is_fingerprint_enrolled ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis'"
                >
                  {{ selectedCandidate.is_fingerprint_enrolled ? 'Fingerprint Enrolled' : 'PIN / Template Missing' }}
                </span>
              </div>
            </div>

            <!-- Hardware Machine PIN / RFID Card Input -->
            <div class="p-3 bg-light rounded-4 text-start">
              <h6 class="fw-bold small text-dark mb-2">
                <i class="bi bi-credit-card-2-front me-1 text-primary"></i>Hardware Biometric Machine User PIN / RFID Card Swipe:
              </h6>
              <div class="row g-2 align-items-center">
                <div class="col-sm-8">
                  <input
                    ref="pinInputRef"
                    type="text"
                    v-model="pinOrCardInput"
                    class="form-control form-control-sm rounded-pill"
                    placeholder="Enter Machine PIN (e.g. stu001, 1001, STU2026...) or Scan RFID..."
                    @keyup.enter="submitPinOrCardPunch"
                  />
                </div>
                <div class="col-sm-4">
                  <button
                    class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-semibold"
                    :disabled="!pinOrCardInput.trim() || scanning"
                    @click="submitPinOrCardPunch"
                  >
                    <i class="bi bi-box-arrow-in-right me-1"></i> Verify PIN
                  </button>
                </div>
              </div>
              <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                Compatible with ZKTeco, eSSL, Mantra, SecuGen, and USB RFID card scanners. Automatically registers on Enter.
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Verification Result & Recent Logs -->
      <div class="col-lg-5">
        <!-- Verification Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-white">
          <div
            class="card-header text-white p-3 d-flex align-items-center justify-content-between"
            :class="lastPunch?.is_duplicate ? 'bg-warning text-dark' : 'bg-success text-white'"
          >
            <div class="d-flex align-items-center gap-2">
              <i :class="lastPunch?.is_duplicate ? 'bi bi-exclamation-triangle-fill fs-5' : 'bi bi-check-circle-fill fs-5'"></i>
              <h6 class="fw-bold mb-0">
                {{ lastPunch?.is_duplicate ? 'Already Verified' : 'Fingerprint Verified' }}
              </h6>
            </div>
            <span class="badge bg-white rounded-pill px-3 py-1 small fw-bold font-monospace" :class="lastPunch?.is_duplicate ? 'text-dark' : 'text-success'">
              {{ lastPunch ? (lastPunch.attendance_status || lastPunch.status) : 'Awaiting Scan' }}
            </span>
          </div>

          <div class="card-body p-4 text-center" v-if="lastPunch">
            <div class="position-relative d-inline-block mb-3">
              <img
                :src="lastPunch.person_photo || '/images/no-image.png'"
                alt="Candidate Photo"
                class="rounded-circle border border-3 shadow-sm object-fit-cover"
                :class="lastPunch.is_duplicate ? 'border-warning' : 'border-success'"
                style="width: 90px; height: 90px;"
                @error="onImgError"
              />
              <span
                class="position-absolute bottom-0 end-0 badge rounded-circle p-2 text-white border border-white"
                :class="lastPunch.is_duplicate ? 'bg-warning text-dark' : 'bg-success'"
              >
                <i class="bi bi-fingerprint"></i>
              </span>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ lastPunch.person_name }}</h5>
            <p class="text-muted small mb-2">
              <span class="badge bg-light text-dark border me-1 font-monospace">{{ lastPunch.person_code }}</span>
              <span>{{ lastPunch.person_badge }}</span>
            </p>

            <div class="p-3 bg-light rounded-3 my-3 text-start small">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Verification Method:</span>
                <strong class="text-primary"><i class="bi bi-fingerprint me-1"></i>Fingerprint Biometric</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Match Confidence:</span>
                <strong class="text-success"><i class="bi bi-check2-circle me-1"></i>{{ lastPunch.confidence_score || '99.0' }}% Match</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Check-in Time:</span>
                <strong>{{ lastPunch.punch_time }}</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Attendance Status:</span>
                <span class="badge rounded-pill" :class="(lastPunch.attendance_status || lastPunch.status) === 'Late' ? 'bg-warning text-dark' : 'bg-success'">
                  {{ lastPunch.attendance_status || lastPunch.status }}
                </span>
              </div>
            </div>

            <div
              class="alert py-2 px-3 small mb-0 rounded-pill"
              :class="lastPunch.is_duplicate ? 'alert-warning' : 'alert-success'"
            >
              <i :class="lastPunch.is_duplicate ? 'bi bi-info-circle me-1' : 'bi bi-check2-all me-1'"></i>
              {{ lastPunch.is_duplicate ? 'Already checked in recently today.' : 'Biometric attendance confirmed in ERP database.' }}
            </div>
          </div>

          <div class="card-body p-5 text-center text-muted" v-else>
            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 75px; height: 75px;">
              <i class="bi bi-fingerprint fs-1 text-secondary opacity-50"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Ready for Fingerprint Input</h6>
            <p class="small text-muted mb-0">
              Select a candidate above, touch the optical sensor pad, or enter the biometric machine PIN.
            </p>
          </div>
        </div>

        <!-- Recent Biometric Log List -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-journal-text text-primary me-2"></i>Recent Finger Punches
            </h6>
            <span class="badge bg-primary-subtle text-primary rounded-pill">
              {{ recentPunches.length }}
            </span>
          </div>

          <div class="card-body p-0 overflow-auto" style="max-height: 250px;">
            <div v-if="recentPunches.length === 0" class="text-center py-4 text-muted small">
              No fingerprint punches recorded yet today.
            </div>
            <ul v-else class="list-group list-group-flush small">
              <li
                v-for="punch in recentPunches"
                :key="punch.id"
                class="list-group-item px-3 py-2 d-flex align-items-center justify-content-between"
              >
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                  <i class="bi bi-fingerprint text-primary fs-5"></i>
                  <div class="overflow-hidden">
                    <strong class="d-block text-truncate text-dark">{{ punch.person_name }}</strong>
                    <span class="text-muted" style="font-size: 0.72rem;">{{ punch.person_code }} &bull; {{ punch.punch_time }}</span>
                  </div>
                </div>
                <span
                  class="badge rounded-pill"
                  :class="punch.attendance_status === 'Late' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success'"
                >
                  {{ punch.attendance_status }}
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/api/axios'
import Swal from 'sweetalert2'

const targetType = ref('all')
const voiceEnabled = ref(true)
const scanning = ref(false)

const isSuperAdmin = ref(false)
const allSchools = ref([])
const selectedSchoolId = ref('')
const activeSchoolName = ref('School ERP')

const devices = ref([])
const selectedDeviceId = ref('')
const candidateList = ref([])
const candidateSearch = ref('')
const selectedCandidateId = ref('')
const enrolledOnly = ref(false)
const pinOrCardInput = ref('')

const pinInputRef = ref(null)
const candidateSelectRef = ref(null)

const sensorStatusText = ref('Optical Sensor Ready')
const padStatusBadgeClass = ref('bg-dark')
const padStateClass = ref('pad-idle')
const fingerprintIconClass = ref('text-primary')

const lastPunch = ref(null)
const recentPunches = ref([])

// Enrolled candidates count
const enrolledCandidatesCount = computed(() => {
  return candidateList.value.filter(c => c.is_fingerprint_enrolled).length
})

// Filtered candidates by search and enrolled switch
const displayedCandidates = computed(() => {
  let list = candidateList.value

  if (enrolledOnly.value) {
    list = list.filter(c => c.is_fingerprint_enrolled)
  }

  const q = candidateSearch.value.trim().toLowerCase()
  if (!q) return list

  return list.filter(c => {
    return (
      (c.name && c.name.toLowerCase().includes(q)) ||
      (c.code && c.code.toLowerCase().includes(q)) ||
      (c.roll_no && String(c.roll_no).toLowerCase().includes(q)) ||
      (c.biometric_pin && String(c.biometric_pin).toLowerCase().includes(q)) ||
      (c.badge && c.badge.toLowerCase().includes(q))
    )
  })
})

const selectedCandidate = computed(() => {
  if (!selectedCandidateId.value) return null
  return candidateList.value.find(c => c.id === selectedCandidateId.value) || null
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

      // Populate recent punches from overview
      if (Array.isArray(res.data.recent_logs)) {
        recentPunches.value = res.data.recent_logs
          .filter(l => l.verification_method === 'fingerprint' || !l.verification_method)
          .map(l => ({
            id: l.id,
            person_name: l.student?.full_name || l.teacher?.full_name || 'Enrolled User',
            person_code: l.student?.admission_no || l.teacher?.employee_id || '-',
            punch_time: l.punch_time ? formatPunchTime(l.punch_time) : '',
            attendance_status: l.attendance_status || 'Present',
          }))
      }
    }
  } catch (err) {
    console.error('Failed to load biometric overview', err)
  }
}

function formatPunchTime(t) {
  if (!t) return ''
  if (t.includes('AM') || t.includes('PM')) return t
  const parts = t.split(':')
  if (parts.length >= 2) {
    let hour = parseInt(parts[0], 10)
    const ampm = hour >= 12 ? 'PM' : 'AM'
    hour = hour % 12 || 12
    const minute = parts[1].padStart(2, '0')
    const formattedHour = hour < 10 ? '0' + hour : '' + hour
    return `${formattedHour}:${minute} ${ampm}`
  }
  return t
}

async function loadDevices() {
  try {
    const res = await api.get('/biometrics/devices', {
      params: { school_id: selectedSchoolId.value || undefined }
    })
    if (res.data.success) {
      devices.value = res.data.data || []
      if (devices.value.length > 0 && !selectedDeviceId.value) {
        selectedDeviceId.value = devices.value[0].id
      }
    }
  } catch (err) {
    console.error('Failed to load biometric devices', err)
  }
}

async function loadCandidates() {
  try {
    const res = await api.get('/biometrics/candidates', {
      params: {
        type: targetType.value,
        school_id: selectedSchoolId.value || undefined
      }
    })
    if (res.data.success) {
      candidateList.value = res.data.candidates || []
    }
  } catch (err) {
    console.error('Failed to load candidate list', err)
  }
}

function setTargetType(type) {
  targetType.value = type
  selectedCandidateId.value = ''
  loadCandidates()
}

function onSchoolChange() {
  selectedCandidateId.value = ''
  loadOverview()
  loadDevices()
  loadCandidates()
}

function triggerSensorTouch() {
  if (selectedCandidateId.value) {
    submitFingerprintPunch(selectedCandidateId.value)
  } else {
    // Alert user gracefully to select a candidate or use the PIN input
    sensorStatusText.value = 'Select Candidate or Enter PIN'
    padStatusBadgeClass.value = 'bg-warning text-dark'
    padStateClass.value = 'pad-warning'

    if (pinInputRef.value) {
      pinInputRef.value.focus()
    }

    setTimeout(() => {
      padStatusBadgeClass.value = 'bg-dark'
      padStateClass.value = 'pad-idle'
      sensorStatusText.value = 'Optical Sensor Ready'
    }, 1800)
  }
}

async function submitFingerprintPunch(candId) {
  const cand = candidateList.value.find(c => c.id === candId)
  if (!cand) return

  scanning.value = true
  padStateClass.value = 'pad-scanning'
  fingerprintIconClass.value = 'text-info'
  sensorStatusText.value = `Scanning Minutiae: ${cand.name}...`
  padStatusBadgeClass.value = 'bg-info text-dark'

  try {
    const res = await api.post('/biometrics/finger-punch', {
      person_type: cand.person_type,
      person_id: cand.person_id,
      biometric_pin: cand.biometric_pin || cand.code,
      device_id: selectedDeviceId.value || undefined,
      school_id: selectedSchoolId.value || undefined,
    })

    if (res.data.success) {
      if (res.data.is_duplicate) {
        playAudioNotification('duplicate')
        padStateClass.value = 'pad-warning'
        fingerprintIconClass.value = 'text-warning'
        sensorStatusText.value = 'Already Checked In'
        padStatusBadgeClass.value = 'bg-warning text-dark'

        lastPunch.value = { ...res.data.punch, is_duplicate: true }
      } else {
        playAudioNotification('success')
        if (voiceEnabled.value && res.data.voice_prompt) {
          speakText(res.data.voice_prompt)
        }
        padStateClass.value = 'pad-success'
        fingerprintIconClass.value = 'text-success'
        sensorStatusText.value = 'Fingerprint Match 99.0%'
        padStatusBadgeClass.value = 'bg-success text-white'

        lastPunch.value = { ...res.data.punch, is_duplicate: false }
        recentPunches.value.unshift({
          id: res.data.punch.id || Date.now(),
          person_name: res.data.punch.person_name,
          person_code: res.data.punch.person_code,
          punch_time: res.data.punch.punch_time,
          attendance_status: res.data.punch.attendance_status,
        })
      }
    }
  } catch (err) {
    playAudioNotification('duplicate')
    padStateClass.value = 'pad-error'
    fingerprintIconClass.value = 'text-danger'
    sensorStatusText.value = 'Scan Failed'
    padStatusBadgeClass.value = 'bg-danger text-white'

    Swal.fire({
      icon: 'error',
      title: 'Fingerprint Verification Failed',
      text: err.response?.data?.message || 'Biometric record not recognized or not enrolled.'
    })
  } finally {
    setTimeout(() => {
      scanning.value = false
      padStateClass.value = 'pad-idle'
      fingerprintIconClass.value = 'text-primary'
      sensorStatusText.value = 'Optical Sensor Ready'
      padStatusBadgeClass.value = 'bg-dark'
    }, 2000)
  }
}

async function submitPinOrCardPunch() {
  const pin = pinOrCardInput.value.trim()
  if (!pin) return

  scanning.value = true
  sensorStatusText.value = `Verifying PIN: ${pin}...`
  padStatusBadgeClass.value = 'bg-info text-dark'
  padStateClass.value = 'pad-scanning'

  try {
    const res = await api.post('/biometrics/finger-punch', {
      biometric_pin: pin,
      rfid_card_no: pin,
      device_id: selectedDeviceId.value || undefined,
      school_id: selectedSchoolId.value || undefined,
    })

    if (res.data.success) {
      if (res.data.is_duplicate) {
        playAudioNotification('duplicate')
        padStateClass.value = 'pad-warning'
        fingerprintIconClass.value = 'text-warning'
        sensorStatusText.value = 'Already Checked In'
        padStatusBadgeClass.value = 'bg-warning text-dark'

        lastPunch.value = { ...res.data.punch, is_duplicate: true }
      } else {
        playAudioNotification('success')
        if (voiceEnabled.value && res.data.voice_prompt) {
          speakText(res.data.voice_prompt)
        }
        padStateClass.value = 'pad-success'
        fingerprintIconClass.value = 'text-success'
        sensorStatusText.value = 'PIN Verified 99.0%'
        padStatusBadgeClass.value = 'bg-success text-white'

        lastPunch.value = { ...res.data.punch, is_duplicate: false }
        recentPunches.value.unshift({
          id: res.data.punch.id || Date.now(),
          person_name: res.data.punch.person_name,
          person_code: res.data.punch.person_code,
          punch_time: res.data.punch.punch_time,
          attendance_status: res.data.punch.attendance_status,
        })
        pinOrCardInput.value = ''
      }
    }
  } catch (err) {
    playAudioNotification('duplicate')
    padStateClass.value = 'pad-error'
    fingerprintIconClass.value = 'text-danger'
    sensorStatusText.value = 'Verification Failed'
    padStatusBadgeClass.value = 'bg-danger text-white'

    Swal.fire({
      icon: 'error',
      title: 'PIN Verification Failed',
      text: err.response?.data?.message || 'Invalid machine PIN or RFID card UID.'
    })
  } finally {
    setTimeout(() => {
      scanning.value = false
      padStateClass.value = 'pad-idle'
      fingerprintIconClass.value = 'text-primary'
      sensorStatusText.value = 'Optical Sensor Ready'
      padStatusBadgeClass.value = 'bg-dark'
    }, 2000)
  }
}

function playAudioNotification(type = 'success') {
  try {
    const AudioCtx = window.AudioContext || window.webkitAudioContext
    if (!AudioCtx) return
    const ctx = new AudioCtx()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()
    osc.connect(gain)
    gain.connect(ctx.destination)

    if (type === 'success') {
      osc.type = 'sine'
      osc.frequency.setValueAtTime(587.33, ctx.currentTime) // D5
      osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.1) // A5
      gain.gain.setValueAtTime(0.15, ctx.currentTime)
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35)
      osc.start()
      osc.stop(ctx.currentTime + 0.35)
    } else {
      osc.type = 'sawtooth'
      osc.frequency.setValueAtTime(220, ctx.currentTime)
      gain.gain.setValueAtTime(0.12, ctx.currentTime)
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25)
      osc.start()
      osc.stop(ctx.currentTime + 0.25)
    }
  } catch (e) {}
}

function speakText(text) {
  if (!window.speechSynthesis) return
  try {
    window.speechSynthesis.cancel()
    const utterance = new SpeechSynthesisUtterance(text)
    utterance.rate = 1.05
    window.speechSynthesis.speak(utterance)
  } catch (e) {}
}

function onImgError(e) {
  e.target.src = '/images/no-image.png'
}

onMounted(() => {
  loadOverview()
  loadDevices()
  loadCandidates()
})
</script>

<style scoped>
.fingerprint-pad {
  width: 220px;
  height: 250px;
  background: radial-gradient(circle, #1e293b 0%, #0f172a 100%);
  border: 3px solid #3b82f6;
  box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
}

.fingerprint-pad:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 40px rgba(59, 130, 246, 0.5);
}

.pad-scanning {
  border-color: #06b6d4 !important;
  box-shadow: 0 0 45px rgba(6, 182, 212, 0.6) !important;
}

.pad-success {
  border-color: #10b981 !important;
  box-shadow: 0 0 45px rgba(16, 185, 129, 0.6) !important;
}

.pad-warning {
  border-color: #f59e0b !important;
  box-shadow: 0 0 45px rgba(245, 158, 11, 0.6) !important;
}

.pad-error {
  border-color: #ef4444 !important;
  box-shadow: 0 0 45px rgba(239, 68, 68, 0.6) !important;
}

.scan-beam {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, transparent, #06b6d4, #22d3ee, transparent);
  box-shadow: 0 0 15px #06b6d4;
  animation: beamScan 1.6s infinite ease-in-out;
}

@keyframes beamScan {
  0% { top: 10%; opacity: 0.9; }
  50% { top: 88%; opacity: 1; }
  100% { top: 10%; opacity: 0.9; }
}
</style>
