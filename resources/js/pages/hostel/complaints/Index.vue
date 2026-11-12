<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Complaints & Maintenance</h4>
            <small class="text-muted">Track and resolve student grievances, room repairs, and facility maintenance</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-megaphone me-1"></i> Lodge Complaint
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
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search complaint title, desc or student..." @keyup.enter="loadComplaints(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadComplaints(1)">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select v-model="filters.priority" class="form-select" @change="loadComplaints(1)">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
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
                            <th>Complaint Details</th>
                            <th>Category</th>
                            <th>Resident Student</th>
                            <th>Room / Facility</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date Lodged</th>
                            <th width="130" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading complaints...</div>
                            </td>
                        </tr>
                        <tr v-else-if="complaints.length === 0">
                            <td colspan="9" class="text-center py-5 text-muted">No complaints found.</td>
                        </tr>
                        <tr v-for="(c, idx) in complaints" :key="c.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ c.title }}</strong>
                                <p class="text-muted small mb-0 text-truncate" style="max-width: 250px;">{{ c.description }}</p>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ c.category }}</span></td>
                            <td>
                                <span v-if="c.student">{{ c.student.first_name }} {{ c.student.last_name }}</span>
                                <span v-else class="text-muted">Staff / General</span>
                            </td>
                            <td>
                                <span v-if="c.room">Room {{ c.room.room_no }} ({{ c.room.building?.name }})</span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <span :class="['badge text-uppercase', c.priority === 'urgent' ? 'bg-danger' : (c.priority === 'high' ? 'bg-warning text-dark' : (c.priority === 'medium' ? 'bg-info text-dark' : 'bg-secondary'))]">
                                    {{ c.priority }}
                                </span>
                            </td>
                            <td>
                                <span :class="['badge text-capitalize', c.status === 'resolved' ? 'bg-success' : (c.status === 'pending' ? 'bg-danger' : (c.status === 'in_progress' ? 'bg-primary' : 'bg-secondary'))]">
                                    {{ c.status.replace('_', ' ') }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ c.created_at?.slice(0, 10) }}</small></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" title="Update Status / Notes" @click="openStatusModal(c)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteComplaint(c)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ complaints.length }} of {{ pagination.total }} complaints</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadComplaints(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadComplaints(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Lodge Complaint) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveComplaint">
                    <div class="modal-header">
                        <h5 class="modal-title">Lodge Hostel Complaint</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Subject / Title <span class="text-danger">*</span></label>
                                <input v-model="form.title" type="text" class="form-control" required placeholder="e.g. Fan not working in Room 102">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select v-model="form.category" class="form-select" required>
                                    <option value="Electrical">Electrical</option>
                                    <option value="Plumbing">Plumbing</option>
                                    <option value="Room / Bed">Room / Bed Furniture</option>
                                    <option value="Cleanliness">Cleanliness & Hygiene</option>
                                    <option value="Food / Mess">Food & Mess</option>
                                    <option value="Discipline">Discipline / Noise</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Priority <span class="text-danger">*</span></label>
                                <select v-model="form.priority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student (Optional)</label>
                                <select v-model="form.student_id" class="form-select">
                                    <option value="">Select Student</option>
                                    <option v-for="st in dropdowns.students" :key="st.id" :value="st.id">{{ st.first_name }} {{ st.last_name }} ({{ st.admission_no }})</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room (Optional)</label>
                                <select v-model="form.hostel_room_id" class="form-select">
                                    <option value="">Select Room</option>
                                    <option v-for="r in dropdowns.rooms" :key="r.id" :value="r.id">Room {{ r.room_no }} ({{ r.building?.name }})</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea v-model="form.description" class="form-control" rows="3" required placeholder="Detailed description of the issue"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            Submit Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div v-if="showStatusModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="submitStatusUpdate">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Complaint Status</h5>
                        <button type="button" class="btn-close" @click="showStatusModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <h6>{{ selectedComplaint?.title }}</h6>
                        <p class="text-muted small">{{ selectedComplaint?.description }}</p>

                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select v-model="statusForm.status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Resolution Notes / Action Taken</label>
                            <textarea v-model="statusForm.resolution_notes" class="form-control" rows="3" placeholder="Describe the action taken or reason for closing"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showStatusModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">Update</button>
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

const complaints = ref([]);
const dropdowns = ref({ students: [], rooms: [] });
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showStatusModal = ref(false);
const selectedComplaint = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', status: '', priority: '', category: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({
    title: '', category: 'Electrical', priority: 'medium', student_id: '',
    hostel_room_id: '', description: ''
});
const form = ref(emptyForm());
const statusForm = ref({ status: 'in_progress', resolution_notes: '' });

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadDropdowns = async () => {
    try {
        const res = await axios.get('/api/hostel/complaints/dropdowns', getAuthConfig());
        if (res.data?.success) dropdowns.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadComplaints = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/complaints', { ...getAuthConfig(), params });
        if (res.data?.success) {
            complaints.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load complaints.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', status: '', priority: '', category: '', per_page: 20 };
    loadComplaints(1);
};

const openCreate = () => {
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveComplaint = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/hostel/complaints', form.value, getAuthConfig());
        success.value = 'Complaint lodged successfully.';
        closeModal();
        loadComplaints(1);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to lodge complaint.';
    } finally {
        saving.value = false;
    }
};

const openStatusModal = (c) => {
    selectedComplaint.value = c;
    statusForm.value = {
        status: c.status,
        resolution_notes: c.resolution_notes || ''
    };
    showStatusModal.value = true;
};

const submitStatusUpdate = async () => {
    saving.value = true;
    try {
        await axios.put(`/api/hostel/complaints/${selectedComplaint.value.id}`, statusForm.value, getAuthConfig());
        success.value = 'Complaint status updated.';
        showStatusModal.value = false;
        loadComplaints(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to update complaint.';
    } finally {
        saving.value = false;
    }
};

const deleteComplaint = async (c) => {
    if (!confirm('Are you sure you want to delete this complaint?')) return;
    try {
        await axios.delete(`/api/hostel/complaints/${c.id}`, getAuthConfig());
        success.value = 'Complaint record deleted.';
        loadComplaints(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete complaint.';
    }
};

onMounted(() => {
    loadDropdowns();
    loadComplaints();
});
</script>
