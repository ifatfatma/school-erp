<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Room Allocation</h4>
            <small class="text-muted">Allocate hostel rooms and beds to students, manage vacancies</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-key me-1"></i> Allocate Room
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
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search student name, adm no or room..." @keyup.enter="loadAllocations(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadAllocations(1)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="vacated">Vacated</option>
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
                            <th>Student</th>
                            <th>Class</th>
                            <th>Building & Room</th>
                            <th>Bed</th>
                            <th>Allocated Date</th>
                            <th>Vacated Date</th>
                            <th>Fee (₹)</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading allocations...</div>
                            </td>
                        </tr>
                        <tr v-else-if="allocations.length === 0">
                            <td colspan="10" class="text-center py-5 text-muted">No room allocations found.</td>
                        </tr>
                        <tr v-for="(a, idx) in allocations" :key="a.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ a.student?.first_name }} {{ a.student?.last_name }}</strong>
                                <small class="text-muted d-block">Adm: {{ a.student?.admission_no || '-' }}</small>
                            </td>
                            <td>{{ a.student?.class?.name || '-' }}</td>
                            <td>
                                <div><strong>Room {{ a.room?.room_no || '-' }}</strong></div>
                                <small class="text-muted">{{ a.room?.building?.name || '-' }} (Floor {{ a.room?.floor_no }})</small>
                            </td>
                            <td>
                                <span v-if="a.bed" class="badge bg-light text-dark border">{{ a.bed.bed_no }}</span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>{{ a.allocated_date }}</td>
                            <td>{{ a.vacated_date || '-' }}</td>
                            <td>₹{{ Number(a.monthly_fee || 0).toLocaleString() }}</td>
                            <td>
                                <span :class="['badge text-capitalize', a.status === 'active' ? 'bg-success' : 'bg-secondary']">
                                    {{ a.status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button v-if="a.status === 'active'" class="btn btn-sm btn-outline-warning me-1" title="Vacate Room" @click="openVacate(a)">
                                    <i class="bi bi-box-arrow-right"></i> Vacate
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteAllocation(a)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ allocations.length }} of {{ pagination.total }} records</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadAllocations(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadAllocations(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Allocate) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveAllocation">
                    <div class="modal-header">
                        <h5 class="modal-title">Allocate Room to Student</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Student <span class="text-danger">*</span></label>
                                <select v-model="form.student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    <option v-for="st in dropdowns.students" :key="st.id" :value="st.id">
                                        {{ st.first_name }} {{ st.last_name }} ({{ st.admission_no }}) - {{ st.class?.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room <span class="text-danger">*</span></label>
                                <select v-model="form.hostel_room_id" class="form-select" required @change="onRoomSelect">
                                    <option value="">Select Room</option>
                                    <option v-for="r in dropdowns.rooms" :key="r.id" :value="r.id">Room {{ r.room_no }} ({{ r.building?.name }})</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bed</label>
                                <select v-model="form.hostel_bed_id" class="form-select">
                                    <option value="">Select Available Bed</option>
                                    <option v-for="b in availableBeds" :key="b.id" :value="b.id">{{ b.bed_no }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Allocated Date <span class="text-danger">*</span></label>
                                <input v-model="form.allocated_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Monthly Fee (₹)</label>
                                <input v-model.number="form.monthly_fee" type="number" min="0" step="0.01" class="form-control">
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
                            Allocate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Vacate Modal -->
    <div v-if="showVacateModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="submitVacate">
                    <div class="modal-header">
                        <h5 class="modal-title">Vacate Room</h5>
                        <button type="button" class="btn-close" @click="showVacateModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <p>Vacate room for <strong>{{ vacateTarget?.student?.first_name }} {{ vacateTarget?.student?.last_name }}</strong> from <strong>Room {{ vacateTarget?.room?.room_no }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Vacate Date</label>
                            <input v-model="vacateForm.vacated_date" type="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks / Clearance Notes</label>
                            <textarea v-model="vacateForm.remarks" class="form-control" rows="2" placeholder="Keys returned, room inspected, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showVacateModal = false">Cancel</button>
                        <button type="submit" class="btn btn-warning" :disabled="saving">Confirm Vacate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const allocations = ref([]);
const dropdowns = ref({ students: [], rooms: [] });
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showVacateModal = ref(false);
const vacateTarget = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({
    student_id: '', hostel_room_id: '', hostel_bed_id: '',
    allocated_date: new Date().toISOString().slice(0, 10), monthly_fee: 0, remarks: ''
});
const form = ref(emptyForm());
const vacateForm = ref({ vacated_date: new Date().toISOString().slice(0, 10), remarks: '' });

const availableBeds = computed(() => {
    if (!form.value.hostel_room_id) return [];
    const r = dropdowns.value.rooms.find(rm => rm.id === form.value.hostel_room_id);
    return r?.beds || [];
});

const onRoomSelect = () => {
    form.value.hostel_bed_id = '';
    const r = dropdowns.value.rooms.find(rm => rm.id === form.value.hostel_room_id);
    if (r?.monthly_fee) {
        form.value.monthly_fee = r.monthly_fee;
    }
};

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadDropdowns = async () => {
    try {
        const res = await axios.get('/api/hostel/allocations/dropdowns', getAuthConfig());
        if (res.data?.success) dropdowns.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadAllocations = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/allocations', { ...getAuthConfig(), params });
        if (res.data?.success) {
            allocations.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load allocations.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', status: '', per_page: 20 };
    loadAllocations(1);
};

const openCreate = () => {
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveAllocation = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/hostel/allocations', form.value, getAuthConfig());
        success.value = 'Room allocated successfully.';
        closeModal();
        loadAllocations(1);
        loadDropdowns();
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to allocate room.';
    } finally {
        saving.value = false;
    }
};

const openVacate = (a) => {
    vacateTarget.value = a;
    vacateForm.value = { vacated_date: new Date().toISOString().slice(0, 10), remarks: '' };
    showVacateModal.value = true;
};

const submitVacate = async () => {
    saving.value = true;
    try {
        await axios.post(`/api/hostel/allocations/${vacateTarget.value.id}/vacate`, vacateForm.value, getAuthConfig());
        success.value = 'Room vacated successfully.';
        showVacateModal.value = false;
        loadAllocations(pagination.value.current_page);
        loadDropdowns();
    } catch (e) {
        error.value = 'Failed to vacate room.';
    } finally {
        saving.value = false;
    }
};

const deleteAllocation = async (a) => {
    if (!confirm('Are you sure you want to delete this allocation record?')) return;
    try {
        await axios.delete(`/api/hostel/allocations/${a.id}`, getAuthConfig());
        success.value = 'Allocation record deleted.';
        loadAllocations(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete allocation.';
    }
};

onMounted(() => {
    loadDropdowns();
    loadAllocations();
});
</script>
