<template>
    <div v-if="inDetails.in_ip_data">
        <app-punch-geolocation-data
            :details="inDetails"
        />
        <geolocation-marker
            v-if="mapModalOpen"
            v-model="mapModalOpen"
            :data="JSON.parse(this.inDetails.in_ip_data)"
        />
        <button type="button"
                v-if="hasCoordinates"
                class="btn p-0 mb-1 primary-text-color"
                @click="mapModalOpen = true"
        >
            <app-icon name="map" class="size-18"/>
        </button>
    </div>

    <div v-else>-</div>
</template>

<script>

import GeolocationMarker from "./Map/GeolocationMarker"

export default {
    name: "PunchInGeolocation",
    components: {GeolocationMarker},
    props: {
        value: {},
        rowData: {},
        tableId: {},
    },
    data() {
        return {
            mapModalOpen: false
        }
    },
    computed: {
        inDetails() {
            return this.collection(this.value).last();
        },
        hasCoordinates() {
            let data = JSON.parse(this.inDetails.in_ip_data);
            return data.coordinate;
        }
    },
}
</script>

