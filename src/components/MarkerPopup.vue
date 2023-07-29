<template>
  <l-popup>
    <table>
      <tbody>
        <tr v-for="(value, key) in marker" :key="key">
          <div v-if="value != null && value != ''">
            <th>{{ key }}</th>
            <td>{{ value }}</td>
          </div>
        </tr>
      </tbody>
    </table>
    <div class="buttonContainer" v-if="userLogged">
      <button @click="addToFavourite">Add to favourite</button>
    </div>
  </l-popup>
</template>



<script>
import { LPopup } from "@vue-leaflet/vue-leaflet";

export default {
  components: {
    LPopup
  },
  props: {
    marker: {
      type: Object,
      required: true
    },
    userLogged: {
      type: Boolean,
      required: true
    }
  },
  methods: {
    addToFavourite() {
      console.log("Add to favourite " + this.marker.idPoi);
      // TODO : add to favourite
    }
  }
};

</script>


<style>
table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}

th,
td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: var(--th_highlight);
}

td {
  background-color: white;
}

h2 {
  font-size: 18px;
  margin-bottom: 10px;
}

tr div {
  display: grid;
  grid-template-columns: 1fr 1fr;
}

.buttonContainer {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

button {
  margin: 1vmin 0;
}
</style>
