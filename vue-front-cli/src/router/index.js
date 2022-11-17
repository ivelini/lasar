import { createRouter, createWebHistory } from 'vue-router'
import PageMain from "@/views/PageMain";
import PageFilterCategory from '@/views/Catalog/PageFilterCategory'
import PageItem from "@/views/Catalog/PageItem";


const routes = [
    {
        name: 'main',
        path: '/',
        component: PageMain
    },
    {
        name:'filterTires',
        path: '/catalog/tires',
        component: PageFilterCategory
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
