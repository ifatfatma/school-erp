import { defineStore } from "pinia";
import timetableApi from "@/api/timetable";

export const useTimetableStore = defineStore("timetable", {

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

                const { data } = await timetableApi.getAll(params);

                this.items = data.data;

                this.pagination = {
                    current_page: data.meta.current_page,
                    last_page: data.meta.last_page,
                    per_page: data.meta.per_page,
                    total: data.meta.total,
                };

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            this.loading = true;

            try {

                const { data } = await timetableApi.get(id);

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

                const { data } = await timetableApi.create(payload);

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

        async update(id, payload) {

            this.saving = true;

            this.errors = {};

            try {

                const { data } = await timetableApi.update(id, payload);

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

        async delete(id) {

            await timetableApi.delete(id);

        },

    },

});