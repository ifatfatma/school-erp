<template>
  <div>
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-1">Fee Dues & Online Payment</h4>
        <small class="text-muted">Review fee breakdown, outstanding balance, make secure online payments, and download official receipts</small>
      </div>
      <div v-if="summary.balance > 0">
        <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2" @click="openPaymentModal(null)">
          <i class="bi bi-credit-card-2-front-fill"></i>
          <span>Pay Outstanding Fees (₹{{ Number(summary.balance).toLocaleString('en-IN') }})</span>
        </button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Fee Assigned</small>
          <h3 class="fw-bold text-dark my-2">₹{{ Number(summary.total_due).toLocaleString('en-IN') }}</h3>
          <small class="text-muted">Total allocated academic dues</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Total Paid</small>
          <h3 class="fw-bold text-success my-2">₹{{ Number(summary.total_paid).toLocaleString('en-IN') }}</h3>
          <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Cleared payments</small>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center h-100">
          <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Outstanding Balance</small>
          <h3 class="fw-bold my-2" :class="summary.balance > 0 ? 'text-danger' : 'text-success'">
            ₹{{ Number(summary.balance).toLocaleString('en-IN') }}
          </h3>
          <small :class="summary.balance > 0 ? 'text-danger fw-semibold' : 'text-muted'">
            {{ summary.balance > 0 ? 'Payment Required' : 'All Clear / No Pending Dues' }}
          </small>
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
              <span v-if="pendingDuesCount > 0" class="badge bg-danger rounded-pill ms-1">{{ pendingDuesCount }}</span>
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
                <th class="ps-4" style="width: 40px;">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    :checked="isAllSelected"
                    :disabled="selectableDues.length === 0"
                    @change="toggleSelectAll"
                  />
                </th>
                <th>Fee Head</th>
                <th>Period</th>
                <th>Due Date</th>
                <th>Net Amount</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
                <th class="pe-4 text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="dues.length === 0">
                <td colspan="9" class="text-center py-5 text-muted small">
                  <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
                  No fee dues on record for this student.
                </td>
              </tr>
              <tr v-for="d in dues" :key="d.id">
                <td class="ps-4">
                  <input
                    v-if="d.balance_amount > 0"
                    type="checkbox"
                    class="form-check-input"
                    :value="d.id"
                    v-model="selectedDueIds"
                  />
                  <i v-else class="bi bi-check-circle-fill text-success"></i>
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ d.fee_type?.name || 'Tuition Fee' }}</div>
                  <small class="text-muted" v-if="d.fee_type?.description">{{ d.fee_type.description }}</small>
                </td>
                <td class="small text-muted">{{ d.period || 'Annual / Term' }}</td>
                <td class="small">{{ d.due_date || 'N/A' }}</td>
                <td class="fw-semibold">₹{{ Number(d.net_amount).toLocaleString('en-IN') }}</td>
                <td class="text-success">₹{{ Number(d.paid_amount).toLocaleString('en-IN') }}</td>
                <td class="fw-bold text-danger">₹{{ Number(d.balance_amount).toLocaleString('en-IN') }}</td>
                <td>
                  <span class="badge rounded-pill text-capitalize" :class="d.status === 'Paid' ? 'bg-success' : (d.status === 'Partial' ? 'bg-info text-dark' : 'bg-warning text-dark')">
                    {{ d.status }}
                  </span>
                </td>
                <td class="pe-4 text-end">
                  <button
                    v-if="d.balance_amount > 0"
                    class="btn btn-sm btn-outline-primary rounded-pill px-3"
                    @click="openPaymentModal(d)"
                  >
                    <i class="bi bi-wallet2 me-1"></i> Pay ₹{{ Number(d.balance_amount).toLocaleString('en-IN') }}
                  </button>
                  <span v-else class="text-success small fw-semibold">
                    <i class="bi bi-check-all me-1"></i>Cleared
                  </span>
                </td>
              </tr>
            </tbody>
            <tfoot v-if="selectedDueIds.length > 0" class="table-light">
              <tr>
                <td colspan="6" class="ps-4 fw-bold">
                  Selected {{ selectedDueIds.length }} item(s) to pay:
                </td>
                <td class="fw-bold text-danger fs-6">
                  ₹{{ Number(selectedDuesTotal).toLocaleString('en-IN') }}
                </td>
                <td colspan="2" class="pe-4 text-end">
                  <button class="btn btn-sm btn-success rounded-pill px-4 fw-semibold" @click="openPaymentModal(null)">
                    <i class="bi bi-lock-fill me-1"></i> Pay Selected (₹{{ Number(selectedDuesTotal).toLocaleString('en-IN') }})
                  </button>
                </td>
              </tr>
            </tfoot>
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
                <th>Status</th>
                <th class="pe-4 text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="payments.length === 0">
                <td colspan="7" class="text-center py-5 text-muted small">
                  <i class="bi bi-receipt text-muted fs-1 d-block mb-2"></i>
                  No payment transactions recorded yet.
                </td>
              </tr>
              <tr v-for="p in payments" :key="p.id">
                <td class="ps-4 fw-bold font-monospace text-primary">{{ p.receipt_no }}</td>
                <td>{{ p.payment_date }}</td>
                <td class="fw-bold text-success">₹{{ Number(p.total_amount).toLocaleString('en-IN') }}</td>
                <td>
                  <span class="badge bg-light text-dark border text-uppercase px-2">
                    <i class="bi bi-credit-card me-1"></i>{{ p.payment_method }}
                  </span>
                </td>
                <td class="small font-monospace text-muted">{{ p.transaction_no || '-' }}</td>
                <td>
                  <span class="badge bg-success-subtle text-success rounded-pill px-2">
                    {{ p.status || 'Success' }}
                  </span>
                </td>
                <td class="pe-4 text-end">
                  <button class="btn btn-sm btn-outline-primary rounded-pill px-3" @click="viewReceipt(p.id)">
                    <i class="bi bi-printer me-1"></i> View Receipt
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- ONLINE FEE PAYMENT MODAL                   -->
    <!-- ========================================== -->
    <div
      v-if="showPaymentModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.6); z-index: 1050;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-primary text-white p-3">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-shield-check fs-4"></i>
              <div>
                <h5 class="modal-title fw-bold mb-0">Secure Fee Payment Gateway</h5>
                <small class="text-white-50" style="font-size: 0.75rem;">256-Bit SSL Encrypted Online Payment</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="showPaymentModal = false" :disabled="isProcessingPayment"></button>
          </div>

          <div class="modal-body p-4">
            <!-- Payment Summary Bar -->
            <div class="bg-light rounded-3 p-3 mb-3 border d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted d-block">Paying for:</small>
                <strong>{{ paymentData.duesCount }} Fee Structure(s)</strong>
              </div>
              <div class="text-end">
                <small class="text-muted d-block">Total Payable Amount:</small>
                <h3 class="fw-bold text-primary mb-0">₹{{ Number(paymentData.amount).toLocaleString('en-IN') }}</h3>
              </div>
            </div>

            <!-- Payment Method Tabs -->
            <ul class="nav nav-pills nav-fill mb-3 gap-2" id="paymentMethodTabs">
              <li class="nav-item">
                <button
                  class="nav-link py-2 rounded-3 border"
                  :class="{ active: paymentMethod === 'UPI', 'bg-primary text-white': paymentMethod === 'UPI' }"
                  @click="paymentMethod = 'UPI'"
                  :disabled="isProcessingPayment"
                >
                  <i class="bi bi-qr-code me-1"></i> UPI & Dynamic QR
                </button>
              </li>
              <li class="nav-item">
                <button
                  class="nav-link py-2 rounded-3 border"
                  :class="{ active: paymentMethod === 'Card', 'bg-primary text-white': paymentMethod === 'Card' }"
                  @click="paymentMethod = 'Card'"
                  :disabled="isProcessingPayment"
                >
                  <i class="bi bi-credit-card me-1"></i> Credit / Debit Card
                </button>
              </li>
              <li class="nav-item">
                <button
                  class="nav-link py-2 rounded-3 border"
                  :class="{ active: paymentMethod === 'Bank Transfer', 'bg-primary text-white': paymentMethod === 'Bank Transfer' }"
                  @click="paymentMethod = 'Bank Transfer'"
                  :disabled="isProcessingPayment"
                >
                  <i class="bi bi-bank me-1"></i> Net Banking
                </button>
              </li>
            </ul>

            <!-- Method 1: UPI & Dynamic QR -->
            <div v-if="paymentMethod === 'UPI'" class="p-3 border rounded-3 bg-white">
              <div class="row align-items-center">
                <div class="col-md-5 text-center border-end">
                  <div class="p-2 border rounded-3 d-inline-block bg-white shadow-sm mb-2">
                    <img
                      :src="upiQrUrl"
                      alt="UPI QR Code"
                      style="width: 170px; height: 170px; object-fit: contain;"
                    />
                  </div>
                  <div class="small fw-semibold text-muted">Scan to pay with any UPI App</div>
                  <div class="d-flex justify-content-center gap-2 mt-2">
                    <span class="badge bg-light text-dark border">GPay</span>
                    <span class="badge bg-light text-dark border">PhonePe</span>
                    <span class="badge bg-light text-dark border">Paytm</span>
                    <span class="badge bg-light text-dark border">BHIM</span>
                  </div>
                </div>
                <div class="col-md-7 ps-md-4 mt-3 mt-md-0">
                  <h6 class="fw-bold mb-2">Or Pay via UPI ID / VPA</h6>
                  <div class="mb-3">
                    <label class="form-label small text-muted">School UPI VPA</label>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control" readonly :value="schoolUpiId" />
                      <button class="btn btn-outline-secondary" type="button" @click="copyUpiId">
                        <i class="bi bi-clipboard me-1"></i> Copy
                      </button>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small text-muted">Enter Your UPI ID / Mobile</label>
                    <input
                      type="text"
                      class="form-control form-control-sm"
                      placeholder="e.g. yourname@oksbi / 9876543210@paytm"
                      v-model="upiForm.vpa"
                    />
                  </div>
                  <small class="text-muted d-block">
                    <i class="bi bi-info-circle me-1"></i> A payment request will be sent to your UPI app for approval.
                  </small>
                </div>
              </div>
            </div>

            <!-- Method 2: Credit / Debit Card -->
            <div v-if="paymentMethod === 'Card'" class="p-3 border rounded-3 bg-white">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">Card Number</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-credit-card"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      maxlength="19"
                      placeholder="XXXX XXXX XXXX XXXX"
                      v-model="cardForm.number"
                      @input="formatCardNumber"
                    />
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Name on Card</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Cardholder's full name"
                    v-model="cardForm.name"
                  />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">Expiry Date</label>
                  <input
                    type="text"
                    class="form-control"
                    placeholder="MM/YY"
                    maxlength="5"
                    v-model="cardForm.expiry"
                  />
                </div>
                <div class="col-6">
                  <label class="form-label small fw-semibold">CVV / CVC</label>
                  <input
                    type="password"
                    class="form-control"
                    placeholder="***"
                    maxlength="4"
                    v-model="cardForm.cvv"
                  />
                </div>
              </div>
            </div>

            <!-- Method 3: Net Banking -->
            <div v-if="paymentMethod === 'Bank Transfer'" class="p-3 border rounded-3 bg-white">
              <h6 class="fw-bold mb-3">Select Your Bank</h6>
              <div class="row g-2 mb-3">
                <div class="col-4" v-for="b in popularBanks" :key="b.code">
                  <button
                    type="button"
                    class="btn btn-sm w-100 p-2 text-truncate border rounded-3 text-start d-flex align-items-center gap-2"
                    :class="{ 'btn-primary text-white': netBankingBank === b.name, 'btn-light': netBankingBank !== b.name }"
                    @click="netBankingBank = b.name"
                  >
                    <i class="bi bi-bank"></i>
                    <span class="small">{{ b.name }}</span>
                  </button>
                </div>
              </div>
              <div class="mt-2">
                <label class="form-label small text-muted">All Other Banks</label>
                <select class="form-select form-select-sm" v-model="netBankingBank">
                  <option value="">-- Choose from all Indian banks --</option>
                  <option value="State Bank of India">State Bank of India</option>
                  <option value="HDFC Bank">HDFC Bank</option>
                  <option value="ICICI Bank">ICICI Bank</option>
                  <option value="Axis Bank">Axis Bank</option>
                  <option value="Kotak Mahindra Bank">Kotak Mahindra Bank</option>
                  <option value="Punjab National Bank">Punjab National Bank</option>
                  <option value="Bank of Baroda">Bank of Baroda</option>
                  <option value="Canara Bank">Canara Bank</option>
                  <option value="Union Bank of India">Union Bank of India</option>
                </select>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light p-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small">
              <i class="bi bi-lock-fill text-success me-1"></i> Bank-grade 256-bit SSL encryption
            </span>
            <div class="d-flex gap-2">
              <button
                type="button"
                class="btn btn-outline-secondary rounded-pill px-3"
                @click="showPaymentModal = false"
                :disabled="isProcessingPayment"
              >
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-success rounded-pill px-4 fw-bold d-flex align-items-center gap-2"
                @click="submitPayment"
                :disabled="isProcessingPayment"
              >
                <span v-if="isProcessingPayment" class="spinner-border spinner-border-sm" role="status"></span>
                <span>{{ isProcessingPayment ? 'Processing...' : `Pay ₹${Number(paymentData.amount).toLocaleString('en-IN')}` }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- OFFICIAL PRINTABLE FEE RECEIPT MODAL       -->
    <!-- ========================================== -->
    <div
      v-if="showReceiptModal && receiptData"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.7); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-light border-0 px-4 pt-3 pb-0 d-flex justify-content-between">
            <h6 class="fw-bold text-muted mb-0">Official Fee Payment Receipt</h6>
            <button type="button" class="btn-close" @click="showReceiptModal = false"></button>
          </div>

          <div class="modal-body p-4" id="printableFeeReceipt">
            <!-- School Header -->
            <div class="border-bottom pb-3 mb-3 text-center position-relative">
              <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
                <img
                  v-if="receiptData.school?.logo"
                  :src="receiptData.school.logo"
                  alt="School Logo"
                  style="max-height: 55px; max-width: 60px; object-fit: contain;"
                />
                <div class="text-center">
                  <h4 class="fw-bold mb-0 text-dark">{{ receiptData.school?.name || 'School ERP Academy' }}</h4>
                  <small class="text-muted d-block">{{ receiptData.school?.address || 'Affiliated to State / Central Board of Secondary Education' }}</small>
                  <small class="text-muted">
                    Phone: {{ receiptData.school?.phone || 'N/A' }} | Email: {{ receiptData.school?.email || 'office@school.edu' }}
                  </small>
                </div>
              </div>
              <div class="badge bg-primary px-3 py-1 text-uppercase mt-2" style="letter-spacing: 1px;">
                Official Fee Payment Receipt
              </div>
            </div>

            <!-- Receipt & Student Metadata -->
            <div class="row g-2 mb-3 bg-light rounded-3 p-3 small">
              <div class="col-sm-6">
                <div><strong>Receipt No:</strong> <span class="font-monospace text-primary fw-bold">{{ receiptData.receipt_no }}</span></div>
                <div><strong>Transaction ID:</strong> <span class="font-monospace text-muted">{{ receiptData.transaction_no }}</span></div>
                <div><strong>Payment Date:</strong> {{ receiptData.payment_date }}</div>
                <div><strong>Payment Mode:</strong> <span class="badge bg-secondary text-uppercase">{{ receiptData.payment_method }}</span></div>
              </div>
              <div class="col-sm-6 text-sm-end">
                <div><strong>Student Name:</strong> <span class="fw-bold text-dark">{{ receiptData.student?.full_name }}</span></div>
                <div><strong>Admission No:</strong> {{ receiptData.student?.admission_no }} &bull; <strong>Roll No:</strong> #{{ receiptData.student?.roll_no || '1' }}</div>
                <div><strong>Class & Section:</strong> {{ receiptData.student?.class_name }} {{ receiptData.student?.section_name ? ('(' + receiptData.student.section_name + ')') : '' }}</div>
                <div v-if="receiptData.student?.father_name"><strong>Parent/Guardian:</strong> {{ receiptData.student.father_name }}</div>
              </div>
            </div>

            <!-- Itemized Table -->
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle mb-0">
                <thead class="table-light small">
                  <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Fee Description</th>
                    <th>Period</th>
                    <th class="text-end" style="width: 150px;">Amount Paid</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(it, idx) in receiptData.items" :key="idx">
                    <td class="text-center small">{{ idx + 1 }}</td>
                    <td class="fw-semibold">{{ it.fee_type }}</td>
                    <td class="small text-muted">{{ it.period }}</td>
                    <td class="text-end fw-bold">₹{{ Number(it.amount).toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}</td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr class="table-light">
                    <td colspan="3" class="text-end fw-bold">Total Amount Cleared:</td>
                    <td class="text-end fw-bold text-success fs-6">
                      ₹{{ Number(receiptData.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 }) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Footer Notes & Seal -->
            <div class="row align-items-end mt-4 pt-2">
              <div class="col-8">
                <small class="text-muted d-block" style="font-size: 0.72rem;">
                  * This is an electronically generated receipt issued by the school financial accounting system.
                </small>
                <small class="text-muted d-block" style="font-size: 0.72rem;">
                  * Fees once paid are non-refundable according to institutional terms.
                </small>
              </div>
              <div class="col-4 text-center">
                <div class="border-top pt-2 mt-4">
                  <small class="fw-bold text-dark d-block">Authorized Signatory / Cashier</small>
                  <small class="text-muted" style="font-size: 0.7rem;">Accounts Department</small>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light p-3 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" @click="showReceiptModal = false">
              Close
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" @click="printReceipt">
              <i class="bi bi-printer-fill me-1"></i> Print / Download PDF
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const activeTab = ref('dues')
const dues = ref([])
const payments = ref([])
const summary = ref({ total_due: 0, total_paid: 0, balance: 0 })

// Selection state
const selectedDueIds = ref([])

// Payment Modal State
const showPaymentModal = ref(false)
const isProcessingPayment = ref(false)
const paymentMethod = ref('UPI')
const netBankingBank = ref('State Bank of India')
const schoolUpiId = ref('schoolfee.payment@okaxis')

const paymentData = ref({
  amount: 0,
  dueIds: [],
  duesCount: 0,
})

const upiForm = ref({
  vpa: 'parent@upi'
})

const cardForm = ref({
  number: '4532 8912 3456 7890',
  name: 'Account Holder',
  expiry: '12/28',
  cvv: '891'
})

const popularBanks = [
  { name: 'State Bank of India', code: 'SBI' },
  { name: 'HDFC Bank', code: 'HDFC' },
  { name: 'ICICI Bank', code: 'ICICI' },
  { name: 'Axis Bank', code: 'AXIS' },
  { name: 'Kotak Bank', code: 'KOTAK' },
  { name: 'Punjab National Bank', code: 'PNB' },
]

// Receipt Modal State
const showReceiptModal = ref(false)
const receiptData = ref(null)

const selectableDues = computed(() => dues.value.filter(d => Number(d.balance_amount) > 0))
const isAllSelected = computed(() => selectableDues.value.length > 0 && selectedDueIds.value.length === selectableDues.value.length)
const pendingDuesCount = computed(() => selectableDues.value.length)

const selectedDuesTotal = computed(() => {
  return dues.value
    .filter(d => selectedDueIds.value.includes(d.id))
    .reduce((sum, d) => sum + Number(d.balance_amount), 0)
})

const upiQrUrl = computed(() => {
  const amt = paymentData.value.amount || 1000
  const encodedUpi = encodeURIComponent(`upi://pay?pa=${schoolUpiId.value}&pn=SchoolERP&am=${amt}&cu=INR`)
  return `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodedUpi}`
})

function toggleSelectAll(e) {
  if (e.target.checked) {
    selectedDueIds.value = selectableDues.value.map(d => d.id)
  } else {
    selectedDueIds.value = []
  }
}

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
      // Clear selections on reload
      selectedDueIds.value = []
    }
  } catch (err) {
    console.error('Failed to load portal fees', err)
  }
}

function openPaymentModal(singleDue = null) {
  if (singleDue) {
    paymentData.value = {
      amount: Number(singleDue.balance_amount),
      dueIds: [singleDue.id],
      duesCount: 1,
    }
  } else if (selectedDueIds.value.length > 0) {
    paymentData.value = {
      amount: selectedDuesTotal.value,
      dueIds: [...selectedDueIds.value],
      duesCount: selectedDueIds.value.length,
    }
  } else {
    // Pay all selectable
    if (selectableDues.value.length === 0) {
      Swal.fire({
        icon: 'info',
        title: 'No Pending Dues',
        text: 'All fees are already cleared!',
      })
      return
    }
    paymentData.value = {
      amount: Number(summary.value.balance),
      dueIds: selectableDues.value.map(d => d.id),
      duesCount: selectableDues.value.length,
    }
  }

  showPaymentModal.value = true
}

function formatCardNumber(e) {
  let val = e.target.value.replace(/\D/g, '')
  val = val.substring(0, 16)
  const parts = val.match(/.{1,4}/g)
  cardForm.value.number = parts ? parts.join(' ') : val
}

function copyUpiId() {
  navigator.clipboard.writeText(schoolUpiId.value)
  Swal.fire({
    icon: 'success',
    title: 'UPI ID Copied',
    text: schoolUpiId.value,
    timer: 1500,
    showConfirmButton: false,
  })
}

async function submitPayment() {
  isProcessingPayment.value = true

  const activeStudentId = localStorage.getItem('portal_active_student_id')

  const payload = {
    student_id: activeStudentId ? Number(activeStudentId) : null,
    due_ids: paymentData.value.dueIds,
    amount: paymentData.value.amount,
    payment_method: paymentMethod.value,
    transaction_no: 'TXN' + Date.now(),
    bank_name: paymentMethod.value === 'Bank Transfer' ? netBankingBank.value : (paymentMethod.value === 'UPI' ? 'UPI Gateway' : 'Online Gateway'),
    upi_id: paymentMethod.value === 'UPI' ? upiForm.value.vpa : null,
  }

  try {
    const res = await axios.post('/api/portal/fees/pay', payload, getAuthConfig())
    if (res.data.success) {
      showPaymentModal.value = false
      Swal.fire({
        icon: 'success',
        title: 'Payment Successful!',
        text: `Transaction completed. Receipt #${res.data.data.receipt_no} has been generated.`,
        confirmButtonText: 'View Receipt',
        showCancelButton: true,
        cancelButtonText: 'Close',
      }).then((result) => {
        if (result.isConfirmed) {
          viewReceipt(res.data.data.payment_id)
        }
      })
      await loadFees()
    }
  } catch (err) {
    console.error('Payment error', err)
    Swal.fire({
      icon: 'error',
      title: 'Payment Failed',
      text: err.response?.data?.message || 'Unable to process payment at this time. Please try again.',
    })
  } finally {
    isProcessingPayment.value = false
  }
}

async function viewReceipt(paymentId) {
  try {
    const res = await axios.get(`/api/portal/fees/receipt/${paymentId}`, getAuthConfig())
    if (res.data.success) {
      receiptData.value = res.data.data
      showReceiptModal.value = true
    }
  } catch (err) {
    console.error('Failed to load receipt', err)
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not fetch receipt details.' })
  }
}

function printReceipt() {
  const printContent = document.getElementById('printableFeeReceipt')
  if (!printContent) return

  const originalContents = document.body.innerHTML
  const printableHtml = printContent.innerHTML

  const printWindow = window.open('', '_blank', 'height=700,width=850')
  printWindow.document.write(`
    <html>
      <head>
        <title>Fee Receipt - ${receiptData.value?.receipt_no || 'Official'}</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <style>
          body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 25px; }
          @media print {
            body { padding: 0; }
            .btn, .no-print { display: none !important; }
          }
        </style>
      </head>
      <body>
        ${printableHtml}
        <script>
          window.onload = function() {
            window.print();
            window.close();
          };
        <\/script>
      </body>
    </html>
  `)
  printWindow.document.close()
}

function handleStudentSwitch() {
  loadFees()
}

onMounted(() => {
  loadFees()
  window.addEventListener('portal-student-switched', handleStudentSwitch)
})

onUnmounted(() => {
  window.removeEventListener('portal-student-switched', handleStudentSwitch)
})
</script>

<style scoped>
@media print {
  .modal-footer, .btn, .nav, header, aside {
    display: none !important;
  }
}
</style>