<template>
  <div class="saas-platform-wrapper d-flex flex-column min-vh-100 bg-white">
    <!-- Top Bar -->
    <div class="bg-dark text-white py-1 small border-bottom border-dark-subtle">
      <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-3">
          <span><i class="bi bi-shield-check text-success me-1"></i> {{ general.security_badge }}</span>
          <span class="d-none d-md-inline"><i class="bi bi-envelope me-1"></i> {{ general.support_email }}</span>
        </div>
        <div class="d-flex align-items-center gap-3">
          <a href="#directory" class="text-white text-decoration-none small">
            <i class="bi bi-search me-1 text-warning"></i> Find Your School
          </a>
          <span class="text-white-50">|</span>
          <router-link to="/login" class="text-white text-decoration-none fw-semibold small">
            <i class="bi bi-shield-lock me-1 text-primary"></i> Super Admin / Staff ERP
          </router-link>
        </div>
      </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-3">
      <div class="container">
        <!-- SaaS Brand Logo -->
        <router-link to="/" class="navbar-brand d-flex align-items-center gap-2">
          <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
            <i class="bi bi-cloud-check-fill fs-4"></i>
          </div>
          <div>
            <span class="fw-bold fs-4 text-dark d-block lh-1">{{ general.brand_name }}</span>
            <small class="text-muted text-uppercase" style="font-size: 0.68rem; letter-spacing: 1.5px;">{{ general.brand_tagline }}</small>
          </div>
        </router-link>

        <button
          class="navbar-toggler border-0 shadow-none"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#saasNavbar"
          aria-controls="saasNavbar"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="saasNavbar">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
            <li class="nav-item">
              <a href="#overview" class="nav-link px-3">Platform</a>
            </li>
            <li class="nav-item">
              <a href="#modules" class="nav-link px-3">ERP Modules</a>
            </li>
            <li class="nav-item">
              <a href="#pricing" class="nav-link px-3">Subscription Plans</a>
            </li>
            <li class="nav-item">
              <a href="#directory" class="nav-link px-3">Schools Directory</a>
            </li>
            <li class="nav-item">
              <a href="#contact" class="nav-link px-3">Request Demo</a>
            </li>
          </ul>

          <div class="d-flex align-items-center gap-2">
            <router-link to="/login" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
              <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </router-link>
            <a href="#register" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
              <i class="bi bi-rocket-takeoff-fill me-1"></i> Register School
            </a>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Dynamic SaaS Content -->
    <main class="flex-grow-1">
      <router-view />
    </main>

    <!-- SaaS Footer -->
    <footer class="bg-dark text-white pt-5 pb-4 border-top border-secondary">
      <div class="container">
        <div class="row g-4 mb-5">
          <div class="col-lg-4 col-md-6">
            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-cloud-check-fill fs-5"></i>
              </div>
              <h5 class="fw-bold mb-0 text-white">{{ general.brand_name }} Platform</h5>
            </div>
            <p class="text-white-50 small mb-3 leading-relaxed">
              {{ general.footer_about }}
            </p>
            <div class="d-flex gap-2">
              <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-linkedin"></i></a>
              <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-twitter-x"></i></a>
              <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width: 34px; height: 34px; line-height: 20px;"><i class="bi bi-youtube"></i></a>
            </div>
          </div>

          <div class="col-lg-2 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Platform</h6>
            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
              <li><a href="#overview" class="text-white-50 text-decoration-none hover-white">Overview</a></li>
              <li><a href="#modules" class="text-white-50 text-decoration-none hover-white">Core Modules</a></li>
              <li><a href="#pricing" class="text-white-50 text-decoration-none hover-white">Pricing & Plans</a></li>
              <li><a href="#register" class="text-white-50 text-decoration-none hover-white">School Onboarding</a></li>
              <li><a href="#directory" class="text-white-50 text-decoration-none hover-white">School Portals</a></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Key Solutions</h6>
            <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
              <li><span class="text-white-50">Online Student Admissions & SIS</span></li>
              <li><span class="text-white-50">Automated Fees & Accounting</span></li>
              <li><span class="text-white-50">Examination & Dynamic Report Cards</span></li>
              <li><span class="text-white-50">GPS Fleet & Route Management</span></li>
              <li><span class="text-white-50">Residential Hostel Dormitories</span></li>
            </ul>
          </div>

          <div class="col-lg-3 col-md-6">
            <h6 class="text-uppercase fw-bold text-primary mb-3">Enterprise Sales</h6>
            <ul class="list-unstyled small text-white-50 d-flex flex-column gap-2 mb-0">
              <li><i class="bi bi-geo-alt me-2 text-primary"></i> Global Technology Tech Park, Sector V</li>
              <li><i class="bi bi-telephone me-2 text-primary"></i> +91 1800 123 4567 (Toll-Free)</li>
              <li><i class="bi bi-envelope me-2 text-primary"></i> sales@schoolerpcloud.com</li>
              <li><i class="bi bi-clock me-2 text-primary"></i> Support Hours: 24/7 Monitoring</li>
            </ul>
          </div>
        </div>

        <hr class="border-secondary my-3">

        <div class="d-flex flex-wrap justify-content-between align-items-center small text-white-50">
          <p class="mb-0">&copy; {{ new Date().getFullYear() }} EduCloud ERP Inc. All rights reserved.</p>
          <div class="d-flex gap-3">
            <a href="#" class="text-white-50 text-decoration-none">Privacy Policy</a>
            <a href="#" class="text-white-50 text-decoration-none">Terms of Service</a>
            <a href="#" class="text-white-50 text-decoration-none">Data Security SLA</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import axios from 'axios'

const general = reactive({
  brand_name: 'EduCloud ERP',
  brand_tagline: 'Next-Gen School Operating System',
  support_email: 'contact@schoolerpcloud.com',
  security_badge: 'ISO 27001 Certified • Bank-Grade Cloud Security',
  footer_about: 'The premier all-in-one cloud platform engineered for forward-thinking schools, academies, and multi-campus institutions. Manage admissions, examinations, finance, fleets, and portals in one secure ecosystem.',
})

async function fetchLayoutSettings() {
  try {
    const res = await axios.get('/api/public/saas/content')
    if (res.data?.success && res.data?.data?.general) {
      Object.assign(general, res.data.data.general)
    }
  } catch (e) {
    // Fallback defaults
  }
}

onMounted(() => {
  fetchLayoutSettings()
})
</script>

<style scoped>
.hover-white:hover {
  color: #fff !important;
  padding-left: 3px;
  transition: all 0.2s ease;
}
</style>
