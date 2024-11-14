import "./bootstrap";

import { createApp } from "vue";
import App from "./App.vue";
import { createVuetify } from "vuetify";
import router from "./router";
import "vuetify/dist/vuetify.min.css";
// import '@mdi/font/css/materialdesignicons.css'
import { aliases, mdi } from "vuetify/iconsets/mdi-svg";
import store from './store';

const app = createApp(App);
const vuetify = createVuetify({
    icons: {
        defaultSet: "mdi",
        aliases,
        sets: {
            mdi,
        },
    },
});

app.use(store).use(router).use(vuetify).mount("#app");
