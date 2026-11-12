<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Fees</h4>
            <small class="text-muted">Generate fee invoices and record fee collections for hostel residents</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-receipt me-1"></i> Generate Invoice
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

    <!-- Stats Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-light">
                <div class="text-muted small">Total Billed</div>
                <div class="h4 fw-bold text-dark mt-1">₹{{ Number(stats.total_amount || 0).toLocaleString() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-success bg-opacity-10">
                <div class="text-success small fw-semibold">Total Collected</div>
                <div class="h4 fw-bold text-success mt-1">₹{{ Number(stats.total_paid || 0).toLocaleString() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-warning bg-opacity-10">
                <div class="text-warning small fw-semibold">Total Outstanding</div>
                <div class="h4 fw-bold text-warning mt-1">₹{{ Number(stats.total_due || 0).toLocaleString() }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search invoice no, student name..." @keyup.enter="loadFees(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <input v-model="filters.month" type="month" class="form-control" @change="loadFees(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadFees(1)">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                        <option value="unpaid">Unpaid</option>
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
                            <th>Invoice No</th>
                            <th>Student</th>
                            <th>Room</th>
                            <th>Fee Type / Month</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th width="140" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading fee records...</div>
                            </td>
                        </tr>
                        <tr v-else-if="fees.length === 0">
                            <td colspan="10" class="text-center py-5 text-muted">No fee invoices found.</td>
                        </tr>
                        <tr v-for="(f, idx) in fees" :key="f.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td><code>{{ f.invoice_no }}</code></td>
                            <td>
                                <strong>{{ f.student?.first_name }} {{ f.student?.last_name }}</strong>
                                <small class="text-muted d-block">{{ f.student?.admission_no }}</small>
                            </td>
                            <td>Room {{ f.room?.room_no || '-' }}</td>
                            <td>
                                <div>{{ f.fee_type }}</div>
                                <small v-if="f.month" class="text-muted">{{ f.month }}</small>
                            </td>
                            <td><strong>₹{{ Number(f.amount || 0).toLocaleString() }}</strong></td>
                            <td class="text-success">₹{{ Number(f.paid_amount || 0).toLocaleString() }}</td>
                            <td>{{ f.due_date || '-' }}</td>
                            <td>
                                <span :class="['badge text-uppercase', f.status === 'paid' ? 'bg-success' : (f.status === 'partial' ? 'bg-warning text-dark' : 'bg-danger')]">
                                    {{ f.status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button v-if="f.status !== 'paid'" class="btn btn-sm btn-outline-success me-1" title="Collect Payment" @click="openCollect(f)">
                                    <i class="bi bi-cash"></i> Pay
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteFee(f)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ fees.length }} of {{ pagination.total }} records</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadFees(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadFees(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Generate Invoice) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveFee">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Hostel Fee Invoice</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Resident Student <span class="text-danger">*</span></label>
                                <select v-model="form.student_id" class="form-select" required @change="onStudentSelect">
                                    <option value="">Select Resident Student</option>
                                    <option v-for="st in residentStudents" :key="st.student_id" :value="st.student_id">
                                        {{ st.student?.first_name }} {{ st.student?.last_name }} ({{ st.student?.admission_no }}) - Room {{ st.room?.room_no }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                                <select v-model="form.fee_type" class="form-select" required>
                                    <option value="Monthly Fee">Monthly Room Fee</option>
                                    <option value="Admission Fee">Hostel Admission Fee</option>
                                    <option value="Caution Deposit">Caution Deposit</option>
                                    <option value="Mess Fee">Mess / Food Fee</option>
                                    <option value="Other">Other Fee</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Month</label>
                                <input v-model="form.month" type="month" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input v-model.number="form.amount" type="number" step="0.01" min="0" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input v-model="form.due_date" type="date" class="form-control">
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
                            Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collect Payment Modal -->
    <div v-if="showCollectModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="submitCollect">
                    <div class="modal-header">
                        <h5 class="modal-title">Collect Fee Payment</h5>
                        <button type="button" class="btn-close" @click="showCollectModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border py-2 px-3 mb-3">
                            <div><strong>Student:</strong> {{ collectTarget?.student?.first_name }} {{ collectTarget?.student?.last_name }}</div>
                            <div><strong>Invoice:</strong> {{ collectTarget?.invoice_no }} ({{ collectTarget?.fee_type }})</div>
                            <div><strong>Total Amount:</strong> ₹{{ Number(collectTarget?.amount || 0).toLocaleString() }}</div>
                            <div><strong>Already Paid:</strong> ₹{{ Number(collectTarget?.paid_amount || 0).toLocaleString() }}</div>
                            <div class="text-danger"><strong>Remaining Due:</strong> ₹{{ (Number(collectTarget?.amount || 0) - Number(collectTarget?.paid_amount || 0)).toLocaleString() }}</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Amount (₹) <span class="text-danger">*</span></label>
                                <input v-model.number="collectForm.paid_amount" type="number" step="0.01" min="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input v-model="collectForm.paid_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                <select v-model="collectForm.payment_method" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online / UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks / Transaction Ref</label>
                                <input v-model="collectForm.remarks" type="text" class="form-control" placeholder="e.g. UPI Ref 12345678">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showCollectModal = false">Cancel</button>
                        <button type="submit" class="btn btn-success" :disabled="saving">Record Payment</button>
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

const fees = ref([]);
const stats = ref({});
const residentStudents = ref([]);
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showCollectModal = ref(false);
const collectTarget = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', month: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({
    student_id: '', hostel_room_id: '', fee_type: 'Monthly Fee',
    month: new Date().toISOString().slice(0, 7), amount: 0, due_date: '', remarks: ''
});
const form = ref(emptyForm());
const collectForm = ref({ paid_amount: 0, paid_date: new Date().toISOString().slice(0, 10), payment_method: 'cash', remarks: '' });

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadResidentStudents = async () => {
    try {
        const res = await axios.get('/api/hostel/fees/students', getAuthConfig());
        if (res.data?.success) residentStudents.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const onStudentSelect = () => {
    const st = residentStudents.value.find(s => s.student_id === form.value.student_id);
    if (st) {
        form.value.hostel_room_id = st.hostel_room_id || '';
        if (st.room?.monthly_fee) form.value.amount = st.room.monthly_fee;
    }
};

const loadFees = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/fees', { ...getAuthConfig(), params });
        if (res.data?.success) {
            fees.value = res.data.data;
            stats.value = res.data.stats || {};
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load fee records.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', month: '', status: '', per_page: 20 };
    loadFees(1);
};

const openCreate = () => {
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveFee = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/hostel/fees', form.value, getAuthConfig());
        success.value = 'Fee invoice generated.';
        closeModal();
        loadFees(1);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to generate fee invoice.';
    } finally {
        saving.value = false;
    }
};

const openCollect = (f) => {
    collectTarget.value = f;
    const due = Math.max(0, Number(f.amount || 0) - Number(f.paid_amount || 0));
    collectForm.value = {
        paid_amount: due,
        paid_date: new Date().toISOString().slice(0, 10),
        payment_method: 'cash',
        remarks: ''
    };
    showCollectModal.value = true;
};

const submitCollect = async () => {
    saving.value = true;
    try {
        await axios.post(`/api/hostel/fees/${collectTarget.value.id}/collect`, collectForm.value, getAuthConfig());
        success.value = 'Payment collected successfully.';
        showCollectModal.value = false;
        loadFees(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to collect payment.';
    } finally {
        saving.value = false;
    }
};

const deleteFee = async (f) => {
    if (!confirm('Are you sure you want to delete this fee invoice?')) return;
    try {
        await axios.delete(`/api/hostel/fees/${f.id}`, getAuthConfig());
        success.value = 'Fee invoice deleted.';
        loadFees(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete fee invoice.';
    }
};

onMounted(() => {
    loadResidentStudents();
    loadFees();
});
</script>
