<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Weekly Class Timetable</h4>
      <small class="text-muted">Daily periods, subjects, assigned educators, and classroom locations</small>
    </div>

    <!-- Day Selector Tabs -->
    <ul class="nav nav-pills mb-4 gap-2">
      <li v-for="d in days" :key="d" class="nav-item">
        <button
          class="nav-link rounded-pill px-3 py-2 fw-semibold"
          :class="activeDay === d ? 'active bg-primary' : 'bg-white text-dark shadow-sm'"
          @click="activeDay = d"
        >
          {{ d }}
        </button>
      </li>
    </ul>

    <!-- Day Schedule Cards -->
    <div v-if="currentDaySchedule.length === 0" class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
      <i class="bi bi-calendar-x fs-1 text-muted mb-2"></i>
      <h5 class="fw-bold text-muted">No scheduled classes for {{ activeDay }}</h5>
      <p class="small text-muted mb-0">Classes or activities have not been scheduled for this day.</p>
    </div>

    <div v-else class="row g-3">
      <div v-for="(slot, idx) in currentDaySchedule" :key="idx" class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 border-start border-4 border-primary">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-2">Period {{ idx + 1 }}</span>
            <span class="small text-muted fw-semibold"><i class="bi bi-clock me-1"></i> {{ slot.start_time }} - {{ slot.end_time }}</span>
          </div>
          <h5 class="fw-bold mb-1 text-dark">{{ slot.subject?.name || 'Subject' }}</h5>
          <small class="text-muted d-block mb-3">Code: {{ slot.subject?.code || 'SUB01' }}</small>

          <div class="d-flex justify-content-between align-items-center pt-3 border-top small text-muted">
            <span><i class="bi bi-person me-1"></i> {{ slot.teacher ? `${slot.teacher.first_name} ${slot.teacher.last_name || ''}` : 'Class Teacher' }}</span>
            <span><i class="bi bi-geo-alt me-1"></i> Room {{ slot.room || '101' }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
const activeDay = ref('Monday')
const timetable = ref({})

const currentDaySchedule = computed(() => {
  return timetable.value[activeDay.value] || []
})

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadTimetable() {
  try {
    const res = await axios.get('/api/portal/timetable', getAuthConfig())
    if (res.data.success) {
      timetable.value = res.data.data.timetable || {}
    }
  } catch (err) {
    console.error('Failed to load timetable', err)
  }
}

onMounted(() => {
  loadTimetable()
})
</script>
