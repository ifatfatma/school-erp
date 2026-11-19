<template>
  <div class="container-fluid py-4">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
      <div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-1 small">
            <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">AI Bot & WhatsApp Support</li>
          </ol>
        </nav>
        <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-robot text-primary"></i>
          <span>AI Support Bot & WhatsApp Desk</span>
        </h3>
        <p class="text-muted small mb-0">
          Manage 24/7 rule-based knowledge base, WhatsApp escalation channels, and student/parent inquiry analytics.
        </p>
      </div>

      <div class="d-flex gap-2">
        <button
          type="button"
          class="btn btn-outline-success rounded-pill fw-semibold shadow-xs d-flex align-items-center gap-1"
          @click="testWhatsAppChat"
        >
          <i class="bi bi-whatsapp"></i> Test WhatsApp Link
        </button>
        <button
          type="button"
          class="btn btn-primary rounded-pill fw-semibold shadow-xs d-flex align-items-center gap-1"
          @click="openAddFaqModal"
        >
          <i class="bi bi-plus-circle me-1"></i> Add Knowledge FAQ
        </button>
      </div>
    </div>

    <!-- Analytics KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="text-muted small fw-semibold text-uppercase">Total Inquiries</span>
              <h3 class="fw-bold text-dark my-1">{{ stats.total_queries || 0 }}</h3>
              <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>24/7 Automated</small>
            </div>
            <div class="bg-primary-subtle text-primary rounded-circle p-3">
              <i class="bi bi-chat-dots-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="text-muted small fw-semibold text-uppercase">WhatsApp Connects</span>
              <h3 class="fw-bold text-success my-1">{{ stats.whatsapp_escalations || 0 }}</h3>
              <small class="text-muted">Human Handover</small>
            </div>
            <div class="bg-success-subtle text-success rounded-circle p-3">
              <i class="bi bi-whatsapp fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="text-muted small fw-semibold text-uppercase">Knowledge FAQs</span>
              <h3 class="fw-bold text-info my-1">{{ faqs.length || 0 }}</h3>
              <small class="text-muted">Active Rules</small>
            </div>
            <div class="bg-info-subtle text-info rounded-circle p-3">
              <i class="bi bi-book-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="text-muted small fw-semibold text-uppercase">Helpful Rate</span>
              <h3 class="fw-bold text-dark my-1">{{ helpfulRate }}%</h3>
              <small class="text-success"><i class="bi bi-hand-thumbs-up-fill me-1"></i>{{ stats.helpful_count || 0 }} Positive</small>
            </div>
            <div class="bg-warning-subtle text-warning rounded-circle p-3">
              <i class="bi bi-star-fill fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
      <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs border-0 px-3 pt-2">
          <li class="nav-item">
            <button
              class="nav-link fw-semibold border-0 py-3 px-4 d-flex align-items-center gap-2"
              :class="{ 'active text-primary border-bottom border-primary border-3': activeTab === 'faqs' }"
              @click="activeTab = 'faqs'"
            >
              <i class="bi bi-journal-text"></i> Knowledge Base & FAQs ({{ faqs.length }})
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link fw-semibold border-0 py-3 px-4 d-flex align-items-center gap-2"
              :class="{ 'active text-primary border-bottom border-primary border-3': activeTab === 'settings' }"
              @click="activeTab = 'settings'"
            >
              <i class="bi bi-gear-fill"></i> WhatsApp & Bot Settings
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link fw-semibold border-0 py-3 px-4 d-flex align-items-center gap-2"
              :class="{ 'active text-primary border-bottom border-primary border-3': activeTab === 'logs' }"
              @click="activeTab = 'logs'"
            >
              <i class="bi bi-activity"></i> Inquiries & Analytics Logs
            </button>
          </li>
        </ul>
      </div>

      <!-- Tab 1: FAQs / Knowledge Base -->
      <div v-if="activeTab === 'faqs'" class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
          <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input
              v-model="faqSearch"
              type="text"
              class="form-control border-start-0"
              placeholder="Search questions or keywords..."
            />
          </div>

          <div class="d-flex gap-2 align-items-center">
            <select v-model="categoryFilter" class="form-select form-select-sm rounded-pill" style="width: auto;">
              <option value="all">All Categories</option>
              <option value="admission">Admissions</option>
              <option value="fees">Fees & Payments</option>
              <option value="attendance">Attendance & Biometrics</option>
              <option value="exams">Examinations</option>
              <option value="contact">Contact & Office</option>
              <option value="general">General</option>
            </select>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small text-muted">
              <tr>
                <th style="width: 35%;">Question</th>
                <th>Category</th>
                <th style="width: 30%;">Answer Summary</th>
                <th>Keywords</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="faq in filteredFaqs" :key="faq.id">
                <td>
                  <strong class="text-dark d-block">{{ faq.question }}</strong>
                  <small v-if="faq.action_label" class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    Action: {{ faq.action_label }}
                  </small>
                </td>
                <td>
                  <span class="badge rounded-pill bg-light text-dark border text-capitalize">
                    {{ faq.category }}
                  </span>
                </td>
                <td>
                  <span class="text-muted small text-truncate d-inline-block" style="max-width: 280px;">
                    {{ faq.answer }}
                  </span>
                </td>
                <td>
                  <span class="small font-monospace text-muted">{{ faq.keywords || '—' }}</span>
                </td>
                <td>
                  <span class="badge rounded-pill" :class="faq.is_active ? 'bg-success' : 'bg-secondary'">
                    {{ faq.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" @click="editFaq(faq)" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" @click="deleteFaq(faq)" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredFaqs.length === 0">
                <td colspan="6" class="text-center py-4 text-muted">
                  No knowledge base entries found matching your search.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 2: Settings -->
      <div v-if="activeTab === 'settings'" class="card-body p-4">
        <form @submit.prevent="saveSettings" style="max-width: 780px;">
          <h5 class="fw-bold text-dark mb-3">AI Support Assistant & WhatsApp Configuration</h5>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Bot Display Name</label>
              <input
                v-model="settingsForm.bot_name"
                type="text"
                class="form-control"
                placeholder="e.g. School AI Assistant"
                required
              />
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">
                <i class="bi bi-whatsapp text-success me-1"></i> Support WhatsApp Number
              </label>
              <input
                v-model="settingsForm.whatsapp_number"
                type="text"
                class="form-control font-monospace"
                placeholder="+919331805678"
                required
              />
              <small class="text-muted">Includes country code without dashes (e.g. +91 93318 05678)</small>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Welcome Greeting Message</label>
            <textarea
              v-model="settingsForm.welcome_message"
              class="form-control"
              rows="3"
              placeholder="Greeting shown when a user opens the chat drawer..."
            ></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Support Phone</label>
              <input v-model="settingsForm.support_phone" type="text" class="form-control" placeholder="+91 033 5562 9768" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Support / Admissions Email</label>
              <input v-model="settingsForm.support_email" type="email" class="form-control" placeholder="support@school.com" />
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Campus Office Working Hours</label>
              <input v-model="settingsForm.office_hours" type="text" class="form-control" placeholder="Mon - Sat: 8:00 AM - 4:00 PM" />
            </div>
          </div>

          <hr class="my-4" />

          <h6 class="fw-bold text-dark mb-3">Feature Toggles</h6>

          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="toggleBot" v-model="settingsForm.is_enabled" />
            <label class="form-check-label fw-semibold" for="toggleBot">
              Enable Floating AI Support Bot across ERP & School Website
            </label>
          </div>

          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="toggleWhatsApp" v-model="settingsForm.enable_whatsapp_button" />
            <label class="form-check-label fw-semibold" for="toggleWhatsApp">
              Enable 1-Click "Chat on WhatsApp" Quick Escalation Pill
            </label>
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" id="toggleVoice" v-model="settingsForm.enable_voice_speech" />
            <label class="form-check-label fw-semibold" for="toggleVoice">
              Enable Voice Speech-to-Text Input (Mic button)
            </label>
          </div>

          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs" :disabled="savingSettings">
            <span v-if="savingSettings" class="spinner-border spinner-border-sm me-1"></span>
            Save Bot Settings
          </button>
        </form>
      </div>

      <!-- Tab 3: Logs -->
      <div v-if="activeTab === 'logs'" class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3">Live Inquiries & Analytics Feed</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted text-uppercase">
              <tr>
                <th>Time</th>
                <th>User Query</th>
                <th>Matched Category</th>
                <th>Escalated to WhatsApp</th>
                <th>Feedback</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id">
                <td class="small text-muted">{{ formatDate(log.created_at) }}</td>
                <td><strong class="text-dark">{{ log.user_query }}</strong></td>
                <td><span class="badge bg-light text-dark border">{{ log.matched_category || 'general' }}</span></td>
                <td>
                  <span v-if="log.escalated_to_whatsapp" class="badge bg-success-subtle text-success border border-success-subtle">
                    <i class="bi bi-whatsapp me-1"></i> WhatsApp
                  </span>
                  <span v-else class="text-muted small">Bot Resolved</span>
                </td>
                <td>
                  <span v-if="log.was_helpful === true" class="text-success small fw-bold">
                    <i class="bi bi-hand-thumbs-up-fill me-1"></i> Helpful
                  </span>
                  <span v-else-if="log.was_helpful === false" class="text-danger small fw-bold">
                    <i class="bi bi-hand-thumbs-down-fill me-1"></i> Unhelpful
                  </span>
                  <span v-else class="text-muted small">—</span>
                </td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="5" class="text-center py-4 text-muted">
                  No inquiry logs recorded yet. Questions asked by visitors will appear here.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal: Add / Edit FAQ -->
    <div v-if="showFaqModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-primary text-white py-3 border-0">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-journal-plus me-2"></i>{{ faqForm.id ? 'Edit Knowledge FAQ' : 'Add Knowledge FAQ' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="showFaqModal = false"></button>
          </div>
          <form @submit.prevent="saveFaq">
            <div class="modal-body p-4">
              <div class="row g-3 mb-3">
                <div class="col-md-8">
                  <label class="form-label fw-semibold">Question / Intent <span class="text-danger">*</span></label>
                  <input v-model="faqForm.question" type="text" class="form-control" placeholder="e.g. How do I pay fees via UPI?" required />
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                  <select v-model="faqForm.category" class="form-select" required>
                    <option value="admission">Admission</option>
                    <option value="fees">Fees & Payments</option>
                    <option value="attendance">Attendance & Biometric</option>
                    <option value="exams">Exams & Results</option>
                    <option value="transport">Transport</option>
                    <option value="hostel">Hostel</option>
                    <option value="contact">Contact & Office</option>
                    <option value="general">General</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Answer (Supports Markdown formatting) <span class="text-danger">*</span></label>
                <textarea v-model="faqForm.answer" class="form-control font-monospace small" rows="5" placeholder="Write answer here..." required></textarea>
                <small class="text-muted">Placeholders: <code>{school_name}</code>, <code>{phone}</code>, <code>{email}</code>, <code>{address}</code>, <code>{whatsapp}</code></small>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Keywords (Comma-separated for fast search)</label>
                <input v-model="faqForm.keywords" type="text" class="form-control font-monospace small" placeholder="e.g. upi, gpay, phonepe, fee payment, receipt" />
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Action Button Label (Optional)</label>
                  <input v-model="faqForm.action_label" type="text" class="form-control" placeholder="e.g. Apply Online Now" />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Action URL (Optional)</label>
                  <input v-model="faqForm.action_url" type="text" class="form-control" placeholder="e.g. /apply-admission or https://..." />
                </div>
              </div>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="faqActive" v-model="faqForm.is_active" />
                <label class="form-check-label fw-semibold" for="faqActive">Active in Knowledge Base</label>
              </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
              <button type="button" class="btn btn-outline-secondary rounded-pill px-3" @click="showFaqModal = false">Cancel</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" :disabled="savingFaq">
                <span v-if="savingFaq" class="spinner-border spinner-border-sm me-1"></span>
                Save FAQ Rule
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import Swal from 'sweetalert2'
import api from '@/api/axios'

const activeTab = ref('faqs')
const faqs = ref([])
const logs = ref([])
const stats = ref({ total_queries: 0, whatsapp_escalations: 0, helpful_count: 0, unhelpful_count: 0 })

const faqSearch = ref('')
const categoryFilter = ref('all')
const showFaqModal = ref(false)
const savingFaq = ref(false)
const savingSettings = ref(false)

const faqForm = reactive({
  id: null,
  category: 'general',
  question: '',
  answer: '',
  keywords: '',
  action_label: '',
  action_url: '',
  is_active: true,
})

const settingsForm = reactive({
  bot_name: 'School AI Assistant',
  welcome_message: '',
  whatsapp_number: '+919331805678',
  support_email: '',
  support_phone: '',
  office_hours: 'Mon - Sat: 8:00 AM - 4:00 PM',
  is_enabled: true,
  enable_whatsapp_button: true,
  enable_voice_speech: true,
})

const helpfulRate = computed(() => {
  const total = (stats.value.helpful_count || 0) + (stats.value.unhelpful_count || 0)
  if (total === 0) return 100
  return Math.round(((stats.value.helpful_count || 0) / total) * 100)
})

const filteredFaqs = computed(() => {
  return faqs.value.filter(f => {
    const matchesCat = categoryFilter.value === 'all' || f.category === categoryFilter.value
    const q = faqSearch.value.trim().toLowerCase()
    const matchesSearch = !q ||
      f.question.toLowerCase().includes(q) ||
      (f.keywords && f.keywords.toLowerCase().includes(q)) ||
      f.answer.toLowerCase().includes(q)
    return matchesCat && matchesSearch
  })
})

async function fetchFaqs() {
  try {
    const res = await api.get('/ai-bot/admin/faqs')
    if (res.data.success) {
      faqs.value = res.data.faqs || []
    }
  } catch (err) {
    console.error('Failed to load FAQs', err)
  }
}

async function fetchSettings() {
  try {
    const res = await api.get('/ai-bot/admin/settings')
    if (res.data.success && res.data.settings) {
      Object.assign(settingsForm, res.data.settings)
    }
  } catch (err) {
    console.error('Failed to load settings', err)
  }
}

async function fetchLogs() {
  try {
    const res = await api.get('/ai-bot/admin/logs')
    if (res.data.success) {
      logs.value = res.data.logs || []
      stats.value = res.data.stats || stats.value
    }
  } catch (err) {
    console.error('Failed to load logs', err)
  }
}

function openAddFaqModal() {
  faqForm.id = null
  faqForm.category = 'general'
  faqForm.question = ''
  faqForm.answer = ''
  faqForm.keywords = ''
  faqForm.action_label = ''
  faqForm.action_url = ''
  faqForm.is_active = true
  showFaqModal.value = true
}

function editFaq(item) {
  faqForm.id = item.id
  faqForm.category = item.category
  faqForm.question = item.question
  faqForm.answer = item.answer
  faqForm.keywords = item.keywords || ''
  faqForm.action_label = item.action_label || ''
  faqForm.action_url = item.action_url || ''
  faqForm.is_active = !!item.is_active
  showFaqModal.value = true
}

async function saveFaq() {
  savingFaq.value = true
  try {
    const res = await api.post('/ai-bot/admin/faqs', faqForm)
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Saved!', text: 'Knowledge FAQ rule updated successfully.', timer: 1500, showConfirmButton: false })
      showFaqModal.value = false
      fetchFaqs()
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to save FAQ' })
  } finally {
    savingFaq.value = false
  }
}

async function deleteFaq(item) {
  const confirm = await Swal.fire({
    title: 'Delete FAQ Rule?',
    text: `Are you sure you want to delete "${item.question}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'Yes, Delete',
  })

  if (confirm.isConfirmed) {
    try {
      await api.delete(`/ai-bot/admin/faqs/${item.id}`)
      Swal.fire({ icon: 'success', title: 'Deleted', text: 'FAQ rule removed.', timer: 1200, showConfirmButton: false })
      fetchFaqs()
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete' })
    }
  }
}

async function saveSettings() {
  savingSettings.value = true
  try {
    const res = await api.post('/ai-bot/admin/settings', settingsForm)
    if (res.data.success) {
      Swal.fire({ icon: 'success', title: 'Settings Saved', text: 'AI Bot & WhatsApp configuration updated.', timer: 1500, showConfirmButton: false })
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save settings' })
  } finally {
    savingSettings.value = false
  }
}

function testWhatsAppChat() {
  const num = (settingsForm.whatsapp_number || '+919331805678').replace(/[^\d]/g, '')
  window.open(`https://wa.me/${num}?text=Hello%20School%20Support%20Desk`, '_blank')
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleString()
}

onMounted(() => {
  fetchFaqs()
  fetchSettings()
  fetchLogs()
})
</script>
