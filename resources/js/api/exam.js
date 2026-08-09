import axios from "./axios";

export default {

    /**
     * List
     */
    getAll(params = {}) {
        return axios.get("/exams", { params });
    },

    /**
     * Active List
     */
    active() {
        return axios.get("/exams/active");
    },

    /**
     * Find
     */
    get(id) {
        return axios.get(`/exams/${id}`);
    },

    /**
     * Create
     */
    create(data) {
        return axios.post("/exams", data);
    },

    /**
     * Update
     */
    update(id, data) {
        return axios.put(`/exams/${id}`, data);
    },

    /**
     * Delete
     */
    delete(id) {
        return axios.delete(`/exams/${id}`);
    },

    /**
     * Lock Marks
     */
    lockMarks(id) {
        return axios.patch(`/exams/${id}/lock-marks`);
    },

    /**
     * Unlock Marks
     */
    unlockMarks(id) {
        return axios.patch(`/exams/${id}/unlock-marks`);
    },

    /**
     * Publish Result
     */
    publishResult(id) {
        return axios.patch(`/exams/${id}/publish-result`);
    },

    /**
     * Unpublish Result
     */
    unpublishResult(id) {
        return axios.patch(`/exams/${id}/unpublish-result`);
    }

};