import { defineStore } from "pinia";
import teacherAttendanceService from "@/services/teacherAttendanceService";

export const useTeacherAttendanceStore = defineStore(
    "teacherAttendance",
    {

        state: () => ({
            items: [],
            item: {},
            loading: false,
            errors: {},
            pagination: {},
            filters: {
                search: "",
                teacher_id: "",
                attendance_date: "",
                status: "",
                page: 1,
                per_page: 15
            }
        }),

        actions: {

            async fetchItems() {

                this.loading = true;

                try {

                    const response =
                        await teacherAttendanceService.getAll(
                            this.filters
                        );

                    this.items = response.data.data;
                    this.pagination = response.data.meta;

                } finally {

                    this.loading = false;

                }
            },

            async fetchItem(id) {

                this.loading = true;

                try {

                    const response =
                        await teacherAttendanceService.get(id);

                    this.item = response.data.data;

                    return this.item;

                } finally {

                    this.loading = false;

                }
            },

            async create(formData) {

                this.errors = {};

                try {

                    const response =
                        await teacherAttendanceService.create(
                            formData
                        );

                    return response.data;

                } catch (error) {

                    if (error.response?.status === 422) {
                        this.errors = error.response.data.errors;
                    }

                    throw error;
                }
            },

            async update(id, formData) {

                this.errors = {};

                try {

                    const response =
                        await teacherAttendanceService.update(
                            id,
                            formData
                        );

                    return response.data;

                } catch (error) {

                    if (error.response?.status === 422) {
                        this.errors = error.response.data.errors;
                    }

                    throw error;
                }
            },

            async delete(id) {

                await teacherAttendanceService.delete(id);

                this.fetchItems();

            }

        }

    }
);