import { createApp } from 'vue'
import App from './App.vue'
import router from './router.js';

const app = createApp(App)

// Register the router plugin
app.use(router)

// Mount the app instance
app.mount('#app')
