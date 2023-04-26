<template>
  <h1>Mappa dell'Emilia Romagna con punti di interesse</h1>
  <div class="map">
    <l-map :zoom="zoom" :center="center">
      <l-tile-layer :url="url" :attribution="attribution" />
      <l-marker :lat-lng="markerLatLng">
        <l-popup>{{ markerPopup }} <br> haa</l-popup>
      </l-marker>
      <!-- <percorso></percorso> -->
      <!-- <div v-if="polygon.length > 0"> -->
        <l-polyline :lat-lngs="polygon.latlngs" :color="polygon.color" />
      <!-- </div> -->
      <!-- <l-polygon :lat-lngs="polygon.latlngs" :color="polygon.color" /> -->
    </l-map>
  </div>
</template>

<script>
// import { LMap, LPolygon, LTileLayer } from "@vue-leaflet/vue-leaflet";
import { LMap, LTileLayer, LMarker, LPopup, LPolyline } from "@vue-leaflet/vue-leaflet";
// import Percorso from "@/components/MappaPercorso.vue";

export default {
  name: "HomePage",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    LPolyline,
    // Percorso,
  },
  setup() {
    console.log('setup');
    return {
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      zoom: 8,
      // Around the middle of the region
      center: [44.499211, 11.2492853],
      markerLatLng: [44.1481831, 12.2354155],
      markerPopup: 'Hello, this is a marker!',
      // polygon: [],
      polygon: {
        latlngs: [
          [44.5343768,10.5664061],
          [45.2035418, 12.0933341],
          [44.3188888,11.2002901],
          [44.2035418, 12.0933341],
        ],
        color: 'red',
      },
    };
  },
};
</script>

<style scoped>
.map {
  margin: auto;
  height: 70vh;
  width: 70%;
}
</style>