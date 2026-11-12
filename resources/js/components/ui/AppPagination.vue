<template>
    <nav v-if="lastPage > 1" aria-label="Pagination">
        <ul class="pagination justify-content-center">

            <!-- Previous -->
            <li
                class="page-item"
                :class="{ disabled: currentPage === 1 }"
            >
                <button
                    class="page-link"
                    @click="changePage(currentPage - 1)"
                    :disabled="currentPage === 1"
                >
                    Previous
                </button>
            </li>

            <!-- First Page -->
            <li
                v-if="startPage > 1"
                class="page-item"
            >
                <button
                    class="page-link"
                    @click="changePage(1)"
                >
                    1
                </button>
            </li>

            <li
                v-if="startPage > 2"
                class="page-item disabled"
            >
                <span class="page-link">...</span>
            </li>

            <!-- Page Numbers -->
            <li
                v-for="page in pages"
                :key="page"
                class="page-item"
                :class="{ active: page === currentPage }"
            >
                <button
                    class="page-link"
                    @click="changePage(page)"
                >
                    {{ page }}
                </button>
            </li>

            <li
                v-if="endPage < lastPage - 1"
                class="page-item disabled"
            >
                <span class="page-link">...</span>
            </li>

            <!-- Last Page -->
            <li
                v-if="endPage < lastPage"
                class="page-item"
            >
                <button
                    class="page-link"
                    @click="changePage(lastPage)"
                >
                    {{ lastPage }}
                </button>
            </li>

            <!-- Next -->
            <li
                class="page-item"
                :class="{ disabled: currentPage === lastPage }"
            >
                <button
                    class="page-link"
                    @click="changePage(currentPage + 1)"
                    :disabled="currentPage === lastPage"
                >
                    Next
                </button>
            </li>

        </ul>
    </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    pagination: Object,
    meta: Object,

    currentPage: Number,
    lastPage: Number,

    maxVisible: {
        type: Number,
        default: 5
    }
})

const pag = computed(() => props.pagination || props.meta)

const currentPage = computed(() =>
    pag.value
        ? pag.value.current_page
        : props.currentPage
)

const lastPage = computed(() =>
    pag.value
        ? pag.value.last_page
        : props.lastPage
)

const emit = defineEmits(['page-change'])

const startPage = computed(() => {

    const half = Math.floor(props.maxVisible / 2)

    if (lastPage.value <= props.maxVisible)
        return 1

    if (currentPage.value <= half + 1)
        return 1

    if (currentPage.value >= lastPage.value - half)
        return lastPage.value - props.maxVisible + 1

    return currentPage.value - half

})

const endPage = computed(() =>
    Math.min(
        startPage.value + props.maxVisible - 1,
        lastPage.value
    )
)

const pages = computed(() => {

    const list = []

    for(let i = startPage.value; i <= endPage.value; i++){

        list.push(i)

    }

    return list

})

function changePage(page){

    if(
        page < 1 ||
        page > lastPage.value ||
        page === currentPage.value
    ){
        return
    }

    emit('page-change', page)

}
</script>