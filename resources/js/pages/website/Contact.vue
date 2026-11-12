<template>
  <div>
    <!-- Page Header -->
    <section class="bg-primary text-white py-5">
      <div class="container py-3">
        <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill">Get In Touch</span>
        <h1 class="display-5 fw-bold mb-2">Contact & Campus Visit</h1>
        <p class="lead text-white-50 mb-0">We are delighted to assist prospective students, parents, and visitors.</p>
      </div>
    </section>

    <!-- Contact & Form Section -->
    <section class="py-5 bg-white">
      <div class="container py-4">
        <div class="row g-5">
          <!-- Contact Info -->
          <div class="col-lg-5">
            <h4 class="fw-bold mb-4">Campus Location & Office</h4>

            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="bg-primary text-white rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-geo-alt-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Campus Address</h6>
                <p class="text-muted small mb-0">{{ general.address || 'Campus Location' }}, {{ general.city || 'Kolkata' }}, {{ general.state || 'West Bengal' }} {{ general.pincode || '' }}</p>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="bg-success text-white rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-telephone-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Telephone & Helpdesk</h6>
                <p class="text-muted small mb-0">{{ general.phone_primary || '+91 033 5562 9768' }} {{ general.phone_secondary ? '/ ' + general.phone_secondary : '' }}</p>
                <small class="text-muted">Office Hours: {{ general.office_hours || '8:00 AM – 3:30 PM (Mon – Sat)' }}</small>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="bg-warning text-dark rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-envelope-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Email Communication</h6>
                <p class="text-muted small mb-0">General: {{ general.email_primary || 'info@school.edu' }}</p>
                <p class="text-muted small mb-0">Admissions: {{ general.email_admissions || 'admissions@school.edu' }}</p>
              </div>
            </div>

            <!-- Quick Map Card -->
            <div class="card border-0 bg-light rounded-4 p-4 mt-4">
              <h6 class="fw-bold mb-2"><i class="bi bi-map me-2 text-primary"></i> Visiting Our Campus</h6>
              <p class="small text-muted mb-0">
                Parents are warmly invited for guided campus tours on weekdays between 9:00 AM and 1:00 PM. No prior booking required for walk-in inquiries.
              </p>
            </div>
          </div>

          <!-- Inquiry Form -->
          <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
              <h4 class="fw-bold mb-2">Send Us an Inquiry</h4>
              <p class="text-muted small mb-4">Have questions about admissions, curriculum, transport, or fee structures? Drop us a message.</p>

              <div v-if="successMsg" class="alert alert-success border-0 rounded-3 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ successMsg }}
              </div>

              <form @submit.prevent="submitForm">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Your Full Name <span class="text-danger">*</span></label>
                    <input type="text" v-model="form.name" class="form-control" placeholder="Parent or Student Name" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" v-model="form.email" class="form-control" placeholder="name@example.com" required>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Contact Mobile</label>
                    <input type="tel" v-model="form.phone" class="form-control" placeholder="10-digit mobile number">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Subject</label>
                    <input type="text" v-model="form.subject" class="form-control" placeholder="e.g. Admission for Class 6">
                  </div>

                  <div class="col-12">
                    <label class="form-label fw-semibold">Message / Questions <span class="text-danger">*</span></label>
                    <textarea v-model="form.message" class="form-control" rows="4" placeholder="How can we help you?" required></textarea>
                  </div>

                  <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow" :disabled="loading">
                      <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                      {{ loading ? 'Sending...' : 'Send Message' }}
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
import { reactive, ref, inject, computed } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const websiteData = inject('websiteData', { content: {}, school: null })
const general = computed(() => websiteData.content?.general || {})

const loading = ref(false)
const successMsg = ref('')

const form = reactive({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
})

async function submitForm() {
  loading.value = true
  successMsg.value = ''
  try {
    const payload = {
      ...form,
      school: route.params.schoolCode || route.query.school || websiteData.school?.code || ''
    }
    const res = await axios.post('/api/public/contact-inquiries', payload)
    if (res.data.success) {
      successMsg.value = res.data.message
      form.name = ''
      form.email = ''
      form.phone = ''
      form.subject = ''
      form.message = ''
      Swal.fire({
        icon: 'success',
        title: 'Message Sent!',
        text: res.data.message || 'Thank you for your inquiry. Our team will contact you shortly.',
      })
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Failed to send message',
      text: err.response?.data?.message || 'Please check your inputs and try again.',
    })
  } finally {
    loading.value = false
  }
}
</script>
