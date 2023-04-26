<template>
  <l-polyline v-if="lining.latlngs"
  :lat-lngs="lining.latlngs"
  :color="lining.color" />
</template>

<script>
import { LPolyline } from '@vue-leaflet/vue-leaflet';
import asyncRequest from '@/js/ajax';
import { ref } from 'vue';

export default {
  components: {
    LPolyline
  },
  setup() {
    const lining = ref({
      latlngs: [],
      color: 'red'
    });
    asyncRequest('function.php', (response) => {
      lining.value.latlngs = response;
    }, { 'function': 'get_path' });
    return {
      lining,
    };
  }
};
</script>