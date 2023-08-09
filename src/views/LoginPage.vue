<template>
  <h1>Login</h1>
  <div class="login-container" v-if="!user_logged">
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
    <button @click="login">Login</button>
    <h2 v-if="login_output">{{ login_output }}</h2>
  </div>
  <div v-else>
    <h2>You are already logged</h2>
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
import { ref } from 'vue';
import asyncRequest from '@/js/ajax';

export default {
  emits: ['changeAdminLogged', 'userLogged'],
  methods: {
    login() {
      const username = this.$refs.usernameInput.value;
      const password = this.$refs.passwordInput.value;
      if(username != '' && password != ''){
        asyncRequest('function.php', (response) => {
          if(response){
            this.login_output = 'Login with success';
            this.user_logged = true;
            this.emitter.emit("userLogged", true);
            asyncRequest('function.php', (response) => {
              this.emitter.emit("changeAdminLogged", response);
            }, { 'function': 'check_admin_logged' });
            this.$router.push({ name: 'Map' });
          }else{
            this.login_output = 'Wrong username or password';
          }
        }, { 'function': 'login_user', 'username' : username, 'password' : password });
      }else{
        this.login_output = 'Both username and password cannot be empty';
      }
    },
    logout() {
      asyncRequest('function.php', () => {
        this.user_logged = false;
        this.login_output = null;
        this.emitter.emit("changeAdminLogged", false);
        this.emitter.emit("userLogged", false);
      }, { 'function': 'logout_user' });
    }
  },
  setup() {
    const user_logged = ref(false);
    const login_output = ref(null);
    asyncRequest('function.php', (response) => {
      user_logged.value = response;
    }, { 'function': 'user_logged' });
    return {
      user_logged,
      login_output
    };
  }
};
</script>

<style>
.login-container, .register-container {
  display: flex;
  margin-top: 10vh;
  flex-direction: column;
  align-items: center;
}

.input-field {
  padding: 10px;
  margin-bottom: 10px;
  width: 250px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
}

button {
  padding: 10px 20px;
  background-color: var(--green);
  color: var(--text_color);
  border: none;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
}

h2, button {
  margin-top: 3vmin;
}
</style>
