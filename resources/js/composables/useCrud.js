import { ref, reactive } from 'vue'

export default function useCrud(service) {

    const loading = ref(false)
    const saving = ref(false)

    const items = ref([])
    const item = ref(null)

    const errors = ref({})

    const pagination = reactive({
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 1
    })

    const filters = reactive({
        search: '',
        status: '',
        page: 1,
        per_page: 10,
        sort_by: 'id',
        sort_order: 'desc'
    })

    const fetchItems = async () => {

        loading.value = true

        try {

            const response = await service.getAll(filters)
            console.log(response.data)
            items.value = response.data.data

            pagination.current_page = response.data.current_page
            pagination.per_page = response.data.per_page
            pagination.total = response.data.total
            pagination.last_page = response.data.last_page

            return response.data

        } finally {

            loading.value = false

        }

    }

    const fetchItem = async (id) => {

        loading.value = true

        try {

            const response = await service.get(id)

            item.value = response.data

            return response.data

        } finally {

            loading.value = false

        }

    }

    const createItem = async (data) => {

        saving.value = true
        errors.value = {}

        try {

            const response = await service.create(data)

            return response.data

        } catch (error) {

            if (error.response?.status === 422) {
                errors.value = error.response.data.errors
            }

            throw error

        } finally {

            saving.value = false

        }

    }

    const updateItem = async (id, data) => {

        saving.value = true
        errors.value = {}

        try {

            const response = await service.update(id, data)

            return response.data

        } catch (error) {

            if (error.response?.status === 422) {
                errors.value = error.response.data.errors
            }

            throw error

        } finally {

            saving.value = false

        }

    }

    const deleteItem = async (id) => {

        return await service.delete(id)

    }

    const search = () => {

        filters.page = 1

        fetchItems()

    }

    const changePage = (page) => {

        filters.page = page

        fetchItems()

    }

    const resetFilters = () => {

        filters.search = ''
        filters.status = ''
        filters.page = 1
        filters.sort_by = 'id'
        filters.sort_order = 'desc'

        fetchItems()

    }

    return {

        loading,
        saving,

        items,
        item,

        errors,

        pagination,
        filters,

        fetchItems,
        fetchItem,

        createItem,
        updateItem,
        deleteItem,

        search,
        changePage,
        resetFilters

    }

}