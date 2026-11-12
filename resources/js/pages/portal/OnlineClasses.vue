<template>
  <div class="py-2">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
      <div>
        <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
          <i class="bi bi-camera-video-fill text-primary"></i>
          Online & Virtual Classes
        </h4>
        <p class="text-muted small mb-0">
          Join your live classes via Google Meet, Zoom, or Teams, and watch recorded lectures anytime.
        </p>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 small">
          <i class="bi bi-person-check me-1"></i> Student Portal Active
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="text-muted small mt-2">Loading your online classes...</p>
    </div>

    <div v-else>
      <!-- ========================================================
           1. LIVE CLASS IN PROGRESS BANNER (Highest Priority)
           ======================================================== -->
      <div v-if="liveClass" class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden live-banner">
        <div class="card-body p-4 text-white">
          <div class="row align-items-center g-3">
            <div class="col-lg-8">
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-white text-danger fw-bold rounded-pill px-3 py-1 pulse-animation">
                  <i class="bi bi-broadcast me-1"></i> LIVE NOW
                </span>
                <span class="badge bg-black bg-opacity-25 rounded-pill px-3 py-1">
                  {{ liveClass.platform_label }}
                </span>
                <span class="badge bg-black bg-opacity-25 rounded-pill px-3 py-1 font-monospace">
                  {{ liveClass.subject?.name }}
                </span>
              </div>

              <h3 class="fw-bold mb-1">{{ liveClass.title }}</h3>
              <p v-if="liveClass.description" class="mb-2 text-white-50 small">
                {{ liveClass.description }}
              </p>

              <div class="d-flex flex-wrap gap-3 small text-white-50 mt-2">
                <span><i class="bi bi-clock me-1 text-white"></i> Timing: <strong class="text-white">{{ liveClass.formatted_time_range }}</strong></span>
                <span><i class="bi bi-person me-1 text-white"></i> Host: <strong class="text-white">{{ liveClass.teacher ? (liveClass.teacher.first_name + ' ' + (liveClass.teacher.last_name || '')) : 'Teacher' }}</strong></span>
              </div>

              <!-- Zoom ID and Passcode if available -->
              <div v-if="liveClass.platform === 'zoom' && (liveClass.meeting_id || liveClass.meeting_password)" class="mt-3 p-2 rounded-3 bg-black bg-opacity-25 d-inline-flex align-items-center gap-3 small">
                <span v-if="liveClass.meeting_id">Meeting ID: <strong>{{ liveClass.meeting_id }}</strong></span>
                <span v-if="liveClass.meeting_password">Passcode: <strong>{{ liveClass.meeting_password }}</strong></span>
                <button class="btn btn-xs btn-light rounded-pill py-0 px-2" @click="copyCredentials(liveClass)">
                  <i class="bi bi-copy"></i>
                </button>
              </div>
            </div>

            <div class="col-lg-4 text-lg-end">
              <a
                :href="liveClass.meeting_link"
                target="_blank"
                class="btn btn-light btn-lg text-danger fw-bold rounded-pill px-4 shadow py-3 d-inline-flex align-items-center gap-2 join-btn-pulse"
              >
                <i class="bi bi-box-arrow-up-right fs-5"></i>
                <span>Join Live Class Now</span>
              </a>
              <small class="d-block text-white-50 mt-2" style="font-size: 0.75rem;">
                Click to open {{ liveClass.platform_label }} in new window
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================
           2. TODAY'S CLASS SCHEDULE
           ======================================================== -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h6 class="fw-bold mb-0">Today's Class Schedule</h6>
          </div>
          <span class="badge bg-light text-muted border small">{{ todayDate }}</span>
        </div>

        <div class="card-body p-4 pt-0">
          <div v-if="todayClasses.length === 0" class="text-center py-4 text-muted">
            <i class="bi bi-calendar2-check fs-2 text-secondary opacity-50 d-block mb-1"></i>
            <p class="small mb-0">No more online classes scheduled for today.</p>
          </div>

          <div v-else class="row g-3">
            <div v-for="c in todayClasses" :key="c.id" class="col-md-6">
              <div class="p-3 rounded-4 border bg-light h-100 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge rounded-pill px-2 py-1 small" :class="getPlatformBadge(c.platform)">
                      <i :class="getPlatformIcon(c.platform)" class="me-1"></i> {{ c.platform_label }}
                    </span>
                    <span v-if="c.is_live" class="badge bg-danger rounded-pill px-2 py-1 small">
                      <i class="bi bi-broadcast me-1"></i> LIVE
                    </span>
                    <span v-else-if="c.status === 'completed'" class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                      Completed
                    </span>
                    <span v-else class="badge bg-white text-muted border rounded-pill px-2 py-1 small">
                      {{ c.formatted_time_range }}
                    </span>
                  </div>

                  <h6 class="fw-bold mb-1 text-dark">{{ c.title }}</h6>
                  <p class="text-muted small mb-2">{{ c.subject?.name }} &bull; {{ c.teacher ? (c.teacher.first_name + ' ' + (c.teacher.last_name || '')) : 'Instructor' }}</p>

                  <p v-if="c.join_instructions" class="small text-secondary mb-2 fst-italic">
                    <i class="bi bi-info-circle me-1"></i> {{ c.join_instructions }}
                  </p>
                </div>

                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                  <span class="text-muted small"><i class="bi bi-clock me-1"></i> {{ c.duration_minutes || 45 }} Mins</span>
                  <a
                    :href="c.meeting_link"
                    target="_blank"
                    class="btn btn-sm rounded-pill px-3 fw-semibold"
                    :class="c.is_live ? 'btn-danger' : 'btn-outline-primary'"
                  >
                    <i class="bi bi-box-arrow-up-right me-1"></i>
                    {{ c.is_live ? 'Join Live Now' : 'Join Class' }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ========================================================
           3. UPCOMING & PAST CLASSES TABS
           ======================================================== -->
      <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white border-0 py-3 px-4">
          <ul class="nav nav-pills card-header-pills" role="tablist">
            <li class="nav-item">
              <button
                class="nav-link py-2 px-4 rounded-pill"
                :class="{ active: activeTab === 'upcoming' }"
                @click="activeTab = 'upcoming'"
              >
                <i class="bi bi-clock-history me-1"></i> Upcoming Classes ({{ upcomingClasses.length }})
              </button>
            </li>
            <li class="nav-item">
              <button
                class="nav-link py-2 px-4 rounded-pill"
                :class="{ active: activeTab === 'past' }"
                @click="activeTab = 'past'"
              >
                <i class="bi bi-camera-reels me-1"></i> Completed & Recordings ({{ pastClasses.length }})
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body p-4 pt-2">
          <!-- Upcoming Tab -->
          <div v-if="activeTab === 'upcoming'">
            <div v-if="upcomingClasses.length === 0" class="text-center py-4 text-muted">
              <i class="bi bi-calendar-check fs-2 text-secondary opacity-50 d-block mb-1"></i>
              <p class="small mb-0">No upcoming classes scheduled yet. Check back soon!</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                  <tr>
                    <th>Date & Time</th>
                    <th>Subject & Topic</th>
                    <th>Platform</th>
                    <th>Teacher</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in upcomingClasses" :key="item.id">
                    <td>
                      <div class="fw-semibold text-dark">{{ item.formatted_date }}</div>
                      <small class="text-muted">{{ item.formatted_time_range }}</small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border small mb-1">{{ item.subject?.name }}</span>
                      <div class="fw-bold text-dark">{{ item.title }}</div>
                    </td>
                    <td>
                      <span class="badge rounded-pill px-2 py-1 small" :class="getPlatformBadge(item.platform)">
                        <i :class="getPlatformIcon(item.platform)" class="me-1"></i> {{ item.platform_label }}
                      </span>
                    </td>
                    <td class="small text-muted">
                      {{ item.teacher ? (item.teacher.first_name + ' ' + (item.teacher.last_name || '')) : 'Staff' }}
                    </td>
                    <td class="text-end">
                      <a :href="item.meeting_link" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Room
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Past Tab -->
          <div v-if="activeTab === 'past'">
            <div v-if="pastClasses.length === 0" class="text-center py-4 text-muted">
              <p class="small mb-0">No past classes logged yet.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                  <tr>
                    <th>Class Date</th>
                    <th>Subject & Topic</th>
                    <th>Teacher</th>
                    <th>Status</th>
                    <th class="text-end">Recording Link</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="item in pastClasses" :key="item.id">
                    <td>
                      <span class="fw-semibold text-dark">{{ item.formatted_date }}</span>
                      <small class="d-block text-muted">{{ item.formatted_time_range }}</small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border small mb-1">{{ item.subject?.name }}</span>
                      <div class="fw-semibold text-dark">{{ item.title }}</div>
                    </td>
                    <td class="small text-muted">
                      {{ item.teacher ? (item.teacher.first_name + ' ' + (item.teacher.last_name || '')) : 'Staff' }}
                    </td>
                    <td>
                      <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">
                        Completed
                      </span>
                    </td>
                    <td class="text-end">
                      <a
                        v-if="item.recorded_link"
                        :href="item.recorded_link"
                        target="_blank"
                        class="btn btn-sm btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1"
                      >
                        <i class="bi bi-play-circle-fill"></i>
                        <span>Watch Video</span>
                      </a>
                      <span v-else class="text-muted small fst-italic">No recording</span>
                    </td>
                  </tr>
                </tbody>
              </table>
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
import Swal from 'sweetalert2'

const loading = ref(true)
const activeTab = ref('upcoming')

const liveClass = ref(null)
const todayClasses = ref([])
const upcomingClasses = ref([])
const pastClasses = ref([])

const todayDate = new Date().toLocaleDateString('en-IN', {
  weekday: 'long',
  year: 'numeric',
  month: 'short',
  day: 'numeric'
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return {
    headers: { Authorization: `Bearer ${token}` }
  }
}

async function loadPortalOnlineClasses() {
  loading.value = true
  try {
    const res = await axios.get('/api/portal/online-classes', getAuthConfig())
    if (res.data?.success) {
      liveClass.value = res.data.data.live_class
      todayClasses.value = res.data.data.today_classes || []
      upcomingClasses.value = res.data.data.upcoming_classes || []
      pastClasses.value = res.data.data.past_classes || []
    }
  } catch (err) {
    console.error('Failed to load portal online classes', err)
  } finally {
    loading.value = false
  }
}

function copyCredentials(item) {
  const text = `Meeting Link: ${item.meeting_link}\nMeeting ID: ${item.meeting_id}\nPasscode: ${item.meeting_password}`
  navigator.clipboard.writeText(text)
  Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: 'Meeting details copied!',
    timer: 1500,
    showConfirmButton: false
  })
}

function getPlatformIcon(platform) {
  return {
    google_meet: 'bi bi-google',
    zoom: 'bi bi-camera-video-fill',
    teams: 'bi bi-microsoft',
    jitsi: 'bi bi-globe-americas',
  }[platform] || 'bi bi-laptop'
}

function getPlatformBadge(platform) {
  return {
    google_meet: 'bg-success text-white',
    zoom: 'bg-primary text-white',
    teams: 'bg-purple text-white',
    jitsi: 'bg-info text-dark',
  }[platform] || 'bg-secondary text-white'
}

onMounted(() => {
  loadPortalOnlineClasses()
})
</script>

<style scoped>
.live-banner {
  background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
}
.pulse-animation {
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}
.join-btn-pulse {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.join-btn-pulse:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.2) !important;
}
.bg-purple {
  background-color: #6264A7 !important;
}
</style>
