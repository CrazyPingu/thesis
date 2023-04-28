import { createRouter, createWebHistory } from 'vue-router';
import Aggiorna_poi from './views/Aggiorna_poi.vue';
import Map from './views/Map.vue';

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Map',
      component: Map,
    },
    {
      path: '/update-database',
      name: 'Update Database',
      component: Aggiorna_poi,
    },
  ],
});

export default router;
