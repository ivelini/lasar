<template>
    <div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

        <!-- Sidebar content -->
        <div class="sidebar-content">
            <!-- Main navigation -->
            <div class="sidebar-section">
                <ul class="nav nav-sidebar" data-nav-type="accordion">

                    <!-- Main -->
                    <template v-for="menu in menus" :key="menu.id">
                        <li class="nav-item" v-if="menu.hasOwnProperty('route')">
                            <router-link :to="{ name: menu.route}" class="nav-link">
                                <i :class="menu.icon"></i>
                                <span>{{ menu.name }}</span>
                            </router-link>
                        </li>

                        <li v-if="menu.hasOwnProperty('children')"
                            @click="onClick($event)"
                            class="nav-item nav-item-submenu nav-item-expanded nav-item-open">

                            <a href="#" class="nav-link">
                                <i :class="menu.icon"></i>
                                <span>{{ menu.name }}</span>
                            </a>

                            <ul class="nav nav-group-sub">

                                <li v-for="child in menu.children"
                                    :key="child.id"
                                    class="nav-item"
                                    style="width: 100%">
                                    <router-link :to="{ name: child.route}" class="nav-link">
                                        <i :class="child.icon"></i>
                                        <span>{{ child.name }}</span>
                                    </router-link>
                                </li>
                            </ul>
                        </li>
                    </template>

                    <!-- /main -->
                </ul>
            </div>
            <!-- /main navigation -->

        </div>
        <!-- /sidebar content -->

    </div>
</template>

<script>
export default {
    data() {
        return {
            menus: menuSidebar()
        }
    },
    methods: {
        onClick(event) {
            console.log(event)
        }
    }
}

function menuSidebar() {
    return [
        { route: 'dashboard', name: 'Dashboard', icon: 'icon-home4' },
        {
            name: 'Каталог',
            icon: '',
            children: [
                { route: 'importCatalog', name: 'Импорт каталога', icon: '' },
                { route: 'updatePriceXml', name: 'Обновление цен', icon: '' },
                { route: 'pageStorages', name: 'Склады', icon: '' },
            ]
        },
        {
            name: 'Страницы',
            icon: '',
            children: [
                { route: 'pageIndex', name: 'Все страницы', icon: '' },
                { route: 'pageCategory', name: 'Категории', icon: '' }
            ]
        },
    ];

}
</script>

<style scoped>

</style>
