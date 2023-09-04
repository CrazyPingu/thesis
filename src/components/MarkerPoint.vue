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
            v-bind:visible="getShowMarker(tableName)">
            <div v-for="marker in markers" :key="marker">
                <l-marker :lat-lng="[marker.latitudine, marker.longitudine]">
                    <l-icon :icon-url="require(`@/assets/${tableName}.png`)"
                    :icon-size="iconSize" />
                    <marker-popup
                      :marker="marker"
                      :isFavourite="listFavouriteId.indexOf(marker.idPoi)==true"
                      :userLogged="userLogged" />
                </l-marker>
            </div>
        </l-layer-group>
    </div>

    <l-layer-group layer-type="overlay"
      name="&nbsp; Favourite"
      @update:visible="checkLogin()"
      v-bind:visible="getShowMarker('favourite')" >

      <div v-for="marker in listFavourite" :key="marker">
        <l-marker :lat-lng="[marker.latitudine, marker.longitudine]">
          <l-icon :icon-url="require(`@/assets/${marker.tipologia}.png`)"
            :icon-size="iconSize" />
          <marker-popup
            :marker="marker"
            :isFavourite="true"
            :userLogged="userLogged" />
        </l-marker>
      </div>
    </l-layer-group>
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
    const listFavourite = ref([]);
    const listFavouriteId = ref([]);
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
      if(response){
        asyncRequest('function.php', (response) => {
          listFavouriteId.value = response;
        }, { 'function': 'get_favourite_id' });

        asyncRequest('function.php', (response) => {
          listFavourite.value = response;
        }, { 'function': 'get_favourite_info' });
      }
    }, { 'function': 'user_logged' });

    return {
      listFavouriteId,
      listFavourite,
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
      this.showMarkers.set('favourite', state);
    },
    checkLogin() {
      if(!this.userLogged){
        this.$router.push({ name: 'Login' });
      }
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
