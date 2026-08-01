import axios from "./axios";

export default {

    /**
     * Attendance List
     */
    getAll(params = {}) {
        return axios.get("/student-attendances", {
            params,
        });
    },

    /**
     * Get Single Attendance
     */
    get(id) {
        return axios.get(`/student-attendances/${id}`);
    },

    /**
     * Create Attendance
     */
    create(data) {
        return axios.post("/student-attendances", data);
    },

    /**
     * Update Attendance
     */
    update(id, data) {
        return axios.put(`/student-attendances/${id}`, data);
    },

    /**
     * Delete Attendance
     */
    delete(id) {
        return axios.delete(`/student-attendances/${id}`);
    },

    /**
     * Bulk Attendance
     */
    bulk(data) {
        return axios.post("/student-attendances/bulk", data);
    },

    /**
     * Monthly Attendance
     */
    monthly(params) {
        return axios.get("/student-attendances/monthly", {
            params,
        });
    },

    /**
     * Attendance Summary
     */
    summary(params) {
        return axios.get("/student-attendances/summary", {
            params,
        });
    }

};