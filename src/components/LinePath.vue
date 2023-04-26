<template>
  <l-polyline
    v-if="lining.latlngs"
    :lat-lngs="lining.latlngs"
    :color="lining.color"
  />
</template>

<script>
import { LPolyline } from '@vue-leaflet/vue-leaflet';
import asyncRequest from '@/js/ajax';

export default {
  components: {
    LPolyline
  },
  setup() {
    asyncRequest('function.php', (response) => {
      console.log(response);
      this.lining.latlngs = response;
    }, { 'function': 'get_path' });
    return {
      lining: {
        latlngs: [],
        color: 'red',
      },
    };
  }
};
</script>
