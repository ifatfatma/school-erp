import axios from 'axios';
import * as bootstrap from 'bootstrap';
window.axios = axios;
window.bootstrap = bootstrap;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = localStorage.getItem("token");
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

const initialStudentId = localStorage.getItem("portal_active_student_id");
if (initialStudentId) {
    window.axios.defaults.headers.common['X-Student-Id'] = initialStudentId;
}

axios.interceptors.request.use(config => {
    const currentToken = localStorage.getItem("token");
    if (currentToken && !config.headers.Authorization) {
        config.headers.Authorization = `Bearer ${currentToken}`;
    }
    const studentId = localStorage.getItem("portal_active_student_id");
    if (studentId) {
        config.headers["X-Student-Id"] = studentId;
        if (!config.params) {
            config.params = {};
        }
        if (!config.params.student_id && config.url && (config.url.includes('/api/parent/') || config.url.includes('/api/portal/'))) {
            config.params.student_id = studentId;
        }
    }
    return config;
});
