import "bootstrap";
import "bootstrap/dist/css/bootstrap.css";
import { createApp } from "vue";
import VueGoogleMaps from "@fawmi/vue-google-maps";
import App from "./App.vue";
createApp(App)
    .use(VueGoogleMaps, {
        load: {
            key: import.meta.env.VITE_GOOGLE_MAP_KEY,
        },
    })
    .mount("#app");
