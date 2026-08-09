import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get("/teachers", {
            params,
        });
    },

    get(id) {
        return api.get(`/teachers/${id}`);
    },

    create(data) {
        const formData = new FormData();

        Object.keys(data).forEach(key => {
            if (data[key] !== null && data[key] !== "") {
                formData.append(key, data[key]);
            }
        });

        return api.post("/teachers", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },

    update(id, data) {
        const formData = new FormData();

        Object.keys(data).forEach(key => {
            if (data[key] !== null && data[key] !== "") {
                formData.append(key, data[key]);
            }
        });

        formData.append("_method", "PUT");

        return api.post(`/teachers/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    },
    active() {
        return api.get("/teachers/active");
    },
    delete(id) {
        return api.delete(`/teachers/${id}`);
    },

};