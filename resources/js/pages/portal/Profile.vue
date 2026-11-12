<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Student Profile</h4>
      <small class="text-muted">Biographical information, parent details, and academic enrollment records</small>
    </div>

    <div v-if="student" class="row g-4">
      <!-- Profile Summary Card -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center h-100">
          <div class="bg-primary-subtle text-primary rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
            <i class="bi bi-person-fill fs-1"></i>
          </div>
          <h5 class="fw-bold mb-1">{{ student.full_name }}</h5>
          <span class="badge bg-primary rounded-pill px-3 py-1 mb-3">Admission #{{ student.admission_no }}</span>

          <ul class="list-group list-group-flush text-start small mt-2">
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Class & Section:</span>
              <strong>{{ student.class?.name || 'Class 1' }} - {{ student.section?.name || 'A' }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Roll Number:</span>
              <strong>#{{ student.roll_no || '1' }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Gender:</span>
              <strong class="text-capitalize">{{ student.gender }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Date of Birth:</span>
              <strong>{{ student.dob }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0">
              <span class="text-muted">Blood Group:</span>
              <strong>{{ student.blood_group || '-' }}</strong>
            </li>
          </ul>
        </div>
      </div>

      <!-- Detailed Information Card -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
            <i class="bi bi-people me-2"></i> Parents & Guardian Contact
          </h6>
          <div class="row g-3 small">
            <div class="col-md-6">
              <span class="text-muted d-block">Father's Name</span>
              <strong class="fs-6">{{ student.father_name || '-' }}</strong>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Father's Mobile</span>
              <strong class="fs-6">{{ student.father_mobile || '-' }}</strong>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Mother's Name</span>
              <strong class="fs-6">{{ student.mother_name || '-' }}</strong>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Mother's Mobile</span>
              <strong class="fs-6">{{ student.mother_mobile || '-' }}</strong>
            </div>
            <div class="col-12">
              <span class="text-muted d-block">Permanent Address</span>
              <span>{{ student.address || '-' }}</span>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
          <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom">
            <i class="bi bi-shield-check me-2"></i> School Enrollment Credentials
          </h6>
          <div class="row g-3 small">
            <div class="col-md-6">
              <span class="text-muted d-block">Institution</span>
              <strong>{{ student.school?.name || 'St. George English Day School' }}</strong>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Admission Date</span>
              <strong>{{ student.admission_date || '-' }}</strong>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Aadhaar Card Number</span>
              <span class="font-monospace">{{ student.aadhaar_no || '-' }}</span>
            </div>
            <div class="col-md-6">
              <span class="text-muted d-block">Student Registered Email</span>
              <span>{{ student.email || '-' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const student = ref(null)

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadProfile() {
  try {
    const res = await axios.get('/api/portal/profile', getAuthConfig())
    if (res.data.success) {
      student.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load portal profile', err)
  }
}

onMounted(() => {
  loadProfile()
})
</script>
