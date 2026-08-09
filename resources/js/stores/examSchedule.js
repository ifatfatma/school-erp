import { defineStore } from "pinia";
import examScheduleApi from "@/api/examSchedule";

export const useExamScheduleStore = defineStore("examSchedule", {

    state: () => ({

        items: [],

        item: null,

        pagination: {},

        loading: false,

        saving: false,

        errors: {},

    }),

    actions: {

        /**
         * List
         */
        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await examScheduleApi.getAll(params);

                this.items = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        /**
         * Active
         */
        async active() {

            const { data } = await examScheduleApi.active();

            this.items = data.data ?? [];

        },

        /**
         * Find
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await examScheduleApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create
         */
        async create(form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await examScheduleApi.create(form);

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
         * Update
         */
        async update(id, form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await examScheduleApi.update(id, form);

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
         * Delete
         */
        async delete(id) {

            await examScheduleApi.delete(id);

        },

        clearErrors() {

            this.errors = {};

        },

        reset() {

            this.items = [];

            this.item = null;

            this.pagination = {};

            this.errors = {};

        }

    }

});