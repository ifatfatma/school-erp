import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/leave-types", {
            params,
        });
    },

    get(id) {
        return api.get(`/leave-types/${id}`);
    },

    create(data) {
        return api.post("/leave-types", data);
    },

    update(id, data) {
        return api.put(`/leave-types/${id}`, data);
    },

    delete(id) {
        return api.delete(`/leave-types/${id}`);
    },

};