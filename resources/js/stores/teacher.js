import { defineStore } from "pinia";
import teacherApi from "@/api/teacher";

export const useTeacherStore = defineStore("teacher", {

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

                const { data } = await teacherApi.getAll(params);

                this.items = data.data;

                this.pagination = data.meta;

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            this.loading = true;

            try {

                const { data } = await teacherApi.get(id);

                this.item = data.data ?? data;

                return this.item;

            } finally {

                this.loading = false;

            }

        },

        async create(payload) {

            this.saving = true;

            this.errors = {};

            try {

                return await teacherApi.create(payload);

            } catch (e) {

                if (e.response?.status === 422) {

                    this.errors = e.response.data.errors;

                }

                throw e;

            } finally {

                this.saving = false;

            }

        },

        async update(id, payload) {

            this.saving = true;

            this.errors = {};

            try {

                return await teacherApi.update(id, payload);

            } catch (e) {

                if (e.response?.status === 422) {

                    this.errors = e.response.data.errors;

                }

                throw e;

            } finally {

                this.saving = false;

            }

        },

        async delete(id) {

            await teacherApi.delete(id);

        }

    }

});