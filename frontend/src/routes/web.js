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
        component: () => import('@/components/layouts/Guest.vue'),
        meta: {
            requiresGuest: true,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('@/views/Home.vue'),
                 meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Home'
                }
            },
            {
                path: 'login/',
                name: 'login',
                component: () => import('@/views/auth/Login.vue'),
                 meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Login'
                }
            },
            {
                path:"register/",
                name:"register",
                component: () => import('@/views/auth/Register.vue'),
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
        component: () => import('@/components/layouts/Default.vue'),
        meta: {
            requiresAuth: true
        },
        children: [
            {
                path: 'dashboard/',
                name: 'dashboard',
                component: () => import('@/views/Dashboard.vue'),
                meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Dashboard'
                }
            },
            {
                path: 'profile/',
                name: 'profile',
                component: () => import('@/views/users/Profile.vue'),
                meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Profile',
                }
            },
        {
                path: 'users/list/',
                name: 'users-list',
                component: () => import('@/views/users/List.vue'),
                meta: {
                    breadcrumbs: [],
                    actions: [],
                    title: 'Users - List'
                }
            },
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/errors/Error404.vue'),
        
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