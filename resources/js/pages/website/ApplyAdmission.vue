<template>
  <div class="py-5 bg-light">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
          <!-- Page Title Card -->
          <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
              <i class="bi bi-mortarboard me-1"></i> Online Admission Form
            </span>
            <h2 class="fw-bold">Student Admission Application</h2>
            <p class="text-muted small">Please fill out all mandatory fields accurately. You will receive an instant application tracking number.</p>
          </div>

          <!-- Success State -->
          <div v-if="submittedApp" class="card border-0 shadow-lg rounded-4 p-5 text-center bg-white">
            <div class="bg-success text-white rounded-circle mx-auto p-4 mb-4 d-flex align-items-center justify-content-center shadow" style="width: 84px; height: 84px;">
              <i class="bi bi-check-lg fs-1"></i>
            </div>
            <h3 class="fw-bold text-success mb-2">Application Submitted Successfully!</h3>
            <p class="text-muted mb-4">
              Thank you for applying to {{ websiteData.school?.name || 'our school' }}. Your application has been logged into our admissions system.
            </p>

            <div class="card bg-light border-0 rounded-4 p-4 mx-auto mb-4" style="max-width: 450px;">
              <small class="text-muted text-uppercase fw-semibold">Your Application Tracking Reference</small>
              <h2 class="fw-bold text-primary my-2 tracking-wide font-monospace">{{ submittedApp.application_no }}</h2>
              <div class="d-flex justify-content-center gap-2 mt-2">
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="copyAppNo">
                  <i class="bi bi-clipboard me-1"></i> {{ copied ? 'Copied!' : 'Copy Reference' }}
                </button>
              </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-3">
              <router-link :to="schoolLink(`/admissions/track?app_no=${submittedApp.application_no}`)" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-search me-1"></i> Track Status Now
              </router-link>
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetForm">
                <i class="bi bi-plus-circle me-1"></i> Submit Another Application
              </button>
            </div>
          </div>

          <!-- Application Form -->
          <div v-else class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-primary text-white p-4 border-0">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> Admission Application</h5>
                <span class="badge bg-warning text-dark">Session 2026-27</span>
              </div>
            </div>

            <div class="card-body p-4 p-md-5">
              <form @submit.prevent="submitApplication">
                <!-- Section 1: Academic Preference -->
                <h6 class="fw-bold text-primary text-uppercase mb-3 pb-2 border-bottom">
                  <i class="bi bi-journal-bookmark me-2"></i> 1. Academic Information
                </h6>

                <!-- Campus Selector (shown when multiple schools exist & viewing direct link) -->
                <div class="mb-3 p-3 bg-light rounded-3 border" v-if="allSchools.length > 1 && !route.params.schoolCode">
                  <label class="form-label fw-bold text-dark mb-1">
                    <i class="bi bi-building me-1 text-primary"></i> Select Institution / Campus <span class="text-danger">*</span>
                  </label>
                  <select v-model="selectedSchoolCode" class="form-select" @change="onSchoolChange">
                    <option v-for="s in allSchools" :key="s.code" :value="s.code">
                      {{ s.name }} — {{ s.city || 'Campus' }} (Code: {{ s.code }})
                    </option>
                  </select>
                  <small class="text-muted d-block mt-1">Select the campus you wish to apply for. Available classes will update automatically.</small>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Class Applying For <span class="text-danger">*</span></label>
                    <select v-model="form.class_id" class="form-select" required>
                      <option value="" disabled>-- Select Class --</option>
                      <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Academic Session</label>
                    <select v-model="form.academic_session_id" class="form-select">
                      <option v-for="s in sessions" :key="s.id" :value="s.id">{{ s.name }} ({{ s.status }})</option>
                    </select>
                  </div>
                </div>

                <!-- Section 2: Student Bio -->
                <h6 class="fw-bold text-primary text-uppercase mb-3 pb-2 border-bottom">
                  <i class="bi bi-person me-2"></i> 2. Student Personal Information
                </h6>
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text" v-model="form.first_name" class="form-control" placeholder="Student First Name" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Last Name</label>
                    <input type="text" v-model="form.last_name" class="form-control" placeholder="Student Last Name">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" v-model="form.dob" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                    <select v-model="form.gender" class="form-select" required>
                      <option value="male">Male</option>
                      <option value="female">Female</option>
                      <option value="other">Other</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Blood Group</label>
                    <select v-model="form.blood_group" class="form-select">
                      <option value="">Unknown / Select</option>
                      <option value="A+">A+</option>
                      <option value="A-">A-</option>
                      <option value="B+">B+</option>
                      <option value="B-">B-</option>
                      <option value="O+">O+</option>
                      <option value="O-">O-</option>
                      <option value="AB+">AB+</option>
                      <option value="AB-">AB-</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Primary Contact Mobile <span class="text-danger">*</span></label>
                    <input type="tel" v-model="form.mobile" class="form-control" placeholder="10-digit mobile number" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Contact Email</label>
                    <input type="email" v-model="form.email" class="form-control" placeholder="parent@email.com">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Student Aadhaar No.</label>
                    <input type="text" v-model="form.aadhaar_no" class="form-control" placeholder="12-digit Aadhaar">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold">Residential Address <span class="text-danger">*</span></label>
                    <textarea v-model="form.address" class="form-control" rows="2" placeholder="Full residential street, city, pin code" required></textarea>
                  </div>
                </div>

                <!-- Section 3: Parents & Guardian -->
                <h6 class="fw-bold text-primary text-uppercase mb-3 pb-2 border-bottom">
                  <i class="bi bi-people me-2"></i> 3. Parents / Guardian Information
                </h6>
                <div class="row g-3 mb-4">
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Father's Full Name <span class="text-danger">*</span></label>
                    <input type="text" v-model="form.father_name" class="form-control" placeholder="Father's Name" required>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Father's Mobile</label>
                    <input type="tel" v-model="form.father_mobile" class="form-control" placeholder="Mobile">
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Father's Occupation</label>
                    <input type="text" v-model="form.father_occupation" class="form-control" placeholder="e.g. Business, Engineer">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Mother's Full Name</label>
                    <input type="text" v-model="form.mother_name" class="form-control" placeholder="Mother's Name">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Mother's Mobile</label>
                    <input type="tel" v-model="form.mother_mobile" class="form-control" placeholder="Mobile">
                  </div>
                </div>

                <!-- Section 4: Previous Education -->
                <h6 class="fw-bold text-primary text-uppercase mb-3 pb-2 border-bottom">
                  <i class="bi bi-building me-2"></i> 4. Previous School Information (Optional)
                </h6>
                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Previous School Name</label>
                    <input type="text" v-model="form.previous_school" class="form-control" placeholder="e.g. St. Jude High School">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Last Class Passed</label>
                    <input type="text" v-model="form.previous_class" class="form-control" placeholder="e.g. Class 4">
                  </div>

                  <div class="col-md-3">
                    <label class="form-label fw-semibold">Percentage / Marks</label>
                    <input type="text" v-model="form.previous_marks" class="form-control" placeholder="e.g. 88% or Grade A">
                  </div>
                </div>

                <!-- Terms & Submit -->
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="termsCheck" v-model="agreed" required>
                  <label class="form-check-label small text-muted" for="termsCheck">
                    I declare that all the information provided above is true and accurate to the best of my knowledge.
                  </label>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                  <router-link to="/admissions" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back
                  </router-link>
                  <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow" :disabled="loading || !agreed">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                    {{ loading ? 'Submitting Application...' : 'Submit Application' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, inject } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const websiteData = inject('websiteData', { content: {}, school: null })
const schoolLink = inject('schoolLink', (p) => p)

const loading = ref(false)
const copied = ref(false)
const agreed = ref(false)
const classes = ref([])
const sessions = ref([])
const allSchools = ref([])
const selectedSchoolCode = ref('')
const submittedApp = ref(null)

const form = reactive({
  academic_session_id: '',
  class_id: '',
  first_name: '',
  last_name: '',
  dob: '',
  gender: 'male',
  blood_group: '',
  religion: '',
  category: '',
  aadhaar_no: '',
  mobile: '',
  email: '',
  address: '',
  father_name: '',
  father_mobile: '',
  father_occupation: '',
  mother_name: '',
  mother_mobile: '',
  previous_school: '',
  previous_class: '',
  previous_marks: '',
})

async function fetchMetadata(customSchool = '') {
  try {
    const schoolParam = customSchool || route.params.schoolCode || route.query.school || selectedSchoolCode.value || websiteData.school?.code || ''
    const res = await axios.get('/api/public/admission-metadata', {
      params: schoolParam ? { school: schoolParam } : {}
    })
    if (res.data.success) {
      classes.value = res.data.classes || []
      sessions.value = res.data.sessions || []
      allSchools.value = res.data.all_schools || []

      if (res.data.school?.code) {
        selectedSchoolCode.value = res.data.school.code
      }

      if (sessions.value.length > 0) {
        const active = sessions.value.find(s => s.status === 'active') || sessions.value[0]
        form.academic_session_id = active.id
      }
      if (classes.value.length > 0) {
        // If current class_id is not in new classes list, default to first class
        if (!classes.value.some(c => c.id === form.class_id)) {
          form.class_id = classes.value[0].id
        }
      } else {
        form.class_id = ''
      }
    }
  } catch (err) {
    console.error('Failed to load admission metadata', err)
  }
}

function onSchoolChange() {
  fetchMetadata(selectedSchoolCode.value)
}

async function submitApplication() {
  loading.value = true
  try {
    const payload = {
      ...form,
      school: selectedSchoolCode.value || route.params.schoolCode || route.query.school || websiteData.school?.code || ''
    }
    const res = await axios.post('/api/public/admissions/apply', payload)
    if (res.data.success) {
      submittedApp.value = res.data.data
      Swal.fire({
        icon: 'success',
        title: 'Application Submitted!',
        text: `Your Application Number is ${res.data.application_no}`,
      })
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Submission Error',
      text: err.response?.data?.message || 'Please check all required fields and try again.',
    })
  } finally {
    loading.value = false
  }
}

function copyAppNo() {
  if (submittedApp.value?.application_no) {
    navigator.clipboard.writeText(submittedApp.value.application_no)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2500)
  }
}

function resetForm() {
  submittedApp.value = null
  form.first_name = ''
  form.last_name = ''
  form.dob = ''
  form.mobile = ''
  form.email = ''
  form.address = ''
  form.father_name = ''
  form.father_mobile = ''
  form.mother_name = ''
  form.mother_mobile = ''
  form.previous_school = ''
  form.previous_class = ''
  form.previous_marks = ''
  agreed.value = false
}

onMounted(() => {
  fetchMetadata()
})
</script>
