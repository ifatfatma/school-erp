<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Library Books & Circulation</h4>
      <small class="text-muted">Currently issued books, due dates, and past return history</small>
    </div>

    <!-- Active Issues Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
      <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-book-half me-2"></i> Currently Issued Books</h6>
        <span class="badge bg-primary rounded-pill px-3">{{ activeIssues.length }} Issued</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Book Title</th>
              <th>Barcode / Accession</th>
              <th>Issue Date</th>
              <th>Due Date</th>
              <th class="pe-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="activeIssues.length === 0">
              <td colspan="5" class="text-center py-4 text-muted small">
                You do not have any borrowed books currently.
              </td>
            </tr>
            <tr v-for="issue in activeIssues" :key="issue.id">
              <td class="ps-4 fw-semibold text-dark">{{ issue.book?.title || 'Book Title' }}</td>
              <td class="small font-monospace">{{ issue.book_copy?.barcode || issue.book_copy?.accession_no || '-' }}</td>
              <td class="small">{{ issue.issue_date }}</td>
              <td class="small fw-semibold text-danger">{{ issue.due_date }}</td>
              <td class="pe-4">
                <span class="badge bg-warning text-dark rounded-pill">Issued</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Return History Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0">
        <h6 class="fw-bold mb-0 text-muted"><i class="bi bi-clock-history me-2"></i> Borrowing History</h6>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Book Title</th>
              <th>Issue Date</th>
              <th>Return Date</th>
              <th class="pe-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="history.length === 0">
              <td colspan="4" class="text-center py-4 text-muted small">No past borrowing history.</td>
            </tr>
            <tr v-for="h in history" :key="h.id">
              <td class="ps-4 fw-semibold">{{ h.book?.title || 'Book Title' }}</td>
              <td class="small">{{ h.issue_date }}</td>
              <td class="small text-success">{{ h.return_date || '-' }}</td>
              <td class="pe-4">
                <span class="badge bg-success rounded-pill text-capitalize">{{ h.status }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const activeIssues = ref([])
const history = ref([])

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadLibrary() {
  try {
    const res = await axios.get('/api/portal/library', getAuthConfig())
    if (res.data.success) {
      activeIssues.value = res.data.data.active_issues || []
      history.value = res.data.data.history || []
    }
  } catch (err) {
    console.error('Failed to load portal library', err)
  }
}

onMounted(() => {
  loadLibrary()
})
</script>
