import { defineStore } from "pinia";
import studentAttendanceApi from "@/api/studentAttendance";

export const useStudentAttendanceStore = defineStore("studentAttendance", {

    state: () => ({

        items: [],
        item: null,

        monthlyItems: [],

        summary: {},

        pagination: {},

        loading: false,
        saving: false,

        errors: {},

    }),

    actions: {

        /**
         * Attendance List
         */
        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await studentAttendanceApi.getAll(params);
               
                this.items = data.data;
                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        /**
         * Find Attendance
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await studentAttendanceApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create Attendance
         */
        async create(form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await studentAttendanceApi.create(form);

                return data;

            } catch (error) {

                if (error.response?.status === 422) {

                    this.errors = error.response.data.errors;

                }

                throw error;

            } finally {

                this.saving = false;

            }

        },

        /**
         * Update Attendance
         */
        async update(id, form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await studentAttendanceApi.update(id, form);

                return data;

            } catch (error) {

                if (error.response?.status === 422) {

                    this.errors = error.response.data.errors;

                }

                throw error;

            } finally {

                this.saving = false;

            }

        },

        /**
         * Delete Attendance
         */
        async delete(id) {

            await studentAttendanceApi.delete(id);

        },

        /**
         * Bulk Attendance Save
         */
        async bulk(form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await studentAttendanceApi.bulk(form);

                return data;

            } catch (error) {

                if (error.response?.status === 422) {

                    this.errors = error.response.data.errors;

                }

                throw error;

            } finally {

                this.saving = false;

            }

        },

        /**
         * Monthly Attendance
         */
        async monthly(params) {

            this.loading = true;

            try {

                const { data } = await studentAttendanceApi.monthly(params);

                this.monthlyItems = data.data ?? data;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Attendance Summary
         */
        async summaryReport(params) {

            this.loading = true;

            try {

                const { data } = await studentAttendanceApi.summary(params);

                this.summary = data;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Clear Validation Errors
         */
        clearErrors() {

            this.errors = {};

        },

        /**
         * Reset Store
         */
        reset() {

            this.item = null;
            this.items = [];
            this.monthlyItems = [];
            this.summary = {};
            this.errors = {};

        }

    }

});