import { createRouter, createWebHistory } from "vue-router";
import Chat from "./components/Chat.vue";
import Login from "./components/Login.vue";
import store from './store';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            component: Chat,
            name: "chat",
            meta: { requiresAuth: true }
        },
        {
            path: "/login",
            component: Login,
            name: "login"
        },
    ],
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.getters['auth/isAuthenticated']) {
        next('/login');
    } else if (to.name === 'login' && store.getters['auth/isAuthenticated']){
        next({ path: '/' });
    } else {
        next();
    }
});

export default router;