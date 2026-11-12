<script setup>
import { reactive, ref, computed, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";

import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";
import { useStudentPromotionStore } from "@/stores/studentPromotion";
import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useAcademicSessionStore } from "@/stores/academicSession";

const router = useRouter();

const promotionStore = useStudentPromotionStore();
const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const sessionStore = useAcademicSessionStore();

const students = ref([]);
const searching = ref(false);

const form = reactive({
    from_session_id: "",
    from_class_id: "",
    from_section_id: "",
    to_session_id: "",
    to_class_id: "",
    to_section_id: "",
    promotion_date: new Date().toISOString().substring(0, 10),
    promotion_type: "Promoted",
    remarks: "",
    student_ids: [],
});

const fromAvailableSections = computed(() => {
    if (!form.from_class_id) return sectionStore.items || [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(form.from_class_id)
    );
});

const toAvailableSections = computed(() => {
    if (!form.to_class_id) return sectionStore.items || [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(form.to_class_id)
    );
});

watch(() => form.from_class_id, () => {
    form.from_section_id = "";
    students.value = [];
    form.student_ids = [];
});

watch(() => form.to_class_id, () => {
    form.to_section_id = "";
});

onMounted(async () => {
    await Promise.all([
        sessionStore.load({ per_page: 1000 }),
        classStore.fetch({ per_page: 1000 }),
        sectionStore.load({ per_page: 1000 })
    ]);
});

async function searchStudents() {
    if (!form.from_session_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select the current academic session.",
        });
        return;
    }

    if (!form.from_class_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select the current class.",
        });
        return;
    }

    searching.value = true;
    try {
        await studentStore.fetch({
            academic_session_id: form.from_session_id,
            class_id: form.from_class_id,
            section_id: form.from_section_id || undefined,
            per_page: 1000,
            status: 1
        });

        students.value = studentStore.items || [];
        form.student_ids = students.value.map(s => s.id);

        if (students.value.length === 0) {
            Swal.fire({
                icon: "info",
                title: "No Students Found",
                text: "No active students found matching the selected session, class and section.",
            });
        }
    } finally {
        searching.value = false;
    }
}

function selectAll() {
    form.student_ids = students.value.map(i => i.id);
}

function unSelectAll() {
    form.student_ids = [];
}

async function promote() {
    if (!form.student_ids.length) {
        Swal.fire({
            icon: "warning",
            title: "No Students Selected",
            text: "Please select at least one student to promote.",
        });
        return;
    }

    if (!form.to_session_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select target academic session.",
        });
        return;
    }

    if (!form.to_class_id) {
        Swal.fire({
            icon: "warning",
            title: "Required",
            text: "Please select target class.",
        });
        return;
    }

    const confirm = await Swal.fire({
        title: "Confirm Bulk Promotion?",
        text: `Promote ${form.student_ids.length} student(s) to the selected session/class?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Promote",
        cancelButtonText: "Cancel",
    });

    if (!confirm.isConfirmed) return;

    try {
        await promotionStore.bulkPromotion(form);

        await Swal.fire({
            icon: "success",
            title: "Success!",
            text: "Selected students have been successfully promoted.",
            timer: 2000,
            showConfirmButton: false,
        });

        router.push({ name: "student-promotions.index" });
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Promotion Failed",
            text: e.response?.data?.message || "An error occurred while promoting students.",
        });
    }
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Student Promotions', to: { name: 'student-promotions.index' } },
            { label: 'Bulk Promotion' }
        ]"
    />

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-box-arrow-in-up-right text-primary me-2"></i>Bulk Student Promotion
            </h5>

            <RouterLink
                :to="{ name: 'student-promotions.index' }"
                class="btn btn-outline-secondary btn-sm"
            >
                <i class="bi bi-list-check me-1"></i> Promotion History
            </RouterLink>
        </div>

        <div class="card-body">
            <!-- Source Filters -->
            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                1. Select Current Students (From)
            </h6>

            <div class="row g-3 p-3 bg-light rounded border mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Current Session *</label>
                    <select class="form-select" v-model="form.from_session_id">
                        <option value="">Select Session</option>
                        <option
                            v-for="item in sessionStore.sessions"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Current Class *</label>
                    <select class="form-select" v-model="form.from_class_id">
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

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Current Section (Optional)</label>
                    <select
                        class="form-select"
                        v-model="form.from_section_id"
                        :disabled="!form.from_class_id"
                    >
                        <option value="">All Sections</option>
                        <option
                            v-for="item in fromAvailableSections"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button
                        class="btn btn-primary"
                        @click="searchStudents"
                        :disabled="searching"
                    >
                        <span v-if="searching" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="bi bi-search me-1"></i>
                        {{ searching ? 'Searching...' : 'Find Eligible Students' }}
                    </button>
                </div>
            </div>

            <!-- Student Selection List -->
            <div v-if="students.length" class="mb-4">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    2. Select Students to Promote ({{ form.student_ids.length }} / {{ students.length }} Selected)
                </h6>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success" @click="selectAll">
                            <i class="bi bi-check-all me-1"></i> Select All
                        </button>
                        <button class="btn btn-outline-secondary" @click="unSelectAll">
                            <i class="bi bi-dash me-1"></i> Deselect All
                        </button>
                    </div>

                    <span class="badge bg-primary fs-6 px-3 py-2">
                        {{ form.student_ids.length }} of {{ students.length }} Selected
                    </span>
                </div>

                <div class="table-responsive border rounded" style="max-height: 360px; overflow-y: auto;">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="45" class="text-center">#</th>
                                <th width="120">Adm No</th>
                                <th width="100">Roll No</th>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="student in students"
                                :key="student.id"
                                :class="{ 'table-primary': form.student_ids.includes(student.id) }"
                            >
                                <td class="text-center">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        :checked="form.student_ids.includes(student.id)"
                                        @change="
                                            $event.target.checked
                                                ? form.student_ids.push(student.id)
                                                : form.student_ids = form.student_ids.filter(i => i != student.id)
                                        "
                                    />
                                </td>
                                <td><code>{{ student.admission_no }}</code></td>
                                <td>{{ student.roll_no || '-' }}</td>
                                <td><strong>{{ student.full_name }}</strong></td>
                                <td>
                                    <span class="badge bg-success">Active</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Destination (Target) Settings -->
                <h6 class="fw-bold text-muted text-uppercase mt-4 mb-3" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    3. Promotion Target Details (To)
                </h6>

                <div class="row g-3 p-3 bg-light rounded border">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Target Session *</label>
                        <select class="form-select" v-model="form.to_session_id">
                            <option value="">Select Target Session</option>
                            <option
                                v-for="item in sessionStore.sessions"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Target Class *</label>
                        <select class="form-select" v-model="form.to_class_id">
                            <option value="">Select Target Class</option>
                            <option
                                v-for="item in classStore.items"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Target Section (Optional)</label>
                        <select
                            class="form-select"
                            v-model="form.to_section_id"
                            :disabled="!form.to_class_id"
                        >
                            <option value="">Select Target Section</option>
                            <option
                                v-for="item in toAvailableSections"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Promotion Date *</label>
                        <input
                            type="date"
                            class="form-control"
                            v-model="form.promotion_date"
                        />
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Promotion Type *</label>
                        <select class="form-select" v-model="form.promotion_type">
                            <option value="Promoted">Promoted</option>
                            <option value="Passed">Passed</option>
                            <option value="Repeated">Repeated</option>
                            <option value="Transferred">Transferred</option>
                            <option value="Graduated">Graduated</option>
                            <option value="Detained">Detained</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Remarks</label>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Optional remark or notes..."
                            v-model="form.remarks"
                        />
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button
                            class="btn btn-success btn-lg px-4"
                            @click="promote"
                            :disabled="promotionStore.saving || !form.student_ids.length"
                        >
                            <span v-if="promotionStore.saving" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-check2-circle me-1"></i>
                            {{ promotionStore.saving ? "Processing Promotion..." : `Promote (${form.student_ids.length}) Students` }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>
