<template>
  <div class="saas-landing">
    <!-- Hero Section -->
    <section id="overview" class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0b1f44 0%, #0d6efd 100%);">
      <div class="container py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-7">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-3 shadow-sm">
              <i class="bi bi-stars me-1"></i> {{ platformContent.hero.badge }}
            </span>
            <h1 class="display-4 fw-bold lh-sm mb-3">
              {{ platformContent.hero.heading_line1 }} <br>
              <span class="text-warning">{{ platformContent.hero.heading_line2 }}</span>
            </h1>
            <p class="lead text-white-50 mb-4 pe-lg-4">
              {{ platformContent.hero.subheading }}
            </p>

            <div class="d-flex flex-wrap gap-3">
              <a href="#register" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow">
                <i class="bi bi-rocket-takeoff-fill me-2"></i> {{ platformContent.hero.cta_register_text }}
              </a>
              <a href="#pricing" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-tag-fill me-2"></i> {{ platformContent.hero.cta_pricing_text }}
              </a>
              <a href="#directory" class="btn btn-link text-white text-decoration-none px-3 d-flex align-items-center">
                {{ platformContent.hero.cta_directory_text }} <i class="bi bi-arrow-right ms-2"></i>
              </a>
            </div>

            <!-- Platform Highlights -->
            <div class="row mt-5 pt-3 border-top border-white-50 text-white g-3">
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ platformContent.stats.stat1_value }}</h3>
                <small class="text-white-50">{{ platformContent.stats.stat1_label }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ platformContent.stats.stat2_value }}</h3>
                <small class="text-white-50">{{ platformContent.stats.stat2_label }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ platformContent.stats.stat3_value }}</h3>
                <small class="text-white-50">{{ platformContent.stats.stat3_label }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ platformContent.stats.stat4_value }}</h3>
                <small class="text-white-50">{{ platformContent.stats.stat4_label }}</small>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <!-- Platform Mockup / Highlights Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white text-dark">
              <div class="card-header bg-dark text-white p-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                    <h6 class="fw-bold mb-0">{{ platformContent.architecture.card_title }}</h6>
                  </div>
                  <span class="badge bg-primary rounded-pill">{{ platformContent.architecture.card_badge }}</span>
                </div>
                <small class="text-white-50 d-block mt-1">{{ platformContent.architecture.card_subtitle }}</small>
              </div>
              <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3 border">
                  <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-globe fs-5"></i>
                  </div>
                  <div>
                    <strong class="d-block text-dark">{{ platformContent.architecture.item1_title }}</strong>
                    <small class="text-muted">{{ platformContent.architecture.item1_desc }}</small>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-primary-subtle rounded-3 mb-3 border border-primary-subtle">
                  <div class="bg-success text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-building-check fs-5"></i>
                  </div>
                  <div>
                    <strong class="d-block text-primary">{{ platformContent.architecture.item2_title }}</strong>
                    <small class="text-muted">{{ platformContent.architecture.item2_desc }}</small>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                  <div class="bg-warning text-dark rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person-badge fs-5"></i>
                  </div>
                  <div>
                    <strong class="d-block text-dark">{{ platformContent.architecture.item3_title }}</strong>
                    <small class="text-muted">{{ platformContent.architecture.item3_desc }}</small>
                  </div>
                </div>

                <div class="text-center mt-4 pt-2">
                  <a href="#register" class="btn btn-primary w-100 rounded-pill fw-bold">
                    {{ platformContent.architecture.cta_button_text }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ERP Modules Grid -->
    <section id="modules" class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small">Full Institutional Capabilities</span>
          <h2 class="display-6 fw-bold mt-1">Enterprise-Grade Modules Built-In</h2>
          <p class="text-muted mx-auto" style="max-width: 650px;">
            Every school registered on our platform receives full access to a modular, integrated management suite configured specifically for their academic regulations.
          </p>
        </div>

        <div class="row g-4">
          <div v-for="mod in modules" :key="mod.title" class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white transition-hover">
              <div :class="`bg-${mod.color}-subtle text-${mod.color} rounded-3 p-3 d-inline-flex align-items-center justify-content-center mb-3`" style="width: 52px; height: 52px;">
                <i :class="`bi ${mod.icon} fs-4`"></i>
              </div>
              <h5 class="fw-bold mb-2">{{ mod.title }}</h5>
              <p class="text-muted small mb-0 leading-relaxed">{{ mod.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing & Subscription Plans -->
    <section id="pricing" class="py-5 bg-white">
      <div class="container py-4">
        <div class="text-center mb-4">
          <span class="text-primary fw-bold text-uppercase small">Transparent & Predictable</span>
          <h2 class="display-6 fw-bold mt-1">School ERP Subscription Plans</h2>
          <p class="text-muted mx-auto" style="max-width: 600px;">
            Scale seamlessly from small academies to multi-branch educational networks. All plans include automated database updates and free platform maintenance.
          </p>

          <!-- Billing Period Toggle -->
          <div class="d-inline-flex align-items-center gap-2 p-1 bg-light rounded-pill border mt-3">
            <button
              class="btn btn-sm rounded-pill px-3 fw-semibold"
              :class="billingCycle === 'monthly' ? 'btn-primary text-white shadow-sm' : 'text-muted'"
              @click="billingCycle = 'monthly'"
            >
              Monthly Billing
            </button>
            <button
              class="btn btn-sm rounded-pill px-3 fw-semibold"
              :class="billingCycle === 'annual' ? 'btn-primary text-white shadow-sm' : 'text-muted'"
              @click="billingCycle = 'annual'"
            >
              Annual Billing <span class="badge bg-warning text-dark ms-1">Save 18%</span>
            </button>
          </div>
        </div>

        <div class="row g-4 mt-2 justify-content-center">
          <div v-for="plan in plans" :key="plan.id" class="col-lg-4 col-md-6">
            <div
              class="card h-100 rounded-4 p-4 position-relative border-2"
              :class="plan.popular ? 'border-primary shadow-lg' : 'border-light-subtle shadow-sm bg-white'"
            >
              <div v-if="plan.popular" class="position-absolute top-0 start-50 translate-middle">
                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                  {{ plan.badge }}
                </span>
              </div>

              <div class="text-center mt-2 mb-4">
                <h5 class="fw-bold mb-1">{{ plan.name }}</h5>
                <small class="text-muted">{{ plan.student_limit === 'Unlimited' ? 'Unlimited Students' : `Up to ${plan.student_limit} Students` }}</small>

                <div class="my-4">
                  <h2 class="display-5 fw-bold mb-0 text-primary">
                    ₹{{ (billingCycle === 'monthly' ? plan.price_monthly : Math.round(plan.price_annual / 12)).toLocaleString() }}
                  </h2>
                  <small class="text-muted">per school / month (billed {{ billingCycle }})</small>
                </div>

                <a
                  href="#register"
                  class="btn w-100 rounded-pill py-2 fw-bold"
                  :class="plan.popular ? 'btn-primary shadow' : 'btn-outline-primary'"
                  @click="selectedPlan = plan.id"
                >
                  Start 14-Day Free Trial
                </a>
              </div>

              <hr class="text-muted">

              <h6 class="fw-bold small text-uppercase text-muted mb-3">Included Platform Features:</h6>
              <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                <li v-for="f in plan.features" :key="f" class="d-flex align-items-center gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6"></i>
                  <span>{{ f }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- School Registration / Onboarding Form -->
    <section id="register" class="py-5 bg-light">
      <div class="container py-4">
        <div class="row justify-content-center">
          <div class="col-lg-10 col-xl-9">
            <div class="text-center mb-4">
              <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
                <i class="bi bi-mortarboard me-1"></i> Instant Institution Onboarding
              </span>
              <h2 class="display-6 fw-bold">Register Your School & Start Free Trial</h2>
              <p class="text-muted">Setup your school in seconds. Your dedicated school portal will be provisioned immediately.</p>
            </div>

            <!-- Registration Success State -->
            <div v-if="registeredSchool" class="card border-0 shadow-lg rounded-4 p-5 text-center bg-white">
              <div class="bg-success text-white rounded-circle mx-auto p-4 mb-3 d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                <i class="bi bi-check-lg fs-1"></i>
              </div>
              <h3 class="fw-bold text-success mb-2">School Provisioned Successfully!</h3>
              <p class="text-muted mb-4">
                <strong>{{ registeredSchool.school_name }}</strong> is now live on our multi-tenant network with School Code: <span class="badge bg-primary font-monospace fs-6">{{ registeredSchool.school_code }}</span>
              </p>

              <div class="card bg-light border-0 rounded-4 p-4 mx-auto mb-4 text-start" style="max-width: 550px;">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-key-fill me-2"></i> Initial Portal Access Credentials:</h6>
                <div class="mb-2"><strong>School Portal URL:</strong> <a :href="registeredSchool.portal_url" target="_blank" class="text-primary font-monospace ms-2">{{ registeredSchool.portal_url }}</a></div>
                <div class="mb-2"><strong>School Admin Email:</strong> <span class="font-monospace text-dark ms-2">{{ registeredSchool.admin_email }}</span></div>
                <div class="mb-2"><strong>Default Password:</strong> <span class="font-monospace text-danger ms-2">{{ registeredSchool.default_password }}</span></div>
                <small class="text-muted d-block mt-3 border-top pt-2">Please save these details. You can log into your ERP and customize settings immediately.</small>
              </div>

              <div class="d-flex flex-wrap justify-content-center gap-3">
                <a :href="registeredSchool.portal_url" class="btn btn-primary rounded-pill px-4 fw-bold">
                  <i class="bi bi-box-arrow-up-right me-1"></i> Open School Portal
                </a>
                <a :href="registeredSchool.admin_login_url" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                  <i class="bi bi-shield-lock me-1"></i> Staff ERP Sign In
                </a>
                <button class="btn btn-link text-muted" @click="registeredSchool = null">Register Another School</button>
              </div>
            </div>

            <!-- Registration Input Form -->
            <div v-else class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
              <div class="card-header bg-primary text-white p-4 border-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-building-add me-2"></i> School Registration Form</h5>
                <small class="text-white-50">Zero credit card required for 14-day full feature trial</small>
              </div>

              <div class="card-body p-4 p-md-5">
                <form @submit.prevent="submitRegistration">
                  <div class="row g-3">
                    <div class="col-md-8">
                      <label class="form-label fw-semibold">Official School Name <span class="text-danger">*</span></label>
                      <input type="text" v-model="form.school_name" class="form-control" placeholder="e.g. Cambridge Academy" required>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-semibold">School Code / Prefix <span class="text-danger">*</span></label>
                      <input type="text" v-model="form.school_code" class="form-control text-uppercase font-monospace" placeholder="e.g. CAMB01" required>
                      <small class="text-muted" style="font-size: 0.75rem;">Used in URL: /s/CODE</small>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Administrator Full Name <span class="text-danger">*</span></label>
                      <input type="text" v-model="form.admin_name" class="form-control" placeholder="Principal / IT Director" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Administrator Email <span class="text-danger">*</span></label>
                      <input type="email" v-model="form.admin_email" class="form-control" placeholder="admin@school.com" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Official Phone / Mobile <span class="text-danger">*</span></label>
                      <input type="tel" v-model="form.admin_phone" class="form-control" placeholder="+91 98765 43210" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">City & State <span class="text-danger">*</span></label>
                      <input type="text" v-model="form.city" class="form-control" placeholder="e.g. Kolkata, West Bengal" required>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Select Desired Plan</label>
                      <select v-model="form.plan" class="form-select">
                        <option value="starter">Starter / Basic (Up to 300 Students)</option>
                        <option value="growth">Professional / Growth (Up to 1200 Students) - Recommended</option>
                        <option value="enterprise">Enterprise (Unlimited Campuses)</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-semibold">Approximate Student Strength</label>
                      <input type="number" v-model="form.estimated_students" class="form-control" placeholder="e.g. 500">
                    </div>

                    <div class="col-12 mt-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termsCheck" v-model="agreed" required>
                        <label class="form-check-label small text-muted" for="termsCheck">
                          I agree to EduCloud ERP's Terms of Service, Privacy Policy, and 14-day free trial conditions.
                        </label>
                      </div>
                    </div>

                    <div class="col-12 mt-4">
                      <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow" :disabled="registering">
                        <span v-if="registering" class="spinner-border spinner-border-sm me-2"></span>
                        {{ registering ? 'Provisioning School...' : 'Create School & Start Trial' }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- School Directory Section -->
    <section id="directory" class="py-5 bg-white">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small">Looking For Your School?</span>
          <h2 class="display-6 fw-bold mt-1">Registered School Portals</h2>
          <p class="text-muted mx-auto" style="max-width: 600px;">
            Students, parents, and teachers can locate their respective institution below to access their dedicated school website and portal.
          </p>
        </div>

        <div v-if="loadingSchools" class="text-center py-4">
          <div class="spinner-border text-primary spinner-border-sm me-2"></div> Loading registered schools...
        </div>

        <div v-else class="row g-4">
          <div v-for="sch in schools" :key="sch.id" class="col-lg-4 col-md-6">
            <div class="card border rounded-4 p-4 h-100 shadow-sm bg-white">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img v-if="sch.logo" :src="sch.logo" alt="" class="rounded-3 border p-1 bg-white" style="width: 48px; height: 48px; object-fit: contain;">
                <div v-else class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                  <i class="bi bi-mortarboard-fill fs-4"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-0 text-dark">{{ sch.name }}</h6>
                  <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">Code: {{ sch.code }}</span>
                </div>
              </div>

              <p class="small text-muted mb-3">
                <i class="bi bi-geo-alt me-1 text-primary"></i> {{ sch.city || 'Campus' }}, {{ sch.state || 'India' }}
              </p>

              <div class="d-flex gap-2 mt-auto pt-2 border-top">
                <router-link :to="sch.portal_url" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                  <i class="bi bi-globe me-1"></i> School Site
                </router-link>
                <router-link :to="sch.student_login_url" class="btn btn-primary btn-sm rounded-pill flex-grow-1">
                  <i class="bi bi-person-circle me-1"></i> Portal Login
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Request Demo / Contact Section -->
    <section id="contact" class="py-5 bg-light">
      <div class="container py-4">
        <div class="row g-5 align-items-center">
          <div class="col-lg-5">
            <span class="text-primary fw-bold text-uppercase small">Speak With A Specialist</span>
            <h2 class="display-6 fw-bold mt-1 mb-3">Request A Personalized Demo</h2>
            <p class="text-muted mb-4">
              Our academic consultants can walk your management and IT committee through modules, data migration, and custom pricing.
            </p>

            <div class="d-flex flex-column gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                  <i class="bi bi-telephone-fill small"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Sales Hotline</small>
                  <strong>+91 1800 123 4567</strong>
                </div>
              </div>

              <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                  <i class="bi bi-envelope-fill small"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Direct Inquiries</small>
                  <strong>sales@schoolerpcloud.com</strong>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
              <h5 class="fw-bold mb-3">Schedule Your Demo</h5>
              <div v-if="demoSuccess" class="alert alert-success border-0 rounded-3">
                <i class="bi bi-check-circle-fill me-2"></i> {{ demoSuccess }}
              </div>
              <form @submit.prevent="submitDemo">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Institution Name <span class="text-danger">*</span></label>
                    <input type="text" v-model="demoForm.institution_name" class="form-control" placeholder="School or College" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Person <span class="text-danger">*</span></label>
                    <input type="text" v-model="demoForm.contact_person" class="form-control" placeholder="Your Name" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Work Email <span class="text-danger">*</span></label>
                    <input type="email" v-model="demoForm.email" class="form-control" placeholder="name@institution.com" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                    <input type="tel" v-model="demoForm.phone" class="form-control" placeholder="Contact number" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-semibold">Special Requirements / Notes</label>
                    <textarea v-model="demoForm.message" class="form-control" rows="3" placeholder="Tell us about your campus requirements..."></textarea>
                  </div>
                  <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" :disabled="demoLoading">
                      <span v-if="demoLoading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ demoLoading ? 'Submitting...' : 'Submit Request' }}
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const billingCycle = ref('annual')
const selectedPlan = ref('growth')
const agreed = ref(false)
const registering = ref(false)
const registeredSchool = ref(null)

const loadingSchools = ref(false)
const schools = ref([])

const demoLoading = ref(false)
const demoSuccess = ref('')

const modules = [
  { title: 'Student Information System', icon: 'bi-people-fill', color: 'primary', desc: 'Complete student digital cumulative records, automated admission numbers, bio tracking, and guardian management.' },
  { title: 'Attendance & Biometrics', icon: 'bi-calendar-check-fill', color: 'success', desc: 'Real-time teacher and student attendance tracking, leave requests, daily automated SMS alerts to parents.' },
  { title: 'Fee Automation & Accounts', icon: 'bi-cash-coin', color: 'danger', desc: 'Configurable fee heads, automated monthly dues, fine calculations, payment gateways, and instant printable receipts.' },
  { title: 'Examinations & Report Cards', icon: 'bi-award-fill', color: 'warning', desc: 'Exam schedules, mark entries, grading scales, GPA calculation, rank determination, and one-click PDF report card publishing.' },
  { title: 'GPS Transport Fleet', icon: 'bi-bus-front-fill', color: 'info', desc: 'Vehicle route allocations, stop timings, driver management, student pickup rosters, and transport fee integration.' },
  { title: 'Residential Hostel Dorms', icon: 'bi-building-fill', color: 'secondary', desc: 'Hostel buildings, room & bed allocations, visitor checkout logs, warden complaint handling, and meal management.' },
  { title: 'Digital Library System', icon: 'bi-book-half', color: 'primary', desc: 'Catalog books with barcodes, manage renewals, book returns, lost copy fines, and student library cards.' },
  { title: 'Live Virtual Classrooms', icon: 'bi-camera-video-fill', color: 'danger', desc: 'Schedule and host interactive live classes with Google Meet, Zoom, MS Teams, or instant free in-browser rooms with lecture recording links.' },
  { title: 'Self-Service Student Portal', icon: 'bi-person-circle', color: 'success', desc: 'Dedicated self-service interface for students & parents to monitor attendance, fee dues, exam results, and timetable.' },
  { title: 'Multi-Tenant Security', icon: 'bi-shield-lock-fill', color: 'dark', desc: 'Role-based access control, strict school-isolated database scoping, and centralized root Super Admin dashboard.' },
]

const plans = ref([
  {
    id: 'starter',
    name: 'Starter / Basic',
    badge: 'Small Institutions',
    price_monthly: 2999,
    price_annual: 29990,
    student_limit: 300,
    features: [
      'Student Information Management',
      'Daily Attendance & SMS Alerts',
      'Fee Collection & Digital Receipts',
      'Examination & Report Cards',
      'Student & Parent Web Portal',
      'Live Online Classes & Meeting Links',
      'Standard Email Support',
    ],
    popular: false,
  },
  {
    id: 'growth',
    name: 'Professional / Growth',
    badge: 'Most Popular',
    price_monthly: 5999,
    price_annual: 59990,
    student_limit: 1200,
    features: [
      'Everything in Starter Plan',
      'Live Online Classes (Google Meet / Zoom / MS Teams)',
      'Instant Free In-Browser Video Rooms (Jitsi)',
      'Class Lecture Recording Attachments',
      'Online Admissions & Public Website',
      'Digital Library Management',
      'Hostel & Dormitory Allocation',
      'Transport Routes & Vehicle Fleet Tracking',
      'Online Fee Payment Gateway',
      'Priority Email & Phone Support',
    ],
    popular: true,
  },
  {
    id: 'enterprise',
    name: 'Enterprise / Multi-Branch',
    badge: 'Large Campuses',
    price_monthly: 11999,
    price_annual: 119990,
    student_limit: 'Unlimited',
    features: [
      'Everything in Professional Plan',
      'Unlimited HD Live Virtual Classrooms & Recordings',
      'Multi-Branch & Campus Aggregation',
      'Dedicated Custom Domain / Subdomain',
      'Custom ERP Modules & Branding',
      'Automated Daily Cloud Backups',
      'Dedicated Account Manager & 24/7 SLA',
    ],
    popular: false,
  },
])

const platformContent = reactive({
  hero: {
    badge: 'Cloud Multi-Tenant School ERP 2.0',
    heading_line1: 'One Unified Cloud ERP for',
    heading_line2: 'Modern Educational Institutions.',
    subheading: 'Eliminate paper bottlenecks, automate admissions and fee collections, deliver intelligent report cards, and connect students, teachers, and parents through dedicated self-service portals.',
    cta_register_text: 'Register Your School Free',
    cta_pricing_text: 'View Pricing & Plans',
    cta_directory_text: 'Access School Portals',
  },
  stats: {
    stat1_value: '100%',
    stat1_label: 'Multi-Tenant',
    stat2_value: '15+',
    stat2_label: 'Core Modules',
    stat3_value: '99.98%',
    stat3_label: 'Uptime SLA',
    stat4_value: '0 Days',
    stat4_label: 'Instant Setup',
  },
  architecture: {
    card_title: 'EduCloud Architecture',
    card_badge: 'SaaS Ready',
    card_subtitle: 'Independent Tenant Portals & Data Isolation',
    item1_title: 'Root Platform (/)',
    item1_desc: 'Marketing, plans, school onboarding & platform billing',
    item2_title: 'School Tenant Portals (/s/:code)',
    item2_desc: 'Dedicated website, admissions, notice board & campus info',
    item3_title: 'Student & Parent Portal',
    item3_desc: 'Attendance, dues, fee receipts, report cards & timetables',
    cta_button_text: 'Launch Your Institution in 2 Minutes →',
  }
})

async function fetchPlatformContent() {
  try {
    const res = await axios.get('/api/public/saas/content')
    if (res.data?.success && res.data?.data) {
      if (res.data.data.hero) Object.assign(platformContent.hero, res.data.data.hero)
      if (res.data.data.stats) Object.assign(platformContent.stats, res.data.data.stats)
      if (res.data.data.architecture) Object.assign(platformContent.architecture, res.data.data.architecture)
    }
  } catch (e) {
    // Keep fallback defaults
  }
}

const form = reactive({
  school_name: '',
  school_code: '',
  admin_name: '',
  admin_email: '',
  admin_phone: '',
  city: '',
  state: 'West Bengal',
  plan: 'growth',
  estimated_students: '',
})

const demoForm = reactive({
  institution_name: '',
  contact_person: '',
  email: '',
  phone: '',
  message: '',
})

async function fetchSchools() {
  loadingSchools.value = true
  try {
    const res = await axios.get('/api/public/saas/schools')
    if (res.data.success) {
      schools.value = res.data.data || []
    }
  } catch (e) {
    console.error('Error fetching schools directory', e)
  } finally {
    loadingSchools.value = false
  }
}

async function submitRegistration() {
  registering.value = true
  try {
    const res = await axios.post('/api/public/saas/register-school', form)
    if (res.data.success) {
      registeredSchool.value = res.data.data
      Swal.fire({
        icon: 'success',
        title: 'School Registered!',
        text: `Your school portal URL is: ${res.data.data.portal_url}`,
      })
      fetchSchools()
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Registration Error',
      text: err.response?.data?.message || 'Please check all required fields and try again.',
    })
  } finally {
    registering.value = false
  }
}

async function submitDemo() {
  demoLoading.value = true
  demoSuccess.value = ''
  try {
    const res = await axios.post('/api/public/saas/demo-inquiry', demoForm)
    if (res.data.success) {
      demoSuccess.value = res.data.message
      demoForm.institution_name = ''
      demoForm.contact_person = ''
      demoForm.email = ''
      demoForm.phone = ''
      demoForm.message = ''
      Swal.fire({
        icon: 'success',
        title: 'Request Received',
        text: 'Thank you! Our ERP consultant will reach out to you shortly.',
      })
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Submission Error',
      text: err.response?.data?.message || 'Failed to submit demo request.',
    })
  } finally {
    demoLoading.value = false
  }
}

async function fetchPlans() {
  try {
    const res = await axios.get('/api/public/saas/plans')
    if (res.data?.success && Array.isArray(res.data?.data) && res.data.data.length > 0) {
      plans.value = res.data.data
    }
  } catch (e) {
    // Keep defaults
  }
}

onMounted(() => {
  fetchPlatformContent()
  fetchPlans()
  fetchSchools()
})
</script>

<style scoped>
.transition-hover {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}
</style>
