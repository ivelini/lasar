<template>
    <div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

        <!-- Sidebar content -->
        <div class="sidebar-content">
            <!-- Main navigation -->
            <div class="sidebar-section">
                <ul class="nav nav-sidebar" data-nav-type="accordion">

                    <!-- Main -->
                    <li v-for="menu in menus" class="nav-item" :key="menu.id">
                        <router-link :to="{ name: menu.route}" class="nav-link">
                            <i :class="menu.icon"></i>
                            <span>{{ menu.name }}</span>
                        </router-link>
                    </li>
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
    }
}

function menuSidebar() {
    return [
        {id: 0, route: 'dashboard', name: 'Dashboard', icon: 'icon-home4'},
        {id: 1, route: 'importCatalog', name: 'Импорт каталога', icon: ''},
        {id: 2, route: 'updatePrice', name: 'Обновление цен', icon: ''},
        {id: 2, route: 'updatePriceXml', name: 'Обновление цен (XML)', icon: ''},
    ];

}
</script>

<style scoped>

</style>
