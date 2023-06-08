<template>
  <l-layer-group
    layer-type="overlay"
    name="&nbsp Show All"
    @update:visible="toggleSelectAll"
    :visible="false"
  />
  <l-layer-group
    layer-type="overlay"
    name="&nbsp Show nothing"
    @update:visible="toggleSelectNothing"
    :visible="true"
  />
  <div
    v-for="(markers, tableName) in markers"
    :key="tableName">
    <l-layer-group
      layer-type="overlay"
      :name="getMarkerIcon(tableName)"
      :visible="selectAll">
      <div v-for="marker in markers" :key="marker">
        <l-marker
        :lat-lng="[marker.latitudine, marker.longitudine]">
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
    const selectAll = ref(false);
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
      selectAll,
      selectAllVisible: selectAll,
    };
  },
  methods: {
    getMarkerIcon(filename) {
      return "<img src=" + require(`@/assets/${filename}.png`) + " /> " + filename;
    },
    toggleSelectAll() {
      console.log("toggleSelectAll" + this.selectAll);
      this.selectAll = true;
    },
    toggleSelectNothing() {
      console.log("toggleSelectNothing" + this.selectAll);
      this.selectAll = false;
    },
  }
};
</script>

<style>

.leaflet-control-layers-overlays label:nth-child(-n+2) input[type="checkbox"] {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 15px;
  height: 15px;
  border: 2px solid #e2e2e2;
  border-radius: 5px;
  outline: none;
  transition: border-color 0.3s ease;
}


</style>
