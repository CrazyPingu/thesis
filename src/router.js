import { createRouter, createWebHistory } from 'vue-router';
import Aggiorna_poi from './views/Aggiorna_poi.vue';
import Home from './views/Home.vue';

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home,
    },
    {
      path: '/update-database',
      name: 'Update Database',
      component: Aggiorna_poi,
    },
  ],
});

export default router;
