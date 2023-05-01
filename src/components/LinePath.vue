<template>
  <l-layer-group
    v-if="lining.latlngs"
    :layer-type="'overlay'"
    :name="layerDescription">

    <l-polyline
      :lat-lngs="lining.latlngs"
      :color="lining.color"
    />

  </l-layer-group>
</template>

<script>
import { LPolyline, LLayerGroup } from '@vue-leaflet/vue-leaflet';
import asyncRequest from '@/js/ajax';
import { ref } from 'vue';

export default {
  components: {
    LPolyline,
    LLayerGroup
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
  },
  computed: {
    layerDescription() {
      return "<img src=" + require(`@/assets/Percorso.png`) + " /> " + "Percorso";
    }
  }
};
</script>