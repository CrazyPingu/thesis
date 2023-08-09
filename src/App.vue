<template>
  <div class="links">
    <RouterLink :to="{ name: 'Map' }" class="router-link">
      Map
    </RouterLink>
    <RouterLink v-if="isAdminLogged"
      :to="{ name: 'Update Database' }" class="router-link">
      Database
    </RouterLink>
    <div v-if="!isUserLogged">
      <RouterLink
        :to="{ name: 'Login' }" class="router-link">
        Login
      </RouterLink>
      <RouterLink :to="{ name: 'Register' }" class="router-link">
        Register
      </RouterLink>
    </div>
    <div v-else>
      <RouterLink :to="{ name: 'Favourite' }" class="router-link">
      Favourite
      </RouterLink>
      <button @click="logout">Logout</button>
    </div>
  </div>
  <RouterView />
</template>

<script>
import { useFavicon, usePreferredDark } from '@vueuse/core';
import { ref, watch } from 'vue';
import asyncRequest from '@/js/ajax';

export default {
  setup() {
    const isAdminLogged = ref(true);
    const isUserLogged = ref(false);
    useFavicon(usePreferredDark().value ? '/favicon-dark.ico' : '/favicon-light.ico');
    watch(usePreferredDark(), () => {
      useFavicon(usePreferredDark().value ? '/favicon-dark.ico' : '/favicon-light.ico');
    });
    asyncRequest('function.php', (response) => {
      isAdminLogged.value = response;
    }, { 'function': 'check_admin_logged' });
    asyncRequest('function.php', (response) => {
      isUserLogged.value = response;
    }, { 'function': 'user_logged' });
    return {
      isUserLogged,
      isAdminLogged,
    };
  },
  methods: {
    logout() {
      this.isUserLogged = false;
      this.isAdminLogged = false;
      asyncRequest('function.php', () => {
      }, { 'function': 'logout_user' });
      this.$router.push({ name: 'Login' });
    }
  },
  mounted() {
    this.emitter.on("changeAdminLogged", response => {
      this.isAdminLogged = response;
    });
    this.emitter.on("userLogged", response => {
      this.isUserLogged = response;
    });
  },
};
</script>



<style>
:root {
  --green: #4CAF50;
  --warning: #f44336;
  --link_not_selected: gray;
  --link_hover: var(--green);
  --link_active: var(--green);
  --text_color: white;
  --h1_font_size: 2rem;
  --h1_margin: 2vmin;
  --div_links_height: 5vh;
  --center_div_height: calc(100vh - var(--div_links_height) * 2);
  --th_highlight: #f2f2f2;
}

* {
  box-sizing: border-box;
  text-decoration: none;
  padding: 0;
  margin: 0;
}

h1 {
  font-size: var(--h1_font_size);
  margin: var(--h1_margin) 0;
}

body {
  background-color: #0c0b0b;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  color: var(--text_color);
  margin-top: 3vh;
}
</style>

<style scoped>
.links {
  height: var(--div_links_height);
  display: flex;
  justify-content: center;
  align-items: center;
}

.router-link {
  padding: 3vmin;
  color: var(--link_not_selected);
}

.router-link:hover {
  color: var(--link_hover);
}

.router-link-active, .router-link-exact-active {
  color: var(--link_active);
}
</style>

<style>
/* Style the checkbox to make them look like button */
.leaflet-control-layers-overlays label:nth-child(2) input[type="checkbox"],
.leaflet-control-layers-overlays label:nth-child(3) input[type="checkbox"],
.leaflet-control-layers-overlays label:nth-child(4) input[type="checkbox"],
.leaflet-control-layers-overlays label:nth-child(5) input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 15px;
    height: 15px;
    border: 2px solid #e2e2e2;
    border-radius: 5px;
    outline: none;
    transition: border-color 0.3s ease;
}

/* Insert the separator div after the third label element */
.leaflet-control-layers-overlays label:nth-child(3)::after,
.leaflet-control-layers-overlays label:nth-child(5)::after {
  content: "";
  display: block;
  border-top: 1px solid #ddd;
  margin: 5px -10px 5px -6px;
}
</style>