import { defineStore } from "pinia";
import examMarkApi from "@/api/examMark";

export const useExamMarkStore = defineStore("examMark", {

    state: () => ({
        schedule: null,
        students: [],
        marks: [],

        loading: false,
        saving: false,
        errors: {},
    }),

    actions: {

        async load(params = {}) {

            this.loading = true;
            this.errors = {};

            try {

                console.log("Marks filters:", params);

                const { data } =
                    await examMarkApi.load(params);

                console.log(
                    "Marks Entry Response:",
                    data
                );

                // IMPORTANT
                this.schedule =
                    data.data?.schedule ?? null;

                this.students =
                    data.data?.students ?? [];

                this.marks =
                    data.data?.marks ?? [];

                console.log(
                    "Loaded Schedule:",
                    this.schedule
                );

                console.log(
                    "Loaded Schedule ID:",
                    this.schedule?.id
                );

                console.log(
                    "Students:",
                    this.students
                );

                return data;

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.loading = false;
            }
        },

        async save(data) {

            this.saving = true;
            this.errors = {};

            try {

                return await examMarkApi.save(data);

            } catch (error) {

                this.errors =
                    error.response?.data?.errors ?? {};

                throw error;

            } finally {

                this.saving = false;
            }
        },
    },
});