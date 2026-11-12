<template>
  <div>
    <!-- Page Header -->
    <section class="bg-primary text-white py-5">
      <div class="container py-3">
        <div class="row align-items-center">
          <div class="col-md-8">
            <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill">Admissions 2026-27</span>
            <h1 class="display-5 fw-bold mb-2">Admission Procedure & Guidelines</h1>
            <p class="lead text-white-50 mb-0">Join our vibrant academic community. Easy and transparent admission steps.</p>
          </div>
          <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <router-link :to="schoolLink('/admissions/apply')" class="btn btn-warning rounded-pill px-4 fw-bold">
              <i class="bi bi-pencil-square me-1"></i> Apply Online Now
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Admission Process Steps -->
    <section class="py-5 bg-white">
      <div class="container py-4">
        <div class="text-center mb-5">
          <span class="text-primary fw-bold text-uppercase small">Simple 4-Step Process</span>
          <h2 class="fw-bold mt-1">How To Apply</h2>
        </div>

        <div class="row g-4">
          <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 position-relative">
              <div class="badge bg-primary rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center fs-4" style="width: 54px; height: 54px;">
                1
              </div>
              <h5 class="fw-bold mb-2">Online Application</h5>
              <p class="small text-muted mb-0">Fill out our online application form with student bio, class, and parent contact details.</p>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 position-relative">
              <div class="badge bg-info text-white rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center fs-4" style="width: 54px; height: 54px;">
                2
              </div>
              <h5 class="fw-bold mb-2">Document Verification</h5>
              <p class="small text-muted mb-0">Our admissions team verifies uploaded details and schedules an interactive session.</p>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 position-relative">
              <div class="badge bg-warning text-dark rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center fs-4" style="width: 54px; height: 54px;">
                3
              </div>
              <h5 class="fw-bold mb-2">Interaction / Test</h5>
              <p class="small text-muted mb-0">A friendly interaction for primary grades or an aptitude evaluation for higher classes.</p>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 position-relative">
              <div class="badge bg-success rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center fs-4" style="width: 54px; height: 54px;">
                4
              </div>
              <h5 class="fw-bold mb-2">Enrollment & Portal</h5>
              <p class="small text-muted mb-0">Pay admission fees, receive official Admission Number, and access the Parent Portal.</p>
            </div>
          </div>
        </div>

        <!-- Required Documents Checklist -->
        <div class="row mt-5 g-5 align-items-center">
          <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
              <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-file-earmark-check me-2"></i> Documents Required for Admission</h5>
              <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small text-muted">
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6 mt-1"></i>
                  <div><strong>Birth Certificate:</strong> Municipal Corporation / Hospital verified copy.</div>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6 mt-1"></i>
                  <div><strong>Passport-size Photographs:</strong> 4 colored photos of student and 2 each of parents.</div>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6 mt-1"></i>
                  <div><strong>Transfer Certificate (TC):</strong> Original counter-signed TC from previous school (Class 1 onwards).</div>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6 mt-1"></i>
                  <div><strong>Previous Report Card:</strong> Copy of marksheet/report card from the previous academic year.</div>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success fs-6 mt-1"></i>
                  <div><strong>Proof of Residence & ID:</strong> Aadhaar Card / Passport / Utility Bill copy.</div>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="p-3">
              <h4 class="fw-bold mb-3">Ready to Begin?</h4>
              <p class="text-muted mb-4">
                You can complete your application 100% online in less than 5 minutes. You will receive an instant application tracking ID.
              </p>
              <div class="d-flex flex-column flex-sm-row gap-3">
                <router-link :to="schoolLink('/admissions/apply')" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow">
                  <i class="bi bi-pencil-square me-2"></i> Fill Online Form
                </router-link>
                <router-link :to="schoolLink('/admissions/track')" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                  <i class="bi bi-search me-2"></i> Track Application
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { inject } from 'vue'

const schoolLink = inject('schoolLink', (p) => p)
</script>
