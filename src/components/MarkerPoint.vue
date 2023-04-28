<template>
  <l-layer-group layerType="overlay" name="Marker 1">
    <l-marker :lat-lng="marker1.latLng">
      <l-popup>{{ marker1.popup }}</l-popup>
    </l-marker>
  </l-layer-group>
  <l-layer-group layerType="overlay" name="Marker 2">
    <l-marker :lat-lng="marker2.latLng">
      <l-popup>{{ marker2.popup }}</l-popup>
    </l-marker>
  </l-layer-group>
</template>

<script>
import { LMarker, LPopup, LLayerGroup } from "@vue-leaflet/vue-leaflet";

export default {
  components: {
    LMarker,
    LPopup,
    LLayerGroup,
  },
  setup() {

    return {
      marker1: {
        latLng: [44.1481831, 12.2354155],
        popup: 'Hello, this is a marker!',
      },
      marker2: {
        latLng: [45.1481831, 12.2354155],
        popup: 'Hello, 2! ',
      },
    };
  }
};
</script>