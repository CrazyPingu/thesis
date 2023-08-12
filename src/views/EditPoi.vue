<template>
  <div class="container">

    <!-- Change value of a field -->
    <div v-if="poiField" class="form-section">
      <h2>Change value of a field</h2>
      <form @submit.prevent="submitChangeValue">
        <label for="table">Change field:</label>
        <select id="table" v-model="selectedField">
          <option v-for="(field, index) in poiField"
            :key="index" :value="field">
            {{ field }}
          </option>
        </select>
        <br>
        <label for="value">New value:</label>
        <input v-model="selectedValue" type="text" id="value" name="value">
        <div class="buttonContainer">
          <button>
            Change value
          </button>
        </div>
      </form>
    </div>

    <!-- Add a field -->
    <div v-if="poiField" class="form-section">
      <h2>Add a field</h2>
      <form @submit.prevent="submitAddField">
        <label for="name">Field name:</label>
        <input v-model="fieldName" type="text" id="name" name="name">
        <br>
        <label for="value">Value:</label>
        <input v-model="fieldValue" type="text" id="value" name="value">
        <div class="buttonContainer">
          <button>
            Submit
          </button>
        </div>
      </form>
    </div>

    <!-- Delete a field -->
    <div v-if="poiField && isAdmin && poiDeletableField.length > 0"
      class="form-section">
      <h2>Delete a field as Admin</h2>
      <div class="warning">
          <p>
            This procedure will delete <br>
            the field from the database
          </p>
        </div>
        <br>
      <form @submit.prevent="submitDelete">
        <label for="table">Field to delete:</label>
        <select id="table" v-model="deleteField">
          <option v-for="(field, index) in poiDeletableField"
            :key="index" :value="field">
            {{ field }}
          </option>
        </select>
        <br>
        <div class="buttonContainer">
          <button>
            Delete field
          </button>
        </div>
      </form>
    </div>

    <!-- case still loading -->
    <div v-if="!poiField">
      <h2>Loading...</h2>
    </div>
  </div>
  <div class="output">
    <h3>{{ output }}</h3>
  </div>
</template>

<script>
import asyncRequest from '@/js/ajax';
import { ref } from 'vue';

export default {
  setup() {
    const url = new URL(window.location.href);
    const idPoi = url.searchParams.get('idPoi');

    const poiField = ref(null);
    const selectedField = ref(null);
    const selectedValue = ref(null);
    const fieldName = ref('');
    const fieldValue = ref('');

    const deleteField = ref(null);
    const poiDeletableField = ref(null);
    const isAdmin = ref(false);

    const output = '';

    asyncRequest('function.php', (response) => {
      selectedField.value = response[0];
      poiDeletableField.value = response.filter(item => item !== 'descrizione');
      deleteField.value = poiDeletableField.value[0];
      poiField.value = response;
    }, { 'function' : 'get_poi_field', 'id_poi' : idPoi });


    asyncRequest('function.php', (response) => {
      isAdmin.value = response;
    }, { 'function' : 'check_admin_logged' });
    return {
      output,
      isAdmin,
      deleteField,
      poiDeletableField,
      poiField,
      idPoi,
      fieldName,
      fieldValue,
      selectedField,
      selectedValue,
    };
  },
  methods: {
    updateField(message) {
      this.output = message;

      asyncRequest('function.php', (response) => {
        this.poiDeletableField = response.filter(item => item !== 'descrizione');
        this.deleteField = this.poiDeletableField[0];
        this.poiField = response;
      }, { 'function' : 'get_poi_field', 'id_poi' : this.idPoi });

      document.getElementsByClassName('output')[0].style.display = 'block';
      setTimeout(() => {
        document.getElementsByClassName('output')[0].style.display = 'none';
      }, 2000);
    },
    submitAddField() {
      asyncRequest('function.php', () => {
        this.updateField('Added field with success');
      },{ 'function' : 'add_poi_field', 'id_poi' : this.idPoi,
        'column' : this.fieldName, 'value' : this.fieldValue });
    },
    submitChangeValue() {
      asyncRequest('function.php', () => {
        this.updateField('Changed value with success');
      },{ 'function' : 'add_poi_field', 'id_poi' : this.idPoi,
        'column' : this.selectedField, 'value' : this.selectedValue });
    },
    submitDelete() {
      asyncRequest('function.php', () => {
        this.updateField('Deleted field with success');
      },{ 'function' : 'remove_poi_field', 'column' : this.deleteField });
    }
  }
};
</script>

<style scoped>
.container {
  margin-top: 10vh;
  display: flex;
  justify-content: space-evenly;

}

.form-section {
  padding: 20px;
  border: 1px solid #ccc;
  width: 25%;
  border-radius: 5px;
  background-color: #f5f5f5;
}

.form-section h2 {
  color: var(--green);
  margin-top: 0;
}

.form-section label {
  display: block;
  color: black;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-section input[type="text"] {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

select {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.warning {
  color: red;
  font-weight: bold;
}

button{
  width: 90%;
}

.output {
  margin-top: 10vh;
  font-size: 2rem;
}
</style>


