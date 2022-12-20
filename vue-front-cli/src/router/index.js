import { createRouter, createWebHistory } from 'vue-router'
import PageMain from "@/views/PageMain";
import PageFilterCategory from '@/views/Catalog/PageFilterCategory'
import PageItem from "@/views/Catalog/PageItem";
import PageFiltered from "@/views/Catalog/PageFiltered";


const routes = [
    {
        name: 'main',
        path: '/',
        component: PageMain
    },
    {
        name:'filterTiresCategory',
        path: '/catalog/tires',
        component: PageFilterCategory
    },
    {
        name:'filteredTires',
        path: '/podbor/shini',
        component: PageFiltered
    },
    {
        name:'item',
        path: '/item',
        component: PageItem
    }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
