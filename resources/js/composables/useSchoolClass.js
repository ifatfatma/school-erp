import useCrud from './useCrud'
import classService from '@/services/classService'

export default function () {
    return useCrud(classService)
}