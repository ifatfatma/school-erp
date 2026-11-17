import { defineStore } from 'pinia'
import api from '@/api/axios'

export const useParentPortalStore = defineStore('parentPortal', {
  state: () => ({
    children: [],
    activeChild: null,
    loading: false,
  }),

  getters: {
    activeChildId: (state) => state.activeChild?.id ?? null,
  },

  actions: {
    async loadChildren() {
      this.loading = true
      try {
        const { data } = await api.get('/parent/children')
        if (data.success && Array.isArray(data.data) && data.data.length > 0) {
          this.children = data.data
          const savedId = localStorage.getItem('portal_active_student_id')
          let matched = this.children.find(c => String(c.id) === String(savedId))
          if (!matched) {
            matched = this.children[0]
          }
          this.setActiveChild(matched)
        }
      } catch (err) {
        console.error('Failed to load children in parent store', err)
      } finally {
        this.loading = false
      }
    },

    setActiveChild(child) {
      if (!child) return
      this.activeChild = child
      localStorage.setItem('portal_active_student_id', String(child.id))

      if (child.school?.code) {
        localStorage.setItem('portal_school_code', child.school.code)
        sessionStorage.setItem('portal_school_code', child.school.code)
      }
    },

    switchChildById(childId) {
      const target = this.children.find(c => String(c.id) === String(childId))
      if (target) {
        this.setActiveChild(target)
      }
    }
  }
})
