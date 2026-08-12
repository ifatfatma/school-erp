import api from "@/api/axios";

export default {

    getAll(params = {}) {
        return api.get(
            "/fee-assignments",
            { params }
        );
    },

    students(params = {}) {
        return api.get(
            "/fee-assignments/students",
            { params }
        );
    },

    assign(data) {
        return api.post(
            "/fee-assignments/assign",
            data
        );
    },

    delete(id) {
        return api.delete(
            `/fee-assignments/${id}`
        );
    },

};