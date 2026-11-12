<template>
  <div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="fw-bold mb-1">
          <i class="bi bi-globe-americas me-2 text-primary"></i> Root Platform Website CMS
        </h4>
        <small class="text-muted">
          Manage the public marketing landing page (<strong>http://127.0.0.1:8000/</strong>) seen by visitors and prospective client schools
        </small>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="/" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm">
          <i class="bi bi-box-arrow-up-right me-1"></i> View Live Root Website ( / )
        </a>
      </div>
    </div>

    <!-- Main CMS Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <!-- Tabs Navigation -->
      <div class="card-header bg-white p-3 border-bottom">
        <ul class="nav nav-pills card-header-pills gap-1 flex-wrap">
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'">
              <i class="bi bi-gear-fill me-1"></i> Brand & Contact
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'hero' }" @click="activeTab = 'hero'">
              <i class="bi bi-card-heading me-1"></i> Hero Section
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'stats' }" @click="activeTab = 'stats'">
              <i class="bi bi-bar-chart-fill me-1"></i> Key Metrics & SLA
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'architecture' }" @click="activeTab = 'architecture'">
              <i class="bi bi-diagram-3-fill me-1"></i> Architecture Highlights
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'plans' }" @click="activeTab = 'plans'">
              <i class="bi bi-tag-fill me-1"></i> Subscription Plans
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-4 p-md-5">
        <div v-if="loading" class="text-center py-5">
          <span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading root platform content...
        </div>

        <div v-else>
          <!-- TAB 1: Brand & Contact -->
          <div v-if="activeTab === 'general'">
            <h5 class="fw-bold text-primary mb-3">Platform Identity & Contact Channels</h5>
            <p class="small text-muted mb-4">Configures the SaaS platform branding, navbar identity, top bar, and footer bio on the root website.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">SaaS Platform Name</label>
                <input type="text" v-model="settings.general.brand_name" class="form-control" placeholder="EduCloud ERP">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Platform Tagline</label>
                <input type="text" v-model="settings.general.brand_tagline" class="form-control" placeholder="Next-Gen School Operating System">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Sales / Support Email</label>
                <input type="email" v-model="settings.general.support_email" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Security / Compliance Badge Text</label>
                <input type="text" v-model="settings.general.security_badge" class="form-control" placeholder="ISO 27001 Certified • Bank-Grade Cloud Security">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Platform Bio (Footer Description)</label>
                <textarea v-model="settings.general.footer_about" class="form-control" rows="3"></textarea>
              </div>

              <!-- Social Links -->
              <div class="col-12"><hr class="my-3"></div>
              <h6 class="fw-bold mb-2">Social Profiles</h6>
              <div class="col-md-4">
                <label class="form-label small text-muted"><i class="bi bi-linkedin me-1"></i> LinkedIn URL</label>
                <input type="text" v-model="settings.general.social_linkedin" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted"><i class="bi bi-twitter-x me-1"></i> X / Twitter URL</label>
                <input type="text" v-model="settings.general.social_twitter" class="form-control form-control-sm">
              </div>
              <div class="col-md-4">
                <label class="form-label small text-muted"><i class="bi bi-github me-1"></i> GitHub URL</label>
                <input type="text" v-model="settings.general.social_github" class="form-control form-control-sm">
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('general')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('general')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Platform Brand
              </button>
            </div>
          </div>

          <!-- TAB 2: Hero Section -->
          <div v-if="activeTab === 'hero'">
            <h5 class="fw-bold text-primary mb-3">Homepage Hero Section</h5>
            <p class="small text-muted mb-4">Edit the prominent headlines, welcome description, and primary call-to-action buttons on the root domain.</p>

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold">Top Pill Badge Text</label>
                <input type="text" v-model="settings.hero.badge" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Main Heading Line 1</label>
                <input type="text" v-model="settings.hero.heading_line1" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Highlighted Heading Line 2 (Yellow accent)</label>
                <input type="text" v-model="settings.hero.heading_line2" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Lead Subheading / Value Proposition</label>
                <textarea v-model="settings.hero.subheading" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Primary CTA Button Label</label>
                <input type="text" v-model="settings.hero.cta_register_text" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Secondary CTA Button Label</label>
                <input type="text" v-model="settings.hero.cta_pricing_text" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Directory CTA Link Label</label>
                <input type="text" v-model="settings.hero.cta_directory_text" class="form-control">
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('hero')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('hero')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Hero Section
              </button>
            </div>
          </div>

          <!-- TAB 3: Metrics & SLA -->
          <div v-if="activeTab === 'stats'">
            <h5 class="fw-bold text-primary mb-3">Platform Credibility Metrics & SLA</h5>
            <p class="small text-muted mb-4">Edit the 4 highlight stats shown right under the hero headline.</p>

            <div class="row g-4">
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Stat 1</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Value</label>
                    <input type="text" v-model="settings.stats.stat1_value" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat1_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Stat 2</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Value</label>
                    <input type="text" v-model="settings.stats.stat2_value" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat2_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Stat 3</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Value</label>
                    <input type="text" v-model="settings.stats.stat3_value" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat3_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Stat 4</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Value</label>
                    <input type="text" v-model="settings.stats.stat4_value" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat4_label" class="form-control">
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('stats')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('stats')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Metrics
              </button>
            </div>
          </div>

          <!-- TAB 4: Architecture Highlights Card -->
          <div v-if="activeTab === 'architecture'">
            <h5 class="fw-bold text-primary mb-3">Hero Architecture Card</h5>
            <p class="small text-muted mb-4">Edit the contents of the interactive multi-tenant architecture preview card on the right side of the hero section.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Card Title</label>
                <input type="text" v-model="settings.architecture.card_title" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Card Badge</label>
                <input type="text" v-model="settings.architecture.card_badge" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Card Subtitle</label>
                <input type="text" v-model="settings.architecture.card_subtitle" class="form-control">
              </div>

              <div class="col-12"><hr class="my-2"></div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 1 Title</label>
                <input type="text" v-model="settings.architecture.item1_title" class="form-control form-control-sm">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 1 Description</label>
                <input type="text" v-model="settings.architecture.item1_desc" class="form-control form-control-sm">
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 2 Title</label>
                <input type="text" v-model="settings.architecture.item2_title" class="form-control form-control-sm">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 2 Description</label>
                <input type="text" v-model="settings.architecture.item2_desc" class="form-control form-control-sm">
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 3 Title</label>
                <input type="text" v-model="settings.architecture.item3_title" class="form-control form-control-sm">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold">Item 3 Description</label>
                <input type="text" v-model="settings.architecture.item3_desc" class="form-control form-control-sm">
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Action Button Text</label>
                <input type="text" v-model="settings.architecture.cta_button_text" class="form-control">
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('architecture')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('architecture')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Architecture
              </button>
            </div>
          </div>

          <!-- TAB 5: Subscription Plans -->
          <div v-if="activeTab === 'plans'">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="fw-bold text-primary mb-1">School ERP Subscription Plans & Pricing</h5>
                <p class="small text-muted mb-0">Customize pricing tiers, student enrollment caps, and bullet-point feature lists shown on the root website.</p>
              </div>
            </div>

            <div class="row g-4 mt-1">
              <div v-for="(plan, idx) in settings.plans" :key="plan.id || idx" class="col-lg-4">
                <div class="card h-100 rounded-4 border p-3 shadow-sm bg-light position-relative">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase">{{ plan.id }}</span>
                    <div class="form-check form-switch m-0">
                      <input class="form-check-input" type="checkbox" v-model="plan.popular" :id="'pop-' + idx">
                      <label class="form-check-label small" :for="'pop-' + idx">Most Popular</label>
                    </div>
                  </div>

                  <div class="mb-2">
                    <label class="form-label small fw-semibold">Plan Name</label>
                    <input type="text" v-model="plan.name" class="form-control form-control-sm">
                  </div>

                  <div class="mb-2">
                    <label class="form-label small fw-semibold">Audience Badge</label>
                    <input type="text" v-model="plan.badge" class="form-control form-control-sm" placeholder="e.g. Small Institutions">
                  </div>

                  <div class="row g-2 mb-2">
                    <div class="col-6">
                      <label class="form-label small fw-semibold">Monthly Price (₹)</label>
                      <input type="number" v-model.number="plan.price_monthly" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                      <label class="form-label small fw-semibold">Annual Price (₹)</label>
                      <input type="number" v-model.number="plan.price_annual" class="form-control form-control-sm">
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold">Student Limit</label>
                    <input type="text" v-model="plan.student_limit" class="form-control form-control-sm" placeholder="e.g. 300 or Unlimited">
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-2 pt-2 border-top">
                    <label class="form-label small fw-bold mb-0">Included Features</label>
                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" @click="addFeature(idx)">
                      <i class="bi bi-plus me-1"></i> Add Feature
                    </button>
                  </div>

                  <div class="d-flex flex-column gap-2 mb-2">
                    <div v-for="(feat, fIdx) in plan.features" :key="fIdx" class="input-group input-group-sm">
                      <input type="text" v-model="plan.features[fIdx]" class="form-control">
                      <button class="btn btn-outline-danger" type="button" @click="removeFeature(idx, fIdx)">
                        &times;
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('plans')">Reset to Defaults</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('plans')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Subscription Plans
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/api/axios'
import Swal from 'sweetalert2'

const activeTab = ref('general')
const loading = ref(true)
const saving = ref(false)

const settings = reactive({
  general: {},
  hero: {},
  stats: {},
  architecture: {},
  plans: [],
})

function addFeature(planIdx) {
  if (!settings.plans[planIdx].features) settings.plans[planIdx].features = []
  settings.plans[planIdx].features.push('New Feature')
}

function removeFeature(planIdx, featIdx) {
  settings.plans[planIdx].features.splice(featIdx, 1)
}

async function loadContent() {
  loading.value = true
  try {
    const res = await api.get('/admin/saas/content')
    if (res.data.success) {
      Object.assign(settings, res.data.data)
    }
  } catch (err) {
    console.error('Failed to load root platform settings', err)
  } finally {
    loading.value = false
  }
}

async function saveSection(sectionKey) {
  saving.value = true
  try {
    const res = await api.post(`/admin/saas/content/${sectionKey}`, {
      content: settings[sectionKey]
    })
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Saved!',
        text: `Root ${sectionKey.toUpperCase()} section updated. Live home page updated immediately.`,
        timer: 1500,
        showConfirmButton: false,
      })
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.response?.data?.message || 'Failed to save section.',
    })
  } finally {
    saving.value = false
  }
}

async function resetSection(sectionKey) {
  const result = await Swal.fire({
    title: 'Reset to Defaults?',
    text: `Are you sure you want to reset the root ${sectionKey} section to default content?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, reset',
  })

  if (result.isConfirmed) {
    try {
      const res = await api.post(`/admin/saas/content/${sectionKey}/reset`, {})
      if (res.data.success) {
        settings[sectionKey] = res.data.data
        Swal.fire({ icon: 'success', title: 'Reset Complete', timer: 1200, showConfirmButton: false })
      }
    } catch (err) {
      console.error(err)
    }
  }
}

onMounted(() => {
  loadContent()
})
</script>
