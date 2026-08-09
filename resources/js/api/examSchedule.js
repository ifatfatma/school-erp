import axios from "./axios";

export default {

    /**
     * List
     */
    getAll(params = {}) {
        return axios.get("/exam-schedules", {
            params,
        });
    },

    /**
     * Active List
     */
    active() {
        return axios.get("/exam-schedules/active");
    },

    /**
     * Find
     */
    get(id) {
        return axios.get(`/exam-schedules/${id}`);
    },

    /**
     * Create
     */
    create(data) {
        return axios.post("/exam-schedules", data);
    },

    /**
     * Update
     */
    update(id, data) {
        return axios.put(`/exam-schedules/${id}`, data);
    },

    /**
     * Delete
     */
    delete(id) {
        return axios.delete(`/exam-schedules/${id}`);
    }

};