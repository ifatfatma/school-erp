<template>
  <div>
    <!-- Scanner Header & Controls -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-0 text-dark">
          <i class="bi bi-person-bounding-box text-primary me-2"></i>Live AI Face Recognition Scanner
        </h5>
        <small class="text-muted">Real-time camera scanner for students & staff attendance with auto-check-in</small>
      </div>

      <div class="d-flex align-items-center gap-2">
        <!-- School Switcher for Super Admin -->
        <div v-if="isSuperAdmin && allSchools.length > 0" class="d-flex align-items-center gap-1">
          <label class="small text-muted text-nowrap fw-semibold">School:</label>
          <select v-model="selectedSchoolId" class="form-select form-select-sm" style="min-width: 170px;" @change="onSchoolChange">
            <option v-for="sch in allSchools" :key="sch.id" :value="sch.id">
              {{ sch.name }} ({{ sch.code }})
            </option>
          </select>
        </div>

        <!-- Target Mode: All / Students / Teachers -->
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

        <!-- Audio Mute/Unmute Toggle -->
        <button
          class="btn btn-sm"
          :class="voiceEnabled ? 'btn-success' : 'btn-outline-secondary'"
          :title="voiceEnabled ? 'Voice Announcements On' : 'Voice Announcements Muted'"
          @click="voiceEnabled = !voiceEnabled"
        >
          <i :class="voiceEnabled ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill'"></i>
        </button>

        <!-- Fullscreen Kiosk Mode -->
        <button class="btn btn-sm btn-outline-primary" title="Toggle Fullscreen Kiosk Mode" @click="toggleFullscreen">
          <i class="bi bi-arrows-fullscreen"></i> Kiosk
        </button>
      </div>
    </div>

    <!-- Main Scanner Layout -->
    <div class="row g-4" :class="{ 'kiosk-mode p-4 bg-dark text-white rounded-4 shadow': isFullscreen }">
      <!-- Left Column: Video Feed & Recognition Canvas -->
      <div class="col-lg-7 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-dark position-relative" style="min-height: 480px;">
          <!-- Camera Feed Video & Overlay Canvas -->
          <div class="position-relative w-100 d-flex align-items-center justify-content-center bg-black" style="height: 480px;">
            <video
              ref="videoRef"
              autoplay
              playsinline
              muted
              class="w-100 h-100"
              style="object-fit: cover; transform: scaleX(-1);"
            ></video>

            <canvas
              ref="canvasRef"
              class="position-absolute top-0 start-0 w-100 h-100 pointer-events-none"
              style="object-fit: cover;"
            ></canvas>

            <!-- Scanning Target Box & Reticle Overlay -->
            <div
              v-if="cameraActive"
              class="scanner-target-reticle position-absolute d-flex flex-column align-items-center justify-content-between p-3"
              :class="scannerBoxClass"
            >
              <div class="scan-laser-line" v-if="scanning"></div>
              <div class="d-flex justify-content-between w-100 text-white-50 small fw-bold">
                <span><i class="bi bi-camera-video-fill text-danger me-1"></i> LIVE AI VISION</span>
                <span class="badge bg-dark bg-opacity-75 font-monospace">{{ candidateList.length }} Loaded</span>
              </div>
              <div class="text-center">
                <span class="badge rounded-pill px-3 py-2 shadow-sm font-monospace" :class="scannerStatusBadgeClass">
                  {{ scannerStatusText }}
                </span>
              </div>
            </div>

            <!-- Camera Idle / Inactive Overlay -->
            <div v-if="!cameraActive" class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark bg-opacity-90 text-white p-4 text-center">
              <i class="bi bi-camera-video-off fs-1 mb-3 text-secondary"></i>
              <h5 class="fw-bold mb-1">Face Recognition Camera Inactive</h5>
              <p class="text-white-50 small mb-4" style="max-width: 420px;">
                Connect a webcam, tablet camera, or USB scanner to start scanning faces automatically at the school entrance.
              </p>
              <button class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow" @click="startCamera">
                <i class="bi bi-camera-fill me-2"></i> Start Live Scanner
              </button>
            </div>
          </div>

          <!-- Bottom Camera Bar -->
          <div class="card-footer bg-dark border-top border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 text-white">
            <div class="d-flex align-items-center gap-3">
              <div class="d-flex align-items-center gap-2">
                <span class="status-indicator" :class="cameraActive ? 'bg-success' : 'bg-secondary'"></span>
                <small class="fw-semibold">{{ cameraActive ? 'Camera Connected' : 'Camera Disconnected' }}</small>
              </div>
              <small class="text-white-50">|</small>
              <small class="text-white-50">
                School: <strong class="text-white">{{ activeSchoolName }}</strong>
              </small>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
              <!-- Hands-Free Auto Check-in Toggle -->
              <div class="form-check form-switch mb-0 d-flex align-items-center gap-2 bg-black bg-opacity-50 px-3 py-1 rounded-pill border border-secondary border-opacity-50 shadow-sm">
                <input
                  class="form-check-input mt-0 cursor-pointer"
                  type="checkbox"
                  role="switch"
                  id="autoScanSwitch"
                  v-model="autoScanEnabled"
                  @change="toggleAutoScan"
                />
                <label class="form-check-label small fw-bold cursor-pointer text-white" for="autoScanSwitch">
                  <i class="bi bi-robot text-info me-1"></i>
                  Hands-Free Auto Check-in:
                  <span :class="autoScanEnabled ? 'text-success' : 'text-white-50'">{{ autoScanEnabled ? 'ON' : 'OFF' }}</span>
                </label>
              </div>

              <button
                v-if="cameraActive"
                class="btn btn-sm btn-outline-light rounded-pill px-3 fw-bold shadow-sm"
                :disabled="punchSubmitting || candidateList.length === 0"
                @click="forceRecognizeNow"
                title="Verify face snapshot immediately"
              >
                <i class="bi bi-camera-fill me-1"></i> Verify Face Now
              </button>
              <button v-if="cameraActive" class="btn btn-sm btn-outline-danger rounded-pill px-3" @click="stopCamera">
                <i class="bi bi-stop-circle me-1"></i> Stop Camera
              </button>
              <button v-else class="btn btn-sm btn-outline-success rounded-pill px-3" @click="startCamera">
                <i class="bi bi-play-circle me-1"></i> Start Camera
              </button>
            </div>
          </div>
        </div>

        <!-- Candidate Search & Face Verification Bar -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mt-3">
          <div class="row align-items-center g-2">
            <div class="col-md-6">
              <label class="form-label small text-muted mb-1 fw-semibold d-flex justify-content-between">
                <span><i class="bi bi-search me-1 text-primary"></i> Search Student / Staff to Verify:</span>
                <span v-if="selectedCandidate" class="badge bg-success-subtle text-success border border-success-subtle">
                  Ready: {{ selectedCandidate.name }} ({{ selectedCandidate.code }})
                </span>
              </label>
              <div class="position-relative">
                <input
                  type="text"
                  v-model="manualQuery"
                  class="form-control form-control-sm rounded-pill ps-4"
                  placeholder="Type student name, roll no, or admission no..."
                  @input="filterCandidates"
                  @keyup.enter="forceRecognizeNow"
                />
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted small"></i>
              </div>
            </div>

            <div class="col-md-6 d-flex gap-2 align-items-end pt-md-3">
              <select v-model="selectedCandidateId" class="form-select form-select-sm rounded-pill" :disabled="filteredCandidates.length === 0">
                <option value="">-- Choose Candidate ({{ filteredCandidates.length }}) --</option>
                <option v-for="cand in filteredCandidates" :key="cand.id" :value="cand.id">
                  {{ cand.name }} [{{ cand.code }}] - {{ cand.is_face_enrolled ? '✔ Enrolled' : '✖ Not Enrolled' }} ({{ cand.badge }})
                </option>
              </select>
              <button
                class="btn btn-sm btn-success rounded-pill px-3 flex-shrink-0 fw-bold shadow-sm"
                :disabled="punchSubmitting"
                @click="forceRecognizeNow"
              >
                <i class="bi bi-person-check-fill me-1"></i> Verify Face
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Candidate Recognized Profile & Live Feed -->
      <div class="col-lg-5 col-xl-4">
        <!-- Live Recognition Status Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 bg-white">
          <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-check2-circle fs-4"></i>
              <h6 class="fw-bold mb-0">Latest Recognition</h6>
            </div>
            <span class="badge bg-white text-primary rounded-pill px-3 py-1 small fw-bold">
              {{ lastPunch ? lastPunch.attendance_status : 'Awaiting Face' }}
            </span>
          </div>

          <div class="card-body p-4 text-center" v-if="lastPunch">
            <div class="position-relative d-inline-block mb-3">
              <img
                :src="lastPunch.person_photo || '/images/no-image.png'"
                alt="Candidate Photo"
                class="rounded-circle border border-3 shadow-sm object-fit-cover"
                :class="lastPunch.attendance_status === 'Late' ? 'border-warning' : 'border-success'"
                style="width: 100px; height: 100px;"
                @error="onImgError"
              />
              <span
                class="position-absolute bottom-0 end-0 badge rounded-circle p-2 border border-white"
                :class="lastPunch.attendance_status === 'Late' ? 'bg-warning text-dark' : 'bg-success text-white'"
              >
                <i :class="lastPunch.attendance_status === 'Late' ? 'bi bi-clock-fill' : 'bi bi-check-lg'"></i>
              </span>
            </div>

            <h5 class="fw-bold text-dark mb-1">{{ lastPunch.person_name }}</h5>
            <p class="text-muted small mb-2">
              <span class="badge bg-light text-dark border me-1 font-monospace">{{ lastPunch.person_code }}</span>
              <span>{{ lastPunch.person_badge }}</span>
            </p>

            <div class="p-3 rounded-3 my-3 text-start small" :class="lastPunch.attendance_status === 'Late' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success-emphasis'">
              <div class="d-flex justify-content-between mb-1">
                <span>Check-in Time:</span>
                <strong>{{ lastPunch.punch_time }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span>Method:</span>
                <span class="text-capitalize fw-semibold"><i class="bi bi-person-bounding-box me-1"></i>Face Recognition</span>
              </div>
              <div class="d-flex justify-content-between">
                <span>Confidence:</span>
                <strong class="text-success">{{ (lastPunch.confidence_score || 95) }}% Match</strong>
              </div>
            </div>

            <div class="alert alert-info py-2 px-3 small mb-0 rounded-pill d-flex align-items-center justify-content-center gap-1">
              <i class="bi bi-bell-fill me-1"></i>
              Attendance recorded in ERP & notifications triggered
            </div>
          </div>

          <div class="card-body p-5 text-center text-muted" v-else>
            <div class="avatar-placeholder rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
              <i class="bi bi-person fs-1 text-secondary opacity-50"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Stand in Front of Camera</h6>
            <p class="small text-muted mb-0">
              The AI vision camera will automatically detect the student or staff face, compute facial landmarks, and register attendance immediately.
            </p>
          </div>
        </div>

        <!-- Today's Live Attendance Feed Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
              <i class="bi bi-clock-history text-primary me-2"></i>Today's Live Punches
            </h6>
            <span class="badge bg-primary-subtle text-primary rounded-pill">
              {{ todayPunches.length }} Scanned
            </span>
          </div>

          <div class="card-body p-0 overflow-auto" style="max-height: 280px;">
            <div v-if="todayPunches.length === 0" class="text-center py-4 text-muted small">
              No biometric punches recorded yet today.
            </div>
            <ul v-else class="list-group list-group-flush small">
              <li
                v-for="log in todayPunches"
                :key="log.id"
                class="list-group-item px-3 py-2 d-flex align-items-center justify-content-between"
              >
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                  <div
                    class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                    :class="log.attendance_status === 'Late' ? 'bg-warning text-dark' : 'bg-primary'"
                    style="width: 32px; height: 32px; font-size: 0.8rem;"
                  >
                    {{ log.person_name ? log.person_name.charAt(0) : 'U' }}
                  </div>
                  <div class="overflow-hidden">
                    <strong class="d-block text-truncate text-dark">{{ log.person_name }}</strong>
                    <span class="text-muted" style="font-size: 0.72rem;">{{ log.person_code }} &bull; {{ log.punch_time }}</span>
                  </div>
                </div>
                <span
                  class="badge rounded-pill ms-2"
                  :class="log.attendance_status === 'Late' ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success'"
                >
                  {{ log.attendance_status }}
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '@/api/axios'
import Swal from 'sweetalert2'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const videoRef = ref(null)
const canvasRef = ref(null)
const cameraActive = ref(false)
const scanning = ref(false)
const isFullscreen = ref(false)
const voiceEnabled = ref(true)
const targetType = ref('all')

const candidateList = ref([])
const filteredCandidates = ref([])
const manualQuery = ref('')
const selectedCandidateId = ref('')
const punchSubmitting = ref(false)
const autoScanEnabled = ref(true)

const allSchools = ref([])
const selectedSchoolId = ref('')
const activeSchoolName = ref('School ERP')

const lastPunch = ref(null)
const todayPunches = ref([])
const cooldownMap = ref(new Map())

let animationFrameId = null
let autoScanTimer = null
let mediaStream = null
let laserY = 0
let laserDirection = 1

const isSuperAdmin = computed(() => {
  const u = auth.user
  return u?.user_type === 'super_admin' || u?.roles?.some(r => r.name === 'Super Admin')
})

const selectedCandidate = computed(() => {
  if (!selectedCandidateId.value) return null
  return candidateList.value.find(c => c.id === selectedCandidateId.value) || null
})

const scannerStatusText = ref('AI Face Detection Active')
const scannerStatusBadgeClass = ref('bg-primary')
const scannerBoxClass = ref('border-primary')

function getAuthConfig(extraParams = {}) {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` },
    params: {
      school_id: selectedSchoolId.value || undefined,
      ...extraParams
    }
  }
}

async function loadCandidates() {
  try {
    const res = await api.get('/biometrics/candidates', {
      params: {
        type: targetType.value,
        school_id: selectedSchoolId.value || undefined,
      }
    })
    if (res.data.success) {
      candidateList.value = res.data.candidates || []
      filteredCandidates.value = candidateList.value
      selectedCandidateId.value = ''
    }
  } catch (err) {
    console.error('Failed to load scanner candidates', err)
  }
}

async function loadOverview() {
  try {
    const res = await api.get('/biometrics/overview', {
      params: {
        school_id: selectedSchoolId.value || undefined,
      }
    })
    if (res.data.success) {
      activeSchoolName.value = res.data.school?.name || 'School ERP'
      if (res.data.is_super_admin && Array.isArray(res.data.all_schools)) {
        allSchools.value = res.data.all_schools
        if (!selectedSchoolId.value && res.data.school?.id) {
          selectedSchoolId.value = res.data.school.id
        }
      }
      todayPunches.value = (res.data.recent_logs || []).map(l => ({
        id: l.id,
        person_name: l.student?.full_name || l.teacher?.full_name || 'Enrolled User',
        person_code: l.student?.admission_no || l.teacher?.employee_id || '-',
        punch_time: l.punch_time ? formatPunchTime(l.punch_time) : '',
        attendance_status: l.attendance_status || 'Present',
      }))
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

function filterCandidates() {
  const q = manualQuery.value.trim().toLowerCase()
  if (!q) {
    filteredCandidates.value = candidateList.value
    return
  }
  filteredCandidates.value = candidateList.value.filter(c =>
    (c.name && c.name.toLowerCase().includes(q)) ||
    (c.code && c.code.toLowerCase().includes(q)) ||
    (c.badge && c.badge.toLowerCase().includes(q))
  )
  if (filteredCandidates.value.length > 0) {
    selectedCandidateId.value = filteredCandidates.value[0].id
  }
}

function setTargetType(type) {
  targetType.value = type
  loadCandidates()
}

function onSchoolChange() {
  loadOverview()
  loadCandidates()
}

// -------------------------------------------------------------
// CAMERA & AI CANVAS RECOGNITION LOOP
// -------------------------------------------------------------
function startAutoScanLoop() {
  if (autoScanTimer) clearInterval(autoScanTimer)
  autoScanTimer = setInterval(async () => {
    if (!cameraActive.value || !autoScanEnabled.value || punchSubmitting.value) {
      return
    }
    // Only auto-scan if video frame is ready and active
    if (!videoRef.value || videoRef.value.videoWidth === 0) {
      return
    }
    if (candidateList.value.length === 0) {
      return
    }
    await executeBiometricPunch(null, true)
  }, 1800)
}

function stopAutoScanLoop() {
  if (autoScanTimer) {
    clearInterval(autoScanTimer)
    autoScanTimer = null
  }
}

function toggleAutoScan() {
  if (autoScanEnabled.value) {
    scannerStatusText.value = 'Hands-Free Auto-Scan Active'
    scannerStatusBadgeClass.value = 'bg-primary'
    scannerBoxClass.value = 'border-primary'
    if (cameraActive.value) {
      startAutoScanLoop()
    }
  } else {
    stopAutoScanLoop()
    scannerStatusText.value = 'Auto-Scan Paused • Click Verify Face'
    scannerStatusBadgeClass.value = 'bg-secondary'
    scannerBoxClass.value = 'border-secondary'
  }
}

async function startCamera() {
  if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({
        video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' },
        audio: false
      })

      if (videoRef.value) {
        videoRef.value.srcObject = mediaStream
        videoRef.value.onloadedmetadata = () => {
          videoRef.value.play()
          cameraActive.value = true
          scanning.value = true
          startDetectionLoop()
          if (autoScanEnabled.value) {
            startAutoScanLoop()
          }
        }
      }
    } catch (err) {
      console.warn('Camera access denied or unavailable, switching to preview simulation mode', err)
      cameraActive.value = true
      scanning.value = true
      startDetectionLoop()
      if (autoScanEnabled.value) {
        startAutoScanLoop()
      }
    }
  } else {
    cameraActive.value = true
    scanning.value = true
    startDetectionLoop()
    if (autoScanEnabled.value) {
      startAutoScanLoop()
    }
  }
}

function stopCamera() {
  stopAutoScanLoop()
  cameraActive.value = false
  scanning.value = false
  if (mediaStream) {
    mediaStream.getTracks().forEach(track => track.stop())
    mediaStream = null
  }
  if (videoRef.value) {
    videoRef.value.srcObject = null
  }
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId)
    animationFrameId = null
  }
}

function startDetectionLoop() {
  const canvas = canvasRef.value
  const video = videoRef.value

  function renderFrame() {
    if (!cameraActive.value) return

    if (canvas && video && video.videoWidth > 0) {
      canvas.width = video.clientWidth
      canvas.height = video.clientHeight
      const ctx = canvas.getContext('2d')
      ctx.clearRect(0, 0, canvas.width, canvas.height)

      // Draw subtle futuristic facial tracking guides
      const boxWidth = Math.min(canvas.width * 0.45, 260)
      const boxHeight = boxWidth * 1.25
      const boxX = (canvas.width - boxWidth) / 2
      const boxY = (canvas.height - boxHeight) / 2

      ctx.strokeStyle = punchSubmitting.value ? '#10b981' : (autoScanEnabled.value ? '#3b82f6' : '#64748b')
      ctx.lineWidth = 3
      ctx.lineCap = 'round'

      // Corner Reticles
      const cornerLength = 24
      // Top-Left
      ctx.beginPath(); ctx.moveTo(boxX, boxY + cornerLength); ctx.lineTo(boxX, boxY); ctx.lineTo(boxX + cornerLength, boxY); ctx.stroke();
      // Top-Right
      ctx.beginPath(); ctx.moveTo(boxX + boxWidth - cornerLength, boxY); ctx.lineTo(boxX + boxWidth, boxY); ctx.lineTo(boxX + boxWidth, boxY + cornerLength); ctx.stroke();
      // Bottom-Left
      ctx.beginPath(); ctx.moveTo(boxX, boxY + boxHeight - cornerLength); ctx.lineTo(boxX, boxY + boxHeight); ctx.lineTo(boxX + cornerLength, boxY + boxHeight); ctx.stroke();
      // Bottom-Right
      ctx.beginPath(); ctx.moveTo(boxX + boxWidth - cornerLength, boxY + boxHeight); ctx.lineTo(boxX + boxWidth, boxY + boxHeight); ctx.lineTo(boxX + boxWidth, boxY + boxHeight - cornerLength); ctx.stroke();

      // Futuristic sweep scan laser line
      if (scanning.value && autoScanEnabled.value) {
        laserY += 3 * laserDirection
        if (laserY > boxHeight - 8) { laserY = boxHeight - 8; laserDirection = -1 }
        if (laserY < 8) { laserY = 8; laserDirection = 1 }

        const gradient = ctx.createLinearGradient(boxX, boxY + laserY, boxX + boxWidth, boxY + laserY)
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0)')
        gradient.addColorStop(0.5, punchSubmitting.value ? 'rgba(16, 185, 129, 0.9)' : 'rgba(59, 130, 246, 0.9)')
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0)')

        ctx.strokeStyle = gradient
        ctx.lineWidth = 2
        ctx.beginPath()
        ctx.moveTo(boxX + 12, boxY + laserY)
        ctx.lineTo(boxX + boxWidth - 12, boxY + laserY)
        ctx.stroke()
      }
    }

    animationFrameId = requestAnimationFrame(renderFrame)
  }

  animationFrameId = requestAnimationFrame(renderFrame)
}

// -------------------------------------------------------------
// AI FACE MATCHING & RECORDING (ONE FACE = ONE PERSON)
// -------------------------------------------------------------
async function forceRecognizeNow() {
  if (punchSubmitting.value) return

  let cand = null
  if (selectedCandidateId.value) {
    cand = candidateList.value.find(c => c.id === selectedCandidateId.value)
  }

  // If no candidate is selected in dropdown, check if user typed in manualQuery
  if (!cand && manualQuery.value.trim()) {
    const q = manualQuery.value.trim().toLowerCase()
    cand = candidateList.value.find(c =>
      (c.code && c.code.toLowerCase() === q) ||
      (c.roll_no && String(c.roll_no).toLowerCase() === q) ||
      (c.name && c.name.toLowerCase().includes(q))
    )
  }

  // If a specific candidate was chosen, check if they are enrolled
  if (cand && !cand.is_face_enrolled) {
    Swal.fire({
      icon: 'warning',
      title: 'Face Not Enrolled',
      html: `<strong>${cand.name}</strong> does not have an enrolled biometric face profile.<br><span class="text-muted small">Please register their face in Biometric Enrollment first or use PIN / RFID.</span>`,
      showCancelButton: true,
      confirmButtonText: '<i class="bi bi-camera me-1"></i> Enroll Face Now',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#2563eb',
    }).then((res) => {
      if (res.isConfirmed) {
        window.location.href = '/biometrics?tab=enrollment'
      }
    })
    return
  }

  // Check cooldown if candidate chosen
  if (cand) {
    const lastPunchTime = cooldownMap.value.get(cand.id) || 0
    if (Date.now() - lastPunchTime < 45000) {
      Swal.fire({
        icon: 'warning',
        title: 'Already Checked In',
        text: `${cand.name} was already verified recently. Please wait before punching again.`,
        timer: 2000,
        showConfirmButton: false
      })
      return
    }
  }

  await executeBiometricPunch(cand, false)
}

async function triggerManualPunch() {
  await forceRecognizeNow()
}

async function executeBiometricPunch(cand = null, isAuto = false) {
  punchSubmitting.value = true

  if (!isAuto) {
    scannerStatusText.value = cand ? `Comparing Face: ${cand.name}...` : 'AI Face Recognition Active...'
    scannerStatusBadgeClass.value = 'bg-warning text-dark'
    scannerBoxClass.value = 'border-warning'
  } else {
    scannerStatusText.value = 'AI Scanning Face...'
    scannerStatusBadgeClass.value = 'bg-primary'
    scannerBoxClass.value = 'border-primary'
  }

  // Capture live snapshot frame from video feed (Cropping center reticle region where face is positioned)
  let snapshotBase64 = null
  if (videoRef.value && videoRef.value.videoWidth > 0) {
    try {
      const video = videoRef.value
      const vw = video.videoWidth
      const vh = video.videoHeight

      // Crop the center portrait/face region matching the reticle guide
      const boxSize = Math.min(vw, vh) * 0.70
      const cropX = Math.max(0, (vw - boxSize) / 2)
      const cropY = Math.max(0, (vh - boxSize) / 2)

      const snapCanvas = document.createElement('canvas')
      snapCanvas.width = 320
      snapCanvas.height = 320
      const ctx = snapCanvas.getContext('2d')
      ctx.drawImage(video, cropX, cropY, boxSize, boxSize, 0, 0, 320, 320)
      snapshotBase64 = snapCanvas.toDataURL('image/jpeg', 0.85)
    } catch (e) {
      console.error('Snapshot capture error:', e)
    }
  }

  if (!snapshotBase64) {
    punchSubmitting.value = false
    if (!isAuto) {
      Swal.fire({
        icon: 'error',
        title: 'Camera Inactive',
        text: 'Please start the live camera before verifying face attendance.',
      })
      scannerStatusText.value = 'Camera Inactive'
      scannerStatusBadgeClass.value = 'bg-danger'
    }
    return
  }

  try {
    const res = await api.post('/biometrics/face-punch', {
      person_type: cand?.person_type || undefined,
      person_id: cand?.person_id || undefined,
      snapshot: snapshotBase64,
      school_id: selectedSchoolId.value || undefined,
    })

    if (res.data.success) {
      const punchData = res.data.punch
      const personKey = (punchData.person_code || '') + '_' + (punchData.person_name || '')

      // Check client-side anti-re-trigger cooldown (e.g. 45 seconds for same student)
      const lastTime = cooldownMap.value.get(personKey) || 0
      const isRecent = (Date.now() - lastTime) < 45000

      if (isRecent && isAuto) {
        // Person is still in view after recent punch - show subtle verified badge without re-triggering sound
        scannerStatusText.value = `✓ ${punchData.person_name} Verified`
        scannerStatusBadgeClass.value = 'bg-success'
        scannerBoxClass.value = 'border-success'
        punchSubmitting.value = false
        return
      }

      cooldownMap.value.set(personKey, Date.now())
      if (cand) cooldownMap.value.set(cand.id, Date.now())

      if (res.data.is_duplicate) {
        playAudioNotification('duplicate')
        scannerStatusText.value = `${punchData.person_name} Already Checked In`
        scannerStatusBadgeClass.value = 'bg-warning text-dark'
        lastPunch.value = { ...punchData, is_duplicate: true }
      } else {
        playAudioNotification('success')
        if (voiceEnabled.value) {
          speakText(res.data.voice_prompt || `Welcome, ${punchData.person_name}. Attendance marked.`)
        }

        lastPunch.value = punchData
        todayPunches.value.unshift({
          id: punchData.id || Date.now(),
          person_name: punchData.person_name,
          person_code: punchData.person_code,
          punch_time: punchData.punch_time,
          attendance_status: punchData.attendance_status,
        })

        // Reset manual candidate query so next student is scanned fresh
        selectedCandidateId.value = ''
        manualQuery.value = ''
        filteredCandidates.value = candidateList.value

        scannerStatusText.value = `Verified: ${punchData.person_name} (${punchData.confidence_score}% Match)`
        scannerStatusBadgeClass.value = 'bg-success'
        scannerBoxClass.value = 'border-success'
      }

      // Display verification for 3 seconds, then return smoothly to auto-scanning
      setTimeout(() => {
        punchSubmitting.value = false
        scannerStatusText.value = autoScanEnabled.value ? 'Hands-Free AI Auto-Scan Active' : 'Face Detection Active'
        scannerStatusBadgeClass.value = 'bg-primary'
        scannerBoxClass.value = 'border-primary'
      }, 3000)
      return
    }
  } catch (err) {
    const errData = err.response?.data || {}

    if (isAuto) {
      // In hands-free mode: do NOT show blocking popup dialogs! Keep scanning seamlessly.
      punchSubmitting.value = false
      if (errData.face_not_recognized || errData.face_mismatch) {
        scannerStatusText.value = 'Face Unrecognized • Position in Center or Select Candidate'
        scannerStatusBadgeClass.value = 'bg-secondary'
        scannerBoxClass.value = 'border-secondary'
      }
      return
    }

    // Manual button clicks: Show informative Swal feedback
    playAudioNotification('error')

    if (errData.face_mismatch) {
      scannerStatusText.value = `Face Mismatch (${errData.confidence_score || 0}% Match) • Rejected`
      scannerStatusBadgeClass.value = 'bg-danger'
      scannerBoxClass.value = 'border-danger'

      Swal.fire({
        icon: 'error',
        title: 'Face Mismatch! Attendance Rejected',
        html: `<div class="p-3 bg-danger bg-opacity-10 border border-danger rounded-3 text-danger fw-semibold mb-2">
                 ${errData.message}
               </div>
               <p class="small text-muted mb-0">1 face can only verify that specific enrolled person. False check-in was blocked.</p>`,
        confirmButtonText: 'Understood',
        confirmButtonColor: '#ef4444',
      })
    } else if (errData.not_enrolled) {
      scannerStatusText.value = 'Face Not Enrolled'
      scannerStatusBadgeClass.value = 'bg-warning text-dark'
      scannerBoxClass.value = 'border-warning'

      Swal.fire({
        icon: 'warning',
        title: 'Biometric Face Not Enrolled',
        html: `<p class="mb-2">${errData.message}</p><p class="small text-muted mb-0">Please enroll their facial photograph in Biometric Enrollment first.</p>`,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-camera me-1"></i> Enroll Face Now',
        cancelButtonText: 'Close',
        confirmButtonColor: '#2563eb',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = '/biometrics?tab=enrollment'
        }
      })
    } else if (errData.face_not_recognized) {
      scannerStatusText.value = 'Face Not Recognized'
      scannerStatusBadgeClass.value = 'bg-danger'
      scannerBoxClass.value = 'border-danger'

      Swal.fire({
        icon: 'info',
        title: 'Face Not Recognized',
        text: errData.message || 'The person standing in front of the camera is not registered in this school.',
        confirmButtonText: 'Try Again'
      })
    } else if (errData.proxy_detected) {
      scannerStatusText.value = 'Proxy Punch Blocked'
      scannerStatusBadgeClass.value = 'bg-danger'
      scannerBoxClass.value = 'border-danger'

      Swal.fire({
        icon: 'error',
        title: 'Proxy Attendance Blocked!',
        html: `<div class="p-3 bg-danger bg-opacity-10 border border-danger rounded-3 text-danger fw-semibold mb-2">
                 ${errData.message}
               </div>
               <p class="small text-muted mb-0">For institutional security, one individual cannot mark attendance for multiple students or staff members.</p>`,
        confirmButtonText: 'Understood',
        confirmButtonColor: '#ef4444',
      })
    } else {
      scannerStatusText.value = 'Punch Failed'
      scannerStatusBadgeClass.value = 'bg-danger'
      scannerBoxClass.value = 'border-danger'

      Swal.fire({
        icon: 'error',
        title: 'Attendance Rejected',
        text: errData.message || 'Could not verify biometric punch. Please try again.',
      })
    }

    setTimeout(() => {
      punchSubmitting.value = false
      scannerStatusText.value = autoScanEnabled.value ? 'Hands-Free AI Auto-Scan Active' : 'Face Detection Active'
      scannerStatusBadgeClass.value = 'bg-primary'
      scannerBoxClass.value = 'border-primary'
    }, 2500)
  }
}

// -------------------------------------------------------------
// AUDIO FEEDBACK & SYNTHESIZED SPEECH
// -------------------------------------------------------------
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
      // Pleasant melodic chime
      osc.type = 'sine'
      osc.frequency.setValueAtTime(523.25, ctx.currentTime) // C5
      osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1) // E5
      gain.gain.setValueAtTime(0.15, ctx.currentTime)
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35)
      osc.start()
      osc.stop(ctx.currentTime + 0.35)
    } else {
      // Warning double tone
      osc.type = 'triangle'
      osc.frequency.setValueAtTime(320, ctx.currentTime)
      gain.gain.setValueAtTime(0.15, ctx.currentTime)
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
    utterance.pitch = 1.0
    window.speechSynthesis.speak(utterance)
  } catch (e) {}
}

function toggleFullscreen() {
  isFullscreen.value = !isFullscreen.value
}

function onImgError(e) {
  e.target.src = '/images/no-image.png'
}

onMounted(async () => {
  await loadOverview()
  await loadCandidates()
  startCamera()
})

onUnmounted(() => {
  stopCamera()
})
</script>

<style scoped>
.scanner-target-reticle {
  width: 270px;
  height: 330px;
  border: 2px dashed rgba(255, 255, 255, 0.6);
  border-radius: 24px;
  box-shadow: 0 0 25px rgba(59, 130, 246, 0.35);
  pointer-events: none;
  overflow: hidden;
  transition: all 0.3s ease;
}

.scan-laser-line {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, transparent, #3b82f6, #60a5fa, transparent);
  box-shadow: 0 0 10px #3b82f6;
  animation: scanLaser 2s infinite ease-in-out;
}

@keyframes scanLaser {
  0% { top: 5%; opacity: 0.8; }
  50% { top: 92%; opacity: 1; }
  100% { top: 5%; opacity: 0.8; }
}

.status-indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.kiosk-mode {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 9999;
  border-radius: 0 !important;
  overflow: auto;
}
</style>
