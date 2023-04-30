<template>
  <div v-for="(marker, tableName) in markers" :key="tableName">
    <l-layer-group :layer-type="'overlay'" :name="getMarkerIcon(tableName)">
      <div v-for="coordinate in marker" :key="coordinate">
        <l-marker :lat-lng="coordinate">
          <l-icon
            :icon-url="require(`@/assets/${tableName}.png`)"
            :icon-size="[30, 30]"
          />
        </l-marker>
      </div>
    </l-layer-group>
  </div>
</template>

<script>
import { LLayerGroup, LMarker, LIcon } from "@vue-leaflet/vue-leaflet";
import { ref } from 'vue';
import asyncRequest from "@/js/ajax";

export default {
  components: {
    // LPopup,
    LIcon,
    LLayerGroup,
    LMarker
  },
  setup() {
    const markers = ref({});
    asyncRequest('function.php', (response) => {
      response.forEach(obj => {
        const key = obj.tipo;
        const coordinate = [obj.latitudine, obj.longitudine];
        if (key in markers.value) {
          markers.value[key].push(coordinate);
        } else {
          markers.value[key] = [coordinate];
        }
      });
    }, { 'function': 'get_marker' });
    return {
      size: [15, 25],
      markers,
    };
  },
  methods: {
    getMarkerIcon(filename) {
      return "<img src=" + require(`@/assets/${filename}.png`) + " /> " + filename;
    }
  }
};
</script>
