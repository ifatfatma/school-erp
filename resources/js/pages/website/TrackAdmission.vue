<template>
  <div class="py-5 bg-light min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
          <!-- Page Header -->
          <div class="text-center mb-4">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
              <i class="bi bi-search me-1"></i> Status Tracker
            </span>
            <h2 class="fw-bold">Track Your Admission Application</h2>
            <p class="text-muted small">Enter your Application Reference Number to check real-time admission progress.</p>
          </div>

          <!-- Lookup Card -->
          <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
            <form @submit.prevent="trackApplication">
              <div class="mb-3">
                <label class="form-label fw-semibold">Application Reference Number <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                  <input
                    type="text"
                    v-model="appNo"
                    class="form-control form-control-lg font-monospace text-uppercase"
                    placeholder="e.g. ADM-2026-0001"
                    required
                  >
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold">Registered Mobile Number (Optional)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                  <input
                    type="tel"
                    v-model="mobile"
                    class="form-control"
                    placeholder="Mobile number entered during registration"
                  >
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Looking up application...' : 'Check Status' }}
              </button>
            </form>
          </div>

          <!-- Result Card -->
          <div v-if="application" class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-primary text-white p-4 border-0 d-flex justify-content-between align-items-center">
              <div>
                <small class="text-white-50 text-uppercase">Application Reference</small>
                <h5 class="fw-bold mb-0 font-monospace">{{ application.application_no }}</h5>
              </div>
              <span class="badge py-2 px-3 rounded-pill" :class="statusBadgeClass(application.status)">
                {{ statusLabel(application.status) }}
              </span>
            </div>

            <div class="card-body p-4 p-md-5">
              <div class="row g-3 mb-4">
                <div v-if="application.school_name" class="col-sm-6">
                  <small class="text-muted d-block">School Campus</small>
                  <strong class="fs-6 text-primary">{{ application.school_name }}</strong>
                </div>
                <div class="col-sm-6">
                  <small class="text-muted d-block">Applicant Name</small>
                  <strong class="fs-6">{{ application.full_name }}</strong>
                </div>
                <div class="col-sm-6">
                  <small class="text-muted d-block">Class Applied</small>
                  <strong class="fs-6">{{ application.class_name }}</strong>
                </div>
                <div class="col-sm-6">
                  <small class="text-muted d-block">Academic Session</small>
                  <span>{{ application.session_name }}</span>
                </div>
                <div class="col-sm-6">
                  <small class="text-muted d-block">Submission Date</small>
                  <span>{{ application.submission_date }}</span>
                </div>
              </div>

              <!-- Status Progress Timeline -->
              <h6 class="fw-bold text-muted text-uppercase small mb-3">Admission Progress</h6>
              <div class="timeline d-flex justify-content-between position-relative mb-4">
                <div class="text-center timeline-step" :class="{ active: isStepActive(1) }">
                  <div class="step-circle mx-auto mb-1"><i class="bi bi-file-earmark-check"></i></div>
                  <small class="d-block fw-semibold">Submitted</small>
                </div>
                <div class="text-center timeline-step" :class="{ active: isStepActive(2) }">
                  <div class="step-circle mx-auto mb-1"><i class="bi bi-eye"></i></div>
                  <small class="d-block fw-semibold">Under Review</small>
                </div>
                <div class="text-center timeline-step" :class="{ active: isStepActive(3) }">
                  <div class="step-circle mx-auto mb-1"><i class="bi bi-check2-circle"></i></div>
                  <small class="d-block fw-semibold">Approved</small>
                </div>
                <div class="text-center timeline-step" :class="{ active: isStepActive(4) }">
                  <div class="step-circle mx-auto mb-1"><i class="bi bi-mortarboard"></i></div>
                  <small class="d-block fw-semibold">Enrolled</small>
                </div>
              </div>

              <!-- Admin Notes / Feedback -->
              <div v-if="application.admin_notes" class="alert alert-info border-0 rounded-3 mb-4">
                <h6 class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Admissions Office Remarks:</h6>
                <p class="small mb-0">{{ application.admin_notes }}</p>
              </div>

              <div v-if="application.status === 'enrolled'" class="alert alert-success border-0 rounded-3 text-center py-3">
                <i class="bi bi-award fs-3 text-success d-block mb-1"></i>
                <h6 class="fw-bold mb-1">Congratulations! You are officially enrolled!</h6>
                <p class="small text-muted mb-2">You can now access the Student & Parent Portal with your registered credentials.</p>
                <router-link to="/portal/login" class="btn btn-sm btn-success rounded-pill px-4">
                  Go to Student/Parent Portal <i class="bi bi-arrow-right ms-1"></i>
                </router-link>
              </div>
            </div>
          </div>

          <!-- Error Alert -->
          <div v-if="error" class="alert alert-danger border-0 rounded-3 text-center mt-3">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const appNo = ref('')
const mobile = ref('')
const loading = ref(false)
const application = ref(null)
const error = ref('')

async function trackApplication() {
  if (!appNo.value) return
  loading.value = true
  error.value = ''
  application.value = null

  try {
    const res = await axios.get('/api/public/admissions/track', {
      params: {
        application_no: appNo.value.trim(),
        mobile: mobile.value ? mobile.value.trim() : undefined,
      }
    })
    if (res.data.success) {
      application.value = res.data.data
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Unable to locate application. Please verify your reference number.'
  } finally {
    loading.value = false
  }
}

function statusBadgeClass(status) {
  switch (status) {
    case 'pending': return 'bg-secondary text-white'
    case 'under_review': return 'bg-info text-white'
    case 'approved': return 'bg-primary text-white'
    case 'enrolled': return 'bg-success text-white'
    case 'rejected': return 'bg-danger text-white'
    default: return 'bg-secondary text-white'
  }
}

function statusLabel(status) {
  switch (status) {
    case 'pending': return 'Submitted'
    case 'under_review': return 'Under Review'
    case 'approved': return 'Approved for Admission'
    case 'enrolled': return 'Enrolled Student'
    case 'rejected': return 'Application Declined'
    default: return status
  }
}

function isStepActive(step) {
  if (!application.value) return false
  const s = application.value.status
  if (step === 1) return true
  if (step === 2) return ['under_review', 'approved', 'enrolled'].includes(s)
  if (step === 3) return ['approved', 'enrolled'].includes(s)
  if (step === 4) return s === 'enrolled'
  return false
}

onMounted(() => {
  if (route.query.app_no) {
    appNo.value = route.query.app_no
    trackApplication()
  }
})
</script>

<style scoped>
.timeline-step {
  flex: 1;
  position: relative;
  z-index: 1;
}
.step-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e9ecef;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}
.timeline-step.active .step-circle {
  background: #0d6efd;
  color: #fff;
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
}
</style>
