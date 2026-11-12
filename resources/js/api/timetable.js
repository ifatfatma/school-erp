import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/timetables", {
            params,
        });
    },

    get(id) {
        return api.get(`/timetables/${id}`);
    },

    find(id) {
        return this.get(id);
    },

    create(data) {
        return api.post("/timetables", data);
    },

    update(id, data) {
        return api.put(`/timetables/${id}`, data);
    },

    delete(id) {
        return api.delete(`/timetables/${id}`);
    },

};