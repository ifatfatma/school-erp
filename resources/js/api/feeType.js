import api from "@/api/axios";

export default {

    getAll(params = {}) {

        return api.get(
            "/fee-types",
            {
                params
            }
        );

    },


    get(id) {

        return api.get(
            `/fee-types/${id}`
        );

    },


    create(data) {

        return api.post(
            "/fee-types",
            data
        );

    },


    update(id, data) {

        return api.put(
            `/fee-types/${id}`,
            data
        );

    },


    delete(id) {

        return api.delete(
            `/fee-types/${id}`
        );

    },

};