<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Floors</h4>
            <small class="text-muted">Manage building floors and structural layout</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-plus-lg me-1"></i> Add Floor
        </button>
    </div>

    <!-- Alerts -->
    <div v-if="success" class="alert alert-success alert-dismissible fade show">
        {{ success }}
        <button type="button" class="btn-close" @click="success = ''"></button>
    </div>
    <div v-if="error" class="alert alert-danger alert-dismissible fade show">
        {{ error }}
        <button type="button" class="btn-close" @click="error = ''"></button>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search floor name or building..." @keyup.enter="loadFloors(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Building</label>
                    <select v-model="filters.hostel_building_id" class="form-select" @change="loadFloors(1)">
                        <option value="">All Buildings</option>
                        <option v-for="b in buildings" :key="b.id" :value="b.id">{{ b.name }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadFloors(1)">
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

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Building</th>
                            <th>Floor No.</th>
                            <th>Floor Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="7" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading floors...</div>
                            </td>
                        </tr>
                        <tr v-else-if="floors.length === 0">
                            <td colspan="7" class="text-center py-5 text-muted">No floors found.</td>
                        </tr>
                        <tr v-for="(f, idx) in floors" :key="f.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ f.building?.name || '-' }}</strong>
                                <small v-if="f.building?.code" class="text-muted d-block"><code>{{ f.building.code }}</code></small>
                            </td>
                            <td><span class="badge bg-light text-dark border">Floor {{ f.floor_no }}</span></td>
                            <td><strong>{{ f.name }}</strong></td>
                            <td><span class="text-muted small">{{ f.description || '-' }}</span></td>
                            <td>
                                <span :class="['badge', f.status ? 'bg-success' : 'bg-secondary']">
                                    {{ f.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit" @click="openEdit(f)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-warning me-1" title="Toggle Status" @click="toggleStatus(f)"><i class="bi bi-toggle-on"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteFloor(f)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ floors.length }} of {{ pagination.total }} floors</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadFloors(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadFloors(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveFloor">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editing ? 'Edit Floor' : 'Add Floor' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Building <span class="text-danger">*</span></label>
                                <select v-model="form.hostel_building_id" class="form-select" required>
                                    <option value="">Select Building</option>
                                    <option v-for="b in buildings" :key="b.id" :value="b.id">{{ b.name }} ({{ b.code || 'B' }})</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Floor Number <span class="text-danger">*</span></label>
                                <input v-model.number="form.floor_no" type="number" class="form-control" required placeholder="0 for Ground, 1, 2...">
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">Floor Name <span class="text-danger">*</span></label>
                                <input v-model="form.name" type="text" class="form-control" required placeholder="e.g. Ground Floor, First Floor">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description / Remarks</label>
                                <textarea v-model="form.description" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                            </div>
                            <div class="col-md-6">
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
                            {{ editing ? 'Update Floor' : 'Save Floor' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const floors = ref([]);
const buildings = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const editing = ref(false);
const editingId = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', hostel_building_id: '', status: '', per_page: 15 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 15, total: 0 });

const emptyForm = () => ({ hostel_building_id: '', floor_no: 1, name: '', description: '', status: true });
const form = ref(emptyForm());

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadBuildings = async () => {
    try {
        const res = await axios.get('/api/hostel/floors/buildings', getAuthConfig());
        if (res.data?.success) buildings.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadFloors = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/floors', { ...getAuthConfig(), params });
        if (res.data?.success) {
            floors.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load floors.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', hostel_building_id: '', status: '', per_page: 15 };
    loadFloors(1);
};

const openCreate = () => {
    editing.value = false;
    editingId.value = null;
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const openEdit = (f) => {
    editing.value = true;
    editingId.value = f.id;
    form.value = { ...f, status: Boolean(f.status) };
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveFloor = async () => {
    saving.value = true;
    formError.value = '';
    try {
        if (editing.value) {
            await axios.put(`/api/hostel/floors/${editingId.value}`, form.value, getAuthConfig());
            success.value = 'Floor updated successfully.';
        } else {
            await axios.post('/api/hostel/floors', form.value, getAuthConfig());
            success.value = 'Floor created successfully.';
        }
        closeModal();
        loadFloors(pagination.value.current_page);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to save floor.';
    } finally {
        saving.value = false;
    }
};

const toggleStatus = async (f) => {
    try {
        await axios.patch(`/api/hostel/floors/${f.id}/status`, {}, getAuthConfig());
        f.status = !f.status;
        success.value = 'Floor status updated.';
    } catch (e) {
        error.value = 'Failed to update status.';
    }
};

const deleteFloor = async (f) => {
    if (!confirm(`Are you sure you want to delete floor "${f.name}"?`)) return;
    try {
        await axios.delete(`/api/hostel/floors/${f.id}`, getAuthConfig());
        success.value = 'Floor deleted successfully.';
        loadFloors(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete floor.';
    }
};

onMounted(() => {
    loadBuildings();
    loadFloors();
});
</script>
