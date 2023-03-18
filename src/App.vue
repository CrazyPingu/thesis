<template>
  <div>
    <RouterView />
    <div class="links">
      <RouterLink to="/" active-class="active">Home</RouterLink>
      <RouterLink to="/update-database" active-class="active">About</RouterLink>
    </div>
  </div>
</template>

<script setup>
import { RouterLink, RouterView } from 'vue-router'
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
