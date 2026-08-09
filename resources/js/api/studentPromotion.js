import axios from "./axios";

export default {

    /**
     * Promotion List
     */
    getAll(params = {}) {
        return axios.get("/student-promotions", {
            params,
        });
    },

    /**
     * Promotion Details
     */
    get(id) {
        return axios.get(`/student-promotions/${id}`);
    },

    /**
     * Single Promotion
     */
    create(data) {
        return axios.post("/student-promotions", data);
    },

    /**
     * Bulk Promotion
     */
    bulkPromotion(data) {
        return axios.post("/student-promotions/bulk", data);
    },

    /**
     * Delete Promotion
     */
    delete(id) {
        return axios.delete(`/student-promotions/${id}`);
    },

    /**
     * Students For Promotion
     */
    getStudents(params = {}) {
        return axios.get("/students", {
            params,
        });
    },

    /**
     * Academic Sessions
     */
    getSessions() {
        return axios.get("/academic-sessions");
    },

    /**
     * Classes
     */
    getClasses() {
        return axios.get("/classes");
    },

    /**
     * Sections
     */
    getSections(params = {}) {
        return axios.get("/sections", {
            params,
        });
    }

};