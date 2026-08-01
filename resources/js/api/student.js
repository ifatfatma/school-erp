import axios from "./axios";

export default {

    /**
     * Student List
     */
    getAll(params = {}) {
        return axios.get("/students", {
            params
        });
    },

    /**
     * Active Students
     */
    getActive() {
        return axios.get("/students-active");
    },

    /**
     * Students By Class
     */
    getByClass(classId, sectionId = null) {

        return axios.get("/students-by-class", {
            params: {
                class_id: classId,
                section_id: sectionId
            }
        });

    },

    /**
     * Single Student
     */
    get(id) {
        return axios.get(`/students/${id}`);
    },

    /**
     * Create
     */
    create(data) {

        return axios.post(
            "/students",
            data,
            {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            }
        );

    },

    /**
     * Update
     */
    update(id, data) {

        data.append("_method", "PUT");

        return axios.post(
            `/students/${id}`,
            data,
            {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            }
        );

    },

    /**
     * Delete
     */
    delete(id) {
        return axios.delete(`/students/${id}`);
    }

};