<script setup>
import { ref, reactive, computed, watch, onMounted } from "vue";
import Swal from "sweetalert2";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useStudentAttendanceStore } from "@/stores/studentAttendance";
import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";

const attendanceStore = useStudentAttendanceStore();
const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();

const loadingStudents = ref(false);
const saving = ref(false);

const form = reactive({
    attendance_date: new Date().toISOString().slice(0, 10),
    class_id: "",
    section_id: "",
    records: [],
});

const availableSections = computed(() => {
    if (!form.class_id) return sectionStore.items || [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(form.class_id)
    );
});

watch(() => form.class_id, () => {
    form.section_id = "";
    form.records = [];
});

watch(() => form.section_id, () => {
    form.records = [];
});

onMounted(async () => {
    await Promise.all([
        classStore.fetch({ per_page: 100 }),
        sectionStore.load({ per_page: 200 }),
    ]);
});

async function loadStudents() {
    if (!form.class_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select a class first.",
        });
        return;
    }

    if (!form.section_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select a section.",
        });
        return;
    }

    loadingStudents.value = true;

    try {
        await studentStore.fetch({
            class_id: form.class_id,
            section_id: form.section_id,
            per_page: 1000,
            status: 1,
        });

        // Try to fetch existing attendance for this date & section
        const existingMap = {};
        try {
            await attendanceStore.fetch({
                class_id: form.class_id,
                section_id: form.section_id,
                attendance_date: form.attendance_date,
                per_page: 1000,
            });
            (attendanceStore.items || []).forEach(r => {
                const sId = r.student_id || r.student?.id;
                if (sId) {
                    existingMap[sId] = r;
                }
            });
        } catch (e) {
            console.warn("Could not check existing attendance:", e);
        }

        form.records = (studentStore.items || []).map(student => {
            const prev = existingMap[student.id];
            return {
                student_id: student.id,
                full_name: student.full_name,
                admission_no: student.admission_no,
                roll_no: student.roll_no,
                status: prev?.status || "Present",
                remarks: prev?.remarks || "",
                check_in: prev?.check_in || null,
                check_out: prev?.check_out || null,
            };
        });

        if (form.records.length === 0) {
            Swal.fire({
                icon: "info",
                title: "No Students",
                text: "No active students found in the selected class and section.",
            });
        }
    } finally {
        loadingStudents.value = false;
    }
}

function markAll(status) {
    form.records.forEach(r => {
        r.status = status;
    });
}

const stats = computed(() => {
    const total = form.records.length;
    let present = 0, absent = 0, late = 0, halfDay = 0, leave = 0;
    form.records.forEach(r => {
        if (r.status === "Present") present++;
        else if (r.status === "Absent") absent++;
        else if (r.status === "Late") late++;
        else if (r.status === "Half Day") halfDay++;
        else if (r.status === "Leave") leave++;
    });
    return { total, present, absent, late, halfDay, leave };
});

async function saveAttendance() {
    if (!form.records.length) return;

    saving.value = true;
    try {
        await attendanceStore.bulk({
            attendance_date: form.attendance_date,
            class_id: form.class_id,
            section_id: form.section_id,
            records: form.records.map(r => ({
                student_id: r.student_id,
                status: r.status,
                remarks: r.remarks,
                check_in: r.check_in,
                check_out: r.check_out,
            })),
        });

        await Swal.fire({
            icon: "success",
            title: "Saved!",
            text: "Student attendance has been recorded successfully.",
            timer: 2000,
            showConfirmButton: false,
        });
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: e.response?.data?.message || "Failed to save attendance.",
        });
    } finally {
        saving.value = false;
    }
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Student Attendance', to: { name: 'student-attendance.index' } },
            { label: 'Bulk Attendance Entry' }
        ]"
    />

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-calendar-check-fill text-primary me-2"></i>Bulk Student Attendance
            </h5>

            <RouterLink
                :to="{ name: 'student-attendance.index' }"
                class="btn btn-outline-secondary btn-sm"
            >
                <i class="bi bi-list-check me-1"></i> View Attendance List
            </RouterLink>
        </div>

        <div class="card-body">
            <!-- Filter Bar -->
            <div class="row g-3 mb-3 p-3 bg-light rounded border">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Attendance Date</label>
                    <input
                        type="date"
                        class="form-control"
                        v-model="form.attendance_date"
                    />
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Class</label>
                    <select
                        class="form-select"
                        v-model="form.class_id"
                    >
                        <option value="">Select Class</option>
                        <option
                            v-for="item in classStore.items"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Section</label>
                    <select
                        class="form-select"
                        v-model="form.section_id"
                        :disabled="!form.class_id"
                    >
                        <option value="">Select Section</option>
                        <option
                            v-for="item in availableSections"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button
                        class="btn btn-primary w-100"
                        @click="loadStudents"
                        :disabled="loadingStudents"
                    >
                        <span v-if="loadingStudents" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="bi bi-arrow-clockwise me-1"></i>
                        {{ loadingStudents ? 'Loading Students...' : 'Load Students' }}
                    </button>
                </div>
            </div>

            <!-- Summary & Bulk Actions -->
            <div v-if="form.records.length" class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-2 bg-white rounded border">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-secondary px-2 py-1">Total: {{ stats.total }}</span>
                    <span class="badge bg-success px-2 py-1">Present: {{ stats.present }}</span>
                    <span class="badge bg-danger px-2 py-1">Absent: {{ stats.absent }}</span>
                    <span class="badge bg-warning text-dark px-2 py-1">Late: {{ stats.late }}</span>
                    <span class="badge bg-info text-dark px-2 py-1">Half Day: {{ stats.halfDay }}</span>
                    <span class="badge bg-dark px-2 py-1">Leave: {{ stats.leave }}</span>
                </div>

                <div class="d-flex gap-2">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-success"
                        @click="markAll('Present')"
                    >
                        <i class="bi bi-check-all me-1"></i> Mark All Present
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        @click="markAll('Absent')"
                    >
                        <i class="bi bi-x-circle me-1"></i> Mark All Absent
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-warning text-dark"
                        @click="markAll('Late')"
                    >
                        <i class="bi bi-clock me-1"></i> Mark All Late
                    </button>
                </div>
            </div>

            <!-- Attendance Table -->
            <div v-if="form.records.length" class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th width="120">Adm / Roll</th>
                            <th>Student Name</th>
                            <th width="380">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in form.records" :key="row.student_id">
                            <td class="text-center fw-semibold text-muted">{{ index + 1 }}</td>
                            <td>
                                <div><small class="text-muted">Adm:</small> <strong>{{ row.admission_no || '-' }}</strong></div>
                                <div v-if="row.roll_no"><small class="text-muted">Roll:</small> {{ row.roll_no }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ row.full_name }}</div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <input
                                        type="radio"
                                        class="btn-check"
                                        :name="'status_' + row.student_id"
                                        :id="'p_' + row.student_id"
                                        value="Present"
                                        v-model="row.status"
                                    />
                                    <label class="btn btn-outline-success" :for="'p_' + row.student_id">Present</label>

                                    <input
                                        type="radio"
                                        class="btn-check"
                                        :name="'status_' + row.student_id"
                                        :id="'a_' + row.student_id"
                                        value="Absent"
                                        v-model="row.status"
                                    />
                                    <label class="btn btn-outline-danger" :for="'a_' + row.student_id">Absent</label>

                                    <input
                                        type="radio"
                                        class="btn-check"
                                        :name="'status_' + row.student_id"
                                        :id="'l_' + row.student_id"
                                        value="Late"
                                        v-model="row.status"
                                    />
                                    <label class="btn btn-outline-warning" :for="'l_' + row.student_id">Late</label>

                                    <input
                                        type="radio"
                                        class="btn-check"
                                        :name="'status_' + row.student_id"
                                        :id="'h_' + row.student_id"
                                        value="Half Day"
                                        v-model="row.status"
                                    />
                                    <label class="btn btn-outline-info" :for="'h_' + row.student_id">Half Day</label>

                                    <input
                                        type="radio"
                                        class="btn-check"
                                        :name="'status_' + row.student_id"
                                        :id="'lv_' + row.student_id"
                                        value="Leave"
                                        v-model="row.status"
                                    />
                                    <label class="btn btn-outline-dark" :for="'lv_' + row.student_id">Leave</label>
                                </div>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    class="form-control form-control-sm"
                                    placeholder="Optional remark..."
                                    v-model="row.remarks"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else-if="!loadingStudents" class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block opacity-25 mb-2"></i>
                <p>Select date, class, and section, then click "Load Students" to mark attendance.</p>
            </div>

            <!-- Submit Button -->
            <div class="text-end mt-4" v-if="form.records.length">
                <button
                    class="btn btn-success px-4"
                    @click="saveAttendance"
                    :disabled="saving"
                >
                    <span v-if="saving" class="spinner-border spinner-border-sm me-1"></span>
                    <i v-else class="bi bi-check2-circle me-1"></i>
                    {{ saving ? 'Saving Attendance...' : 'Save Attendance' }}
                </button>
            </div>
        </div>
    </div>
</div>
</template>
