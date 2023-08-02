<template>
  <h1>Mappa dell'Emilia Romagna</h1>
  <div class="map">
    <img
      src="loading.gif"
      alt="loading"
      ref="loadingImage"
    />
    <l-map :zoom="zoom" :center="center">
      <l-control-layers />

      <!-- Add a zoom scale -->
      <l-control-scale position="bottomleft" :imperial="false" :metric="true" />

      <!-- Add the tile layers -->
      <l-tile-layer
        v-for="tileProvider in tileProviders"
        :key="tileProvider.name"
        :name="tileProvider.name"
        :visible="tileProvider.visible"
        :url="tileProvider.url"
        :attribution="tileProvider.attribution"
        :layer-type="tileProvider.layerType"
      />

      <!-- Add the marker -->
      <marker-point />

      <!-- Add the path -->
      <line-path @showLoading="handleShowLoading"/>
    </l-map>
  </div>
</template>


<script>
import { LMap, LTileLayer, LControlScale, LControlLayers } from "@vue-leaflet/vue-leaflet";
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
    const center = [44.499211, 11.2492853];
    return {
      // The url of the map
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      // The zoom to start the map
      zoom: 8,
      // Around the middle of the region
      center,
      // The tile providers
      tileProviders: [
        {
          name: "&nbsp; Street Map",
          visible: true, // Set this to true to show the layer by default
          url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
          attribution:
            '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
          layerType: "base",
        },
        {
          name: "&nbsp; Topographic Map",
          visible: false,
          url: "https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png",
          attribution:
            'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
          layerType: "base",
        },
        {
          name: "&nbsp; Satellite",
          visible: false,
          url: "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}",
          attribution:
            "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, " +
            "AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community",
          layerType: "base",
        },
        {
          name: "&nbsp; Highlight railway",
          visible: false,
          url:
            "https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png",
          attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors | Map style: &copy; <a href="https://www.OpenRailwayMap.org">OpenRailwayMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
          layerType: "overlay",
        },
      ],
    };
  },
  methods: {
    handleShowLoading(status) {
      if (status) {
        this.$refs.loadingImage.style.display = "";
        this.$refs.loadingImage.classList.add("loading-image");
      } else {
        this.$refs.loadingImage.style.display = "none";
        this.$refs.loadingImage.classList.remove("loading-image");
      }
    },
  },
};
</script>

<style>
.map {
  margin: auto;
  height: calc(100vh - var(--h1_font_size)
    - var(--div_links_height) * 2 - var(--h1_margin) * 2);
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


.leaflet-control-layers.leaflet-control span img {
  width: 1.3rem;
  padding: 0 0.3rem;
}

.hidden {
  display: none;
}

.loading-image {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1000;
}
</style>