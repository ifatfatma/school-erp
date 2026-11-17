<template>
    <div class="container-fluid vh-100">
        <div class="row h-100">

            <div
                class="col-md-7 d-none d-md-flex text-white justify-content-center align-items-center position-relative overflow-hidden"
                :style="leftPanelStyle"
            >
                <!-- Elegant Glassmorphic Ambient Glow & Vignette -->
                <div
                    class="position-absolute top-0 start-0 w-100 h-100 pointer-events-none"
                    :style="{
                        background: hasCustomBg
                            ? 'radial-gradient(circle at center, transparent 30%, rgba(10, 15, 30, 0.55) 100%)'
                            : 'radial-gradient(circle at top left, rgba(255,255,255,0.12) 0%, transparent 60%)'
                    }"
                ></div>

                <div class="text-center p-4 p-lg-5 position-relative z-1" style="max-width: 620px;">

                    <!-- Pill Badge -->
                    <div class="mb-3">
                        <span class="badge bg-white bg-opacity-20 text-white backdrop-blur border border-white border-opacity-25 px-3 py-2 rounded-pill text-uppercase fw-semibold shadow-sm" style="letter-spacing: 1.5px; font-size: 0.72rem;">
                            <i class="bi bi-mortarboard-fill me-1 text-warning"></i> Official School Portal
                        </span>
                    </div>

                    <!-- School Logo with Modern Glassmorphism Frame -->
                    <div v-if="schoolInfo?.logo" class="mb-3">
                        <div class="d-inline-flex p-3 rounded-4 bg-white shadow-lg border border-white border-opacity-75">
                            <img
                                :src="schoolInfo.logo"
                                alt="School Logo"
                                style="max-height: 85px; max-width: 160px; object-fit: contain;"
                            />
                        </div>
                    </div>

                    <!-- School Name -->
                    <h1 class="display-4 fw-black text-white mb-2" style="font-weight: 800; text-shadow: 0 4px 16px rgba(0,0,0,0.45); letter-spacing: -0.5px;">
                        {{ schoolInfo?.name || 'School ERP' }}
                    </h1>

                    <!-- Location / Subtitle -->
                    <p class="lead text-white-50 mb-4" style="text-shadow: 0 2px 8px rgba(0,0,0,0.35); font-size: 1.15rem;">
                        <i class="bi bi-geo-alt-fill text-danger me-1" v-if="schoolInfo?.city"></i>
                        {{ schoolInfo?.city ? `${schoolInfo.city}, ${schoolInfo.state || ''}` : 'Complete School Management System' }}
                    </p>

                    <!-- Feature Badges -->
                    <div class="d-flex justify-content-center gap-2 flex-wrap pt-2">
                        <span class="badge bg-white bg-opacity-15 border border-white border-opacity-20 text-white rounded-pill px-3 py-2 small shadow-sm">
                            <i class="bi bi-mortarboard text-warning me-1"></i> Academic Hub
                        </span>
                        <span class="badge bg-white bg-opacity-15 border border-white border-opacity-20 text-white rounded-pill px-3 py-2 small shadow-sm">
                            <i class="bi bi-fingerprint text-info me-1"></i> Biometrics & AI
                        </span>
                        <span class="badge bg-white bg-opacity-15 border border-white border-opacity-20 text-white rounded-pill px-3 py-2 small shadow-sm">
                            <i class="bi bi-shield-check text-success me-1"></i> Cloud ERP
                        </span>
                    </div>

                </div>

                <!-- Subtle bottom brand copyright -->
                <div class="position-absolute bottom-0 start-0 w-100 p-3 text-center text-white-50 small z-1" style="font-size: 0.78rem;">
                    &copy; {{ new Date().getFullYear() }} {{ schoolInfo?.name || 'School ERP' }} &bull; All Rights Reserved
                </div>
            </div>

            <div class="col-md-5 d-flex align-items-center justify-content-center">

                <div class="card shadow-lg border-0 w-75">

                    <div class="card-body p-5">

                        <div class="text-center mb-4">
                            <img
                                v-if="schoolInfo?.logo"
                                :src="schoolInfo.logo"
                                alt="Logo"
                                class="mb-2 rounded"
                                style="max-height: 48px; object-fit: contain;"
                            />
                            <h3 class="mb-0">
                                Login
                            </h3>
                            <small class="text-muted">Enter credentials to continue</small>
                        </div>

                        <form @submit.prevent="submit">

                            <div class="mb-3">

                                <label>Email or Admission No</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="form.email"
                                    placeholder="email@example.com or admission no"
                                    required
                                >

                            </div>

                            <div class="mb-4">

                                <label>Password</label>

                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    class="form-control"
                                    v-model="form.password"
                                >

                                <div class="form-check mt-2">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        v-model="showPassword"
                                    >

                                    <label class="form-check-label">
                                        Show Password
                                    </label>

                                </div>

                            </div>

                            <button
                                class="btn btn-primary w-100"
                                :disabled="loading"
                            >

                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm"
                                ></span>

                                {{ loading ? 'Please wait...' : 'Login' }}

                            </button>

                            <div class="mt-4 pt-3 border-top text-center">
                                <small class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.75rem;">QUICK DEMO CREDENTIALS (PASSWORD: <code>12345678</code>)</small>
                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                    <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2" style="font-size: 0.75rem;" @click="fillCreds('admin@school.com')">
                                        <i class="bi bi-shield-lock me-1"></i>Super Admin
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" @click="fillCreds('admin_school1@school.com')">
                                        <i class="bi bi-building me-1"></i>School 1 Admin
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2" style="font-size: 0.75rem;" @click="fillCreds('admin_school2@school.com')">
                                        <i class="bi bi-building me-1"></i>School 2 Admin
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>
</template>

<script setup>

import { reactive, ref, computed, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";

import Swal from "sweetalert2";
import api from "@/api/axios";

import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const loading = ref(false);
const showPassword = ref(false);
const schoolInfo = ref(null);

const hasCustomBg = computed(() => {
    const bg = schoolInfo.value?.login_background;
    return typeof bg === 'string' && bg.trim() !== '' && bg !== 'null' && bg !== '[]' && bg !== '{}';
});

const leftPanelStyle = computed(() => {
    if (hasCustomBg.value) {
        return {
            backgroundImage: `linear-gradient(135deg, rgba(15, 23, 42, 0.78) 0%, rgba(30, 58, 138, 0.68) 100%), url('${schoolInfo.value.login_background}')`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
        };
    }
    return {
        background: 'linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1d4ed8 100%)',
    };
});

const schoolCodeParam = computed(() => {
    return route.params.schoolCode || route.query.school || "";
});

onMounted(async () => {
    const code = schoolCodeParam.value;
    if (code === 'S001') {
        form.email = 'admin_school1@school.com';
    } else if (code === 'S002') {
        form.email = 'admin_school2@school.com';
    }
    try {
        const { data } = await api.get('/public/school-info', {
            params: code ? { school: code } : {}
        });
        schoolInfo.value = data.data ?? data;
    } catch (e) {
        // Fallback
    }
});

const form = reactive({
    email: "admin@school.com",
    password: "12345678"
});

function fillCreds(email) {
    form.email = email;
    form.password = "12345678";
}

async function submit(){

    loading.value = true;

    try{

        const response = await auth.login(form);

        Swal.fire({
            icon:"success",
            title:"Welcome",
            timer:1000,
            showConfirmButton:false
        });

        const loggedUser = response?.data?.user || auth.user;
        const isParent = loggedUser?.user_type === 'parent' || loggedUser?.roles?.some(r => r.name?.toLowerCase() === 'parent');
        const isStudent = loggedUser?.user_type === 'student' || loggedUser?.roles?.some(r => r.name?.toLowerCase() === 'student');

        if (isParent) {
            router.push("/parent/dashboard");
        } else if (isStudent) {
            router.push("/portal/dashboard");
        } else {
            router.push("/dashboard");
        }

    }catch(error){

        console.error("Login error:", error);

        Swal.fire({

            icon:"error",

            title:"Login Failed",

            text:error.response?.data?.message ?? "Invalid Credentials"

        });

    }

    loading.value = false;

}

</script>

<style scoped>
.backdrop-blur {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
.text-shadow {
    text-shadow: 0 4px 16px rgba(0, 0, 0, 0.45);
}
</style>