<template>
  <h1>Mappa dell'Emilia Romagna</h1>
  <div class="map">
    <l-map @ready="onMapReady" :zoom="zoom" :center="center" >
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
      <line-path />

      <l-geo-json :style="{ color: 'red' }" :url="geoJsonUrl" :visible="true" />
    </l-map>
  </div>
</template>


<script>
import { LMap, LTileLayer, LControlScale, LControlLayers, LGeoJson, } from "@vue-leaflet/vue-leaflet";
import LinePath from "@/components/LinePath.vue";
import MarkerPoint from "@/components/MarkerPoint.vue";
import L from "leaflet";
import { ref } from "vue";

export default {
  name: "HomePage",
  components: {
    LMap,
    LTileLayer,
    LControlScale,
    LinePath,
    LGeoJson,
    LControlLayers,
    MarkerPoint,
  },
  setup() {
    const center = [44.499211, 11.2492853];
    const markerRef = ref(null);
    return {
      markerRef,
      geoJsonUrl:
        "https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json",
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
    onMapReady(mapIns) {
      mapIns.on("click", (ev) => {
        // Check if a marker already exists
        if (this.markerRef) {
          // Move the existing marker to the new location
          this.markerRef.setLatLng(ev.latlng);
          // Update the popup content with new coordinates
          this.markerRef.setPopupContent(this.getPopupContent);
        } else {
          // Create a new marker and add it to the map
          this.markerRef = L.marker(ev.latlng);
          this.markerRef.bindPopup(this.getPopupContent);
          this.markerRef.addTo(mapIns);
        }

        this.markerRef.openPopup();
      });
    },
  },
  computed: {
    getPopupContent() {
      const lat = this.markerRef.getLatLng()["lat"];
      const lng = this.markerRef.getLatLng()["lng"];
      return `
        <div>
          <h3>StreetView</h3>
          <p>Latitude: ${lat}</p>
          <p>Longitude: ${lng}</p>
          <a href="http://maps.google.com/maps?q=&layer=c&cbll=${lat},${lng}&cbp=11,0,0,0,0">Open Google Street View</a>
        </div>
        `;
      // return "Latitude: " + this.markerRef.latlng + "<br>Longitude: " + this.markerRef.latlng;
    }
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
</style>