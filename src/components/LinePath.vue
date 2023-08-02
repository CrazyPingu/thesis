<template>
  <l-layer-group
    layer-type="overlay"
    @update:visible="getNearPath(this.location)"
    name="&nbsp; Show path near you"
    :visible="false"
  />

  <l-layer-group
    layer-type="overlay"
    @update:visible="getAllPath()"
    name="&nbsp; Show all the path"
    :visible="false"
  />

  <l-layer-group
    v-if="lining.latlngs"
    layer-type="overlay"
    :name="layerDescription">

    <l-polyline
      v-for="(line, index) in lining.latlngs" :key="index"
      :lat-lngs="line"
      :color="lining.color[index][0]"
      @ready="onClick($event, lining.id[index][0])"
      :weight="4"
    />

  </l-layer-group>
  <l-marker
    @add="$nextTick(() => $event.target.openPopup())"
    ref="marker"
    :lat-lng="markerInfo.latlng"
    :visible="markerInfo.visible">
    <l-popup ref="popup" :visible="markerInfo.visible">
      <table>
        <tbody>
          <tr v-for="(value, key) in popupContent" :key="key">
            <div v-if="value != null && value != '' &&
              !((key == 'tempo_ritorno' || key == 'tempo_andata') &&
              (value == 'http://w' || value == 'https://'))">
              <th> {{ key }}</th>
              <td v-if="key != 'link_google' && key != 'link'">
                {{ value }}
              </td>
              <td v-else-if="key == 'link_google'">
                <a :href="value">Open Google Link</a>
              </td>
              <td v-else-if="key == 'link'">
                <a :href="value">Open Link</a>
              </td>
            </div>
          </tr>
        </tbody>
      </table>
      <div class="buttonContainer">
        <button @click="markerInfo.visible = false">Remove marker</button>
      </div>
    </l-popup>
  </l-marker>
</template>

<script>
import { LPolyline, LLayerGroup, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import asyncRequest from '@/js/ajax';

export default {
  emits: ['showLoading'],
  components: {
    LPopup,
    LMarker,
    LPolyline,
    LLayerGroup
  },
  data() {
    return {
      markerInfo: {
        latlng: [-1, -1],
        visible: false,
        test: 'test'
      },
      lining: {
        latlngs: [],
        color: [],
        id: [],
      },
      mapDifficultyColor: {
        'E - Escursionistico': 'red',
        'EE - Difficile': 'blue',
        'EEA - Attrezzato': 'green',
        'T - Turistico': 'yellow'
      },
      location: {},
      popupContent: {},
    };
  },
  methods: {
    onClick(path, id) {
      path.on('click', (ev) => {
        this.markerInfo.visible = true;
        this.markerInfo.latlng = ev.latlng;
        this.$refs.marker.leafletObject.openPopup();
        this.updatePopupContent(id);
      });
    },
    updatePopupContent(id) {
      asyncRequest('function.php', (response) => {
        this.popupContent = response[0];
      }, { 'function': 'get_path_info', 'path_id': id });
    },
    getNearPath(position){
      this.$emit('showLoading', true);
      asyncRequest('function.php', (response) => {
        this.lining.latlngs = response.map(innerArray =>
          innerArray.map(subArray => [subArray[0], subArray[1]])
        );
        this.lining.color = response.map(innerArray =>
          innerArray.map(subArray => this.mapDifficultyColor[subArray[2]] || '#000000')
        );
        this.lining.id = response.map(innerArray =>
          innerArray.map(subArray => subArray[3])
        );
        this.$emit('showLoading', false);
      }, { 'function': 'get_path_near', 'lat': position['latitude'], 'lng': position['longitude'] });
    },
    getAllPath() {
      this.$emit('showLoading', true);
      asyncRequest('function.php', (response) => {
        this.lining.latlngs = response.map(innerArray =>
          innerArray.map(subArray => [subArray[0], subArray[1]])
        );
        this.lining.color = response.map(innerArray =>
          innerArray.map(subArray => this.mapDifficultyColor[subArray[2]] || '#000000')
        );
        this.lining.id = response.map(innerArray =>
          innerArray.map(subArray => subArray[3])
        );
        this.$emit('showLoading', false);
      }, { 'function': 'get_path' });
    },
  },
  created() {
    if ("geolocation" in navigator) {
      const self = this;
      navigator.geolocation.getCurrentPosition(
        function(position) {
          const latitude = position.coords.latitude;
          const longitude = position.coords.longitude;
          self.location = { latitude, longitude };
          self.getNearPath(self.location);
        },
        this.getAllPath,
        { maximumAge: 60000, timeout: 5000, enableHighAccuracy: true }
      );
    } else {
      this.getAllPath();
    }

    asyncRequest('function.php', (response) => {
      this.popupContent = response[0];
    }, { 'function': 'get_path_info', 'path_id': "E6C931D9-A7CE-4A8B-88C1-DE7BBBFC6024" });
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
