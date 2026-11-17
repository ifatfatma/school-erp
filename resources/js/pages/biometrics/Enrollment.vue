<template>
  <div class="biometric-enrollment">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h5 class="fw-bold mb-1">
          <i class="bi bi-person-bounding-box text-primary me-2"></i>Biometric Credential Enrollment
        </h5>
        <small class="text-muted">Register and manage facial profiles, fingerprint PINs, and RFID access cards for students and faculty.</small>
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

        <button class="btn btn-outline-secondary rounded-pill btn-sm" @click="fetchEnrollments">
          <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
      </div>
    </div>

    <!-- Metric Badges -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted fw-semibold">Total Persons</small>
          <h3 class="fw-bold text-dark my-1">{{ totalPersons }}</h3>
          <small class="text-muted">Students & Staff</small>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center border-start border-4 border-success">
          <small class="text-success fw-semibold">Face Enrolled</small>
          <h3 class="fw-bold text-success my-1">{{ faceEnrolledCount }}</h3>
          <small class="text-muted">Active Face Descriptors</small>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center border-start border-4 border-primary">
          <small class="text-primary fw-semibold">Finger / PIN Enrolled</small>
          <h3 class="fw-bold text-primary my-1">{{ fingerEnrolledCount }}</h3>
          <small class="text-muted">Ready for Scanner</small>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center border-start border-4 border-warning">
          <small class="text-warning fw-semibold">Pending Enrollment</small>
          <h3 class="fw-bold text-warning my-1">{{ pendingCount }}</h3>
          <small class="text-muted">Needs Credentials</small>
        </div>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4">
      <div class="row g-2 align-items-center">
        <div class="col-md-3">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
            <input
              type="text"
              v-model="search"
              class="form-control border-start-0"
              placeholder="Search by name, roll no, employee ID..."
              @input="debounceSearch"
            />
          </div>
        </div>

        <div class="col-md-2">
          <select v-model="selectedType" class="form-select" @change="fetchEnrollments">
            <option value="all">All Roles</option>
            <option value="student">Students Only</option>
            <option value="teacher">Teachers & Staff</option>
          </select>
        </div>

        <div class="col-md-2" v-if="selectedType !== 'teacher'">
          <select v-model="selectedClass" class="form-select" @change="fetchEnrollments">
            <option value="">All Classes</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>

        <div class="col-md-3">
          <select v-model="statusFilter" class="form-select" @change="fetchEnrollments">
            <option value="">All Enrollment Statuses</option>
            <option value="enrolled">Fully Enrolled</option>
            <option value="not_enrolled">Not Enrolled</option>
            <option value="face_only">Face Enrolled Only</option>
            <option value="finger_only">Finger / PIN Enrolled Only</option>
          </select>
        </div>

        <div class="col-md-2 text-md-end">
          <button class="btn btn-outline-secondary w-100 rounded-pill" @click="resetFilters">
            <i class="bi bi-x-circle me-1"></i> Reset
          </button>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted text-uppercase">
            <tr>
              <th class="ps-4">Person</th>
              <th>Role / Affiliation</th>
              <th>Face Recognition</th>
              <th>Fingerprint / PIN</th>
              <th>RFID Card</th>
              <th>Enrolled Date</th>
              <th class="text-end pe-4" style="min-width: 230px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                <span class="text-muted">Loading enrollment directory...</span>
              </td>
            </tr>
            <tr v-else-if="enrollments.length === 0">
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                No students or staff found matching the selected filters.
              </td>
            </tr>
            <tr v-for="item in enrollments" :key="item.person_type + '_' + item.person_id">
              <td class="ps-4">
                <div class="d-flex align-items-center gap-2">
                  <img
                    :src="item.photo_url || defaultAvatar"
                    class="rounded-circle border"
                    style="width: 40px; height: 40px; object-fit: cover;"
                    alt=""
                  />
                  <div>
                    <strong class="d-block text-dark">{{ item.name }}</strong>
                    <small class="text-muted font-monospace">{{ item.code }}</small>
                  </div>
                </div>
              </td>
              <td>
                <span v-if="item.person_type === 'student'" class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  Student: {{ item.class_name }} {{ item.section_name ? ('(' + item.section_name + ')') : '' }}
                </span>
                <span v-else class="badge bg-success-subtle text-success border border-success-subtle">
                  Faculty: {{ item.designation || 'Staff' }}
                </span>
              </td>
              <td>
                <span v-if="item.is_face_enrolled" class="badge bg-success rounded-pill px-3 py-1">
                  <i class="bi bi-check-circle-fill me-1"></i> Enrolled
                </span>
                <span v-else class="badge bg-light text-muted border rounded-pill px-3 py-1">
                  <i class="bi bi-x-circle me-1"></i> Not Registered
                </span>
              </td>
              <td>
                <span v-if="item.is_fingerprint_enrolled" class="badge bg-primary rounded-pill px-3 py-1">
                  <i class="bi bi-fingerprint me-1"></i> PIN: {{ item.biometric_pin || 'Active' }}
                </span>
                <span v-else class="badge bg-light text-muted border rounded-pill px-3 py-1">
                  <i class="bi bi-dash-circle me-1"></i> Not Assigned
                </span>
              </td>
              <td>
                <span v-if="item.rfid_card_no" class="font-monospace text-primary fw-semibold small">
                  <i class="bi bi-credit-card-2-front me-1"></i>{{ item.rfid_card_no }}
                </span>
                <span v-else class="text-muted small">-</span>
              </td>
              <td class="small text-muted">
                {{ item.enrolled_at || '-' }}
              </td>
              <td class="text-end pe-4 text-nowrap">
                <div class="btn-group btn-group-sm shadow-sm">
                  <button
                    class="btn btn-primary"
                    title="Edit Biometric Profile"
                    @click="openEditModal(item)"
                  >
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </button>
                  <button
                    class="btn btn-outline-primary"
                    title="Capture Face Profile"
                    @click="openFaceModal(item)"
                  >
                    <i class="bi bi-camera me-1"></i> Face
                  </button>
                  <button
                    class="btn btn-outline-secondary"
                    title="Assign PIN / RFID Card"
                    @click="openPinModal(item)"
                  >
                    <i class="bi bi-key me-1"></i> PIN
                  </button>
                  <button
                    class="btn btn-outline-danger"
                    title="Delete Biometric Credentials"
                    @click="confirmDeleteEnrollment(item)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Face Enrollment Modal -->
    <div
      v-if="showFaceModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0,0,0,0.6); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-camera me-2"></i>Enroll Face: {{ activePerson?.name }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeFaceModal"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <p class="small text-muted mb-3">
              Look straight into the camera and click <strong>Capture & Save Face</strong> to enroll this person's facial biometric profile.
            </p>

            <!-- Video or Preview -->
            <div class="position-relative mx-auto rounded-4 overflow-hidden border shadow-sm mb-3" style="width: 280px; height: 280px; background: #0f172a;">
              <video
                v-show="isCameraActive"
                ref="videoRef"
                autoplay
                playsinline
                muted
                class="w-100 h-100"
                style="object-fit: cover; transform: scaleX(-1);"
              ></video>
              <img
                v-if="!isCameraActive && capturedPhoto"
                :src="capturedPhoto"
                class="w-100 h-100"
                style="object-fit: cover;"
                alt="Captured Face"
              />
              <img
                v-if="!isCameraActive && !capturedPhoto && existingPhoto"
                :src="existingPhoto"
                class="w-100 h-100"
                style="object-fit: cover;"
                alt="Current Photo"
              />
              <div v-if="!isCameraActive && !capturedPhoto && !existingPhoto" class="d-flex flex-column align-items-center justify-content-center h-100 text-white-50">
                <i class="bi bi-person-bounding-box fs-1 mb-2"></i>
                <small>Camera Inactive</small>
              </div>

              <!-- Reticle Overlay for alignment -->
              <div
                v-if="isCameraActive"
                class="position-absolute top-50 start-50 translate-middle rounded-circle border border-2 border-primary border-opacity-75 pointer-events-none"
                style="width: 190px; height: 230px; box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);"
              ></div>
            </div>

            <!-- Camera & Capture Controls -->
            <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
              <button
                v-if="!isCameraActive"
                class="btn btn-outline-primary rounded-pill btn-sm"
                @click="startCamera"
              >
                <i class="bi bi-camera-video me-1"></i> Start Camera
              </button>
              <button
                v-if="isCameraActive"
                class="btn btn-success rounded-pill btn-sm fw-semibold shadow-sm"
                @click="snapPhoto"
              >
                <i class="bi bi-camera me-1"></i> Snap Photo
              </button>
              <button
                v-if="!isCameraActive && capturedPhoto"
                class="btn btn-outline-warning rounded-pill btn-sm"
                @click="retakePhoto"
              >
                <i class="bi bi-arrow-repeat me-1"></i> Retake
              </button>
              <button
                v-if="isCameraActive"
                class="btn btn-outline-secondary rounded-pill btn-sm"
                @click="stopCamera"
              >
                Stop Camera
              </button>

              <label class="btn btn-outline-secondary rounded-pill btn-sm mb-0 cursor-pointer">
                <i class="bi bi-upload me-1"></i> Upload File
                <input type="file" accept="image/*" class="d-none" @change="handleFileUpload" />
              </label>
            </div>

            <small v-if="capturedPhoto" class="text-success fw-bold d-block">
              <i class="bi bi-check-circle-fill me-1"></i> Fresh Face Photo Captured & Ready to Save!
            </small>
            <small v-else-if="isCameraActive" class="text-primary fw-semibold d-block">
              <i class="bi bi-person-bounding-box me-1"></i> Position face in center, then click "Capture & Save Face"
            </small>
            <small v-else-if="existingPhoto" class="text-muted d-block">
              Showing previously saved profile photo. Start camera to update.
            </small>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" @click="closeFaceModal">Cancel</button>
            <button
              type="button"
              class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"
              :disabled="savingEnrollment || (!isCameraActive && !capturedPhoto)"
              @click="saveFaceEnrollment"
            >
              <span v-if="savingEnrollment" class="spinner-border spinner-border-sm me-1"></span>
              {{ savingEnrollment ? 'Saving Face...' : 'Capture & Save Face Profile' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- PIN & RFID Modal -->
    <div
      v-if="showPinModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0,0,0,0.6); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-dark text-white border-0 py-3">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-key me-2"></i>Biometric PIN & RFID Card
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showPinModal = false"></button>
          </div>
          <div class="modal-body p-4">
            <p class="small text-muted mb-3">
              Configure hardware PIN and RFID smart card code for <strong>{{ activePerson?.name }}</strong>.
            </p>

            <div class="mb-3">
              <label class="form-label fw-semibold">Biometric Terminal PIN <span class="text-danger">*</span></label>
              <input
                type="text"
                v-model="pinForm.biometric_pin"
                class="form-control font-monospace"
                placeholder="e.g. 4 to 8 digit terminal PIN"
                required
              />
              <small class="text-muted">Used on keypad terminals and fingerprint consoles.</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">RFID Smart Card / Badge Number</label>
              <input
                type="text"
                v-model="pinForm.rfid_card_no"
                class="form-control font-monospace"
                placeholder="e.g. 10-digit RFID card serial"
              />
              <small class="text-muted">Optional: Tap or scan card on RFID reader to populate.</small>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" @click="showPinModal = false">Cancel</button>
            <button
              type="button"
              class="btn btn-primary rounded-pill px-4"
              :disabled="savingEnrollment || !pinForm.biometric_pin"
              @click="savePinEnrollment"
            >
              <span v-if="savingEnrollment" class="spinner-border spinner-border-sm me-1"></span>
              Save Credentials
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Credentials Modal -->
    <div
      v-if="showEditModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0,0,0,0.65); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-dark text-white border-0 py-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-gear fs-4 text-warning"></i>
              <div>
                <h5 class="modal-title fw-bold mb-0">Edit Biometrics: {{ activePerson?.name }}</h5>
                <small class="text-white-50 font-monospace">
                  {{ activePerson?.code }} &bull;
                  {{ activePerson?.person_type === 'student' ? ('Student (' + (activePerson?.class_name || 'Class') + ')') : 'Faculty Staff' }}
                </small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="closeEditModal"></button>
          </div>

          <div class="modal-body p-4">
            <div class="row g-4">
              <!-- Left Column: Face Profile -->
              <div class="col-md-5 border-end">
                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                  <i class="bi bi-camera text-primary"></i> Face Recognition Profile
                </h6>

                <!-- Photo Preview or Camera View -->
                <div class="position-relative mx-auto rounded-4 overflow-hidden border shadow-sm mb-3" style="width: 210px; height: 210px; background: #0f172a;">
                  <video
                    v-show="isEditCameraActive"
                    ref="editVideoRef"
                    autoplay
                    playsinline
                    muted
                    class="w-100 h-100"
                    style="object-fit: cover; transform: scaleX(-1);"
                  ></video>
                  <img
                    v-if="!isEditCameraActive && editForm.preview_url"
                    :src="editForm.preview_url"
                    class="w-100 h-100"
                    style="object-fit: cover;"
                    alt="Face Profile"
                  />
                  <div v-if="!isEditCameraActive && !editForm.preview_url" class="d-flex flex-column align-items-center justify-content-center h-100 text-white-50">
                    <i class="bi bi-person-bounding-box fs-1 mb-2"></i>
                    <small>No Face Photo</small>
                  </div>
                </div>

                <!-- Camera / Upload Controls -->
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex gap-2 justify-content-center">
                    <button
                      v-if="!isEditCameraActive"
                      type="button"
                      class="btn btn-sm btn-outline-primary rounded-pill flex-fill"
                      @click="startEditCamera"
                    >
                      <i class="bi bi-camera-video me-1"></i> Open Camera
                    </button>
                    <button
                      v-if="isEditCameraActive"
                      type="button"
                      class="btn btn-sm btn-success rounded-pill flex-fill fw-bold"
                      @click="snapEditPhoto"
                    >
                      <i class="bi bi-camera me-1"></i> Snap Photo
                    </button>
                    <button
                      v-if="isEditCameraActive"
                      type="button"
                      class="btn btn-sm btn-outline-secondary rounded-pill"
                      @click="stopEditCamera"
                    >
                      Cancel
                    </button>
                  </div>

                  <div class="d-flex gap-2 justify-content-center">
                    <label class="btn btn-sm btn-outline-secondary rounded-pill flex-fill mb-0 cursor-pointer">
                      <i class="bi bi-upload me-1"></i> Upload Photo
                      <input type="file" accept="image/*" class="d-none" @change="handleEditFileUpload" />
                    </label>
                    <button
                      v-if="editForm.preview_url"
                      type="button"
                      class="btn btn-sm btn-outline-danger rounded-pill"
                      title="Clear Face Photo"
                      @click="clearEditFacePhoto"
                    >
                      <i class="bi bi-trash"></i> Remove
                    </button>
                  </div>
                </div>

                <div class="form-check form-switch mt-3 pt-2 border-top">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="editFaceToggle"
                    v-model="editForm.is_face_enrolled"
                  />
                  <label class="form-check-label fw-semibold small" for="editFaceToggle">
                    Enable Face Recognition
                  </label>
                </div>
              </div>

              <!-- Right Column: Terminal PIN & RFID Card -->
              <div class="col-md-7">
                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                  <i class="bi bi-key text-primary"></i> Terminal & RFID Credentials
                </h6>

                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    Biometric Terminal PIN <span class="text-danger">*</span>
                  </label>
                  <input
                    type="text"
                    v-model="editForm.biometric_pin"
                    class="form-control font-monospace"
                    placeholder="e.g. 4 to 8 digit terminal PIN"
                  />
                  <small class="text-muted">Used on fingerprint machine consoles, keypad entry, and kiosk punch.</small>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold">
                    RFID Smart Card / Badge Number
                  </label>
                  <input
                    type="text"
                    v-model="editForm.rfid_card_no"
                    class="form-control font-monospace"
                    placeholder="e.g. 10-digit RFID card serial / badge number"
                  />
                  <small class="text-muted">Optional: Tap or scan RFID smart card to assign.</small>
                </div>

                <div class="form-check form-switch mb-3 pt-2 border-top">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="editFingerToggle"
                    v-model="editForm.is_fingerprint_enrolled"
                  />
                  <label class="form-check-label fw-semibold small" for="editFingerToggle">
                    Enable Fingerprint / PIN Check-in
                  </label>
                </div>

                <div class="alert alert-light border small text-muted mb-0">
                  <i class="bi bi-info-circle text-primary me-1"></i>
                  Modifying these credentials immediately synchronizes both the <strong>Live AI Scanner</strong> and the <strong>Fingerprint Console</strong> across all school terminals.
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer d-flex justify-content-between border-0 bg-light rounded-bottom-4 py-3">
            <button
              type="button"
              class="btn btn-outline-danger btn-sm rounded-pill"
              @click="confirmDeleteEnrollment(activePerson)"
            >
              <i class="bi bi-trash me-1"></i> Delete Credentials
            </button>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" @click="closeEditModal">Cancel</button>
              <button
                type="button"
                class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm"
                :disabled="savingEnrollment"
                @click="saveEditEnrollment"
              >
                <span v-if="savingEnrollment" class="spinner-border spinner-border-sm me-1"></span>
                Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import api from '@/api/axios'
import Swal from 'sweetalert2'

const isSuperAdmin = ref(false)
const allSchools = ref([])
const selectedSchoolId = ref('')
const activeSchoolName = ref('School ERP')

const enrollments = ref([])
const classes = ref([])
const loading = ref(false)
const savingEnrollment = ref(false)

const selectedType = ref('all')
const selectedClass = ref('')
const statusFilter = ref('')
const search = ref('')

const showFaceModal = ref(false)
const showPinModal = ref(false)
const showEditModal = ref(false)
const activePerson = ref(null)

const isCameraActive = ref(false)
const capturedPhoto = ref('')
const existingPhoto = ref('')
const videoRef = ref(null)
let mediaStream = null

const isEditCameraActive = ref(false)
const editVideoRef = ref(null)
let editMediaStream = null

const pinForm = reactive({
  biometric_pin: '',
  rfid_card_no: '',
})

const editForm = reactive({
  biometric_pin: '',
  rfid_card_no: '',
  is_face_enrolled: false,
  is_fingerprint_enrolled: false,
  face_photo: '',
  clear_face_photo: false,
  preview_url: '',
})

const defaultAvatar = 'https://ui-avatars.com/api/?background=2563eb&color=fff&name=User'

const totalPersons = computed(() => enrollments.value.length)
const faceEnrolledCount = computed(() => enrollments.value.filter(e => e.is_face_enrolled).length)
const fingerEnrolledCount = computed(() => enrollments.value.filter(e => e.is_fingerprint_enrolled).length)
const pendingCount = computed(() => enrollments.value.filter(e => !e.is_face_enrolled && !e.is_fingerprint_enrolled).length)

let searchTimeout = null
function debounceSearch() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchEnrollments()
  }, 350)
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
  fetchEnrollments()
}

async function fetchEnrollments() {
  loading.value = true
  try {
    const res = await api.get('/biometrics/enrollments', {
      params: {
        type: selectedType.value,
        class_id: selectedClass.value || undefined,
        status: statusFilter.value || undefined,
        search: search.value || undefined,
        school_id: selectedSchoolId.value || undefined,
      },
    })
    if (res.data.success) {
      enrollments.value = res.data.data || []
      classes.value = res.data.classes || []
    }
  } catch (err) {
    console.error('Failed to load enrollments', err)
  } finally {
    loading.value = false
  }
}

function resetFilters() {
  selectedType.value = 'all'
  selectedClass.value = ''
  statusFilter.value = ''
  search.value = ''
  fetchEnrollments()
}

function openFaceModal(item) {
  activePerson.value = item
  existingPhoto.value = item.photo_url || ''
  capturedPhoto.value = ''
  showFaceModal.value = true
  startCamera()
}

function closeFaceModal() {
  stopCamera()
  capturedPhoto.value = ''
  showFaceModal.value = false
}

function retakePhoto() {
  capturedPhoto.value = ''
  startCamera()
}

async function startCamera() {
  try {
    mediaStream = await navigator.mediaDevices.getUserMedia({
      video: { width: { ideal: 480 }, height: { ideal: 480 }, facingMode: 'user' },
      audio: false,
    })
    isCameraActive.value = true
    if (videoRef.value) {
      videoRef.value.srcObject = mediaStream
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Camera Error', text: 'Unable to access your camera device.' })
  }
}

function stopCamera() {
  if (mediaStream) {
    mediaStream.getTracks().forEach(t => t.stop())
    mediaStream = null
  }
  isCameraActive.value = false
}

function snapPhoto() {
  if (!videoRef.value) return
  const video = videoRef.value
  const canvas = document.createElement('canvas')
  canvas.width = video.videoWidth || 480
  canvas.height = video.videoHeight || 480
  const ctx = canvas.getContext('2d')
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
  capturedPhoto.value = canvas.toDataURL('image/jpeg', 0.85)
  stopCamera()
}

function handleFileUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    capturedPhoto.value = ev.target?.result
    stopCamera()
  }
  reader.readAsDataURL(file)
}

async function saveFaceEnrollment() {
  if (!activePerson.value) return

  // If live camera is currently active, snap the live frame automatically right now
  if (isCameraActive.value && videoRef.value) {
    snapPhoto()
  }

  const photoToSave = capturedPhoto.value || existingPhoto.value
  if (!photoToSave) {
    Swal.fire({ icon: 'warning', title: 'No Photo', text: 'Please start the camera and capture a face photo first.' })
    return
  }

  savingEnrollment.value = true
  try {
    const payload = {
      person_type: activePerson.value.person_type,
      person_id: activePerson.value.person_id,
      face_photo: photoToSave,
      is_face_enrolled: true,
      school_id: selectedSchoolId.value || undefined,
    }
    const res = await api.post('/biometrics/enroll', payload)
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Face Profile Enrolled!',
        text: `Facial biometric profile saved for ${activePerson.value.name}. Ready for Live AI Scanner.`,
        timer: 1600,
        showConfirmButton: false,
      })
      closeFaceModal()
      fetchEnrollments()
    }
  } catch (err) {
    if (err.response?.data?.duplicate_face) {
      const conf = err.response.data.conflicting_person
      Swal.fire({
        icon: 'error',
        title: 'Duplicate Face Detected!',
        html: `This face is <strong>already enrolled</strong> for <strong>${conf?.name}</strong> (${conf?.code || ''}).<br><br><span class="text-danger small"><i class="bi bi-shield-lock me-1"></i> For security and anti-proxy protection, one face cannot be registered to multiple accounts.</span><br><br><small class="text-muted">An administrator must delete or reset the previous person's biometric face before it can be assigned to anyone else.</small>`,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Understood',
      })
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to save face profile' })
    }
  } finally {
    savingEnrollment.value = false
  }
}

function openPinModal(item) {
  activePerson.value = item
  pinForm.biometric_pin = item.biometric_pin || item.code || ''
  pinForm.rfid_card_no = item.rfid_card_no || ''
  showPinModal.value = true
}

async function savePinEnrollment() {
  if (!activePerson.value || !pinForm.biometric_pin) return
  savingEnrollment.value = true
  try {
    const payload = {
      person_type: activePerson.value.person_type,
      person_id: activePerson.value.person_id,
      biometric_pin: pinForm.biometric_pin,
      rfid_card_no: pinForm.rfid_card_no,
      is_fingerprint_enrolled: true,
      school_id: selectedSchoolId.value || undefined,
    }
    const res = await api.post('/biometrics/enroll', payload)
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Credentials Saved!', timer: 1200, showConfirmButton: false })
      showPinModal.value = false
      fetchEnrollments()
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to save PIN credentials' })
  } finally {
    savingEnrollment.value = false
  }
}

function openEditModal(item) {
  activePerson.value = item
  editForm.biometric_pin = item.biometric_pin || item.code || ''
  editForm.rfid_card_no = item.rfid_card_no || ''
  editForm.is_face_enrolled = !!item.is_face_enrolled
  editForm.is_fingerprint_enrolled = !!item.is_fingerprint_enrolled
  editForm.face_photo = ''
  editForm.clear_face_photo = false
  editForm.preview_url = item.photo_url || ''
  isEditCameraActive.value = false
  showEditModal.value = true
}

function closeEditModal() {
  stopEditCamera()
  showEditModal.value = false
}

async function startEditCamera() {
  try {
    editMediaStream = await navigator.mediaDevices.getUserMedia({
      video: { width: { ideal: 480 }, height: { ideal: 480 }, facingMode: 'user' },
      audio: false,
    })
    isEditCameraActive.value = true
    if (editVideoRef.value) {
      editVideoRef.value.srcObject = editMediaStream
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Camera Error', text: 'Unable to access your camera device.' })
  }
}

function stopEditCamera() {
  if (editMediaStream) {
    editMediaStream.getTracks().forEach(t => t.stop())
    editMediaStream = null
  }
  isEditCameraActive.value = false
}

function snapEditPhoto() {
  if (!editVideoRef.value) return
  const video = editVideoRef.value
  const canvas = document.createElement('canvas')
  canvas.width = video.videoWidth || 480
  canvas.height = video.videoHeight || 480
  const ctx = canvas.getContext('2d')
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
  const data = canvas.toDataURL('image/jpeg', 0.85)
  editForm.face_photo = data
  editForm.preview_url = data
  editForm.clear_face_photo = false
  editForm.is_face_enrolled = true
  stopEditCamera()
}

function handleEditFileUpload(e) {
  const file = e.target.files?.[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (ev) => {
    editForm.face_photo = ev.target?.result
    editForm.preview_url = ev.target?.result
    editForm.clear_face_photo = false
    editForm.is_face_enrolled = true
    stopEditCamera()
  }
  reader.readAsDataURL(file)
}

function clearEditFacePhoto() {
  editForm.face_photo = ''
  editForm.preview_url = ''
  editForm.clear_face_photo = true
  editForm.is_face_enrolled = false
  stopEditCamera()
}

async function saveEditEnrollment() {
  if (!activePerson.value) return

  if (isEditCameraActive.value && editVideoRef.value) {
    snapEditPhoto()
  }

  savingEnrollment.value = true
  try {
    const payload = {
      person_type: activePerson.value.person_type,
      person_id: activePerson.value.person_id,
      biometric_pin: editForm.biometric_pin,
      rfid_card_no: editForm.rfid_card_no,
      is_face_enrolled: editForm.is_face_enrolled,
      is_fingerprint_enrolled: editForm.is_fingerprint_enrolled,
      face_photo: editForm.face_photo || undefined,
      clear_face_photo: editForm.clear_face_photo,
      school_id: selectedSchoolId.value || undefined,
    }
    const res = await api.post('/biometrics/enroll', payload)
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Credentials Updated!',
        text: `Biometrics updated successfully for ${activePerson.value.name}.`,
        timer: 1500,
        showConfirmButton: false,
      })
      closeEditModal()
      fetchEnrollments()
    }
  } catch (err) {
    if (err.response?.data?.duplicate_face) {
      const conf = err.response.data.conflicting_person
      Swal.fire({
        icon: 'error',
        title: 'Duplicate Face Detected!',
        html: `This face is <strong>already enrolled</strong> for <strong>${conf?.name}</strong> (${conf?.code || ''}).<br><br><span class="text-danger small"><i class="bi bi-shield-lock me-1"></i> For security and anti-proxy protection, one face cannot be registered to multiple accounts.</span><br><br><small class="text-muted">An administrator must delete or reset the previous person's biometric face before it can be assigned to anyone else.</small>`,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Understood',
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Update Error',
        text: err.response?.data?.message || 'Failed to update credentials'
      })
    }
  } finally {
    savingEnrollment.value = false
  }
}

async function confirmDeleteEnrollment(item) {
  const target = item || activePerson.value
  if (!target) return

  const result = await Swal.fire({
    title: 'Delete Biometric Credentials?',
    html: `Are you sure you want to delete all biometric credentials for <strong>${target.name}</strong> (${target.code})?<br><br><span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i> This will delete their enrolled face photograph, terminal PIN, and RFID smart card code.</span>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, Delete Credentials',
    cancelButtonText: 'Cancel',
  })

  if (result.isConfirmed) {
    savingEnrollment.value = true
    try {
      const res = await api.delete('/biometrics/enrollments', {
        data: {
          person_type: target.person_type,
          person_id: target.person_id,
          school_id: selectedSchoolId.value || undefined,
        }
      })
      if (res.data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Credentials Deleted',
          text: `Biometric credentials for ${target.name} have been completely removed.`,
          timer: 1500,
          showConfirmButton: false,
        })
        if (showEditModal.value) {
          closeEditModal()
        }
        fetchEnrollments()
      }
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Delete Failed',
        text: err.response?.data?.message || 'Unable to delete biometric credentials.'
      })
    } finally {
      savingEnrollment.value = false
    }
  }
}

onMounted(() => {
  loadOverview()
  fetchEnrollments()
})

onUnmounted(() => {
  stopCamera()
  stopEditCamera()
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
