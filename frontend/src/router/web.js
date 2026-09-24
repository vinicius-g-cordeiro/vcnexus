/**
 * @brief Router for web application
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authentication/authenticationStore";

const routes = [
  {
    path: "/",
    component: () => import("@/layouts/Guest.vue"),
    meta: {
      requiresGuest: true,
    },
    children: [
      {
        path: "",
        name: "home",
        component: () => import("@/views/Home.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Home",
        },
      },
      {
        path: "login",
        name: "login",
        component: () => import("@/views/authentication/Login.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Login",
        },
      },
    ],
  },
  {
    path: "/",
    component: () => import("@/layouts/Default.vue"),
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: "",
        name: "home.auth",
        component: () => import("@/views/Home.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Home",
        },
      },
      {
        path: "",
        name: "web.chat",
        component: () => import("@/views/chat/Chat.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Chat",
        },
      },
    ],
  },

  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: () => import("@/views/errors/Error404.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore();
  if(!authStore.hydration){
    await authStore.getAuthenticatedUser();
  }
  document.title = to.meta.title ? `${to.meta.title} - VCNexus` : "VCNexus";

  const isAuthenticated = authStore.isAuthenticated;
  const isSuperAdminAccount = authStore.isSuperAdmin;
  const requiresAuth = to.matched.some((r) => r.meta.requiresAuth);
  const requiresGuest = to.matched.some((r) => r.meta.requiresGuest);
  const requiresSuperAdmin = to.matched.some((r) => r.meta.requiresSuperAdmin);
  to.matched.some((r) => r.meta.requiresPermission);

  const requiresPermission = to.matched.some((r) => r.meta.requiredPermissions);

  if (requiresPermission === true && requiresAuth === true) {
    const requiredPermissions = to.matched
      .filter((route) => route.meta.requiredPermissions)
      .flatMap((route) => {
        const permission = route.meta.requiredPermissions;

        return Array.isArray(permission) ? permission : [permission];
      });

    if (authStore.hasPermission(requiredPermissions) === false) {
      return { name: "home.auth" };
    }
  }

  if (requiresSuperAdmin && isSuperAdminAccount === false) {
    return { name: "home.auth" };
  }

  if (requiresAuth && !isAuthenticated) {
    return { name: "login", query: { redirect: to.fullPath } };
  }

  if (requiresGuest && isAuthenticated) {
    return { name: "home.auth" };
  }
});

export default router;
