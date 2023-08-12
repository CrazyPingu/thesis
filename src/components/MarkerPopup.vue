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
    <div class="buttonContainer" v-if="userLogged && !favourite">
      <button @click="addToFavourite">Add to favourite</button>
    </div>
    <div class="buttonContainer" v-else-if="userLogged && favourite">
      <button @click="removeFromFavourite">Remove from favourite</button>
    </div>
    
    <div class="buttonContainer" v-if="userLogged">
      <button @click="redirectToEditInfoPage">Add extra field</button>
    </div>
    </l-popup>
</template>



<script>
import asyncRequest from "@/js/ajax";
import { LPopup } from "@vue-leaflet/vue-leaflet";
import { ref } from "vue";

export default {
  components: {
    LPopup
  },
  props: {
    marker: {
      type: Object,
      required: true
    },
    isFavourite: {
      type: Boolean,
      required: true
    },
    userLogged: {
      type: Boolean,
      required: true
    }
  },
  setup(props) {
    let favourite = ref(props.isFavourite);
    return {
      favourite
    };
  },
  methods: {
    addToFavourite() {
      asyncRequest('function.php', () => {
        this.favourite = true;
      }, { 'function': 'add_favourite', 'path_id': this.marker.idPoi });
    },
    removeFromFavourite() {
      asyncRequest('function.php', () => {
        this.favourite = false;
      }, { 'function': 'remove_favourite', 'path_id': this.marker.idPoi });
    },
    redirectToEditInfoPage() {
      this.$router.push({ name: 'Edit POI', query: { idPoi: this.marker.idPoi } });
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
