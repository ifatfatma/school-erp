import { defineStore } from "pinia";
import studentApi from "@/api/student";

export const useStudentStore = defineStore("student", {

    state: () => ({

        items: [],

        allSchools: [],

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

                const { data } = await studentApi.getAll(params);

                this.items = data.data;

                this.pagination = data.meta;

                if (data.all_schools) {
                    this.allSchools = data.all_schools;
                }

                return data;

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            this.loading = true;

            try {

                const { data } = await studentApi.get(id);

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

                return await studentApi.create(payload);

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

                return await studentApi.update(id, payload);

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

            await studentApi.delete(id);

        }

    }

});