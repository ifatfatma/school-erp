<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Attendance</h4>
            <small class="text-muted">Daily night roll-call and attendance tracking for hostel residents</small>
        </div>
        <button type="button" class="btn btn-primary" :disabled="saving || students.length === 0" @click="saveAttendance">
            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
            <i v-else class="bi bi-check2-circle me-1"></i> Save Attendance
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

    <!-- Filter & Action Bar -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Attendance Date</label>
                    <input v-model="selectedDate" type="date" class="form-control" @change="loadAttendance">
                </div>
                <div class="col-md-5">
                    <label class="form-label d-block text-muted small">Quick Actions</label>
                    <button class="btn btn-outline-success btn-sm me-2" @click="markAll('present')">
                        <i class="bi bi-check-all me-1"></i> Mark All Present
                    </button>
                    <button class="btn btn-outline-danger btn-sm" @click="markAll('absent')">
                        <i class="bi bi-x-circle me-1"></i> Mark All Absent
                    </button>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end gap-2 text-center pt-2">
                        <div class="badge bg-light text-dark border px-3 py-2">
                            Total: <strong>{{ summary.total || 0 }}</strong>
                        </div>
                        <div class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2">
                            Present: <strong>{{ liveCounts.present }}</strong>
                        </div>
                        <div class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2">
                            Absent: <strong>{{ liveCounts.absent }}</strong>
                        </div>
                        <div class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2">
                            Leave: <strong>{{ liveCounts.leave }}</strong>
                        </div>
                    </div>
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
                            <th>Student</th>
                            <th>Class</th>
                            <th>Room & Bed</th>
                            <th width="320" class="text-center">Attendance Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading attendance sheet...</div>
                            </td>
                        </tr>
                        <tr v-else-if="students.length === 0">
                            <td colspan="6" class="text-center py-5 text-muted">No active hostel students found.</td>
                        </tr>
                        <tr v-for="(item, idx) in students" :key="item.student_id">
                            <td>{{ idx + 1 }}</td>
                            <td>
                                <strong>{{ item.student?.first_name }} {{ item.student?.last_name }}</strong>
                                <small class="text-muted d-block">Adm: {{ item.student?.admission_no || '-' }}</small>
                            </td>
                            <td>{{ item.student?.class?.name || '-' }}</td>
                            <td>
                                <span>Room {{ item.room?.room_no || '-' }}</span>
                                <small v-if="item.bed" class="text-muted"> (Bed {{ item.bed.bed_no }})</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" :name="'att_' + item.student_id" :id="'p_' + item.student_id" value="present" v-model="item.status" class="btn-check">
                                    <label :class="['btn btn-outline-success', item.status === 'present' ? 'active' : '']" :for="'p_' + item.student_id">Present</label>

                                    <input type="radio" :name="'att_' + item.student_id" :id="'a_' + item.student_id" value="absent" v-model="item.status" class="btn-check">
                                    <label :class="['btn btn-outline-danger', item.status === 'absent' ? 'active' : '']" :for="'a_' + item.student_id">Absent</label>

                                    <input type="radio" :name="'att_' + item.student_id" :id="'l_' + item.student_id" value="leave" v-model="item.status" class="btn-check">
                                    <label :class="['btn btn-outline-warning', item.status === 'leave' ? 'active' : '']" :for="'l_' + item.student_id">Leave</label>

                                    <input type="radio" :name="'att_' + item.student_id" :id="'lt_' + item.student_id" value="late" v-model="item.status" class="btn-check">
                                    <label :class="['btn btn-outline-info', item.status === 'late' ? 'active' : '']" :for="'lt_' + item.student_id">Late</label>
                                </div>
                            </td>
                            <td>
                                <input v-model="item.remarks" type="text" class="form-control form-control-sm" placeholder="Optional notes">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="students.length > 0" class="card-footer d-flex justify-content-end py-3">
            <button type="button" class="btn btn-primary px-4" :disabled="saving" @click="saveAttendance">
                <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                <i v-else class="bi bi-save me-1"></i> Save Attendance
            </button>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const students = ref([]);
const summary = ref({});
const loading = ref(false);
const saving = ref(false);
const success = ref('');
const error = ref('');

const selectedDate = ref(new Date().toISOString().slice(0, 10));

const liveCounts = computed(() => {
    let p = 0, a = 0, l = 0, lt = 0;
    for (const s of students.value) {
        if (s.status === 'present') p++;
        else if (s.status === 'absent') a++;
        else if (s.status === 'leave') l++;
        else if (s.status === 'late') lt++;
    }
    return { present: p, absent: a, leave: l, late: lt };
});

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadAttendance = async () => {
    loading.value = true;
    error.value = '';
    try {
        const res = await axios.get('/api/hostel/attendances', {
            ...getAuthConfig(),
            params: { date: selectedDate.value }
        });
        if (res.data?.success) {
            students.value = res.data.data;
            summary.value = res.data.summary || {};
        }
    } catch (e) {
        error.value = 'Failed to load attendance records.';
    } finally {
        loading.value = false;
    }
};

const markAll = (status) => {
    students.value.forEach(s => {
        s.status = status;
    });
};

const saveAttendance = async () => {
    saving.value = true;
    error.value = '';
    try {
        const payload = {
            date: selectedDate.value,
            attendances: students.value.map(s => ({
                student_id: s.student_id,
                status: s.status,
                remarks: s.remarks,
            }))
        };
        await axios.post('/api/hostel/attendances', payload, getAuthConfig());
        success.value = 'Attendance saved successfully for ' + selectedDate.value + '.';
    } catch (e) {
        error.value = e.response?.data?.message || 'Failed to save attendance.';
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadAttendance();
});
</script>
