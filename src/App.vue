<template>
  <div class="links">
    <RouterLink :to="{ name: 'Home' }" active-class="active">
      Map
    </RouterLink>
    <RouterLink :to="{ name: 'Update Database' }" active-class="active">
      Database
    </RouterLink>
  </div>
  <RouterView />
</template>

<script setup>
import { useFavicon, usePreferredDark } from '@vueuse/core';
import { computed } from 'vue';

const isDark = usePreferredDark();
const favicon = computed(() => isDark.value ? '/favicon-dark.ico' : '/favicon-light.ico');

useFavicon(favicon);
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

.links>* {
  padding: 3vmin;
  color: var(--link_not_selected);
}

.links>*:hover {
  color: var(--link_hover);
}

.active {
  color: var(--link_active);
}
</style>