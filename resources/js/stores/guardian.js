import { defineStore } from "pinia";
import guardianApi from "@/api/guardian";

export const useGuardianStore = defineStore("guardian", {

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
         * Load Guardian List
         */
        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await guardianApi.getAll(params);

                this.items = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        /**
         * Find Guardian
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await guardianApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create Guardian
         */
        async create(form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await guardianApi.create(form);

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
         * Update Guardian
         */
        async update(id, form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await guardianApi.update(id, form);

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
         * Delete Guardian
         */
        async delete(id) {

            await guardianApi.delete(id);

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

            this.pagination = {};

            this.errors = {};

        }

    }

});