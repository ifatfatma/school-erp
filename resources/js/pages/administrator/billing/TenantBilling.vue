<template>
  <div class="container-fluid py-3">
    <AppBreadcrumb
      :items="[
        { label: 'Dashboard', to: '/' },
        { label: 'Administrator' },
        { label: 'Tenant Billing & Upgrades' }
      ]"
    />

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
      <div>
        <h3 class="fw-bold mb-1">
          <i class="bi bi-credit-card-2-front text-primary me-2"></i>
          Tenant Subscriptions & Plan Upgrades
        </h3>
        <p class="text-muted small mb-0">
          Manage institution subscription tiers, generate prorated difference upgrade invoices, and mark offline payments as paid.
        </p>
      </div>
      <div>
        <button class="btn btn-primary" @click="openUpgradeModal(null)">
          <i class="bi bi-arrow-up-circle me-1"></i> Generate Upgrade Invoice
        </button>
      </div>
    </div>

    <!-- Metric Counters -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-semibold d-block">Total Institutions</span>
              <h3 class="fw-bold mb-0 text-dark">{{ tenants.length }}</h3>
            </div>
            <div class="bg-primary-subtle text-primary rounded-circle p-3 fs-4">
              <i class="bi bi-buildings"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-semibold d-block">Starter / Basic (1 School)</span>
              <h3 class="fw-bold mb-0 text-secondary">{{ basicCount }}</h3>
            </div>
            <div class="bg-light text-secondary rounded-circle p-3 fs-4">
              <i class="bi bi-building"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-semibold d-block">Professional (2 Schools)</span>
              <h3 class="fw-bold mb-0 text-success">{{ proCount }}</h3>
            </div>
            <div class="bg-success-subtle text-success rounded-circle p-3 fs-4">
              <i class="bi bi-diagram-2"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted small fw-semibold d-block">Enterprise (5 Schools)</span>
              <h3 class="fw-bold mb-0 text-primary">{{ entCount }}</h3>
            </div>
            <div class="bg-primary-subtle text-primary rounded-circle p-3 fs-4">
              <i class="bi bi-shield-check"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-3 gap-2" role="tablist">
      <li class="nav-item">
        <button
          class="nav-link"
          :class="{ active: activeTab === 'tenants' }"
          @click="activeTab = 'tenants'"
        >
          <i class="bi bi-people me-1"></i> School Admins & Tiers ({{ tenants.length }})
        </button>
      </li>
      <li class="nav-item">
        <button
          class="nav-link position-relative"
          :class="{ active: activeTab === 'invoices' }"
          @click="activeTab = 'invoices'; loadInvoices();"
        >
          <i class="bi bi-receipt me-1"></i> Plan Upgrade Invoices
          <span v-if="pendingInvoicesCount > 0" class="badge bg-danger rounded-pill ms-1">
            {{ pendingInvoicesCount }}
          </span>
        </button>
      </li>
    </ul>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary me-2"></div>
      <span class="text-muted">Loading billing data...</span>
    </div>

    <!-- TAB 1: Tenants List -->
    <div v-else-if="activeTab === 'tenants'" class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
        <h5 class="mb-0 fw-bold">All School Institutions & Admins</h5>
        <div class="input-group input-group-sm" style="max-width: 300px;">
          <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
          <input
            type="text"
            class="form-control"
            placeholder="Search institution or admin..."
            v-model="tenantSearch"
          />
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>School / Institution</th>
              <th>Admin Contact</th>
              <th>Active Plan Tier</th>
              <th>School Usage</th>
              <th>Pending Invoice</th>
              <th class="text-end pe-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in filteredTenants" :key="'tenant-school-' + t.school_id">
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img
                    v-if="t.school_logo"
                    :src="t.school_logo"
                    class="rounded border bg-white p-1"
                    style="width: 38px; height: 38px; object-fit: contain;"
                  />
                  <div
                    v-else
                    class="rounded bg-light border d-flex align-items-center justify-content-center"
                    style="width: 38px; height: 38px;"
                  >
                    <i class="bi bi-building text-secondary"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                      <span>{{ t.school_name }}</span>
                      <span
                        v-if="String(t.school_id) === String(tenantStore.activeSchoolId)"
                        class="badge bg-success-subtle text-success border border-success"
                        style="font-size: 0.7rem;"
                      >
                        Active Context
                      </span>
                    </div>
                    <small class="text-muted font-monospace">{{ t.school_code }}</small>
                  </div>
                </div>
              </td>
              <td>
                <div class="fw-semibold">{{ t.name }}</div>
                <small class="text-muted">{{ t.email }}</small>
              </td>
              <td>
                <span
                  class="badge text-uppercase px-2 py-1"
                  :class="{
                    'bg-secondary': t.plan_slug === 'basic' || t.plan_slug === 'starter',
                    'bg-success': t.plan_slug === 'professional' || t.plan_slug === 'growth',
                    'bg-primary': t.plan_slug === 'enterprise'
                  }"
                >
                  {{ t.plan_name }}
                </span>
                <div class="small text-muted mt-1">₹{{ Number(t.plan_price).toLocaleString('en-IN') }}/yr</div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2" style="max-width: 140px;">
                  <div class="progress flex-grow-1" style="height: 6px;">
                    <div
                      class="progress-bar"
                      :class="t.owned_schools_count >= t.max_schools ? 'bg-warning' : 'bg-success'"
                      :style="{ width: `${Math.min(100, (t.owned_schools_count / t.max_schools) * 100)}%` }"
                    ></div>
                  </div>
                  <small class="fw-bold">{{ t.owned_schools_count }} / {{ t.max_schools }}</small>
                </div>
                <small v-if="t.owned_schools_count >= t.max_schools" class="text-danger small d-block">
                  <i class="bi bi-exclamation-circle me-1"></i>Limit Reached
                </small>
                <small v-else class="text-success small d-block">
                  {{ t.max_schools - t.owned_schools_count }} available
                </small>
              </td>
              <td>
                <div v-if="t.pending_invoice" class="p-2 rounded bg-warning-subtle border border-warning text-dark small">
                  <div class="fw-bold text-truncate" style="max-width: 180px;">
                    {{ t.pending_invoice.invoice_number }}
                  </div>
                  <div>To: <strong>{{ t.pending_invoice.target_plan?.name }}</strong></div>
                  <div class="fw-bold text-danger">₹{{ Number(t.pending_invoice.net_amount).toLocaleString('en-IN') }}</div>
                  <button
                    class="btn btn-xs btn-success mt-1 py-0 px-2 fw-semibold"
                    style="font-size: 0.75rem;"
                    @click="markInvoicePaid(t.pending_invoice)"
                  >
                    <i class="bi bi-check-circle me-1"></i>Mark Paid
                  </button>
                </div>
                <span v-else class="text-muted small">None</span>
              </td>
              <td class="text-end pe-3">
                <button
                  class="btn btn-sm btn-outline-primary fw-semibold"
                  @click="openUpgradeModal(t)"
                  title="Generate Plan Upgrade Invoice"
                >
                  <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Plan
                </button>
              </td>
            </tr>
            <tr v-if="tenants.length === 0">
              <td colspan="6" class="text-center py-4 text-muted">No school tenants found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: Invoices List -->
    <div v-else-if="activeTab === 'invoices'" class="card border-0 shadow-sm rounded-3">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Plan Upgrade & Billing Invoices</h5>
        <div class="d-flex align-items-center gap-2">
          <select class="form-select form-select-sm" v-model="invoiceFilterStatus" @change="loadInvoices">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
          </select>
          <button class="btn btn-sm btn-outline-secondary" @click="loadInvoices">
            <i class="bi bi-arrow-repeat"></i>
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Invoice #</th>
              <th>Institution / Admin</th>
              <th>Plan Transition</th>
              <th>Breakdown</th>
              <th>Net Payable</th>
              <th>Status</th>
              <th>Date</th>
              <th class="text-end pe-3">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inv in invoices" :key="'inv-' + inv.id">
              <td>
                <span class="font-monospace fw-bold text-dark">{{ inv.invoice_number }}</span>
                <span v-if="inv.type" class="badge bg-light text-muted border ms-1 small">Upgrade</span>
              </td>
              <td>
                <div class="fw-bold">{{ inv.user?.name || 'N/A' }}</div>
                <small class="text-muted">{{ inv.school?.name || inv.user?.email }}</small>
              </td>
              <td>
                <div class="d-flex align-items-center gap-1 small">
                  <span class="badge bg-secondary">{{ inv.current_plan?.name || 'Basic' }}</span>
                  <i class="bi bi-arrow-right text-muted"></i>
                  <span class="badge bg-success">{{ inv.target_plan?.name }}</span>
                </div>
              </td>
              <td>
                <small class="d-block text-muted">Target: ₹{{ Number(inv.target_plan_price).toLocaleString('en-IN') }}</small>
                <small class="d-block text-success">Credit: -₹{{ Number(inv.credit_amount).toLocaleString('en-IN') }}</small>
              </td>
              <td>
                <span class="fw-bold fs-6 text-primary">₹{{ Number(inv.net_amount).toLocaleString('en-IN') }}</span>
              </td>
              <td>
                <span
                  class="badge text-uppercase"
                  :class="{
                    'bg-warning text-dark': inv.status === 'pending',
                    'bg-success': inv.status === 'paid',
                    'bg-secondary': inv.status === 'cancelled'
                  }"
                >
                  {{ inv.status }}
                </span>
                <div v-if="inv.payment_method" class="text-muted small mt-1">
                  via {{ inv.payment_method }}
                </div>
              </td>
              <td>
                <small class="text-muted">{{ formatDate(inv.created_at) }}</small>
              </td>
              <td class="text-end pe-3">
                <button
                  v-if="inv.status === 'pending'"
                  class="btn btn-sm btn-success fw-semibold"
                  @click="markInvoicePaid(inv)"
                >
                  <i class="bi bi-check-lg me-1"></i> Mark as Paid
                </button>
                <span v-else-if="inv.status === 'paid'" class="text-success small fw-semibold">
                  <i class="bi bi-check-circle-fill me-1"></i> Activated
                </span>
              </td>
            </tr>
            <tr v-if="invoices.length === 0">
              <td colspan="8" class="text-center py-4 text-muted">No invoices found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Upgrade & Generate Invoice Modal -->
    <div class="modal fade" id="upgradeModal" tabindex="-1" ref="upgradeModalRef">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold">
              <i class="bi bi-arrow-up-circle-fill text-primary me-2"></i>
              Generate Subscription & Upgrade Invoice
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="generateInvoice">
              <!-- Select School / Tenant -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Select School / Institution <span class="text-danger">*</span></label>
                <select class="form-select" v-model="selectedSchoolId" @change="onSchoolSelected" required>
                  <option value="" disabled>-- Select School Institution --</option>
                  <option v-for="t in tenants" :key="'sel-s-' + t.school_id" :value="t.school_id">
                    {{ t.school_name }} ({{ t.school_code }}) — {{ t.name }} [{{ t.plan_name }}]
                  </option>
                </select>
              </div>

              <!-- Highest Plan Notice (if already on Enterprise) -->
              <div v-if="selectedTenant && isMaxPlan" class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div>
                  <strong>Maximum Tier Active:</strong> This institution is already enrolled in the highest tier ({{ selectedTenant.plan_name }} - {{ selectedTenant.max_schools }} Campuses). You can generate an <strong>Annual Renewal</strong> invoice or adjust plan tier below.
                </div>
              </div>

              <!-- Target Plan -->
              <div class="mb-3" v-if="selectedTenant">
                <label class="form-label fw-semibold">Select Target Upgrade Plan <span class="text-danger">*</span></label>
                <select class="form-select" v-model="selectedTargetPlanId" @change="fetchCalculation" required>
                  <option value="" disabled>-- Select Target Plan --</option>
                  <option
                    v-for="p in allPlansForSelection"
                    :key="'target-p-' + p.id"
                    :value="p.id"
                  >
                    {{ p.name }} (Max: {{ p.max_schools }} Campuses) - ₹{{ Number(p.price).toLocaleString('en-IN') }}/yr {{ getPlanBadgeLabel(p) }}
                  </option>
                </select>
              </div>

              <!-- Calculation Preview Breakdown -->
              <div v-if="calcData" class="card bg-light border p-3 mb-3">
                <h6 class="fw-bold mb-3 text-dark">
                  <i class="bi bi-calculator me-1 text-primary"></i>
                  Prorated Difference Breakdown
                </h6>
                <div class="row g-2 mb-2">
                  <div class="col-sm-6">
                    <small class="text-muted d-block">Current Plan</small>
                    <strong>{{ calcData.current_plan.name }}</strong>
                    <div class="text-muted small">₹{{ Number(calcData.current_plan.price).toLocaleString('en-IN') }}</div>
                  </div>
                  <div class="col-sm-6">
                    <small class="text-muted d-block">Target Upgrade Plan</small>
                    <strong class="text-primary">{{ calcData.target_plan.name }}</strong>
                    <div class="text-muted small">₹{{ Number(calcData.target_plan.price).toLocaleString('en-IN') }} (Allows {{ calcData.target_plan.max_schools }} schools)</div>
                  </div>
                </div>

                <hr class="my-2" />

                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span>Target Plan Price:</span>
                  <span>₹{{ Number(calcData.target_plan.price).toLocaleString('en-IN') }}</span>
                </div>
                <div v-if="calcData.credit_amount > 0" class="d-flex justify-content-between align-items-center mb-1 text-success">
                  <span>Previous Plan Credit (Already Paid):</span>
                  <span>-₹{{ Number(calcData.credit_amount).toLocaleString('en-IN') }}</span>
                </div>
                <div v-else-if="calcData.target_plan.id === calcData.current_plan.id" class="d-flex justify-content-between align-items-center mb-1 text-muted small">
                  <span>Billing Type:</span>
                  <span class="badge bg-info-subtle text-info border border-info">Annual Plan Renewal</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top fw-bold fs-5 text-dark">
                  <span>Net Payable Amount:</span>
                  <span class="text-primary">₹{{ Number(customAmount ?? calcData.net_amount).toLocaleString('en-IN') }}</span>
                </div>

                <!-- Custom Override -->
                <div class="mt-3">
                  <label class="form-label small fw-semibold text-muted">
                    Custom Override Amount (Optional discount/adjustment):
                  </label>
                  <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text">₹</span>
                    <input
                      type="number"
                      class="form-control"
                      v-model.number="customAmount"
                      :placeholder="String(calcData.net_amount)"
                      min="0"
                    />
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Invoice Notes / Remarks (Optional)</label>
                <textarea
                  class="form-control"
                  rows="2"
                  v-model="invoiceNotes"
                  placeholder="e.g. Upgrade for addition of 2nd campus branch..."
                ></textarea>
              </div>

              <div class="modal-footer px-0 pb-0 pt-3 border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="generating || !selectedTargetPlanId"
                >
                  <span v-if="generating" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="bi bi-file-earmark-check me-1"></i>
                  {{ generating ? 'Generating Invoice...' : 'Generate Upgrade Invoice' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue';
import api from '@/api/axios';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';
import { useTenantStore } from '@/stores/tenant';

const tenantStore = useTenantStore();
const activeTab = ref('tenants');
const loading = ref(false);
const tenants = ref([]);
const tenantSearch = ref('');
const plans = ref([]);
const invoices = ref([]);
const invoiceFilterStatus = ref('');

const filteredTenants = computed(() => {
  if (!tenantSearch.value.trim()) return tenants.value;
  const q = tenantSearch.value.toLowerCase().trim();
  return tenants.value.filter(t =>
    (t.name && t.name.toLowerCase().includes(q)) ||
    (t.email && t.email.toLowerCase().includes(q)) ||
    (t.school_name && t.school_name.toLowerCase().includes(q)) ||
    (t.school_code && t.school_code.toLowerCase().includes(q))
  );
});

// Modal state
const upgradeModalRef = ref(null);
let bsUpgradeModal = null;
const selectedSchoolId = ref('');
const selectedUserId = ref('');
const selectedTargetPlanId = ref('');
const customAmount = ref(null);
const invoiceNotes = ref('');
const calcData = ref(null);
const generating = ref(false);

const basicCount = computed(() => tenants.value.filter(t => t.plan_slug === 'basic' || t.plan_slug === 'starter').length);
const proCount = computed(() => tenants.value.filter(t => t.plan_slug === 'professional' || t.plan_slug === 'growth').length);
const entCount = computed(() => tenants.value.filter(t => t.plan_slug === 'enterprise').length);
const pendingInvoicesCount = computed(() => invoices.value.filter(i => i.status === 'pending').length);

const selectedTenant = computed(() => tenants.value.find(t => t.school_id === Number(selectedSchoolId.value)));

const isMaxPlan = computed(() => {
  if (!selectedTenant.value) return false;
  const currentMax = selectedTenant.value.max_schools || 1;
  const highest = Math.max(...plans.value.map(p => p.max_schools || 1), 5);
  return currentMax >= highest;
});

const allPlansForSelection = computed(() => {
  return plans.value;
});

const getPlanBadgeLabel = (p) => {
  if (!selectedTenant.value) return '';
  const currentMax = selectedTenant.value.max_schools || 1;
  const currentSlug = selectedTenant.value.plan_slug;

  if (p.slug === currentSlug) {
    return '• (Current Plan / Annual Renewal)';
  } else if (p.max_schools > currentMax) {
    return '• (Upgrade)';
  } else {
    return '• (Switch / Downgrade)';
  }
};

const loadTenants = async () => {
  loading.value = true;
  try {
    const res = await api.get('/billing/tenants');
    tenants.value = res.data.data || [];
  } catch (err) {
    console.error('Failed loading billing tenants:', err);
  } finally {
    loading.value = false;
  }
};

const loadPlans = async () => {
  try {
    const res = await api.get('/billing/plans');
    plans.value = res.data.data || [];
  } catch (err) {
    console.error('Failed loading plans:', err);
  }
};

const loadInvoices = async () => {
  try {
    const params = {};
    if (invoiceFilterStatus.value) params.status = invoiceFilterStatus.value;
    const res = await api.get('/billing/invoices', { params });
    invoices.value = res.data.data || [];
  } catch (err) {
    console.error('Failed loading invoices:', err);
  }
};

const openUpgradeModal = (tenant = null) => {
  if (!bsUpgradeModal) {
    bsUpgradeModal = new Modal(upgradeModalRef.value);
  }
  calcData.value = null;
  customAmount.value = null;
  invoiceNotes.value = '';

  if (tenant) {
    selectedSchoolId.value = tenant.school_id;
    selectedUserId.value = tenant.user_id || '';
  } else {
    const upgradeable = tenants.value.find(t => (t.max_schools || 1) < 5) || tenants.value[0];
    selectedSchoolId.value = upgradeable ? upgradeable.school_id : '';
    selectedUserId.value = upgradeable ? (upgradeable.user_id || '') : '';
  }

  onSchoolSelected();
  bsUpgradeModal.show();
};

const onSchoolSelected = () => {
  if (!selectedTenant.value) return;
  selectedUserId.value = selectedTenant.value.user_id || '';
  const currentMax = selectedTenant.value.max_schools || 1;
  const higher = plans.value.filter(p => p.max_schools > currentMax);
  if (higher.length > 0) {
    selectedTargetPlanId.value = higher[0].id;
  } else {
    const current = plans.value.find(p => p.slug === selectedTenant.value.plan_slug);
    selectedTargetPlanId.value = current?.id || plans.value[0]?.id || '';
  }
  fetchCalculation();
};

const fetchCalculation = async () => {
  if (!selectedSchoolId.value || !selectedTargetPlanId.value) {
    calcData.value = null;
    return;
  }
  try {
    const res = await api.post('/billing/upgrade-invoice/calculate', {
      school_id: selectedSchoolId.value,
      user_id: selectedUserId.value || null,
      target_plan_id: selectedTargetPlanId.value,
    });
    calcData.value = res.data.data;
    customAmount.value = calcData.value.net_amount;
  } catch (err) {
    console.error('Failed calculating difference:', err);
  }
};

const generateInvoice = async () => {
  generating.value = true;
  try {
    const payload = {
      school_id: selectedSchoolId.value,
      user_id: selectedUserId.value || null,
      target_plan_id: selectedTargetPlanId.value,
      custom_net_amount: customAmount.value,
      notes: invoiceNotes.value,
    };
    const res = await api.post('/billing/upgrade-invoice/generate', payload);

    bsUpgradeModal.hide();
    Swal.fire({
      icon: 'success',
      title: 'Invoice Generated!',
      text: res.data.message || 'Upgrade invoice created successfully.',
    });

    await loadTenants();
    await loadInvoices();
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Failed to Generate Invoice',
      text: err.response?.data?.message || 'Error occurred while creating invoice.',
    });
  } finally {
    generating.value = false;
  }
};

const markInvoicePaid = async (invoice) => {
  const { value: paymentMethod } = await Swal.fire({
    title: `Mark Invoice ${invoice.invoice_number} as Paid?`,
    html: `
      <p class="text-muted small">
        This will immediately upgrade <strong>${invoice.user?.name || 'the tenant'}</strong> to the
        <strong>${invoice.target_plan?.name}</strong> plan and unlock school limit to
        <strong>${invoice.target_plan?.max_schools} schools</strong>.
      </p>
      <div class="mt-3 text-start">
        <label class="form-label small fw-bold">Select Offline Payment Method:</label>
        <select id="swal-payment-method" class="form-select form-select-sm">
          <option value="bank_transfer">Bank Wire / IMPS / NEFT</option>
          <option value="cash">Cash Received</option>
          <option value="cheque">Cheque Deposit</option>
          <option value="manual_approval">Super Admin Manual Approval</option>
        </select>
      </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Mark Paid & Activate',
    confirmButtonColor: '#198754',
    preConfirm: () => {
      return document.getElementById('swal-payment-method').value;
    }
  });

  if (paymentMethod) {
    try {
      const res = await api.post(`/billing/invoices/${invoice.id}/pay`, {
        payment_method: paymentMethod,
      });

      Swal.fire({
        icon: 'success',
        title: 'Plan Upgraded & Activated!',
        text: res.data.message,
      });

      await loadTenants();
      await loadInvoices();
    } catch (err) {
      Swal.fire({
        icon: 'error',
        title: 'Payment Activation Failed',
        text: err.response?.data?.message || 'Error occurred.',
      });
    }
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-IN', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  });
};

onMounted(async () => {
  await Promise.all([loadTenants(), loadPlans(), loadInvoices()]);
});
</script>

<style scoped>
.btn-xs {
  font-size: 0.75rem;
  padding: 0.15rem 0.5rem;
}
</style>
