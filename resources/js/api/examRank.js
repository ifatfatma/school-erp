import api from "@/api/axios";

export default {

    calculate(data) {
        return api.post(
            "/exam-ranks/calculate",
            data
        );
    },

};