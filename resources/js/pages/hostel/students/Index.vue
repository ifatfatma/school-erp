<template>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Hostel Students</h4>
            <small class="text-muted">Register and manage hostel student residents</small>
        </div>
        <button type="button" class="btn btn-primary" @click="openCreate">
            <i class="bi bi-person-plus me-1"></i> Register Student
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

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-control" placeholder="Search student name, adm no..." @keyup.enter="loadStudents(1)">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Hostel</label>
                    <select v-model="filters.hostel_id" class="form-select" @change="loadStudents(1)">
                        <option value="">All Hostels</option>
                        <option v-for="h in dropdowns.hostels" :key="h.id" :value="h.id">{{ h.name }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-select" @change="loadStudents(1)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="vacated">Vacated</option>
                        <option value="suspended">Suspended</option>
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
                            <th>Student</th>
                            <th>Class</th>
                            <th>Hostel / Building</th>
                            <th>Room & Bed</th>
                            <th>Admission Date</th>
                            <th>Food</th>
                            <th>Emergency Contact</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="loading">
                            <td colspan="10" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status"></div>
                                <div class="mt-2 text-muted">Loading hostel residents...</div>
                            </td>
                        </tr>
                        <tr v-else-if="students.length === 0">
                            <td colspan="10" class="text-center py-5 text-muted">No hostel students found.</td>
                        </tr>
                        <tr v-for="(s, idx) in students" :key="s.id">
                            <td>{{ ((pagination.current_page - 1) * pagination.per_page) + idx + 1 }}</td>
                            <td>
                                <strong>{{ s.student?.first_name }} {{ s.student?.last_name }}</strong>
                                <small class="text-muted d-block">Adm: {{ s.student?.admission_no || '-' }}</small>
                            </td>
                            <td>{{ s.student?.class?.name || '-' }} <span v-if="s.student?.section">- {{ s.student.section.name }}</span></td>
                            <td>
                                <div>{{ s.hostel?.name || s.room?.building?.name || '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    Room {{ s.room?.room_no || '-' }} <span v-if="s.bed">• Bed {{ s.bed.bed_no }}</span>
                                </span>
                            </td>
                            <td>{{ s.admission_date || '-' }}</td>
                            <td>
                                <span :class="['badge text-uppercase', s.food_preference === 'veg' ? 'bg-success' : (s.food_preference === 'non-veg' ? 'bg-danger' : 'bg-secondary')]">
                                    {{ s.food_preference || 'Both' }}
                                </span>
                            </td>
                            <td>
                                <div v-if="s.emergency_contact_phone" class="small">
                                    {{ s.emergency_contact_name || 'Contact' }}: {{ s.emergency_contact_phone }}
                                </div>
                                <span v-else class="text-muted small">-</span>
                            </td>
                            <td>
                                <span :class="['badge text-capitalize', s.status === 'active' ? 'bg-success' : (s.status === 'vacated' ? 'bg-secondary' : 'bg-danger')]">
                                    {{ s.status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit" @click="openEdit(s)"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger" title="Delete" @click="deleteStudent(s)"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
            <span class="text-muted small">Showing {{ students.length }} of {{ pagination.total }} students</span>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page <= 1" @click="loadStudents(pagination.current_page - 1)">Prev</button>
                <button class="btn btn-outline-secondary" :disabled="pagination.current_page >= pagination.last_page" @click="loadStudents(pagination.current_page + 1)">Next</button>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form @submit.prevent="saveStudent">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ editing ? 'Edit Resident Profile' : 'Register Hostel Student' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="formError" class="alert alert-danger">{{ formError }}</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student <span class="text-danger">*</span></label>
                                <select v-model="form.student_id" class="form-select" :disabled="editing" required>
                                    <option value="">Select Student</option>
                                    <option v-for="st in dropdowns.students" :key="st.id" :value="st.id">
                                        {{ st.first_name }} {{ st.last_name }} ({{ st.admission_no }}) - {{ st.class?.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hostel</label>
                                <select v-model="form.hostel_id" class="form-select">
                                    <option value="">Select Hostel</option>
                                    <option v-for="h in dropdowns.hostels" :key="h.id" :value="h.id">{{ h.name }} ({{ h.type }})</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Room</label>
                                <select v-model="form.hostel_room_id" class="form-select" @change="onRoomChange">
                                    <option value="">Select Room</option>
                                    <option v-for="r in dropdowns.rooms" :key="r.id" :value="r.id">Room {{ r.room_no }} ({{ r.building?.name }})</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bed</label>
                                <select v-model="form.hostel_bed_id" class="form-select">
                                    <option value="">Select Bed</option>
                                    <option v-for="b in availableBeds" :key="b.id" :value="b.id">{{ b.bed_no }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Admission Date</label>
                                <input v-model="form.admission_date" type="date" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Food Preference</label>
                                <select v-model="form.food_preference" class="form-select">
                                    <option value="both">Both (Veg & Non-Veg)</option>
                                    <option value="veg">Vegetarian</option>
                                    <option value="non-veg">Non-Vegetarian</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select v-model="form.status" class="form-select" required>
                                    <option value="active">Active</option>
                                    <option value="vacated">Vacated</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact Name</label>
                                <input v-model="form.emergency_contact_name" type="text" class="form-control" placeholder="Parent or relative name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Emergency Contact Phone</label>
                                <input v-model="form.emergency_contact_phone" type="text" class="form-control" placeholder="Emergency phone">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Medical Conditions / Allergies</label>
                                <input v-model="form.medical_conditions" type="text" class="form-control" placeholder="Any special medical conditions, allergies, or diet needs">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea v-model="form.remarks" class="form-control" rows="2" placeholder="Notes"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="saving">
                            <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                            {{ editing ? 'Update Student' : 'Register Student' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const students = ref([]);
const dropdowns = ref({ students: [], hostels: [], rooms: [] });
const loading = ref(false);
const saving = ref(false);
const showModal = ref(false);
const editing = ref(false);
const editingId = ref(null);
const success = ref('');
const error = ref('');
const formError = ref('');

const filters = ref({ search: '', hostel_id: '', status: '', per_page: 20 });
const pagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

const emptyForm = () => ({
    student_id: '', hostel_id: '', hostel_room_id: '', hostel_bed_id: '', admission_date: new Date().toISOString().slice(0, 10),
    status: 'active', food_preference: 'both', emergency_contact_name: '', emergency_contact_phone: '', medical_conditions: '', remarks: ''
});
const form = ref(emptyForm());

const availableBeds = computed(() => {
    if (!form.value.hostel_room_id) return [];
    const r = dropdowns.value.rooms.find(rm => rm.id === form.value.hostel_room_id);
    return r?.beds || [];
});

const onRoomChange = () => {
    form.value.hostel_bed_id = '';
};

const getAuthConfig = () => {
    const token = localStorage.getItem('token') || localStorage.getItem('auth_token') || sessionStorage.getItem('token');
    const config = { withCredentials: true, headers: { Accept: 'application/json' } };
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
};

const loadDropdowns = async () => {
    try {
        const res = await axios.get('/api/hostel/students/dropdowns', getAuthConfig());
        if (res.data?.success) dropdowns.value = res.data.data;
    } catch (e) {
        console.error(e);
    }
};

const loadStudents = async (page = 1) => {
    loading.value = true;
    try {
        const params = { page, ...filters.value };
        const res = await axios.get('/api/hostel/students', { ...getAuthConfig(), params });
        if (res.data?.success) {
            students.value = res.data.data;
            pagination.value = res.data.meta;
        }
    } catch (e) {
        error.value = 'Failed to load hostel students.';
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { search: '', hostel_id: '', status: '', per_page: 20 };
    loadStudents(1);
};

const openCreate = () => {
    editing.value = false;
    editingId.value = null;
    form.value = emptyForm();
    formError.value = '';
    showModal.value = true;
};

const openEdit = (s) => {
    editing.value = true;
    editingId.value = s.id;
    form.value = { ...s };
    formError.value = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveStudent = async () => {
    saving.value = true;
    formError.value = '';
    try {
        if (editing.value) {
            await axios.put(`/api/hostel/students/${editingId.value}`, form.value, getAuthConfig());
            success.value = 'Hostel student updated successfully.';
        } else {
            await axios.post('/api/hostel/students', form.value, getAuthConfig());
            success.value = 'Hostel student registered successfully.';
        }
        closeModal();
        loadStudents(pagination.value.current_page);
    } catch (e) {
        formError.value = e.response?.data?.message || 'Failed to save hostel student.';
    } finally {
        saving.value = false;
    }
};

const deleteStudent = async (s) => {
    if (!confirm(`Are you sure you want to remove student "${s.student?.first_name} ${s.student?.last_name}"?`)) return;
    try {
        await axios.delete(`/api/hostel/students/${s.id}`, getAuthConfig());
        success.value = 'Hostel student removed successfully.';
        loadStudents(pagination.value.current_page);
    } catch (e) {
        error.value = 'Failed to delete student.';
    }
};

onMounted(() => {
    loadDropdowns();
    loadStudents();
});
</script>
