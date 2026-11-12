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

    const isLogout = config.url && (config.url.includes('/logout') || config.url.endsWith('logout'))

    if (!isLogout) {
        const activeSchoolId = localStorage.getItem("active_school_id")

        if (activeSchoolId !== null && activeSchoolId !== undefined && activeSchoolId !== "") {
            config.headers["X-School-Id"] = activeSchoolId
        }
    }

    return config

})

export default api