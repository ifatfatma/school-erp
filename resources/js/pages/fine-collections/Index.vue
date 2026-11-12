<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Library Fine Collections</h4>
            <small class="text-muted">Record fee payments, issue receipts and manage library fine settlements</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-receipt me-1"></i> Collect Fine
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

    <!-- Stats Row -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm bg-light">
                <small class="text-muted">Total Fines Collected</small>
                <div class="h4 fw-bold text-dark mt-1">₹{{ Number(stats.total_collected || 0).toLocaleString() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
                <small class="text-success fw-semibold">Collected Today</small>
                <div class="h4 fw-bold text-success mt-1">₹{{ Number(stats.today_collected || 0).toLocaleString() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm bg-info bg-opacity-10">
                <small class="text-info fw-semibold">Collected This Month</small>
                <div class="h4 fw-bold text-info mt-1">₹{{ Number(stats.this_month || 0).toLocaleString() }}</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search receipt no, member, student..." @keyup.enter="loadCollections(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment Mode</label>
                    <select v-model="filters.payment_mode" class="form-select" @change="loadCollections(1)">
                        <option value="">All Payment Modes</option>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI / Online</option>
                        <option value="Card">Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input v-model="filters.date_from" type="date" class="form-control" @change="loadCollections(1)">
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
                            <th>Receipt No</th>
                            <th>Fine Ref</th>
                            <th>Member / Student</th>
                            <th>Book</th>
                            <th>Amount Paid</th>
                            <th>Mode</th>
                            <th>Date</th>
                            <th>Collected By</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading collection records...</div>
                            </td>
                        </tr>
                        <tr v-else-if="collections.length === 0">
                            <td colspan="10" class="text-center py-5 text-muted">No fine collections found.</td>
                        </tr>
                        <tr v-for="(c, idx) in collections" :key="c.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td><code>{{ c.receipt_no }}</code></td>
                            <td>
                                <div><strong>{{ c.fine?.fine_no }}</strong></div>
                                <small class="text-muted">{{ c.fine?.fine_type }}</small>
                            </td>
                            <td>
                                <strong>
                                    {{ c.member?.student ? (c.member.student.first_name + ' ' + c.member.student.last_name) : (c.member?.staff ? (c.member.staff.first_name + ' ' + c.member.staff.last_name) : 'Member') }}
                                </strong>
                                <small class="text-muted d-block">{{ c.member?.member_no }}</small>
                            </td>
                            <td>
                                <span v-if="c.fine?.book" class="text-truncate d-inline-block" style="max-width: 180px;">{{ c.fine.book.title }}</span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td><strong class="text-success">₹{{ Number(c.amount || 0).toLocaleString() }}</strong></td>
                            <td><span class="badge bg-light text-dark border">{{ c.payment_mode }}</span></td>
                            <td>{{ c.collection_date }}</td>
                            <td><small class="text-muted">{{ c.collector?.name || 'Staff' }}</small></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info me-1" title="View Receipt" @click="viewReceipt(c)">
                                    <i class="bi bi-printer"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteCollection(c)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ collections.length }} of {{ pagination.total }} records</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadCollections(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadCollections(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form (Collect Fine) -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveCollection">
                    <div class="modal-header">
                        <h5 class="modal-title">Collect Library Fine</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Select Pending Fine <span class="text-danger">*</span></label>
                                <select v-model="form.library_fine_id" class="form-select" required @change="onFineSelect">
                                    <option value="">Select Fine</option>
                                    <option v-for="f in pendingFines" :key="f.id" :value="f.id">
                                        {{ f.fine_no }} - {{ f.member_name }} ({{ f.member_no }}) - Due: ₹{{ f.remaining_amount }} [{{ f.fine_type }}]
                                    </option>
                                </select>
                            </div>

                            <div v-if="selectedFine" class="col-12">
                                <div class="alert alert-light border py-2 px-3 small mb-0">
                                    <div><strong>Book:</strong> {{ selectedFine.book_title || 'N/A' }}</div>
                                    <div><strong>Total Fine:</strong> ₹{{ selectedFine.payable_amount }} | <strong>Remaining Due:</strong> ₹{{ selectedFine.remaining_amount }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Collection Amount (₹) <span class="text-danger">*</span></label>
                                <input v-model.number="form.amount" type="number" step="0.01" min="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input v-model="form.collection_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select v-model="form.payment_mode" class="form-select" required>
                                    <option value="Cash">Cash</option>
                                    <option value="UPI">UPI / QR Code</option>
                                    <option value="Card">Debit / Credit Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Transaction Ref / Cheque No</label>
                                <input v-model="form.transaction_no" type="text" class="form-control" placeholder="e.g. UTR / Ref">
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
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div v-if="showReceiptModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fine Payment Receipt</h5>
                    <button type="button" class="btn-close" @click="showReceiptModal = false"></button>
                </div>
                <div class="modal-body p-4" id="receiptPrintArea">
                    <div class="text-center mb-4 border-bottom pb-3">
                        <h5 class="fw-bold mb-1">Library Fine Receipt</h5>
                        <p class="text-muted small mb-0">Receipt No: <strong>{{ activeReceipt?.receipt_no }}</strong></p>
                        <p class="text-muted small mb-0">Date: {{ activeReceipt?.collection_date }}</p>
                    </div>
                    <div class="row g-2 mb-3 small">
                        <div class="col-6"><strong>Member:</strong> {{ activeReceipt?.member?.student ? (activeReceipt.member.student.first_name + ' ' + activeReceipt.member.student.last_name) : 'Member' }}</div>
                        <div class="col-6"><strong>Member No:</strong> {{ activeReceipt?.member?.member_no }}</div>
                        <div class="col-6"><strong>Fine No:</strong> {{ activeReceipt?.fine?.fine_no }}</div>
                        <div class="col-6"><strong>Fine Type:</strong> {{ activeReceipt?.fine?.fine_type }}</div>
                        <div class="col-6"><strong>Payment Mode:</strong> {{ activeReceipt?.payment_mode }}</div>
                        <div class="col-6"><strong>Txn Ref:</strong> {{ activeReceipt?.transaction_no || '-' }}</div>
                    </div>
                    <div class="p-3 bg-light rounded text-center my-3">
                        <div class="text-muted small">Amount Received</div>
                        <div class="h3 fw-bold text-success mt-1">₹{{ Number(activeReceipt?.amount || 0).toLocaleString() }}</div>
                    </div>
                    <div class="text-muted small text-center mt-3">Collected by: {{ activeReceipt?.collector?.name || 'Authorized Staff' }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showReceiptModal = false">Close</button>
                    <button type="button" class="btn btn-primary" @click="printReceipt"><i class="bi bi-printer me-1"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const collections = ref([]);
const pendingFines = ref([]);
const stats = ref({});
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const showReceiptModal = ref(false);
const activeReceipt = ref(null);
const selectedFine = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', payment_mode: '', date_from: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({
    library_fine_id: '', amount: 0,
    collection_date: new Date().toISOString().slice(0, 10),
    payment_mode: 'Cash', transaction_no: '', remarks: ''
});
const form = ref(emptyForm());

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadPendingFines = async () => {
    try {
        const res = await axios.get('/api/fine-collections/pending-fines', getAuthConfig());
        if (res.data?.success) pendingFines.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const onFineSelect = () => {
    const f = pendingFines.value.find(item => item.id === form.value.library_fine_id);
    selectedFine.value = f;
    if (f) form.value.amount = f.remaining_amount;
};

const loadCollections = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/fine-collections', { ...getAuthConfig(), params });
        if (res.data?.success) {
            collections.value = res.data.data;
            stats.value = res.data.stats || {};
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load fine collections.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', payment_mode: '', date_from: '', per_page: 20 };
    loadCollections(1);
};

const openCreate = () => {
    form.value = emptyForm();
    selectedFine.value = null;
    formError.value = '';
    loadPendingFines();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveCollection = async () => {
    saving.value = true;
    formError.value = '';
    try {
        await axios.post('/api/fine-collections', form.value, getAuthConfig());
        success.value = 'Fine payment collected successfully.';
        closeModal();
        loadCollections(1);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to collect payment.';
    } finally {
        saving.value = false;
    }
};

const viewReceipt = (c) => {
    activeReceipt.value = c;
    showReceiptModal.value = true;
};

const printReceipt = () => {
    window.print();
};

const deleteCollection = async (c) => {
    if (!confirm(`Are you sure you want to void receipt "${c.receipt_no}"?`)) return;
    try {
        await axios.delete(`/api/fine-collections/${c.id}`, getAuthConfig());
        success.value = 'Collection record voided.';
        loadCollections(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete collection.';
    }
};

onMounted(() => {
    loadCollections();
});
</script>
