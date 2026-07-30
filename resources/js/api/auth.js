import api from "./axios";

export const login = (data) => api.post("/login", data);

export const logout = () => api.post("/logout");

export const profile = () => api.get("/profile");