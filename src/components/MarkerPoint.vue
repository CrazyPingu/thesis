<template>
  <div v-for="(marker, index) in markers" :key="index">
    <l-layer-group :layer-type="'overlay'"
  :name="marker[0][1]">

      <div v-for="(info, index) in marker" :key="index">
        <l-marker :lat-lng="info[0]">
          <l-icon :icon-url="require(`@/assets/${info[1]}.svg`)"
          :icon-size="[30, 30]">
          </l-icon>
        </l-marker>
      </div>
    </l-layer-group>
  </div>
</template>

<script>
import { LLayerGroup, LMarker, LIcon } from "@vue-leaflet/vue-leaflet";
import { ref } from 'vue';
import logo from "@/assets/logo.png";
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
        const info = [
          [obj.latitudine, obj.longitudine],
          // ["<img src='/assets/`${obj.idTipologia}.svg`) + " />.svg " + obj.tipo]
          ["<img src=" + require(`@/assets/${obj.idTipologia}.svg`) + " /> " + obj.tipo]
        ];
        if (key in markers.value) {
          markers.value[key].push(info);
        } else {
          markers.value[key] = [info];
        }
      });
      console.log(markers.value);
    }, { 'function': 'get_marker' });
    return {
      logo,
      size: [15, 25],
      markers,
    };
  }
};
</script>
