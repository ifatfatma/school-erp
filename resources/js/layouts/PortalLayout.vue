<template>
  <div class="d-flex min-vh-100 bg-light">
    <!-- Portal Sidebar -->
    <aside class="bg-white border-end shadow-sm d-flex flex-column" style="width: 270px; min-height: 100vh;">
      <!-- Brand & School Header -->
      <div class="p-3 border-bottom bg-primary text-white">
        <div class="d-flex align-items-center gap-2 mb-2">
          <img
            v-if="activeStudent?.school?.logo || auth.user?.school?.logo"
            :src="activeStudent?.school?.logo || auth.user?.school?.logo"
            alt="School Logo"
            class="bg-white rounded-3 p-1 shadow-sm"
            style="width: 36px; height: 36px; object-fit: contain;"
          />
          <div v-else class="bg-white text-primary rounded-3 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
            <i class="bi bi-mortarboard-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <span class="fw-bold fs-6 lh-1 text-truncate d-block" style="max-width: 190px;">
              {{ activeStudent?.school?.name || auth.user?.school?.name || 'School ERP' }}
            </span>
            <small class="text-white-50" style="font-size: 0.72rem;">Student & Parent Portal</small>
          </div>
        </div>

        <!-- Active Child Card / Switcher in Sidebar -->
        <div class="bg-white bg-opacity-10 rounded-3 p-2 text-white border border-white border-opacity-25 mt-2">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="badge bg-white text-primary rounded-pill px-2 py-0" style="font-size: 0.65rem;">
              <i class="bi bi-person-fill"></i> ACTIVE STUDENT
            </span>
            <span v-if="children.length > 1" class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">
              {{ children.length }} Children Linked
            </span>
          </div>

          <div class="fw-bold text-truncate" style="font-size: 0.95rem;">
            {{ activeStudent?.name || activeStudent?.full_name || auth.user?.name || 'Student' }}
          </div>
          <div class="text-white-50" style="font-size: 0.75rem;">
            Adm: <span class="text-white fw-semibold">{{ activeStudent?.admission_no || '—' }}</span> &bull; 
            Class: <span class="text-white fw-semibold">{{ activeStudent?.class?.name || '—' }}</span>
            <span v-if="activeStudent?.section?.name"> ({{ activeStudent.section.name }})</span>
          </div>

          <!-- Quick Child Selector if Multiple -->
          <div v-if="children.length > 1" class="mt-2 pt-2 border-top border-white border-opacity-25">
            <label class="form-label text-white-50 small mb-1" style="font-size: 0.7rem;">
              <i class="bi bi-arrow-left-right me-1"></i>Switch Child Profile:
            </label>
            <select
              class="form-select form-select-sm bg-white text-dark fw-semibold border-0 shadow-sm"
              style="font-size: 0.8rem;"
              :value="activeStudent?.id"
              @change="onChildSelectChange($event.target.value)"
            >
              <option v-for="c in children" :key="c.id" :value="c.id">
                {{ c.name }} ({{ c.class?.name || 'Class' }})
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Navigation Links -->
      <nav class="p-2 flex-grow-1 overflow-auto">
        <ul class="nav nav-pills flex-column gap-1">
          <li class="nav-item">
            <router-link to="/portal/dashboard" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/attendance" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-calendar-check-fill"></i>
              <span>My Attendance</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/timetable" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-calendar3-range-fill"></i>
              <span>Timetable</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/homework" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-journal-bookmark-fill"></i>
              <span>Homework & Diary</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/online-classes" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-camera-video-fill"></i>
              <span>Online Classes</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/fees" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-cash-stack"></i>
              <span>Fees & Payment</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/results" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-award-fill"></i>
              <span>Exam Results & Reports</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/notices" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-megaphone-fill"></i>
              <span>Notices & Events</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/library" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-book-fill"></i>
              <span>Library Books</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/portal/profile" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
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
          <strong class="small text-truncate d-block text-dark fw-semibold" style="max-width: 160px;" :title="signedInLabel">
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
      <header class="bg-white border-bottom shadow-sm px-4 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h5 class="fw-bold mb-0 text-dark">Parent & Student Portal</h5>

          <!-- Multi-Child Profile Switcher Pill -->
          <div v-if="children.length > 1" class="dropdown">
            <button
              class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center gap-2 shadow-sm"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-person-circle text-primary"></i>
              <span>
                Child: <strong class="text-primary">{{ activeStudent?.name || activeStudent?.full_name || 'Select Child' }}</strong>
                ({{ activeStudent?.class?.name || 'Class' }})
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-start shadow border-0 p-2" style="min-width: 280px; border-radius: 12px;">
              <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">
                Linked Children ({{ children.length }})
              </li>
              <li v-for="child in children" :key="child.id">
                <a
                  class="dropdown-item rounded-3 p-2 d-flex align-items-center justify-content-between mb-1"
                  :class="{ 'bg-primary text-white': child.id === activeStudent?.id }"
                  href="#"
                  @click.prevent="switchChild(child)"
                >
                  <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div
                      class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                      :class="child.id === activeStudent?.id ? 'bg-white text-primary' : 'bg-primary-subtle text-primary'"
                      style="width: 32px; height: 32px; font-size: 0.8rem;"
                    >
                      {{ child.name?.charAt(0) || 'S' }}
                    </div>
                    <div class="overflow-hidden">
                      <div class="fw-semibold text-truncate small">{{ child.name }}</div>
                      <div :class="child.id === activeStudent?.id ? 'text-white-50' : 'text-muted'" style="font-size: 0.72rem;">
                        {{ child.class?.name || 'Class' }} - {{ child.section?.name || 'A' }} &bull; Adm: {{ child.admission_no }}
                      </div>
                    </div>
                  </div>
                  <span
                    v-if="child.pending_balance > 0"
                    class="badge rounded-pill"
                    :class="child.id === activeStudent?.id ? 'bg-warning text-dark' : 'bg-danger-subtle text-danger'"
                    style="font-size: 0.65rem;"
                  >
                    Due: ₹{{ Number(child.pending_balance).toLocaleString('en-IN') }}
                  </span>
                  <i v-else-if="child.id === activeStudent?.id" class="bi bi-check-circle-fill text-white ms-2"></i>
                </a>
              </li>
            </ul>
          </div>

          <!-- Single Child Badge -->
          <div v-else-if="activeStudent" class="d-none d-md-flex align-items-center gap-1 badge bg-light text-dark border px-3 py-2 rounded-pill">
            <i class="bi bi-person-fill text-primary"></i>
            <span>{{ activeStudent?.name || activeStudent?.full_name }}</span>
            <span class="text-muted">({{ activeStudent?.class?.name || 'Class' }})</span>
          </div>
        </div>

        <div class="d-flex align-items-center gap-2">
          <router-link :to="schoolWebsiteUrl" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-globe me-1"></i> School Website
          </router-link>
          <button class="btn btn-sm btn-danger rounded-pill px-3" @click="handleLogout">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </button>
        </div>
      </header>

      <!-- View Container -->
      <main class="flex-grow-1 p-4 overflow-auto">
        <router-view :key="activeStudent?.id" :student="activeStudent" />
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

const children = ref([])
const activeStudent = ref(null)

const schoolCode = computed(() => {
  return activeStudent.value?.school?.code 
    || auth.user?.school?.code 
    || localStorage.getItem('portal_school_code') 
    || sessionStorage.getItem('portal_school_code') 
    || ''
})

const schoolWebsiteUrl = computed(() => {
  return schoolCode.value ? `/s/${schoolCode.value}` : '/'
})

const signedInLabel = computed(() => {
  return auth.user?.email || activeStudent.value?.email || auth.user?.name || activeStudent.value?.name || 'Portal User'
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` }
  }
}

async function loadChildren() {
  try {
    const res = await axios.get('/api/portal/children', getAuthConfig())
    if (res.data.success && Array.isArray(res.data.data) && res.data.data.length > 0) {
      children.value = res.data.data
      
      const savedStudentId = localStorage.getItem('portal_active_student_id')
      let matched = children.value.find(c => String(c.id) === String(savedStudentId))
      
      if (!matched) {
        matched = children.value[0]
      }
      
      setActiveChild(matched, false)
    } else {
      // Fallback to profile
      await loadProfileFallback()
    }
  } catch (err) {
    console.error('Failed to load children list', err)
    await loadProfileFallback()
  }
}

async function loadProfileFallback() {
  try {
    const res = await axios.get('/api/portal/profile', getAuthConfig())
    if (res.data.success && res.data.data) {
      const p = res.data.data
      activeStudent.value = {
        id: p.id,
        name: p.full_name || p.name,
        full_name: p.full_name,
        admission_no: p.admission_no,
        roll_no: p.roll_no,
        class: p.class,
        section: p.section,
        school: p.school,
      }
      children.value = [activeStudent.value]
      localStorage.setItem('portal_active_student_id', String(p.id))
      if (p.school?.code) {
        localStorage.setItem('portal_school_code', p.school.code)
      }
    }
  } catch (err) {
    console.error('Failed to load portal fallback profile', err)
  }
}

function setActiveChild(child, shouldBroadcast = true) {
  if (!child) return
  activeStudent.value = child
  localStorage.setItem('portal_active_student_id', String(child.id))
  
  if (child.school?.code) {
    localStorage.setItem('portal_school_code', child.school.code)
    sessionStorage.setItem('portal_school_code', child.school.code)
  }

  if (shouldBroadcast) {
    window.dispatchEvent(new CustomEvent('portal-student-switched', { detail: child }))
  }
}

function switchChild(child) {
  if (activeStudent.value?.id === child.id) return
  setActiveChild(child, true)
}

function onChildSelectChange(childId) {
  const target = children.value.find(c => String(c.id) === String(childId))
  if (target) {
    switchChild(target)
  }
}

async function handleLogout() {
  const targetSchoolCode = schoolCode.value
  await auth.logout()
  localStorage.removeItem('portal_active_student_id')

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
  await loadChildren()
})
</script>