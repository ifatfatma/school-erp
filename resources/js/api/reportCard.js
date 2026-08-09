import api from "@/api/axios";

export default {

    student(studentId, examId) {

        return api.get(
            `/report-cards/student/${studentId}/exam/${examId}`
        );

    },

};