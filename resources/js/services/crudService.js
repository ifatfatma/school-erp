// resources/js/services/crudService.js

import api from '@/api/axios'

export default function(endpoint) {
    return {
        getAll(params = {}) {
            return api.get(endpoint, { params })
        },

        get(id) {
            return api.get(`${endpoint}/${id}`)
        },

        create(data) {
            return api.post(endpoint, data)
        },

        update(id, data) {
            return api.put(`${endpoint}/${id}`, data)
        },

        delete(id) {
            return api.delete(`${endpoint}/${id}`)
        }
    }
}