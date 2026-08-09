import { defineStore } from "pinia";

import gradeApi from "@/api/grade";

export const useGradeStore = defineStore("grade", {

    state: () => ({
        items: [],
        pagination: {},
        loading: false,
        saving: false,
        errors: {},
    }),

    actions: {

        async fetch(params = {}) {

            this.loading = true;
            this.errors = {};

            try {

                const { data } = await gradeApi.getAll(params);

                this.items = data.data ?? [];
                this.pagination = data.meta ?? {};

                return data;

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.loading = false;
            }
        },

        async find(id) {

            this.loading = true;
            this.errors = {};

            try {

                const { data } = await gradeApi.get(id);

                return data.data ?? data;

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.loading = false;
            }
        },

        async create(form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } =
                    await gradeApi.create(form);

                return data;

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.saving = false;
            }
        },

        async update(id, form) {

            this.saving = true;
            this.errors = {};

            try {

                const { data } =
                    await gradeApi.update(id, form);

                return data;

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.saving = false;
            }
        },

        async remove(id) {

            this.loading = true;
            this.errors = {};

            try {

                return await gradeApi.delete(id);

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.loading = false;
            }
        },

        async active() {

            const { data } =
                await gradeApi.active();

            return data.data ?? [];

        },

    },

});