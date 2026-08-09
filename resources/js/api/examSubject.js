import axios from "./axios";

export default {

    getAll(params = {}) {
        return axios.get("/exam-subjects", { params });
    },

    active() {
        return axios.get("/exam-subjects/active");
    },

    byExamAndClass(examId, classId) {
        return axios.get(
            `/exam-subjects/exam/${examId}/class/${classId}`
        );
    },

    get(id) {
        return axios.get(`/exam-subjects/${id}`);
    },

    create(data) {
        return axios.post("/exam-subjects", data);
    },

    update(id, data) {
        return axios.put(`/exam-subjects/${id}`, data);
    },

    delete(id) {
        return axios.delete(`/exam-subjects/${id}`);
    }

};