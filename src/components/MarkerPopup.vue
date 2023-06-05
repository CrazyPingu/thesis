<template>
  <l-popup>
    <!-- <h2>{{ marker }}</h2> -->

    <table>
      <tbody>
        <tr v-for="(value, key) in marker" :key="key">
          <div v-if="value != null">
            <th>{{ key }}</th>
            <td>{{ value }}</td>
          </div>
        </tr>
      </tbody>
    </table>
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
    }
  },
};

</script>


<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  font-family: Arial, sans-serif;
}

th, td {
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
</style>
