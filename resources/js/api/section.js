import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/sections", {
            params,
        });
    },

    get(id) {
        return api.get(`/sections/${id}`);
    },

    create(data) {
        return api.post("/sections", data);
    },

    update(id, data) {
        return api.put(`/sections/${id}`, data);
    },

    delete(id) {
        return api.delete(`/sections/${id}`);
    },

};