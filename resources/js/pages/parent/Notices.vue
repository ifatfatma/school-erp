<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">School Notices, Circulars & Event Calendar</h4>
        <small class="text-muted">Official school announcements, upcoming parent-teacher meetings, examinations, and activity schedules</small>
      </div>
      <div>
        <div class="btn-group shadow-sm" role="group">
          <button
            type="button"
            class="btn btn-sm"
            :class="viewMode === 'notices' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="viewMode = 'notices'"
          >
            <i class="bi bi-megaphone me-1"></i> Notices & Circulars ({{ notices.length }})
          </button>
          <button
            type="button"
            class="btn btn-sm"
            :class="viewMode === 'events' ? 'btn-primary' : 'btn-outline-secondary'"
            @click="viewMode = 'events'"
          >
            <i class="bi bi-calendar-event me-1"></i> Academic Events ({{ events.length }})
          </button>
        </div>
      </div>
    </div>

    <!-- View Mode: Notices -->
    <div v-if="viewMode === 'notices'">
      <!-- Pinned Urgent Notices Banner if any -->
      <div v-if="pinnedNotices.length > 0" class="mb-4">
        <h6 class="text-uppercase fw-bold text-danger small mb-2">
          <i class="bi bi-pin-angle-fill me-1"></i> Important & Pinned Circulars
        </h6>
        <div class="row g-3">
          <div v-for="pn in pinnedNotices" :key="pn.id" class="col-12">
            <div class="card border-0 border-start border-danger border-4 shadow-sm rounded-4 p-4 bg-white">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-danger rounded-pill px-3 py-1">IMPORTANT</span>
                  <span class="badge bg-light text-dark border">Published: {{ formatDate(pn.publish_date) }}</span>
                </div>
                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="viewNotice(pn)">
                  Read Notice &rarr;
                </button>
              </div>
              <h5 class="fw-bold text-dark mb-1">{{ pn.title }}</h5>
              <p class="text-muted small mb-0">{{ pn.content || pn.description }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- General Notices Grid -->
      <h6 class="text-uppercase fw-bold text-muted small mb-3">
        <i class="bi bi-card-list me-1"></i> All School Circulars
      </h6>
      <div class="row g-3">
        <div v-if="notices.length === 0" class="col-12">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-megaphone text-muted fs-1 mb-2"></i>
            <h6 class="fw-bold text-dark">No notices currently published</h6>
            <p class="text-muted small mb-0">Check back regularly for updates from the school administration.</p>
          </div>
        </div>

        <div v-for="n in regularNotices" :key="n.id" class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">
                  {{ n.target_audience ? n.target_audience.toUpperCase() : 'ALL PARENTS' }}
                </span>
                <small class="text-muted">{{ formatDate(n.publish_date) }}</small>
              </div>
              <h5 class="fw-bold text-dark mb-2">{{ n.title }}</h5>
              <p class="text-muted small text-truncate-3 mb-3">
                {{ n.content || n.description }}
              </p>
            </div>

            <div class="border-top pt-3 d-flex justify-content-between align-items-center">
              <span v-if="n.attachment" class="badge bg-light text-dark border">
                <i class="bi bi-paperclip me-1"></i> Attachment
              </span>
              <span v-else class="text-muted small">General Circular</span>
              <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="viewNotice(n)">
                Read Full &rarr;
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Mode: Events -->
    <div v-if="viewMode === 'events'">
      <div class="row g-3">
        <div v-if="events.length === 0" class="col-12">
          <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
            <i class="bi bi-calendar2-event text-muted fs-1 mb-2"></i>
            <h6 class="fw-bold text-dark">No upcoming academic events scheduled</h6>
            <p class="text-muted small mb-0">Upcoming sports, exhibitions, and PTMs will be listed here.</p>
          </div>
        </div>

        <div v-for="ev in events" :key="ev.id" class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge rounded-pill px-3 py-1" :class="'bg-' + (ev.badge_color || 'primary')">
                  {{ ev.tag || 'Event' }}
                </span>
                <span class="badge bg-light text-dark border">
                  <i class="bi bi-clock me-1"></i>{{ ev.time }}
                </span>
              </div>
              <h5 class="fw-bold text-dark mb-2">{{ ev.title }}</h5>
              <p class="text-muted small mb-3">{{ ev.description }}</p>
            </div>

            <div class="border-top pt-3">
              <div class="small text-muted mb-1">
                <i class="bi bi-calendar3 me-1 text-primary"></i><strong>Date:</strong> {{ ev.date }}
              </div>
              <div class="small text-muted">
                <i class="bi bi-geo-alt me-1 text-danger"></i><strong>Venue:</strong> {{ ev.location }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notice Detail Modal -->
    <div
      v-if="selectedNotice"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.6); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-megaphone-fill fs-4"></i>
              <div>
                <h5 class="modal-title fw-bold mb-0">{{ selectedNotice.title }}</h5>
                <small class="text-white-50">Published on {{ formatDate(selectedNotice.publish_date) }}</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="selectedNotice = null"></button>
          </div>

          <div class="modal-body p-4">
            <div class="p-3 border rounded-3 bg-white mb-3 shadow-sm" style="white-space: pre-wrap; line-height: 1.7; font-size: 0.95rem; color: #2d3748;">
              <div v-if="selectedNotice.content" v-html="selectedNotice.content"></div>
              <div v-else-if="selectedNotice.description" v-html="selectedNotice.description"></div>
              <div v-else class="text-muted fst-italic">No additional circular content provided.</div>
            </div>

            <div v-if="selectedNotice.attachment" class="alert alert-primary d-flex align-items-center justify-content-between p-3 rounded-3 mb-0">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-paperclip fs-4"></i>
                <div>
                  <strong>Official Circular Document / Attachment</strong>
                  <small class="d-block text-muted">Downloadable notification PDF / circular image</small>
                </div>
              </div>
              <a :href="selectedNotice.attachment" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-download me-1"></i> Download File
              </a>
            </div>
          </div>

          <div class="modal-footer bg-light p-3">
            <button type="button" class="btn btn-secondary rounded-pill px-4" @click="selectedNotice = null">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

const viewMode = ref('notices')
const notices = ref([])
const events = ref([])
const selectedNotice = ref(null)

const pinnedNotices = computed(() => notices.value.filter(n => n.is_pinned))
const regularNotices = computed(() => notices.value.filter(n => !n.is_pinned))

function formatDate(dateStr) {
  if (!dateStr) return ''
  try {
    const raw = typeof dateStr === 'string' ? dateStr.split('T')[0] : dateStr
    const parts = String(raw).split('-')
    if (parts.length === 3) {
      const year = parseInt(parts[0], 10)
      const month = parseInt(parts[1], 10) - 1
      const day = parseInt(parts[2], 10)
      const d = new Date(year, month, day)
      return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })
    }
    const d = new Date(dateStr)
    if (!isNaN(d.getTime())) {
      return d.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' })
    }
    return dateStr
  } catch (e) {
    return dateStr
  }
}

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

async function loadNotices(overrideChildId = null) {
  const studentId = overrideChildId || localStorage.getItem('portal_active_student_id')
  try {
    const res = await axios.get('/api/parent/notices', getAuthConfig(studentId ? { student_id: Number(studentId) } : {}))
    if (res.data.success) {
      notices.value = res.data.data.notices || []
      events.value = res.data.data.events || []
    }
  } catch (err) {
    console.error('Failed to load parent notices', err)
  }
}

function viewNotice(n) {
  selectedNotice.value = n
}

function handleChildSwitch(e) {
  const childId = e.detail?.id
  loadNotices(childId)
}

onMounted(() => {
  loadNotices()
  window.addEventListener('parent-child-switched', handleChildSwitch)
})

onUnmounted(() => {
  window.removeEventListener('parent-child-switched', handleChildSwitch)
})
</script>

<style scoped>
.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>