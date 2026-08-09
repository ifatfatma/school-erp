import { defineStore } from "pinia";
import examApi from "@/api/exam";

export const useExamStore = defineStore("exam", {

    state: () => ({

        items: [],

        item: null,

        pagination: {},

        loading: false,

        saving: false,

        errors: {},

    }),

    actions: {

        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await examApi.getAll(params);

                this.items = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        async active() {

            const { data } = await examApi.active();

            this.items = data.data ?? [];

        },

        async find(id) {

            this.loading = true;

            try {

                const { data } = await examApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        async create(form) {

            this.saving = true;

            this.errors = {};

            try {

                return await examApi.create(form);

            } catch (error) {

                if (error.response?.status === 422) {

                    this.errors = error.response.data.errors;

                }

                throw error;

            } finally {

                this.saving = false;

            }

        },

        async update(id, form) {

            this.saving = true;

            this.errors = {};

            try {

                return await examApi.update(id, form);

            } catch (error) {

                if (error.response?.status === 422) {

                    this.errors = error.response.data.errors;

                }

                throw error;

            } finally {

                this.saving = false;

            }

        },

        async delete(id) {

            await examApi.delete(id);

        },

        async lockMarks(id) {

            await examApi.lockMarks(id);

        },

        async unlockMarks(id) {

            await examApi.unlockMarks(id);

        },

        async publishResult(id) {

            await examApi.publishResult(id);

        },

        async unpublishResult(id) {

            await examApi.unpublishResult(id);

        },

        clearErrors() {

            this.errors = {};

        }

    }

});