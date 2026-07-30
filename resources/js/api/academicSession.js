import api from "./axios";

export const getAcademicSessions = (params = {}) =>
    api.get("/academic-sessions", { params });

export const getAcademicSession = (id) =>
    api.get(`/academic-sessions/${id}`);

export const createAcademicSession = (data) =>
    api.post("/academic-sessions", data);

export const updateAcademicSession = (id, data) =>
    api.put(`/academic-sessions/${id}`, data);

export const deleteAcademicSession = (id) =>
    api.delete(`/academic-sessions/${id}`);