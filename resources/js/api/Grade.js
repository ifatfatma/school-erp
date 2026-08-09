import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/grades", {
            params
        });
    },

    get(id) {
        return api.get(`/grades/${id}`);
    },

    create(data) {
        return api.post("/grades", data);
    },

    update(id, data) {
        return api.put(`/grades/${id}`, data);
    },

    delete(id) {
        return api.delete(`/grades/${id}`);
    },

    active() {
        return api.get("/grades/active");
    }

};