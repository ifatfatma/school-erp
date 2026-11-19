<template>
  <div class="gallery-page">
    <!-- Header Banner -->
    <section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #1e3a8a 100%);">
      <div class="container py-4 text-center">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-3 shadow-sm">
          <i class="bi bi-camera-fill me-1"></i> {{ gallery.badge || 'Campus Life & Visual Tour' }}
        </span>
        <h1 class="display-5 fw-bold mb-3">{{ gallery.title || 'Photo & Event Gallery' }}</h1>
        <p class="lead text-white-50 mx-auto mb-0" style="max-width: 720px;">
          {{ gallery.subtitle || 'Explore the vibrant life, achievements, celebrations, sports meets, and modern campus infrastructure at our institution.' }}
        </p>
      </div>
    </section>

    <!-- Main Content Area -->
    <section class="py-5 bg-light">
      <div class="container">
        <!-- Category Filter Pills & View Mode Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 pb-2 border-bottom">
          <!-- Category Filter Pills -->
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <button
              class="btn btn-sm rounded-pill px-3 py-2 fw-semibold transition-all shadow-sm"
              :class="activeCategory === 'all' ? 'btn-primary' : 'btn-white bg-white text-secondary border'"
              @click="activeCategory = 'all'"
            >
              <i class="bi bi-grid-fill me-1"></i> All Media
              <span class="badge ms-1 rounded-pill" :class="activeCategory === 'all' ? 'bg-light text-primary' : 'bg-secondary-subtle text-secondary'">
                {{ totalPhotosCount }}
              </span>
            </button>

            <button
              v-for="cat in availableCategories"
              :key="cat.id"
              class="btn btn-sm rounded-pill px-3 py-2 fw-semibold transition-all shadow-sm"
              :class="activeCategory === cat.id ? 'btn-primary' : 'btn-white bg-white text-secondary border'"
              @click="activeCategory = cat.id"
            >
              <i :class="getCategoryIcon(cat.id)" class="me-1"></i> {{ cat.name }}
              <span class="badge ms-1 rounded-pill" :class="activeCategory === cat.id ? 'bg-light text-primary' : 'bg-secondary-subtle text-secondary'">
                {{ getCategoryPhotosCount(cat.id) }}
              </span>
            </button>
          </div>

          <!-- View Mode Toggle: Albums vs All Photos -->
          <div class="btn-group btn-group-sm shadow-sm rounded-pill p-1 bg-white border">
            <button
              type="button"
              class="btn rounded-pill px-3 fw-semibold"
              :class="viewMode === 'albums' ? 'btn-primary' : 'btn-light text-muted'"
              @click="viewMode = 'albums'"
            >
              <i class="bi bi-folder-fill me-1"></i> Albums ({{ filteredAlbums.length }})
            </button>
            <button
              type="button"
              class="btn rounded-pill px-3 fw-semibold"
              :class="viewMode === 'photos' ? 'btn-primary' : 'btn-light text-muted'"
              @click="viewMode = 'photos'"
            >
              <i class="bi bi-images me-1"></i> Photos ({{ filteredPhotos.length }})
            </button>
          </div>
        </div>

        <!-- Category Description Banner (if specific category chosen) -->
        <div v-if="activeCategoryInfo" class="alert alert-light border rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center gap-3">
          <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i :class="getCategoryIcon(activeCategoryInfo.id)" class="fs-4"></i>
          </div>
          <div>
            <h6 class="fw-bold mb-1 text-dark">{{ activeCategoryInfo.name }}</h6>
            <small class="text-muted">{{ activeCategoryInfo.description }}</small>
          </div>
        </div>

        <!-- MODE 1: ALBUMS VIEW -->
        <div v-if="viewMode === 'albums'">
          <div v-if="filteredAlbums.length === 0" class="card border-0 rounded-4 p-5 text-center bg-white shadow-sm my-4">
            <i class="bi bi-images fs-1 text-secondary opacity-50 mb-3"></i>
            <h5 class="fw-bold text-dark">No Albums in this Category Yet</h5>
            <p class="text-muted small mb-3">Check back soon as new campus and event moments are uploaded.</p>
            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 mx-auto" @click="activeCategory = 'all'">
              View All Photos
            </button>
          </div>

          <div v-else class="row g-4">
            <div v-for="album in filteredAlbums" :key="album.id" class="col-md-6 col-lg-4">
              <div class="card h-100 border-0 rounded-4 overflow-hidden shadow-sm album-card bg-white cursor-pointer" @click="openAlbum(album)">
                <!-- Album Cover Image -->
                <div class="position-relative overflow-hidden" style="height: 240px; background: #0f172a;">
                  <img
                    :src="formatAssetUrl(album.cover_image || (album.images && album.images[0]?.url) || '/images/no-image.png')"
                    :alt="album.title"
                    class="w-100 h-100 object-fit-cover album-thumb transition-all"
                    loading="lazy"
                    @error="onImgError"
                  />
                  <!-- Category Badge -->
                  <span class="badge position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm" :class="getCategoryBadgeClass(album.category)">
                    <i :class="getCategoryIcon(album.category)" class="me-1"></i> {{ getCategoryName(album.category) }}
                  </span>
                  <!-- Photo Count Badge -->
                  <span class="badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 end-0 m-3 px-3 py-1 rounded-pill font-monospace small">
                    <i class="bi bi-camera me-1"></i> {{ (album.images ? album.images.length : 1) }} Photos
                  </span>
                </div>

                <!-- Album Details -->
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                  <div>
                    <div class="text-muted small mb-1 fw-semibold">
                      <i class="bi bi-calendar3 me-1 text-primary"></i> {{ album.event_date || 'Academic Session' }}
                    </div>
                    <h5 class="fw-bold text-dark mb-2 text-truncate-2">{{ album.title }}</h5>
                    <p class="text-muted small mb-3 text-truncate-2">{{ album.description }}</p>
                  </div>

                  <div class="pt-2 border-top d-flex align-items-center justify-content-between text-primary fw-semibold small">
                    <span>View Album Photos</span>
                    <i class="bi bi-arrow-right"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODE 2: ALL PHOTOS GRID -->
        <div v-if="viewMode === 'photos'">
          <div v-if="filteredPhotos.length === 0" class="card border-0 rounded-4 p-5 text-center bg-white shadow-sm my-4">
            <i class="bi bi-camera fs-1 text-secondary opacity-50 mb-3"></i>
            <h5 class="fw-bold text-dark">No Photos Found</h5>
            <p class="text-muted small">No photos available in this category yet.</p>
          </div>

          <div v-else class="row g-3">
            <div
              v-for="(photo, idx) in filteredPhotos"
              :key="photo.id || idx"
              class="col-6 col-md-4 col-lg-3"
            >
              <div
                class="photo-card position-relative rounded-4 overflow-hidden shadow-sm cursor-pointer bg-dark"
                style="height: 220px;"
                @click="openLightbox(filteredPhotos, idx)"
              >
                <img
                  :src="formatAssetUrl(photo.url)"
                  :alt="photo.caption || photo.albumTitle"
                  class="w-100 h-100 object-fit-cover photo-img transition-all"
                  loading="lazy"
                  @error="onImgError"
                />
                <!-- Hover Overlay -->
                <div class="photo-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-3 text-white">
                  <div class="d-flex justify-content-between">
                    <span class="badge rounded-pill px-2 py-1 shadow-sm" :class="getCategoryBadgeClass(photo.category)" style="font-size: 0.7rem;">
                      {{ getCategoryName(photo.category) }}
                    </span>
                    <span class="btn btn-sm btn-light rounded-circle shadow p-0 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                      <i class="bi bi-arrows-fullscreen text-dark" style="font-size: 0.75rem;"></i>
                    </span>
                  </div>
                  <div>
                    <h6 class="small fw-bold mb-1 text-truncate">{{ photo.caption || photo.albumTitle }}</h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">{{ photo.eventDate }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FULLSCREEN LIGHTBOX MODAL -->
    <div
      v-if="lightboxActive && currentPhoto"
      class="lightbox-backdrop position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between text-white"
      tabindex="0"
      @keydown.esc="closeLightbox"
      @keydown.left="prevPhoto"
      @keydown.right="nextPhoto"
    >
      <!-- Top Toolbar -->
      <div class="d-flex justify-content-between align-items-center p-3 p-md-4 bg-dark bg-opacity-75 backdrop-blur z-3">
        <div class="d-flex align-items-center gap-2">
          <span class="badge px-3 py-2 rounded-pill" :class="getCategoryBadgeClass(currentPhoto.category)">
            <i :class="getCategoryIcon(currentPhoto.category)" class="me-1"></i> {{ getCategoryName(currentPhoto.category) }}
          </span>
          <span class="font-monospace small text-white-50">
            {{ activeLightboxIndex + 1 }} / {{ activeLightboxPhotos.length }}
          </span>
        </div>

        <div class="d-flex align-items-center gap-2">
          <a
            :href="formatAssetUrl(currentPhoto.url)"
            target="_blank"
            download
            class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm"
            title="Download Original Photo"
          >
            <i class="bi bi-download me-1"></i> Download
          </a>
          <button class="btn btn-sm btn-outline-light rounded-circle p-2 shadow-sm" @click="closeLightbox" title="Close (Esc)">
            <i class="bi bi-x-lg fs-6"></i>
          </button>
        </div>
      </div>

      <!-- Center Image with Nav Buttons -->
      <div class="position-relative flex-grow-1 d-flex align-items-center justify-content-center p-2 p-md-4 overflow-hidden">
        <!-- Previous Button -->
        <button
          v-if="activeLightboxPhotos.length > 1"
          class="nav-arrow nav-prev position-absolute start-0 top-50 translate-middle-y ms-2 ms-md-4 btn btn-light rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center z-3"
          @click.stop="prevPhoto"
          title="Previous Photo (Left Arrow)"
        >
          <i class="bi bi-chevron-left fs-4 text-dark"></i>
        </button>

        <!-- Main Displayed Image -->
        <div class="d-flex align-items-center justify-content-center h-100 w-100">
          <img
            :src="formatAssetUrl(currentPhoto.url)"
            :alt="currentPhoto.caption || 'Gallery Image'"
            class="lightbox-img rounded-3 shadow-lg"
            @error="onImgError"
          />
        </div>

        <!-- Next Button -->
        <button
          v-if="activeLightboxPhotos.length > 1"
          class="nav-arrow nav-next position-absolute end-0 top-50 translate-middle-y me-2 me-md-4 btn btn-light rounded-circle shadow-lg p-3 d-flex align-items-center justify-content-center z-3"
          @click.stop="nextPhoto"
          title="Next Photo (Right Arrow)"
        >
          <i class="bi bi-chevron-right fs-4 text-dark"></i>
        </button>
      </div>

      <!-- Bottom Caption Bar -->
      <div class="p-3 p-md-4 bg-dark bg-opacity-85 backdrop-blur text-center z-3">
        <h5 class="fw-bold mb-1">{{ currentPhoto.caption || currentPhoto.albumTitle }}</h5>
        <div class="text-white-50 small">
          <span v-if="currentPhoto.albumTitle && currentPhoto.caption !== currentPhoto.albumTitle" class="me-3">
            <i class="bi bi-folder2-open me-1"></i> {{ currentPhoto.albumTitle }}
          </span>
          <span v-if="currentPhoto.eventDate">
            <i class="bi bi-calendar3 me-1"></i> {{ currentPhoto.eventDate }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, onMounted, onUnmounted } from 'vue'

const websiteData = inject('websiteData', { content: {}, notices: [] })
const schoolLink = inject('schoolLink', (p) => p)

const gallery = computed(() => websiteData.content?.gallery || {})

// State
const activeCategory = ref('all')
const viewMode = ref('albums') // 'albums' or 'photos'

// Lightbox state
const lightboxActive = ref(false)
const activeLightboxPhotos = ref([])
const activeLightboxIndex = ref(0)

const currentPhoto = computed(() => {
  return activeLightboxPhotos.value[activeLightboxIndex.value] || null
})

// Categories
const defaultCategories = [
  { id: 'campus', name: 'Campus & Classrooms', icon: 'bi-building', description: 'Explore our modern digital classrooms, STEM laboratories, central library, and green campus infrastructure.' },
  { id: 'sports', name: 'Sports & Athletics', icon: 'bi-trophy', description: 'Annual sports meets, athletic track events, football tournaments, martial arts, and team championships.' },
  { id: 'annual_function', name: 'Annual Function & Cultural', icon: 'bi-music-note-beamed', description: 'Grand annual day celebrations, drama, choir performances, classical dance, and student award ceremonies.' },
  { id: 'academic', name: 'Science & Academic Fairs', icon: 'bi-lightbulb', description: 'Science fairs, working robotics prototypes, math olympiads, and inter-school academic symposiums.' },
  { id: 'celebrations', name: 'Celebrations & Festivals', icon: 'bi-stars', description: 'National celebrations, Independence Day, Teachers Day, Childrens Day, and institutional ceremonies.' },
]

const availableCategories = computed(() => {
  if (Array.isArray(gallery.value?.categories) && gallery.value.categories.length > 0) {
    return gallery.value.categories
  }
  return defaultCategories
})

const activeCategoryInfo = computed(() => {
  if (activeCategory.value === 'all') return null
  return availableCategories.value.find(c => c.id === activeCategory.value) || null
})

// Albums
const allAlbums = computed(() => {
  return Array.isArray(gallery.value?.albums) ? gallery.value.albums : []
})

const filteredAlbums = computed(() => {
  if (activeCategory.value === 'all') {
    return allAlbums.value
  }
  return allAlbums.value.filter(a => a.category === activeCategory.value)
})

// Flattened list of all individual photos
const allPhotos = computed(() => {
  const list = []
  allAlbums.value.forEach(album => {
    if (Array.isArray(album.images) && album.images.length > 0) {
      album.images.forEach(img => {
        list.push({
          id: img.id || `${album.id}_${Math.random()}`,
          url: img.url,
          caption: img.caption || album.title,
          category: album.category,
          albumId: album.id,
          albumTitle: album.title,
          eventDate: album.event_date || '',
        })
      })
    } else if (album.cover_image) {
      list.push({
        id: `${album.id}_cover`,
        url: album.cover_image,
        caption: album.title,
        category: album.category,
        albumId: album.id,
        albumTitle: album.title,
        eventDate: album.event_date || '',
      })
    }
  })
  return list
})

const filteredPhotos = computed(() => {
  if (activeCategory.value === 'all') {
    return allPhotos.value
  }
  return allPhotos.value.filter(p => p.category === activeCategory.value)
})

const totalPhotosCount = computed(() => allPhotos.value.length)

function getCategoryPhotosCount(catId) {
  return allPhotos.value.filter(p => p.category === catId).length
}

function getCategoryName(catId) {
  const cat = availableCategories.value.find(c => c.id === catId)
  return cat ? cat.name : (catId ? catId.toUpperCase() : 'General')
}

function getCategoryIcon(catId) {
  const cat = availableCategories.value.find(c => c.id === catId)
  if (cat?.icon) return `bi ${cat.icon}`
  switch (catId) {
    case 'campus': return 'bi bi-building'
    case 'sports': return 'bi bi-trophy-fill'
    case 'annual_function': return 'bi bi-music-note-beamed'
    case 'academic': return 'bi bi-lightbulb-fill'
    case 'celebrations': return 'bi bi-stars'
    default: return 'bi bi-image-fill'
  }
}

function getCategoryBadgeClass(catId) {
  switch (catId) {
    case 'campus': return 'bg-info text-dark'
    case 'sports': return 'bg-success text-white'
    case 'annual_function': return 'bg-warning text-dark'
    case 'academic': return 'bg-primary text-white'
    case 'celebrations': return 'bg-danger text-white'
    default: return 'bg-secondary text-white'
  }
}

function formatAssetUrl(url) {
  if (!url) return '/images/no-image.png'
  if (typeof url !== 'string') return '/images/no-image.png'
  const match = url.match(/^https?:\/\/(localhost|127\.0\.0\.1)(:\d+)?\/(storage\/.*)$/)
  if (match) {
    return '/' + match[3]
  }
  return url
}

function onImgError(e) {
  e.target.src = 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80'
}

// Lightbox Handlers
function openAlbum(album) {
  const photos = []
  if (Array.isArray(album.images) && album.images.length > 0) {
    album.images.forEach(img => {
      photos.push({
        id: img.id,
        url: img.url,
        caption: img.caption || album.title,
        category: album.category,
        albumTitle: album.title,
        eventDate: album.event_date,
      })
    })
  } else if (album.cover_image) {
    photos.push({
      id: `${album.id}_cover`,
      url: album.cover_image,
      caption: album.title,
      category: album.category,
      albumTitle: album.title,
      eventDate: album.event_date,
    })
  }
  openLightbox(photos, 0)
}

function openLightbox(photoArray, index = 0) {
  if (!photoArray || photoArray.length === 0) return
  activeLightboxPhotos.value = photoArray
  activeLightboxIndex.value = index
  lightboxActive.value = true
  document.body.style.overflow = 'hidden'
}

function closeLightbox() {
  lightboxActive.value = false
  activeLightboxPhotos.value = []
  document.body.style.overflow = ''
}

function prevPhoto() {
  if (activeLightboxPhotos.value.length <= 1) return
  activeLightboxIndex.value = (activeLightboxIndex.value - 1 + activeLightboxPhotos.value.length) % activeLightboxPhotos.value.length
}

function nextPhoto() {
  if (activeLightboxPhotos.value.length <= 1) return
  activeLightboxIndex.value = (activeLightboxIndex.value + 1) % activeLightboxPhotos.value.length
}

function handleKeydown(e) {
  if (!lightboxActive.value) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowLeft') prevPhoto()
  if (e.key === 'ArrowRight') nextPhoto()
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.album-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.album-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important;
}
.album-card:hover .album-thumb {
  transform: scale(1.05);
}

.photo-card {
  overflow: hidden;
}
.photo-card:hover .photo-img {
  transform: scale(1.08);
}
.photo-overlay {
  background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.85) 100%);
  opacity: 0;
  transition: opacity 0.25s ease;
}
.photo-card:hover .photo-overlay {
  opacity: 1;
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

.backdrop-blur {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.lightbox-backdrop {
  background: rgba(10, 15, 25, 0.96);
  z-index: 99999;
}

.lightbox-img {
  max-width: 90vw;
  max-height: 75vh;
  object-fit: contain;
  transition: all 0.2s ease;
}

.nav-arrow {
  opacity: 0.85;
  transition: transform 0.2s ease, opacity 0.2s ease;
}
.nav-arrow:hover {
  opacity: 1;
  transform: translateY(-50%) scale(1.1);
}
</style>
