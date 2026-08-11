import { defineStore } from "pinia";

import resultGradeApi from "@/api/resultGrade";

export const useResultGradeStore = defineStore(
    "resultGrade",
    {

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

                    const { data } =
                        await resultGradeApi.getAll(
                            params
                        );

                    this.items =
                        data.data ?? [];

                    this.pagination =
                        data.meta ?? {};

                    return data;

                } catch (error) {

                    this.errors =
                        error.response?.data?.errors
                        ?? {};

                    throw error;

                } finally {

                    this.loading = false;
                }
            },

            async find(id) {

                this.loading = true;

                try {

                    const { data } =
                        await resultGradeApi.get(id);

                    return data.data ?? data;

                } finally {

                    this.loading = false;
                }
            },

            async create(form) {

                this.saving = true;
                this.errors = {};

                try {

                    const { data } =
                        await resultGradeApi.create(
                            form
                        );

                    return data;

                } catch (error) {

                    this.errors =
                        error.response?.data?.errors
                        ?? {};

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
                        await resultGradeApi.update(
                            id,
                            form
                        );

                    return data;

                } catch (error) {

                    this.errors =
                        error.response?.data?.errors
                        ?? {};

                    throw error;

                } finally {

                    this.saving = false;
                }
            },

            async remove(id) {

                this.loading = true;

                try {

                    return await resultGradeApi.delete(
                        id
                    );

                } finally {

                    this.loading = false;
                }
            },

            async active() {

                const { data } =
                    await resultGradeApi.active();

                return data.data ?? [];
            },

        },

    }
);