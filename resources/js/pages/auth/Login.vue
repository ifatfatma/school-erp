<template>
    <div class="container-fluid vh-100">
        <div class="row h-100">

            <div class="col-md-7 d-none d-md-flex bg-primary text-white justify-content-center align-items-center">
                <div class="text-center p-4">

                    <img
                        v-if="schoolInfo?.logo"
                        :src="schoolInfo.logo"
                        alt="School Logo"
                        class="mb-3 rounded bg-white p-2 shadow"
                        style="max-height: 90px; max-width: 140px; object-fit: contain;"
                    />

                    <h1 class="display-5 fw-bold">
                        {{ schoolInfo?.name || 'School ERP' }}
                    </h1>

                    <p class="lead">
                        {{ schoolInfo?.city ? `${schoolInfo.city}, ${schoolInfo.state || ''}` : 'Complete School Management System' }}
                    </p>

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

                                <label>Email</label>

                                <input
                                    type="email"
                                    class="form-control"
                                    v-model="form.email"
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

        await auth.login(form);

        Swal.fire({

            icon:"success",

            title:"Welcome",

            timer:1000,

            showConfirmButton:false

        });

        router.push("/dashboard");

    }catch(error){

        Swal.fire({

            icon:"error",

            title:"Login Failed",

            text:error.response?.data?.message ?? "Invalid Credentials"

        });

    }

    loading.value = false;

}

</script>