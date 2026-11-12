<template>
  <div>
    <!-- Hero Section -->
    <section class="hero-section bg-gradient py-5 text-white position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #0a48a3 100%);">
      <div class="container py-5">
        <div class="row align-items-center g-5">
          <div class="col-lg-7">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-3">
              <i class="bi bi-stars me-1"></i> {{ hero.badge_text || 'Admissions Open for Academic Session 2026-27' }}
            </span>
            <h1 class="display-4 fw-bold lh-sm mb-3">
              {{ hero.heading_line1 || 'Empowering Minds,' }} <br>
              <span class="text-warning">{{ hero.heading_line2 || 'Shaping Futures.' }}</span>
            </h1>
            <p class="lead mb-4 text-white-50">
              {{ hero.subheading || 'Welcome to St. George English Day School. We deliver a blend of academic rigor, modern technological integration, and ethical leadership to prepare your child for tomorrow\'s world.' }}
            </p>
            <div class="d-flex flex-wrap gap-3">
              <router-link :to="schoolLink(hero.cta_primary_link || '/admissions/apply')" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold shadow">
                <i class="bi bi-pencil-square me-2"></i> {{ hero.cta_primary_text || 'Apply for Admission' }}
              </router-link>
              <router-link :to="hero.cta_secondary_link || '/portal/login'" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-semibold">
                <i class="bi bi-person-circle me-2"></i> {{ hero.cta_secondary_text || 'Student / Parent Portal' }}
              </router-link>
              <router-link :to="schoolLink('/about')" class="btn btn-link text-white text-decoration-none px-3 d-flex align-items-center">
                Explore School <i class="bi bi-arrow-right ms-2"></i>
              </router-link>
            </div>

            <!-- Quick Trust Badges -->
            <div class="row mt-5 pt-3 border-top border-primary-subtle text-white g-3">
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ stats.stat1_number || '450+' }}</h3>
                <small class="text-white-50">{{ stats.stat1_label || 'Enrolled Students' }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ stats.stat2_number || '32+' }}</h3>
                <small class="text-white-50">{{ stats.stat2_label || 'Expert Educators' }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ stats.stat3_number || '100%' }}</h3>
                <small class="text-white-50">{{ stats.stat3_label || 'Board Pass Rate' }}</small>
              </div>
              <div class="col-3">
                <h3 class="fw-bold mb-0">{{ stats.stat4_number || '25+' }}</h3>
                <small class="text-white-50">{{ stats.stat4_label || 'Years Legacy' }}</small>
              </div>
            </div>
          </div>

          <div class="col-lg-5">
            <!-- Admission Card Quick Action -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-dark bg-white">
              <div class="card-header bg-primary text-white p-4 border-0">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i> Online Admission</h5>
                  <span class="badge bg-light text-primary">Simple 3 Steps</span>
                </div>
                <small class="text-white-50 d-block mt-1">Submit your application online in just 5 minutes</small>
              </div>
              <div class="card-body p-4">
                <ul class="list-unstyled mb-4">
                  <li class="d-flex gap-3 mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</span>
                    <div>
                      <strong class="d-block">Fill Student & Parent Details</strong>
                      <small class="text-muted">Enter basic bio, class applying for, and contact details.</small>
                    </div>
                  </li>
                  <li class="d-flex gap-3 mb-3">
                    <span class="badge bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</span>
                    <div>
                      <strong class="d-block">Instant Application Tracking No.</strong>
                      <small class="text-muted">Get your instant reference code like ADM-2026-XXXX.</small>
                    </div>
                  </li>
                  <li class="d-flex gap-3">
                    <span class="badge bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</span>
                    <div>
                      <strong class="d-block">Counselor Review & Enrollment</strong>
                      <small class="text-muted">Our academic counselors review and notify you immediately.</small>
                    </div>
                  </li>
                </ul>

                <router-link to="/admissions/apply" class="btn btn-primary w-100 py-2 rounded-pill fw-bold mb-2">
                  <i class="bi bi-arrow-right-circle me-1"></i> Start Application Now
                </router-link>
                <router-link to="/admissions/track" class="btn btn-outline-secondary w-100 py-2 rounded-pill small">
                  <i class="bi bi-search me-1"></i> Track Existing Application
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Live Notice Board Ticker / Alert -->
    <div v-if="notices.length > 0" class="bg-warning-subtle border-bottom py-2">
      <div class="container d-flex align-items-center">
        <span class="badge bg-danger me-3 text-uppercase fw-bold px-2 py-1">
          <i class="bi bi-megaphone-fill me-1"></i> Notices
        </span>
        <div class="text-truncate flex-grow-1 small">
          <span v-for="(n, idx) in notices" :key="n.id" class="me-4">
            <strong class="text-dark">{{ n.title }}</strong>: <span class="text-muted">{{ n.content }}</span>
            <span v-if="idx < notices.length - 1" class="text-muted ms-3">&bull;</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Why Choose Us -->
    <section class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small tracking-wide">Why Choose St. George</span>
          <h2 class="fw-bold mt-1">Holistic Education For Global Leaders</h2>
          <p class="text-muted mx-auto" style="max-width: 650px;">
            We combine high academic standards with moral values, co-curricular sports, and modern digital classrooms.
          </p>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center transition-hover">
              <div class="card-body">
                <div class="bg-primary-subtle text-primary rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                  <i class="bi bi-award fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Academic Rigor</h5>
                <p class="text-muted small mb-0">
                  Comprehensive curriculum aligned with national boards focusing on deep concept clarity and analytical thinking.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center transition-hover">
              <div class="card-body">
                <div class="bg-success-subtle text-success rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                  <i class="bi bi-laptop fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Digital Classrooms</h5>
                <p class="text-muted small mb-0">
                  Interactive smartboards, computer lab, coding clubs, and complete ERP tracking for parents and students.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center transition-hover">
              <div class="card-body">
                <div class="bg-warning-subtle text-warning-emphasis rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                  <i class="bi bi-shield-check fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Safe & Caring Campus</h5>
                <p class="text-muted small mb-0">
                  CCTV monitored campus, dedicated school transport fleet, comfortable hostel, and continuous medical support.
                </p>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3 text-center transition-hover">
              <div class="card-body">
                <div class="bg-info-subtle text-info-emphasis rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                  <i class="bi bi-trophy fs-3"></i>
                </div>
                <h5 class="fw-bold mb-2">Sports & Arts</h5>
                <p class="text-muted small mb-0">
                  Cricket, football, martial arts, debates, music, and annual cultural festivals nurturing all-around personalities.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Academic Programs -->
    <section class="py-5 bg-white">
      <div class="container py-4">
        <div class="row align-items-center mb-5">
          <div class="col-lg-8">
            <span class="text-primary fw-bold text-uppercase small">Structured Learning</span>
            <h2 class="fw-bold mt-1">Our Academic Wings</h2>
          </div>
          <div class="col-lg-4 text-lg-end">
            <router-link to="/academics" class="btn btn-outline-primary rounded-pill px-4">
              Explore All Curriculums <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card border rounded-4 overflow-hidden h-100 shadow-sm">
              <div class="bg-primary text-white p-4 text-center">
                <i class="bi bi-balloon-heart fs-1"></i>
                <h5 class="fw-bold mt-2 mb-0">Pre-Primary</h5>
                <small class="text-white-50">Nursery, LKG & UKG</small>
              </div>
              <div class="card-body p-4">
                <p class="small text-muted">
                  Play-based experiential learning fostering curiosity, early literacy, numeracy, and fine motor skills in an affectionate setting.
                </p>
                <ul class="list-unstyled small mb-0 text-muted">
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Sensory Play Activities</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Phonics & Storytelling</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Social Interaction</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card border rounded-4 overflow-hidden h-100 shadow-sm">
              <div class="bg-success text-white p-4 text-center">
                <i class="bi bi-book-half fs-1"></i>
                <h5 class="fw-bold mt-2 mb-0">Primary Wing</h5>
                <small class="text-white-50">Class 1 to Class 5</small>
              </div>
              <div class="card-body p-4">
                <p class="small text-muted">
                  Strengthening fundamental core competencies in mathematics, languages, environmental studies, and computer basics.
                </p>
                <ul class="list-unstyled small mb-0 text-muted">
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Activity-Based Learning</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Bilingual Fluency</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Creative Arts & Crafts</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card border rounded-4 overflow-hidden h-100 shadow-sm">
              <div class="bg-warning text-dark p-4 text-center">
                <i class="bi bi-compass fs-1"></i>
                <h5 class="fw-bold mt-2 mb-0">Middle School</h5>
                <small class="text-dark-50">Class 6 to Class 8</small>
              </div>
              <div class="card-body p-4">
                <p class="small text-muted">
                  Transitioning into scientific inquiries, problem solving, advanced languages, and structured lab experiments.
                </p>
                <ul class="list-unstyled small mb-0 text-muted">
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Science Lab Practicals</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Project Investigations</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Debating & Public Speaking</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-lg-3">
            <div class="card border rounded-4 overflow-hidden h-100 shadow-sm">
              <div class="bg-info text-dark p-4 text-center">
                <i class="bi bi-mortarboard fs-1"></i>
                <h5 class="fw-bold mt-2 mb-0">Secondary Wing</h5>
                <small class="text-dark-50">Class 9 to Class 12</small>
              </div>
              <div class="card-body p-4">
                <p class="small text-muted">
                  Targeted academic excellence for board examinations and competitive entrance foundation across Science, Commerce & Arts.
                </p>
                <ul class="list-unstyled small mb-0 text-muted">
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Regular Assessment Series</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> Career Guidance Cell</li>
                  <li><i class="bi bi-check-circle-fill text-success me-1"></i> College Prep Seminars</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Campus Facilities -->
    <section class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small">Infrastructure</span>
          <h2 class="fw-bold mt-1">World-Class Facilities</h2>
          <p class="text-muted mx-auto" style="max-width: 600px;">
            A safe, vibrant, and well-equipped environment where students excel academically and personally.
          </p>
        </div>

        <div class="row g-4">
          <div v-for="(fac, idx) in facilities" :key="idx" class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
              <div :class="`bg-${fac.color || 'primary'} text-white p-4 d-flex align-items-center gap-3`">
                <i :class="`bi ${fac.icon || 'bi-stars'} fs-2`"></i>
                <h5 class="fw-bold mb-0">{{ fac.title }}</h5>
              </div>
              <div class="card-body p-4">
                <p class="text-muted small mb-0">{{ fac.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Admission CTA Banner -->
    <section class="py-5 text-white text-center" style="background: linear-gradient(135deg, #0a48a3 0%, #0d6efd 100%);">
      <div class="container py-4">
        <h2 class="display-6 fw-bold mb-3">Begin Your Child's Journey With Us</h2>
        <p class="lead mb-4 text-white-50 mx-auto" style="max-width: 650px;">
          Seats for the upcoming academic session are filling up. Apply online today to secure your child's placement.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
          <router-link :to="schoolLink('/admissions/apply')" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">
            <i class="bi bi-pencil-square me-2"></i> Apply for Admission Now
          </router-link>
          <router-link :to="schoolLink('/contact')" class="btn btn-outline-light btn-lg rounded-pill px-4">
            <i class="bi bi-chat-dots me-2"></i> Enquire or Visit Campus
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { inject, computed } from 'vue'

const websiteData = inject('websiteData', { content: {}, notices: [] })
const schoolLink = inject('schoolLink', (p) => p)

const hero = computed(() => websiteData.content?.hero || {})
const stats = computed(() => websiteData.content?.stats || {})
const notices = computed(() => websiteData.notices || [])

const defaultFacilities = [
  { title: 'Rich Library', icon: 'bi-book', color: 'primary', description: 'Over 10,000+ reference books, journals, and quiet reading spaces managed digitally.' },
  { title: 'Hi-Tech Labs', icon: 'bi-pc-display', color: 'success', description: 'Modern computer laboratories, physics, chemistry, and biology labs with modern apparatus.' },
  { title: 'Transport & Safety', icon: 'bi-bus-front', color: 'danger', description: 'Extensive GPS-tracked bus fleet covering all city routes with trained drivers.' },
  { title: 'Residential Hostel', icon: 'bi-building', color: 'info', description: 'Safe, hygienic residential dormitories with 24/7 security and nutritious dining.' },
  { title: 'Sports Complex', icon: 'bi-dribbble', color: 'warning', description: 'Dedicated grounds for football, basketball, indoor badminton, and table tennis.' },
  { title: 'Parent ERP Portal', icon: 'bi-shield-lock', color: 'secondary', description: 'Direct mobile & web portal access for parents to view attendance, results, and fee dues.' },
]

const facilities = computed(() => {
  return websiteData.content?.facilities?.items && websiteData.content.facilities.items.length > 0
    ? websiteData.content.facilities.items
    : defaultFacilities
})
</script>

<style scoped>
.transition-hover {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-hover:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}
</style>
