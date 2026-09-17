<template>
    <aside
        class="sidebar bg-dark text-white"
        :class="{ 'sidebar-collapsed': collapsed }"
    >
        <!-- =========================
             SIDEBAR HEADER
        ========================== -->
        <div
            class="sidebar-header d-flex align-items-center justify-content-between px-3"
        >
            <div
                v-if="!collapsed"
                class="brand d-flex align-items-center"
            >
                <div class="brand-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                <span class="brand-text ms-2">
                    School ERP
                </span>
            </div>

            <button
                type="button"
                class="sidebar-toggle"
                @click="toggleCollapse"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <i
                    :class="
                        collapsed
                            ? 'bi bi-chevron-right'
                            : 'bi bi-chevron-left'
                    "
                ></i>
            </button>
        </div>

        <!-- =========================
             USER
        ========================== -->
        <div
            v-if="auth.user"
            class="sidebar-user"
            :class="{ 'justify-content-center': collapsed }"
        >
            <div class="user-avatar">
                {{ userInitial }}
            </div>

            <div
                v-if="!collapsed"
                class="user-info ms-2"
            >
                <div class="user-name">
                    {{ auth.user.name }}
                </div>

                <small>
                    {{ userRole }}
                </small>
            </div>
        </div>

        <!-- =========================
             MENU
        ========================== -->
        <nav class="sidebar-menu">
            <ul class="nav flex-column">

                <template
                    v-for="(item, index) in filteredMenu"
                    :key="item.title || index"
                >

                    <!-- =====================
                         SINGLE MENU
                    ====================== -->
                    <li
                        v-if="!item.children"
                        class="nav-item"
                    >
                        <RouterLink
                            :to="item.route"
                            class="nav-link"
                            :class="{
                                'collapsed-link': collapsed
                            }"
                            @click="closeMobile"
                            :title="collapsed ? item.title : ''"
                        >
                            <i :class="item.icon"></i>

                            <span v-if="!collapsed">
                                {{ item.title }}
                            </span>
                        </RouterLink>
                    </li>

                    <!-- =====================
                         PARENT MENU
                    ====================== -->
                    <li
                        v-else
                        class="nav-item"
                    >
                        <button
                            type="button"
                            class="nav-link parent-link w-100 border-0"
                            :class="{
                                'collapsed-link': collapsed,
                                'parent-active':
                                    hasActiveChild(item)
                            }"
                            @click="toggleMenu(index)"
                            :title="collapsed ? item.title : ''"
                        >
                            <span class="d-flex align-items-center">
                                <i :class="item.icon"></i>

                                <span
                                    v-if="!collapsed"
                                    class="menu-title"
                                >
                                    {{ item.title }}
                                </span>
                            </span>

                            <i
                                v-if="!collapsed"
                                class="bi"
                                :class="
                                    openMenus.includes(index)
                                        ? 'bi-chevron-down'
                                        : 'bi-chevron-right'
                                "
                            ></i>
                        </button>

                        <!-- =====================
                             CHILDREN
                        ====================== -->
                        <transition name="submenu">
                            <ul
                                v-show="
                                    !collapsed &&
                                    openMenus.includes(index)
                                "
                                class="submenu nav flex-column"
                            >
                                <li
                                    v-for="child in item.children"
                                    :key="child.title"
                                >
                                    <RouterLink
                                        :to="child.route"
                                        class="nav-link child-link"
                                        active-class="active-menu"
                                        @click="closeMobile"
                                    >
                                        <i
                                            v-if="child.icon"
                                            :class="child.icon"
                                        ></i>

                                        <span>
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

import {
    computed,
    ref,
    watch,
    onMounted
} from "vue";

import {
    useRoute
} from "vue-router";

import {
    useAuthStore
} from "@/stores/auth";

import menu from "@/menu";

import {
    hasPermission
} from "@/utils/permission";


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    collapsed: {
        type: Boolean,
        default: false
    }
});


/*
|--------------------------------------------------------------------------
| EMITS
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
    "toggle-collapse",
    "close-mobile"
]);


/*
|--------------------------------------------------------------------------
| STORES
|--------------------------------------------------------------------------
*/

const auth = useAuthStore();

const route = useRoute();


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const openMenus = ref([]);


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

const userInitial = computed(() => {

    const name = auth.user?.name || "U";

    return name
        .charAt(0)
        .toUpperCase();

});


const userRole = computed(() => {

    return (
        auth.user?.roles?.[0]?.name ||
        auth.user?.user_type ||
        "User"
    );

});


/*
|--------------------------------------------------------------------------
| COLLAPSE
|--------------------------------------------------------------------------
*/

function toggleCollapse() {

    emit("toggle-collapse");

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

function closeMobile() {

    emit("close-mobile");

}


/*
|--------------------------------------------------------------------------
| PERMISSION
|--------------------------------------------------------------------------
|
| Menu items without permission are publicly available to the
| authenticated admin area, e.g. Dashboard.
|--------------------------------------------------------------------------
*/

function canAccess(item) {

    if (!item.permission) {
        return true;
    }

    return hasPermission(
        auth.user,
        item.permission
    );

}


/*
|--------------------------------------------------------------------------
| FILTER MENU
|--------------------------------------------------------------------------
*/

const filteredMenu = computed(() => {

    return menu

        .map(item => {

            /*
            |--------------------------------------------------------------------------
            | Parent menu
            |--------------------------------------------------------------------------
            */

            if (item.children) {

                const children =
                    item.children.filter(child =>
                        canAccess(child)
                    );

                return {
                    ...item,
                    children
                };

            }


            /*
            |--------------------------------------------------------------------------
            | Single menu
            |--------------------------------------------------------------------------
            */

            return item;

        })

        .filter(item => {

            if (item.children) {

                return item.children.length > 0;

            }

            return canAccess(item);

        });

});


/*
|--------------------------------------------------------------------------
| MENU TOGGLE
|--------------------------------------------------------------------------
*/

function toggleMenu(index) {

    /*
    |--------------------------------------------------------------------------
    | When collapsed, clicking a parent expands sidebar
    |--------------------------------------------------------------------------
    */

    if (props.collapsed) {

        emit("toggle-collapse");

        return;

    }


    if (openMenus.value.includes(index)) {

        openMenus.value =
            openMenus.value.filter(
                i => i !== index
            );

    } else {

        openMenus.value.push(index);

    }

}


/*
|--------------------------------------------------------------------------
| ACTIVE CHILD
|--------------------------------------------------------------------------
*/

function hasActiveChild(item) {

    if (!item.children) {
        return false;
    }

    return item.children.some(child => {

        return route.path === child.route ||
            route.path.startsWith(child.route + "/");

    });

}


/*
|--------------------------------------------------------------------------
| AUTO OPEN ACTIVE MENU
|--------------------------------------------------------------------------
*/

function openActiveMenus() {

    filteredMenu.value.forEach(
        (item, index) => {

            if (item.children && hasActiveChild(item)) {

                if (!openMenus.value.includes(index)) {

                    openMenus.value.push(index);

                }

            }

        }
    );

}


watch(
    () => route.path,
    () => {
        openActiveMenus();
    }
);


watch(
    () => filteredMenu.value,
    () => {
        openActiveMenus();
    },
    {
        deep: true
    }
);


onMounted(() => {

    openActiveMenus();

});


</script>


<style scoped>

/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {

    width: 260px;

    height: 100vh;

    min-height: 100vh;

    position: relative;

    display: flex;

    flex-direction: column;

    overflow: hidden;

    transition:
        width 0.25s ease,
        box-shadow 0.25s ease;

}


/* =========================================================
   COLLAPSED
========================================================= */

.sidebar-collapsed {

    width: 75px;

}


/* =========================================================
   HEADER
========================================================= */

.sidebar-header {

    height: 72px;

    min-height: 72px;

    border-bottom: 1px solid rgba(255,255,255,0.08);

}


.brand {

    min-width: 0;

}


.brand-icon {

    width: 38px;

    height: 38px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    background: rgba(255,255,255,0.1);

    font-size: 18px;

}


.brand-text {

    font-size: 17px;

    font-weight: 700;

    white-space: nowrap;

}


.sidebar-toggle {

    width: 34px;

    height: 34px;

    border: 0;

    border-radius: 8px;

    background: rgba(255,255,255,0.08);

    color: #fff;

    display: flex;

    align-items: center;

    justify-content: center;

    transition: all 0.2s ease;

}


.sidebar-toggle:hover {

    background: rgba(255,255,255,0.18);

}


/* =========================================================
   USER
========================================================= */

.sidebar-user {

    min-height: 76px;

    padding: 14px 16px;

    display: flex;

    align-items: center;

    border-bottom: 1px solid rgba(255,255,255,0.08);

}


.user-avatar {

    width: 40px;

    height: 40px;

    min-width: 40px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #0d6efd;

    color: #fff;

    font-weight: 700;

}


.user-info {

    min-width: 0;

}


.user-name {

    font-size: 14px;

    font-weight: 600;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

    max-width: 165px;

}


.user-info small {

    color: rgba(255,255,255,0.55);

}


/* =========================================================
   MENU
========================================================= */

.sidebar-menu {

    flex: 1;

    overflow-y: auto;

    overflow-x: hidden;

    padding: 12px 10px 20px;

}


.sidebar-menu::-webkit-scrollbar {

    width: 5px;

}


.sidebar-menu::-webkit-scrollbar-thumb {

    background: rgba(255,255,255,0.2);

    border-radius: 10px;

}


.nav-item {

    margin-bottom: 3px;

}


.nav-link {

    min-height: 44px;

    padding: 10px 13px;

    display: flex;

    align-items: center;

    gap: 11px;

    border-radius: 9px;

    color: rgba(255,255,255,0.78) !important;

    text-decoration: none;

    font-size: 14px;

    font-weight: 500;

    white-space: nowrap;

    transition:
        background 0.2s ease,
        color 0.2s ease;

}


.nav-link i {

    width: 20px;

    min-width: 20px;

    text-align: center;

    font-size: 16px;

}


.nav-link:hover {

    background: rgba(255,255,255,0.08);

    color: #fff !important;

}


.active-menu {

    background: #0d6efd !important;

    color: #fff !important;

}


.parent-link {

    justify-content: space-between;

    cursor: pointer;

}


.parent-active {

    color: #fff !important;

}


.menu-title {

    margin-left: 0;

}


/* =========================================================
   COLLAPSED LINKS
========================================================= */

.collapsed-link {

    justify-content: center;

    padding-left: 10px;

    padding-right: 10px;

}


.sidebar-collapsed .nav-link {

    justify-content: center;

}


.sidebar-collapsed .parent-link > span {

    margin: 0;

}


/* =========================================================
   SUBMENU
========================================================= */

.submenu {

    padding: 4px 0 5px 16px;

}


.child-link {

    min-height: 40px;

    padding-top: 8px;

    padding-bottom: 8px;

    font-size: 13px;

}


.child-link i {

    font-size: 14px;

}


/* =========================================================
   SUBMENU ANIMATION
========================================================= */

.submenu-enter-active,
.submenu-leave-active {

    transition:
        opacity 0.2s ease,
        transform 0.2s ease;

}


.submenu-enter-from,
.submenu-leave-to {

    opacity: 0;

    transform: translateY(-5px);

}


/* =========================================================
   COLLAPSED USER
========================================================= */

.sidebar-collapsed .sidebar-user {

    padding-left: 0;

    padding-right: 0;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 991.98px) {

    .sidebar {

        width: 260px;

    }

    .sidebar-collapsed {

        width: 260px;

    }

}

</style>