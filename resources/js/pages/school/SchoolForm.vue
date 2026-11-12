<template>
  <div class="modal fade" ref="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <h5 class="modal-title fw-bold">
            <i class="bi bi-building me-2 text-primary"></i>
            {{ isEdit ? 'Edit School' : 'Add School' }}
          </h5>
          <button type="button" class="btn-close" @click="close" />
        </div>
        <div class="modal-body p-4">
          <form @submit.prevent="submit">
            <div class="row">
              <!-- Basic Info -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">School Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" v-model="form.name" placeholder="e.g. St. George English Day School" required />
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">School Code <span class="text-danger">*</span></label>
                <input
                  type="text"
                  class="form-control"
                  v-model="form.code"
                  placeholder="e.g. SGEDS01"
                  required
                  :disabled="!isSuperAdmin && isEdit"
                />
                <small v-if="!isSuperAdmin && isEdit" class="text-muted small">Managed by Super Admin</small>
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" v-model="form.email" placeholder="contact@school.com" />
              </div>

              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" class="form-control" v-model="form.phone" placeholder="+91 9876543210" />
              </div>

              <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold">Address</label>
                <textarea class="form-control" v-model="form.address" rows="2" placeholder="Street, locality, landmark..."></textarea>
              </div>

              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">City</label>
                <input type="text" class="form-control" v-model="form.city" placeholder="Kolkata" />
              </div>

              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">State</label>
                <input type="text" class="form-control" v-model="form.state" placeholder="West Bengal" />
              </div>

              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" class="form-control" v-model="form.country" placeholder="India" />
              </div>

              <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold">Postal Code</label>
                <input type="text" class="form-control" v-model="form.postal_code" placeholder="700001" />
              </div>

              <!-- Logo Upload Section -->
              <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold d-block">
                  School Logo
                  <small class="text-muted fw-normal">(Will appear across sidebar, header, website & portals)</small>
                </label>

                <div class="card bg-light border-dashed p-3">
                  <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">
                    <!-- Thumbnail Preview -->
                    <div class="position-relative text-center">
                      <div
                        class="rounded-3 border bg-white d-flex align-items-center justify-content-center shadow-sm overflow-hidden"
                        style="width: 100px; height: 100px; min-width: 100px;"
                      >
                        <img
                          v-if="previewUrl"
                          :src="previewUrl"
                          alt="Logo Preview"
                          style="max-width: 90px; max-height: 90px; object-fit: contain;"
                        />
                        <div v-else class="text-muted text-center p-2">
                          <i class="bi bi-image fs-1 d-block text-secondary opacity-50"></i>
                          <small style="font-size: 0.65rem;">No Logo</small>
                        </div>
                      </div>

                      <button
                        v-if="previewUrl"
                        type="button"
                        class="btn btn-sm btn-outline-danger py-0 px-2 mt-1"
                        style="font-size: 0.75rem;"
                        @click="removeLogo"
                        title="Remove logo"
                      >
                        <i class="bi bi-trash me-1"></i> Remove
                      </button>
                    </div>

                    <!-- Upload Controls -->
                    <div class="flex-grow-1">
                      <div class="mb-2">
                        <label class="btn btn-outline-primary btn-sm mb-1 cursor-pointer">
                          <i class="bi bi-cloud-arrow-up-fill me-1"></i>
                          {{ previewUrl ? 'Change Logo Image' : 'Upload School Logo' }}
                          <input
                            type="file"
                            class="d-none"
                            accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml"
                            @change="onFileSelected"
                            ref="fileInput"
                          />
                        </label>
                        <span v-if="selectedFileName" class="ms-2 small text-success fw-semibold">
                          <i class="bi bi-check-circle-fill me-1"></i>{{ selectedFileName }}
                        </span>
                      </div>
                      <small class="text-muted d-block mb-2">
                        Supported formats: PNG, JPG, JPEG, WEBP, SVG (Max: 5MB). High-resolution transparent PNG recommended.
                      </small>

                      <!-- Optional URL Input -->
                      <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted">Or URL</span>
                        <input
                          type="text"
                          class="form-control"
                          v-model="form.logo"
                          placeholder="https://example.com/logo.png"
                          @input="onUrlInput"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Subscription Plan (Super Admin only can edit) -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Subscription Plan</label>
                <select class="form-select" v-model="form.subscription_plan" :disabled="!isSuperAdmin">
                  <option value="starter">Starter / Basic (Core ERP)</option>
                  <option value="growth">Growth / Professional (+ Hostel, Library, Transport, Web CMS)</option>
                  <option value="enterprise">Enterprise (+ Custom Domain, Multi-Branch, APIs)</option>
                </select>
                <small v-if="!isSuperAdmin" class="text-muted small">Subscription package is managed by Super Admin</small>
              </div>

              <!-- Status (Super Admin only can edit) -->
              <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" v-model="form.status" :disabled="!isSuperAdmin">
                  <option :value="1">Active</option>
                  <option :value="0">Inactive</option>
                </select>
                <small v-if="!isSuperAdmin" class="text-muted small">Account status is managed by Super Admin</small>
              </div>
            </div>

            <div v-if="store.error" class="alert alert-danger py-2 mb-3">
              <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ store.error }}
            </div>

            <div class="modal-footer px-0 pb-0 pt-3 border-top">
              <button type="button" class="btn btn-secondary" @click="close">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="store.loading">
                <span v-if="store.loading" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-check-lg me-1"></i>
                {{ store.loading ? 'Saving School...' : (isEdit ? 'Update School' : 'Save School') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { useSchoolStore } from '@/stores/school';
import { useAuthStore } from '@/stores/auth';
import { Modal } from 'bootstrap';

const store = useSchoolStore();
const authStore = useAuthStore();

const isSuperAdmin = computed(() => {
  const u = authStore.user;
  return u?.user_type === 'super_admin' || u?.roles?.some(r => {
    const roleName = typeof r === 'string' ? r : (r.name || '');
    return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin';
  });
});

const modal = ref(null);
const fileInput = ref(null);
let bsModal = null;

const isEdit = ref(false);
const currentId = ref(null);

const selectedFile = ref(null);
const selectedFileName = ref('');
const previewUrl = ref('');
const isLogoRemoved = ref(false);

const form = reactive({
  name: '',
  code: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  country: '',
  postal_code: '',
  logo: '',
  subscription_plan: 'starter',
  status: 1,
});

const emit = defineEmits(['saved']);

function onFileSelected(e) {
  const file = e.target.files?.[0];
  if (!file) return;

  selectedFile.value = file;
  selectedFileName.value = file.name;
  isLogoRemoved.value = false;

  const reader = new FileReader();
  reader.onload = (event) => {
    previewUrl.value = event.target.result;
  };
  reader.readAsDataURL(file);
}

function onUrlInput() {
  if (form.logo && !selectedFile.value) {
    previewUrl.value = form.logo;
    isLogoRemoved.value = false;
  }
}

function removeLogo() {
  selectedFile.value = null;
  selectedFileName.value = '';
  previewUrl.value = '';
  form.logo = '';
  isLogoRemoved.value = true;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
}

const open = (school = null) => {
  isEdit.value = !!school;
  selectedFile.value = null;
  selectedFileName.value = '';
  isLogoRemoved.value = false;
  if (fileInput.value) {
    fileInput.value.value = '';
  }

  if (school) {
    currentId.value = school.id;
    const rawLogo = Array.isArray(school.logo) ? school.logo[0] : school.logo;
    const cleanLogo = (typeof rawLogo === 'string' && rawLogo.trim() !== '' && rawLogo !== '[]' && !rawLogo.endsWith('/[]') && rawLogo !== 'null' && rawLogo !== '{}')
      ? rawLogo.trim()
      : '';
    Object.assign(form, {
      name: school.name || '',
      code: school.code || '',
      email: school.email || '',
      phone: school.phone || '',
      address: school.address || '',
      city: school.city || '',
      state: school.state || '',
      country: school.country || '',
      postal_code: school.postal_code || '',
      logo: cleanLogo,
      subscription_plan: school.subscription_plan || 'starter',
      status: school.status ? 1 : 0
    });
    previewUrl.value = cleanLogo;
  } else {
    currentId.value = null;
    Object.assign(form, {
      name: '', code: '', email: '', phone: '', address: '',
      city: '', state: '', country: '', postal_code: '', logo: '',
      subscription_plan: 'starter', status: 1
    });
    previewUrl.value = '';
  }

  store.error = null;
  if (!bsModal) {
    bsModal = new Modal(modal.value);
  }
  bsModal.show();
};

const close = () => {
  if (bsModal) {
    bsModal.hide();
  }
};

const submit = async () => {
  try {
    const formData = new FormData();
    formData.append('name', form.name || '');
    formData.append('code', form.code || '');
    formData.append('email', form.email || '');
    formData.append('phone', form.phone || '');
    formData.append('address', form.address || '');
    formData.append('city', form.city || '');
    formData.append('state', form.state || '');
    formData.append('country', form.country || '');
    formData.append('postal_code', form.postal_code || '');
    formData.append('status', form.status ? '1' : '0');

    if (selectedFile.value) {
      formData.append('logo', selectedFile.value);
    } else if (isLogoRemoved.value) {
      formData.append('remove_logo', '1');
    } else if (form.logo && typeof form.logo === 'string') {
      const clean = form.logo.trim();
      if (clean && clean !== '[]' && clean !== 'null' && clean !== '{}') {
        formData.append('logo', clean);
      }
    }

    if (isEdit.value) {
      await store.updateSchool(currentId.value, formData);
    } else {
      await store.createSchool(formData);
    }

    // Refresh auth user so sidebar, navbar, and layouts instantly get the new logo
    await authStore.loadProfile();

    // Dispatch global event for other components to reload branding
    window.dispatchEvent(new CustomEvent('school-logo-updated', {
      detail: {
        logo: previewUrl.value,
        name: form.name
      }
    }));

    close();
    emit('saved');
  } catch (err) {
    console.error('Failed saving school:', err);
  }
};

defineExpose({ open });
</script>

<style scoped>
.border-dashed {
  border: 2px dashed #dee2e6;
}
.cursor-pointer {
  cursor: pointer;
}
</style>
