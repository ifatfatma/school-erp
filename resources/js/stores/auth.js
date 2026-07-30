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

                return response

            } finally {

                this.loading = false

            }

        },
        async loadProfile() {

            if (!this.token) return;

            const response = await authApi.profile();

            this.user = response.data.user;
        },
        async logout() {

            await authApi.logout()

            localStorage.removeItem("token")

            this.user = null

            this.token = null

        }

    }

})