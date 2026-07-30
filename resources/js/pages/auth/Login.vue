<template>
    <div class="container-fluid vh-100">
        <div class="row h-100">

            <div class="col-md-7 d-none d-md-flex bg-primary text-white justify-content-center align-items-center">
                <div class="text-center">

                    <h1 class="display-4 fw-bold">
                        School ERP
                    </h1>

                    <p class="lead">
                        Complete School Management System
                    </p>

                </div>
            </div>

            <div class="col-md-5 d-flex align-items-center justify-content-center">

                <div class="card shadow-lg border-0 w-75">

                    <div class="card-body p-5">

                        <h3 class="text-center mb-4">
                            Login
                        </h3>

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

                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>
</template>

<script setup>

import { reactive, ref } from "vue";

import { useRouter } from "vue-router";

import Swal from "sweetalert2";

import { useAuthStore } from "@/stores/auth";

const router = useRouter();

const auth = useAuthStore();

const loading = ref(false);

const showPassword = ref(false);

const form = reactive({

    email: "admin@school.com",

    password: "12345678"

});

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