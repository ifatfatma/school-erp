<template>
  <div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
          <!-- School Header -->
          <div class="text-center mb-4">
            <router-link :to="schoolWebsiteUrl" class="text-decoration-none">
              <img
                v-if="schoolInfo?.logo"
                :src="schoolInfo.logo"
                alt="School Logo"
                class="rounded-4 bg-white p-2 shadow-sm mb-2 border"
                style="max-height: 60px; max-width: 150px; object-fit: contain;"
              />
              <div v-else class="bg-primary text-white rounded-4 p-3 d-inline-flex align-items-center justify-content-center shadow mb-2" style="width: 58px; height: 58px;">
                <i class="bi bi-mortarboard-fill fs-2"></i>
              </div>
              <h4 class="fw-bold text-dark mb-0">{{ schoolInfo?.name || 'School ERP Portal' }}</h4>
              <span v-if="schoolInfo?.code" class="badge bg-secondary-subtle text-secondary font-monospace mb-1">{{ schoolInfo.code }}</span>
              <small class="text-muted d-block">Student & Parent Portal</small>
            </router-link>
          </div>

          <!-- Login Card -->
          <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-primary text-white text-center p-4 border-0">
              <h5 class="fw-bold mb-1"><i class="bi bi-person-badge me-2"></i> Portal Sign In</h5>
              <small class="text-white-50">Access your attendance, fees, timetable & results</small>
            </div>

            <div class="card-body p-4 p-md-5">
              <form @submit.prevent="submitLogin">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Email or Username <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input
                      type="text"
                      v-model="form.email"
                      class="form-control"
                      placeholder="student@school.com or parent@school.com"
                      required
                    >
                  </div>
                </div>

                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center">
                    <label class="form-label fw-semibold mb-0">Password <span class="text-danger">*</span></label>
                  </div>
                  <div class="input-group mt-1">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input
                      :type="showPassword ? 'text' : 'password'"
                      v-model="form.password"
                      class="form-control"
                      placeholder="••••••••"
                      required
                    >
                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                      <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                    </button>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ loading ? 'Signing in...' : 'Sign In to Portal' }}
                </button>
              </form>

              <!-- One-Click Demo Access -->
              <div class="mt-4 pt-4 border-top">
                <small class="text-muted fw-bold text-uppercase d-block mb-2 text-center" style="font-size: 0.75rem;">
                  One-Click Demo Access
                </small>
                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" @click="setDemo('student')">
                    <i class="bi bi-backpack me-1"></i> Demo Student ({{ schoolCodeParam === 'S002' ? 'Robert Bruce' : 'Ahmed Hussain Khan' }})
                  </button>
                  <button v-if="schoolCodeParam !== 'S002'" type="button" class="btn btn-sm btn-outline-success rounded-pill" @click="setDemo('parent')">
                    <i class="bi bi-people me-1"></i> Demo Parent (Nurullah Khan)
                  </button>
                </div>
              </div>

              <div class="mt-4 text-center">
                <router-link :to="staffLoginUrl" class="text-muted small text-decoration-none">
                  <i class="bi bi-shield-lock me-1"></i> Staff / Admin ERP Login &rarr;
                </router-link>
              </div>
            </div>
          </div>

          <div class="text-center mt-3">
            <router-link :to="schoolWebsiteUrl" class="text-decoration-none small text-muted">
              <i class="bi bi-arrow-left me-1"></i> Back to School Website
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const loading = ref(false)
const showPassword = ref(false)
const schoolInfo = ref(null)

const schoolCodeParam = computed(() => {
  return route.params.schoolCode || route.query.school || localStorage.getItem('portal_school_code') || ''
})

const schoolWebsiteUrl = computed(() => {
  return schoolCodeParam.value ? `/s/${schoolCodeParam.value}` : '/'
})

const staffLoginUrl = computed(() => {
  return schoolCodeParam.value ? `/s/${schoolCodeParam.value}/login` : '/login'
})

const form = reactive({
  email: 'student@school.com',
  password: '12345678'
})

async function fetchSchoolInfo() {
  const code = schoolCodeParam.value
  try {
    const res = await axios.get('/api/public/school-info', {
      params: code ? { school: code } : {}
    })
    if (res.data) {
      schoolInfo.value = res.data.data ?? res.data
      if (code === 'S002' || schoolInfo.value?.code === 'S002') {
        form.email = 'robert@school.com'
      }
    }
  } catch (err) {
    console.error('Failed to load portal school info', err)
  }
}

function setDemo(role) {
  if (role === 'student') {
    if (schoolCodeParam.value === 'S002') {
      form.email = 'robert@school.com'
    } else {
      form.email = 'student@school.com'
    }
    form.password = '12345678'
  } else if (role === 'parent') {
    form.email = 'parent@school.com'
    form.password = '12345678'
  }
}

async function submitLogin() {
  loading.value = true
  try {
    const payload = {
      ...form,
      school_code: schoolCodeParam.value || undefined,
    }
    const response = await auth.login(payload)
    
    // Remember school code for subsequent logout redirects
    const user = response.data?.user || auth.user
    const code = user?.school?.code || schoolCodeParam.value
    if (code) {
      localStorage.setItem('portal_school_code', code)
      sessionStorage.setItem('portal_school_code', code)
    }

    Swal.fire({
      icon: 'success',
      title: 'Welcome to Portal!',
      timer: 1000,
      showConfirmButton: false,
    })
    router.push('/portal/dashboard')
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: err.response?.data?.message || 'Invalid credentials. Please verify your email/password.',
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSchoolInfo()
})
</script>
