<template>
  <div
    class="id-card-wrapper"
    :class="[
      'layout-' + (template?.layout || 'vertical'),
      'theme-' + (template?.theme || 'modern'),
      { 'is-back': isBack }
    ]"
    :style="cardCustomStyles"
  >
    <!-- FRONT SIDE -->
    <div v-if="!isBack" class="id-card-inner front-face">
      <!-- Top Decorative Accent Bar -->
      <div class="accent-top-bar" :style="{ backgroundColor: template?.accent_color || '#f59e0b' }"></div>

      <!-- Card Header -->
      <header class="card-header-band" :style="headerStyle">
        <div class="header-content">
          <div class="school-brand-block">
            <img
              v-if="template?.logo_url"
              :src="template.logo_url"
              alt="School Logo"
              class="school-logo-img"
              @error="handleImgError"
            />
            <div class="school-titles">
              <h4 class="school-name">{{ template?.header_title || 'INSTITUTION NAME' }}</h4>
              <p v-if="template?.header_subtitle" class="school-sub">
                {{ template.header_subtitle }}
              </p>
              <div v-if="template?.academic_session" class="session-pill">
                <span>Session: {{ template.academic_session }}</span>
              </div>
            </div>
          </div>
        </div>
      </header>

      <!-- Main Body: Student Photo & Essential Info -->
      <div class="card-body-section">
        <!-- Student Photo -->
        <div class="photo-container position-relative">
          <div
            class="photo-frame"
            :class="'shape-' + (template?.photo_shape || 'rounded')"
            :style="{ borderColor: template?.accent_color || '#f59e0b' }"
          >
            <img
              :src="student?.photo_url || '/images/no-image.png'"
              :alt="student?.full_name"
              class="student-photo"
              @error="handlePhotoError"
            />
          </div>

          <!-- Quick Change/Upload Photo Button (Screen only) -->
          <button
            v-if="allowPhotoUpload && student?.id && student.id !== 'demo'"
            type="button"
            class="card-photo-hover-btn d-print-none"
            title="Upload/Update Student Photo"
            @click.stop="$emit('upload-photo', student)"
          >
            <i class="bi bi-camera-fill"></i>
          </button>

          <div v-if="template?.show_blood_group && student?.blood_group" class="blood-group-badge">
            <i class="bi bi-droplet-fill text-danger me-1"></i>
            <span>{{ student.blood_group }}</span>
          </div>
        </div>

        <!-- Student Identity Details -->
        <div class="details-container">
          <h3 class="student-name" :style="{ color: template?.primary_color || '#1e3a8a' }">
            {{ student?.full_name || 'STUDENT NAME' }}
          </h3>

          <div
            v-if="template?.show_class_section"
            class="class-section-tag"
            :style="{ backgroundColor: hexToRgba(template?.primary_color || '#1e3a8a', 0.1), color: template?.primary_color || '#1e3a8a' }"
          >
            <strong>Class:</strong> {{ student?.class_name || '-' }} ({{ student?.section_name || '-' }})
          </div>

          <div class="meta-grid">
            <div v-if="template?.show_admission_no" class="meta-row">
              <span class="meta-label">Adm No:</span>
              <span class="meta-val font-mono">{{ student?.admission_no || '-' }}</span>
            </div>

            <div v-if="template?.show_roll_no" class="meta-row">
              <span class="meta-label">Roll No:</span>
              <span class="meta-val">{{ student?.roll_no || '-' }}</span>
            </div>

            <div v-if="template?.show_dob && student?.dob" class="meta-row">
              <span class="meta-label">DOB:</span>
              <span class="meta-val">{{ student.dob }}</span>
            </div>

            <div v-if="template?.show_father_name && student?.father_name" class="meta-row">
              <span class="meta-label">Guardian:</span>
              <span class="meta-val text-truncate" style="max-width: 120px;">{{ student.father_name }}</span>
            </div>

            <div v-if="template?.show_emergency_phone && student?.emergency_phone" class="meta-row">
              <span class="meta-label">Emergency:</span>
              <span class="meta-val font-mono">{{ student.emergency_phone }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Card Bottom Band: Barcode / QR & Signatory -->
      <footer class="card-footer-band">
        <!-- Scan Code Area (QR or Barcode) -->
        <div class="scan-area">
          <div v-if="template?.barcode_type === 'qr'" class="qr-box">
            <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code" class="qr-code-img" />
            <span class="scan-subtext">{{ student?.admission_no }}</span>
          </div>

          <div v-else-if="template?.barcode_type === 'barcode'" class="barcode-box">
            <svg ref="barcodeSvgRef" class="barcode-svg"></svg>
          </div>
        </div>

        <!-- Authority Signature -->
        <div class="signature-box">
          <div class="sig-wrapper">
            <img
              v-if="template?.signature_url"
              :src="template.signature_url"
              alt="Principal Signature"
              class="signature-img"
              @error="handleImgError"
            />
            <div v-else class="sig-placeholder">
              <span class="sig-line"></span>
            </div>
          </div>
          <span class="signatory-label">{{ template?.signatory_title || 'Authorized Signatory' }}</span>
        </div>
      </footer>

      <!-- Footer Micro Contact Strip -->
      <div v-if="template?.contact_info" class="micro-strip" :style="{ backgroundColor: template?.primary_color || '#1e3a8a' }">
        <span>{{ template.contact_info }}</span>
      </div>
    </div>

    <!-- BACK SIDE -->
    <div v-else class="id-card-inner back-face">
      <div class="accent-top-bar" :style="{ backgroundColor: template?.accent_color || '#f59e0b' }"></div>

      <header class="back-header" :style="{ backgroundColor: hexToRgba(template?.primary_color || '#1e3a8a', 0.08) }">
        <div class="d-flex align-items-center justify-content-center gap-2">
          <img
            v-if="template?.logo_url"
            :src="template.logo_url"
            alt="School Logo"
            class="back-mini-logo"
            @error="handleImgError"
          />
          <h5 class="back-school-name" :style="{ color: template?.primary_color || '#1e3a8a' }">
            {{ template?.header_title || 'TERMS & INSTRUCTIONS' }}
          </h5>
        </div>
      </header>

      <div class="back-body">
        <div class="instructions-card">
          <h6 class="instructions-heading">Instructions / Guidelines</h6>
          <ul class="instructions-list">
            <li v-for="(line, idx) in formattedInstructions" :key="idx">
              {{ line }}
            </li>
          </ul>
        </div>

        <div v-if="student?.address && template?.show_address" class="address-box">
          <div class="address-title">Residential Address:</div>
          <div class="address-text">{{ student.address }}</div>
        </div>

        <div class="return-box" :style="{ borderColor: hexToRgba(template?.primary_color || '#1e3a8a', 0.3) }">
          <div class="return-title">IF FOUND, PLEASE RETURN TO:</div>
          <div class="return-content">
            <strong>{{ template?.header_title }}</strong><br />
            {{ template?.contact_info }}
          </div>
        </div>
      </div>

      <footer class="back-footer" :style="{ backgroundColor: template?.primary_color || '#1e3a8a' }">
        <span>Student Identity Card • Academic Session {{ template?.academic_session }}</span>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue';
import QRCode from 'qrcode';
import JsBarcode from 'jsbarcode';

const props = defineProps({
  template: {
    type: Object,
    required: true,
  },
  student: {
    type: Object,
    default: () => ({}),
  },
  isBack: {
    type: Boolean,
    default: false,
  },
  allowPhotoUpload: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['upload-photo']);

const qrDataUrl = ref('');
const barcodeSvgRef = ref(null);

// Convert HEX color to RGBA with opacity
const hexToRgba = (hex, alpha = 1) => {
  if (!hex || typeof hex !== 'string') return `rgba(37, 99, 235, ${alpha})`;
  let c = hex.replace('#', '');
  if (c.length === 3) {
    c = c.split('').map((char) => char + char).join('');
  }
  const num = parseInt(c, 16);
  if (isNaN(num)) return `rgba(37, 99, 235, ${alpha})`;
  const r = (num >> 16) & 255;
  const g = (num >> 8) & 255;
  const b = num & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

// Card Styles
const cardCustomStyles = computed(() => {
  return {
    '--card-primary': props.template?.primary_color || '#1d4ed8',
    '--card-accent': props.template?.accent_color || '#f59e0b',
    '--card-bg': props.template?.bg_color || '#ffffff',
  };
});

const headerStyle = computed(() => {
  const p = props.template?.primary_color || '#1d4ed8';
  const theme = props.template?.theme || 'modern';

  if (theme === 'modern') {
    return {
      background: `linear-gradient(135deg, ${p} 0%, ${hexToRgba(p, 0.88)} 100%)`,
    };
  } else if (theme === 'classic') {
    return {
      background: p,
      borderBottom: `2.5px solid ${props.template?.accent_color || '#f59e0b'}`,
    };
  } else if (theme === 'minimal') {
    return {
      background: 'transparent',
      color: '#111827',
      borderBottom: `1.5px solid ${hexToRgba(p, 0.2)}`,
    };
  } else if (theme === 'tech') {
    return {
      background: `linear-gradient(90deg, ${p} 0%, #0f172a 100%)`,
      borderBottom: `2px solid ${props.template?.accent_color || '#06b6d4'}`,
    };
  }

  return { background: p };
});

const formattedInstructions = computed(() => {
  const text = props.template?.back_instructions ||
    '1. This card is non-transferable and remains property of the school.\n2. Must be worn or presented on campus at all times.\n3. Report loss immediately to the administration office.';
  return text.split('\n').filter((l) => l.trim().length > 0);
});

// Generate QR Code or Barcode safely
const generateCodes = async () => {
  if (props.isBack) return;
  const codeVal = props.student?.scan_code || props.student?.admission_no || 'ERP-STU';

  if (props.template?.barcode_type === 'qr') {
    try {
      qrDataUrl.value = await QRCode.toDataURL(String(codeVal), {
        width: 140,
        margin: 1,
        color: {
          dark: '#0f172a',
          light: '#ffffff',
        },
      });
    } catch (err) {
      console.warn('QR Code generation error:', err);
    }
  } else if (props.template?.barcode_type === 'barcode') {
    await nextTick();
    if (barcodeSvgRef.value) {
      try {
        JsBarcode(barcodeSvgRef.value, String(codeVal), {
          format: 'CODE128',
          width: 1.1,
          height: 24,
          displayValue: true,
          fontSize: 9,
          margin: 0,
          lineColor: '#0f172a',
        });
      } catch (err) {
        console.warn('JsBarcode error:', err);
      }
    }
  }
};

const handlePhotoError = (e) => {
  e.target.src = '/images/no-image.png';
};

const handleImgError = (e) => {
  e.target.style.display = 'none';
};

watch(
  () => [props.template?.barcode_type, props.student?.admission_no, props.student?.scan_code, props.isBack],
  () => {
    generateCodes();
  },
  { immediate: true }
);

onMounted(() => {
  generateCodes();
});
</script>

<style scoped>
.id-card-wrapper {
  box-sizing: border-box;
  background-color: var(--card-bg, #ffffff);
  border-radius: 9px;
  overflow: hidden;
  box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  position: relative;
  user-select: none;
  border: 1px solid rgba(226, 232, 240, 0.85);
  display: flex;
  flex-direction: column;
}

/* Vertical / Portrait Mode (Default Lanyard Badge) */
.id-card-wrapper.layout-vertical {
  width: 54mm;
  height: 85.6mm;
  min-width: 54mm;
  max-width: 54mm;
  min-height: 85.6mm;
  max-height: 85.6mm;
}

/* Horizontal / Landscape Mode (Pocket / Clip Badge) */
.id-card-wrapper.layout-horizontal {
  width: 85.6mm;
  height: 54mm;
  min-width: 85.6mm;
  max-width: 85.6mm;
  min-height: 54mm;
  max-height: 54mm;
}

.id-card-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  width: 100%;
  position: relative;
  background: inherit;
}

/* Accent Top Bar */
.accent-top-bar {
  height: 3px;
  width: 100%;
  flex-shrink: 0;
}

/* Header Band */
.card-header-band {
  padding: 4px 6px 5px;
  color: #ffffff;
  text-align: center;
  flex-shrink: 0;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: center;
}

.school-brand-block {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  width: 100%;
}

.school-logo-img {
  width: 26px;
  height: 26px;
  object-fit: contain;
  background: #ffffff;
  padding: 1px;
  border-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
  flex-shrink: 0;
}

.school-titles {
  display: flex;
  flex-direction: column;
  align-items: center;
  overflow: hidden;
  line-height: 1.1;
}

.school-name {
  font-size: 8.5pt;
  font-weight: 800;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 155px;
}

.school-sub {
  font-size: 5.5pt;
  font-weight: 600;
  margin: 1px 0 0;
  letter-spacing: 0.5px;
  opacity: 0.9;
  text-transform: uppercase;
}

.session-pill {
  display: inline-block;
  background: rgba(255, 255, 255, 0.22);
  border-radius: 6px;
  padding: 0 4px;
  margin-top: 1px;
  font-size: 4.8pt;
  font-weight: 700;
  letter-spacing: 0.3px;
}

/* Minimal Theme Overrides for Header */
.theme-minimal .card-header-band {
  color: #111827;
}
.theme-minimal .school-name {
  color: var(--card-primary);
}
.theme-minimal .school-sub {
  color: #64748b;
}
.theme-minimal .session-pill {
  background: #f1f5f9;
  color: #334155;
}

/* Body Section: Vertical Layout */
.layout-vertical .card-body-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 5px 6px 2px;
  flex: 1 1 auto;
}

.layout-vertical .photo-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 3px;
  position: relative;
}

.photo-frame {
  width: 52px;
  height: 52px;
  overflow: hidden;
  border: 2px solid;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.photo-frame.shape-circle {
  border-radius: 50%;
}
.photo-frame.shape-rounded {
  border-radius: 8px;
}
.photo-frame.shape-square {
  border-radius: 2px;
}

.student-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Camera upload button overlay */
.card-photo-hover-btn {
  position: absolute;
  top: 1px;
  right: -5px;
  background: #1e293b;
  color: #ffffff;
  border: 1.5px solid #ffffff;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  cursor: pointer;
  z-index: 5;
  box-shadow: 0 2px 4px rgba(0,0,0,0.25);
  transition: all 0.15s ease;
}
.card-photo-hover-btn:hover {
  background: #2563eb;
  transform: scale(1.15);
}

.blood-group-badge {
  display: inline-flex;
  align-items: center;
  background: #fee2e2;
  color: #991b1b;
  font-size: 5.5pt;
  font-weight: 800;
  padding: 0 4px;
  border-radius: 8px;
  margin-top: -5px;
  z-index: 2;
  border: 1px solid #fca5a5;
  box-shadow: 0 1px 2px rgba(0,0,0,0.06);
}

.details-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.student-name {
  font-size: 8.5pt;
  font-weight: 800;
  margin: 1px 0 2px;
  letter-spacing: -0.1px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 180px;
}

.class-section-tag {
  display: inline-block;
  font-size: 5.8pt;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 10px;
  margin-bottom: 3px;
  text-transform: uppercase;
}

.meta-grid {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1px;
  padding: 0 4px;
}

.meta-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 5.8pt;
  line-height: 1.25;
  border-bottom: 0.5px dashed #f1f5f9;
  padding-bottom: 1px;
}

.meta-label {
  font-weight: 600;
  color: #64748b;
  text-align: left;
}

.meta-val {
  font-weight: 700;
  color: #1e293b;
  text-align: right;
}

/* Footer Band */
.layout-vertical .card-footer-band {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  padding: 2px 7px 3px;
  background: #fafaf9;
  border-top: 1px solid #f1f5f9;
  flex-shrink: 0;
}

.scan-area {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.qr-box {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.qr-code-img {
  width: 28px;
  height: 28px;
}

.barcode-svg {
  max-width: 65px;
  height: 24px;
}

.scan-subtext {
  font-size: 4.5pt;
  font-weight: 700;
  color: #64748b;
  margin-top: -1px;
}

.signature-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.sig-wrapper {
  height: 18px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.signature-img {
  max-height: 18px;
  max-width: 55px;
  object-fit: contain;
}

.sig-line {
  display: block;
  width: 45px;
  height: 1px;
  background: #cbd5e1;
  margin-bottom: 2px;
}

.signatory-label {
  font-size: 4.8pt;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.2px;
}

.micro-strip {
  color: #ffffff;
  font-size: 4.5pt;
  text-align: center;
  padding: 1.5px 3px;
  font-weight: 600;
  letter-spacing: 0.2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex-shrink: 0;
}

/* ==========================================================
   HORIZONTAL / LANDSCAPE MODE ADAPTATIONS
   ========================================================== */
.layout-horizontal .card-body-section {
  display: flex;
  flex-direction: row;
  align-items: center;
  padding: 4px 8px;
  gap: 8px;
  flex: 1 1 auto;
}

.layout-horizontal .photo-frame {
  width: 50px;
  height: 50px;
}

.layout-horizontal .details-container {
  align-items: flex-start;
  text-align: left;
  flex: 1;
}

.layout-horizontal .student-name {
  font-size: 9pt;
  margin-bottom: 2px;
}

.layout-horizontal .card-footer-band {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 2px 10px;
  background: #fafaf9;
  border-top: 1px solid #f1f5f9;
  flex-shrink: 0;
}

/* ==========================================================
   BACK FACE DESIGN
   ========================================================== */
.back-face {
  background: #ffffff;
  display: flex;
  flex-direction: column;
}

.back-header {
  padding: 4px 6px;
  text-align: center;
  border-bottom: 1px solid #e2e8f0;
}

.back-mini-logo {
  width: 14px;
  height: 14px;
  object-fit: contain;
}

.back-school-name {
  font-size: 6.8pt;
  font-weight: 800;
  margin: 0;
  text-transform: uppercase;
}

.back-body {
  padding: 5px 6px;
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.instructions-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  padding: 3px 5px;
}

.instructions-heading {
  font-size: 5.5pt;
  font-weight: 800;
  color: #334155;
  margin: 0 0 2px;
  text-transform: uppercase;
}

.instructions-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.instructions-list li {
  font-size: 4.8pt;
  line-height: 1.25;
  color: #475569;
  position: relative;
  padding-left: 7px;
  margin-bottom: 1.5px;
}

.instructions-list li::before {
  content: "•";
  position: absolute;
  left: 1px;
  color: var(--card-accent, #f59e0b);
  font-size: 6pt;
}

.address-box {
  background: #f1f5f9;
  border-radius: 4px;
  padding: 2px 4px;
  font-size: 4.8pt;
}

.address-title {
  font-weight: 700;
  color: #475569;
}

.address-text {
  color: #1e293b;
  line-height: 1.15;
}

.return-box {
  border: 1px dashed;
  border-radius: 4px;
  padding: 2.5px 4px;
  text-align: center;
  margin-top: auto;
}

.return-title {
  font-size: 4.5pt;
  font-weight: 800;
  color: #dc2626;
  letter-spacing: 0.3px;
}

.return-content {
  font-size: 4.5pt;
  color: #334155;
  line-height: 1.2;
}

.back-footer {
  color: #ffffff;
  font-size: 4.5pt;
  text-align: center;
  padding: 2px;
  font-weight: 600;
  letter-spacing: 0.2px;
}
</style>
