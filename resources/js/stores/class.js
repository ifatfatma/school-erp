import { defineStore } from "pinia";
import classApi from "@/api/class";

export const useClassStore = defineStore("class", {

    state: () => ({
        items: [],
        item: {},
        loading: false,
    }),

    actions: {

        async fetch(params = {}) {

            this.loading = true;

            try {

                const { data } = await classApi.getAll(params);

                this.items = data.data;

            } finally {

                this.loading = false;

            }

        },

        async find(id) {

            const { data } = await classApi.get(id);

            this.item = data.data;

            return this.item;

        }

    }

});