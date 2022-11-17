import { createRouter, createWebHistory } from "vue-router";
import ImportCatalog from "@/views/ImportCatalog";
import PageDashboard from "@/views/PageDashboard";
import PageError404 from "@/views/PageError404";
import UpdatePrice from "@/views/UpdatePrice";
import UpdatePriceXml from "@/views/UpdatePriceXml";

const routes = [
    {
        name: 'dashboard',
        path: '/',
        component: PageDashboard
    },
    {
        name: 'importCatalog',
        path: '/catalog/import-data',
        component: ImportCatalog
    },
    {
        name: 'updatePrice',
        path: '/catalog/import-price',
        component: UpdatePrice
    },
    {
        name: 'updatePriceXml',
        path: '/catalog/import-price-xml',
        component: UpdatePriceXml
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
