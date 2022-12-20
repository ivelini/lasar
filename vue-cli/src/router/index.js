import { createRouter, createWebHistory } from "vue-router";
import ImportCatalog from "@/views/ImportCatalog";
import PageDashboard from "@/views/PageDashboard";
import PageError404 from "@/views/PageError404";
import PageStorages from "@/views/PageStorages";
import PageIndex from "@/views/PageIndex";
import PageCategory from "@/views/PageCategory";
import PageAdd from "@/views/PageAdd";
import PageEdit from "@/views/PageEdit";
import UpdatePriceXml from "@/views/UpdatePriceXml";

const routes = [
    {
        path: '/',
        component: PageDashboard,
        name: 'dashboard',
    },
    {
        path: '/catalog/import-data',
        component: ImportCatalog,
        name: 'importCatalog',
    },
    {
        path: '/catalog/import-price',
        component: UpdatePriceXml,
        name: 'updatePriceXml',
    },
    {
        path: '/catalog/storages',
        component: PageStorages,
        name: 'pageStorages',
    },
    {
        path: '/page/index',
        component: PageIndex,
        name: 'pageIndex',
    },
    {
        path: '/page/add',
        component: PageAdd,
        name: 'pageAdd',
    },
    {
        path: '/page/:page_id',
        component: PageEdit,
        name: 'pageEdit',
    },
    {
        path: '/page/category',
        component: PageCategory,
        name: 'pageCategory',
    },
    {
        path: '/:any(.*)',
        component: PageError404
    }

]

export default createRouter({
    history: createWebHistory(),
    routes
})
