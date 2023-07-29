<template>
  <l-layer-group
    v-if="lining.latlngs"
    :layer-type="'overlay'"
    :name="layerDescription">

    <l-polyline
      v-for="(line, index) in lining.latlngs" :key="index"
      :lat-lngs="line"
      :color="lining.color[index][0]"
      @ready="onClick"
      :weight="4"
    />

  </l-layer-group>
  <l-marker
    @add="$nextTick(() => $event.target.openPopup())"
    ref="marker"
    :lat-lng="markerInfo.latlng"
    :visible="markerInfo.visible">
    <l-popup ref="popup" :visible="markerInfo.visible">
      <div>
        <h3>Marker</h3>
        <p>Lat: {{ markerInfo.latlng['lat'] }}</p>
        <p>Lng: {{ markerInfo.latlng['lng'] }}</p>
        <a :href="getGoogleStreetViewLink">
          Open Google Street View
        </a>
      </div>
    </l-popup>
  </l-marker>
</template>

<script>
import { LPolyline, LLayerGroup, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import asyncRequest from '@/js/ajax';
import { ref } from 'vue';

export default {
  components: {
    LPopup,
    LMarker,
    LPolyline,
    LLayerGroup
  },
  setup() {
    const markerInfo = ref({
      latlng: [-1, -1],
      visible: false
    });
    const lining = ref({
      latlngs: [],
      color: []
    });
    const mapDifficultyColor = {
      'E - Escursionistico': 'red',
      'EE - Difficile': 'blue',
      'EEA - Attrezzato': 'green',
      'T - Turistico': 'yellow'
    };
    asyncRequest('function.php', (response) => {
      lining.value.latlngs = response.map(innerArray =>
        innerArray.map(subArray => [subArray[0], subArray[1]])
      );
      lining.value.color = response.map(innerArray =>
        innerArray.map(subArray => mapDifficultyColor[subArray[2]] || '#000000')
      );
    }, { 'function': 'get_path' });
    return {
      markerInfo,
      lining,
    };
  },
  methods: {
    onClick(path) {
      path.on('click', (ev) => {
        this.markerInfo.visible = true;
        this.markerInfo.latlng = ev.latlng;
        this.$refs.marker.leafletObject.openPopup();
      });
    }
  },
  computed: {
    layerDescription() {
      return "<img src=" + require(`@/assets/Percorso.png`) + " /> " + "Percorso";
    },
    getGoogleStreetViewLink() {
      const lat = this.markerInfo.latlng['lat'];
      const lng = this.markerInfo.latlng['lng'];
      return `http://maps.google.com/maps?q=&layer=c&cbll=${lat},${lng}&cbp=11,0,0,0,0`;
    },
  }
};
</script>