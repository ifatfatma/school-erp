<template>
  <div class="website-wrapper d-flex flex-column min-vh-100">
    <!-- Top Announcement Bar -->
    <div class="bg-primary text-white py-1 small border-bottom border-primary-subtle">
      <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <!-- Contact quick info -->
        <div class="d-flex align-items-center gap-3">
          <span><i class="bi bi-telephone-fill me-1"></i> {{ general.phone_primary || '+91 033 5562 9768' }}</span>
          <span class="d-none d-sm-inline"><i class="bi bi-envelope-fill me-1"></i> {{ general.email_admissions || 'admissions@stgeorge.edu' }}</span>
          <span class="d-none d-md-inline"><i class="bi bi-geo-alt-fill me-1"></i> {{ general.city || 'Kolkata' }}, {{ general.state || 'West Bengal' }}</span>
        </div>

        <!-- Campus Status & Portal Links -->
        <div class="d-flex align-items-center gap-2">
          <router-link :to="schoolLink('/admissions/track')" class="text-white text-decoration-none me-2">
            <i class="bi bi-search me-1"></i> Track Application
          </router-link>
          <span class="text-white-50">|</span>
          <router-link :to="portalLoginLink()" class="text-white text-decoration-none fw-semibold">
            <i class="bi bi-person-circle me-1"></i> Student/Parent Portal
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-2">
      <div class="container">
        <!-- School Brand & Logo -->
        <router-link :to="schoolLink('/')" class="navbar-brand d-flex align-items-center gap-2">
          <img
            v-if="general.logo"
            :src="general.logo"
            alt="Logo"
            class="rounded-3 shadow-sm border p-1 bg-white"
            style="width: 44px; height: 44px; object-fit: contain;"
          />
          <div v-else class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
            <i class="bi bi-mortarboard-fill fs-4"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <span class="fw-bold fs-5 text-dark d-block lh-1">{{ general.school_name || 'School ERP' }}</span>
              <span v-if="currentSchoolCode" class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.68rem;">{{ currentSchoolCode }}</span>
            </div>
            <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">{{ general.tagline || 'Excellence • Integrity • Innovation' }}</small>
          </div>
        </router-link>

        <button
          class="navbar-toggler border-0"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#websiteNav"
          aria-controls="websiteNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="websiteNav">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
            <li class="nav-item">
              <router-link :to="schoolLink('/')" class="nav-link px-3" active-class="text-primary active">Home</router-link>
            </li>
            <li class="nav-item">
              <router-link :to="schoolLink('/about')" class="nav-link px-3" active-class="text-primary active">About Us</router-link>
            </li>
            <li class="nav-item">
              <router-link :to="schoolLink('/academics')" class="nav-link px-3" active-class="text-primary active">Academics</router-link>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Admissions
              </a>
              <ul class="dropdown-menu shadow border-0 rounded-3">
                <li><router-link :to="schoolLink('/admissions')" class="dropdown-item py-2"><i class="bi bi-info-circle me-2 text-primary"></i> Admission Procedure</router-link></li>
                <li><router-link :to="schoolLink('/admissions/apply')" class="dropdown-item py-2"><i class="bi bi-file-earmark-plus me-2 text-success"></i> Apply Online Form</router-link></li>
                <li><router-link :to="schoolLink('/admissions/track')" class="dropdown-item py-2"><i class="bi bi-search me-2 text-info"></i> Track Application</router-link></li>
              </ul>
            </li>
            <li class="nav-item">
              <router-link :to="schoolLink('/contact')" class="nav-link px-3" active-class="text-primary active">Contact</router-link>
            </li>
          </ul>

          <div class="d-flex align-items-center gap-2">
            <router-link :to="schoolLink('/admissions/apply')" class="btn btn-primary rounded-pill px-3 shadow-sm">
              <i class="bi bi-pencil-square me-1"></i> Apply Now
            </router-link>

            <div class="dropdown">
              <button class="btn btn-outline-secondary rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li>
                  <router-link :to="portalLoginLink()" class="dropdown-item py-2">
                    <i class="bi bi-person-badge me-2 text-primary"></i> Student / Parent Portal
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <router-link :to="staffLoginLink()" class="dropdown-item py-2">
                    <i class="bi bi-shield-lock me-2 text-danger"></i> Staff & Admin ERP
                  </router-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Dynamic Content -->
    <main class="flex-grow-1">
      <router-view />
    </main>

    <!-- Public Website Footer -->
    <footer class="bg-dark text-white pt-5 pb-3 border-top border-secondary">
      <div class="container">
        <div class="row g-4 mb-4">
          <div class="col-lg-4 col-md-6">
            <div class="d-flex align-items-center gap-2 mb-3">
              <img
                v-if="general.logo"
                :src="general.logo"
                alt="Logo"
                class="rounded-3 p-1 bg-white border"
                style="width: 40px; height: 40px; object-fit: contain;"
              />
              <div v-else class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-mortarboard-fill fs-5"></i>
              </div>
              <h5 class="fw-bold mb-0 text-white">{{ general.school_name || 'School ERP' }}</h5>
            </div>
            <p class="text-white-50 small mb-3">
              {{ general.description || 'Empowering students through comprehensive education, fostering intellectual growth, moral character, and lifelong leadership.' }}
            </p>
            <div class="d-flex gap-2">
              <a :href="general.social_facebook || '#'" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-facebook"></i></a>
              <a :href="general.social_twitter || '#'" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-twitter-x"></i></a>
              <a :href="general.social_instagram || '#'" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-instagram"></i></a>
              <a :href="general.social_youtube || '#'" target="_blank" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-youtube"></i></a>
            </div>
          </div>

          <div class="col-lg-2 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Quick Links</h6>
            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
              <li><router-link :to="schoolLink('/')" class="text-white-50 text-decoration-none hover-white">Home</router-link></li>
              <li><router-link :to="schoolLink('/about')" class="text-white-50 text-decoration-none hover-white">About School</router-link></li>
              <li><router-link :to="schoolLink('/academics')" class="text-white-50 text-decoration-none hover-white">Academics</router-link></li>
              <li><router-link :to="schoolLink('/admissions')" class="text-white-50 text-decoration-none hover-white">Admissions</router-link></li>
              <li><router-link :to="schoolLink('/contact')" class="text-white-50 text-decoration-none hover-white">Contact Us</router-link></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Admissions & Portal</h6>
            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
              <li><router-link :to="schoolLink('/admissions/apply')" class="text-white-50 text-decoration-none hover-white">Online Application Form</router-link></li>
              <li><router-link :to="schoolLink('/admissions/track')" class="text-white-50 text-decoration-none hover-white">Track Application Status</router-link></li>
              <li><router-link :to="portalLoginLink()" class="text-white-50 text-decoration-none hover-white">Student & Parent Portal</router-link></li>
              <li><router-link :to="staffLoginLink()" class="text-white-50 text-decoration-none hover-white">Staff ERP Login</router-link></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Contact Info</h6>
            <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2 mb-0">
              <li><i class="bi bi-geo-alt me-2 text-primary"></i> {{ general.address || 'Campus Address' }}, {{ general.city || 'Kolkata' }} - {{ general.pincode || '700024' }}</li>
              <li><i class="bi bi-telephone me-2 text-primary"></i> {{ general.phone_primary || '+91 033 5562 9768' }}</li>
              <li><i class="bi bi-envelope me-2 text-primary"></i> {{ general.email_primary || 'info@school.edu' }}</li>
              <li><i class="bi bi-clock me-2 text-primary"></i> {{ general.office_hours || 'Mon - Sat: 8:00 AM - 3:30 PM' }}</li>
            </ul>
          </div>
        </div>

        <hr class="border-secondary my-3">

        <div class="d-flex flex-wrap justify-content-between align-items-center small text-white-50">
          <p class="mb-0">&copy; {{ new Date().getFullYear() }} {{ general.school_name || 'School ERP' }}. All rights reserved.</p>
          <div class="d-flex gap-3">
            <router-link :to="schoolLink('/admissions')" class="text-white-50 text-decoration-none">Privacy Policy</router-link>
            <router-link :to="schoolLink('/admissions')" class="text-white-50 text-decoration-none">Terms of Service</router-link>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, reactive, provide, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const websiteData = reactive({
  content: {},
  notices: [],
  school: null,
})

const general = ref({})
const allSchools = ref([])

const currentSchoolCode = computed(() => {
  return route.params.schoolCode || route.query.school || websiteData.school?.code || ''
})

/**
 * Generate a school-scoped URL link:
 * - If currently viewing /s/:schoolCode, generates /s/:schoolCode/path
 * - If on standard root /, returns clean path
 */
function schoolLink(path) {
  const clean = path.startsWith('/') ? path : '/' + path
  const code = currentSchoolCode.value
  if (code) {
    return clean === '/' ? `/s/${code}` : `/s/${code}${clean}`
  }
  return clean
}

function portalLoginLink() {
  const code = currentSchoolCode.value
  return code ? `/s/${code}/portal/login` : '/portal/login'
}

function staffLoginLink() {
  const code = currentSchoolCode.value
  return code ? `/s/${code}/login` : '/login'
}

provide('websiteData', websiteData)
provide('schoolLink', schoolLink)
provide('currentSchoolCode', currentSchoolCode)
provide('portalLoginLink', portalLoginLink)
provide('staffLoginLink', staffLoginLink)

async function loadContent() {
  try {
    const schoolParam = route.params.schoolCode || route.query.school || ''
    const res = await axios.get('/api/public/website-content', {
      params: schoolParam ? { school: schoolParam } : {}
    })
    if (res.data.success) {
      Object.assign(websiteData, res.data.data)
      general.value = res.data.data.content?.general || {}
      allSchools.value = res.data.data.all_schools || []

      // If viewing school-scoped route, update document title with school name
      if (res.data.data.school?.name) {
        document.title = `${res.data.data.school.name} - Official Portal`
      }
    }
  } catch (err) {
    console.error('Failed to load website content', err)
  }
}

onMounted(() => {
  loadContent()
})

// Watch for route param / query changes (e.g., when clicking another campus or link)
watch(
  () => [route.params.schoolCode, route.query.school],
  () => {
    loadContent()
  }
)
</script>

<style scoped>
.hover-white:hover {
  color: #fff !important;
  padding-left: 4px;
  transition: all 0.2s ease;
}
</style>
