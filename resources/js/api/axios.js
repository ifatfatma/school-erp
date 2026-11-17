import axios from "axios";

const api = axios.create({

    baseURL: "/api",

    headers: {

        Accept: "application/json",

        "Content-Type": "application/json"

    }

})

api.interceptors.request.use(config => {

    const token = localStorage.getItem("token")

    if (token) {

        config.headers.Authorization = `Bearer ${token}`

    }

    const isAuthRoute = config.url && (
        config.url.includes('/login') || 
        config.url.includes('/logout') || 
        config.url.includes('/register') ||
        config.url.includes('/public/')
    )

    if (!isAuthRoute) {
        const activeSchoolId = localStorage.getItem("active_school_id")

        if (activeSchoolId !== null && activeSchoolId !== undefined && activeSchoolId !== "") {
            config.headers["X-School-Id"] = activeSchoolId
        }

        const activeStudentId = localStorage.getItem("portal_active_student_id")
        if (activeStudentId) {
            config.headers["X-Student-Id"] = activeStudentId
        }
    }

    return config

})

export default api