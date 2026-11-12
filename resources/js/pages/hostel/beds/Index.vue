<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Beds</h4>
            <small class="text-muted">Manage bed allocation units and status</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-plus-lg me-1"></i> Add Bed
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
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search bed no, room or building..." @keyup.enter="loadBeds(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Room</label>
                    <select v-model="filters.hostel_room_id" class="form-select" @change="loadBeds(1)">
                        <option value="">All Rooms</option>
                        <option v-for="r in rooms" :key="r.id" :value="r.id">Room {{ r.room_no }} ({{ r.building?.name }})</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadBeds(1)">
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
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
                            <th>Bed No.</th>
                            <th>Room</th>
                            <th>Building</th>
                            <th>Occupant</th>
                            <th>Monthly Fee</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading beds...</div>
                            </td>
                        </tr>
                        <tr v-else-if="beds.length === 0">
                            <td colspan="8" class="text-center py-5 text-muted">No beds found.</td>
                        </tr>
                        <tr v-for="(b, idx) in beds" :key="b.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td><strong>{{ b.bed_no }}</strong></td>
                            <td>Room {{ b.room?.room_no || '-' }}</td>
                            <td>{{ b.room?.building?.name || '-' }}</td>
                            <td>
                                <span v-if="b.allocation?.student" class="text-primary fw-semibold">
                                    {{ b.allocation.student.first_name }} {{ b.allocation.student.last_name }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>₹{{ Number(b.monthly_fee || 0).toLocaleString() }}</td>
                            <td>
                                <span :class="['badge text-capitalize', b.status === 'available' ? 'bg-success' : (b.status === 'occupied' ? 'bg-primary' : 'bg-warning text-dark')]">
                                    {{ b.status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit" @click="openEdit(b)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteBed(b)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ beds.length }} of {{ pagination.total }} beds</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadBeds(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadBeds(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveBed">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editing ? 'Edit Bed' : 'Add Bed' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Room <span class="text-danger">*</span></label>
                                <select v-model="form.hostel_room_id" class="form-select" required>
                                    <option value="">Select Room</option>
                                    <option v-for="r in rooms" :key="r.id" :value="r.id">Room {{ r.room_no }} ({{ r.building?.name }})</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bed No. / Label <span class="text-danger">*</span></label>
                                <input v-model="form.bed_no" type="text" class="form-control" required placeholder="e.g. Bed-A, Bed-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select v-model="form.status" class="form-select" required>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monthly Fee (₹)</label>
                                <input v-model.number="form.monthly_fee" type="number" step="0.01" min="0" class="form-control" placeholder="0.00">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea v-model="form.remarks" class="form-control" rows="2" placeholder="Optional notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            {{ editing ? 'Update Bed' : 'Save Bed' }}
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

const beds = ref([]);
const rooms = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const editing = ref(false);
const editingId = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', hostel_room_id: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({ hostel_room_id: '', bed_no: '', status: 'available', monthly_fee: 0, remarks: '' });
const form = ref(emptyForm());

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadRooms = async () => {
    try {
        const res = await axios.get('/api/hostel/beds/rooms', getAuthConfig());
        if (res.data?.success) rooms.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadBeds = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/beds', { ...getAuthConfig(), params });
        if (res.data?.success) {
            beds.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load beds.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', hostel_room_id: '', status: '', per_page: 20 };
    loadBeds(1);
};

const openCreate = () => {
    editing.value = false;
    editingId.value = null;
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const openEdit = (b) => {
    editing.value = true;
    editingId.value = b.id;
    form.value = { ...b };
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveBed = async () => {
    saving.value = true;
    formError.value = '';
    try {
        if (editing.value) {
            await axios.put(`/api/hostel/beds/${editingId.value}`, form.value, getAuthConfig());
            success.value = 'Bed updated successfully.';
        } else {
            await axios.post('/api/hostel/beds', form.value, getAuthConfig());
            success.value = 'Bed created successfully.';
        }
        closeModal();
        loadBeds(pagination.value.current_page);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to save bed.';
    } finally {
        saving.value = false;
    }
};

const deleteBed = async (b) => {
    if (!confirm(`Are you sure you want to delete bed "${b.bed_no}"?`)) return;
    try {
        await axios.delete(`/api/hostel/beds/${b.id}`, getAuthConfig());
        success.value = 'Bed deleted successfully.';
        loadBeds(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete bed.';
    }
};

onMounted(() => {
    loadRooms();
    loadBeds();
});
</script>
