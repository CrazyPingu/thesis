<template>
  <img alt="Vue logo" src="./assets/logo.png">
  <HelloWorld msg="Welcome to Your Vue.js App"/>
  <DynamicFavicon />
</template>

<script>
import HelloWorld from '@/components/HelloWorld.vue'
import DynamicFavicon from '@/components/DynamicFavicon.vue'

export default {
  name: 'App',
  components: {
    HelloWorld,
    DynamicFavicon
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
