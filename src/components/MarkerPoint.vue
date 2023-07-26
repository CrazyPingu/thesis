<template>
    <l-layer-group
    layer-type="overlay"
    name="&nbsp; Show All"
    @update:visible="toggleSelect(true)"
    :visible="false" />

    <l-layer-group layer-type="overlay"
    name="&nbsp; Show nothing"
    @update:visible="toggleSelect(false)"
    :visible="true" />

    <div v-for="(markers, tableName) in markers" :key="tableName">
        <l-layer-group layer-type="overlay" :name="getMarkerIcon(tableName)"
            @update:visible="showMarkers.set(tableName, $event)"
            v-bind:visible="getShowMarker(tableName)">
            <div v-for="marker in markers" :key="marker">
                <l-marker :lat-lng="[marker.latitudine, marker.longitudine]">
                    <l-icon :icon-url="require(`@/assets/${tableName}.png`)"
                    :icon-size="iconSize" />
                    <marker-popup :marker="marker" :userLogged="userLogged" />
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
    const showMarkers = ref(new Map());
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
      showMarkers,
    };
  },
  methods: {
    getMarkerIcon(filename) {
      return "<img src=" + require(`@/assets/${filename}.png`) + " /> " + filename;
    },
    toggleSelect(state) {
      Object.keys(this.markers).forEach((key) => {
        this.showMarkers.set(key, state);
      });
    },
    getShowMarker(tableName) {
      if (!this.showMarkers.get(tableName)) {
        this.showMarkers.set(tableName, false);
      }
      return this.showMarkers.get(tableName);
    },
  },
};
</script>

<style>
/* <div class="leaflet-control-layers-separator" style=""></div> */

/* Style the checkbox to make them look like button */
.leaflet-control-layers-overlays label:nth-child(3) input[type="checkbox"],
.leaflet-control-layers-overlays label:nth-child(2) input[type="checkbox"] {
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

/* Insert the separator div after the third label element */
.leaflet-control-layers-overlays label:nth-child(3)::after {
  content: "";
  display: block;
  border-top: 1px solid #ddd;
  margin: 5px -10px 5px -6px;
}
</style>
