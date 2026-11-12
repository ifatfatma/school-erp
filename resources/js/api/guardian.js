import axios from "./axios";

export default {

    /**
     * Guardian List
     */
    getAll(params = {}) {
        return axios.get("/guardians", {
            params,
        });
    },

    /**
     * Get Guardian
     */
    get(id) {
        return axios.get(`/guardians/${id}`);
    },

    /**
     * Create Guardian
     */
    create(data) {

        const formData = new FormData();

        Object.keys(data).forEach(key => {

            if (key === "student_ids" && Array.isArray(data.student_ids)) {

                data.student_ids.forEach(student => {

                    formData.append("student_ids[]", student);

                });

            } else if (key === "photo") {

                if (data.photo instanceof File) {
                    formData.append("photo", data.photo);
                }

            } else if (key !== "students" && key !== "photo_url" && data[key] !== null && data[key] !== undefined) {

                formData.append(key, data[key]);

            }

        });

        return axios.post("/guardians", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

    },

    /**
     * Update Guardian
     */
    update(id, data) {

        const formData = new FormData();

        formData.append("_method", "PUT");

        Object.keys(data).forEach(key => {

            if (key === "student_ids" && Array.isArray(data.student_ids)) {

                data.student_ids.forEach(student => {

                    formData.append("student_ids[]", student);

                });

            } else if (key === "photo") {

                if (data.photo instanceof File) {
                    formData.append("photo", data.photo);
                }

            } else if (key !== "students" && key !== "photo_url" && data[key] !== null && data[key] !== undefined) {

                formData.append(key, data[key]);

            }

        });

        return axios.post(`/guardians/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

    },

    /**
     * Delete Guardian
     */
    delete(id) {
        return axios.delete(`/guardians/${id}`);
    }

};