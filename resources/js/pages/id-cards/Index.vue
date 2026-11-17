<template>
  <div class="id-cards-page-container">
    <!-- Hidden Student Photo File Input for instant single-click upload -->
    <input
      type="file"
      ref="studentPhotoInputRef"
      class="d-none"
      accept="image/png, image/jpeg, image/jpg, image/webp"
      @change="handleStudentPhotoSelected"
    />

    <!-- Dedicated Student Photo Upload Modal -->
    <div
      v-if="showUploadPhotoModal"
      class="custom-modal-backdrop d-print-none"
      @click.self="showUploadPhotoModal = false"
    >
      <div class="custom-modal-dialog shadow-lg">
        <div class="custom-modal-header d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
          <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-camera-fill text-primary"></i>
            Upload Student Picture for ID Card
          </h5>
          <button type="button" class="btn-close" @click="showUploadPhotoModal = false"></button>
        </div>

        <div class="custom-modal-body p-4">
          <!-- Step 1: Select Student -->
          <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Select Student</label>
            <div class="input-group input-group-sm mb-2">
              <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
              <input
                type="text"
                v-model="modalStudentFilter"
                class="form-control"
                placeholder="Type name, roll, or admission no..."
              />
            </div>
            <select v-model="modalStudentId" class="form-select shadow-none">
              <option value="" disabled>-- Choose a student --</option>
              <option
                v-for="s in modalFilteredStudents"
                :key="s.id"
                :value="s.id"
              >
                {{ s.full_name }} (Class {{ s.class_name }}-{{ s.section_name }} | Roll: {{ s.roll_no }} | Adm: {{ s.admission_no }})
              </option>
            </select>
          </div>

          <!-- Step 2: Photo Preview & Selector -->
          <div class="d-flex align-items-center gap-4 my-3 p-3 bg-light rounded-3 border">
            <!-- Current / New Photo Preview -->
            <div class="text-center">
              <div class="photo-preview-frame mb-1">
                <img
                  :src="modalFilePreview || selectedModalStudent?.photo_url || '/images/no-image.png'"
                  alt="Student Photo"
                  class="preview-img"
                />
              </div>
              <small class="text-muted d-block">{{ modalFilePreview ? 'New Photo' : 'Current Photo' }}</small>
            </div>

            <!-- Choose File Button & Details -->
            <div class="flex-grow-1">
              <label class="form-label small fw-semibold text-dark mb-1">Choose Image File</label>
              <input
                type="file"
                class="form-control form-control-sm"
                accept="image/png, image/jpeg, image/jpg, image/webp"
                @change="onModalFileChange"
              />
              <small class="text-muted d-block mt-1">
                Supported formats: JPG, PNG, WebP (up to 10 MB). Clear passport-style photos look best on badges.
              </small>
            </div>
          </div>
        </div>

        <div class="custom-modal-footer d-flex justify-content-end gap-2 p-3 border-top bg-light">
          <button
            type="button"
            class="btn btn-secondary btn-sm px-3"
            @click="showUploadPhotoModal = false"
          >
            Cancel
          </button>
          <button
            type="button"
            class="btn btn-primary btn-sm px-4 d-inline-flex align-items-center gap-2"
            :disabled="!modalStudentId || !modalFile || modalUploading"
            @click="submitModalPhotoUpload"
          >
            <span v-if="modalUploading" class="spinner-border spinner-border-sm"></span>
            <i v-else class="bi bi-cloud-arrow-up-fill"></i>
            <span>Upload Picture</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Non-print Header & Breadcrumb -->
    <div class="d-print-none">
      <AppBreadcrumb
        :items="[
          { label: 'Dashboard', route: '/dashboard' },
          { label: 'Students', route: '/students' },
          { label: 'ID Card Generator', active: true }
        ]"
      />

      <!-- Page Top Header -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
          <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-person-badge-fill text-primary"></i>
            Automated Student ID Card Generator
          </h1>
          <p class="text-muted small mb-0">
            Design customized institution badges, auto-generate QR barcodes, and batch print student ID cards in high resolution.
          </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <!-- Prominent "Upload Student Photo" Button -->
          <button
            type="button"
            class="btn btn-outline-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
            @click="openUploadPhotoModal(selectedPreviewStudentId !== 'demo' ? previewStudent : null)"
          >
            <i class="bi bi-camera-fill"></i>
            <span>Upload Student Photo</span>
          </button>

          <button
            v-if="activeTab === 'designer'"
            class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm"
            :disabled="savingTemplate"
            @click="saveTemplate"
          >
            <span v-if="savingTemplate" class="spinner-border spinner-border-sm"></span>
            <i v-else class="bi bi-floppy2-fill"></i>
            <span>Save Card Design</span>
          </button>

          <button
            v-if="activeTab === 'batch'"
            class="btn btn-success d-inline-flex align-items-center gap-2 px-4 py-2 shadow-sm fw-semibold"
            :disabled="selectedStudentIds.length === 0"
            @click="triggerPrint"
          >
            <i class="bi bi-printer-fill fs-5"></i>
            <span>Print Selected ({{ selectedStudentIds.length }})</span>
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2">
          <ul class="nav nav-pills custom-nav-pills gap-2">
            <li class="nav-item">
              <button
                class="nav-link d-flex align-items-center gap-2"
                :class="{ active: activeTab === 'designer' }"
                @click="activeTab = 'designer'"
              >
                <i class="bi bi-palette-fill"></i>
                <span class="fw-semibold">1. Card Designer & Customizer</span>
              </button>
            </li>
            <li class="nav-item">
              <button
                class="nav-link d-flex align-items-center gap-2"
                :class="{ active: activeTab === 'batch' }"
                @click="switchToBatchTab"
              >
                <i class="bi bi-people-fill"></i>
                <span class="fw-semibold">2. Generate & Batch Print</span>
                <span class="badge bg-primary rounded-pill ms-1">{{ students.length }}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- TAB 1: CARD DESIGNER & LIVE CUSTOMIZER -->
    <div v-show="activeTab === 'designer'" class="d-print-none">
      <div class="row g-4">
        <!-- Controls Column -->
        <div class="col-lg-7 col-xl-8">
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
              <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-primary"></i>
                Layout, Themes & Dimensions
              </h5>
            </div>
            <div class="card-body">
              <!-- Orientation Toggle -->
              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <label class="form-label fw-semibold text-secondary small">Card Orientation</label>
                  <div class="d-flex gap-2">
                    <button
                      type="button"
                      class="btn flex-fill d-flex flex-column align-items-center justify-content-center p-3 border-2"
                      :class="template.layout === 'vertical' ? 'btn-outline-primary active' : 'btn-outline-secondary'"
                      @click="template.layout = 'vertical'"
                    >
                      <i class="bi bi-phone fs-3 mb-1"></i>
                      <span class="fw-bold">Vertical (Portrait)</span>
                      <small class="text-muted">54mm × 85.6mm (Lanyard Badge)</small>
                    </button>
                    <button
                      type="button"
                      class="btn flex-fill d-flex flex-column align-items-center justify-content-center p-3 border-2"
                      :class="template.layout === 'horizontal' ? 'btn-outline-primary active' : 'btn-outline-secondary'"
                      @click="template.layout = 'horizontal'"
                    >
                      <i class="bi bi-phone-landscape fs-3 mb-1"></i>
                      <span class="fw-bold">Horizontal (Landscape)</span>
                      <small class="text-muted">85.6mm × 54mm (Pocket Clip)</small>
                    </button>
                  </div>
                </div>

                <!-- Theme Style -->
                <div class="col-sm-6">
                  <label class="form-label fw-semibold text-secondary small">Visual Theme Style</label>
                  <div class="row g-2">
                    <div
                      v-for="th in themes"
                      :key="th.id"
                      class="col-6"
                    >
                      <div
                        class="theme-card p-2 border rounded-3 cursor-pointer text-center"
                        :class="{ 'border-primary bg-primary-subtle text-primary fw-bold': template.theme === th.id }"
                        @click="template.theme = th.id"
                      >
                        <i :class="th.icon" class="fs-5 d-block mb-1"></i>
                        <span class="small">{{ th.name }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Color Palette -->
              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small d-flex justify-content-between">
                    <span>Primary Brand Color</span>
                    <span class="font-mono text-muted">{{ template.primary_color }}</span>
                  </label>
                  <div class="input-group">
                    <input
                      type="color"
                      v-model="template.primary_color"
                      class="form-control form-control-color w-100"
                    />
                  </div>
                  <div class="d-flex gap-1 mt-2 flex-wrap">
                    <button
                      v-for="c in colorPresetsPrimary"
                      :key="c"
                      type="button"
                      class="color-preset-dot"
                      :style="{ backgroundColor: c }"
                      @click="template.primary_color = c"
                    ></button>
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small d-flex justify-content-between">
                    <span>Accent Highlight</span>
                    <span class="font-mono text-muted">{{ template.accent_color }}</span>
                  </label>
                  <div class="input-group">
                    <input
                      type="color"
                      v-model="template.accent_color"
                      class="form-control form-control-color w-100"
                    />
                  </div>
                  <div class="d-flex gap-1 mt-2 flex-wrap">
                    <button
                      v-for="c in colorPresetsAccent"
                      :key="c"
                      type="button"
                      class="color-preset-dot"
                      :style="{ backgroundColor: c }"
                      @click="template.accent_color = c"
                    ></button>
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold text-secondary small d-flex justify-content-between">
                    <span>Card Background</span>
                    <span class="font-mono text-muted">{{ template.bg_color }}</span>
                  </label>
                  <div class="input-group">
                    <input
                      type="color"
                      v-model="template.bg_color"
                      class="form-control form-control-color w-100"
                    />
                  </div>
                  <div class="d-flex gap-1 mt-2 flex-wrap">
                    <button
                      v-for="c in ['#ffffff', '#f8fafc', '#fffbeb', '#f0fdf4']"
                      :key="c"
                      type="button"
                      class="color-preset-dot border"
                      :style="{ backgroundColor: c }"
                      @click="template.bg_color = c"
                    ></button>
                  </div>
                </div>
              </div>

              <!-- Institution Branding Info -->
              <h6 class="fw-bold text-dark border-top pt-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i>
                School Branding & Header Info
              </h6>
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted">School / Institution Name</label>
                  <input
                    type="text"
                    v-model="template.header_title"
                    class="form-control"
                    placeholder="e.g. St. George English Day School"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted">Header Subtitle / Tagline</label>
                  <input
                    type="text"
                    v-model="template.header_subtitle"
                    class="form-control"
                    placeholder="e.g. STUDENT IDENTITY CARD"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted">Academic Session</label>
                  <input
                    type="text"
                    v-model="template.academic_session"
                    class="form-control"
                    placeholder="e.g. 2026 - 2027"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted">Contact Info Strip (Phone & Email)</label>
                  <input
                    type="text"
                    v-model="template.contact_info"
                    class="form-control"
                    placeholder="e.g. Ph: +91 90079 63069 | info@school.edu"
                  />
                </div>
              </div>

              <!-- Logo & Signature Uploads -->
              <div class="row g-3 mb-4">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted d-flex justify-content-between">
                    <span>School Emblem / Logo</span>
                    <span v-if="template.logo_url" class="text-success small"><i class="bi bi-check-circle-fill"></i> Loaded</span>
                  </label>
                  <div class="d-flex align-items-center gap-2">
                    <div
                      class="border rounded p-1 bg-light d-flex align-items-center justify-content-center"
                      style="width: 48px; height: 48px; flex-shrink: 0;"
                    >
                      <img
                        v-if="template.logo_url"
                        :src="template.logo_url"
                        alt="Logo"
                        style="max-width: 100%; max-height: 100%; object-fit: contain;"
                      />
                      <i v-else class="bi bi-image text-muted fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                      <input
                        type="file"
                        ref="logoFileInput"
                        class="form-control form-control-sm"
                        accept="image/*"
                        @change="handleLogoUpload"
                      />
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold text-muted d-flex justify-content-between">
                    <span>Principal / Authority Signature</span>
                    <span v-if="template.signature_url" class="text-success small"><i class="bi bi-check-circle-fill"></i> Signed</span>
                  </label>
                  <div class="d-flex align-items-center gap-2">
                    <div
                      class="border rounded p-1 bg-light d-flex align-items-center justify-content-center"
                      style="width: 48px; height: 48px; flex-shrink: 0;"
                    >
                      <img
                        v-if="template.signature_url"
                        :src="template.signature_url"
                        alt="Sig"
                        style="max-width: 100%; max-height: 100%; object-fit: contain;"
                      />
                      <i v-else class="bi bi-pen text-muted fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                      <input
                        type="file"
                        ref="sigFileInput"
                        class="form-control form-control-sm"
                        accept="image/*"
                        @change="handleSigUpload"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Student Details & Field Toggles -->
              <h6 class="fw-bold text-dark border-top pt-3 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-check2-square text-primary"></i>
                Student Photo & Visible Information
              </h6>

              <div class="row g-3 mb-4">
                <div class="col-md-4">
                  <label class="form-label small fw-semibold text-muted">Photo Frame Shape</label>
                  <select v-model="template.photo_shape" class="form-select form-select-sm">
                    <option value="rounded">Soft Rounded (Standard)</option>
                    <option value="circle">Circular Portrait</option>
                    <option value="square">Sharp Square</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label small fw-semibold text-muted">Scan Barcode / QR Code</label>
                  <select v-model="template.barcode_type" class="form-select form-select-sm">
                    <option value="qr">QR Code (Mobile & Scanner Ready)</option>
                    <option value="barcode">Linear Barcode (Code128)</option>
                    <option value="none">Disabled (No Barcode)</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label small fw-semibold text-muted">Signatory Title</label>
                  <input
                    type="text"
                    v-model="template.signatory_title"
                    class="form-control form-control-sm"
                    placeholder="e.g. Principal"
                  />
                </div>
              </div>

              <!-- Field Checkbox Switches -->
              <div class="bg-light p-3 rounded-3 border">
                <div class="row g-3">
                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swRoll"
                        v-model="template.show_roll_no"
                      />
                      <label class="form-check-label small fw-semibold" for="swRoll">Roll Number</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swAdm"
                        v-model="template.show_admission_no"
                      />
                      <label class="form-check-label small fw-semibold" for="swAdm">Admission Number</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swClass"
                        v-model="template.show_class_section"
                      />
                      <label class="form-check-label small fw-semibold" for="swClass">Class & Section</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swBlood"
                        v-model="template.show_blood_group"
                      />
                      <label class="form-check-label small fw-semibold" for="swBlood">Blood Group Badge</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swDob"
                        v-model="template.show_dob"
                      />
                      <label class="form-check-label small fw-semibold" for="swDob">Date of Birth (DOB)</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swFather"
                        v-model="template.show_father_name"
                      />
                      <label class="form-check-label small fw-semibold" for="swFather">Guardian / Father Name</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swPhone"
                        v-model="template.show_emergency_phone"
                      />
                      <label class="form-check-label small fw-semibold" for="swPhone">Emergency Contact No</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swAddr"
                        v-model="template.show_address"
                      />
                      <label class="form-check-label small fw-semibold" for="swAddr">Residential Address</label>
                    </div>
                  </div>

                  <div class="col-sm-6 col-md-4">
                    <div class="form-check form-switch">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        id="swBack"
                        v-model="template.show_back_side"
                      />
                      <label class="form-check-label small fw-semibold text-primary" for="swBack">Print Card Back Side</label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Back Side Instructions Text -->
              <div v-if="template.show_back_side" class="mt-3">
                <label class="form-label small fw-semibold text-muted">Card Back Instructions & Rules (Line by Line)</label>
                <textarea
                  v-model="template.back_instructions"
                  class="form-control"
                  rows="3"
                  placeholder="Enter institution instructions for card holder..."
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Live Sticky Preview Column -->
        <div class="col-lg-5 col-xl-4">
          <div class="card border-0 shadow-sm sticky-preview-card">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
              <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-eye-fill text-primary"></i>
                Live Interactive Preview
              </h6>
              <div class="btn-group btn-group-sm">
                <button
                  type="button"
                  class="btn"
                  :class="!previewIsBack ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="previewIsBack = false"
                >
                  Front
                </button>
                <button
                  type="button"
                  class="btn"
                  :class="previewIsBack ? 'btn-primary' : 'btn-outline-secondary'"
                  @click="previewIsBack = true"
                >
                  Back
                </button>
              </div>
            </div>

            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4 bg-slate-50">
              <!-- Preview Student Selector -->
              <div class="w-100 mb-3">
                <label class="form-label small text-muted fw-semibold">Preview with Student Data:</label>
                <select v-model="selectedPreviewStudentId" class="form-select form-select-sm shadow-none">
                  <option value="demo">✨ Demo Student Profile</option>
                  <option
                    v-for="s in students.slice(0, 30)"
                    :key="s.id"
                    :value="s.id"
                  >
                    {{ s.full_name }} ({{ s.class_name }}-{{ s.section_name }} | {{ s.admission_no }})
                  </option>
                </select>
              </div>

              <!-- Live Card Rendering Container -->
              <div class="preview-card-viewport my-2">
                <StudentCard
                  :template="template"
                  :student="previewStudent"
                  :is-back="previewIsBack"
                  :allow-photo-upload="true"
                  @upload-photo="openUploadPhotoModal"
                />
              </div>

              <!-- Direct Photo upload button under preview card -->
              <div class="mt-3 text-center w-100">
                <button
                  type="button"
                  class="btn btn-sm btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 py-2"
                  @click="openUploadPhotoModal(selectedPreviewStudentId !== 'demo' ? previewStudent : null)"
                >
                  <i class="bi bi-camera-fill"></i>
                  <span>Upload Student Picture</span>
                </button>
              </div>

              <!-- Card Dimension Specs -->
              <div class="text-center mt-3 text-muted small">
                <span class="badge bg-light text-dark border">
                  {{ template.layout === 'vertical' ? 'Standard 54mm × 85.6mm' : 'Standard 85.6mm × 54mm' }}
                </span>
                <span class="badge bg-light text-dark border ms-1">
                  CR-80 PVC & Paper Ready
                </span>
              </div>
            </div>

            <div class="card-footer bg-white border-top p-3 d-flex gap-2">
              <button
                class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                :disabled="savingTemplate"
                @click="saveTemplate"
              >
                <span v-if="savingTemplate" class="spinner-border spinner-border-sm"></span>
                <i v-else class="bi bi-check-circle-fill"></i>
                <span>Save Design Template</span>
              </button>
              <button
                class="btn btn-outline-secondary"
                title="Switch to Batch Print"
                @click="switchToBatchTab"
              >
                <i class="bi bi-arrow-right"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: GENERATE & BATCH PRINT (Mounted only when active) -->
    <div v-if="activeTab === 'batch'">
      <!-- Filter Bar (Non-print) -->
      <div class="card border-0 shadow-sm mb-4 d-print-none">
        <div class="card-body p-3">
          <div class="row g-3 align-items-end">
            <!-- Class Filter -->
            <div class="col-md-3 col-sm-6">
              <label class="form-label small fw-semibold text-muted mb-1">Filter by Class</label>
              <select v-model="filterClassId" class="form-select form-select-sm" @change="currentPage = 1">
                <option value="">All Classes ({{ students.length }})</option>
                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>

            <!-- Section Filter -->
            <div class="col-md-3 col-sm-6">
              <label class="form-label small fw-semibold text-muted mb-1">Section</label>
              <select v-model="filterSectionId" class="form-select form-select-sm" :disabled="!filterClassId" @change="currentPage = 1">
                <option value="">All Sections</option>
                <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>

            <!-- Search Filter -->
            <div class="col-md-4 col-sm-8">
              <label class="form-label small fw-semibold text-muted mb-1">Search Student</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input
                  type="text"
                  v-model="searchQuery"
                  class="form-control"
                  placeholder="Search by name, roll no, admission no..."
                  @input="currentPage = 1"
                />
              </div>
            </div>

            <!-- Reset Button -->
            <div class="col-md-2 col-sm-4">
              <button class="btn btn-outline-secondary btn-sm w-100" @click="resetFilters">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Bulk Selection Toolbar (Non-print) -->
      <div class="card border-0 shadow-sm mb-4 bg-white d-print-none">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                id="selectAllCheck"
                :checked="isAllSelected"
                :indeterminate.prop="isIndeterminate"
                @change="toggleSelectAll"
              />
              <label class="form-check-label fw-bold text-dark cursor-pointer" for="selectAllCheck">
                Select All Visible ({{ filteredStudents.length }})
              </label>
            </div>

            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-6">
              <i class="bi bi-check2-all me-1"></i>
              {{ selectedStudentIds.length }} Cards Selected
            </span>
          </div>

          <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Upload Photo Shortcut in Batch tab -->
            <button
              type="button"
              class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
              @click="openUploadPhotoModal()"
            >
              <i class="bi bi-camera-fill"></i>
              <span>Upload Photo</span>
            </button>

            <!-- Print Sheet Layout Selector -->
            <div class="btn-group btn-group-sm">
              <button
                type="button"
                class="btn"
                :class="printSheetMode === 'a4_sheet' ? 'btn-dark' : 'btn-outline-secondary'"
                @click="printSheetMode = 'a4_sheet'"
              >
                <i class="bi bi-grid-3x3-gap-fill me-1"></i>
                A4 Sheet (Multi-card Grid)
              </button>
              <button
                type="button"
                class="btn"
                :class="printSheetMode === 'single_card' ? 'btn-dark' : 'btn-outline-secondary'"
                @click="printSheetMode = 'single_card'"
              >
                <i class="bi bi-credit-card-2-front-fill me-1"></i>
                Single PVC Card Printer
              </button>
            </div>

            <!-- Print Card Face Selector -->
            <div class="btn-group btn-group-sm">
              <button
                type="button"
                class="btn"
                :class="printFaceMode === 'front' ? 'btn-primary' : 'btn-outline-secondary'"
                @click="printFaceMode = 'front'"
              >
                Front Only
              </button>
              <button
                v-if="template.show_back_side"
                type="button"
                class="btn"
                :class="printFaceMode === 'back' ? 'btn-primary' : 'btn-outline-secondary'"
                @click="printFaceMode = 'back'"
              >
                Back Only
              </button>
              <button
                v-if="template.show_back_side"
                type="button"
                class="btn"
                :class="printFaceMode === 'both' ? 'btn-primary' : 'btn-outline-secondary'"
                @click="printFaceMode = 'both'"
              >
                Both Sides (Duplex)
              </button>
            </div>

            <!-- Big Print Button -->
            <button
              class="btn btn-success btn-sm px-4 fw-semibold shadow-sm d-flex align-items-center gap-2"
              :disabled="selectedStudentIds.length === 0"
              @click="triggerPrint"
            >
              <i class="bi bi-printer-fill fs-6"></i>
              <span>Print Selected ({{ selectedStudentIds.length }})</span>
            </button>
          </div>
        </div>
      </div>

      <!-- No Students Found State -->
      <div v-if="filteredStudents.length === 0" class="card border-0 shadow-sm p-5 text-center d-print-none">
        <i class="bi bi-people text-muted display-4 mb-3"></i>
        <h5 class="fw-bold text-dark">No Students Found</h5>
        <p class="text-muted small">No active students match your selected class/section filter or search keyword.</p>
      </div>

      <!-- ON-SCREEN PAGINATED CARDS GALLERY (Smooth 12 cards/page for high performance) -->
      <div v-else class="d-print-none mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold text-dark mb-0">
            Preview Gallery (Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, filteredStudents.length) }} of {{ filteredStudents.length }})
          </h6>

          <!-- Quick Pagination Controls -->
          <div v-if="totalPages > 1" class="d-flex align-items-center gap-2">
            <button
              class="btn btn-sm btn-outline-secondary"
              :disabled="currentPage <= 1"
              @click="currentPage--"
            >
              <i class="bi bi-chevron-left"></i> Previous
            </button>
            <span class="small fw-semibold text-muted">Page {{ currentPage }} / {{ totalPages }}</span>
            <button
              class="btn btn-sm btn-outline-secondary"
              :disabled="currentPage >= totalPages"
              @click="currentPage++"
            >
              Next <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        </div>

        <div class="id-cards-print-grid">
          <div
            v-for="stu in paginatedStudents"
            :key="stu.id"
            class="id-card-print-item"
          >
            <!-- Checkbox for on-screen selection -->
            <div class="card-select-overlay">
              <input
                type="checkbox"
                class="form-check-input"
                :value="stu.id"
                v-model="selectedStudentIds"
              />
            </div>

            <StudentCard
              :template="template"
              :student="stu"
              :is-back="false"
              :allow-photo-upload="true"
              @upload-photo="openUploadPhotoModal"
            />
          </div>
        </div>

        <!-- Bottom Pagination -->
        <div v-if="totalPages > 1" class="d-flex justify-content-center align-items-center gap-2 mt-4">
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="currentPage <= 1"
            @click="currentPage--"
          >
            <i class="bi bi-chevron-left"></i> Previous
          </button>
          <span class="small fw-semibold text-muted px-2">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            class="btn btn-sm btn-outline-secondary"
            :disabled="currentPage >= totalPages"
            @click="currentPage++"
          >
            Next <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>

      <!-- PRINT-READY CONTAINER (Displayed only during print) -->
      <div
        id="printable-id-cards-area"
        class="id-cards-print-grid print-only-area"
        :class="[
          'mode-' + printSheetMode,
          'layout-' + template.layout
        ]"
      >
        <template v-for="stu in printSelectedStudents" :key="stu.id">
          <!-- Card Front -->
          <div
            v-if="printFaceMode === 'front' || printFaceMode === 'both'"
            class="id-card-print-item"
          >
            <StudentCard
              :template="template"
              :student="stu"
              :is-back="false"
              :allow-photo-upload="false"
            />
          </div>

          <!-- Card Back -->
          <div
            v-if="(printFaceMode === 'back' || printFaceMode === 'both') && template.show_back_side"
            class="id-card-print-item is-back-item"
          >
            <StudentCard
              :template="template"
              :student="stu"
              :is-back="true"
              :allow-photo-upload="false"
            />
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/api/axios';
import Swal from 'sweetalert2';
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue';
import StudentCard from './StudentCard.vue';

// Active Tab ('designer' | 'batch')
const activeTab = ref('designer');

// State
const loading = ref(false);
const savingTemplate = ref(false);

const filterClassId = ref('');
const filterSectionId = ref('');
const searchQuery = ref('');

const selectedStudentIds = ref([]);
const printSheetMode = ref('a4_sheet'); // 'a4_sheet' | 'single_card'
const printFaceMode = ref('front'); // 'front' | 'back' | 'both'

const previewIsBack = ref(false);
const selectedPreviewStudentId = ref('demo');

const logoFileInput = ref(null);
const sigFileInput = ref(null);
const studentPhotoInputRef = ref(null);
const activeUploadStudent = ref(null);

// Dedicated Photo Upload Modal State
const showUploadPhotoModal = ref(false);
const modalStudentId = ref('');
const modalStudentFilter = ref('');
const modalFile = ref(null);
const modalFilePreview = ref('');
const modalUploading = ref(false);

// Pagination for on-screen batch preview
const currentPage = ref(1);
const perPage = ref(12);

// Template Model
const template = ref({
  layout: 'vertical',
  theme: 'modern',
  primary_color: '#1d4ed8',
  accent_color: '#f59e0b',
  bg_color: '#ffffff',
  header_title: 'Institution Name',
  header_subtitle: 'STUDENT IDENTITY CARD',
  academic_session: '2026 - 2027',
  contact_info: 'Ph: +91 90079 63069 | info@school.edu',
  logo_url: '',
  signature_url: '',
  signatory_title: 'Principal',
  photo_shape: 'rounded',
  barcode_type: 'qr',
  show_roll_no: true,
  show_admission_no: true,
  show_class_section: true,
  show_blood_group: true,
  show_dob: true,
  show_father_name: true,
  show_emergency_phone: true,
  show_address: false,
  show_back_side: true,
  back_instructions:
    "1. This Identity Card is non-transferable and must be displayed while on school premises.\n2. In case of loss or mutilation, immediately inform the school administrative office.\n3. If found, kindly return to the Principal's Office or nearest police station.",
});

// Data Lists
const students = ref([]);
const classes = ref([]);
const sections = ref([]);

// Presets
const themes = [
  { id: 'modern', name: 'Modern Gradient', icon: 'bi-gem' },
  { id: 'classic', name: 'Academic Classic', icon: 'bi-mortarboard' },
  { id: 'minimal', name: 'Minimal Elite', icon: 'bi-square' },
  { id: 'tech', name: 'Tech Cyber', icon: 'bi-cpu' },
];

const colorPresetsPrimary = [
  '#1d4ed8', // Royal Blue
  '#0f766e', // Teal
  '#991b1b', // Crimson
  '#7c3aed', // Purple
  '#0f172a', // Slate Dark
  '#065f46', // Emerald Forest
];

const colorPresetsAccent = [
  '#f59e0b', // Amber Gold
  '#06b6d4', // Cyan
  '#ec4899', // Pink
  '#10b981', // Green
  '#6366f1', // Indigo
];

// Demo Student for Live Designer Preview
const demoStudent = {
  id: 'demo',
  full_name: 'Aarav Sharma',
  first_name: 'Aarav',
  last_name: 'Sharma',
  admission_no: 'ADM-2026-001',
  roll_no: '12',
  class_name: 'Class 5',
  section_name: 'A',
  dob: '15 May 2015',
  blood_group: 'B+',
  father_name: 'Rajesh Sharma',
  emergency_phone: '+91 98310 12345',
  photo_url: '/images/no-image.png',
  address: '14/B, Park Street, Kolkata - 700016',
  scan_code: 'ADM-2026-001',
};

// Preview Student computed
const previewStudent = computed(() => {
  if (selectedPreviewStudentId.value === 'demo') {
    return demoStudent;
  }
  const found = students.value.find((s) => s.id === Number(selectedPreviewStudentId.value));
  return found || demoStudent;
});

// Modal filtered students
const modalFilteredStudents = computed(() => {
  if (!modalStudentFilter.value) {
    return students.value.slice(0, 100);
  }
  const q = modalStudentFilter.value.toLowerCase().trim();
  return students.value
    .filter(
      (s) =>
        s.full_name?.toLowerCase().includes(q) ||
        s.admission_no?.toLowerCase().includes(q) ||
        String(s.roll_no).toLowerCase().includes(q)
    )
    .slice(0, 100);
});

const selectedModalStudent = computed(() => {
  return students.value.find((s) => s.id === Number(modalStudentId.value)) || null;
});

// Filtered Sections based on selected class
const filteredSections = computed(() => {
  if (!filterClassId.value) return [];
  return sections.value.filter((s) => Number(s.class_id) === Number(filterClassId.value));
});

// Filtered Students based on search and class filters
const filteredStudents = computed(() => {
  return students.value.filter((s) => {
    if (filterClassId.value && Number(s.class_id) !== Number(filterClassId.value)) {
      return false;
    }
    if (filterSectionId.value && Number(s.section_id) !== Number(filterSectionId.value)) {
      return false;
    }
    if (searchQuery.value) {
      const q = searchQuery.value.toLowerCase().trim();
      const matchName = s.full_name?.toLowerCase().includes(q);
      const matchAdm = s.admission_no?.toLowerCase().includes(q);
      const matchRoll = String(s.roll_no).toLowerCase().includes(q);
      if (!matchName && !matchAdm && !matchRoll) return false;
    }
    return true;
  });
});

// Total pages for screen pagination
const totalPages = computed(() => {
  return Math.ceil(filteredStudents.value.length / perPage.value) || 1;
});

// Paginated students for smooth screen rendering
const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredStudents.value.slice(start, start + perPage.value);
});

// Selected students for print
const printSelectedStudents = computed(() => {
  if (selectedStudentIds.value.length === 0) {
    return paginatedStudents.value;
  }
  return filteredStudents.value.filter((s) => selectedStudentIds.value.includes(s.id));
});

// Checkbox select all state
const isAllSelected = computed(() => {
  if (filteredStudents.value.length === 0) return false;
  return filteredStudents.value.every((s) => selectedStudentIds.value.includes(s.id));
});

const isIndeterminate = computed(() => {
  if (filteredStudents.value.length === 0) return false;
  const count = filteredStudents.value.filter((s) => selectedStudentIds.value.includes(s.id)).length;
  return count > 0 && count < filteredStudents.value.length;
});

const toggleSelectAll = (e) => {
  if (e.target.checked) {
    const visibleIds = filteredStudents.value.map((s) => s.id);
    selectedStudentIds.value = Array.from(new Set([...selectedStudentIds.value, ...visibleIds]));
  } else {
    const visibleIds = new Set(filteredStudents.value.map((s) => s.id));
    selectedStudentIds.value = selectedStudentIds.value.filter((id) => !visibleIds.has(id));
  }
};

const resetFilters = () => {
  filterClassId.value = '';
  filterSectionId.value = '';
  searchQuery.value = '';
  currentPage.value = 1;
};

const switchToBatchTab = () => {
  activeTab.value = 'batch';
  currentPage.value = 1;
  if (selectedStudentIds.value.length === 0 && paginatedStudents.value.length > 0) {
    selectedStudentIds.value = paginatedStudents.value.map((s) => s.id);
  }
};

// Fetch Template and Students from Backend
const fetchTemplate = async () => {
  try {
    const res = await axios.get('/id-cards/template');
    if (res.data?.success && res.data.template) {
      const t = res.data.template;
      template.value = {
        ...template.value,
        ...t,
      };
      if (res.data.school?.name && !t.header_title) {
        template.value.header_title = res.data.school.name;
      }
      if (res.data.school?.logo && !t.logo_url) {
        template.value.logo_url = res.data.school.logo;
      }
    }
  } catch (err) {
    console.error('Failed to load ID card template:', err);
  }
};

const fetchStudents = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/id-cards/students');
    if (res.data?.success) {
      students.value = res.data.students || [];
      classes.value = res.data.classes || [];
      sections.value = res.data.sections || [];
      if (students.value.length > 0) {
        selectedStudentIds.value = students.value.slice(0, 12).map((s) => s.id);
      }
    }
  } catch (err) {
    console.error('Failed to fetch students for ID cards:', err);
  } finally {
    loading.value = false;
  }
};

// Save Template to Backend
const saveTemplate = async () => {
  savingTemplate.value = true;
  try {
    const res = await axios.post('/id-cards/template', template.value);
    if (res.data?.success) {
      Swal.fire({
        icon: 'success',
        title: 'Template Saved!',
        text: 'Your customized student ID card design has been saved for this institution.',
        timer: 1800,
        showConfirmButton: false,
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Save Failed',
      text: err.response?.data?.message || 'Could not save ID card template.',
    });
  } finally {
    savingTemplate.value = false;
  }
};

// Handle Logo Upload
const handleLogoUpload = async (e) => {
  const file = e.target.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('logo', file);

  try {
    const res = await axios.post('/id-cards/upload-logo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    if (res.data?.success && res.data.url) {
      template.value.logo_url = res.data.url;
      Swal.fire({
        icon: 'success',
        title: 'Emblem Updated',
        text: 'School logo updated successfully for ID card.',
        timer: 1500,
        showConfirmButton: false,
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: err.response?.data?.message || 'Failed to upload emblem image.',
    });
  }
};

// Handle Signature Upload
const handleSigUpload = async (e) => {
  const file = e.target.files?.[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('signature', file);

  try {
    const res = await axios.post('/id-cards/upload-signature', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    if (res.data?.success && res.data.url) {
      template.value.signature_url = res.data.url;
      Swal.fire({
        icon: 'success',
        title: 'Signature Saved',
        text: 'Principal signature uploaded and bound to ID card.',
        timer: 1500,
        showConfirmButton: false,
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: err.response?.data?.message || 'Failed to upload signature image.',
    });
  }
};

// Modal Photo Upload Controls
const openUploadPhotoModal = (student = null) => {
  modalFile.value = null;
  modalFilePreview.value = '';
  modalStudentFilter.value = '';

  if (student && student.id && student.id !== 'demo') {
    modalStudentId.value = student.id;
  } else if (selectedPreviewStudentId.value !== 'demo') {
    modalStudentId.value = Number(selectedPreviewStudentId.value);
  } else if (students.value.length > 0) {
    modalStudentId.value = students.value[0].id;
  } else {
    modalStudentId.value = '';
  }

  showUploadPhotoModal.value = true;
};

const onModalFileChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  modalFile.value = file;
  modalFilePreview.value = URL.createObjectURL(file);
};

const submitModalPhotoUpload = async () => {
  if (!modalStudentId.value || !modalFile.value) return;

  const stu = students.value.find((s) => s.id === Number(modalStudentId.value));
  const studentName = stu?.full_name || 'Student';

  const formData = new FormData();
  formData.append('photo', modalFile.value);

  modalUploading.value = true;
  try {
    const res = await axios.post(`/id-cards/students/${modalStudentId.value}/photo`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (res.data?.success && res.data.photo_url) {
      // Update in local lists
      if (stu) {
        stu.photo_url = res.data.photo_url;
      }
      const foundInList = students.value.find((s) => s.id === Number(modalStudentId.value));
      if (foundInList) {
        foundInList.photo_url = res.data.photo_url;
      }

      showUploadPhotoModal.value = false;

      Swal.fire({
        icon: 'success',
        title: 'Picture Updated!',
        text: `Photo for ${studentName} updated successfully on their ID card.`,
        timer: 1800,
        showConfirmButton: false,
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: err.response?.data?.message || 'Could not upload student picture.',
    });
  } finally {
    modalUploading.value = false;
  }
};

// Single-click Student Photo Upload Trigger
const handleStudentPhotoSelected = async (e) => {
  const file = e.target.files?.[0];
  if (!file || !activeUploadStudent.value) return;

  const stu = activeUploadStudent.value;
  const formData = new FormData();
  formData.append('photo', file);

  try {
    Swal.fire({
      title: 'Uploading Photo...',
      text: `Updating picture for ${stu.full_name}`,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const res = await axios.post(`/id-cards/students/${stu.id}/photo`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (res.data?.success && res.data.photo_url) {
      stu.photo_url = res.data.photo_url;
      const foundInList = students.value.find((s) => s.id === stu.id);
      if (foundInList) {
        foundInList.photo_url = res.data.photo_url;
      }

      Swal.fire({
        icon: 'success',
        title: 'Photo Updated!',
        text: `Picture for ${stu.full_name} is now updated on their identity card.`,
        timer: 1800,
        showConfirmButton: false,
      });
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: err.response?.data?.message || 'Could not upload student picture.',
    });
  } finally {
    if (studentPhotoInputRef.value) {
      studentPhotoInputRef.value.value = '';
    }
  }
};

// Trigger Browser Print
const triggerPrint = () => {
  if (selectedStudentIds.value.length === 0) {
    Swal.fire('No Cards Selected', 'Please select at least one student card to print.', 'warning');
    return;
  }
  window.print();
};

onMounted(async () => {
  await Promise.all([fetchTemplate(), fetchStudents()]);
});
</script>

<style scoped>
.id-cards-page-container {
  padding: 0 0 40px;
}

.custom-nav-pills .nav-link {
  border-radius: 8px;
  padding: 10px 18px;
  color: #475569;
  font-weight: 600;
  border: 1px solid transparent;
  transition: all 0.2s ease;
}

.custom-nav-pills .nav-link.active {
  background-color: #2563eb;
  color: #ffffff;
  box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
}

.theme-card {
  transition: all 0.2s ease;
  user-select: none;
}
.theme-card:hover {
  border-color: #93c5fd;
  transform: translateY(-1px);
}

.color-preset-dot {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: transform 0.15s ease;
}
.color-preset-dot:hover {
  transform: scale(1.15);
}

.sticky-preview-card {
  position: sticky;
  top: 20px;
}

.bg-slate-50 {
  background-color: #f8fafc;
}

.preview-card-viewport {
  filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.12));
  transition: transform 0.2s ease;
}

/* ==========================================================
   CARDS DISPLAY & PRINT GRID (SCREEN VIEW)
   ========================================================== */
.id-cards-print-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  align-items: flex-start;
  justify-content: flex-start;
}

.id-card-print-item {
  position: relative;
  transition: transform 0.2s ease;
}

.id-card-print-item:hover {
  transform: translateY(-2px);
}

.card-select-overlay {
  position: absolute;
  top: -8px;
  left: -8px;
  z-index: 10;
  background: #ffffff;
  border-radius: 50%;
  padding: 2px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
}

.card-select-overlay input {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

/* Modal Styling */
.custom-modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.custom-modal-dialog {
  background: #ffffff;
  border-radius: 12px;
  width: 100%;
  max-width: 540px;
  overflow: hidden;
  animation: modalFadeIn 0.2s ease-out;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.photo-preview-frame {
  width: 76px;
  height: 76px;
  border-radius: 10px;
  overflow: hidden;
  border: 2px solid #2563eb;
  background: #ffffff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
}

.photo-preview-frame .preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Hidden on screen, shown when printing */
.print-only-area {
  display: none;
}

/* ==========================================================
   PRINT MEDIA STYLESHEET (@media print)
   ========================================================== */
@media print {
  body {
    background: #ffffff !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  .d-print-none,
  header,
  nav,
  aside,
  footer,
  .main-sidebar,
  .app-header {
    display: none !important;
  }

  .id-cards-page-container {
    padding: 0 !important;
    margin: 0 !important;
  }

  .print-only-area {
    display: flex !important;
  }

  .id-cards-print-grid.mode-a4_sheet {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8mm 6mm !important;
    padding: 10mm 8mm !important;
    margin: 0 auto !important;
    justify-content: flex-start !important;
  }

  .id-cards-print-grid.mode-single_card {
    display: block !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  .id-cards-print-grid.mode-single_card .id-card-print-item {
    page-break-after: always !important;
    break-after: page !important;
    margin: 0 !important;
  }

  .id-card-print-item {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
    box-shadow: none !important;
    outline: 0.5px dashed #cbd5e1 !important;
    margin: 0 !important;
  }
}
</style>
