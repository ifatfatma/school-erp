import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/classes", {
            params,
        });
    },

    get(id) {
        return api.get(`/classes/${id}`);
    },

    create(data) {
        return api.post("/classes", data);
    },

    update(id, data) {
        return api.put(`/classes/${id}`, data);
    },

    delete(id) {
        return api.delete(`/classes/${id}`);
    },

};