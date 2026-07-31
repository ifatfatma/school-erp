import { defineStore } from "pinia";
import leaveRequestApi from "@/api/leaveRequest";

export const useLeaveRequestStore = defineStore("leaveRequest", {

    state: () => ({

        items: [],

        item: null,

        loading: false,

        errors: {},

        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0,
        }

    }),

    actions: {

        /**
         * Fetch List
         */
        async fetch(params = {}) {

            this.loading = true;

            try {

                const response = await leaveRequestApi.getAll(params);

                this.items = response.data.data;

                if (response.data.meta) {

                    this.pagination = {
                        current_page: response.data.meta.current_page,
                        last_page: response.data.meta.last_page,
                        per_page: response.data.meta.per_page,
                        total: response.data.meta.total,
                    };

                }

                return response;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Get Single
         */
        async get(id) {

            this.loading = true;

            try {

                const response = await leaveRequestApi.get(id);

                this.item = response.data.data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create
         */
        async create(data) {

            this.loading = true;
            this.errors = {};

            try {

                const response = await leaveRequestApi.create(data);

                return response.data;

            } catch (error) {

                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }

                throw error;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Update
         */
        async update(id, data) {

            this.loading = true;
            this.errors = {};

            try {

                const response = await leaveRequestApi.update(id, data);

                return response.data;

            } catch (error) {

                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors;
                }

                throw error;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Delete
         */
        async delete(id) {

            this.loading = true;

            try {

                const response = await leaveRequestApi.delete(id);

                return response.data;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Approve Leave
         */
        async approve(id, remarks = "") {

            this.loading = true;

            try {

                const response = await leaveRequestApi.approve(id, remarks);

                return response.data;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Reject Leave
         */
        async reject(id, remarks = "") {

            this.loading = true;

            try {

                const response = await leaveRequestApi.reject(id, remarks);

                return response.data;

            } finally {

                this.loading = false;

            }

        }

    }

});