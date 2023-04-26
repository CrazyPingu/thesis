<template>
  <l-marker :lat-lng="markerLatLng">
    <l-popup>{{ markerPopup }} <br> haa</l-popup>
  </l-marker>
</template>

<script>
import { LMarker, LPopup } from "@vue-leaflet/vue-leaflet";

export default {
  components: {
    LMarker,
    LPopup,
  },
  setup() {
    return {
      markerLatLng: [44.1481831, 12.2354155],
      markerPopup: 'Hello, this is a marker!',
    };
  }
};
</script>