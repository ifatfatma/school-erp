import api from "@/api/axios";

export default {

    load(params) {
        return api.get("/exam-marks/load", { params });
    },

    save(data) {
        return api.post("/exam-marks/save", data);
    },

    student(studentId) {
        return api.get(`/exam-marks/student/${studentId}`);
    },

    lock(examId) {
        return api.patch(`/exam-marks/lock/${examId}`);
    },

    unlock(examId) {
        return api.patch(`/exam-marks/unlock/${examId}`);
    }

};