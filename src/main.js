import { createApp } from 'vue';
import App from './App.vue';
import router from './router.js';
import mitt from 'mitt';
import "leaflet/dist/leaflet.css";

const app = createApp(App);

// Create a new emitter instance
const emitter = mitt();

// Register the router plugin
app.use(router);

// Make the emitter accessible to all components
app.config.globalProperties.emitter = emitter;

// Mount the app instance
app.mount('#app');
