<template>
  <h1>Mappa dell'Emilia Romagna con punti di interesse</h1>
  <div class="map">
    <l-map :zoom="zoom" :center="center">
      <l-control-layers />

      <!-- Add a zoom scale -->
      <l-control-scale position="bottomleft" :imperial="false" :metric="true" />

      <!-- Add the map leaflet -->
      <l-tile-layer :url="url" />

      <!-- Add the marker -->
      <marker-point />

      <!-- Add the path -->
      <line-path />
    </l-map>
  </div>
</template>

<script>
import { LMap, LTileLayer, LControlScale,  LControlLayers } from "@vue-leaflet/vue-leaflet";
import LinePath from "@/components/LinePath.vue";
import MarkerPoint from "@/components/MarkerPoint.vue";

export default {
  name: "HomePage",
  components: {
    LMap,
    LTileLayer,
    LControlScale,
    LinePath,
    LControlLayers,
    MarkerPoint,
  },
  setup() {
    return {
      // The url of the map
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      // The zoom to start the map
      zoom: 8,
      // Around the middle of the region
      center: [44.499211, 11.2492853],
    };
  },
};
</script>

<style scoped>
.map {
  margin: 0 auto;
  height: 70vh;
  width: 80%;
}
</style>