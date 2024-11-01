import { createRouter, createWebHistory } from "vue-router";
import Chat from "./components/Chat.vue";


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            component: Chat,
            name: "chat"
        },

    ],
});

export default router;