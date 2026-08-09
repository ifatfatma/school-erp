import { defineStore } from "pinia";
import examMarkApi from "@/api/examMark";

export const useExamMarkStore = defineStore("examMark", {

    state: () => ({

        schedule: null,

        students: [],

        marks: {},

        loading: false,

        saving: false,

        errors: {}

    }),

    actions: {

        async load(filters = {}) {

            this.loading = true;

            this.errors = {};

            try {

                const { data } = await examMarkApi.load(filters);

                this.schedule = data.data?.schedule ?? null;

                this.students = data.data?.students ?? [];

                this.marks = data.data?.marks ?? {};

                console.log("Marks Entry Response:", data);

                console.log("Students:", this.students);

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.loading = false;

            }

        },

        async save(payload) {

            this.saving = true;

            this.errors = {};

            try {

                return await examMarkApi.save(payload);

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.saving = false;

            }

        },

        async student(studentId) {

            return await examMarkApi.student(studentId);

        }

    }

});