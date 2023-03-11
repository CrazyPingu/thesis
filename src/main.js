import { createApp } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')

// Change the favicon based on the user's preferred color scheme
document.head.insertBefore(
    Object.assign(document.createElement('link'), {
        rel: 'icon',
        type: 'image/x-icon',
        href: window.matchMedia('(prefers-color-scheme: dark)').matches
            ? '/favicon-light.ico'
            : '/favicon-dark.ico',
    }),
    document.head.firstChild
);
