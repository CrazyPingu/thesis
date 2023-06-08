<template>
  <div
    v-for="(markers, tableName) in markers"
    :key="tableName">
    <l-layer-group
      layer-type="overlay"
      :name="getMarkerIcon(tableName)"
      :visible="false">
      <div v-for="marker in markers" :key="marker">
        <l-marker :lat-lng="[marker.latitudine, marker.longitudine]">
          <l-icon
            :icon-url="require(`@/assets/${tableName}.png`)"
            :icon-size="iconSize"
          />
          <marker-popup :marker="marker" :userLogged="userLogged"/>
        </l-marker>
      </div>
    </l-layer-group>
  </div>
</template>

<script>
import { LLayerGroup, LMarker, LIcon } from "@vue-leaflet/vue-leaflet";
import MarkerPopup from "@/components/MarkerPopup.vue";
import asyncRequest from "@/js/ajax";
import { ref } from 'vue';

export default {
  components: {
    MarkerPopup,
    LIcon,
    LLayerGroup,
    LMarker
  },
  setup() {
    const markers = ref({});
    const userLogged = ref(false);
    asyncRequest('function.php', (response) => {
      response.forEach(obj => {
        const key = obj.tipo;
        if (key in markers.value) {
          markers.value[key].push(obj);
        } else {
          markers.value[key] = [obj];
        }
      });
    }, { 'function': 'get_marker' });

    asyncRequest('function.php', (response) => {
      userLogged.value = response;
    }, { 'function': 'user_logged' });
    return {
      iconSize: [25, 35],
      markers,
      userLogged,
      selectAllIconUrl: require('@/assets/Campeggio.png'),
    };
  },
  methods: {
    getMarkerIcon(filename) {
      return "<img src=" + require(`@/assets/${filename}.png`) + " /> " + filename;
    },
  }
};
</script>
