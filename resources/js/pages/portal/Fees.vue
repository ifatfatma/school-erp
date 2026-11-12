<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Fee Dues & Payment History</h4>
      <small class="text-muted">Review fee breakdown, outstanding balance, and generated payment receipts</small>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Total Fee Assigned</small>
          <h3 class="fw-bold text-dark my-1">₹{{ Number(summary.total_due).toLocaleString('en-IN') }}</h3>
          <small class="text-muted">All assigned fee structures</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Paid Amount</small>
          <h3 class="fw-bold text-success my-1">₹{{ Number(summary.total_paid).toLocaleString('en-IN') }}</h3>
          <small class="text-muted">Cleared payments</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
          <small class="text-muted text-uppercase fw-semibold">Outstanding Balance</small>
          <h3 class="fw-bold text-danger my-1">₹{{ Number(summary.balance).toLocaleString('en-IN') }}</h3>
          <small class="text-muted">Pending balance</small>
        </div>
      </div>
    </div>

    <!-- Dues & Receipts Tabs -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
      <div class="card-header bg-white p-3 border-0">
        <ul class="nav nav-tabs card-header-tabs">
          <li class="nav-item">
            <button class="nav-link" :class="{ active: activeTab === 'dues' }" @click="activeTab = 'dues'">
              <i class="bi bi-clock-history me-1"></i> Fee Dues Breakdown
            </button>
          </li>
          <li class="nav-item">
            <button class="nav-link" :class="{ active: activeTab === 'receipts' }" @click="activeTab = 'receipts'">
              <i class="bi bi-receipt me-1"></i> Payment Receipts ({{ payments.length }})
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-0">
        <!-- Dues Tab -->
        <div v-if="activeTab === 'dues'" class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
              <tr>
                <th class="ps-4">Fee Head</th>
                <th>Period</th>
                <th>Due Date</th>
                <th>Net Amount</th>
                <th>Paid</th>
                <th>Balance</th>
                <th class="pe-4">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="dues.length === 0">
                <td colspan="7" class="text-center py-5 text-muted small">No fee dues on record.</td>
              </tr>
              <tr v-for="d in dues" :key="d.id">
                <td class="ps-4 fw-semibold">{{ d.fee_type?.name || 'Academic Fee' }}</td>
                <td class="small text-muted">{{ d.period || 'Annual' }}</td>
                <td class="small">{{ d.due_date }}</td>
                <td>₹{{ Number(d.net_amount).toLocaleString('en-IN') }}</td>
                <td class="text-success">₹{{ Number(d.paid_amount).toLocaleString('en-IN') }}</td>
                <td class="fw-bold text-danger">₹{{ Number(d.balance_amount).toLocaleString('en-IN') }}</td>
                <td class="pe-4">
                  <span class="badge rounded-pill text-capitalize" :class="d.status === 'paid' ? 'bg-success' : 'bg-warning text-dark'">
                    {{ d.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Receipts Tab -->
        <div v-if="activeTab === 'receipts'" class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
              <tr>
                <th class="ps-4">Receipt No.</th>
                <th>Payment Date</th>
                <th>Paid Amount</th>
                <th>Payment Method</th>
                <th>Transaction / Ref</th>
                <th class="pe-4 text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="payments.length === 0">
                <td colspan="6" class="text-center py-5 text-muted small">No payment transactions recorded yet.</td>
              </tr>
              <tr v-for="p in payments" :key="p.id">
                <td class="ps-4 fw-bold font-monospace text-primary">{{ p.receipt_no }}</td>
                <td>{{ p.payment_date }}</td>
                <td class="fw-bold text-success">₹{{ Number(p.total_amount).toLocaleString('en-IN') }}</td>
                <td><span class="badge bg-light text-dark text-uppercase">{{ p.payment_method }}</span></td>
                <td class="small text-muted">{{ p.transaction_no || '-' }}</td>
                <td class="pe-4 text-end">
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="printReceipt(p)">
                    <i class="bi bi-printer me-1"></i> Receipt
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const activeTab = ref('dues')
const dues = ref([])
const payments = ref([])
const summary = ref({ total_due: 0, total_paid: 0, balance: 0 })

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadFees() {
  try {
    const res = await axios.get('/api/portal/fees', getAuthConfig())
    if (res.data.success) {
      dues.value = res.data.data.dues || []
      payments.value = res.data.data.payments || []
      summary.value = res.data.data.summary || summary.value
    }
  } catch (err) {
    console.error('Failed to load portal fees', err)
  }
}

function printReceipt(payment) {
  Swal.fire({
    title: 'Payment Receipt',
    html: `
      <div class="text-start small">
        <p><strong>Receipt:</strong> ${payment.receipt_no}</p>
        <p><strong>Date:</strong> ${payment.payment_date}</p>
        <p><strong>Amount Paid:</strong> ₹${payment.total_amount}</p>
        <p><strong>Mode:</strong> ${payment.payment_method}</p>
      </div>
    `,
    showCloseButton: true,
    confirmButtonText: 'Print / OK',
  })
}

onMounted(() => {
  loadFees()
})
</script>
