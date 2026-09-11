/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/authStore";

const routes = [
  {
    path: "/",
    component: () => import("@/components/layouts/Guest.vue"),
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
        path: "login/",
        name: "login",
        component: () => import("@/views/auth/Login.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Login",
        },
      },
      {
        path: "register/",
        name: "register",
        component: () => import("@/views/auth/Register.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Register",
        },
      },
    ],
  },
  {
    path: "/",
    component: () => import("@/components/layouts/Default.vue"),
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: "dashboard/",
        name: "dashboard",
        component: () => import("@/views/Dashboard.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Dashboard",
        },
      },
      {
        path: "profile/",
        name: "profile",
        component: () => import("@/views/users/Profile.vue"),
        meta: {
          breadcrumbs: [],
          actions: [],
          title: "Profile",
        },
      },
      {
        path: "tenants/",
        children: [
          {
            path: "list/",
            name: "tenants.list",
            component: () => import("@/views/tenants/List.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Tenants - List",
            },
          },            
          {
            path: "view/:uuid",
            name: "tenants.view",
            component: () => import("@/views/tenants/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Tenants - View",
            },
          },
          {
            path: "new/",
            name: "tenants.new",
            component: () => import("@/views/tenants/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Tenants - New",
            },
          },
          {
            path: "edit/:uuid",
            name: "tenants.edit",
            component: () => import("@/views/tenants/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Tenants - Edit",
            },
          },
        ],
        meta: {
          requiresSuperAdmin: true,
        },
      },
      {
        path: "users/",
        children: [
          {
            path: "list/",
            name: "users.list",
            component: () => import("@/views/users/List.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Users - List",
            },
          },
          {
            path: "new/",
            name: "users.new",
            component: () => import("@/views/users/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Users - New",
            },
          },

          {
            path: "view/:uuid",
            name: "users.view",
            component: () => import("@/views/users/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Users - View",
            },
          },

          {
            path: "edit/:uuid",
            name: "users.edit",
            component: () => import("@/views/users/Form.vue"),
            meta: {
              breadcrumbs: [],
              actions: [],
              title: "Users - Edit",
            },
          },
        ],
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
  document.title = to.meta.title ? `${to.meta.title} - VCNexus` : "VCNexus";

  if (!authStore.hydration) {
    await authStore.fetchUser();
  }

  const isAuthenticated = authStore.isAuthenticated;
  const isSuperAdminAccount = authStore.isSuperAdmin;
  const requiresAuth = to.matched.some((r) => r.meta.requiresAuth);
  const requiresGuest = to.matched.some((r) => r.meta.requiresGuest);
  const requiresSuperAdmin = to.matched.some((r) => r.meta.requiresSuperAdmin);  

  if (requiresSuperAdmin && isSuperAdminAccount === false) {
    return { name: "dashboard" };
  }

  if (requiresAuth && !isAuthenticated) {
    return { name: "login", query: { redirect: to.fullPath } };
  }

  if (requiresGuest && isAuthenticated) {
    return { name: "dashboard" };
  }
});

export default router;
