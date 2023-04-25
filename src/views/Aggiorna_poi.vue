<template>
  <div>
    <h1>Load Database</h1>
    <div class="warning">
      <input type="button" value="Load" @click="carica_database">
      <p>
        This procedure will truncate the database and then load it with new data
      </p>
    </div>
  </div>
  <div v-if="response">
    <h2>Response</h2><br>
    <div v-if="totalTime">
      <h3>Total time</h3>
      <p>{{ totalTime }} seconds</p>
    </div>
    <br><br>
    <h3> Details</h3>
    <div v-for="(obj, index) in response" :key="index" class="response">
      <div class="field" v-if="obj['file_name']">
        <p>The file name is </p>
        <strong>{{ obj['file_name'] }}</strong>
      </div>
      <div class="field" v-if="obj['type']">
        <p>The table is</p>
        <strong>{{ obj['type'] }}</strong>
      </div>
      <div class="field" v-if="obj['time']">
        <p>The time impiegated is </p>
        <strong>{{ obj['time'] }} seconds</strong>
      </div>
      <br>
    </div>
  </div>
</template>

<script>
import asyncRequest from '@/js/ajax';

export default {
  data() {
    return {
      response: '',
      totalTime: 0
    };
  },
  methods: {
    carica_database() {
      // Reset the data
      this.totalTime = 0;
      this.response = '';
      asyncRequest('test.php', (response) => {
        // Save the response in the data
        this.response = response;

        // Calculate the total time
        response.forEach(element => {
          this.totalTime += parseFloat(element['time']);
        });

        // Round the total time to 2 decimal places
        this.totalTime = this.totalTime.toFixed(2);
      });
    }
  }
};
</script>


<style scoped>
input[type=button] {
  background-color: var(--green);
  border: none;
  color: white;
  padding: 2rem 4rem;
  font-size: 1.3rem;
  cursor: pointer;
  margin: 3vh 0;
}

.response {
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  align-items: center;
  margin: 3vh 0;
  width: 100%;
  text-align: center;
}


.field {
  width: 100%;
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
  gap: 50px;
}

.field>p {
  text-align: right;
}

.field>strong {
  text-align: left;
}

.warning {
  color: var(--warning);
  text-align: center;
}

.warning>p {
  position: relative;
  bottom: 30px;
}
</style>
