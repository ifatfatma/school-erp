<template>
  <div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'School Management' },
        { label: 'Biometric Attendance' }
      ]"
    />

    <!-- Header & Tabs -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">
          <i class="bi bi-person-badge-fill text-primary me-2"></i>Biometric Attendance System
        </h4>
        <small class="text-muted">
          Unified Face Recognition scanner, Optical Fingerprint console, credential enrollment, and physical terminal management.
        </small>
      </div>
      <div>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fw-semibold">
          <i class="bi bi-shield-check me-1"></i> Multi-Tenant School Scoped
        </span>
      </div>
    </div>

    <!-- Navigation Pills / Tabs -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-2 mb-4">
      <ul class="nav nav-pills nav-fill gap-1">
        <li class="nav-item">
          <button
            class="nav-link rounded-3 fw-semibold py-2"
            :class="{ active: currentTab === 'scanner' }"
            @click="setTab('scanner')"
          >
            <i class="bi bi-camera-video me-1"></i> Live Face Scanner
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link rounded-3 fw-semibold py-2"
            :class="{ active: currentTab === 'fingerprint' }"
            @click="setTab('fingerprint')"
          >
            <i class="bi bi-fingerprint me-1"></i> Fingerprint Console
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link rounded-3 fw-semibold py-2"
            :class="{ active: currentTab === 'enrollment' }"
            @click="setTab('enrollment')"
          >
            <i class="bi bi-person-bounding-box me-1"></i> Credential Enrollment
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link rounded-3 fw-semibold py-2"
            :class="{ active: currentTab === 'devices' }"
            @click="setTab('devices')"
          >
            <i class="bi bi-hdd-network me-1"></i> Terminals & Kiosks
          </button>
        </li>
        <li class="nav-item">
          <button
            class="nav-link rounded-3 fw-semibold py-2"
            :class="{ active: currentTab === 'logs' }"
            @click="setTab('logs')"
          >
            <i class="bi bi-clock-history me-1"></i> Audit Logs
          </button>
        </li>
      </ul>
    </div>

    <!-- Active Tab Component -->
    <div class="tab-content">
      <LiveScanner v-if="currentTab === 'scanner'" />
      <FingerprintScanner v-else-if="currentTab === 'fingerprint'" />
      <Enrollment v-else-if="currentTab === 'enrollment'" />
      <Devices v-else-if="currentTab === 'devices'" />
      <Logs v-else-if="currentTab === 'logs'" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'
import LiveScanner from './LiveScanner.vue'
import FingerprintScanner from './FingerprintScanner.vue'
import Enrollment from './Enrollment.vue'
import Devices from './Devices.vue'
import Logs from './Logs.vue'

const route = useRoute()
const router = useRouter()

const currentTab = ref('scanner')

function setTab(tab) {
  currentTab.value = tab
  router.replace({ query: { ...route.query, tab } })
}

onMounted(() => {
  if (route.query.tab) {
    currentTab.value = route.query.tab
  }
})

watch(
  () => route.query.tab,
  (newTab) => {
    if (newTab && newTab !== currentTab.value) {
      currentTab.value = newTab
    }
  }
)
</script>

<style scoped>
.nav-pills .nav-link {
  color: #475569;
  transition: all 0.2s ease;
}
.nav-pills .nav-link.active {
  background-color: #2563eb;
  color: #ffffff;
  box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
}
</style>
