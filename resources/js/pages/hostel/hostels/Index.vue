<template>
<div class="container-fluid py-2">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Hostel', to: '/hostel/dashboard' },
            { label: 'Hostels' }
        ]"
    />
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostels</h4>
            <small class="text-muted">Manage hostel facilities and warden details</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-plus-lg me-1"></i> Add Hostel
        </button>
    </div>

    <!-- Alerts -->
    <div v-if="success" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ success }}
        <button type="button" class="btn-close" @click="success = ''"></button>
    </div>
    <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ error }}
        <button type="button" class="btn-close" @click="error = ''"></button>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <!-- School Filter (Super Admin) -->
                <div class="col-md-3" v-if="isSuperAdmin && allSchools.length > 0">
                    <label class="form-label">School / Campus</label>
                    <select v-model="filters.school_id" class="form-select fw-semibold" @change="loadHostels(1)">
                        <option value="">All Schools</option>
                        <option v-for="s in allSchools" :key="'hst-sch-' + s.id" :value="s.id">
                            {{ s.name }} ({{ s.code }})
                        </option>
                    </select>
                </div>
                <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-3' : 'col-md-5'">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search by name, code or warden..." @keyup.enter="loadHostels(1)">
                </div>
                <div :class="(isSuperAdmin && allSchools.length > 0) ? 'col-md-2' : 'col-md-3'">
                    <label class="form-label">Type</label>
                    <select v-model="filters.type" class="form-select" @change="loadHostels(1)">
                        <option value="">All Types</option>
                        <option value="boys">Boys</option>
                        <option value="girls">Girls</option>
                        <option value="co-ed">Co-ed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadHostels(1)">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" @click="resetFilters">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th v-if="isSuperAdmin">School / Campus</th>
                            <th>Hostel Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Warden</th>
                            <th>Contact</th>
                            <th>Buildings</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td :colspan="isSuperAdmin ? 11 : 10" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading hostels...</div>
                            </td>
                        </tr>
                        <tr v-else-if="hostels.length === 0">
                            <td :colspan="isSuperAdmin ? 11 : 10" class="text-center py-5 text-muted">No hostels found.</td>
                        </tr>
                        <tr v-for="(h, idx) in hostels" :key="h.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td v-if="isSuperAdmin">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                    <i class="bi bi-building me-1"></i> {{ h.school?.name || 'School' }}
                                </span>
                                <small v-if="h.school?.code" class="text-muted d-block font-monospace">
                                    ({{ h.school.code }})
                                </small>
                            </td>
                            <td>
                                <strong>{{ h.name }}</strong>
                                <small v-if="h.address" class="d-block text-muted text-truncate" style="max-width: 200px;">{{ h.address }}</small>
                            </td>
                            <td><code>{{ h.code || '-' }}</code></td>
                            <td>
                                <span :class="['badge text-uppercase', h.type === 'boys' ? 'bg-primary' : (h.type === 'girls' ? 'bg-danger' : 'bg-info')]">
                                    {{ h.type }}
                                </span>
                            </td>
                            <td>{{ h.warden_name || '-' }}</td>
                            <td>{{ h.warden_phone || h.warden_email || '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ h.buildings_count || 0 }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ h.students_count || 0 }}</span></td>
                            <td>
                                <span :class="['badge', h.status ? 'bg-success' : 'bg-secondary']">
                                    {{ h.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit" @click="openEdit(h)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-warning me-1" title="Toggle Status" @click="toggleStatus(h)"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteHostel(h)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ hostels.length }} of {{ pagination.total }} hostels</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadHostels(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadHostels(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveHostel">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editing ? 'Edit Hostel' : 'Add Hostel' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hostel Name <span class="text-danger">*</span></label>
                                <input v-model="form.name" type="text" class="form-control" required placeholder="e.g. Tagore Boys Hostel">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Code</label>
                                <input v-model="form.code" type="text" class="form-control" placeholder="e.g. TBH-01">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select v-model="form.type" class="form-select" required>
                                    <option value="boys">Boys</option>
                                    <option value="girls">Girls</option>
                                    <option value="co-ed">Co-ed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warden Name</label>
                                <input v-model="form.warden_name" type="text" class="form-control" placeholder="Warden's name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warden Phone</label>
                                <input v-model="form.warden_phone" type="text" class="form-control" placeholder="Phone number">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Warden Email</label>
                                <input v-model="form.warden_email" type="email" class="form-control" placeholder="Email address">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea v-model="form.address" class="form-control" rows="2" placeholder="Full address"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description / Facilities</label>
                                <textarea v-model="form.description" class="form-control" rows="2" placeholder="Rules, notes or facility details"></textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status</label>
                                <select v-model="form.status" class="form-select">
                                    <option :value="true">Active</option>
                                    <option :value="false">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            {{ editing ? 'Update Hostel' : 'Save Hostel' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const allSchools = ref([]);
const hostels = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const editing = ref(false);
const editingId = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', school_id: '', type: '', status: '', per_page: 15 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 });

const emptyForm = () => ({
    name: '', code: '', type: 'boys', warden_name: '', warden_phone: '', warden_email: '', address: '', description: '', status: true
});
const form = ref(emptyForm());

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadSchools = async () => {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get('/api/tenants', getAuthConfig());
            if (res.data?.data?.length) {
                allSchools.value = res.data.data;
                return;
            }
        } catch (e) {}

        try {
            const res2 = await axios.get('/api/public/saas/schools');
            if (res2.data?.data?.length) {
                allSchools.value = res2.data.data;
            }
        } catch (e2) {}
    }
};

const loadHostels = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/hostels', { ...getAuthConfig(), params });
        if (res.data?.success) {
            hostels.value = res.data.data;
            pagination.value = res.data.meta;
            if (res.data.all_schools?.length) {
                allSchools.value = res.data.all_schools;
            }
        }
    } catch (e) {
        error.value = e.response?.data?.message || 'Failed to load hostels.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', school_id: '', type: '', status: '', per_page: 15 };
    loadHostels(1);
};

const openCreate = () => {
    editing.value = false;
    editingId.value = null;
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const openEdit = (h) => {
    editing.value = true;
    editingId.value = h.id;
    form.value = { ...h, status: Boolean(h.status) };
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveHostel = async () => {
    saving.value = true;
    formError.value = '';
    try {
        if (editing.value) {
            await axios.put(`/api/hostel/hostels/${editingId.value}`, form.value, getAuthConfig());
            success.value = 'Hostel updated successfully.';
        } else {
            await axios.post('/api/hostel/hostels', form.value, getAuthConfig());
            success.value = 'Hostel created successfully.';
        }
        closeModal();
        loadHostels(pagination.value.current_page);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to save hostel.';
    } finally {
        saving.value = false;
    }
};

const toggleStatus = async (h) => {
    try {
        await axios.patch(`/api/hostel/hostels/${h.id}/status`, {}, getAuthConfig());
        h.status = !h.status;
        success.value = 'Hostel status updated.';
    } catch (e) {
        error.value = 'Failed to update status.';
    }
};

const deleteHostel = async (h) => {
    if (!confirm(`Are you sure you want to delete "${h.name}"?`)) return;
    try {
        await axios.delete(`/api/hostel/hostels/${h.id}`, getAuthConfig());
        success.value = 'Hostel deleted successfully.';
        loadHostels(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete hostel.';
    }
};

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        loadHostels(),
    ]);
});
</script>
