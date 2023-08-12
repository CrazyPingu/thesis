import { createRouter, createWebHistory } from 'vue-router';
import Aggiorna_poi from '@/views/Aggiorna_poi.vue';
import LoginPage from '@/views/LoginPage.vue';
import RegisterPage from '@/views/RegisterPage.vue';
import FavouritePage from '@/views/FavouritePage.vue';
import EditPoi from '@/views/EditPoi.vue';
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
    {
      path: '/login',
      name: 'Login',
      component: LoginPage,
    },
    {
      path: '/favourite',
      name: 'Favourite',
      component: FavouritePage,
    },
    {
      path: '/register',
      name: 'Register',
      component: RegisterPage,
    },
    {
      path: '/edit-poi',
      name: 'Edit POI',
      component: EditPoi,
    }
  ],
});

export default router;
