import { defineStore } from "pinia";

import subjectApi from "@/api/subject";

export const useSubjectStore = defineStore("subject", {
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
         * Load subject List
         */
        async load(params = {}) {
            this.loading = true;

            try {

                const { data } = await subjectApi.getAll(params);

                this.items = data.data;
                this.pagination = data.meta;

            } finally {

                this.loading = false;

            }
        },

        /**
         * Get Single subject
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await subjectApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Create subject
         */
        async create(form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await subjectApi.create(form);

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
         * Update subject
         */
        async update(id, form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } = await subjectApi.update(id, form);

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
         * Delete subject
         */
        async delete(id) {

            await subjectApi.delete(id);

        },

        /**
         * Clear Form Errors
         */
        clearErrors() {

            this.errors = {};

        },

        async active() {
            
                        const { data } = await subjectApi.active();
            
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