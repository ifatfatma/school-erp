import useCrud from '@/composables/useCrud'
import subjectService from '@/services/subjectService'

export function useSubject() {
    return useCrud(subjectService)
}