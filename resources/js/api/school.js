import apiClient from './axios'; // your configured axios instance

export const getSchools = (params) => apiClient.get('/schools', { params });
export const getSchool = (id) => apiClient.get(`/schools/${id}`);
export const createSchool = (data) => apiClient.post('/schools', data);
export const updateSchool = (id, data) => {
    if (data instanceof FormData) {
        data.append('_method', 'PUT');
        return apiClient.post(`/schools/${id}`, data);
    }
    return apiClient.put(`/schools/${id}`, data);
};
export const deleteSchool = (id) => apiClient.delete(`/schools/${id}`);
export const restoreSchool = (id) => apiClient.post(`/schools/${id}/restore`);