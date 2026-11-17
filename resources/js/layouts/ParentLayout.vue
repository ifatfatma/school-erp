<template>
  <div class="d-flex min-vh-100 bg-light">
    <!-- Parent Sidebar -->
    <aside class="bg-white border-end shadow-sm d-flex flex-column" style="width: 275px; min-height: 100vh;">
      <!-- Brand & School Header -->
      <div class="p-3 border-bottom bg-primary text-white">
        <div class="d-flex align-items-center gap-2 mb-2">
          <img
            v-if="activeChild?.school?.logo || auth.user?.school?.logo"
            :src="activeChild?.school?.logo || auth.user?.school?.logo"
            alt="School Logo"
            class="bg-white rounded-3 p-1 shadow-sm"
            style="width: 36px; height: 36px; object-fit: contain;"
          />
          <div v-else class="bg-white text-primary rounded-3 p-1 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
            <i class="bi bi-mortarboard-fill fs-5"></i>
          </div>
          <div class="overflow-hidden">
            <span class="fw-bold fs-6 lh-1 text-truncate d-block" style="max-width: 195px;">
              {{ activeChild?.school?.name || auth.user?.school?.name || 'School ERP' }}
            </span>
            <small class="text-white-50" style="font-size: 0.72rem;">Parent Portal</small>
          </div>
        </div>

        <!-- Multi-Child Profile Switcher Card in Sidebar -->
        <div class="bg-white bg-opacity-10 rounded-3 p-2 text-white border border-white border-opacity-25 mt-2">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="badge bg-white text-primary rounded-pill px-2 py-0" style="font-size: 0.65rem;">
              <i class="bi bi-person-fill"></i> SELECTED CHILD
            </span>
            <span v-if="children.length > 1" class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">
              {{ children.length }} Children
            </span>
          </div>

          <div class="fw-bold text-truncate" style="font-size: 0.95rem;">
            {{ activeChild?.name || 'Child' }}
          </div>
          <div class="text-white-50" style="font-size: 0.75rem;">
            Adm No: <span class="text-white fw-semibold">{{ activeChild?.admission_no || '—' }}</span> &bull; 
            Class: <span class="text-white fw-semibold">{{ activeChild?.class?.name || '—' }}</span>
            <span v-if="activeChild?.section?.name"> ({{ activeChild.section.name }})</span>
          </div>

          <!-- Quick Dropdown Switcher if Multiple Children -->
          <div v-if="children.length > 1" class="mt-2 pt-2 border-top border-white border-opacity-25">
            <label class="form-label text-white-50 small mb-1" style="font-size: 0.7rem;">
              <i class="bi bi-arrow-left-right me-1"></i>Switch Child Profile:
            </label>
            <select
              class="form-select form-select-sm bg-white text-dark fw-semibold border-0 shadow-sm"
              style="font-size: 0.8rem;"
              :value="activeChild?.id"
              @change="onChildSelectChange($event.target.value)"
            >
              <option v-for="c in children" :key="c.id" :value="c.id">
                {{ c.name }} ({{ c.class?.name || 'Class' }})
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Parent Focused Navigation Links -->
      <nav class="p-2 flex-grow-1 overflow-auto">
        <ul class="nav nav-pills flex-column gap-1">
          <li class="nav-item">
            <router-link to="/parent/dashboard" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-grid-fill"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/fees" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-cash-stack"></i>
              <span>Fees & Payment</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/results" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-award-fill"></i>
              <span>Results & Report Cards</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/attendance" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-calendar-check-fill"></i>
              <span>Attendance Record</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/timetable" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-calendar3-range-fill"></i>
              <span>Timetable & Homework</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/notices" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-megaphone-fill"></i>
              <span>Notices & Events</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/leave-application" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-envelope-paper-fill"></i>
              <span>Leave Application</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link to="/parent/profile" class="nav-link py-2 px-3 d-flex align-items-center gap-2 text-dark rounded-3" active-class="active bg-primary text-white">
              <i class="bi bi-person-circle"></i>
              <span>Profile & Family</span>
            </router-link>
          </li>
        </ul>
      </nav>

      <!-- Bottom User & Logout -->
      <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
        <div class="overflow-hidden me-2">
          <small class="d-block text-muted" style="font-size: 0.7rem;">Logged in as Parent</small>
          <strong class="small text-truncate d-block text-dark fw-semibold" style="max-width: 165px;" :title="signedInLabel">
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
      <!-- Parent Top Header -->
      <header class="bg-white border-bottom shadow-sm px-4 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
          <h5 class="fw-bold mb-0 text-dark">Parent Portal</h5>

          <!-- Multi-Child Switcher Pill in Top Bar -->
          <div v-if="children.length > 1" class="dropdown">
            <button
              class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center gap-2 shadow-sm"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-people-fill text-primary"></i>
              <span>
                Child: <strong class="text-primary">{{ activeChild?.name || 'Select Child' }}</strong>
                ({{ activeChild?.class?.name || 'Class' }})
              </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-start shadow border-0 p-2" style="min-width: 290px; border-radius: 12px; z-index: 1050;">
              <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 py-1">
                Your Linked Children ({{ children.length }})
              </li>
              <li v-for="child in children" :key="child.id">
                <a
                  class="dropdown-item rounded-3 p-2 d-flex align-items-center justify-content-between mb-1"
                  :class="{ 'bg-primary text-white': child.id === activeChild?.id }"
                  href="#"
                  @click.prevent="switchChild(child)"
                >
                  <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div
                      class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                      :class="child.id === activeChild?.id ? 'bg-white text-primary' : 'bg-primary-subtle text-primary'"
                      style="width: 32px; height: 32px; font-size: 0.8rem;"
                    >
                      {{ child.name?.charAt(0) || 'C' }}
                    </div>
                    <div class="overflow-hidden">
                      <div class="fw-semibold text-truncate small">{{ child.name }}</div>
                      <div :class="child.id === activeChild?.id ? 'text-white-50' : 'text-muted'" style="font-size: 0.72rem;">
                        {{ child.class?.name || 'Class' }} - {{ child.section?.name || 'A' }} &bull; Roll: #{{ child.roll_no || '1' }}
                      </div>
                    </div>
                  </div>
                  <span
                    v-if="child.pending_balance > 0"
                    class="badge rounded-pill"
                    :class="child.id === activeChild?.id ? 'bg-warning text-dark' : 'bg-danger-subtle text-danger'"
                    style="font-size: 0.65rem;"
                  >
                    Due: ₹{{ Number(child.pending_balance).toLocaleString('en-IN') }}
                  </span>
                  <i v-else-if="child.id === activeChild?.id" class="bi bi-check-circle-fill text-white ms-2"></i>
                </a>
              </li>
            </ul>
          </div>

          <!-- Single Child Badge -->
          <div v-else-if="activeChild" class="d-none d-md-flex align-items-center gap-1 badge bg-light text-dark border px-3 py-2 rounded-pill">
            <i class="bi bi-person-fill text-primary"></i>
            <span>{{ activeChild?.name }}</span>
            <span class="text-muted">({{ activeChild?.class?.name || 'Class' }} - {{ activeChild?.section?.name || 'A' }})</span>
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
        <router-view v-slot="{ Component }">
          <component
            :is="Component"
            :key="activeChild?.id"
            :child="activeChild"
            :all-children="children"
            @switch-child="switchChild"
          />
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useParentPortalStore } from '@/stores/parentPortal'

const router = useRouter()
const auth = useAuthStore()
const parentStore = useParentPortalStore()

const children = computed(() => parentStore.children)
const activeChild = computed(() => parentStore.activeChild)

const schoolCode = computed(() => {
  return activeChild.value?.school?.code 
    || auth.user?.school?.code 
    || localStorage.getItem('portal_school_code') 
    || sessionStorage.getItem('portal_school_code') 
    || ''
})

const schoolWebsiteUrl = computed(() => {
  return schoolCode.value ? `/s/${schoolCode.value}` : '/'
})

const signedInLabel = computed(() => {
  return auth.user?.name || auth.user?.email || 'Parent User'
})

function switchChild(child) {
  parentStore.setActiveChild(child)
}

function onChildSelectChange(childId) {
  parentStore.switchChildById(childId)
}

async function handleLogout() {
  const targetSchoolCode = schoolCode.value
  await auth.logout()
  localStorage.removeItem('portal_active_student_id')

  if (targetSchoolCode) {
    router.push(`/s/${targetSchoolCode}/portal/login`)
  } else {
    router.push('/login')
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
  await parentStore.loadChildren()
})
</script>