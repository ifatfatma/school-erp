<template>
  <div class="container-fluid py-3">
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Admissions & School Web' },
        { label: 'School Website CMS' }
      ]"
    />

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
      <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-window-sidebar me-2 text-primary"></i> Website Content Management (CMS)</h4>
        <small class="text-muted">Customize website text, contact numbers, hero banners, principal's message, facilities, and notice board</small>
      </div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span v-if="!isSuperAdmin && currentSchool" class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill small">
          <i class="bi bi-building me-1"></i> Managing: <strong>{{ currentSchool.name }}</strong> ({{ currentSchool.code }})
        </span>
        <router-link :to="schoolLiveUrl" target="_blank" class="btn btn-outline-primary rounded-pill btn-sm">
          <i class="bi bi-box-arrow-up-right me-1"></i> View Live School Website
        </router-link>
      </div>
    </div>

    <!-- Super Admin School Switcher Card -->
    <div v-if="isSuperAdmin && allSchools.length > 0" class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-3 border-start border-primary border-4">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
            <i class="bi bi-buildings-fill fs-5"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h6 class="fw-bold mb-0">Select School Website to Manage</h6>
              <span class="badge bg-primary text-white font-monospace small">{{ allSchools.length }} Schools Registered</span>
            </div>
            <small class="text-muted">Choose which institution's public website content, contact info, banners, facilities, and notices you wish to edit.</small>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-grow-1 flex-md-grow-0" style="min-width: 340px;">
          <select v-model="selectedSchoolId" class="form-select form-select-lg fw-semibold border-primary shadow-sm" @change="onSchoolChange">
            <option v-for="s in allSchools" :key="'banner-'+s.id" :value="s.id">
              {{ s.name }} (Code: {{ s.code }}) — {{ s.city || 'Campus' }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Main CMS Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <!-- Tabs Navigation -->
      <div class="card-header bg-white p-3 border-bottom">
        <ul class="nav nav-pills card-header-pills gap-1 flex-wrap">
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'general' }" @click="activeTab = 'general'">
              <i class="bi bi-gear-fill me-1"></i> General & Contact
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'hero' }" @click="activeTab = 'hero'">
              <i class="bi bi-card-heading me-1"></i> Hero Banner
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'about' }" @click="activeTab = 'about'">
              <i class="bi bi-info-circle-fill me-1"></i> About & Vision
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'principal' }" @click="activeTab = 'principal'">
              <i class="bi bi-person-workspace me-1"></i> Principal Message
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'stats' }" @click="activeTab = 'stats'">
              <i class="bi bi-bar-chart-fill me-1"></i> Counters & Stats
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'facilities' }" @click="activeTab = 'facilities'">
              <i class="bi bi-building-fill me-1"></i> Facilities
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'admissions' }" @click="activeTab = 'admissions'">
              <i class="bi bi-mortarboard-fill me-1"></i> Admissions Info
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link py-2 px-3 fw-semibold" :class="{ active: activeTab === 'notices' }" @click="activeTab = 'notices'">
              <i class="bi bi-megaphone-fill me-1"></i> Notice Board
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-4 p-md-5">
        <div v-if="loading" class="text-center py-5">
          <span class="spinner-border spinner-border-sm text-primary me-2"></span> Loading content settings...
        </div>

        <div v-else>
          <!-- TAB 1: General & Contact -->
          <div v-if="activeTab === 'general'">
            <h5 class="fw-bold text-primary mb-3">General Institution & Contact Information</h5>
            <p class="small text-muted mb-4">This information appears across the website header, announcement top-bar, and footer.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">School Name</label>
                <input type="text" v-model="settings.general.school_name" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Tagline / Motto</label>
                <input type="text" v-model="settings.general.tagline" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Short School Description (Footer)</label>
                <textarea v-model="settings.general.description" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Primary Contact Phone</label>
                <input type="text" v-model="settings.general.phone_primary" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Secondary Phone / Helpline</label>
                <input type="text" v-model="settings.general.phone_secondary" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">General Inquiries Email</label>
                <input type="email" v-model="settings.general.email_primary" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Admissions Desk Email</label>
                <input type="email" v-model="settings.general.email_admissions" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Campus Street Address</label>
                <input type="text" v-model="settings.general.address" class="form-control">
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">City</label>
                <input type="text" v-model="settings.general.city" class="form-control">
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">State</label>
                <input type="text" v-model="settings.general.state" class="form-control">
              </div>
              <div class="col-md-2">
                <label class="form-label fw-semibold">PIN Code</label>
                <input type="text" v-model="settings.general.pincode" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Visiting / Office Hours</label>
                <input type="text" v-model="settings.general.office_hours" class="form-control">
              </div>

              <!-- Social Links -->
              <div class="col-12"><hr class="my-3"></div>
              <h6 class="fw-bold mb-2">Social Media Channels</h6>
              <div class="col-md-3">
                <label class="form-label small text-muted"><i class="bi bi-facebook me-1"></i> Facebook URL</label>
                <input type="text" v-model="settings.general.social_facebook" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted"><i class="bi bi-instagram me-1"></i> Instagram URL</label>
                <input type="text" v-model="settings.general.social_instagram" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted"><i class="bi bi-youtube me-1"></i> YouTube URL</label>
                <input type="text" v-model="settings.general.social_youtube" class="form-control form-control-sm">
              </div>
              <div class="col-md-3">
                <label class="form-label small text-muted"><i class="bi bi-twitter-x me-1"></i> X / Twitter URL</label>
                <input type="text" v-model="settings.general.social_twitter" class="form-control form-control-sm">
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('general')">Reset to Defaults</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('general')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save General Settings
              </button>
            </div>
          </div>

          <!-- TAB 2: Hero Banner -->
          <div v-if="activeTab === 'hero'">
            <h5 class="fw-bold text-primary mb-3">Homepage Hero Banner</h5>
            <p class="small text-muted mb-4">Customize the primary headline, banner badges, and call-to-action buttons on the homepage.</p>

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold">Hero Top Badge Text</label>
                <input type="text" v-model="settings.hero.badge_text" class="form-control" placeholder="e.g. Admissions Open for Academic Session 2026-27">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Main Heading (Line 1)</label>
                <input type="text" v-model="settings.hero.heading_line1" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Main Heading Highlight (Line 2)</label>
                <input type="text" v-model="settings.hero.heading_line2" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Lead Subheading / Welcome Paragraph</label>
                <textarea v-model="settings.hero.subheading" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Primary CTA Button Text</label>
                <input type="text" v-model="settings.hero.cta_primary_text" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Primary CTA Link</label>
                <input type="text" v-model="settings.hero.cta_primary_link" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Secondary CTA Button Text</label>
                <input type="text" v-model="settings.hero.cta_secondary_text" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Secondary CTA Link</label>
                <input type="text" v-model="settings.hero.cta_secondary_link" class="form-control">
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

          <!-- TAB 3: About & Vision -->
          <div v-if="activeTab === 'about'">
            <h5 class="fw-bold text-primary mb-3">About Us, Vision & Mission</h5>
            <p class="small text-muted mb-4">Edit the institution's historical background, vision statement, and core mission.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Section Badge</label>
                <input type="text" v-model="settings.about.badge" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Main Heading</label>
                <input type="text" v-model="settings.about.heading" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Subtitle Text</label>
                <input type="text" v-model="settings.about.subheading" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">About School - Paragraph 1</label>
                <textarea v-model="settings.about.paragraph1" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">About School - Paragraph 2</label>
                <textarea v-model="settings.about.paragraph2" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Vision Statement</label>
                <textarea v-model="settings.about.vision" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Mission Statement</label>
                <textarea v-model="settings.about.mission" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('about')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('about')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save About Section
              </button>
            </div>
          </div>

          <!-- TAB 4: Principal Message -->
          <div v-if="activeTab === 'principal'">
            <h5 class="fw-bold text-primary mb-3">Principal's Desk & Leadership Message</h5>
            <p class="small text-muted mb-4">Official message displayed prominently on the About Us page.</p>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Principal's Full Name</label>
                <input type="text" v-model="settings.principal.name" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Section Title</label>
                <input type="text" v-model="settings.principal.title" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Academic Designation</label>
                <input type="text" v-model="settings.principal.designation" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Experience Badge</label>
                <input type="text" v-model="settings.principal.experience" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Principal's Address / Quotation</label>
                <textarea v-model="settings.principal.quote" class="form-control" rows="5"></textarea>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('principal')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('principal')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Principal Message
              </button>
            </div>
          </div>

          <!-- TAB 5: Stats & Counters -->
          <div v-if="activeTab === 'stats'">
            <h5 class="fw-bold text-primary mb-3">Homepage Live Numbers & Metrics</h5>
            <p class="small text-muted mb-4">Highlight your school's achievement milestones to prospective visitors.</p>

            <div class="row g-4">
              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Metric 1</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Number / Value</label>
                    <input type="text" v-model="settings.stats.stat1_number" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat1_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Metric 2</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Number / Value</label>
                    <input type="text" v-model="settings.stats.stat2_number" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat2_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Metric 3</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Number / Value</label>
                    <input type="text" v-model="settings.stats.stat3_number" class="form-control">
                  </div>
                  <div>
                    <label class="form-label small text-muted">Label</label>
                    <input type="text" v-model="settings.stats.stat3_label" class="form-control">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                  <h6 class="fw-bold mb-3">Metric 4</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted">Number / Value</label>
                    <input type="text" v-model="settings.stats.stat4_number" class="form-control">
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

          <!-- TAB 6: Facilities Manager -->
          <div v-if="activeTab === 'facilities'">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h5 class="fw-bold text-primary mb-1">Campus Facilities & Infrastructure</h5>
                <small class="text-muted">Manage the facility cards displayed on the homepage</small>
              </div>
              <button class="btn btn-sm btn-outline-primary rounded-pill" @click="addFacility">
                <i class="bi bi-plus-circle me-1"></i> Add Facility Card
              </button>
            </div>

            <div class="row g-3">
              <div v-for="(item, idx) in settings.facilities.items" :key="idx" class="col-md-6">
                <div class="card border rounded-3 p-3 bg-light position-relative">
                  <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle" style="width: 28px; height: 28px; padding: 0;" @click="removeFacility(idx)">
                    &times;
                  </button>
                  <div class="mb-2 pe-4">
                    <label class="form-label small fw-semibold">Facility Title</label>
                    <input type="text" v-model="item.title" class="form-control form-control-sm">
                  </div>
                  <div class="row g-2 mb-2">
                    <div class="col-6">
                      <label class="form-label small fw-semibold">Bootstrap Icon Class</label>
                      <input type="text" v-model="item.icon" class="form-control form-control-sm" placeholder="e.g. bi-book">
                    </div>
                    <div class="col-6">
                      <label class="form-label small fw-semibold">Theme Color</label>
                      <select v-model="item.color" class="form-select form-select-sm">
                        <option value="primary">Primary (Blue)</option>
                        <option value="success">Success (Green)</option>
                        <option value="warning">Warning (Yellow)</option>
                        <option value="danger">Danger (Red)</option>
                        <option value="info">Info (Cyan)</option>
                        <option value="secondary">Secondary (Gray)</option>
                      </select>
                    </div>
                  </div>
                  <div>
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea v-model="item.description" class="form-control form-control-sm" rows="2"></textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('facilities')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('facilities')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Facilities
              </button>
            </div>
          </div>

          <!-- TAB 7: Admissions Content -->
          <div v-if="activeTab === 'admissions'">
            <h5 class="fw-bold text-primary mb-3">Admissions Guidelines & Document Checklist</h5>
            <p class="small text-muted mb-4">Customize the requirements and instructions prospective parents read on the Admissions page.</p>

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Badge Text</label>
                <input type="text" v-model="settings.admissions.badge" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" v-model="settings.admissions.title" class="form-control">
              </div>
              <div class="col-12">
                <label class="form-label fw-semibold">Subtitle / Intro</label>
                <input type="text" v-model="settings.admissions.subtitle" class="form-control">
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">Required Documents Checklist</h6>
              <button class="btn btn-sm btn-outline-primary rounded-pill" @click="addDocument">
                <i class="bi bi-plus me-1"></i> Add Document Item
              </button>
            </div>

            <div class="d-flex flex-column gap-2 mb-4">
              <div v-for="(doc, idx) in settings.admissions.documents" :key="idx" class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-file-earmark-check text-success"></i></span>
                <input type="text" v-model="settings.admissions.documents[idx]" class="form-control">
                <button class="btn btn-outline-danger" type="button" @click="removeDocument(idx)">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
              <button class="btn btn-outline-secondary rounded-pill px-4" @click="resetSection('admissions')">Reset</button>
              <button class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" :disabled="saving" @click="saveSection('admissions')">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                Save Admissions Content
              </button>
            </div>
          </div>

          <!-- TAB 8: Notice Board & Announcements -->
          <div v-if="activeTab === 'notices'">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h5 class="fw-bold text-primary mb-1">Notice Board & Circulars</h5>
                <small class="text-muted">Create notices that appear on the school website and student portal</small>
              </div>
              <button class="btn btn-primary rounded-pill px-4" @click="openNoticeModal()">
                <i class="bi bi-plus-circle me-1"></i> Publish New Notice
              </button>
            </div>

            <!-- Notices Table -->
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted">
                  <tr>
                    <th class="ps-3">Title</th>
                    <th>Audience</th>
                    <th>Publish Date</th>
                    <th>Pinned</th>
                    <th>Status</th>
                    <th class="pe-3 text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="notices.length === 0">
                    <td colspan="6" class="text-center py-4 text-muted small">No notices published yet.</td>
                  </tr>
                  <tr v-for="n in notices" :key="n.id">
                    <td class="ps-3">
                      <div class="fw-semibold text-dark">{{ n.title }}</div>
                      <small class="text-muted text-truncate d-block" style="max-width: 320px;">{{ n.content }}</small>
                    </td>
                    <td>
                      <span class="badge rounded-pill text-capitalize" :class="audienceBadge(n.target_audience)">
                        {{ n.target_audience }}
                      </span>
                    </td>
                    <td class="small">{{ n.publish_date }}</td>
                    <td>
                      <i v-if="n.is_pinned" class="bi bi-pin-angle-fill text-danger fs-5" title="Pinned to top"></i>
                      <span v-else class="text-muted">-</span>
                    </td>
                    <td>
                      <span class="badge rounded-pill" :class="n.status ? 'bg-success' : 'bg-secondary'">
                        {{ n.status ? 'Active' : 'Archived' }}
                      </span>
                    </td>
                    <td class="pe-3 text-end">
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" @click="openNoticeModal(n)">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" @click="deleteNotice(n)">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Notice Modal -->
    <div class="modal fade" id="noticeModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
          <div class="modal-header bg-primary text-white border-0 py-3">
            <h5 class="modal-title fw-bold">{{ editingNotice ? 'Edit Notice' : 'Publish Notice' }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-semibold">Notice Title <span class="text-danger">*</span></label>
              <input type="text" v-model="noticeForm.title" class="form-control" placeholder="e.g. Annual Sports Meet 2026" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Target Audience</label>
                <select v-model="noticeForm.target_audience" class="form-select">
                  <option value="all">Everyone (All)</option>
                  <option value="public">Website Public Only</option>
                  <option value="students">Students Only</option>
                  <option value="parents">Parents Only</option>
                  <option value="teachers">Staff & Teachers</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Publish Date</label>
                <input type="date" v-model="noticeForm.publish_date" class="form-control">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Notice Body / Content <span class="text-danger">*</span></label>
              <textarea v-model="noticeForm.content" class="form-control" rows="5" placeholder="Full notice details..." required></textarea>
            </div>

            <div class="d-flex gap-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="pinCheck" v-model="noticeForm.is_pinned">
                <label class="form-check-label" for="pinCheck">Pin to Top (Sticky)</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="statusCheck" v-model="noticeForm.status">
                <label class="form-check-label" for="statusCheck">Active (Visible)</label>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary rounded-pill px-4" :disabled="savingNotice" @click="saveNotice">
              <span v-if="savingNotice" class="spinner-border spinner-border-sm me-1"></span>
              {{ editingNotice ? 'Update Notice' : 'Publish Notice' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useTenantStore } from '@/stores/tenant'
import api from '@/api/axios'
import Swal from 'sweetalert2'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const tenantStore = useTenantStore()

const activeTab = ref('general')
const loading = ref(true)
const saving = ref(false)
const savingNotice = ref(false)
const notices = ref([])
const editingNotice = ref(null)

const currentSchool = ref(null)
const allSchools = ref([])
const selectedSchoolId = ref(null)

const isSuperAdmin = computed(() => {
  const u = authStore.user
  return u?.user_type === 'super_admin' || u?.roles?.some(r => {
    const roleName = typeof r === 'string' ? r : (r.name || '')
    return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin'
  })
})

const schoolLiveUrl = computed(() => {
  return currentSchool.value?.live_url 
    || (settings.general?.school_code ? `/s/${settings.general.school_code}` : '/')
})

const settings = reactive({
  general: {},
  hero: {},
  about: {},
  principal: {},
  stats: {},
  facilities: { items: [] },
  admissions: { documents: [] },
})

const noticeForm = reactive({
  title: '',
  target_audience: 'all',
  publish_date: new Date().toISOString().split('T')[0],
  content: '',
  is_pinned: false,
  status: true,
})

async function loadSettings(targetSchoolId = null) {
  loading.value = true
  try {
    const sid = targetSchoolId || selectedSchoolId.value || route.query.school_id || localStorage.getItem('active_school_id') || ''
    const res = await api.get('/admin/website/content', {
      params: sid ? { school_id: sid } : {}
    })
    if (res.data.success) {
      Object.assign(settings, res.data.data)
      if (res.data.school) {
        currentSchool.value = res.data.school
        selectedSchoolId.value = res.data.school.id
      }
      if (Array.isArray(res.data.all_schools) && res.data.all_schools.length > 0) {
        allSchools.value = res.data.all_schools
      }
    }
  } catch (err) {
    console.error('Failed to load website settings', err)
  } finally {
    loading.value = false
  }
}

async function onSchoolChange() {
  if (!selectedSchoolId.value) return
  // Instantly broadcast and switch global tenant context across entire ERP (Navbar, Sidebar, Axios headers)
  await tenantStore.switchTenant(selectedSchoolId.value)
  await loadSettings(selectedSchoolId.value)
  await loadNotices(selectedSchoolId.value)
}

async function saveSection(sectionKey) {
  saving.value = true
  try {
    const sid = selectedSchoolId.value || currentSchool.value?.id || null
    const res = await api.post(`/admin/website/content/${sectionKey}`, {
      content: settings[sectionKey],
      school_id: sid
    }, {
      params: sid ? { school_id: sid } : {}
    })
    if (res.data.success) {
      Swal.fire({
        icon: 'success',
        title: 'Saved!',
        text: `${sectionKey.toUpperCase()} section updated successfully for ${currentSchool.value?.name || 'the school'}.`,
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
    text: `Are you sure you want to reset the ${sectionKey} section to default content for ${currentSchool.value?.name || 'this school'}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, reset',
  })

  if (result.isConfirmed) {
    try {
      const sid = selectedSchoolId.value || currentSchool.value?.id || null
      const res = await api.post(`/admin/website/content/${sectionKey}/reset`, {
        school_id: sid
      }, {
        params: sid ? { school_id: sid } : {}
      })
      if (res.data.success) {
        settings[sectionKey] = res.data.data
        Swal.fire({ icon: 'success', title: 'Reset Complete', timer: 1200, showConfirmButton: false })
      }
    } catch (err) {
      console.error(err)
    }
  }
}

function addFacility() {
  if (!settings.facilities.items) settings.facilities.items = []
  settings.facilities.items.push({
    title: 'New Facility',
    icon: 'bi-stars',
    color: 'primary',
    description: 'Description of facility...',
  })
}

function removeFacility(idx) {
  settings.facilities.items.splice(idx, 1)
}

function addDocument() {
  if (!settings.admissions.documents) settings.admissions.documents = []
  settings.admissions.documents.push('New Document Requirement')
}

function removeDocument(idx) {
  settings.admissions.documents.splice(idx, 1)
}

/* Notices Management */
async function loadNotices(targetSchoolId = null) {
  try {
    const sid = targetSchoolId || selectedSchoolId.value || currentSchool.value?.id || ''
    const res = await api.get('/admin/website/notices', {
      params: sid ? { school_id: sid } : {}
    })
    if (res.data.success) {
      notices.value = res.data.data.data || []
    }
  } catch (err) {
    console.error('Failed to load notices', err)
  }
}

function audienceBadge(aud) {
  switch (aud) {
    case 'all': return 'bg-primary'
    case 'public': return 'bg-info text-white'
    case 'students': return 'bg-success'
    case 'parents': return 'bg-warning text-dark'
    default: return 'bg-secondary'
  }
}

function openNoticeModal(notice = null) {
  editingNotice.value = notice
  if (notice) {
    noticeForm.title = notice.title
    noticeForm.target_audience = notice.target_audience
    noticeForm.publish_date = notice.publish_date
    noticeForm.content = notice.content
    noticeForm.is_pinned = Boolean(notice.is_pinned)
    noticeForm.status = Boolean(notice.status)
  } else {
    noticeForm.title = ''
    noticeForm.target_audience = 'all'
    noticeForm.publish_date = new Date().toISOString().split('T')[0]
    noticeForm.content = ''
    noticeForm.is_pinned = false
    noticeForm.status = true
  }
  const modal = new window.bootstrap.Modal(document.getElementById('noticeModal'))
  modal.show()
}

async function saveNotice() {
  if (!noticeForm.title || !noticeForm.content) {
    Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please enter title and content.' })
    return
  }
  savingNotice.value = true
  try {
    const sid = selectedSchoolId.value || currentSchool.value?.id || null
    const payload = {
      ...noticeForm,
      school_id: sid
    }
    if (editingNotice.value) {
      await api.put(`/admin/website/notices/${editingNotice.value.id}`, payload, {
        params: sid ? { school_id: sid } : {}
      })
    } else {
      await api.post('/admin/website/notices', payload, {
        params: sid ? { school_id: sid } : {}
      })
    }
    Swal.fire({ icon: 'success', title: 'Notice Saved!', timer: 1200, showConfirmButton: false })
    const modal = window.bootstrap.Modal.getInstance(document.getElementById('noticeModal'))
    if (modal) modal.hide()
    loadNotices(sid)
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.response?.data?.message || 'Failed to save notice' })
  } finally {
    savingNotice.value = false
  }
}

async function deleteNotice(notice) {
  const result = await Swal.fire({
    title: 'Delete Notice?',
    text: `Are you sure you want to delete "${notice.title}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete',
  })

  if (result.isConfirmed) {
    try {
      const sid = selectedSchoolId.value || currentSchool.value?.id || null
      await api.delete(`/admin/website/notices/${notice.id}`, {
        params: sid ? { school_id: sid } : {}
      })
      Swal.fire({ icon: 'success', title: 'Deleted', timer: 1000, showConfirmButton: false })
      loadNotices(sid)
    } catch (err) {
      console.error(err)
    }
  }
}

function handleTenantSwitched(e) {
  const sid = e.detail?.school_id
  if (sid && sid !== 'all' && Number(sid) !== Number(selectedSchoolId.value)) {
    selectedSchoolId.value = Number(sid)
    loadSettings(Number(sid))
    loadNotices(Number(sid))
  }
}

onMounted(async () => {
  window.addEventListener('tenant-switched', handleTenantSwitched)
  if (!authStore.user && authStore.token) {
    await authStore.loadProfile()
  }
  const initialId = route.query.school_id || tenantStore.activeSchoolId || localStorage.getItem('active_school_id') || null
  await loadSettings(initialId)
  await loadNotices(initialId)
})

onUnmounted(() => {
  window.removeEventListener('tenant-switched', handleTenantSwitched)
})
</script>
