<template>
  <div class="container-fluid py-3">
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Academic' },
        { label: 'Online Classes' }
      ]"
    />

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h3 class="fw-bold mb-1 d-flex align-items-center gap-2">
          <i class="bi bi-camera-video-fill text-primary"></i>
          Online Classes & Virtual Rooms
        </h3>
        <p class="text-muted small mb-0">
          Schedule, host, and manage live lectures via Google Meet, Zoom, MS Teams, or instant in-browser video.
        </p>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" @click="openModal()">
          <i class="bi bi-plus-circle-fill"></i>
          <span>Schedule Online Class</span>
        </button>
      </div>
    </div>

    <!-- Live Now Alert Banner (if any class is currently live) -->
    <div v-if="liveClassesCount > 0" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-3">
        <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
        <div>
          <strong class="text-danger d-block">
            <i class="bi bi-broadcast me-1"></i> {{ liveClassesCount }} Class{{ liveClassesCount > 1 ? 'es' : '' }} Currently LIVE!
          </strong>
          <small class="text-dark">Teachers and students can join ongoing sessions directly.</small>
        </div>
      </div>
      <button class="btn btn-sm btn-danger rounded-pill px-3" @click="setFilterScope('today')">
        View Live Sessions
      </button>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
      <div class="card-body p-3">
        <div class="row g-2 align-items-center">
          <!-- Scope Tabs -->
          <div class="col-12 col-lg-auto mb-2 mb-lg-0">
            <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
              <button
                type="button"
                class="btn rounded-pill px-3"
                :class="filterScope === '' ? 'btn-primary text-white shadow-sm' : 'btn-light text-dark'"
                @click="setFilterScope('')"
              >
                All ({{ stats.total }})
              </button>
              <button
                type="button"
                class="btn rounded-pill px-3"
                :class="filterScope === 'today' ? 'btn-danger text-white shadow-sm' : 'btn-light text-dark'"
                @click="setFilterScope('today')"
              >
                Today / Live
              </button>
              <button
                type="button"
                class="btn rounded-pill px-3"
                :class="filterScope === 'upcoming' ? 'btn-primary text-white shadow-sm' : 'btn-light text-dark'"
                @click="setFilterScope('upcoming')"
              >
                Upcoming
              </button>
              <button
                type="button"
                class="btn rounded-pill px-3"
                :class="filterScope === 'past' ? 'btn-primary text-white shadow-sm' : 'btn-light text-dark'"
                @click="setFilterScope('past')"
              >
                Completed / Past
              </button>
            </div>
          </div>

          <!-- Class Dropdown -->
          <div class="col-6 col-md-3 col-lg-2">
            <select v-model="filterClass" class="form-select form-select-sm rounded-pill" @change="loadClasses">
              <option value="">All Classes</option>
              <option v-for="c in classesList" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <!-- Platform Dropdown -->
          <div class="col-6 col-md-3 col-lg-2">
            <select v-model="filterPlatform" class="form-select form-select-sm rounded-pill" @change="loadClasses">
              <option value="">All Platforms</option>
              <option value="google_meet">Google Meet</option>
              <option value="zoom">Zoom</option>
              <option value="teams">MS Teams</option>
              <option value="jitsi">Free Browser Room</option>
            </select>
          </div>

          <!-- Search Input -->
          <div class="col-12 col-md-6 col-lg">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-white border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
              <input
                type="text"
                v-model="searchQuery"
                class="form-control border-start-0 rounded-end-pill"
                placeholder="Search topic or subject..."
                @keyup.enter="loadClasses"
              />
            </div>
          </div>

          <div class="col-auto">
            <button class="btn btn-sm btn-outline-secondary rounded-pill" title="Reset Filters" @click="resetFilters">
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="text-muted small mt-2">Loading online classes...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="classesData.length === 0" class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
      <div class="card-body">
        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
          <i class="bi bi-camera-video fs-1"></i>
        </div>
        <h5 class="fw-bold">No Online Classes Found</h5>
        <p class="text-muted small mx-auto" style="max-width: 420px;">
          {{ filterScope || filterClass || filterPlatform || searchQuery ? 'No classes match your selected filter criteria. Try clearing filters.' : 'Get started by scheduling your first interactive online lecture for students.' }}
        </p>
        <button class="btn btn-primary rounded-pill px-4 mt-2" @click="openModal()">
          <i class="bi bi-plus-circle me-1"></i> Schedule Class Now
        </button>
      </div>
    </div>

    <!-- Classes Cards Grid -->
    <div v-else class="row g-4">
      <div v-for="item in classesData" :key="item.id" class="col-md-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative bg-white transition-card">
          <!-- Card Header / Platform Banner -->
          <div class="card-header border-0 p-3 d-flex align-items-center justify-content-between" :class="getPlatformHeaderBg(item.platform)">
            <div class="d-flex align-items-center gap-2">
              <span class="badge rounded-pill px-2 py-1 small fw-semibold" :class="getPlatformBadgeClass(item.platform)">
                <i :class="getPlatformIcon(item.platform)" class="me-1"></i>
                {{ item.platform_label }}
              </span>
            </div>

            <!-- Status Badge -->
            <span v-if="item.is_live" class="badge bg-danger text-white rounded-pill px-3 py-1 shadow-sm pulse-badge">
              <i class="bi bi-broadcast me-1"></i> LIVE NOW
            </span>
            <span v-else-if="item.status === 'completed'" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
              <i class="bi bi-check-circle me-1"></i> Completed
            </span>
            <span v-else-if="item.status === 'cancelled'" class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
              Cancelled
            </span>
            <span v-else class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
              <i class="bi bi-clock me-1"></i> Scheduled
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body p-4 d-flex flex-column">
            <!-- Topic & Subject -->
            <div class="mb-3">
              <span class="badge bg-light text-dark border small mb-2 font-monospace">
                {{ item.subject?.name || 'General Subject' }}
              </span>
              <h5 class="fw-bold mb-1 text-dark lh-sm">{{ item.title }}</h5>
              <p v-if="item.description" class="text-muted small mb-0 text-truncate-2">
                {{ item.description }}
              </p>
            </div>

            <!-- Class & Timing Meta -->
            <div class="bg-light rounded-3 p-3 mb-3 small">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted"><i class="bi bi-mortarboard me-1 text-primary"></i> Target:</span>
                <strong class="text-dark">{{ item.class?.name || 'All Classes' }} {{ item.section?.name ? '(' + item.section.name + ')' : '' }}</strong>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted"><i class="bi bi-calendar3 me-1 text-primary"></i> Date:</span>
                <strong class="text-dark">{{ item.formatted_date }}</strong>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted"><i class="bi bi-clock-history me-1 text-primary"></i> Timing:</span>
                <strong class="text-dark">{{ item.formatted_time_range }} ({{ item.duration_minutes || 45 }}m)</strong>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted"><i class="bi bi-person-badge me-1 text-primary"></i> Host / Teacher:</span>
                <strong class="text-dark">{{ item.teacher ? (item.teacher.first_name + ' ' + (item.teacher.last_name || '')) : 'School Staff' }}</strong>
              </div>
            </div>

            <!-- Zoom Credentials (if Zoom) -->
            <div v-if="item.platform === 'zoom' && (item.meeting_id || item.meeting_password)" class="bg-primary-subtle rounded-3 p-2 mb-3 small d-flex justify-content-between align-items-center">
              <div>
                <span v-if="item.meeting_id" class="d-block text-primary font-monospace" style="font-size: 0.75rem;">
                  ID: <strong>{{ item.meeting_id }}</strong>
                </span>
                <span v-if="item.meeting_password" class="d-block text-primary font-monospace" style="font-size: 0.75rem;">
                  Pass: <strong>{{ item.meeting_password }}</strong>
                </span>
              </div>
              <button class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" @click="copyCredentials(item)" title="Copy meeting details">
                <i class="bi bi-copy"></i> Copy
              </button>
            </div>

            <!-- Recording Link if completed -->
            <div v-if="item.recorded_link" class="mb-3">
              <a :href="item.recorded_link" target="_blank" class="btn btn-sm btn-outline-success w-100 rounded-pill d-flex align-items-center justify-content-center gap-1">
                <i class="bi bi-play-circle-fill"></i>
                <span>Watch Class Recording</span>
              </a>
            </div>

            <!-- Actions Footer -->
            <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between gap-2">
              <div class="d-flex align-items-center gap-1">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-2" title="Edit Class" @click="openModal(item)">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Delete Class" @click="deleteClass(item.id)">
                  <i class="bi bi-trash"></i>
                </button>

                <!-- Quick Status Dropdown -->
                <div class="dropdown d-inline">
                  <button class="btn btn-sm btn-outline-secondary rounded-pill px-2" type="button" data-bs-toggle="dropdown" title="Change status">
                    <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu shadow-sm">
                    <li><h6 class="dropdown-header">Update Status</h6></li>
                    <li><a class="dropdown-item small" href="#" @click.prevent="quickStatus(item, 'live')"><i class="bi bi-broadcast text-danger me-2"></i>Mark Live</a></li>
                    <li><a class="dropdown-item small" href="#" @click.prevent="quickStatus(item, 'completed')"><i class="bi bi-check-circle text-success me-2"></i>Mark Completed</a></li>
                    <li><a class="dropdown-item small" href="#" @click.prevent="promptRecording(item)"><i class="bi bi-camera-reels text-primary me-2"></i>Attach Recording Link</a></li>
                    <li><a class="dropdown-item small" href="#" @click.prevent="quickStatus(item, 'cancelled')"><i class="bi bi-x-circle text-muted me-2"></i>Cancel Class</a></li>
                  </ul>
                </div>
              </div>

              <!-- Main Join / Host Button -->
              <a
                :href="item.meeting_link"
                target="_blank"
                class="btn btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-1 shadow-sm"
                :class="item.is_live ? 'btn-danger' : 'btn-primary'"
              >
                <i class="bi bi-box-arrow-up-right"></i>
                <span>{{ item.is_live ? 'Join Live Now' : 'Start / Join Class' }}</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for Schedule / Edit Online Class -->
    <div class="modal fade" id="onlineClassModal" tabindex="-1" aria-hidden="true" ref="modalRef">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="modal-header bg-primary text-white p-3 px-4 border-0">
            <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
              <i class="bi bi-camera-video-fill"></i>
              <span>{{ editingId ? 'Edit Online Class' : 'Schedule Online Class' }}</span>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form @submit.prevent="saveClass">
            <div class="modal-body p-4">
              <!-- Class Topic Title -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Class Topic / Lecture Title <span class="text-danger">*</span></label>
                <input
                  type="text"
                  v-model="form.title"
                  class="form-control rounded-3"
                  placeholder="e.g. Chapter 4: Algebraic Fractions & Equations"
                  required
                />
              </div>

              <div class="row g-3 mb-3">
                <!-- Class -->
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Class <span class="text-danger">*</span></label>
                  <select v-model="form.class_id" class="form-select rounded-3" required @change="onClassChange">
                    <option value="" disabled>Select Class</option>
                    <option v-for="c in classesList" :key="c.id" :value="c.id">{{ c.name }}</option>
                  </select>
                </div>

                <!-- Section (Optional) -->
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Section (Optional)</label>
                  <select v-model="form.section_id" class="form-select rounded-3">
                    <option :value="null">All Sections (Entire Class)</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                  </select>
                </div>

                <!-- Subject -->
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Subject <span class="text-danger">*</span></label>
                  <select v-model="form.subject_id" class="form-select rounded-3" required>
                    <option value="" disabled>Select Subject</option>
                    <option v-for="sub in subjectsList" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                  </select>
                </div>

                <!-- Teacher -->
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Teacher / Host</label>
                  <select v-model="form.teacher_id" class="form-select rounded-3">
                    <option :value="null">Select Teacher (Optional)</option>
                    <option v-for="t in teachersList" :key="t.id" :value="t.id">{{ t.first_name }} {{ t.last_name || '' }}</option>
                  </select>
                </div>
              </div>

              <!-- Platform Selection Cards -->
              <div class="mb-3">
                <label class="form-label fw-semibold d-block">Video Meeting Platform <span class="text-danger">*</span></label>
                <div class="row g-2">
                  <div class="col-6 col-md-3">
                    <div
                      class="card text-center p-2 rounded-3 cursor-pointer border h-100 transition-platform"
                      :class="form.platform === 'google_meet' ? 'border-primary bg-primary-subtle text-primary fw-bold' : 'bg-light text-muted'"
                      @click="selectPlatform('google_meet')"
                    >
                      <i class="bi bi-google fs-4 mb-1"></i>
                      <div class="small">Google Meet</div>
                    </div>
                  </div>

                  <div class="col-6 col-md-3">
                    <div
                      class="card text-center p-2 rounded-3 cursor-pointer border h-100 transition-platform"
                      :class="form.platform === 'zoom' ? 'border-primary bg-primary-subtle text-primary fw-bold' : 'bg-light text-muted'"
                      @click="selectPlatform('zoom')"
                    >
                      <i class="bi bi-camera-video fs-4 mb-1"></i>
                      <div class="small">Zoom Meeting</div>
                    </div>
                  </div>

                  <div class="col-6 col-md-3">
                    <div
                      class="card text-center p-2 rounded-3 cursor-pointer border h-100 transition-platform"
                      :class="form.platform === 'teams' ? 'border-primary bg-primary-subtle text-primary fw-bold' : 'bg-light text-muted'"
                      @click="selectPlatform('teams')"
                    >
                      <i class="bi bi-microsoft fs-4 mb-1"></i>
                      <div class="small">MS Teams</div>
                    </div>
                  </div>

                  <div class="col-6 col-md-3">
                    <div
                      class="card text-center p-2 rounded-3 cursor-pointer border h-100 transition-platform"
                      :class="form.platform === 'jitsi' ? 'border-primary bg-primary-subtle text-primary fw-bold' : 'bg-light text-muted'"
                      @click="selectPlatform('jitsi')"
                    >
                      <i class="bi bi-globe fs-4 mb-1"></i>
                      <div class="small">Free In-Browser</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Meeting Link with Instant Room Generator -->
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label class="form-label fw-semibold mb-0">Meeting Link / URL <span class="text-danger">*</span></label>
                  <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" @click="autoGenerateRoom">
                    <i class="bi bi-magic me-1"></i> Auto-Generate Free Room URL
                  </button>
                </div>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                  <input
                    type="url"
                    v-model="form.meeting_link"
                    class="form-control"
                    placeholder="https://meet.google.com/xyz or https://zoom.us/j/..."
                    required
                  />
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">
                  Paste your Google Meet, Zoom, or Teams invite link here, or click <em>Auto-Generate Free Room URL</em> for a zero-setup browser video room.
                </small>
              </div>

              <!-- Zoom Extra Fields (Shown only if platform is Zoom) -->
              <div v-if="form.platform === 'zoom'" class="row g-3 mb-3 bg-light p-3 rounded-3 border">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Zoom Meeting ID</label>
                  <input type="text" v-model="form.meeting_id" class="form-control form-control-sm" placeholder="e.g. 849 2019 4821" />
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Meeting Passcode / Password</label>
                  <input type="text" v-model="form.meeting_password" class="form-control form-control-sm" placeholder="e.g. 123456" />
                </div>
              </div>

              <!-- Schedule Date, Time & Timezone Box -->
              <div class="card border bg-light-subtle rounded-4 p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                  <span class="fw-bold small text-uppercase text-primary">
                    <i class="bi bi-clock-history me-1"></i> Class Timing & Time Zone
                  </span>
                  <span class="badge bg-primary text-white font-monospace px-3 py-1 rounded-pill shadow-sm">
                    <i class="bi bi-broadcast-pin me-1"></i> {{ previewTimeRange }}
                  </span>
                </div>

                <div class="row g-3">
                  <!-- Date -->
                  <div class="col-md-5">
                    <label class="form-label fw-semibold small">Class Date <span class="text-danger">*</span></label>
                    <input type="date" v-model="form.class_date" class="form-control rounded-3" required />
                  </div>

                  <!-- Timezone -->
                  <div class="col-md-7">
                    <label class="form-label fw-semibold small">
                      <i class="bi bi-globe me-1 text-primary"></i> Time Zone <span class="text-danger">*</span>
                    </label>
                    <select v-model="form.timezone" class="form-select rounded-3">
                      <option value="Asia/Kolkata">🇮🇳 India Standard Time — IST (GMT+5:30)</option>
                      <option value="Asia/Dubai">🇦🇪 Gulf Standard Time — GST (GMT+4:00)</option>
                      <option value="Asia/Dhaka">🇧🇩 Bangladesh Standard Time — BST (GMT+6:00)</option>
                      <option value="Asia/Kathmandu">🇳🇵 Nepal Time — NPT (GMT+5:45)</option>
                      <option value="Asia/Singapore">🇸🇬 Singapore Time — SGT (GMT+8:00)</option>
                      <option value="Europe/London">🇬🇧 British Time — GMT / BST</option>
                      <option value="America/New_York">🇺🇸 US Eastern Time — EST / EDT</option>
                      <option value="UTC">🌐 Universal Time — UTC</option>
                    </select>
                  </div>

                  <!-- Start Time with 12-Hour AM/PM Selector -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small mb-1">
                      Start Time <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <select v-model="timeState.start_hour" class="form-select text-center font-monospace fw-semibold" title="Hour">
                        <option v-for="h in hoursList" :key="'sh-'+h" :value="h">{{ h }}</option>
                      </select>
                      <span class="input-group-text bg-white px-1 fw-bold border-start-0 border-end-0">:</span>
                      <select v-model="timeState.start_minute" class="form-select text-center font-monospace" title="Minute">
                        <option v-for="m in minutesList" :key="'sm-'+m" :value="m">{{ m }}</option>
                      </select>
                      <div class="btn-group" role="group">
                        <button
                          type="button"
                          class="btn btn-sm px-2 fw-bold"
                          :class="timeState.start_ampm === 'AM' ? 'btn-primary' : 'btn-outline-secondary'"
                          @click="timeState.start_ampm = 'AM'"
                        >AM</button>
                        <button
                          type="button"
                          class="btn btn-sm px-2 fw-bold"
                          :class="timeState.start_ampm === 'PM' ? 'btn-primary' : 'btn-outline-secondary'"
                          @click="timeState.start_ampm = 'PM'"
                        >PM</button>
                      </div>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                      Class starts: <strong class="text-dark">{{ timeState.start_hour }}:{{ timeState.start_minute }} {{ timeState.start_ampm }}</strong>
                    </small>
                  </div>

                  <!-- End Time with 12-Hour AM/PM Selector -->
                  <div class="col-md-6">
                    <label class="form-label fw-semibold small mb-1">
                      End Time <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                      <select v-model="timeState.end_hour" class="form-select text-center font-monospace fw-semibold" title="Hour">
                        <option v-for="h in hoursList" :key="'eh-'+h" :value="h">{{ h }}</option>
                      </select>
                      <span class="input-group-text bg-white px-1 fw-bold border-start-0 border-end-0">:</span>
                      <select v-model="timeState.end_minute" class="form-select text-center font-monospace" title="Minute">
                        <option v-for="m in minutesList" :key="'em-'+m" :value="m">{{ m }}</option>
                      </select>
                      <div class="btn-group" role="group">
                        <button
                          type="button"
                          class="btn btn-sm px-2 fw-bold"
                          :class="timeState.end_ampm === 'AM' ? 'btn-primary' : 'btn-outline-secondary'"
                          @click="timeState.end_ampm = 'AM'"
                        >AM</button>
                        <button
                          type="button"
                          class="btn btn-sm px-2 fw-bold"
                          :class="timeState.end_ampm === 'PM' ? 'btn-primary' : 'btn-outline-secondary'"
                          @click="timeState.end_ampm = 'PM'"
                        >PM</button>
                      </div>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                      Class ends: <strong class="text-dark">{{ timeState.end_hour }}:{{ timeState.end_minute }} {{ timeState.end_ampm }}</strong>
                    </small>
                  </div>
                </div>
              </div>

              <!-- Description & Instructions -->
              <div class="row g-3 mb-2">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Class Agenda / Description</label>
                  <textarea
                    v-model="form.description"
                    class="form-control rounded-3"
                    rows="2"
                    placeholder="Brief summary of topics to be covered..."
                  ></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Join Instructions for Students</label>
                  <textarea
                    v-model="form.join_instructions"
                    class="form-control rounded-3"
                    rows="2"
                    placeholder="e.g. Keep mics muted, bring Chapter 4 worksheet..."
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="modal-footer bg-light p-3 px-4 border-0">
              <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="submitting">
                <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-check2-circle me-1"></i>
                {{ editingId ? 'Update Class' : 'Schedule Class' }}
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
import axios from '@/api/axios'
import Swal from 'sweetalert2'
import Modal from 'bootstrap/js/dist/modal'
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue'

const loading = ref(false)
const submitting = ref(false)
const classesData = ref([])
const classesList = ref([])
const sectionsList = ref([])
const subjectsList = ref([])
const teachersList = ref([])

const filterScope = ref('')
const filterClass = ref('')
const filterPlatform = ref('')
const searchQuery = ref('')

const modalRef = ref(null)
let bsModal = null
const editingId = ref(null)

const stats = reactive({
  total: 0,
})

const hoursList = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']
const minutesList = ['00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55']

const timeState = reactive({
  start_hour: '10',
  start_minute: '00',
  start_ampm: 'AM',
  end_hour: '11',
  end_minute: '00',
  end_ampm: 'AM',
})

const form = reactive({
  title: '',
  class_id: '',
  section_id: null,
  subject_id: '',
  teacher_id: null,
  platform: 'google_meet',
  meeting_link: '',
  meeting_id: '',
  meeting_password: '',
  class_date: new Date().toISOString().split('T')[0],
  start_time: '10:00',
  end_time: '11:00',
  timezone: 'Asia/Kolkata',
  description: '',
  join_instructions: '',
  status: 'scheduled',
})

const previewTimeRange = computed(() => {
  const tzMap = {
    'Asia/Kolkata': 'IST',
    'Asia/Dubai': 'GST',
    'Asia/Dhaka': 'BST',
    'Asia/Kathmandu': 'NPT',
    'Asia/Singapore': 'SGT',
    'Europe/London': 'GMT',
    'America/New_York': 'EST',
    'UTC': 'UTC',
  }
  const tzShort = tzMap[form.timezone] || 'IST'
  return `${timeState.start_hour}:${timeState.start_minute} ${timeState.start_ampm} - ${timeState.end_hour}:${timeState.end_minute} ${timeState.end_ampm} (${tzShort})`
})

function parse24to12(timeStr) {
  if (!timeStr) return { hour: '10', minute: '00', ampm: 'AM' }
  const parts = String(timeStr).split(':')
  let h = parseInt(parts[0], 10)
  if (isNaN(h)) h = 10
  const m = parts[1] ? parts[1].substring(0, 2) : '00'
  const ampm = h >= 12 ? 'PM' : 'AM'
  h = h % 12
  if (h === 0) h = 12
  return {
    hour: String(h).padStart(2, '0'),
    minute: m,
    ampm,
  }
}

function format12to24(hour, minute, ampm) {
  let h = parseInt(hour, 10) || 12
  if (ampm === 'PM' && h < 12) h += 12
  if (ampm === 'AM' && h === 12) h = 0
  return `${String(h).padStart(2, '0')}:${minute}`
}

const filteredSections = computed(() => {
  if (!form.class_id) return []
  return sectionsList.value.filter(s => String(s.class_id) === String(form.class_id))
})

const liveClassesCount = computed(() => {
  return classesData.value.filter(c => c.is_live).length
})

function setFilterScope(scope) {
  filterScope.value = scope
  loadClasses()
}

function resetFilters() {
  filterScope.value = ''
  filterClass.value = ''
  filterPlatform.value = ''
  searchQuery.value = ''
  loadClasses()
}

async function loadDropdowns() {
  try {
    const [cRes, sRes, subRes, tRes] = await Promise.all([
      axios.get('/classes?all=true'),
      axios.get('/sections?all=true'),
      axios.get('/subjects?all=true'),
      axios.get('/teachers?all=true'),
    ])
    classesList.value = cRes.data?.data || cRes.data || []
    sectionsList.value = sRes.data?.data || sRes.data || []
    subjectsList.value = subRes.data?.data || subRes.data || []
    teachersList.value = tRes.data?.data || tRes.data || []
  } catch (err) {
    console.error('Failed to load online classes dropdowns', err)
  }
}

async function loadClasses() {
  loading.value = true
  try {
    const params = {}
    if (filterScope.value) params.scope = filterScope.value
    if (filterClass.value) params.class_id = filterClass.value
    if (filterPlatform.value) params.platform = filterPlatform.value
    if (searchQuery.value) params.search = searchQuery.value

    const res = await axios.get('/online-classes', { params })
    if (res.data?.success) {
      classesData.value = res.data.data
      stats.total = res.data.pagination?.total || classesData.value.length
    }
  } catch (err) {
    console.error('Failed to load online classes', err)
  } finally {
    loading.value = false
  }
}

function selectPlatform(platform) {
  form.platform = platform
  if (platform === 'jitsi' && !form.meeting_link) {
    autoGenerateRoom()
  }
}

async function autoGenerateRoom() {
  try {
    const res = await axios.post('/online-classes/generate-room', {
      title: form.title || 'Live-Class',
      class_id: form.class_id || null
    })
    if (res.data?.success) {
      form.platform = 'jitsi'
      form.meeting_link = res.data.meeting_link
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Free in-browser room URL generated!',
        timer: 2000,
        showConfirmButton: false
      })
    }
  } catch (err) {
    console.error('Failed to auto generate room', err)
  }
}

function onClassChange() {
  form.section_id = null
}

function openModal(item = null) {
  if (item) {
    editingId.value = item.id
    const sParsed = parse24to12(item.start_time)
    timeState.start_hour = sParsed.hour
    timeState.start_minute = sParsed.minute
    timeState.start_ampm = sParsed.ampm

    const eParsed = parse24to12(item.end_time)
    timeState.end_hour = eParsed.hour
    timeState.end_minute = eParsed.minute
    timeState.end_ampm = eParsed.ampm

    Object.assign(form, {
      title: item.title,
      class_id: item.class_id,
      section_id: item.section_id,
      subject_id: item.subject_id,
      teacher_id: item.teacher_id,
      platform: item.platform || 'google_meet',
      meeting_link: item.meeting_link,
      meeting_id: item.meeting_id || '',
      meeting_password: item.meeting_password || '',
      class_date: item.class_date,
      start_time: item.start_time?.substring(0, 5) || '10:00',
      end_time: item.end_time?.substring(0, 5) || '11:00',
      timezone: item.timezone || 'Asia/Kolkata',
      description: item.description || '',
      join_instructions: item.join_instructions || '',
      status: item.status || 'scheduled',
    })
  } else {
    editingId.value = null
    timeState.start_hour = '10'
    timeState.start_minute = '00'
    timeState.start_ampm = 'AM'

    timeState.end_hour = '11'
    timeState.end_minute = '00'
    timeState.end_ampm = 'AM'

    Object.assign(form, {
      title: '',
      class_id: classesList.value[0]?.id || '',
      section_id: null,
      subject_id: subjectsList.value[0]?.id || '',
      teacher_id: teachersList.value[0]?.id || null,
      platform: 'google_meet',
      meeting_link: '',
      meeting_id: '',
      meeting_password: '',
      class_date: new Date().toISOString().split('T')[0],
      start_time: '10:00',
      end_time: '11:00',
      timezone: 'Asia/Kolkata',
      description: '',
      join_instructions: '',
      status: 'scheduled',
    })
  }

  if (!bsModal && modalRef.value) {
    bsModal = new Modal(modalRef.value)
  }
  bsModal?.show()
}

async function saveClass() {
  submitting.value = true
  try {
    // Sync 12-hour AM/PM state to standard 24-hr time strings
    form.start_time = format12to24(timeState.start_hour, timeState.start_minute, timeState.start_ampm)
    form.end_time = format12to24(timeState.end_hour, timeState.end_minute, timeState.end_ampm)

    if (editingId.value) {
      await axios.put(`/online-classes/${editingId.value}`, form)
      Swal.fire({ icon: 'success', title: 'Updated!', text: 'Online class updated successfully.', timer: 1500, showConfirmButton: false })
    } else {
      await axios.post('/online-classes', form)
      Swal.fire({ icon: 'success', title: 'Scheduled!', text: 'Online class scheduled successfully.', timer: 1500, showConfirmButton: false })
    }
    bsModal?.hide()
    loadClasses()
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Action Failed',
      text: err.response?.data?.message || 'Could not save online class. Please check required fields.'
    })
  } finally {
    submitting.value = false
  }
}

async function deleteClass(id) {
  const confirm = await Swal.fire({
    title: 'Delete Online Class?',
    text: 'Are you sure you want to remove this scheduled online lecture?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it'
  })

  if (confirm.isConfirmed) {
    try {
      await axios.delete(`/online-classes/${id}`)
      Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false })
      loadClasses()
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Could not delete class.' })
    }
  }
}

async function quickStatus(item, newStatus) {
  try {
    await axios.patch(`/online-classes/${item.id}/status`, { status: newStatus })
    item.status = newStatus
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: `Status set to ${newStatus}`,
      timer: 1500,
      showConfirmButton: false
    })
    loadClasses()
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not update status.' })
  }
}

async function promptRecording(item) {
  const { value: url } = await Swal.fire({
    title: 'Attach Class Recording URL',
    input: 'url',
    inputLabel: 'Recording Link (YouTube, Drive, Zoom Cloud, Vimeo)',
    inputValue: item.recorded_link || '',
    inputPlaceholder: 'https://...',
    showCancelButton: true
  })

  if (url !== undefined) {
    try {
      await axios.patch(`/online-classes/${item.id}/status`, {
        status: 'completed',
        recorded_link: url
      })
      item.recorded_link = url
      item.status = 'completed'
      Swal.fire({ icon: 'success', title: 'Saved!', timer: 1200, showConfirmButton: false })
    } catch (err) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save recording link.' })
    }
  }
}

function copyCredentials(item) {
  const text = `Meeting Link: ${item.meeting_link}\nMeeting ID: ${item.meeting_id}\nPasscode: ${item.meeting_password}`
  navigator.clipboard.writeText(text)
  Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Zoom credentials copied!', timer: 1500, showConfirmButton: false })
}

function getPlatformIcon(platform) {
  return {
    google_meet: 'bi bi-google',
    zoom: 'bi bi-camera-video-fill',
    teams: 'bi bi-microsoft',
    jitsi: 'bi bi-globe-americas',
  }[platform] || 'bi bi-laptop'
}

function getPlatformBadgeClass(platform) {
  return {
    google_meet: 'bg-success text-white',
    zoom: 'bg-primary text-white',
    teams: 'bg-purple text-white',
    jitsi: 'bg-info text-dark',
  }[platform] || 'bg-secondary text-white'
}

function getPlatformHeaderBg(platform) {
  return {
    google_meet: 'bg-success-subtle',
    zoom: 'bg-primary-subtle',
    teams: 'bg-light',
    jitsi: 'bg-info-subtle',
  }[platform] || 'bg-light'
}

onMounted(() => {
  loadDropdowns()
  loadClasses()
})
</script>

<style scoped>
.transition-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.transition-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
}
.transition-platform {
  transition: all 0.15s ease-in-out;
}
.cursor-pointer {
  cursor: pointer;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.pulse-badge {
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.85; }
  100% { transform: scale(1); opacity: 1; }
}
.bg-purple {
  background-color: #6264A7 !important;
}
</style>
