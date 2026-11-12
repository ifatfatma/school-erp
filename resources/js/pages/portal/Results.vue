<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Examination Results & Grades</h4>
      <small class="text-muted">Assessment marks, subjects evaluation, and published report cards</small>
    </div>

    <!-- Marks Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Evaluated Subject Marks</h6>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3">Session 2026-27</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Examination</th>
              <th>Subject</th>
              <th>Theory Marks</th>
              <th>Practical / Viva</th>
              <th>Total Marks</th>
              <th>Attendance</th>
              <th class="pe-4">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="marks.length === 0">
              <td colspan="7" class="text-center py-5 text-muted small">
                No examination marks published yet for this session.
              </td>
            </tr>
            <tr v-for="m in marks" :key="m.id">
              <td class="ps-4 fw-semibold text-primary">{{ m.exam_schedule?.exam?.name || 'Unit Assessment' }}</td>
              <td class="fw-semibold">{{ m.exam_schedule?.subject?.name || 'Subject' }}</td>
              <td>{{ m.theory_marks ?? '-' }}</td>
              <td>{{ (Number(m.practical_marks || 0) + Number(m.viva_marks || 0)) || '-' }}</td>
              <td class="fw-bold text-success">{{ m.total_marks ?? '-' }}</td>
              <td>
                <span class="badge rounded-pill" :class="m.attendance === 'present' ? 'bg-success' : 'bg-danger'">
                  {{ m.attendance || 'Present' }}
                </span>
              </td>
              <td class="pe-4 small text-muted">{{ m.remarks || 'Good Effort' }}</td>
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

const marks = ref([])

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadResults() {
  try {
    const res = await axios.get('/api/portal/results', getAuthConfig())
    if (res.data.success) {
      marks.value = res.data.data.marks || []
    }
  } catch (err) {
    console.error('Failed to load portal results', err)
  }
}

onMounted(() => {
  loadResults()
})
</script>
