<template>
  <div>
    <!-- Page Header -->
    <div class="mb-4">
      <h4 class="fw-bold mb-1">Academic Performance & Report Cards</h4>
      <small class="text-muted">Review subject-wise examination scores, grading assessments, and download official term report cards</small>
    </div>

    <!-- Exam Performance Cards -->
    <div v-if="examGroups.length > 0" class="row g-3 mb-4">
      <div v-for="ex in examGroups" :key="ex.exam_id" class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 position-relative overflow-hidden">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small mb-1">
                Term Examination
              </span>
              <h5 class="fw-bold text-dark mb-0">{{ ex.exam_name }}</h5>
            </div>
            <span
              class="badge rounded-pill px-3 py-1"
              :class="ex.percentage >= 40 ? 'bg-success text-white' : 'bg-danger text-white'"
            >
              {{ ex.percentage >= 40 ? 'PASSED' : 'NEEDS IMPROVEMENT' }}
            </span>
          </div>

          <div class="my-3 py-2 border-top border-bottom">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted small">Total Marks Obtained:</span>
              <strong class="text-dark">{{ ex.total_obtained }} / {{ ex.total_max }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="text-muted small">Score Percentage:</span>
              <strong class="text-primary">{{ ex.percentage }}%</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small">Subjects Evaluated:</span>
              <span class="badge bg-light text-dark">{{ ex.subjects_count }} Subjects</span>
            </div>
          </div>

          <button
            class="btn btn-outline-primary rounded-pill w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 mt-auto"
            @click="openReportCard(ex.exam_id)"
          >
            <i class="bi bi-file-earmark-pdf-fill"></i>
            <span>View & Download Report Card</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Marks Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
      <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0">Detailed Subject-wise Assessment Marks</h6>
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
              <th>Total Marks Scored</th>
              <th>Status</th>
              <th class="pe-4">Teacher's Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="marks.length === 0">
              <td colspan="7" class="text-center py-5 text-muted small">
                <i class="bi bi-mortarboard text-muted fs-1 d-block mb-2"></i>
                No examination marks published yet for this student.
              </td>
            </tr>
            <tr v-for="m in marks" :key="m.id">
              <td class="ps-4 fw-semibold text-primary">{{ m.exam_schedule?.exam?.name || 'Academic Assessment' }}</td>
              <td class="fw-semibold">{{ m.exam_schedule?.subject?.name || 'Subject' }}</td>
              <td>{{ m.theory_marks ?? '-' }}</td>
              <td>{{ (Number(m.practical_marks || 0) + Number(m.viva_marks || 0)) || '-' }}</td>
              <td class="fw-bold text-success">{{ m.total_marks ?? '-' }}</td>
              <td>
                <span class="badge rounded-pill" :class="m.attendance === 'absent' ? 'bg-danger' : 'bg-success'">
                  {{ m.attendance === 'absent' ? 'Absent' : 'Present' }}
                </span>
              </td>
              <td class="pe-4 small text-muted">{{ m.remarks || 'Satisfactory Performance' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- OFFICIAL REPORT CARD MODAL                 -->
    <!-- ========================================== -->
    <div
      v-if="showReportModal && reportData"
      class="modal fade show d-block"
      tabindex="-1"
      style="background: rgba(0, 0, 0, 0.7); z-index: 1060;"
    >
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
          <div class="modal-header bg-light border-0 px-4 pt-3 pb-0 d-flex justify-content-between">
            <h6 class="fw-bold text-muted mb-0">Student Progress & Annual Performance Report</h6>
            <button type="button" class="btn-close" @click="showReportModal = false"></button>
          </div>

          <div class="modal-body p-4" id="printableReportCard">
            <!-- School Header -->
            <div class="border-bottom pb-3 mb-3 text-center position-relative">
              <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
                <img
                  v-if="reportData.school?.logo"
                  :src="reportData.school.logo"
                  alt="School Crest"
                  style="max-height: 60px; max-width: 65px; object-fit: contain;"
                />
                <div class="text-center">
                  <h3 class="fw-bold mb-0 text-dark">{{ reportData.school?.name || 'School ERP Academy' }}</h3>
                  <small class="text-muted d-block">{{ reportData.school?.address || 'Affiliated to State / Central Board of Secondary Education' }}</small>
                  <small class="text-muted">
                    Phone: {{ reportData.school?.phone || 'N/A' }} | Email: {{ reportData.school?.email || 'office@school.edu' }}
                  </small>
                </div>
              </div>
              <div class="badge bg-primary px-4 py-1 text-uppercase mt-2 fs-6" style="letter-spacing: 1.5px;">
                {{ reportData.exam?.name || 'Annual Examination' }} - Performance Report
              </div>
            </div>

            <!-- Student Bio Information Grid -->
            <div class="bg-light rounded-3 p-3 mb-3 border">
              <div class="row g-2 small">
                <div class="col-sm-4">
                  <div><strong>Student Name:</strong> <span class="fw-bold text-primary">{{ reportData.student?.full_name }}</span></div>
                  <div><strong>Admission No:</strong> <span class="font-monospace fw-semibold">{{ reportData.student?.admission_no }}</span></div>
                  <div><strong>Roll Number:</strong> #{{ reportData.student?.roll_no || '1' }}</div>
                </div>
                <div class="col-sm-4">
                  <div><strong>Class:</strong> {{ reportData.student?.class_name }}</div>
                  <div><strong>Section:</strong> {{ reportData.student?.section_name || 'A' }}</div>
                  <div><strong>Date of Birth:</strong> {{ reportData.student?.dob || 'N/A' }}</div>
                </div>
                <div class="col-sm-4 text-sm-end">
                  <div><strong>Parent/Guardian:</strong> {{ reportData.student?.father_name || 'N/A' }}</div>
                  <div><strong>Academic Session:</strong> 2026 - 2027</div>
                  <div><strong>Report Issue Date:</strong> {{ reportData.issue_date }}</div>
                </div>
              </div>
            </div>

            <!-- Subject Assessment Table -->
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="table-light small">
                  <tr>
                    <th class="text-start ps-3" style="width: 220px;">Subject Name</th>
                    <th>Max Marks</th>
                    <th>Theory</th>
                    <th>Practical / Viva</th>
                    <th>Total Obtained</th>
                    <th>Grade</th>
                    <th>Grade Point</th>
                    <th class="text-start pe-3">Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(sub, idx) in reportData.subjects" :key="idx">
                    <td class="text-start ps-3 fw-semibold text-dark">{{ sub.subject_name }}</td>
                    <td>{{ sub.max_marks }}</td>
                    <td>{{ sub.theory_marks ?? '-' }}</td>
                    <td>{{ (Number(sub.practical_marks || 0) + Number(sub.viva_marks || 0)) || '-' }}</td>
                    <td class="fw-bold text-primary">{{ sub.obtained_marks }}</td>
                    <td><span class="badge bg-secondary">{{ sub.grade }}</span></td>
                    <td>{{ sub.grade_point }}</td>
                    <td class="text-start pe-3 small text-muted">{{ sub.remarks || 'Good Effort' }}</td>
                  </tr>
                </tbody>
                <tfoot class="table-light fw-bold">
                  <tr>
                    <td class="text-start ps-3">Grand Total:</td>
                    <td>{{ reportData.summary?.max_marks }}</td>
                    <td colspan="2">-</td>
                    <td class="text-primary fs-6">{{ reportData.summary?.total_marks }}</td>
                    <td colspan="3" class="text-end pe-3">
                      Overall Score: <span class="text-primary fs-6">{{ reportData.summary?.percentage }}%</span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <!-- Consolidated Summary & Key Performance Indicators -->
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                  <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Overall Grade</small>
                  <h4 class="fw-bold text-success mb-0">{{ reportData.summary?.overall_grade || 'A' }}</h4>
                  <small class="text-muted" style="font-size: 0.72rem;">{{ reportData.summary?.remarks }}</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                  <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Term Attendance</small>
                  <h4 class="fw-bold text-primary mb-0">{{ reportData.summary?.attendance_percentage }}%</h4>
                  <small class="text-muted" style="font-size: 0.72rem;">Working Days Evaluated</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                  <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Class Rank</small>
                  <h4 class="fw-bold text-warning mb-0">{{ reportData.summary?.rank || 'Top 10%' }}</h4>
                  <small class="text-muted" style="font-size: 0.72rem;">Class Position</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded-3 p-2 text-center bg-white shadow-sm">
                  <small class="text-muted text-uppercase d-block" style="font-size: 0.7rem;">Final Status</small>
                  <h5 class="fw-bold text-success mb-0 mt-1">{{ reportData.summary?.result_status || 'PASSED' }}</h5>
                  <small class="text-success" style="font-size: 0.72rem;">Promoted to Next Grade</small>
                </div>
              </div>
            </div>

            <!-- Grading Scale Legend -->
            <div class="border rounded-3 p-2 bg-light mb-3">
              <small class="fw-bold text-muted d-block mb-1" style="font-size: 0.7rem;">GRADING SCALE SYSTEM:</small>
              <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.72rem;">
                <span><strong>A+</strong> (90-100% Outstanding)</span> &bull;
                <span><strong>A</strong> (80-89% Excellent)</span> &bull;
                <span><strong>B+</strong> (70-79% Very Good)</span> &bull;
                <span><strong>B</strong> (60-69% Good)</span> &bull;
                <span><strong>C</strong> (50-59% Satisfactory)</span> &bull;
                <span><strong>D</strong> (35-49% Pass)</span> &bull;
                <span><strong>F</strong> (Below 35% Needs Improvement)</span>
              </div>
            </div>

            <!-- Signatures Section -->
            <div class="row pt-4 text-center mt-3">
              <div class="col-4">
                <div class="border-top pt-2">
                  <span class="fw-bold small text-dark d-block">Class Teacher</span>
                  <small class="text-muted" style="font-size: 0.7rem;">Signature & Date</small>
                </div>
              </div>
              <div class="col-4">
                <div class="border-top pt-2">
                  <span class="fw-bold small text-dark d-block">Controller of Examinations</span>
                  <small class="text-muted" style="font-size: 0.7rem;">Signature & Date</small>
                </div>
              </div>
              <div class="col-4">
                <div class="border-top pt-2">
                  <span class="fw-bold small text-dark d-block">Principal / Headmaster</span>
                  <small class="text-muted" style="font-size: 0.7rem;">Official Seal & Signature</small>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light p-3 d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3" @click="showReportModal = false">
              Close
            </button>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" @click="printReportCard">
              <i class="bi bi-printer-fill me-1"></i> Print / Download Report Card
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const marks = ref([])
const examGroups = ref([])

// Report Card State
const showReportModal = ref(false)
const reportData = ref(null)

function getAuthConfig() {
  const token = localStorage.getItem('token')
  return { headers: { Authorization: `Bearer ${token}` } }
}

async function loadResults() {
  try {
    const res = await axios.get('/api/portal/results', getAuthConfig())
    if (res.data.success) {
      marks.value = res.data.data.marks || []
      examGroups.value = res.data.data.exam_groups || []
    }
  } catch (err) {
    console.error('Failed to load portal results', err)
  }
}

async function openReportCard(examId) {
  try {
    const res = await axios.get(`/api/portal/results/report-card/${examId}`, getAuthConfig())
    if (res.data.success) {
      reportData.value = res.data.data
      showReportModal.value = true
    }
  } catch (err) {
    console.error('Failed to load report card', err)
    Swal.fire({
      icon: 'error',
      title: 'Report Unavailable',
      text: 'Unable to generate report card for this examination.',
    })
  }
}

function printReportCard() {
  const printContent = document.getElementById('printableReportCard')
  if (!printContent) return

  const printableHtml = printContent.innerHTML

  const printWindow = window.open('', '_blank', 'height=800,width=950')
  printWindow.document.write(`
    <html>
      <head>
        <title>Report Card - ${reportData.value?.student?.full_name || 'Official'}</title>
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
  loadResults()
}

onMounted(() => {
  loadResults()
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