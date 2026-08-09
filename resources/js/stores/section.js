import { defineStore } from "pinia";

import sectionApi from "@/api/section";

export const useSectionStore = defineStore("section", {
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
         * Load Section List
         */
        async load(params = {}) {
            this.loading = true;

            try {

                const { data } = await sectionApi.getAll(params);

                this.items = data.data;
                this.pagination = data.meta;

            } finally {

                this.loading = false;

            }
        },

        /**
         * Get Single Section
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await sectionApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create Section
         */
        async create(form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await sectionApi.create(form);

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
         * Update Section
         */
        async update(id, form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await sectionApi.update(id, form);

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
         * Delete Section
         */
        async delete(id) {

            await sectionApi.delete(id);

        },

        /**
         * Clear Form Errors
         */
        clearErrors() {

            this.errors = {};

        },

        async active() {
            
                        const { data } = await sectionApi.active();
            
                        this.items = data.data ?? [];
            
                    },

        /**
         * Reset Current Item
         */
        reset() {

            this.item = null;

            this.errors = {};

        }

    },
});