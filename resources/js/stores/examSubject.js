import { defineStore } from "pinia";
import api from "@/api/examSubject";

export const useExamSubjectStore = defineStore("examSubject", {

    state: () => ({

        items: [],

        item: null,

        pagination: {},

        loading: false,

        saving: false,

        errors: {}

    }),

    actions: {

        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await api.getAll(params);

                this.items = data.data ?? [];

                this.pagination = data.meta ?? {};

            } finally {

                this.loading = false;

            }

        },

        async active() {

            const { data } = await api.active();

            this.items = data.data ?? [];

        },

        async byExamAndClass(examId, classId) {

            const { data } = await api.byExamAndClass(
                examId,
                classId
            );

            this.items = data.data ?? [];

        },

        async find(id) {

            this.loading = true;

            try {

                const { data } = await api.get(id);

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

                return await api.create(form);

            } catch (e) {

                if (e.response?.status === 422) {

                    this.errors = e.response.data.errors;

                }

                throw e;

            } finally {

                this.saving = false;

            }

        },

        async update(id, form) {

            this.saving = true;

            this.errors = {};

            try {

                return await api.update(id, form);

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

            await api.delete(id);

        }

    }

});