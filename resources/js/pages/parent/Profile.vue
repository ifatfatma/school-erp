<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Profile & Family Records</h4>
      <small class="text-muted">Enrolled student details, guardian contact records, and institutional affiliation</small>
    </div>

    <div class="row g-4">
      <!-- Child Profile Card -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
          <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
            <div
              class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold shadow-sm"
              style="width: 64px; height: 64px; font-size: 1.5rem;"
            >
              {{ child?.full_name?.charAt(0) || 'S' }}
            </div>
            <div>
              <h5 class="fw-bold mb-0 text-dark">{{ child?.full_name }}</h5>
              <div class="text-muted small">Admission No: <strong class="text-dark">{{ child?.admission_no }}</strong></div>
              <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0 mt-1">Enrolled & Active</span>
            </div>
          </div>

          <h6 class="fw-bold text-primary mb-3">
            <i class="bi bi-mortarboard me-2"></i>Academic & Bio Details
          </h6>
          <div class="row g-2 small">
            <div class="col-sm-6 text-muted">Class & Section:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ child?.class?.name }} - {{ child?.section?.name || 'A' }}</div>

            <div class="col-sm-6 text-muted">Roll Number:</div>
            <div class="col-sm-6 fw-semibold text-dark">#{{ child?.roll_no || '1' }}</div>

            <div class="col-sm-6 text-muted">Date of Birth:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ child?.dob || 'N/A' }}</div>

            <div class="col-sm-6 text-muted">Gender:</div>
            <div class="col-sm-6 fw-semibold text-dark text-capitalize">{{ child?.gender || 'N/A' }}</div>

            <div class="col-sm-6 text-muted">Blood Group:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ child?.blood_group || 'N/A' }}</div>

            <div class="col-sm-6 text-muted">Residential Address:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ child?.address || 'On File with School' }}</div>
          </div>
        </div>
      </div>

      <!-- Guardian / Parent Card -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
          <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
            <div
              class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold shadow-sm"
              style="width: 64px; height: 64px; font-size: 1.5rem;"
            >
              <i class="bi bi-person-heart"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0 text-dark">{{ guardian?.first_name ? `${guardian.first_name} ${guardian.last_name || ''}` : (auth.user?.name || 'Guardian') }}</h5>
              <div class="text-muted small">Relation: <strong class="text-dark">{{ guardian?.relation || 'Parent / Guardian' }}</strong></div>
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0 mt-1">Primary Guardian</span>
            </div>
          </div>

          <h6 class="fw-bold text-success mb-3">
            <i class="bi bi-telephone me-2"></i>Guardian Contact Information
          </h6>
          <div class="row g-2 small">
            <div class="col-sm-6 text-muted">Mobile Number:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ guardian?.mobile || auth.user?.mobile || 'N/A' }}</div>

            <div class="col-sm-6 text-muted">Registered Email:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ guardian?.email || auth.user?.email || 'N/A' }}</div>

            <div class="col-sm-6 text-muted">Occupation:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ guardian?.occupation || 'Business / Professional' }}</div>

            <div class="col-sm-6 text-muted">Address:</div>
            <div class="col-sm-6 fw-semibold text-dark">{{ guardian?.address || child?.address || 'Registered Residence' }}</div>

            <div class="col-sm-6 text-muted">School Affiliation:</div>
            <div class="col-sm-6 fw-semibold text-primary">{{ child?.school?.name || auth.user?.school?.name || 'School ERP' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()
const child = ref(null)
const guardian = ref(null)

function getAuthConfig(extraParams = {}) {
  const token = localStorage.getItem('token')
  const studentId = localStorage.getItem('portal_active_student_id')
  return {
    headers: {
      Authorization: `Bearer ${token}`,
      ...(studentId ? { 'X-Student-Id': String(studentId) } : {})
    },
    params: {
      ...(studentId ? { student_id: Number(studentId) } : {}),
      ...extraParams
    }
  }
}

async function loadProfile(overrideChildId = null) {
  const studentId = overrideChildId || localStorage.getItem('portal_active_student_id')
  try {
    const res = await axios.get('/api/parent/profile', getAuthConfig(studentId ? { student_id: Number(studentId) } : {}))
    if (res.data.success) {
      child.value = res.data.data.child
      guardian.value = res.data.data.guardian
    }
  } catch (err) {
    console.error('Failed to load parent profile', err)
  }
}

function handleChildSwitch(e) {
  const childId = e.detail?.id
  loadProfile(childId)
}

onMounted(() => {
  loadProfile()
  window.addEventListener('parent-child-switched', handleChildSwitch)
})

onUnmounted(() => {
  window.removeEventListener('parent-child-switched', handleChildSwitch)
})
</script>