import { defineStore } from "pinia";

import {
    getAcademicSessions,
    getAcademicSession,
    createAcademicSession,
    updateAcademicSession,
    deleteAcademicSession
} from "@/api/academicSession";

export const useAcademicSessionStore = defineStore("academicSession", {

    state: () => ({
        sessions: [],
        pagination: {},
        loading: false
    }),

    actions: {

        async load(params = {}) {

            this.loading = true;

            try {

                const { data } = await getAcademicSessions(params);

                this.sessions = data.data;

                this.pagination = data.meta;

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            const { data } = await getAcademicSession(id);

            return data.data ?? data;

        },

        async create(form) {

            return await createAcademicSession(form);

        },

        async update(id, form) {

            return await updateAcademicSession(id, form);

        },

        async delete(id) {

            return await deleteAcademicSession(id);

        }

    }

});