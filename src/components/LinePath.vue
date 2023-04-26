<template>
  <div>
    <l-map :zoom="zoom" :center="center">
      <l-tile-layer :url="url"></l-tile-layer>
      <l-polyline :lat-lngs="coordinates" :color="color"
      :weight="weight" :opacity="opacity"></l-polyline>
    </l-map>
  </div>
</template>

<script>
import { L } from 'leaflet';
import { LMap, LTileLayer, LPolyline } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  components: {
    LMap,
    LTileLayer,
    LPolyline
  },
  setup() {
    return {
      zoom: 13,
      center: [37.7749, -122.4194],
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      coordinates: [
        L.latLng(37.7749, -122.4194),
        L.latLng(37.7753, -122.4189),
        L.latLng(37.7757, -122.4184),
        L.latLng(37.7760, -122.4179)
      ],
      color: 'red',
      weight: 5,
      opacity: 0.5
    };
  }
};
</script>
