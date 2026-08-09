import axios from "./axios";

export default {

    /**
     * Exam Type List
     */
    getAll(params = {}) {
        return axios.get("/exam-types", {
            params,
        });
    },

    /**
     * Active Exam Types
     */
    active() {
        return axios.get("/exam-types/active");
    },

    /**
     * Single Exam Type
     */
    get(id) {
        return axios.get(`/exam-types/${id}`);
    },

    /**
     * Create
     */
    create(data) {
        return axios.post("/exam-types", data);
    },

    /**
     * Update
     */
    update(id, data) {
        return axios.put(`/exam-types/${id}`, data);
    },

    /**
     * Delete
     */
    delete(id) {
        return axios.delete(`/exam-types/${id}`);
    }

};