<template>
  <div>
    <!-- Page Header -->
    <section class="bg-primary text-white py-5">
      <div class="container py-3">
        <div class="row align-items-center">
          <div class="col-md-8">
            <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill">{{ about.badge || 'About Our Institution' }}</span>
            <h1 class="display-5 fw-bold mb-2">{{ about.heading || 'History & Mission' }}</h1>
            <p class="lead text-white-50 mb-0">{{ about.subheading || 'Nurturing potential and cultivating leadership for over two decades.' }}</p>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <router-link :to="schoolLink('/admissions/apply')" class="btn btn-warning rounded-pill px-4 fw-bold">
              Join {{ general.school_name || 'Our School' }} <i class="bi bi-arrow-right ms-1"></i>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Vision & Mission -->
    <section class="py-5 bg-white">
      <div class="container py-4">
        <div class="row g-5 align-items-center">
          <div class="col-lg-6">
            <div class="pe-lg-4">
              <span class="text-primary fw-bold text-uppercase small">Our Foundation</span>
              <h2 class="fw-bold mt-1 mb-4">{{ about.heading || 'Dedicated to Inspiring Young Minds' }}</h2>
              <p class="text-muted leading-relaxed mb-3">
                {{ about.paragraph1 || 'Founded with a visionary commitment to provide accessible, high-caliber English-medium education, St. George English Day School has established itself as an institution of repute in Kolkata.' }}
              </p>
              <p v-if="about.paragraph2" class="text-muted leading-relaxed mb-4">
                {{ about.paragraph2 }}
              </p>
              <div class="card border-0 bg-light p-4 rounded-4 mb-4 border-start border-4 border-primary">
                <h5 class="fw-bold text-primary mb-2"><i class="bi bi-compass me-2"></i> Our Vision</h5>
                <p class="small text-muted mb-0">
                  {{ about.vision || 'To be a premier learning center that inspires intellectual rigor, ethical values, emotional strength, and social empathy in every student to thrive in a globalized society.' }}
                </p>
              </div>
              <div class="card border-0 bg-light p-4 rounded-4 border-start border-4 border-success">
                <h5 class="fw-bold text-success mb-2"><i class="bi bi-bullseye me-2"></i> Our Mission</h5>
                <p class="small text-muted mb-0">
                  {{ about.mission || 'To empower each student with critical thinking capabilities, technological proficiency, physical fitness, and moral integrity through compassionate, research-backed pedagogy.' }}
                </p>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <!-- Principal Message Box -->
            <div class="card border-0 shadow-lg rounded-4 p-4 bg-white position-relative">
              <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                <div class="bg-primary text-white rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                  <i class="bi bi-person-fill fs-2"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-0">{{ principal.title || "Principal's Address" }}</h5>
                  <small class="text-muted">{{ general.school_name || 'St. George English Day School' }}</small>
                </div>
              </div>
              <blockquote class="blockquote text-muted fs-6 mb-4 fst-italic">
                "{{ principal.quote || 'Education is not merely about accumulating facts; it is about kindling an insatiable appetite for learning, shaping compassionate character, and instilling courage to lead with responsibility.' }}"
              </blockquote>
              <div class="d-flex justify-content-between align-items-center small text-muted">
                <span><strong>{{ principal.name || 'Fr. David Reynolds' }}</strong><br><small>{{ principal.designation || 'Head of School' }}</small></span>
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">{{ principal.experience || '25+ Years Leadership' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Core Values -->
    <section class="py-5 bg-light">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small">The Pillars We Stand On</span>
          <h2 class="fw-bold mt-1">Our Core Values</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-3 col-sm-6 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="bg-primary-subtle text-primary rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-gem fs-3"></i>
              </div>
              <h5 class="fw-bold mb-2">Excellence</h5>
              <p class="small text-muted mb-0">Striving constantly for highest standards in academics, arts, and character.</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="bg-success-subtle text-success rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-heart fs-3"></i>
              </div>
              <h5 class="fw-bold mb-2">Compassion</h5>
              <p class="small text-muted mb-0">Treating everyone with empathy, kindness, and deep respect for diversity.</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="bg-warning-subtle text-warning-emphasis rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-shield-check fs-3"></i>
              </div>
              <h5 class="fw-bold mb-2">Integrity</h5>
              <p class="small text-muted mb-0">Upholding honesty, ethical responsibility, and transparency in every action.</p>
            </div>
          </div>
          <div class="col-md-3 col-sm-6 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
              <div class="bg-info-subtle text-info-emphasis rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="bi bi-lightbulb fs-3"></i>
              </div>
              <h5 class="fw-bold mb-2">Innovation</h5>
              <p class="small text-muted mb-0">Embracing progressive teaching methods, technology, and creative inquiry.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { inject, computed } from 'vue'

const websiteData = inject('websiteData', { content: {} })
const schoolLink = inject('schoolLink', (p) => p)

const about = computed(() => websiteData.content?.about || {})
const principal = computed(() => websiteData.content?.principal || {})
const general = computed(() => websiteData.content?.general || {})
</script>
