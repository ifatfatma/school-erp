import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/result-grades", {
            params
        });
    },

    get(id) {
        return api.get(`/result-grades/${id}`);
    },

    create(data) {
        return api.post("/result-grades", data);
    },

    update(id, data) {
        return api.put(
            `/result-grades/${id}`,
            data
        );
    },

    delete(id) {
        return api.delete(
            `/result-grades/${id}`
        );
    },

    active() {
        return api.get(
            "/result-grades/active"
        );
    },

};