import { defineStore } from "pinia";
import studentPromotionApi from "@/api/studentPromotion";

export const useStudentPromotionStore = defineStore("studentPromotion", {

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
         * Load Promotions
         */
        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await studentPromotionApi.getAll(params);

                this.items = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        /**
         * Get Promotion
         */
        async find(id) {

            this.loading = true;

            try {

                const { data } = await studentPromotionApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        /**
         * Single Promotion
         */
        async create(form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await studentPromotionApi.create(form);

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
         * Bulk Promotion
         */
        async bulkPromotion(form) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await studentPromotionApi.bulkPromotion(form);

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
         * Delete Promotion
         */
        async delete(id) {

            await studentPromotionApi.delete(id);

        },

        clearErrors() {

            this.errors = {};

        },

        reset() {

            this.item = null;

            this.items = [];

            this.pagination = {};

            this.errors = {};

        }

    }

});