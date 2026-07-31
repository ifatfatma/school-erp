import { defineStore } from "pinia";
import leaveTypeApi from "@/api/leaveType";

export const useLeaveTypeStore = defineStore("leaveType", {

    state: () => ({
        items: [],
        item: null,

        loading: false,

        pagination: {},

        errors: {},
    }),

    actions: {

        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await leaveTypeApi.getAll(params);

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

        async get(id) {

            const { data } = await leaveTypeApi.get(id);

            this.item = data.data ?? data;

            return this.item;

        },

        async create(form) {

            this.errors = {};

            try {

                return await leaveTypeApi.create(form);

            } catch (e) {

                if (e.response?.status === 422) {

                    this.errors = e.response.data.errors;

                }

                throw e;

            }

        },

        async update(id, form) {

            this.errors = {};

            try {

                return await leaveTypeApi.update(id, form);

            } catch (e) {

                if (e.response?.status === 422) {

                    this.errors = e.response.data.errors;

                }

                throw e;

            }

        },

        async delete(id) {

            return await leaveTypeApi.delete(id);

        },

    },

});