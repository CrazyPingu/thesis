<template>
  <div class="container">
    <div class="tableContainer" v-if="listFavourite.length != 0">
      <table>
        <thead>
          <tr>
            <th v-for="header in tableHeaders" :key="header">{{ header }}</th>
            <th>Remove from favourite</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(favourite, totalIndex)
          in listFavourite" :key="totalIndex">
            <td v-for="(value, key) in favourite" :key="key">
              <p v-if="value != null && value != ''">{{ value }}</p>
            </td>
            <td>
              <button value="Remove" @click="removeFavourite(favourite.idPoi)">
                Remove
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="tableContainer" v-else>
      <h1>No favourite found</h1>
    </div>
    <div class="filterContainer">
      <button value="Reset filter" @click="resetFilter">
        Reset filter
      </button>
      <div class="vertical-filters">
        <label v-for="(type, index) in listType" :key="index">
          <input type="radio" :value="type"
            @change="filter" v-model="selectedValue" />
          {{ type }}
        </label>
      </div>
    </div>
  </div>
</template>


<script>
import asyncRequest from '@/js/ajax';
import { ref } from 'vue';

export default {
  setup() {
    const selectedValue = ref(0);
    const listFavourite = ref([]);
    const listType = ref([]);
    asyncRequest('function.php', (response) => {
      listFavourite.value = response;
    }, { 'function': 'get_favourite_info' });

    asyncRequest('function.php', (response) => {
      listType.value = response;
    }, { 'function': 'get_type' });
    return {
      listType,
      listFavourite,
      selectedValue,
    };
  },
  methods: {
    resetFilter() {
      this.selectedValue = 0;
      asyncRequest('function.php', (response) => {
        this.listFavourite = response;
      }, { 'function': 'get_favourite_info' });
    },
    removeFavourite(idPoi){
      asyncRequest('function.php', () => {
        this.filter();
      }, { 'function': 'remove_favourite', 'path_id': idPoi });
    },
    filter(){
      if(this.selectedValue == 0){
        this.resetFilter();
        return;
      }
      asyncRequest('function.php', (response) => {
        this.listFavourite = response;
      }, { 'function': 'get_favourite_info_filtered', 'type': this.selectedValue });
    }
  },
  computed: {
    tableHeaders() {
      if (this.listFavourite.length > 0) {
        const headers = {};
        this.listFavourite.forEach((element) => {
          Object.keys(element).forEach((key) => {
            if(element[key] != null){
              headers[key] = true;
            }
          });
        });

        // remove all the keys that are not in all the elements
        for (const item of this.listFavourite) {
          for (const prop in item) {
            if (!Object.keys(headers).includes(prop)) {
              delete item[prop];
            }
          }
        }
        return Object.keys(headers);

      }
      return [];
    },
  },
};
</script>

<style scoped>

th, td {
  text-align: left;
  text-align: center;
  color: black;
}

.container {
  margin: 10vh 10vw;
  display: flex;
  gap: 30px;
}

.filterContainer {
  position: -webkit-sticky;
  position: sticky;
  top: 5vh;
  flex: 1;
  max-height: 75vh;
  display: flex;
  flex-direction: column;
  padding: 10px;
  border: 1px solid var(--green);
}

.vertical-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  flex-direction: column;
  max-height: inherit;
}

.tableContainer {
  flex: 2;
  padding: 10px;
}

label {
  display: flex;
  text-align: start;
  gap: 10px;
}

button {
  width: 100%;
  margin-bottom: 15px;
}

td {
  padding: 10px;
  font-size: 0.9em;
}

input[type="radio"] {
  /* Hide the default radio button */
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid #ccc;
  border-radius: 50%;
  background-color: transparent;
  outline: none;
  cursor: pointer;
}

/* Style for the checked radio button */
input[type="radio"]:checked {
  border-color: var(--green);
  background-color: var(--green); /* Change to desired color */
}
</style>