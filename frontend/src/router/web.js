/**
 * @brief Router for web application 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createRouter, createWebHistory } from "vue-router";

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
    ],
  },
  {
    path: "/web-chat",
    component: () => import("@/layouts/Guest.vue"),
    meta: {
      requiresGuest: true,
    },
    children: [
      {
        path: "",
        name: "web-chat",
        component: () => import("@/views/chat/WebSocketTest.vue"),
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

});

export default router;

