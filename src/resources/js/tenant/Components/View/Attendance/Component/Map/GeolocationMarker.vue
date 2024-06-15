<template>
    <div>
    <modal id="view-map-modal"
           size="large"
           v-model="showModal"
           :title="$t('location')"
           :scrollable="false"
           :hide-submit-button="true"
           :cancel-btn-label="$t('close')"
    >
        <app-overlay-loader v-if="preloader"/>
        <div id='show-map' style='width: 100%; height: 300px;'></div>
        <div class="w-100 py-3 px-2 alert alert-warning text-dark" v-if="message">
            <p class="m-0">{{ message }}</p>
        </div>
    </modal>
    </div>
</template>

<script>

import ModalMixin from "../../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../../common/Helper/AxiosHelper";
import { Loader } from '@googlemaps/js-api-loader';
import mapboxgl from "mapbox-gl";

export default {
    name: "GeolocationMarker",
    mixins: [ModalMixin],
    props: {
        data: {},
    },
    data() {
        return {
            message: '',
            geoSettings: null,
            map: null,
        }
    },
    created() {
        this.getGeolocationSettings()
    },

    methods: {
        getGeolocationSettings() {
            this.preloader = true;
            axiosGet(this.apiUrl.GEOLOCATION).then(({data}) => {
                this.geoSettings = data;
                if (data.service_name) {
                    if (data.service_name === 'mapbox') {
                        this.renderMapbox(data.api_key);
                    } else if (data.service_name === 'google_map') {
                        this.renderGoogleMap(data.api_key);
                    } else {
                        document.querySelector('#show-map').style.display = 'none';
                        this.message = 'No map service found!'
                    }
                } else {
                    document.querySelector('#show-map').style.display = 'none';
                    this.message = 'No map service found!'
                }
            }).catch(({response}) => {
                document.querySelector('#show-map').style.display = 'none';
                this.message = response.data?.message;
                console.log(response)
            }).finally(()=>{
                this.preloader = false;
            });
        },
        renderGoogleMap(token) {
            const loader = new Loader({
                apiKey: token,
                version: "weekly",
                libraries: ["places"]
            });
            const mapOptions = {
                center: this.data.coordinate,
                zoom: 16,
                gestureHandling: "cooperative",
            };
            loader.load().then((google) => {
                    this.map = new google.maps.Map(document.getElementById("show-map"), mapOptions);
                    new google.maps.Marker({
                        position: this.data.coordinate,
                        map: this.map,
                    });
                }).catch(err => {
                    console.log(err)
                });
        },
        renderMapbox(token) {
            mapboxgl.accessToken = token;
            this.map = new mapboxgl.Map({
                container: 'show-map', // container ID
                style: 'mapbox://styles/mapbox/streets-v11', // style URL
                center: Object.values(this.data.coordinate).reverse(), // starting position [lng, lat]
                zoom: 16 //
            });
            console.log(Object.values(this.data.coordinate));
            // this.identifyLocation(token)
            const marker = new mapboxgl.Marker({color: "#d60606"})
                .setLngLat(Object.values(this.data.coordinate).reverse())
                .addTo(this.map);
        },
    },
}
</script>

