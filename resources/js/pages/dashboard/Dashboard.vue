<template>
    <div class="container-fluid py-3">

        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div
            class="d-flex flex-wrap justify-content-between align-items-center mb-4"
        >
            <div>
                <h4 class="mb-1">
                    <i :class="dashboard.is_global ? 'bi bi-globe2 text-primary me-2' : 'bi bi-building text-primary me-2'"></i>
                    {{ dashboard.is_global ? 'Global Dashboard (All Schools)' : (dashboard.school_name || 'School Dashboard') }}
                </h4>

                <div class="text-muted">
                    <span v-if="dashboard.is_global">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2">Global Mode</span>
                        System-wide aggregated overview across all active registered schools
                    </span>
                    <span v-else-if="dashboard.academic_session">
                        <span v-if="dashboard.school_code" class="badge bg-light text-dark border me-2">{{ dashboard.school_code }}</span>
                        Session: {{ dashboard.academic_session.name }}
                    </span>
                    <span v-else>
                        <span v-if="dashboard.school_code" class="badge bg-light text-dark border me-2">{{ dashboard.school_code }}</span>
                        School overview
                    </span>
                </div>
            </div>

            <button
                type="button"
                class="btn btn-primary"
                :disabled="loading"
                @click="loadDashboard"
            >
                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-1"
                ></span>

                <i
                    v-else
                    class="bi bi-arrow-clockwise me-1"
                ></i>

                Refresh
            </button>
        </div>


        <!-- ========================================================= -->
        <!-- ERROR -->
        <!-- ========================================================= -->

        <div
            v-if="error"
            class="alert alert-danger d-flex justify-content-between align-items-center"
        >
            <span>
                {{ error }}
            </span>

            <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                @click="loadDashboard"
            >
                Retry
            </button>
        </div>


        <!-- ========================================================= -->
        <!-- MAIN CARDS -->
        <!-- ========================================================= -->

        <div class="row g-3 mb-4">

            <!-- Students -->

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <div class="text-muted small">
                                    Total Students
                                </div>

                                <h3 class="mb-1">
                                    {{ dashboard.students.total }}
                                </h3>

                                <small class="text-success">
                                    {{ dashboard.students.active }}
                                    Active
                                </small>
                            </div>

                            <div
                                class="dashboard-icon bg-primary-subtle text-primary"
                            >
                                <i class="bi bi-mortarboard"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <!-- Teachers -->

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <div class="text-muted small">
                                    Total Teachers
                                </div>

                                <h3 class="mb-1">
                                    {{ dashboard.teachers.total }}
                                </h3>

                                <small class="text-success">
                                    {{ dashboard.teachers.active }}
                                    Active
                                </small>
                            </div>

                            <div
                                class="dashboard-icon bg-success-subtle text-success"
                            >
                                <i class="bi bi-person-workspace"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <!-- Classes -->

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <div class="text-muted small">
                                    Classes
                                </div>

                                <h3 class="mb-1">
                                    {{ dashboard.academic.classes }}
                                </h3>

                                <small class="text-muted">
                                    {{ dashboard.academic.sections }}
                                    Sections
                                </small>
                            </div>

                            <div
                                class="dashboard-icon bg-info-subtle text-info"
                            >
                                <i class="bi bi-building"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            <!-- Subjects -->

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card dashboard-card h-100">
                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>
                                <div class="text-muted small">
                                    Subjects
                                </div>

                                <h3 class="mb-1">
                                    {{ dashboard.academic.subjects }}
                                </h3>

                                <small class="text-muted">
                                    Academic subjects
                                </small>
                            </div>

                            <div
                                class="dashboard-icon bg-warning-subtle text-warning"
                            >
                                <i class="bi bi-book"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- FINANCE CARDS -->
        <!-- ========================================================= -->

        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="text-muted small">
                            Fees Collected
                        </div>

                        <h4 class="mt-2 mb-0">
                            ₹{{ money(dashboard.fees.collected) }}
                        </h4>

                        <small class="text-muted">
                            Today:
                            ₹{{ money(dashboard.fees.today) }}
                        </small>

                    </div>
                </div>
            </div>


            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="text-muted small">
                            Fee Due
                        </div>

                        <h4 class="mt-2 mb-0 text-danger">
                            ₹{{ money(dashboard.fees.due) }}
                        </h4>

                        <small class="text-muted">
                            {{ dashboard.fees.pending_students }}
                            students pending
                        </small>

                    </div>
                </div>
            </div>


            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="text-muted small">
                            Monthly Income
                        </div>

                        <h4 class="mt-2 mb-0 text-success">
                            ₹{{ money(dashboard.finance.monthly_income) }}
                        </h4>

                        <small class="text-muted">
                            Fees + other income
                        </small>

                    </div>
                </div>
            </div>


            <div class="col-12 col-md-6 col-xl-3">
                <div class="card h-100">
                    <div class="card-body">

                        <div class="text-muted small">
                            Monthly Balance
                        </div>

                        <h4
                            class="mt-2 mb-0"
                            :class="
                                dashboard.finance.monthly_balance >= 0
                                    ? 'text-success'
                                    : 'text-danger'
                            "
                        >
                            ₹{{ money(dashboard.finance.monthly_balance) }}
                        </h4>

                        <small class="text-muted">
                            After expenses
                        </small>

                    </div>
                </div>
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- ATTENDANCE + EXAMS -->
        <!-- ========================================================= -->

        <div class="row g-3 mb-4">

            <!-- Student Attendance -->

            <div class="col-12 col-lg-6">

                <div class="card h-100">

                    <div class="card-header bg-white">
                        <strong>
                            Today's Student Attendance
                        </strong>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-6 col-md-3">
                                <div class="attendance-box present">
                                    <div class="small">
                                        Present
                                    </div>

                                    <strong>
                                        {{
                                            dashboard.student_attendance.present
                                        }}
                                    </strong>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="attendance-box absent">
                                    <div class="small">
                                        Absent
                                    </div>

                                    <strong>
                                        {{
                                            dashboard.student_attendance.absent
                                        }}
                                    </strong>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="attendance-box late">
                                    <div class="small">
                                        Late
                                    </div>

                                    <strong>
                                        {{
                                            dashboard.student_attendance.late
                                        }}
                                    </strong>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="attendance-box leave">
                                    <div class="small">
                                        Leave
                                    </div>

                                    <strong>
                                        {{
                                            dashboard.student_attendance.leave
                                        }}
                                    </strong>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Exams -->

            <div class="col-12 col-lg-6">

                <div class="card h-100">

                    <div
                        class="card-header bg-white d-flex justify-content-between"
                    >
                        <strong>
                            Examination
                        </strong>

                        <span class="badge bg-primary">
                            {{ dashboard.exams.total }}
                            Total
                        </span>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            <div class="col-4 text-center">
                                <div class="text-muted small">
                                    Total
                                </div>

                                <h4>
                                    {{ dashboard.exams.total }}
                                </h4>
                            </div>

                            <div class="col-4 text-center">
                                <div class="text-muted small">
                                    Upcoming
                                </div>

                                <h4 class="text-warning">
                                    {{ dashboard.exams.upcoming }}
                                </h4>
                            </div>

                            <div class="col-4 text-center">
                                <div class="text-muted small">
                                    Published
                                </div>

                                <h4 class="text-success">
                                    {{ dashboard.exams.published }}
                                </h4>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- HOSTEL / INVENTORY / TRANSPORT -->
        <!-- ========================================================= -->

        <div class="row g-3 mb-4">

            <!-- Hostel -->

            <div class="col-12 col-md-4">

                <div class="card h-100">

                    <div class="card-header bg-white">
                        <strong>
                            Hostel
                        </strong>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Buildings</span>
                            <strong>
                                {{ dashboard.hostel.buildings }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Rooms</span>
                            <strong>
                                {{ dashboard.hostel.rooms }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Beds</span>
                            <strong>
                                {{ dashboard.hostel.beds }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Available Beds</span>
                            <strong class="text-success">
                                {{ dashboard.hostel.available_beds }}
                            </strong>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Inventory -->

            <div class="col-12 col-md-4">

                <div class="card h-100">

                    <div class="card-header bg-white">
                        <strong>
                            Inventory
                        </strong>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Items</span>

                            <strong>
                                {{ dashboard.inventory.items }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Low Stock</span>

                            <strong
                                class="text-danger"
                            >
                                {{ dashboard.inventory.low_stock }}
                            </strong>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Transport -->

            <div class="col-12 col-md-4">

                <div class="card h-100">

                    <div class="card-header bg-white">
                        <strong>
                            Transport
                        </strong>
                    </div>

                    <div class="card-body">

                        <div
                            class="d-flex justify-content-between"
                        >
                            <span>
                                Active Vehicles
                            </span>

                            <strong>
                                {{ dashboard.transport.vehicles }}
                            </strong>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- RECENT STUDENTS + UPCOMING EXAMS -->
        <!-- ========================================================= -->

        <div class="row g-3">

            <!-- Recent Students -->

            <div class="col-12 col-lg-7">

                <div class="card">

                    <div class="card-header bg-white">
                        <strong>
                            Recent Students
                        </strong>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table
                                class="table table-hover table-bordered mb-0"
                            >

                                <thead class="table-light">

                                    <tr>
                                        <th>
                                            #
                                        </th>

                                        <th>
                                            Admission No
                                        </th>

                                        <th>
                                            Student
                                        </th>

                                        <th>
                                            Gender
                                        </th>

                                        <th>
                                            Status
                                        </th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <tr
                                        v-if="
                                            dashboard.recent_students.length === 0
                                        "
                                    >
                                        <td
                                            colspan="5"
                                            class="text-center text-muted py-4"
                                        >
                                            No students found.
                                        </td>
                                    </tr>

                                    <tr
                                        v-for="(
                                            student,
                                            index
                                        ) in dashboard.recent_students"
                                        :key="student.id"
                                    >

                                        <td>
                                            {{ index + 1 }}
                                        </td>

                                        <td>
                                            {{ student.admission_no }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{ student.name }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ student.gender }}
                                        </td>

                                        <td>

                                            <span
                                                class="badge"
                                                :class="
                                                    student.status
                                                        ? 'bg-success'
                                                        : 'bg-secondary'
                                                "
                                            >
                                                {{
                                                    student.status
                                                        ? 'Active'
                                                        : 'Inactive'
                                                }}
                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Upcoming Exams -->

            <div class="col-12 col-lg-5">

                <div class="card">

                    <div class="card-header bg-white">
                        <strong>
                            Upcoming Exams
                        </strong>
                    </div>

                    <div class="card-body">

                        <div
                            v-if="
                                dashboard.upcoming_exam_list.length === 0
                            "
                            class="text-center text-muted py-4"
                        >
                            No upcoming exams.
                        </div>

                        <div
                            v-for="exam in dashboard.upcoming_exam_list"
                            :key="exam.id"
                            class="border-bottom pb-3 mb-3"
                        >

                            <div
                                class="d-flex justify-content-between"
                            >

                                <strong>
                                    {{ exam.name }}
                                </strong>

                                <span
                                    class="badge"
                                    :class="
                                        exam.is_result_published
                                            ? 'bg-success'
                                            : 'bg-warning text-dark'
                                    "
                                >
                                    {{
                                        exam.is_result_published
                                            ? 'Published'
                                            : 'Upcoming'
                                    }}
                                </span>

                            </div>

                            <div class="small text-muted mt-1">

                                {{ formatDate(exam.start_date) }}

                                <span
                                    v-if="
                                        exam.end_date &&
                                        exam.end_date !== exam.start_date
                                    "
                                >
                                    -
                                    {{ formatDate(exam.end_date) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    reactive,
    ref,
    onMounted,
    onUnmounted
} from 'vue';

import axios from 'axios';
import Swal from 'sweetalert2';


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const error = ref('');


/*
|--------------------------------------------------------------------------
| Dashboard Data
|--------------------------------------------------------------------------
*/

const dashboard = reactive({

    is_global: false,

    school_name: '',

    school_code: '',

    school_id: null,

    academic_session: null,

    students: {
        total: 0,
        active: 0,
        inactive: 0,
        male: 0,
        female: 0,
        other: 0
    },

    teachers: {
        total: 0,
        active: 0,
        inactive: 0
    },

    academic: {
        classes: 0,
        active_classes: 0,
        sections: 0,
        active_sections: 0,
        subjects: 0
    },

    exams: {
        total: 0,
        published: 0,
        upcoming: 0
    },

    fees: {
        collected: 0,
        due: 0,
        pending_students: 0,
        today: 0,
        monthly: 0
    },

    finance: {
        expenses: 0,
        today_expenses: 0,
        other_income: 0,
        today_other_income: 0,
        monthly_income: 0,
        monthly_expenses: 0,
        monthly_balance: 0
    },

    student_attendance: {
        present: 0,
        absent: 0,
        late: 0,
        leave: 0
    },

    teacher_attendance: {
        present: 0,
        absent: 0,
        late: 0
    },

    hostel: {
        buildings: 0,
        rooms: 0,
        beds: 0,
        occupied_beds: 0,
        available_beds: 0
    },

    inventory: {
        items: 0,
        low_stock: 0
    },

    transport: {
        vehicles: 0
    },

    recent_students: [],

    upcoming_exam_list: []

});


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

const getAuthConfig = () => {

    const token =
        localStorage.getItem('token') ||
        localStorage.getItem('auth_token') ||
        localStorage.getItem('access_token') ||
        localStorage.getItem('authToken') ||
        sessionStorage.getItem('token') ||
        sessionStorage.getItem('auth_token') ||
        sessionStorage.getItem('access_token') ||
        sessionStorage.getItem('authToken');


    const config = {

        withCredentials: true,

        headers: {

            Accept:
                'application/json'

        }

    };


    if (token) {

        config.headers.Authorization =
            `Bearer ${token}`;

    }


    return config;

};


/*
|--------------------------------------------------------------------------
| Load Dashboard
|--------------------------------------------------------------------------
*/

const loadDashboard = async () => {

    loading.value = true;

    error.value = '';


    try {

        const response =
            await axios.get(
                '/api/dashboard',
                getAuthConfig()
            );


        const result =
            response.data?.data || {};


        /*
        |--------------------------------------------------------------------------
        | Safely update nested data
        |--------------------------------------------------------------------------
        */

        dashboard.is_global =
            !!result.is_global;

        dashboard.school_name =
            result.school_name || '';

        dashboard.school_code =
            result.school_code || '';

        dashboard.school_id =
            result.school_id ?? null;


        dashboard.academic_session =
            result.academic_session ?? null;


        dashboard.students =
            {
                ...dashboard.students,
                ...(result.students || {})
            };


        dashboard.teachers =
            {
                ...dashboard.teachers,
                ...(result.teachers || {})
            };


        dashboard.academic =
            {
                ...dashboard.academic,
                ...(result.academic || {})
            };


        dashboard.exams =
            {
                ...dashboard.exams,
                ...(result.exams || {})
            };


        dashboard.fees =
            {
                ...dashboard.fees,
                ...(result.fees || {})
            };


        dashboard.finance =
            {
                ...dashboard.finance,
                ...(result.finance || {})
            };


        dashboard.student_attendance =
            {
                ...dashboard.student_attendance,
                ...(result.student_attendance || {})
            };


        dashboard.teacher_attendance =
            {
                ...dashboard.teacher_attendance,
                ...(result.teacher_attendance || {})
            };


        dashboard.hostel =
            {
                ...dashboard.hostel,
                ...(result.hostel || {})
            };


        dashboard.inventory =
            {
                ...dashboard.inventory,
                ...(result.inventory || {})
            };


        dashboard.transport =
            {
                ...dashboard.transport,
                ...(result.transport || {})
            };


        dashboard.recent_students =
            Array.isArray(
                result.recent_students
            )
                ? result.recent_students
                : [];


        dashboard.upcoming_exam_list =
            Array.isArray(
                result.upcoming_exam_list
            )
                ? result.upcoming_exam_list
                : [];


    } catch (err) {

        console.error(
            'Dashboard loading failed:',
            err
        );


        if (
            err.response?.data?.message
        ) {

            error.value =
                err.response.data.message;

        } else {

            error.value =
                'Unable to load dashboard.';

        }

    } finally {

        loading.value = false;

    }

};


/*
|--------------------------------------------------------------------------
| Money
|--------------------------------------------------------------------------
*/

const money = (value) => {

    const number =
        Number(value || 0);


    return number.toLocaleString(
        'en-IN',
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

};


/*
|--------------------------------------------------------------------------
| Date
|--------------------------------------------------------------------------
*/

const formatDate = (value) => {

    if (!value) {
        return '-';
    }


    const date =
        new Date(value);


    if (
        Number.isNaN(
            date.getTime()
        )
    ) {

        return value;

    }


    return date.toLocaleDateString(
        'en-IN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

};


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

const onTenantSwitched = () => {
    loadDashboard();
};

onMounted(() => {
    loadDashboard();
    window.addEventListener('tenant-switched', onTenantSwitched);

    const lockMsg = sessionStorage.getItem("subscription_lock_msg");
    if (lockMsg) {
        sessionStorage.removeItem("subscription_lock_msg");
        Swal.fire({
            icon: 'warning',
            title: 'Feature Locked',
            text: lockMsg,
            confirmButtonText: 'Understood',
            confirmButtonColor: '#0d6efd',
        });
    }
});

onUnmounted(() => {
    window.removeEventListener('tenant-switched', onTenantSwitched);
});

</script>


<style scoped>

.dashboard-card {
    border: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: transform 0.15s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
}

.dashboard-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 22px;
}

.attendance-box {
    border-radius: 10px;
    padding: 14px;
    text-align: center;
}

.attendance-box strong {
    display: block;
    font-size: 24px;
    margin-top: 4px;
}

.attendance-box.present {
    background: #d1e7dd;
    color: #0f5132;
}

.attendance-box.absent {
    background: #f8d7da;
    color: #842029;
}

.attendance-box.late {
    background: #fff3cd;
    color: #664d03;
}

.attendance-box.leave {
    background: #cff4fc;
    color: #055160;
}

.card {
    border-radius: 10px;
}

</style>