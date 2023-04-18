<template>
  <div>
    <RouterView />
    <div class="links">
      <RouterLink :to="{ name: 'Home' }" active-class="active">
        Home
      </RouterLink>
      <RouterLink :to="{ name: 'Update Database' }" active-class="active">
        Update Database
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { useFavicon, usePreferredDark } from '@vueuse/core';
import { computed } from 'vue';

const isDark = usePreferredDark();
const favicon = computed(() => isDark.value ? '/favicon-dark.ico' : '/favicon-light.ico');

useFavicon(favicon);
</script>

<style scoped>
.links {
  height: 5vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.links>* {
  padding: 3vmin;
  text-decoration: none;
}

.links>*:hover {
  color: #42b983;
}

.active {
  color: green
}
</style>

<style>
* {
  box-sizing: border-box;
}

body {
  background-color: #0c0b0b;
}

#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #f7eeee;
  margin-top: 60px;
}
</style>
