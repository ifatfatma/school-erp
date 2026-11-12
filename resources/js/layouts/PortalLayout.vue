<template>
  <div class="d-flex min-vh-100 bg-light">
    <!-- Portal Sidebar -->
    <aside class="bg-white border-end shadow-sm d-flex flex-column" style="width: 260px; min-height: 100vh;">
      <!-- Brand & Student Header -->
      <div class="p-3 border-bottom bg-primary text-white">
        <div class="d-flex align-items-center gap-2 mb-2">
          <img
            v-if="studentInfo?.school?.logo || auth.user?.school?.logo"
            :src="studentInfo?.school?.logo || auth.user?.school?.logo"
            alt="School Logo"
            class="bg-white rounded-3 p-1"
            style="width: 32px; height: 32px; object-fit: contain;"
          />
          <div v-else class="bg-white text-primary rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <span class="fw-bold fs-6 lh-1 text-truncate" style="max-width: 175px;">{{ studentInfo?.school?.name || auth.user?.school?.name || 'School Portal' }}</span>
        </div>
        <div class="bg-primary-subtle text-dark rounded-3 p-2 small mt-2">
          <div class="fw-bold text-primary">{{ studentInfo?.full_name || auth.user?.name || 'Student Portal' }}</div>
          <div class="text-muted" style="font-size: 0.75rem;">
            Adm No: <span class="fw-semibold">{{ studentInfo?.admission_no || '—' }}</span> &bull; 
            Class: <span class="fw-semibold">{{ studentInfo?.class?.name || '—' }}</span>
          </div>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="p-2 flex-grow-1 overflow-auto">
        <ul class="nav nav-pills flex-column gap-1">
          <li class="nav-item">
            <router-link to="/portal/dashboard" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/attendance" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-calendar-check-fill"></i>
              <span>My Attendance</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/timetable" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-calendar3-range-fill"></i>
              <span>Timetable</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/online-classes" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-camera-video-fill"></i>
              <span>Online Classes</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/fees" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-cash-stack"></i>
              <span>Fees & Receipts</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/results" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-award-fill"></i>
              <span>Exam Results</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/library" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-book-fill"></i>
              <span>Library Books</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/profile" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark" active-class="active bg-primary text-white">
              <i class="bi bi-person-circle"></i>
              <span>My Profile</span>
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Bottom User & Logout -->
      <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
        <div class="overflow-hidden me-2">
          <small class="d-block text-muted" style="font-size: 0.7rem;">Signed in as</small>
          <strong class="small text-truncate d-block text-dark fw-semibold" style="max-width: 150px;" :title="signedInLabel">
            {{ signedInLabel }}
          </strong>
        </div>
        <button class="btn btn-sm btn-outline-danger flex-shrink-0" title="Logout" @click="handleLogout">
          <i class="bi bi-box-arrow-right"></i>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow-1 d-flex flex-column overflow-hidden">
      <!-- Portal Top Header -->
      <header class="bg-white border-bottom shadow-sm px-4 py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <h5 class="fw-bold mb-0">Student & Parent Portal</h5>
        </div>

        <div class="d-flex align-items-center gap-3">
          <router-link :to="schoolWebsiteUrl" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="bi bi-globe me-1"></i> School Website
          </router-link>
          <button class="btn btn-sm btn-danger rounded-pill px-3" @click="handleLogout">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </button>
        </div>
      </header>

      <!-- View Container -->
      <main class="flex-grow-1 p-4 overflow-auto">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const router = useRouter()
const auth = useAuthStore()
const studentInfo = ref(null)

const schoolCode = computed(() => {
  return studentInfo.value?.school?.code 
    || auth.user?.school?.code 
    || localStorage.getItem('portal_school_code') 
    || sessionStorage.getItem('portal_school_code') 
    || ''
})

const schoolWebsiteUrl = computed(() => {
  return schoolCode.value ? `/s/${schoolCode.value}` : '/'
})

const signedInLabel = computed(() => {
  return auth.user?.email || studentInfo.value?.email || auth.user?.name || studentInfo.value?.full_name || 'Portal User'
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` }
  }
}

async function loadStudentData() {
  try {
    const res = await axios.get('/api/portal/profile', getAuthConfig())
    if (res.data.success) {
      studentInfo.value = res.data.data
      if (res.data.data?.school?.code) {
        localStorage.setItem('portal_school_code', res.data.data.school.code)
        sessionStorage.setItem('portal_school_code', res.data.data.school.code)
      }
    }
  } catch (err) {
    console.error('Failed to load portal student data', err)
  }
}

async function handleLogout() {
  const targetSchoolCode = schoolCode.value

  await auth.logout()

  // Redirect student/parent back to their dedicated school portal login
  if (targetSchoolCode) {
    router.push(`/s/${targetSchoolCode}/portal/login`)
  } else {
    router.push('/portal/login')
  }
}

onMounted(async () => {
  if (auth.token && !auth.user) {
    try {
      await auth.loadProfile()
    } catch (e) {
      console.warn('Failed to load profile', e)
    }
  }
  await loadStudentData()
})
</script>
