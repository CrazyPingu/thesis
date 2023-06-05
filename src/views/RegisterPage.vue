<template>
  <h1>Register</h1>
  <div class="register-container">
  <input
      type="text"
      name="username"
      id="username"
      required
      class="input-field"
      placeholder="Username"
      ref="usernameInput" />
    <input
      type="password"
      name="password"
      id="password"
      required
      class="input-field"
      placeholder="Password"
      ref="passwordInput"  />
    <input
      type="password"
      name="confirmPassword"
      id="confirmPassword"
      required
      class="input-field"
      placeholder="Confirm Password"
      ref="confirmPasswordInput"  />
    <button @click="register">Register</button>
    <h2 v-if="register_output">{{ register_output }}</h2>
  </div>
</template>

<script>

import { ref } from 'vue';
import asyncRequest from '@/js/ajax';

export default {
  methods: {
    register() {
      const username = this.$refs.usernameInput.value;
      const password = this.$refs.passwordInput.value;
      const confirmPassword = this.$refs.confirmPasswordInput.value;
      if(confirmPassword != password){
        this.register_output = "Password and confirmation must be the same";
      }else if(username != '' && password != '' && confirmPassword != ''){
        asyncRequest('function.php', (response) => {
          if(response){
            this.register_output = 'Registered with success and already logged in';
          }else{
            this.register_output = 'Username already used';
          }
        }, { 'function': 'register_user', 'username' : username, 'password' : password });
      }else{
        this.register_output = 'Username and password cannot be empty';
      }
    },
  },
  setup() {
    const register_output = ref(null);
    return {
      register_output
    };
  }
};
</script>