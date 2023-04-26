<template>
  <h1>Mappa dell'Emilia Romagna con punti di interesse</h1>
  <div class="map">
    <l-map :zoom="zoom" :center="center">
      <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
      <l-marker :lat-lng="markerLatLng">
        <l-popup>{{ markerPopup }} <br> haa</l-popup>
      </l-marker>
      <percorso></percorso>
    </l-map>
  </div>
</template>

<script>
import { LMap, LTileLayer, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";
import Percorso from "@/components/MappaPercorso.vue";

export default {
  name: "HomePage",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    Percorso,
  },
  data() {
    return {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      zoom: 8,
      // Around the middle of the region
      center: [44.499211, 11.2492853],
      markerLatLng: [44.1481831, 12.2354155],
      markerPopup: 'Hello, this is a marker!',
    };
  }
};
</script>



<style scoped>
.map {
  margin: auto;
  height: 70vh;
  width: 70%;
}
</style>