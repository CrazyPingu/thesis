<template>
  <l-polyline :lat-lngs="lining.latlngs" :color="lining.color" />
</template>

<script>
import { LPolyline } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  components: {
    LPolyline
  },
  setup() {
    return {
      lining: {
        latlngs: [
          [44.5343768, 10.5664061],
          [45.2035418, 12.0933341],
          [44.3188888, 11.2002901],
          [44.2035418, 12.0933341],
          [44.2035418, 13.0933341],
        ],
        color: 'red',
      },
    };
  }
};
</script>
