<template>
  <div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'School Management' },
            { label: 'Schools' }
        ]"
    />

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div>
        <h3 class="mb-0 fw-bold">
          {{ isSuperAdmin ? 'Schools' : (canAddSchool || store.schools.length > 1 ? 'My Managed Schools' : 'School Profile & Branding') }}
        </h3>
        <small class="text-muted" v-if="!isSuperAdmin">
          {{ canAddSchool || store.schools.length > 1
            ? 'Manage and switch between your assigned schools, or add a new school institution.'
            : 'Manage your institution\'s official details, contact info, school logo, and login page picture.'
          }}
        </small>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button v-if="canAddSchool" class="btn btn-primary" @click="openCreateModal">
          <i class="bi bi-plus-circle me-1"></i> Add School <span v-if="!isSuperAdmin" class="badge bg-white text-primary ms-1">{{ store.schools.length }}/{{ tenantStore.maxSchools }}</span>
        </button>
        <router-link
          v-else-if="!isSuperAdmin"
          to="/subscription/billing"
          class="btn btn-warning text-dark fw-semibold"
          title="Upgrade plan to register more schools"
        >
          <i class="bi bi-arrow-up-circle-fill me-1"></i> Upgrade Plan to Add More Schools ({{ store.schools.length }}/{{ tenantStore.maxSchools }})
        </router-link>
        <button v-if="!isSuperAdmin && store.schools.length > 0" class="btn btn-outline-secondary" @click="openEditModal(store.schools[0])">
          <i class="bi bi-pencil-square me-1"></i> Edit School Branding
        </button>
      </div>
    </div>

    <!-- Search -->
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input
            type="text"
            class="form-control"
            v-model="search"
            placeholder="Search school by name or code..."
            @input="searchSchools"
          />
          <button v-if="search" class="btn btn-outline-secondary" @click="clearSearch">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="text-center py-5">
      <div class="spinner-border text-primary me-2" />
      <span>Loading schools...</span>
    </div>

    <!-- Error -->
    <div v-else-if="store.error" class="alert alert-danger">
      <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ store.error }}
    </div>

    <!-- Table -->
    <div v-else class="card">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th width="110" class="text-center">Branding</th>
              <th>Name</th>
              <th>Code</th>
              <th>Email</th>
              <th>Phone</th>
              <th width="120">Plan</th>
              <th width="90">Status</th>
              <th width="130" class="text-center" v-if="canSwitch || isSuperAdmin">Active Context</th>
              <th width="140" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="school in store.schools" :key="school.id">
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1">
                  <img
                    v-if="isValidLogo(school)"
                    :src="school.logo"
                    alt="Logo"
                    class="rounded border bg-white p-1"
                    style="width: 40px; height: 40px; object-fit: contain;"
                    title="School Logo"
                    @error="onLogoError(school.id)"
                  />
                  <div
                    v-else
                    class="rounded bg-light text-secondary border d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px;"
                    title="No logo"
                  >
                    <i class="bi bi-building fs-5"></i>
                  </div>

                  <img
                    v-if="school.login_background"
                    :src="school.login_background"
                    alt="Login Backdrop"
                    class="rounded border"
                    style="width: 40px; height: 40px; object-fit: cover;"
                    title="Custom Login Background Active"
                  />
                </div>
              </td>
              <td>
                <div class="fw-bold">{{ school.name }}</div>
                <small class="text-muted" v-if="school.address">{{ school.address }}</small>
              </td>
              <td><code>{{ school.code }}</code></td>
              <td>{{ school.email || '-' }}</td>
              <td>{{ school.phone || '-' }}</td>
              <td>
                <span
                  class="badge text-uppercase"
                  :class="{
                    'bg-secondary': !school.subscription_plan || school.subscription_plan === 'starter' || school.subscription_plan === 'basic',
                    'bg-success': school.subscription_plan === 'growth' || school.subscription_plan === 'professional',
                    'bg-primary': school.subscription_plan === 'enterprise'
                  }"
                >
                  {{ school.subscription_plan || 'starter' }}
                </span>
              </td>
              <td>
                <span :class="school.status ? 'badge bg-success' : 'badge bg-secondary'">
                  {{ school.status ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="text-center" v-if="canSwitch || isSuperAdmin">
                <span
                  v-if="String(school.id) === String(tenantStore.activeSchoolId)"
                  class="badge bg-success-subtle text-success border border-success px-2 py-1"
                >
                  <i class="bi bi-check-circle-fill me-1"></i> Active
                </span>
                <button
                  v-else
                  class="btn btn-sm btn-outline-primary"
                  @click="handleSwitch(school)"
                  :disabled="switchingId === school.id"
                  title="Switch to this school"
                >
                  <span v-if="switchingId === school.id" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-arrow-repeat me-1"></i>
                  Switch
                </button>
              </td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <router-link
                    :to="'/s/' + school.code"
                    target="_blank"
                    class="btn btn-outline-primary"
                    title="Open Public School Website"
                  >
                    <i class="bi bi-box-arrow-up-right"></i>
                  </router-link>
                  <button
                    class="btn btn-warning text-dark"
                    :title="isSuperAdmin ? 'Edit School & Plan' : 'Edit School Information'"
                    @click="openEditModal(school)"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button
                    v-if="isSuperAdmin"
                    class="btn btn-danger"
                    title="Delete School"
                    @click="confirmDelete(school.id)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>

            <tr v-if="!store.schools.length">
              <td colspan="8" class="text-center py-4 text-muted">
                No schools found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer bg-white d-flex justify-content-between align-items-center p-3" v-if="store.pagination.total > 0">
        <small class="text-muted">
          Showing page {{ store.pagination.current_page }} of {{ store.pagination.last_page }} ({{ store.pagination.total }} total)
        </small>
        <ul class="pagination pagination-sm mb-0">
          <li class="page-item" :class="{ disabled: store.pagination.current_page <= 1 }">
            <a class="page-link" href="#" @click.prevent="changePage(store.pagination.current_page - 1)">Previous</a>
          </li>
          <li class="page-item active">
            <span class="page-link">{{ store.pagination.current_page }}</span>
          </li>
          <li class="page-item" :class="{ disabled: store.pagination.current_page >= store.pagination.last_page }">
            <a class="page-link" href="#" @click.prevent="changePage(store.pagination.current_page + 1)">Next</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Modal for Create/Edit -->
    <SchoolForm ref="formModal" @saved="onSaved" />
  </div>
</template>

<script setup>
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { ref, reactive, computed, onMounted } from 'vue';
import Swal from 'sweetalert2';
import { useSchoolStore } from '@/stores/school';
import { useAuthStore } from '@/stores/auth';
import { useTenantStore } from '@/stores/tenant';
import SchoolForm from '@/pages/school/SchoolForm.vue';

const store = useSchoolStore();
const authStore = useAuthStore();
const tenantStore = useTenantStore();
const search = ref('');
const formModal = ref(null);
const failedLogos = reactive({});
const switchingId = ref(null);

const isSuperAdmin = computed(() => {
  const u = authStore.user;
  return u?.user_type === 'super_admin' || u?.roles?.some(r => {
    const roleName = typeof r === 'string' ? r : (r.name || '');
    return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin';
  });
});

const canAddSchool = computed(() => {
  if (isSuperAdmin.value) return true;
  return !!tenantStore.canCreateSchool && (store.schools.length < tenantStore.maxSchools);
});

const canSwitch = computed(() => {
  return isSuperAdmin.value || (tenantStore.maxSchools > 1 && store.schools.length > 1);
});

const handleSwitch = async (school) => {
  if (String(school.id) === String(tenantStore.activeSchoolId)) return;
  switchingId.value = school.id;
  try {
    await tenantStore.switchTenant(school.id);
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: `Switched to ${school.name}`,
      showConfirmButton: false,
      timer: 2000
    });
    // Refresh school data for new context
    await store.fetchSchools({ page: store.pagination.current_page, search: search.value });
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Failed to switch school',
      text: err.response?.data?.message || 'Error occurred while switching schools.'
    });
  } finally {
    switchingId.value = null;
  }
};

function onLogoError(id) {
  if (id) {
    failedLogos[id] = true;
  }
}

function isValidLogo(school) {
  if (!school || !school.logo || typeof school.logo !== 'string') return false;
  if (failedLogos[school.id]) return false;
  const clean = school.logo.trim();
  if (clean === '' || clean === '[]' || clean.endsWith('/[]') || clean === 'null' || clean === '{}') return false;
  return true;
}

const loadSchools = () => {
  store.fetchSchools({ page: store.pagination.current_page, search: search.value });
};

onMounted(loadSchools);

const searchSchools = () => {
  store.pagination.current_page = 1;
  loadSchools();
};

const clearSearch = () => {
  search.value = '';
  searchSchools();
};

const changePage = (page) => {
  if (page >= 1 && page <= store.pagination.last_page) {
    store.pagination.current_page = page;
    loadSchools();
  }
};

const openCreateModal = () => {
  formModal.value.open();
};

const openEditModal = (school) => {
  formModal.value.open(school);
};

const confirmDelete = async (id) => {
  const result = await Swal.fire({
    title: 'Delete School?',
    text: 'Are you sure you want to delete this school record?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Delete',
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
  });

  if (result.isConfirmed) {
    try {
      await store.deleteSchool(id);
      Swal.fire('Deleted!', 'School has been deleted.', 'success');
    } catch (e) {
      Swal.fire('Error', 'Failed to delete school.', 'error');
    }
  }
};

const onSaved = () => {
  loadSchools();
  tenantStore.fetchTenants();
};
</script>
