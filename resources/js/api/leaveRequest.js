import api from "@/api/axios";

export default {

    /**
     * Get Paginated List
     */
    getAll(params = {}) {
        return api.get("/teacher-leaves", {
            params
        });
    },

    /**
     * Get Single Record
     */
    get(id) {
        return api.get(`/teacher-leaves/${id}`);
    },

    /**
     * Create
     */
    create(data) {

        const formData = new FormData();

        Object.keys(data).forEach(key => {

            if (
                data[key] !== null &&
                data[key] !== undefined
            ) {
                formData.append(key, data[key]);
            }

        });

        return api.post(
            "/teacher-leaves",
            formData,
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

        const formData = new FormData();

        Object.keys(data).forEach(key => {

            if (
                data[key] !== null &&
                data[key] !== undefined
            ) {
                formData.append(key, data[key]);
            }

        });

        formData.append("_method", "PUT");

        return api.post(
            `/teacher-leaves/${id}`,
            formData,
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
        return api.delete(`/teacher-leaves/${id}`);
    },

    /**
     * Approve
     */
    approve(id, remarks = "") {
        return api.post(`/teacher-leaves/${id}/approve`, {
            remarks
        });
    },

    /**
     * Reject
     */
    reject(id, remarks = "") {
        return api.post(`/teacher-leaves/${id}/reject`, {
            remarks
        });
    }

};