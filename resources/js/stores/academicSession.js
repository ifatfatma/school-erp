import { defineStore } from "pinia";
import academicSessionApi from "@/api/academicSession";

export const useAcademicSessionStore = defineStore("academicSession", {

    state: () => ({
        sessions: [],
        pagination: {},
        loading: false,
        errors: {}
    }),

    actions: {

        async load(params = {}) {

            this.loading = true;

            try {

                const { data } = await academicSessionApi.index(params);

                this.sessions = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        async active() {

            this.loading = true;

            try {

                const { data } = await academicSessionApi.active();

                this.sessions = data.data ?? [];

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            const { data } = await academicSessionApi.find(id);

            return data.data ?? data;

        },

        async create(form) {

            return await academicSessionApi.create(form);

        },

        async update(id, form) {

            return await academicSessionApi.update(id, form);

        },

        async delete(id) {

            return await academicSessionApi.delete(id);

        }

    }

});