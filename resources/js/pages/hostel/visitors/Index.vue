<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Visitors</h4>
            <small class="text-muted">Track visitor entry, checkout, relation to student, and ID verification</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-person-plus me-1"></i> Log New Visitor
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
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search visitor name, phone or student..." @keyup.enter="loadVisitors(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input v-model="filters.date" type="date" class="form-control" @change="loadVisitors(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadVisitors(1)">
                        <option value="">All Status</option>
                        <option value="inside">Currently Inside</option>
                        <option value="checked_out">Checked Out</option>
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
                            <th>Visitor</th>
                            <th>Relationship</th>
                            <th>Visiting Student</th>
                            <th>Date</th>
                            <th>In / Out Time</th>
                            <th>Purpose / ID</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="9" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading visitor records...</div>
                            </td>
                        </tr>
                        <tr v-else-if="visitors.length === 0">
                            <td colspan="9" class="text-center py-5 text-muted">No visitor records found.</td>
                        </tr>
                        <tr v-for="(v, idx) in visitors" :key="v.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ v.visitor_name }}</strong>
                                <small v-if="v.visitor_phone" class="text-muted d-block">{{ v.visitor_phone }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ v.relation }}</span></td>
                            <td>
                                <strong>{{ v.student?.first_name }} {{ v.student?.last_name }}</strong>
                                <small class="text-muted d-block">{{ v.student?.class?.name }}</small>
                            </td>
                            <td>{{ v.visit_date }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success me-1">In: {{ v.in_time || '-' }}</span>
                                <span v-if="v.out_time" class="badge bg-secondary bg-opacity-10 text-secondary">Out: {{ v.out_time }}</span>
                            </td>
                            <td>
                                <div class="small">{{ v.purpose || '-' }}</div>
                                <small v-if="v.id_proof_number" class="text-muted">{{ v.id_proof_type }}: {{ v.id_proof_number }}</small>
                            </td>
                            <td>
                                <span :class="['badge rounded-pill', v.status === 'inside' ? 'bg-success' : 'bg-secondary']">
                                    {{ v.status === 'inside' ? 'Inside' : 'Checked Out' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button v-if="v.status === 'inside'" class="btn btn-sm btn-outline-warning me-1" title="Check Out" @click="checkoutVisitor(v)">
                                    <i class="bi bi-box-arrow-right"></i> Out
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteVisitor(v)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ visitors.length }} of {{ pagination.total }} records</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadVisitors(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadVisitors(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Log Visitor) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveVisitor">
                    <div class="modal-header">
                        <h5 class="modal-title">Log New Visitor</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Visiting Resident Student <span class="text-danger">*</span></label>
                                <select v-model="form.student_id" class="form-select" required>
                                    <option value="">Select Student</option>
                                    <option v-for="st in residentStudents" :key="st.id" :value="st.id">
                                        {{ st.first_name }} {{ st.last_name }} ({{ st.admission_no }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Visitor Full Name <span class="text-danger">*</span></label>
                                <input v-model="form.visitor_name" type="text" class="form-control" required placeholder="e.g. Ramesh Kumar">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Relationship <span class="text-danger">*</span></label>
                                <input v-model="form.relation" type="text" class="form-control" required placeholder="e.g. Father, Mother, Brother, Guardian">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input v-model="form.visitor_phone" type="text" class="form-control" placeholder="Mobile number">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                                <input v-model="form.visit_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">In-Time</label>
                                <input v-model="form.in_time" type="text" class="form-control" placeholder="e.g. 04:30 PM">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Out-Time (Leave empty if inside)</label>
                                <input v-model="form.out_time" type="text" class="form-control" placeholder="e.g. 05:45 PM">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ID Proof Type</label>
                                <select v-model="form.id_proof_type" class="form-select">
                                    <option value="">None</option>
                                    <option value="Aadhaar Card">Aadhaar Card</option>
                                    <option value="Voter ID">Voter ID</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Passport">Passport</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ID Proof Number</label>
                                <input v-model="form.id_proof_number" type="text" class="form-control" placeholder="ID Number">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Purpose of Visit</label>
                                <input v-model="form.purpose" type="text" class="form-control" placeholder="e.g. Meeting student, bringing essentials, fee payment">
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
                            Save Entry
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

const visitors = ref([]);
const residentStudents = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', date: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const defaultTime = () => {
    return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const emptyForm = () => ({
    student_id: '', visitor_name: '', relation: '', visitor_phone: '',
    visit_date: new Date().toISOString().slice(0, 10), in_time: defaultTime(),
    out_time: '', id_proof_type: 'Aadhaar Card', id_proof_number: '', purpose: '', remarks: ''
});
const form = ref(emptyForm());

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadResidentStudents = async () => {
    try {
        const res = await axios.get('/api/hostel/visitors/students', getAuthConfig());
        if (res.data?.success) residentStudents.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadVisitors = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/visitors', { ...getAuthConfig(), params });
        if (res.data?.success) {
            visitors.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load visitors.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', date: '', status: '', per_page: 20 };
    loadVisitors(1);
};

const openCreate = () => {
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveVisitor = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/hostel/visitors', form.value, getAuthConfig());
        success.value = 'Visitor entry logged successfully.';
        closeModal();
        loadVisitors(1);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to save visitor.';
    } finally {
        saving.value = false;
    }
};

const checkoutVisitor = async (v) => {
    const time = prompt('Confirm checkout time:', defaultTime());
    if (time === null) return;
    try {
        await axios.post(`/api/hostel/visitors/${v.id}/checkout`, { out_time: time }, getAuthConfig());
        success.value = 'Visitor checked out successfully.';
        loadVisitors(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to checkout visitor.';
    }
};

const deleteVisitor = async (v) => {
    if (!confirm('Are you sure you want to delete this visitor log?')) return;
    try {
        await axios.delete(`/api/hostel/visitors/${v.id}`, getAuthConfig());
        success.value = 'Visitor log deleted.';
        loadVisitors(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete record.';
    }
};

onMounted(() => {
    loadResidentStudents();
    loadVisitors();
});
</script>
