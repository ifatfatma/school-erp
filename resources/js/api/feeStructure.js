import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get(
            "/fee-structures",
            { params }
        );
    },

    get(id) {
        return api.get(
            `/fee-structures/${id}`
        );
    },

    create(data) {
        return api.post(
            "/fee-structures",
            data
        );
    },

    update(id, data) {
        return api.put(
            `/fee-structures/${id}`,
            data
        );
    },

    delete(id) {
        return api.delete(
            `/fee-structures/${id}`
        );
    },

};