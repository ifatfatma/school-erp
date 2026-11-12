import { defineStore } from "pinia";
import api from "@/api/axios";
import { useAuthStore } from "@/stores/auth";

export const useTenantStore = defineStore("tenant", {
    state: () => ({
        activeSchoolId: localStorage.getItem("active_school_id") || null,
        currentTenant: null,
        isGlobal: false,
        tenants: [],
        isSuperAdmin: false,
        stats: null,
        loading: false,
    }),

    getters: {
        activeSchoolName(state) {
            if (state.isGlobal || state.activeSchoolId === "all") {
                return "All Schools (Global Mode)";
            }
            if (state.currentTenant) {
                return state.currentTenant.name;
            }
            return "School Tenant";
        },

        activeSchoolLogo(state) {
            return state.currentTenant?.logo || null;
        },
    },

    actions: {
        async fetchTenants() {
            try {
                const response = await api.get("/tenants");
                this.tenants = response.data.data || [];
                this.isSuperAdmin = !!response.data.is_super_admin;
            } catch (e) {
                console.error("Failed fetching tenants list:", e);
            }
        },

        async fetchCurrent() {
            this.loading = true;
            try {
                const response = await api.get("/tenants/current");
                this.isGlobal = !!response.data.is_global;
                this.currentTenant = response.data.tenant || null;
                this.stats = response.data.stats || null;

                if (this.currentTenant) {
                    this.activeSchoolId = String(this.currentTenant.id);
                    localStorage.setItem("active_school_id", String(this.currentTenant.id));
                    api.defaults.headers.common["X-School-Id"] = String(this.currentTenant.id);
                } else if (this.isGlobal) {
                    this.activeSchoolId = "all";
                    localStorage.setItem("active_school_id", "all");
                    delete api.defaults.headers.common["X-School-Id"];
                }
            } catch (e) {
                console.error("Failed fetching current tenant:", e);
            } finally {
                this.loading = false;
            }
        },

        async switchTenant(schoolId) {
            this.loading = true;
            try {
                const payload = {
                    school_id: schoolId === "all" || !schoolId ? "all" : schoolId
                };
                const response = await api.post("/tenants/switch", payload);

                if (schoolId === "all" || !schoolId) {
                    this.activeSchoolId = "all";
                    localStorage.setItem("active_school_id", "all");
                    this.isGlobal = true;
                    this.currentTenant = null;
                    delete api.defaults.headers.common["X-School-Id"];
                } else {
                    this.activeSchoolId = String(schoolId);
                    localStorage.setItem("active_school_id", String(schoolId));
                    this.isGlobal = false;
                    this.currentTenant = response.data.tenant || null;
                    api.defaults.headers.common["X-School-Id"] = String(schoolId);
                }

                // Immediately update authStore user school object
                try {
                    const authStore = useAuthStore();
                    if (authStore.user) {
                        authStore.user.school = this.currentTenant;
                        authStore.user.school_id = this.currentTenant?.id || null;
                    }
                } catch (e) {}

                // Notify all components across the app that school branding/tenant switched
                window.dispatchEvent(new CustomEvent("tenant-switched", {
                    detail: {
                        school_id: this.activeSchoolId,
                        school: this.currentTenant,
                    }
                }));

                return response.data;
            } finally {
                this.loading = false;
            }
        },
    },
});
