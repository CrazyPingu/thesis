<template>
  <div class="links">
    <RouterLink :to="{ name: 'Map' }" class="router-link">
      Map
    </RouterLink>
    <RouterLink :to="{ name: 'Update Database' }" class="router-link">
      Database
    </RouterLink>
    <RouterLink :to="{ name: 'Login' }" class="router-link">
      Login
    </RouterLink>
    <RouterLink :to="{ name: 'Register' }" class="router-link">
      Register
    </RouterLink>
  </div>
  <RouterView />
</template>

<script>
import { useFavicon, usePreferredDark } from '@vueuse/core';
import { computed } from 'vue';

export default {
  computed: {
    favicon() {
      const isDark = usePreferredDark().value;
      return computed(() => isDark ? '/favicon-dark.ico' : '/favicon-light.ico');
    }
  },
  mounted() {
    useFavicon(this.favicon);
  },
  setup() {
    const userLogged = true;
    return {
      userLogged
    };
  }
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