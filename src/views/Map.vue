<template>
  <h1>Mappa dell'Emilia Romagna</h1>
  <div class="map">
    <l-map :zoom="zoom" :center="center">
      <l-control-layers />

      <!-- Add a zoom scale -->
      <l-control-scale position="bottomleft" :imperial="false" :metric="true" />

      <!-- Add the tile layer -->
      <l-tile-layer
      v-for="tileProvider in tileProviders"
        :key="tileProvider.name"
        :name="tileProvider.name"
        :visible="tileProvider.visible"
        :url="tileProvider.url"
        :attribution="tileProvider.attribution"
        layer-type="base"/>

      <!-- Add the marker -->
      <marker-point />

      <!-- Add the path -->
      <line-path />
    </l-map>
  </div>
</template>

<script>
import { LMap, LTileLayer, LControlScale,  LControlLayers } from "@vue-leaflet/vue-leaflet";
import LinePath from "@/components/LinePath.vue";
import MarkerPoint from "@/components/MarkerPoint.vue";

export default {
  name: "HomePage",
  components: {
    LMap,
    LTileLayer,
    LControlScale,
    LinePath,
    LControlLayers,
    MarkerPoint,
  },
  setup() {
    return {
      // The url of the map
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      // The zoom to start the map
      zoom: 8,
      // Around the middle of the region
      center: [44.499211, 11.2492853],
      // The tile providers
      tileProviders: [
        {
          name: '&nbsp Street Map',
          visible: true,
          attribution:
            '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
          url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        },
        {
          name: '&nbsp Topographic Map',
          visible: false,
          url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
          attribution:
            'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
        },
      ],
    };
  },
};
</script>

<style>
.map {
  margin: auto;
  height: calc(100vh - var(--h1_font_size) -
    var(--div_links_height) * 2 - var(--h1_margin) * 2);
  width: 90%;
}

.leaflet-control-layers-overlays {
  text-align: left;
}

.leaflet-control-layers.leaflet-control span {
  display: flex;
  align-items: center;
  justify-content: left;
}

.leaflet-control-layers.leaflet-control input[type="checkbox"] {
  margin: auto 0;
}


.leaflet-control-layers.leaflet-control span img{
  width: 1.3rem;
  padding: 0 0.3rem;
}
</style>