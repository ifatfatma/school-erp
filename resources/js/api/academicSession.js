import api from "./axios";

export default {

    index(params = {}) {
        return api.get("/academic-sessions", { params });
    },

    active() {
        return api.get("/academic-sessions/active");
    },

    find(id) {
        return api.get(`/academic-sessions/${id}`);
    },

    create(data) {
        return api.post("/academic-sessions", data);
    },

    update(id, data) {
        return api.put(`/academic-sessions/${id}`, data);
    },

    delete(id) {
        return api.delete(`/academic-sessions/${id}`);
    }

};