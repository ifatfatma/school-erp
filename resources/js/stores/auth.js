import { defineStore } from "pinia";

import * as authApi from "@/api/auth";

export const useAuthStore = defineStore("auth", {

    state: () => ({

        user: null,

        token: localStorage.getItem("token"),

        loading: false,

    }),

    actions: {

        async login(form) {

            this.loading = true

            try {

                const response = await authApi.login(form)

                this.token = response.data.token
                this.user = response.data.user

                localStorage.setItem("token", this.token)

                // Sync active_school_id in localStorage with the logged-in user's school
                const isSuperAdmin = this.user?.user_type === 'super_admin' || this.user?.roles?.some(r => r.name === 'Super Admin')
                if (!isSuperAdmin) {
                    if (this.user?.school_id) {
                        localStorage.setItem("active_school_id", String(this.user.school_id))
                    } else {
                        localStorage.removeItem("active_school_id")
                    }
                }

                return response

            } finally {

                this.loading = false

            }

        },
        async loadProfile() {

            if (!this.token) return;

            const response = await authApi.profile();

            this.user = response.data.user;

            const isSuperAdmin = this.user?.user_type === 'super_admin' || this.user?.roles?.some(r => r.name === 'Super Admin')
            if (!isSuperAdmin && this.user?.school_id) {
                localStorage.setItem("active_school_id", String(this.user.school_id))
            }
        },
        async logout() {

            try {
                await authApi.logout()
            } catch (error) {
                console.warn('Logout API notification error, proceeding with local logout', error)
            } finally {
                localStorage.removeItem("token")
                localStorage.removeItem("active_school_id")

                this.user = null
                this.token = null
            }

        }

    }

})