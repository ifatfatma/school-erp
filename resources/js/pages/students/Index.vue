<script setup>
import { ref, reactive, computed, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import axios from "@/api/axios";
import Modal from "bootstrap/js/dist/modal";

import AppTable from "@/components/ui/AppTable.vue";
import AppPagination from "@/components/ui/AppPagination.vue";
import AppBreadcrumb from "@/components/ui/AppBreadcrumb.vue";

import { useStudentStore } from "@/stores/student";
import { useClassStore } from "@/stores/class";
import { useSectionStore } from "@/stores/section";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();

const studentStore = useStudentStore();
const classStore = useClassStore();
const sectionStore = useSectionStore();
const authStore = useAuthStore();

const allSchools = ref([]);

const isSuperAdmin = computed(() => {
    const u = authStore.user;
    return u?.user_type === 'super_admin' || u?.roles?.some(r => {
        const name = typeof r === 'string' ? r : (r.name || '');
        return name.toLowerCase() === 'super admin' || name.toLowerCase() === 'super_admin';
    });
});

const filters = reactive({
    search: "",
    school_id: "",
    class_id: "",
    section_id: "",
    status: "",
    page: 1,
    per_page: 15,
});

const columns = computed(() => {
    const cols = [
        { key: "photo", label: "Photo" },
        { key: "admission_no", label: "Admission No" },
    ];

    if (isSuperAdmin.value) {
        cols.push({ key: "school", label: "School / Campus" });
    }

    cols.push(
        { key: "roll_no", label: "Roll No" },
        { key: "student", label: "Student" },
        { key: "class", label: "Class" },
        { key: "section", label: "Section" },
        { key: "mobile", label: "Mobile" },
        { key: "status", label: "Status" },
        { key: "action", label: "Action" },
    );

    return cols;
});

const availableSections = computed(() => {
    if (!filters.class_id) return sectionStore.items || [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(filters.class_id)
    );
});

watch(() => filters.class_id, () => {
    filters.section_id = "";
});

// Import & Export State
const exporting = ref(false);
const importing = ref(false);
const importModalRef = ref(null);
let bsImportModal = null;
const selectedFile = ref(null);
const fileInputRef = ref(null);

const importOptions = reactive({
    default_class_id: "",
    default_section_id: "",
    create_portal_accounts: true,
});

const importResult = ref(null);

const importModalSections = computed(() => {
    if (!importOptions.default_class_id) return [];
    return (sectionStore.items || []).filter(
        s => String(s.class_id) === String(importOptions.default_class_id)
    );
});

async function loadSchools() {
    if (isSuperAdmin.value) {
        try {
            const res = await axios.get("/tenants");
            if (res.data?.data?.length) {
                allSchools.value = res.data.data;
                return;
            }
        } catch (e) {}

        try {
            const res2 = await axios.get("/public/saas/schools");
            if (res2.data?.data?.length) {
                allSchools.value = res2.data.data;
            }
        } catch (e2) {}
    }
}

async function onSchoolFilterChange() {
    filters.class_id = "";
    filters.section_id = "";
    filters.page = 1;
    await Promise.all([
        classStore.fetch({ per_page: 100, school_id: filters.school_id || undefined }),
        sectionStore.load({ per_page: 200, school_id: filters.school_id || undefined }),
        load(),
    ]);
}

onMounted(async () => {
    if (!authStore.user && authStore.token) {
        await authStore.loadProfile();
    }
    await Promise.all([
        loadSchools(),
        classStore.fetch({ per_page: 100 }),
        sectionStore.load({ per_page: 200 }),
        load(),
    ]);
});

async function load() {
    const res = await studentStore.fetch(filters);
    if ((!allSchools.value || allSchools.value.length === 0) && studentStore.allSchools?.length) {
        allSchools.value = studentStore.allSchools;
    }
}

function onPageChange(page) {
    filters.page = page;
    load();
}

function reset() {
    filters.search = "";
    filters.school_id = "";
    filters.class_id = "";
    filters.section_id = "";
    filters.status = "";
    filters.page = 1;
    classStore.fetch({ per_page: 100 });
    sectionStore.load({ per_page: 200 });
    load();
}

// Download Excel Template
async function downloadTemplate() {
    try {
        const response = await axios.get("/students/export/template", {
            responseType: "blob",
        });
        const blob = new Blob([response.data], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = "student_import_template.xlsx";
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Download Failed",
            text: "Could not download the Excel template. Please try again.",
        });
    }
}

// Export Filtered Student List to Excel
async function exportStudentsExcel() {
    exporting.value = true;
    try {
        const response = await axios.get("/students/export/excel", {
            params: {
                school_id: filters.school_id || undefined,
                class_id: filters.class_id,
                section_id: filters.section_id,
                status: filters.status,
                search: filters.search,
            },
            responseType: "blob",
        });

        const blob = new Blob([response.data], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        const dateStr = new Date().toISOString().split("T")[0];
        link.download = `students_export_${dateStr}.xlsx`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Excel exported successfully!",
            timer: 2000,
            showConfirmButton: false,
        });
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Export Failed",
            text: "Could not export students to Excel.",
        });
    } finally {
        exporting.value = false;
    }
}

// Open Import Modal
function openImportModal() {
    selectedFile.value = null;
    importResult.value = null;
    importOptions.default_class_id = "";
    importOptions.default_section_id = "";
    importOptions.create_portal_accounts = true;
    if (fileInputRef.value) {
        fileInputRef.value.value = "";
    }

    if (!bsImportModal && importModalRef.value) {
        bsImportModal = new Modal(importModalRef.value);
    }
    bsImportModal?.show();
}

function onFileChange(e) {
    const file = e.target.files[0];
    if (file) {
        selectedFile.value = file;
    }
}

// Execute Bulk Import
async function submitImport() {
    if (!selectedFile.value) {
        Swal.fire({
            icon: "warning",
            title: "File Required",
            text: "Please select an Excel (.xlsx, .xls) or CSV file to import.",
        });
        return;
    }

    importing.value = true;
    importResult.value = null;

    const formData = new FormData();
    formData.append("file", selectedFile.value);
    if (importOptions.default_class_id) {
        formData.append("default_class_id", importOptions.default_class_id);
    }
    if (importOptions.default_section_id) {
        formData.append("default_section_id", importOptions.default_section_id);
    }
    formData.append("create_portal_accounts", importOptions.create_portal_accounts ? "1" : "0");

    try {
        const response = await axios.post("/students/import/excel", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        importResult.value = response.data;

        if (response.data.imported_count > 0) {
            Swal.fire({
                icon: "success",
                title: "Import Finished",
                text: response.data.message,
                timer: 2500,
                showConfirmButton: false,
            });
            load();
        }
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Import Error",
            text: err.response?.data?.message || "Failed to process the uploaded spreadsheet.",
        });
    } finally {
        importing.value = false;
    }
}

async function remove(id) {
    const result = await Swal.fire({
        title: "Delete Student?",
        text: "Are you sure you want to delete this student? This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",
    });

    if (!result.isConfirmed) return;

    try {
        await studentStore.delete(id);
        Swal.fire({
            icon: "success",
            title: "Deleted!",
            text: "Student has been removed.",
            timer: 1500,
            showConfirmButton: false,
        });
        load();
    } catch (e) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: e.response?.data?.message || "Failed to delete student.",
        });
    }
}
</script>

<template>
<div class="container-fluid">
    <AppBreadcrumb
        :items="[
            { label: 'Dashboard', to: '/' },
            { label: 'Students' }
        ]"
    />

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-people-fill text-primary me-2"></i>Students Management
            </h5>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Download Template Button -->
                <button
                    type="button"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                    title="Download Sample Excel Template to fill in student data"
                    @click="downloadTemplate"
                >
                    <i class="bi bi-file-earmark-excel text-success"></i>
                    <span>Template</span>
                </button>

                <!-- Export Excel Button -->
                <button
                    type="button"
                    class="btn btn-outline-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                    title="Export Current Student List to Excel (.xlsx)"
                    :disabled="exporting"
                    @click="exportStudentsExcel"
                >
                    <span v-if="exporting" class="spinner-border spinner-border-sm"></span>
                    <i v-else class="bi bi-download"></i>
                    <span>{{ exporting ? 'Exporting...' : 'Export Excel' }}</span>
                </button>

                <!-- Import Excel Button -->
                <button
                    type="button"
                    class="btn btn-success btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1 text-white"
                    title="Bulk Import Students from Excel (.xlsx, .csv)"
                    @click="openImportModal"
                >
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                    <span>Import Excel</span>
                </button>

                <!-- Add Student Button -->
                <RouterLink
                    :to="{ name: 'students.create' }"
                    class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1"
                >
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Add Student</span>
                </RouterLink>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-2 mb-3 align-items-center">
                <!-- School Filter (Super Admin) -->
                <div class="col-md-3" v-if="isSuperAdmin">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-primary">
                            <i class="bi bi-buildings"></i>
                        </span>
                        <select
                            class="form-select border-start-0 fw-semibold"
                            v-model="filters.school_id"
                            @change="onSchoolFilterChange"
                        >
                            <option value="">All Schools</option>
                            <option
                                v-for="item in allSchools"
                                :key="'sch-opt-'+item.id"
                                :value="item.id"
                            >
                                {{ item.name }} ({{ item.code }})
                            </option>
                        </select>
                    </div>
                </div>

                <div :class="isSuperAdmin ? 'col-md-2' : 'col-md-3'">
                    <input
                        class="form-control"
                        placeholder="Search student or admission no..."
                        v-model="filters.search"
                        @keyup.enter="load"
                    />
                </div>

                <div class="col-md-2">
                    <select
                        class="form-select"
                        v-model="filters.class_id"
                        @change="load"
                    >
                        <option value="">All Classes</option>
                        <option
                            v-for="item in classStore.items"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div :class="isSuperAdmin ? 'col-md-2' : 'col-md-2'">
                    <select
                        class="form-select"
                        v-model="filters.section_id"
                        @change="load"
                    >
                        <option value="">All Sections</option>
                        <option
                            v-for="item in availableSections"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <div :class="isSuperAdmin ? 'col-md-1' : 'col-md-2'">
                    <select
                        class="form-select"
                        v-model="filters.status"
                        @change="load"
                    >
                        <option value="">Status</option>
                        <option :value="true">Active</option>
                        <option :value="false">Inactive</option>
                    </select>
                </div>

                <div :class="isSuperAdmin ? 'col-md-2 d-flex gap-1 justify-content-end' : 'col-md-3 d-flex gap-1 justify-content-end'">
                    <button
                        class="btn btn-primary"
                        @click="load"
                    >
                        <i class="bi bi-search me-1"></i> Filter
                    </button>

                    <button
                        class="btn btn-secondary"
                        @click="reset"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>

            <AppTable
                :columns="columns"
                :items="studentStore.items"
                :loading="studentStore.loading"
            >
                <template #photo="{ item }">
                    <img
                        v-if="item.photo_url"
                        :src="item.photo_url"
                        class="rounded border bg-light"
                        style="width: 40px; height: 40px; object-fit: cover;"
                        @error="e => e.target.style.display = 'none'"
                    />
                    <div
                        v-else
                        class="rounded bg-light text-secondary border d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px; font-weight: 600; font-size: 14px;"
                    >
                        {{ (item.first_name || 'S').charAt(0).toUpperCase() }}
                    </div>
                </template>

                <template #school="{ item }">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                        <i class="bi bi-building me-1"></i> {{ item.school_name || item.school?.name || 'School' }}
                    </span>
                    <small v-if="item.school_code || item.school?.code" class="text-muted d-block font-monospace">
                        ({{ item.school_code || item.school?.code }})
                    </small>
                </template>

                <template #student="{ item }">
                    <div class="fw-bold">{{ item.full_name }}</div>
                    <small class="text-muted" v-if="item.father_name">
                        Father: {{ item.father_name }}
                    </small>
                </template>

                <template #class="{ item }">
                    <span class="badge bg-light text-dark border">
                        {{ item.class?.name || item.class_name || '-' }}
                    </span>
                </template>

                <template #section="{ item }">
                    <span class="badge bg-light text-secondary border">
                        {{ item.section?.name || item.section_name || '-' }}
                    </span>
                </template>

                <template #status="{ item }">
                    <span
                        class="badge"
                        :class="item.status ? 'bg-success' : 'bg-danger'"
                    >
                        {{ item.status_text || (item.status ? 'Active' : 'Inactive') }}
                    </span>
                </template>

                <template #action="{ item }">
                    <div class="btn-group btn-group-sm">
                        <RouterLink
                            class="btn btn-info text-white"
                            :to="{
                                name: 'students.show',
                                params: { id: item.id }
                            }"
                            title="View Details"
                        >
                            <i class="bi bi-eye"></i>
                        </RouterLink>

                        <RouterLink
                            class="btn btn-warning text-dark"
                            :to="{
                                name: 'students.edit',
                                params: { id: item.id }
                            }"
                            title="Edit"
                        >
                            <i class="bi bi-pencil"></i>
                        </RouterLink>

                        <button
                            class="btn btn-danger"
                            @click="remove(item.id)"
                            title="Delete"
                        >
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </template>
            </AppTable>

            <div class="mt-3" v-if="studentStore.pagination && studentStore.pagination.total > 0">
                <AppPagination
                    :pagination="studentStore.pagination"
                    @change="onPageChange"
                />
            </div>
        </div>
    </div>

    <!-- ========================================================
         BULK IMPORT EXCEL MODAL
         ======================================================== -->
    <div class="modal fade" id="studentImportModal" tabindex="-1" aria-hidden="true" ref="importModalRef">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white p-3 px-4 border-0">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-excel-fill"></i>
                        <span>Import Students from Excel / CSV</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form @submit.prevent="submitImport">
                    <div class="modal-body p-4">
                        <!-- Step 1: Download Template Helper -->
                        <div class="alert alert-success-subtle border border-success-subtle rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-success fs-5"></i>
                                <div>
                                    <strong class="text-success d-block">Need the sample Excel format?</strong>
                                    <small class="text-muted">Download our pre-formatted spreadsheet template with sample columns & data.</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" @click="downloadTemplate">
                                <i class="bi bi-download me-1"></i> Download Template (.xlsx)
                            </button>
                        </div>

                        <!-- Step 2: Choose File -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Select Excel / CSV File <span class="text-danger">*</span></label>
                            <input
                                type="file"
                                class="form-control form-control-lg rounded-3"
                                accept=".xlsx,.xls,.csv"
                                ref="fileInputRef"
                                required
                                @change="onFileChange"
                            />
                            <small class="text-muted mt-1 d-block">Supported file types: <code>.xlsx</code>, <code>.xls</code>, <code>.csv</code> (Max size: 10MB)</small>
                        </div>

                        <!-- Step 3: Default Class & Section (Optional Fallback) -->
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-dark mb-2 small text-uppercase">
                                <i class="bi bi-sliders me-1 text-primary"></i> Default Fallbacks (Optional)
                            </h6>
                            <p class="text-muted small mb-3">
                                If your spreadsheet rows do not have class or section columns, you can select the default target class & section below:
                            </p>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Default Class</label>
                                    <select v-model="importOptions.default_class_id" class="form-select form-select-sm rounded-3">
                                        <option value="">Auto-detect from file rows</option>
                                        <option v-for="c in classStore.items" :key="c.id" :value="c.id">{{ c.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Default Section</label>
                                    <select v-model="importOptions.default_section_id" class="form-select form-select-sm rounded-3" :disabled="!importOptions.default_class_id">
                                        <option value="">Auto-detect from file rows</option>
                                        <option v-for="s in importModalSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Portal Login Account Checkbox -->
                        <div class="form-check form-switch mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="portalAccountsSwitch"
                                v-model="importOptions.create_portal_accounts"
                            />
                            <label class="form-check-label fw-semibold" for="portalAccountsSwitch">
                                Automatically generate Student Portal user logins
                            </label>
                            <small class="d-block text-muted">
                                Students will be able to log in to the portal using their admission number / email with default password: <code>12345678</code>.
                            </small>
                        </div>

                        <!-- Result & Error Summary Box -->
                        <div v-if="importResult" class="mt-4 pt-3 border-top">
                            <div v-if="importResult.imported_count > 0" class="alert alert-success rounded-3 p-3 mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                                <div>
                                    <strong>{{ importResult.imported_count }} student(s) imported successfully!</strong>
                                </div>
                            </div>

                            <div v-if="importResult.errors && importResult.errors.length > 0" class="alert alert-danger rounded-3 p-3 mb-0">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                    <strong>{{ importResult.errors.length }} row(s) could not be imported:</strong>
                                </div>
                                <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered bg-white mb-0 small">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="70">Row #</th>
                                                <th>Name</th>
                                                <th>Admission No</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(err, idx) in importResult.errors" :key="idx">
                                                <td class="fw-bold">{{ err.row }}</td>
                                                <td>{{ err.name || '-' }}</td>
                                                <td><code>{{ err.admission_no || '-' }}</code></td>
                                                <td class="text-danger">{{ err.error }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3 px-4 border-0">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" :disabled="importing || !selectedFile">
                            <span v-if="importing" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-upload me-1"></i>
                            {{ importing ? 'Importing Students...' : 'Upload & Start Import' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>
