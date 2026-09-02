/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const routes = [
    {
        path: '/',        
        component: () => import('@/components/shared/layouts/Guest.vue'),
        meta: {
            requiresGuest: true,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('@/views/locales/pt-br/Home.vue'),
                 meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Home'
                }
            },
            {
                path: 'login/',
                name: 'login',
                component: () => import('@/views/locales/pt-br/auth/Login.vue'),
                 meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Login'
                }
            },
            {
                path:"register/",
                name:"register",
                component: () => import('@/views/locales/pt-br/auth/Register.vue'),
                 meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Register'
                }
            },
        ]
    },
    {
        path: '/',        
        component: () => import('@/components/shared/layouts/Default.vue'),
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: 'dashboard/',
                name: 'dashboard',
                component: () => import('@/views/locales/pt-br/Dashboard.vue'),
                meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Dashboard'
                }
            },
            {
                path: 'users/',
                name: 'users',
                component: () => import('@/views/locales/pt-br/users/Profile.vue'),
                meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Profile'
                }
            }
        ]
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from) => {
    const authStore = useAuthStore()
    document.title = to.meta.title ? `${to.meta.title} - VCNexus` : 'VCNexus'
    
    if (!authStore.hydration) {
        await authStore.fetchUser()
    }

    const isAuthenticated = authStore.isAuthenticated
    const requiresAuth = to.matched.some(r => r.meta.requiresAuth)
    const requiresGuest = to.matched.some(r => r.meta.requiresGuest)

    if (requiresAuth && !isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } }
    }

    if (requiresGuest && isAuthenticated) {
        return { name: 'dashboard' }
    }
})

export default router