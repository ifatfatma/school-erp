<template>
  <div class="ai-bot-wrapper position-fixed" style="z-index: 1050; bottom: 24px; right: 24px;">
    <!-- Floating Launcher Button (When Drawer is Closed) -->
    <div v-if="!isOpen" class="d-flex align-items-center gap-2">
      <!-- WhatsApp Direct Pill -->
      <a
        v-if="botConfig.enable_whatsapp && whatsappNumber"
        :href="whatsappDirectUrl"
        target="_blank"
        rel="noopener noreferrer"
        class="btn btn-success rounded-pill shadow-lg d-none d-md-flex align-items-center gap-2 py-2 px-3 text-white text-decoration-none fw-semibold border border-white"
        title="Direct Chat on WhatsApp"
        style="background: #25D366; border-color: #25D366; transition: transform 0.2s;"
        @mouseenter="$event.currentTarget.style.transform = 'scale(1.05)'"
        @mouseleave="$event.currentTarget.style.transform = 'scale(1)'"
      >
        <i class="bi bi-whatsapp fs-5"></i>
        <span>WhatsApp Support</span>
      </a>

      <!-- AI Bot Launcher Button -->
      <button
        type="button"
        class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center p-0 position-relative border border-2 border-white"
        style="width: 58px; height: 58px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); transition: transform 0.25s ease;"
        @click="toggleChat"
        title="School AI Assistant & WhatsApp Helpdesk"
      >
        <i class="bi bi-robot fs-3 text-white"></i>
        <!-- Glowing Online Pulse Dot -->
        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle">
          <span class="visually-hidden">Online</span>
        </span>
      </button>
    </div>

    <!-- AI Bot Chat Drawer Modal / Window -->
    <div
      v-else
      class="card shadow-2xl border-0 rounded-4 overflow-hidden d-flex flex-column"
      style="width: 380px; max-width: calc(100vw - 32px); height: 580px; max-height: calc(100vh - 48px); background: #ffffff;"
    >
      <!-- Chat Header -->
      <div class="p-3 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);">
        <div class="d-flex align-items-center gap-2">
          <div class="position-relative">
            <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
              <i class="bi bi-robot fs-4 text-primary"></i>
            </div>
            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
          </div>
          <div>
            <div class="fw-bold fs-6 text-white text-truncate lh-1" style="max-width: 180px;">
              {{ currentSchoolName }}
            </div>
            <small class="text-white-50" style="font-size: 0.72rem;">
              <i class="bi bi-shield-check text-success me-1"></i>AI Support & WhatsApp 24/7
            </small>
          </div>
        </div>

        <!-- Header Actions -->
        <div class="d-flex align-items-center gap-1">
          <a
            v-if="botConfig.enable_whatsapp && whatsappNumber"
            :href="whatsappDirectUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-sm btn-success rounded-circle p-1 text-white border-0 d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px; background: #25D366;"
            title="Chat on WhatsApp"
          >
            <i class="bi bi-whatsapp"></i>
          </a>
          <button
            type="button"
            class="btn btn-sm btn-outline-light rounded-circle p-1 border-0 d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px;"
            title="Clear Chat"
            @click="clearChat"
          >
            <i class="bi bi-arrow-clockwise"></i>
          </button>
          <button
            type="button"
            class="btn btn-sm btn-outline-light rounded-circle p-1 border-0 d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px;"
            title="Minimize"
            @click="toggleChat"
          >
            <i class="bi bi-dash-lg"></i>
          </button>
        </div>
      </div>

      <!-- Live Quick Connect Banner -->
      <div class="bg-light py-2 px-3 border-bottom d-flex align-items-center justify-content-between small text-muted">
        <span class="d-flex align-items-center gap-1" style="font-size: 0.75rem;">
          <i class="bi bi-clock-history text-primary"></i>
          <span>{{ botConfig.office_hours || 'Mon - Sat: 8AM - 4PM' }}</span>
        </span>
        <a
          v-if="whatsappNumber"
          :href="whatsappDirectUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="text-success text-decoration-none fw-bold d-flex align-items-center gap-1"
          style="font-size: 0.75rem;"
        >
          <i class="bi bi-whatsapp"></i> WhatsApp Live
        </a>
      </div>

      <!-- Messages Scroll Area -->
      <div ref="messagesContainer" class="flex-grow-1 p-3 overflow-y-auto" style="background: #f8fafc; font-size: 0.88rem;">
        <!-- Welcome Prompt -->
        <div class="text-center my-2">
          <span class="badge bg-white text-secondary shadow-xs border px-3 py-1 rounded-pill" style="font-size: 0.7rem;">
            Knowledge-Base Powered • Zero Cost Support
          </span>
        </div>

        <!-- Message List -->
        <div v-for="(msg, idx) in messages" :key="idx" class="mb-3">
          <!-- User Bubble -->
          <div v-if="msg.sender === 'user'" class="d-flex justify-content-end mb-1">
            <div
              class="p-2 px-3 text-white rounded-3 shadow-xs"
              style="max-width: 85%; background: linear-gradient(135deg, #2563eb, #1d4ed8); border-bottom-right-radius: 4px !important;"
            >
              {{ msg.text }}
            </div>
          </div>

          <!-- Bot Bubble -->
          <div v-else class="d-flex align-items-start gap-2 mb-1">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1 shadow-xs" style="width: 28px; height: 28px; font-size: 0.75rem;">
              <i class="bi bi-robot"></i>
            </div>
            <div
              class="p-3 bg-white text-dark rounded-3 shadow-xs border"
              style="max-width: 85%; border-top-left-radius: 4px !important;"
            >
              <!-- Formatted Markdown/HTML text -->
              <div class="bot-markdown-content" v-html="formatMarkdown(msg.text)"></div>

              <!-- Message Action Button -->
              <div v-if="msg.action" class="mt-2 pt-2 border-top">
                <router-link
                  v-if="!msg.action.is_external && msg.action.url?.startsWith('/')"
                  :to="msg.action.url"
                  class="btn btn-primary btn-sm rounded-pill w-100 fw-semibold d-flex align-items-center justify-content-center gap-1 shadow-xs"
                  @click="toggleChat"
                >
                  <i class="bi bi-box-arrow-up-right me-1"></i> {{ msg.action.label }}
                </router-link>
                <a
                  v-else
                  :href="msg.action.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-semibold d-flex align-items-center justify-content-center gap-1"
                >
                  <i class="bi bi-box-arrow-up-right me-1"></i> {{ msg.action.label }}
                </a>
              </div>

              <!-- WhatsApp Quick Escalation Card inside Message -->
              <div v-if="msg.whatsapp?.url" class="mt-2 pt-2 border-top">
                <a
                  :href="msg.whatsapp.url"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-sm rounded-pill w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 text-white shadow-xs"
                  style="background: #25D366; border-color: #25D366;"
                >
                  <i class="bi bi-whatsapp fs-6"></i>
                  <span>{{ msg.whatsapp.label || 'Ask Counselor on WhatsApp' }}</span>
                </a>
              </div>

              <!-- Feedback Thumbs -->
              <div class="d-flex align-items-center justify-content-between mt-2 pt-1 text-muted" style="font-size: 0.72rem;">
                <span class="text-muted">{{ msg.time }}</span>
                <div class="d-flex align-items-center gap-1">
                  <span class="me-1">Helpful?</span>
                  <button
                    type="button"
                    class="btn btn-link p-0 text-muted border-0"
                    :class="{ 'text-success fw-bold': msg.feedback === 'up' }"
                    @click="sendFeedback(msg, true)"
                    title="Helpful"
                  >
                    <i class="bi bi-hand-thumbs-up"></i>
                  </button>
                  <button
                    type="button"
                    class="btn btn-link p-0 text-muted border-0"
                    :class="{ 'text-danger fw-bold': msg.feedback === 'down' }"
                    @click="sendFeedback(msg, false)"
                    title="Not Helpful"
                  >
                    <i class="bi bi-hand-thumbs-down"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Typing Animation -->
        <div v-if="isTyping" class="d-flex align-items-start gap-2 mb-2">
          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.75rem;">
            <i class="bi bi-robot"></i>
          </div>
          <div class="p-2 px-3 bg-white text-muted rounded-pill border shadow-xs d-flex align-items-center gap-2" style="font-size: 0.8rem;">
            <div class="spinner-grow spinner-grow-sm text-primary" style="width: 0.6rem; height: 0.6rem;" role="status"></div>
            <span>Thinking...</span>
          </div>
        </div>
      </div>

      <!-- Quick Suggestion Chips (Horizontal Scroll) -->
      <div class="bg-white border-top py-2 px-3 overflow-x-auto d-flex gap-2 align-items-center no-scrollbar" style="white-space: nowrap;">
        <button
          v-for="(chip, cIdx) in currentSuggestions"
          :key="cIdx"
          type="button"
          class="btn btn-outline-secondary btn-sm rounded-pill text-nowrap py-1 px-2 border-secondary-subtle bg-light text-dark"
          style="font-size: 0.75rem;"
          @click="submitQuery(chip)"
        >
          {{ chip }}
        </button>
      </div>

      <!-- Input Footer -->
      <div class="p-2 px-3 bg-white border-top">
        <form @submit.prevent="handleSend" class="d-flex align-items-center gap-2">
          <input
            ref="inputField"
            v-model="userQuery"
            type="text"
            class="form-control form-control-sm rounded-pill border-secondary-subtle px-3 py-2 shadow-xs"
            placeholder="Type your question..."
            :disabled="isTyping"
          />

          <!-- Speech-to-Text Button (If supported) -->
          <button
            v-if="speechSupported"
            type="button"
            class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0"
            :class="isListening ? 'btn-danger animate-pulse' : 'btn-outline-secondary'"
            style="width: 36px; height: 36px;"
            :title="isListening ? 'Listening... click to stop' : 'Voice Input (Speech-to-Text)'"
            @click="toggleSpeechRecognition"
          >
            <i class="bi" :class="isListening ? 'bi-mic-fill' : 'bi-mic'"></i>
          </button>

          <!-- Send Button -->
          <button
            type="submit"
            class="btn btn-primary btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 flex-shrink-0 shadow-xs"
            style="width: 36px; height: 36px;"
            :disabled="!userQuery.trim() || isTyping"
          >
            <i class="bi bi-send-fill fs-6"></i>
          </button>
        </form>
        <div class="text-center mt-1 text-muted" style="font-size: 0.65rem;">
          Rule-Based Knowledge Engine • Zero-Cost Fast Assistance
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { useTenantStore } from '@/stores/tenant'
import api from '@/api/axios'

const route = useRoute()
const tenantStore = useTenantStore()

const isOpen = ref(false)
const isTyping = ref(false)
const userQuery = ref('')
const inputField = ref(null)
const messagesContainer = ref(null)

const speechSupported = ref(false)
const isListening = ref(false)
let recognition = null

const botConfig = ref({
  name: 'School AI Assistant',
  welcome_message: '',
  is_enabled: true,
  enable_whatsapp: true,
  enable_voice: true,
  office_hours: 'Mon - Sat: 8:00 AM - 4:00 PM',
})

const whatsappNumber = ref('+919331805678')
const whatsappDirectUrl = ref('https://wa.me/919331805678?text=Hello%20School%20Support')
const currentSchoolName = ref('School AI Support')

const defaultSuggestions = ref([
  'How do I apply for admission?',
  'What are the school fees & payment options?',
  'What are the daily campus timings?',
  'Chat on WhatsApp with Counselor',
])

const currentSuggestions = ref([...defaultSuggestions.value])

const messages = ref([])

const botSessionId = ref(localStorage.getItem('ai_bot_session_id') || '')
if (!botSessionId.value) {
  botSessionId.value = 'bot_' + Math.random().toString(36).substring(2, 12) + Date.now().toString(36)
  localStorage.setItem('ai_bot_session_id', botSessionId.value)
}

// Current school ID from active tenant or website route
const currentSchoolId = computed(() => {
  if (tenantStore.activeSchoolId && tenantStore.activeSchoolId !== 'all') {
    return Number(tenantStore.activeSchoolId)
  }
  return null
})

function toggleChat() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => {
      scrollToBottom()
      inputField.value?.focus()
    })
  }
}

function clearChat() {
  messages.value = []
  initBot()
}

async function initBot() {
  try {
    const res = await api.get('/ai-bot/init', {
      params: { school_id: currentSchoolId.value || undefined },
    })

    if (res.data) {
      if (res.data.school?.name) {
        currentSchoolName.value = res.data.school.name
      } else if (tenantStore.activeSchoolName) {
        currentSchoolName.value = tenantStore.activeSchoolName
      }

      if (res.data.bot) {
        botConfig.value = { ...botConfig.value, ...res.data.bot }
      }

      if (res.data.whatsapp?.number) {
        whatsappNumber.value = res.data.whatsapp.number
        whatsappDirectUrl.value = res.data.whatsapp.url
      }

      if (res.data.default_suggestions?.length) {
        currentSuggestions.value = res.data.default_suggestions
      }

      // Add initial greeting message if messages empty
      if (messages.value.length === 0) {
        messages.value.push({
          sender: 'bot',
          text: res.data.bot?.welcome_message || `Hello! Welcome to ${currentSchoolName.value}. I am your 24/7 AI Support Assistant. Ask me anything about admissions, fees, timetable, attendance, or tap WhatsApp to connect with our counseling staff.`,
          time: formatTime(new Date()),
          suggestions: currentSuggestions.value,
          whatsapp: {
            url: whatsappDirectUrl.value,
            label: 'Chat on WhatsApp with Staff',
          },
        })
      }
    }
  } catch (err) {
    console.error('Failed to init AI Bot:', err)
  }
}

async function submitQuery(queryText) {
  if (!queryText || isTyping.value) return
  userQuery.value = queryText
  await handleSend()
}

async function handleSend() {
  const query = userQuery.value.trim()
  if (!query || isTyping.value) return

  // Add user bubble
  messages.value.push({
    sender: 'user',
    text: query,
    time: formatTime(new Date()),
  })

  userQuery.value = ''
  isTyping.value = true
  scrollToBottom()

  try {
    const res = await api.post('/ai-bot/chat', {
      query,
      school_id: currentSchoolId.value || undefined,
      session_id: botSessionId.value,
    })

    const botData = res.data?.data || {}

    messages.value.push({
      sender: 'bot',
      text: botData.reply || 'I am happy to assist you! For more details, connect with our school helpdesk on WhatsApp.',
      time: formatTime(new Date()),
      category: botData.category,
      action: botData.action,
      whatsapp: botData.whatsapp,
      feedback: null,
    })

    if (botData.suggestions?.length) {
      currentSuggestions.value = botData.suggestions
    }
  } catch (err) {
    messages.value.push({
      sender: 'bot',
      text: "### 💬 WhatsApp Live Support\n\nI am experiencing a momentary connection issue, but our admissions and helpdesk team is online on WhatsApp right now to assist you directly!",
      time: formatTime(new Date()),
      whatsapp: {
        url: whatsappDirectUrl.value,
        label: 'Chat Directly on WhatsApp',
      },
    })
  } finally {
    isTyping.value = false
    scrollToBottom()
  }
}

async function sendFeedback(msg, wasHelpful) {
  msg.feedback = wasHelpful ? 'up' : 'down'
  try {
    await api.post('/ai-bot/feedback', {
      session_id: botSessionId.value,
      was_helpful: wasHelpful,
    })
  } catch (e) {}
}

function formatMarkdown(text) {
  if (!text) return ''
  let html = text
    .replace(/^### (.*$)/gim, '<h6 class="fw-bold text-primary mb-1 mt-1">$1</h6>')
    .replace(/^## (.*$)/gim, '<h6 class="fw-bold text-dark mb-1">$1</h6>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`([^`]+)`/g, '<code class="bg-light px-1 rounded text-danger">$1</code>')
    .replace(/^\* (.*$)/gim, '<div class="d-flex align-items-start gap-1 my-1"><i class="bi bi-check2 text-success me-1 mt-1"></i><div>$1</div></div>')
    .replace(/\n\n/g, '<br>')
    .replace(/\n/g, '<br>')
  return html
}

function formatTime(d) {
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Voice Recognition
function setupSpeechRecognition() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  if (SpeechRecognition) {
    speechSupported.value = true
    recognition = new SpeechRecognition()
    recognition.continuous = false
    recognition.interimResults = false
    recognition.lang = 'en-IN'

    recognition.onresult = (event) => {
      const transcript = event.results[0][0].transcript
      userQuery.value = transcript
      isListening.value = false
      handleSend()
    }

    recognition.onerror = () => {
      isListening.value = false
    }

    recognition.onend = () => {
      isListening.value = false
    }
  }
}

function toggleSpeechRecognition() {
  if (!recognition) return
  if (isListening.value) {
    recognition.stop()
    isListening.value = false
  } else {
    try {
      recognition.start()
      isListening.value = true
    } catch (e) {
      isListening.value = false
    }
  }
}

onMounted(() => {
  initBot()
  setupSpeechRecognition()
})
</script>

<style scoped>
.bot-markdown-content {
  line-height: 1.5;
}
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.animate-pulse {
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.15); opacity: 0.8; }
  100% { transform: scale(1); opacity: 1; }
}
</style>
