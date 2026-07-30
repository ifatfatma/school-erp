import { defineStore } from 'pinia';
import * as schoolApi from '@/api/school';

export const useSchoolStore = defineStore('school', {
  state: () => ({
    schools: [],
    pagination: {
      current_page: 1,
      per_page: 15,
      total: 0,
      last_page: 1,
    },
    loading: false,
    error: null,
  }),

  actions: {
    async fetchSchools(params = {}) {
      this.loading = true;
      this.error = null;
      try {
        const response = await schoolApi.getSchools(params);
        this.schools = response.data.data;
        this.pagination = {
          current_page: response.data.meta?.current_page || 1,
          per_page: response.data.meta?.per_page || 15,
          total: response.data.meta?.total || 0,
          last_page: response.data.meta?.last_page || 1,
        };
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to load schools';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async createSchool(data) {
      this.loading = true;
      try {
        const response = await schoolApi.createSchool(data);
        // Refresh list or push to state
        await this.fetchSchools({ page: this.pagination.current_page });
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to create school';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async updateSchool(id, data) {
      this.loading = true;
      try {
        const response = await schoolApi.updateSchool(id, data);
        await this.fetchSchools({ page: this.pagination.current_page });
        return response.data;
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to update school';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async deleteSchool(id) {
      this.loading = true;
      try {
        await schoolApi.deleteSchool(id);
        await this.fetchSchools({ page: this.pagination.current_page });
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to delete school';
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async restoreSchool(id) {
      this.loading = true;
      try {
        await schoolApi.restoreSchool(id);
        await this.fetchSchools({ page: this.pagination.current_page });
      } catch (err) {
        this.error = err.response?.data?.message || 'Failed to restore school';
        throw err;
      } finally {
        this.loading = false;
      }
    },
  },
});