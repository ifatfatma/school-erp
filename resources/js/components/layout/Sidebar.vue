<template>
    <aside
        :class="[
            'sidebar bg-dark text-white',
            collapsed ? 'sidebar-collapsed' : ''
        ]"
    >
        <!-- Logo -->
        <div
            class="sidebar-header d-flex justify-content-between align-items-center p-3 border-bottom"
        >
            <div
                class="fw-bold fs-5"
                v-if="!collapsed"
            >
                School ERP
            </div>

            <button
                class="btn btn-sm btn-outline-light"
                @click="toggleSidebar"
            >
                <i class="bi bi-list"></i>
            </button>
        </div>

        <!-- User -->
        <div
            class="p-3 border-bottom"
            v-if="!collapsed && auth.user"
        >
            <div class="fw-bold">
                {{ auth.user.name }}
            </div>

            <small class="text-light opacity-75">
                {{ auth.user.roles?.[0]?.name }}
            </small>
        </div>

        <!-- Menu -->
        <nav class="mt-2">

            <ul class="nav flex-column">

                <template
                    v-for="(item,index) in filteredMenu"
                    :key="index"
                >

                    <!-- Single Menu -->

                    <li
                        v-if="!item.children"
                        class="nav-item"
                    >
                        <RouterLink
                            :to="item.route"
                            class="nav-link text-white"
                            active-class="active-menu"
                        >
                            <i :class="item.icon"></i>

                            <span
                                v-if="!collapsed"
                                class="ms-2"
                            >
                                {{ item.title }}
                            </span>

                        </RouterLink>
                    </li>

                    <!-- Parent Menu -->

                    <li
                        v-else
                        class="nav-item"
                    >

                        <a
                            href="#"
                            class="nav-link text-white d-flex justify-content-between align-items-center"
                            @click.prevent="toggleMenu(index)"
                        >

                            <div>

                                <i :class="item.icon"></i>

                                <span
                                    v-if="!collapsed"
                                    class="ms-2"
                                >
                                    {{ item.title }}
                                </span>

                            </div>

                            <i
                                v-if="!collapsed"
                                :class="[
                                    'bi',
                                    openMenus.includes(index)
                                        ? 'bi-chevron-down'
                                        : 'bi-chevron-right'
                                ]"
                            ></i>

                        </a>

                        <!-- Children -->

                        <transition name="fade">

                            <ul
                                v-show="openMenus.includes(index)"
                                class="nav flex-column ms-3"
                            >

                                <li
                                    v-for="child in item.children"
                                    :key="child.title"
                                >

                                    <RouterLink
                                        :to="child.route"
                                        class="nav-link text-light"
                                        active-class="active-menu"
                                    >

                                        <i
                                            v-if="child.icon"
                                            :class="child.icon"
                                        ></i>

                                        <span
                                            v-if="!collapsed"
                                            class="ms-2"
                                        >
                                            {{ child.title }}
                                        </span>

                                    </RouterLink>

                                </li>

                            </ul>

                        </transition>

                    </li>

                </template>

            </ul>

        </nav>

    </aside>
</template>

<script setup>

import { ref, computed } from "vue";

import { useAuthStore } from "@/stores/auth";

import menu from "@/menu";

import { hasPermission } from "@/utils/permission";

const auth = useAuthStore();

const collapsed = ref(false);

const openMenus = ref([]);

function toggleSidebar() {

    collapsed.value = !collapsed.value;

}

function toggleMenu(index) {

    if (openMenus.value.includes(index)) {

        openMenus.value = openMenus.value.filter(i => i !== index);

    } else {

        openMenus.value.push(index);

    }

}

const filteredMenu = computed(() => {

    return menu
        .map(item => {

            if (item.children) {

                return {

                    ...item,

                    children: item.children.filter(child =>
                        hasPermission(auth.user, child.permission)
                    )

                };

            }

            return item;

        })
        .filter(item => {

            if (item.children) {

                return item.children.length > 0;

            }

            return hasPermission(auth.user, item.permission);

        });

});

</script>

<style scoped>

.sidebar{

    width:260px;

    min-height:100vh;

    transition:.3s;

    overflow-y:auto;

}

.sidebar-collapsed{

    width:75px;

}

.nav-link{

    padding:12px 18px;

    transition:.2s;

    white-space:nowrap;

}

.nav-link:hover{

    background:#495057;

}

.active-menu{

    background:#0d6efd !important;

    color:#fff !important;

    border-radius:6px;

}

.sidebar-header{

    height:70px;

}

.fade-enter-active,
.fade-leave-active{

    transition:.2s;

}

.fade-enter-from,
.fade-leave-to{

    opacity:0;

}

::-webkit-scrollbar{

    width:5px;

}

::-webkit-scrollbar-thumb{

    background:#666;

}

</style>