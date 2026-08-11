import { defineStore } from "pinia";
import examRankApi from "@/api/examRank";

export const useExamRankStore = defineStore(
    "examRank",
    {
        state: () => ({
            items: [],

            loading: false,

            calculating: false,

            errors: {},
        }),

        actions: {

            async calculate(payload) {

                this.calculating = true;
                this.errors = {};

                try {

                    const { data } =
                        await examRankApi.calculate(
                            payload
                        );

                    this.items =
                        data.data ?? [];

                    return data;

                } catch (error) {

                    this.errors =
                        error.response?.data?.errors
                        ?? {};

                    throw error;

                } finally {

                    this.calculating = false;

                }
            },

        },
    }
);