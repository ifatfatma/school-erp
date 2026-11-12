<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Room Transfer</h4>
            <small class="text-muted">Transfer resident students between rooms and beds</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-arrow-left-right me-1"></i> New Transfer
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
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search student name, adm no..." @keyup.enter="loadTransfers(1)">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadTransfers(1)">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
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
                            <th>From (Room & Bed)</th>
                            <th>To (Room & Bed)</th>
                            <th>Transfer Date</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th width="100" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="8" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading transfer records...</div>
                            </td>
                        </tr>
                        <tr v-else-if="transfers.length === 0">
                            <td colspan="8" class="text-center py-5 text-muted">No room transfer records found.</td>
                        </tr>
                        <tr v-for="(t, idx) in transfers" :key="t.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ t.student?.first_name }} {{ t.student?.last_name }}</strong>
                                <small class="text-muted d-block">Adm: {{ t.student?.admission_no || '-' }}</small>
                            </td>
                            <td>
                                <div><strong class="text-danger">Room {{ t.from_room?.room_no || '-' }}</strong></div>
                                <small class="text-muted">{{ t.from_room?.building?.name }} • Bed {{ t.from_bed?.bed_no || '-' }}</small>
                            </td>
                            <td>
                                <div><strong class="text-success">Room {{ t.to_room?.room_no || '-' }}</strong></div>
                                <small class="text-muted">{{ t.to_room?.building?.name }} • Bed {{ t.to_bed?.bed_no || '-' }}</small>
                            </td>
                            <td>{{ t.transfer_date }}</td>
                            <td><span class="small">{{ t.reason || '-' }}</span></td>
                            <td>
                                <span :class="['badge text-capitalize', t.status === 'approved' ? 'bg-success' : 'bg-warning text-dark']">
                                    {{ t.status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger" title="Delete Record" @click="deleteTransfer(t)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ transfers.length }} of {{ pagination.total }} records</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadTransfers(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadTransfers(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Transfer) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveTransfer">
                    <div class="modal-header">
                        <h5 class="modal-title">Transfer Student Room</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Resident Student <span class="text-danger">*</span></label>
                                <select v-model="selectedAllocationId" class="form-select" required @change="onAllocationSelect">
                                    <option value="">Select Resident Student (Currently Allocated)</option>
                                    <option v-for="al in dropdowns.allocations" :key="al.id" :value="al.id">
                                        {{ al.student?.first_name }} {{ al.student?.last_name }} ({{ al.student?.admission_no }}) - Room {{ al.room?.room_no }} ({{ al.room?.building?.name }})
                                    </option>
                                </select>
                            </div>

                            <!-- Source Details Display -->
                            <div v-if="form.from_room_id" class="col-12">
                                <div class="alert alert-info py-2 px-3 small mb-0">
                                    <strong>Current Room:</strong> Room {{ currentAllocation?.room?.room_no }}
                                    <span v-if="currentAllocation?.bed">• Bed {{ currentAllocation?.bed?.bed_no }}</span>
                                    ({{ currentAllocation?.room?.building?.name }})
                                </div>
                            </div>

                            <!-- Destination Room & Bed -->
                            <div class="col-md-6">
                                <label class="form-label">Target Room <span class="text-danger">*</span></label>
                                <select v-model="form.to_room_id" class="form-select" required @change="onTargetRoomChange">
                                    <option value="">Select Target Room</option>
                                    <option v-for="r in filteredTargetRooms" :key="r.id" :value="r.id">
                                        Room {{ r.room_no }} ({{ r.building?.name }}) - {{ r.beds?.length || 0 }} Beds Available
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Target Bed</label>
                                <select v-model="form.to_bed_id" class="form-select">
                                    <option value="">Select Target Bed (Optional)</option>
                                    <option v-for="b in availableTargetBeds" :key="b.id" :value="b.id">{{ b.bed_no }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Transfer Date <span class="text-danger">*</span></label>
                                <input v-model="form.transfer_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reason for Transfer</label>
                                <input v-model="form.reason" type="text" class="form-control" placeholder="e.g. Student request, maintenance, upgrade">
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
                            Complete Transfer
                        </button>
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

const transfers = ref([]);
const dropdowns = ref({ allocations: [], rooms: [] });
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const selectedAllocationId = ref('');
const currentAllocation = ref(null);

const emptyForm = () => ({
    student_id: '', from_room_id: '', from_bed_id: '',
    to_room_id: '', to_bed_id: '', transfer_date: new Date().toISOString().slice(0, 10),
    reason: '', remarks: ''
});
const form = ref(emptyForm());

const filteredTargetRooms = computed(() => {
    if (!form.value.from_room_id) return dropdowns.value.rooms;
    return dropdowns.value.rooms.filter(r => r.id !== form.value.from_room_id);
});

const availableTargetBeds = computed(() => {
    if (!form.value.to_room_id) return [];
    const r = dropdowns.value.rooms.find(rm => rm.id === form.value.to_room_id);
    return r?.beds || [];
});

const onAllocationSelect = () => {
    const al = dropdowns.value.allocations.find(a => a.id === selectedAllocationId.value);
    currentAllocation.value = al;
    if (al) {
        form.value.student_id = al.student_id;
        form.value.from_room_id = al.hostel_room_id;
        form.value.from_bed_id = al.hostel_bed_id || '';
    } else {
        form.value.student_id = '';
        form.value.from_room_id = '';
        form.value.from_bed_id = '';
    }
};

const onTargetRoomChange = () => {
    form.value.to_bed_id = '';
};

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadDropdowns = async () => {
    try {
        const res = await axios.get('/api/hostel/transfers/dropdowns', getAuthConfig());
        if (res.data?.success) dropdowns.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadTransfers = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/transfers', { ...getAuthConfig(), params });
        if (res.data?.success) {
            transfers.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load transfer records.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', status: '', per_page: 20 };
    loadTransfers(1);
};

const openCreate = () => {
    form.value = emptyForm();
    selectedAllocationId.value = '';
    currentAllocation.value = null;
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveTransfer = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/hostel/transfers', form.value, getAuthConfig());
        success.value = 'Student transferred successfully.';
        closeModal();
        loadTransfers(1);
        loadDropdowns();
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to transfer student.';
    } finally {
        saving.value = false;
    }
};

const deleteTransfer = async (t) => {
    if (!confirm('Are you sure you want to delete this transfer log?')) return;
    try {
        await axios.delete(`/api/hostel/transfers/${t.id}`, getAuthConfig());
        success.value = 'Transfer log deleted.';
        loadTransfers(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete transfer record.';
    }
};

onMounted(() => {
    loadDropdowns();
    loadTransfers();
});
</script>
