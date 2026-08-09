import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/subjects", {
            params,
        });
    },

    get(id) {
        return api.get(`/subjects/${id}`);
    },

    create(data) {
        return api.post("/subjects", data);
    },

    update(id, data) {
        return api.put(`/subjects/${id}`, data);
    },
    active() {
        return api.get("/subjects/active");
    },
    delete(id) {
        return api.delete(`/subjects/${id}`);
    },
    
};