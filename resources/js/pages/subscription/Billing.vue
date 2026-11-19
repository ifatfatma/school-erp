<template>
  <div>
    <!-- SUPER ADMIN VIEW: Tenant Subscriptions & Upgrade Invoice Management Console -->
    <TenantBilling v-if="isSuperAdmin" />

    <!-- SCHOOL ADMIN VIEW: Institution Subscription & Upgrade Invoices -->
    <div v-else class="container-fluid py-3">
      <AppBreadcrumb
        :items="[
          { label: 'Dashboard', to: '/' },
          { label: 'Subscription & Invoices' }
        ]"
      />

      <!-- Header -->
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h3 class="fw-bold mb-1">
            <i class="bi bi-credit-card text-primary me-2"></i>
            Subscription & Institution Billing
          </h3>
          <p class="text-muted small mb-0">
            Review your institution's subscription plan, campus quotas, feature access, and upgrade invoices.
          </p>
        </div>
        <div>
          <router-link to="/schools" class="btn btn-outline-primary">
            <i class="bi bi-buildings me-1"></i> View My Schools
          </router-link>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary me-2"></div>
        <span class="text-muted">Loading subscription details...</span>
      </div>

      <div v-else>
        <!-- PROMINENT PENDING UPGRADE INVOICE BANNER -->
      <div v-if="subData.pending_invoice" class="card border-warning shadow-sm rounded-4 mb-4 bg-warning-subtle">
        <div class="card-body p-4">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-warning text-dark text-uppercase px-2 py-1 fw-bold">Pending Upgrade Invoice</span>
                <span class="font-monospace text-dark fw-bold">{{ subData.pending_invoice.invoice_number }}</span>
              </div>
              <h4 class="fw-bold text-dark mb-1">
                Upgrade to {{ subData.pending_invoice.target_plan?.name }}
              </h4>
              <p class="text-muted small mb-2">
                A plan upgrade invoice has been generated for your institution. Complete the prorated payment to unlock
                <strong>{{ subData.pending_invoice.target_plan?.max_schools }} campuses</strong> and advanced modules.
              </p>
              <div class="d-flex flex-wrap align-items-center gap-3 small text-dark">
                <span>Target Plan: <strong>₹{{ Number(subData.pending_invoice.target_plan_price).toLocaleString('en-IN') }}</strong></span>
                <span>•</span>
                <span class="text-success">Paid Plan Credit: <strong>-₹{{ Number(subData.pending_invoice.credit_amount).toLocaleString('en-IN') }}</strong></span>
                <span>•</span>
                <span class="fs-6 fw-bold text-primary">Net Payable: ₹{{ Number(subData.pending_invoice.net_amount).toLocaleString('en-IN') }}</span>
              </div>
            </div>

            <div class="text-md-end flex-shrink-0">
              <button
                class="btn btn-success btn-lg px-4 shadow-sm fw-bold"
                :disabled="paying"
                @click="openPaymentModal(subData.pending_invoice)"
              >
                <span v-if="paying" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-credit-card-2-front-fill me-2"></i>
                {{ paying ? 'Processing...' : 'Pay ₹' + Number(subData.pending_invoice.net_amount).toLocaleString('en-IN') + ' Now' }}
              </button>
              <small class="text-muted d-block mt-1">Instant plan & campus quota activation</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <!-- Current Active Plan Card -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white py-3 border-0">
              <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-primary-subtle text-primary text-uppercase px-3 py-2 rounded-pill fw-semibold">
                  <i class="bi bi-shield-check me-1"></i> Current Active Package
                </span>
                <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Active</span>
              </div>
            </div>
            <div class="card-body p-4 pt-0">
              <h2 class="fw-bold mb-1 text-dark">{{ subData.plan?.name || 'Starter / Basic Plan' }}</h2>
              <div class="fs-4 fw-bold text-primary mb-3">
                ₹{{ Number(subData.plan?.price || 2499).toLocaleString('en-IN') }}
                <span class="fs-6 text-muted fw-normal">/ year</span>
              </div>

              <!-- Campus Quota Usage -->
              <div class="bg-light rounded-3 p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-semibold text-dark">
                    <i class="bi bi-buildings text-primary me-1"></i> Multi-School Quota
                  </span>
                  <span class="fw-bold fs-6" :class="subData.owned_schools_count >= subData.max_schools ? 'text-warning' : 'text-success'">
                    {{ subData.owned_schools_count }} of {{ subData.max_schools }} Schools Used
                  </span>
                </div>
                <div class="progress mb-2" style="height: 10px;">
                  <div
                    class="progress-bar"
                    :class="subData.owned_schools_count >= subData.max_schools ? 'bg-warning' : 'bg-primary'"
                    :style="{ width: `${Math.min(100, (subData.owned_schools_count / subData.max_schools) * 100)}%` }"
                  ></div>
                </div>
                <small v-if="subData.can_add_more_schools" class="text-success fw-semibold">
                  <i class="bi bi-check-circle-fill me-1"></i> You can add {{ subData.max_schools - subData.owned_schools_count }} more school(s) under your current plan.
                </small>
                <small v-else class="text-danger fw-semibold">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i> Campus limit reached. Upgrade to unlock more schools.
                </small>
              </div>

              <!-- Registered Schools List -->
              <h6 class="fw-bold text-muted small text-uppercase mb-2">Schools Under Your Management:</h6>
              <ul class="list-group list-group-flush border rounded-3 mb-3">
                <li
                  v-for="s in subData.owned_schools"
                  :key="'owned-s-' + s.id"
                  class="list-group-item d-flex align-items-center justify-content-between py-2"
                >
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-building text-primary"></i>
                    <span class="fw-semibold">{{ s.name }}</span>
                    <small class="text-muted font-monospace">({{ s.code }})</small>
                  </div>
                  <span
                    v-if="String(s.id) === String(tenantStore.activeSchoolId)"
                    class="badge bg-success-subtle text-success border border-success"
                  >
                    Active Context
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Upgrade & Quotas Card -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white py-3 border-0">
              <h5 class="fw-bold mb-0">Plan Tiers & Multi-Campus Limits</h5>
            </div>
            <div class="card-body p-4 pt-0">
              <div class="d-flex flex-column gap-3">
                <!-- Tier 1: Basic -->
                <div
                  class="p-3 rounded-3 border"
                  :class="subData.plan?.slug === 'basic' ? 'border-primary bg-primary-subtle bg-opacity-10' : 'bg-light'"
                >
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0">Starter / Basic Plan</h6>
                    <span class="fw-bold">₹2,499 / yr</span>
                  </div>
                  <div class="small text-muted mb-2">Single Institution • Core ERP Modules</div>
                  <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-secondary">1 School Max</span>
                    <span v-if="subData.plan?.slug === 'basic'" class="badge bg-primary">Your Current Plan</span>
                  </div>
                </div>

                <!-- Tier 2: Professional -->
                <div
                  class="p-3 rounded-3 border"
                  :class="subData.plan?.slug === 'professional' ? 'border-primary bg-primary-subtle bg-opacity-10' : 'bg-light'"
                >
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0 text-success">Professional / Growth Plan</h6>
                    <span class="fw-bold text-success">₹4,999 / yr</span>
                  </div>
                  <div class="small text-muted mb-2">Up to 2 Campuses • Interactive Switcher • Library, Hostel & Transport</div>
                  <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-success">2 Schools Allowed</span>
                    <span v-if="subData.plan?.slug === 'professional'" class="badge bg-primary">Your Current Plan</span>
                    <span v-else-if="subData.plan?.slug === 'basic'" class="badge bg-warning text-dark">
                      Prorated Difference: ₹2,500
                    </span>
                  </div>
                </div>

                <!-- Tier 3: Enterprise -->
                <div
                  class="p-3 rounded-3 border"
                  :class="subData.plan?.slug === 'enterprise' ? 'border-primary bg-primary-subtle bg-opacity-10' : 'bg-light'"
                >
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold mb-0 text-primary">Enterprise / Multi-Branch Plan</h6>
                    <span class="fw-bold text-primary">₹9,999 / yr</span>
                  </div>
                  <div class="small text-muted mb-2">Up to 5 Campuses • Interactive Switcher • Custom Domain & Multi-Branch</div>
                  <div class="d-flex align-items-center gap-2 small">
                    <span class="badge bg-primary">5 Schools Allowed</span>
                    <span v-if="subData.plan?.slug === 'enterprise'" class="badge bg-primary">Your Current Plan</span>
                    <span v-else class="badge bg-warning text-dark">
                      Prorated Difference: {{ subData.plan?.slug === 'professional' ? '₹5,000' : '₹7,500' }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Request Upgrade Box -->
              <div class="mt-4 p-3 bg-light rounded-3 border text-start">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="bg-primary-subtle text-primary rounded-circle p-2 fs-5">
                    <i class="bi bi-headset"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-0 text-dark">Need More Campuses or Plan Upgrade?</h6>
                    <small class="text-muted">Directly contact platform administration or generate upgrade invoice instantly.</small>
                  </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                  <button
                    type="button"
                    class="btn btn-primary fw-semibold btn-sm shadow-sm flex-grow-1"
                    @click="openContactModal"
                  >
                    <i class="bi bi-arrow-up-circle-fill me-1"></i> Request Plan Upgrade
                  </button>
                  <a
                    :href="whatsappUrl"
                    target="_blank"
                    class="btn btn-outline-success btn-sm fw-semibold d-inline-flex align-items-center gap-1"
                    title="Chat with Super Admin on WhatsApp"
                  >
                    <i class="bi bi-whatsapp"></i> WhatsApp
                  </a>
                  <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm fw-semibold"
                    @click="copyAdminEmail"
                    title="Copy Super Admin Email Address"
                  >
                    <i class="bi bi-envelope me-1"></i> Copy Email
                  </button>
                  <a
                    href="tel:+919331805678"
                    class="btn btn-outline-secondary btn-sm fw-semibold"
                    title="Call Super Admin Support"
                  >
                    <i class="bi bi-telephone me-1"></i> Call
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Invoices History -->
      <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="fw-bold mb-0">Billing & Upgrade Invoice History</h5>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice #</th>
                <th>Type</th>
                <th>Target Plan</th>
                <th>Previous Credit</th>
                <th>Net Paid / Payable</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-end pe-3">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="inv in subData.invoices" :key="'my-inv-' + inv.id">
                <td>
                  <span class="font-monospace fw-bold">{{ inv.invoice_number }}</span>
                </td>
                <td>
                  <span class="badge bg-light text-muted border text-uppercase">{{ inv.type }}</span>
                </td>
                <td>
                  <strong>{{ inv.target_plan?.name }}</strong>
                </td>
                <td>
                  <span class="text-success small">-₹{{ Number(inv.credit_amount).toLocaleString('en-IN') }}</span>
                </td>
                <td>
                  <span class="fw-bold text-dark">₹{{ Number(inv.net_amount).toLocaleString('en-IN') }}</span>
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
                </td>
                <td>
                  <small class="text-muted">{{ formatDate(inv.created_at) }}</small>
                </td>
                <td class="text-end pe-3">
                  <button
                    v-if="inv.status === 'pending'"
                    class="btn btn-sm btn-success fw-bold"
                    @click="openPaymentModal(inv)"
                  >
                    <i class="bi bi-credit-card me-1"></i> Pay Now
                  </button>
                  <span v-else-if="inv.status === 'paid'" class="text-success small fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i> Paid
                  </span>
                </td>
              </tr>
              <tr v-if="!subData.invoices || subData.invoices.length === 0">
                <td colspan="8" class="text-center py-4 text-muted">No invoices found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- SECURE SUBSCRIPTION & UPGRADE PAYMENT GATEWAY MODAL      -->
    <!-- ======================================================== -->
    <div
      v-if="showPayModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.65); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          
          <!-- Modal Header -->
          <div class="modal-header bg-primary text-white px-4 py-3">
            <div class="d-flex align-items-center gap-2">
              <div class="bg-white text-primary rounded-circle p-2 fs-5 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-shield-check"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold mb-0">Subscription & Campus Upgrade Gateway</h5>
                <small class="text-white-50">Official 256-Bit SSL Encrypted Institutional SaaS Billing</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="showPayModal = false" :disabled="paying"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body p-4 bg-light">
            
            <!-- Invoice Summary Banner -->
            <div class="card border-0 rounded-3 shadow-sm mb-4 bg-white">
              <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                  <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <span class="badge bg-warning text-dark text-uppercase fw-bold px-2 py-1">Pending Invoice</span>
                      <span class="font-monospace fw-bold text-dark">{{ activeInvoice?.invoice_number }}</span>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">
                      {{ activeInvoice?.target_plan?.name || 'Plan Upgrade' }}
                    </h5>
                    <small class="text-muted">
                      Unlocks up to <strong>{{ activeInvoice?.target_plan?.max_schools }} Campuses</strong> and advanced ERP features.
                    </small>
                  </div>
                  <div class="text-md-end bg-light p-2 rounded border">
                    <small class="text-muted d-block">Net Payable Amount</small>
                    <h3 class="fw-bold text-primary mb-0">₹{{ Number(activeInvoice?.net_amount || 0).toLocaleString('en-IN') }}</h3>
                  </div>
                </div>

                <div v-if="Number(activeInvoice?.credit_amount) > 0" class="d-flex align-items-center gap-2 mt-2 pt-2 border-top text-muted small">
                  <span>Target Plan: ₹{{ Number(activeInvoice?.target_plan_price).toLocaleString('en-IN') }}</span>
                  <span>•</span>
                  <span class="text-success fw-semibold">Paid Plan Credit: -₹{{ Number(activeInvoice?.credit_amount).toLocaleString('en-IN') }}</span>
                </div>
              </div>
            </div>

            <!-- Payment Methods Nav Tabs -->
            <ul class="nav nav-pills nav-fill mb-3 gap-2 bg-white p-2 rounded-3 border shadow-sm">
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border"
                  :class="{ active: payMethod === 'upi', 'bg-primary text-white': payMethod === 'upi' }"
                  @click="payMethod = 'upi'"
                >
                  <i class="bi bi-qr-code fs-5"></i>
                  <span>UPI & Dynamic QR</span>
                </button>
              </li>
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border"
                  :class="{ active: payMethod === 'card', 'bg-primary text-white': payMethod === 'card' }"
                  @click="payMethod = 'card'"
                >
                  <i class="bi bi-credit-card-2-front fs-5"></i>
                  <span>Debit / Credit Card</span>
                </button>
              </li>
              <li class="nav-item">
                <button
                  type="button"
                  class="nav-link py-2 rounded-3 fw-semibold d-flex align-items-center justify-content-center gap-2 border"
                  :class="{ active: payMethod === 'net_banking', 'bg-primary text-white': payMethod === 'net_banking' }"
                  @click="payMethod = 'net_banking'"
                >
                  <i class="bi bi-bank fs-5"></i>
                  <span>Bank Wire / NEFT</span>
                </button>
              </li>
            </ul>

            <!-- METHOD 1: Dynamic UPI & QR Code -->
            <div v-if="payMethod === 'upi'" class="card border-0 shadow-sm rounded-3 bg-white p-3">
              <div class="row align-items-center g-3">
                <div class="col-md-5 text-center border-end-md pb-3 pb-md-0">
                  <div class="p-2 border rounded-3 d-inline-block bg-white shadow-sm mb-2">
                    <img
                      :src="upiQrUrl"
                      alt="UPI QR Code"
                      style="width: 180px; height: 180px; object-fit: contain;"
                      class="img-fluid"
                    />
                  </div>
                  <div class="small fw-semibold text-dark">Scan to Pay with Any UPI App</div>
                  <div class="d-flex justify-content-center flex-wrap gap-1 mt-2">
                    <span class="badge bg-light text-dark border">GPay</span>
                    <span class="badge bg-light text-dark border">PhonePe</span>
                    <span class="badge bg-light text-dark border">Paytm</span>
                    <span class="badge bg-light text-dark border">BHIM</span>
                    <span class="badge bg-light text-dark border">Cred</span>
                  </div>
                </div>

                <div class="col-md-7 ps-md-4">
                  <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-phone text-primary me-1"></i> Pay via UPI ID / VPA
                  </h6>

                  <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold">Official Super Admin Platform UPI VPA</label>
                    <div class="input-group input-group-sm">
                      <input type="text" class="form-control font-monospace" readonly :value="platformUpiId" />
                      <button class="btn btn-outline-secondary" type="button" @click="copyUpiId">
                        <i class="bi bi-clipboard me-1"></i> Copy
                      </button>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold">Your UPI ID / Mobile Number (Optional)</label>
                    <input
                      type="text"
                      class="form-control form-control-sm"
                      placeholder="e.g. schooladmin@okaxis or 9876543210@paytm"
                      v-model="payForm.upi_id"
                    />
                  </div>

                  <div class="mb-2">
                    <label class="form-label small text-muted fw-semibold">UPI UTR / Reference No. (Optional)</label>
                    <input
                      type="text"
                      class="form-control form-control-sm font-monospace"
                      placeholder="e.g. 432109876543"
                      v-model="payForm.transaction_ref"
                    />
                    <small class="text-muted" style="font-size: 0.72rem;">Enter the 12-digit UPI reference number after scanning.</small>
                  </div>
                </div>
              </div>
            </div>

            <!-- METHOD 2: Credit / Debit Card -->
            <div v-if="payMethod === 'card'" class="card border-0 shadow-sm rounded-3 bg-white p-3">
              <h6 class="fw-bold text-dark mb-3">
                <i class="bi bi-credit-card text-primary me-1"></i> Enter Card Details
              </h6>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small text-muted fw-semibold">Card Number</label>
                  <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                    <input
                      type="text"
                      class="form-control font-monospace"
                      placeholder="4532 •••• •••• 8890"
                      v-model="payForm.card_number"
                      maxlength="19"
                    />
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label small text-muted fw-semibold">Cardholder Name</label>
                  <input
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="e.g. IFAT FATMA"
                    v-model="payForm.card_name"
                  />
                </div>
                <div class="col-6">
                  <label class="form-label small text-muted fw-semibold">Expiry Date</label>
                  <input
                    type="text"
                    class="form-control form-control-sm font-monospace"
                    placeholder="MM/YY"
                    v-model="payForm.card_expiry"
                    maxlength="5"
                  />
                </div>
                <div class="col-6">
                  <label class="form-label small text-muted fw-semibold">CVV</label>
                  <input
                    type="password"
                    class="form-control form-control-sm font-monospace"
                    placeholder="•••"
                    v-model="payForm.card_cvv"
                    maxlength="4"
                  />
                </div>
              </div>
              <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top text-muted small">
                <i class="bi bi-shield-lock-fill text-success"></i>
                <span style="font-size: 0.75rem;">Supported cards: Visa, MasterCard, RuPay, Maestro. Secured by 256-bit encryption.</span>
              </div>
            </div>

            <!-- METHOD 3: Net Banking / Bank Wire -->
            <div v-if="payMethod === 'net_banking'" class="card border-0 shadow-sm rounded-3 bg-white p-3">
              <h6 class="fw-bold text-dark mb-2">
                <i class="bi bi-bank text-primary me-1"></i> Official Platform Bank Wire Details
              </h6>
              <p class="text-muted small mb-3">
                Transfer via IMPS / NEFT / RTGS directly to the Super Admin platform account:
              </p>

              <div class="p-3 bg-light rounded border mb-3 small">
                <div class="row g-2">
                  <div class="col-sm-6">
                    <span class="text-muted d-block">Account Name:</span>
                    <strong>School ERP Cloud Technologies Pvt Ltd</strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block">Bank Name:</span>
                    <strong>HDFC Bank Ltd</strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block">Account Number:</span>
                    <strong class="font-monospace text-primary fs-6">50200098765432</strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block">IFSC Code:</span>
                    <strong class="font-monospace text-primary fs-6">HDFC0001234</strong>
                  </div>
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label small text-muted fw-semibold">Bank UTR / Transaction Reference No.</label>
                <input
                  type="text"
                  class="form-control form-control-sm font-monospace"
                  placeholder="e.g. HDFC20261119001928"
                  v-model="payForm.transaction_ref"
                />
              </div>
            </div>

          </div>

          <!-- Modal Footer -->
          <div class="modal-footer bg-white px-4 py-3 border-top d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" @click="showPayModal = false" :disabled="paying">
              Cancel
            </button>
            <button
              type="button"
              class="btn btn-success btn-lg px-4 fw-bold shadow-sm"
              :disabled="paying"
              @click="submitInvoicePayment"
            >
              <span v-if="paying" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="bi bi-check-circle-fill me-2"></i>
              {{ paying ? 'Processing & Activating...' : 'Confirm & Pay ₹' + Number(activeInvoice?.net_amount || 0).toLocaleString('en-IN') + ' Now' }}
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- ======================================================== -->
    <!-- CONTACT SUPER ADMIN & PLAN UPGRADE REQUEST MODAL         -->
    <!-- ======================================================== -->
    <div
      v-if="showContactModal"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.65); z-index: 1055;"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          
          <!-- Modal Header -->
          <div class="modal-header bg-primary text-white px-4 py-3">
            <div class="d-flex align-items-center gap-2">
              <div class="bg-white text-primary rounded-circle p-2 fs-5 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                <i class="bi bi-headset"></i>
              </div>
              <div>
                <h5 class="modal-title fw-bold mb-0">Contact Super Admin & Upgrade Plan</h5>
                <small class="text-white-50">Direct assistance, custom campus requests, and instant invoice generation</small>
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" @click="showContactModal = false" :disabled="requestingUpgrade"></button>
          </div>

          <!-- Modal Body -->
          <div class="modal-body p-4 bg-light">
            <div class="row g-4">
              <!-- Left Column: Self-Service Invoice Generation -->
              <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                  <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i>
                    Instant Plan Upgrade
                  </h6>
                  <p class="text-muted small mb-3">
                    Select your target tier below to generate an upgrade invoice with prorated credit applied automatically:
                  </p>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Select Target Package</label>
                    <select class="form-select form-select-sm" v-model="targetUpgradePlanId">
                      <option
                        v-for="p in subData.available_plans"
                        :key="'avail-p-' + p.id"
                        :value="p.id"
                      >
                        {{ p.name }} (Max: {{ p.max_schools }} Campuses) — ₹{{ Number(p.price).toLocaleString('en-IN') }}/yr
                        {{ p.slug === subData.plan?.slug ? '(Current Plan)' : (p.max_schools > subData.max_schools ? '(Upgrade)' : '') }}
                      </option>
                    </select>
                  </div>

                  <!-- Price breakdown -->
                  <div v-if="selectedTargetPlan" class="p-2 bg-light rounded border mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                      <span>Target Plan:</span>
                      <strong>₹{{ Number(selectedTargetPlan.price).toLocaleString('en-IN') }}</strong>
                    </div>
                    <div v-if="selectedTargetPlan.id !== subData.plan?.id" class="d-flex justify-content-between mb-1 text-success">
                      <span>Paid Plan Credit:</span>
                      <span>-₹{{ Number(subData.plan?.price || 2499).toLocaleString('en-IN') }}</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold text-primary">
                      <span>Net Payable:</span>
                      <span>₹{{ Number(calculatedUpgradeNet).toLocaleString('en-IN') }}</span>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Notes / Requirements (Optional)</label>
                    <textarea
                      class="form-control form-control-sm"
                      rows="2"
                      placeholder="e.g. Need to register our new campus branch in Kolkata..."
                      v-model="upgradeNotes"
                    ></textarea>
                  </div>

                  <button
                    type="button"
                    class="btn btn-success btn-sm w-100 fw-bold py-2 shadow-sm"
                    :disabled="requestingUpgrade || !targetUpgradePlanId"
                    @click="submitSelfUpgradeRequest"
                  >
                    <span v-if="requestingUpgrade" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bi bi-file-earmark-check-fill me-1"></i>
                    {{ requestingUpgrade ? 'Generating...' : 'Generate Invoice & Proceed to Pay' }}
                  </button>
                </div>
              </div>

              <!-- Right Column: Official Support Channels -->
              <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                  <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-person-lines-fill text-primary me-1"></i>
                    Super Admin Contact
                  </h6>
                  <p class="text-muted small mb-3">
                    Connect directly with the platform administrator for custom deals, multi-campus onboarding, or billing questions.
                  </p>

                  <div class="d-grid gap-2">
                    <!-- WhatsApp Link -->
                    <a
                      :href="whatsappUrl"
                      target="_blank"
                      class="btn btn-outline-success btn-sm py-2 text-start d-flex align-items-center gap-2 fw-semibold"
                    >
                      <i class="bi bi-whatsapp fs-5 text-success"></i>
                      <div>
                        <div>Chat on WhatsApp</div>
                        <small class="text-muted fw-normal" style="font-size: 0.72rem;">Instant messaging & support</small>
                      </div>
                    </a>

                    <!-- Direct Call -->
                    <a
                      href="tel:+919331805678"
                      class="btn btn-outline-primary btn-sm py-2 text-start d-flex align-items-center gap-2 fw-semibold"
                    >
                      <i class="bi bi-telephone fs-5 text-primary"></i>
                      <div>
                        <div>+91 93318 05678</div>
                        <small class="text-muted fw-normal" style="font-size: 0.72rem;">Direct phone line</small>
                      </div>
                    </a>

                    <!-- Email Copy -->
                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm py-2 text-start d-flex align-items-center gap-2 fw-semibold"
                      @click="copyAdminEmail"
                    >
                      <i class="bi bi-envelope fs-5 text-secondary"></i>
                      <div class="flex-grow-1 text-truncate">
                        <div class="text-truncate">{{ superAdminEmail }}</div>
                        <small class="text-muted fw-normal" style="font-size: 0.72rem;">Click to copy email address</small>
                      </div>
                      <i class="bi bi-clipboard text-muted"></i>
                    </button>
                  </div>

                  <div class="mt-3 p-2 bg-light rounded border text-muted small" style="font-size: 0.75rem;">
                    <i class="bi bi-clock me-1 text-primary"></i>
                    <strong>Support Hours:</strong> Mon - Sat, 9:00 AM - 7:00 PM IST.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="modal-footer bg-white px-4 py-2 border-top">
            <button type="button" class="btn btn-secondary btn-sm px-3" @click="showContactModal = false">
              Close
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppBreadcrumb from '@/components/ui/AppBreadcrumb.vue';
import TenantBilling from '@/pages/administrator/billing/TenantBilling.vue';
import api from '@/api/axios';
import Swal from 'sweetalert2';
import { useTenantStore } from '@/stores/tenant';
import { useAuthStore } from '@/stores/auth';

const tenantStore = useTenantStore();
const authStore = useAuthStore();
const loading = ref(false);
const paying = ref(false);
const showPayModal = ref(false);
const activeInvoice = ref(null);
const payMethod = ref('upi');
const platformUpiId = ref('superadmin.schoolerp@icici');
const payForm = ref({
  upi_id: '',
  transaction_ref: '',
  card_number: '',
  card_name: '',
  card_expiry: '',
  card_cvv: '',
});

const upiQrUrl = computed(() => {
  const amt = Number(activeInvoice.value?.net_amount || 0).toFixed(2);
  const vpa = platformUpiId.value;
  const encoded = encodeURIComponent(`upi://pay?pa=${vpa}&pn=SchoolERP Platform&am=${amt}&cu=INR`);
  return `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encoded}`;
});

const copyUpiId = () => {
  navigator.clipboard.writeText(platformUpiId.value);
  Swal.fire({
    icon: 'success',
    title: 'UPI ID Copied!',
    text: platformUpiId.value,
    timer: 1500,
    showConfirmButton: false,
  });
};

const subData = ref({
  plan: null,
  max_schools: 1,
  owned_schools_count: 1,
  can_add_more_schools: false,
  owned_schools: [],
  pending_invoice: null,
  invoices: [],
  available_plans: [],
});

const isSuperAdmin = computed(() => {
  const u = authStore.user;
  if (!u) return !!subData.value?.is_super_admin;
  if (u.user_type === 'super_admin') return true;
  if (u.roles && Array.isArray(u.roles)) {
    return u.roles.some(r => {
      const roleName = typeof r === 'string' ? r : (r.name || '');
      return roleName.toLowerCase() === 'super admin' || roleName.toLowerCase() === 'super_admin';
    });
  }
  return !!subData.value?.is_super_admin;
});

const loadSubscription = async () => {
  if (!authStore.user && authStore.token) {
    try {
      await authStore.loadProfile();
    } catch (e) {
      console.warn('Could not load profile:', e);
    }
  }

  if (isSuperAdmin.value) {
    loading.value = false;
    return;
  }

  loading.value = true;
  try {
    const res = await api.get('/billing/my-subscription');
    subData.value = res.data.data;
  } catch (err) {
    console.error('Failed loading subscription data:', err);
  } finally {
    loading.value = false;
  }
};

const openPaymentModal = (invoice) => {
  activeInvoice.value = invoice;
  payMethod.value = 'upi';
  payForm.value = {
    upi_id: authStore.user?.email ? authStore.user.email.split('@')[0] + '@upi' : '',
    transaction_ref: 'TXN-' + Math.floor(1000000000 + Math.random() * 9000000000),
    card_number: '',
    card_name: authStore.user?.name || '',
    card_expiry: '',
    card_cvv: '',
  };
  showPayModal.value = true;
};

const submitInvoicePayment = async () => {
  if (!activeInvoice.value) return;

  paying.value = true;
  try {
    const payload = {
      payment_method: payMethod.value,
      transaction_ref: payForm.value.transaction_ref,
      upi_id: payMethod.value === 'upi' ? payForm.value.upi_id : null,
    };

    const res = await api.post(`/billing/invoices/${activeInvoice.value.id}/pay`, payload);

    showPayModal.value = false;

    // Reload profile & tenant state so navbar switcher activates immediately!
    await authStore.loadProfile();
    await tenantStore.fetchTenants();
    await tenantStore.fetchCurrent();

    await Swal.fire({
      icon: 'success',
      title: 'Upgrade & Payment Successful!',
      html: `
        <div class="text-start p-3 bg-light rounded border mb-2 small">
          <div class="d-flex justify-content-between mb-1">
            <span>Invoice:</span>
            <strong>${activeInvoice.value.invoice_number}</strong>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span>Payment Method:</span>
            <span class="text-uppercase fw-semibold text-primary">${payMethod.value}</span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span>Amount Paid:</span>
            <strong class="text-success">₹${Number(activeInvoice.value.net_amount).toLocaleString('en-IN')}</strong>
          </div>
          <div class="d-flex justify-content-between">
            <span>Active Tier:</span>
            <span class="badge bg-primary text-uppercase">${activeInvoice.value.target_plan?.name}</span>
          </div>
        </div>
        <p class="text-success fw-semibold mb-0">Multi-campus switcher and school registration privileges have been unlocked!</p>
      `,
    });

    await loadSubscription();
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Payment Failed',
      text: err.response?.data?.message || 'Error occurred while processing payment.',
    });
  } finally {
    paying.value = false;
  }
};

const showContactModal = ref(false);
const requestingUpgrade = ref(false);
const superAdminEmail = ref('admin@school.com');
const superAdminPhone = ref('919331805678');
const targetUpgradePlanId = ref('');
const upgradeNotes = ref('');

const selectedTargetPlan = computed(() => {
  return subData.value.available_plans?.find(p => p.id === Number(targetUpgradePlanId.value));
});

const calculatedUpgradeNet = computed(() => {
  if (!selectedTargetPlan.value) return 0;
  const currentPrice = Number(subData.value.plan?.price || 2499);
  const targetPrice = Number(selectedTargetPlan.value.price);
  if (selectedTargetPlan.value.id === subData.value.plan?.id) {
    return targetPrice;
  }
  return Math.max(0, targetPrice - currentPrice);
});

const whatsappUrl = computed(() => {
  const schoolName = authStore.user?.school?.name || 'our school';
  const text = encodeURIComponent(`Hello Super Admin, I am from ${schoolName} (Admin: ${authStore.user?.name || ''}). I want to request a plan upgrade for multi-school campus management.`);
  return `https://wa.me/${superAdminPhone.value}?text=${text}`;
});

const copyAdminEmail = () => {
  navigator.clipboard.writeText(superAdminEmail.value);
  Swal.fire({
    icon: 'success',
    title: 'Email Copied!',
    text: superAdminEmail.value,
    timer: 1500,
    showConfirmButton: false,
  });
};

const openContactModal = () => {
  const higher = subData.value.available_plans?.filter(p => p.max_schools > subData.value.max_schools) || [];
  targetUpgradePlanId.value = higher[0]?.id || subData.value.available_plans?.[0]?.id || '';
  upgradeNotes.value = '';
  showContactModal.value = true;
};

const submitSelfUpgradeRequest = async () => {
  if (!targetUpgradePlanId.value) return;

  requestingUpgrade.value = true;
  try {
    const res = await api.post('/billing/request-upgrade', {
      target_plan_id: targetUpgradePlanId.value,
      notes: upgradeNotes.value,
    });

    showContactModal.value = false;

    // Reload subscription so the pending invoice banner appears immediately!
    await loadSubscription();

    // Immediately open the payment modal with the newly generated invoice!
    if (res.data.data) {
      openPaymentModal(res.data.data);
    }
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Upgrade Request Failed',
      text: err.response?.data?.message || 'Error occurred while creating upgrade request.',
    });
  } finally {
    requestingUpgrade.value = false;
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

onMounted(loadSubscription);
</script>
