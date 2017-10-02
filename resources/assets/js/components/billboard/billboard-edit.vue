<template>
    <div>
        <box>
            <box-title>
                Billboards DashBoard
            </box-title>
            <box-body>

                <!-- billboard  -->
                <column size="5">
                    <form-submit v-model="form" @submit="save">
                        <row>
                            <column size="12">
                                <form-group :form="form" field="name">
                                    <input-label for="name">Name: </input-label>
                                    <input-text v-model="form.name" id="name" name="name"></input-text>
                                </form-group>
                                <form-group :form="form" field="description">
                                    <input-label for="description">Description: </input-label>
                                    <text-area v-model="form.description" id="description"
                                               name="description"></text-area>
                                </form-group>
                                <form-group :form="form" field="address">
                                    <input-label for="address">Address: </input-label>
                                    <input-text v-model="form.address" id="address" name="address"></input-text>
                                </form-group>
                            </column>
                            <column size="12">
                                <gmap-map
                                        :center="center"
                                        :zoom="zoom"
                                        @click="onMapClick"
                                        @zoom_changed="onZoomChanged"
                                        :options="mapOptions"
                                        style="width: 100%; min-height: 320px">
                                    <gmap-marker
                                            v-if="marker"
                                            :position="marker"
                                            :clickable="true"
                                            :draggable="true"
                                            @dragend="onMarkerMoved"
                                            @click="center=marker"
                                    ></gmap-marker>
                                </gmap-map>
                            </column>
                            <column size="6">
                                <form-group :form="form" field="lat">
                                    <input-label for="lat">Latitude: </input-label>
                                    <input-text v-model="form.lat" id="lat" name="lat"></input-text>
                                </form-group>
                            </column>
                            <column size="6">
                                <form-group :form="form" field="lng">
                                    <input-label for="lng">Longitude: </input-label>
                                    <input-text v-model="form.lng" id="lng" name="lng"></input-text>
                                </form-group>
                            </column>
                            <column size="12">
                                <form-group :form="form" field="digital_driveby">
                                    <input-label for="digital_driveby">Digital Driveby: </input-label>
                                    <input-text v-model="form.digital_driveby" id="digital_driveby"
                                                name="digital_driveby"></input-text>
                                </form-group>
                            </column>
                        </row>
                        <hr>
                        <column size="12">
                            <form-group :form="form" field="name">
                                <column size="7">
                                </column>
                                <column size="3">
                                    <btn-default
                                    >
                                        <a href="http://signly.dev/billboards">CANCEL</a>
                                    </btn-default>
                                </column>
                                <column size="2">
                                    <btn-submit :disabled="form.busy">
                                        <spinner v-if="form.busy"></spinner>
                                    </btn-submit>
                                </column>
                            </form-group>
                        </column>

                    </form-submit>
                </column>

                <!-- faces  -->
                <column size="7">
                    <billboard-face-list-card
                            :billboard-id="id"
                    >
                    </billboard-face-list-card>
                </column>
            </box-body>
        </box>
    </div>
</template>

<style lang="scss" scoped="scoped">
    .margin-billboard-edit {
        margin-right: 5px;
    }
</style>

<script>
    import * as Slc from "../../vue/http";
    import BillboardFaceForm from '../billboard-face/billboard-face-form.vue';
    import ModalForm from '../shared/Mixins/ModalForm';
    export default {
        props: {
            id: {required: true}
        },
        mixins: [ModalForm],
        components: {
            BillboardFaceForm
        },
        data() {
            return {
                form: new SlcForm({}),
                loading: false,
                api: 'billboard',
                marker: null,
                zoom: 7,
                center: {lat: 39.3209801, lng: -111.09373110000001},
                mapOptions: {
                    mapTypeControl: false,
                    scrollWell: true,
                    gestureHandling: 'greedy'
                },
                zoomChanged: false,
                billboardFaces: []
            }
        },
        watch: {
            'form.address': function () {
                this.onAddressChange();
            }
        },
        created() {
            const self = this;
            this.load();
        },
        methods: {
            buildForm(billboard) {
                this.marker = null;
                this.address = null;
                this.zoom = 7;
                this.center = {lat: 39.3209801, lng: -111.09373110000001};
                this.zoomChanged = false;
                return new SlcForm({
                    id: billboard ? billboard.id : null,
                    name: billboard ? billboard.name : null,
                    description: billboard ? billboard.description : null,
                    digital_driveby: billboard ? billboard.digital_driveby : null,
                    address: billboard ? billboard.address : null,
                    lat: billboard ? billboard.lat : null,
                    lng: billboard ? billboard.lng : null,
                });
            },
            onMapClick(e) {
                const self = this;
                console.log(e);
                if (this.marker) {
                    return;
                }
                const geocoder = new google.maps.Geocoder;
                const pos = {
                    lat: e.latLng.lat(),
                    lng: e.latLng.lng(),
                };
                geocoder.geocode({'location': pos}, (results, status) => {
                    console.log("Geocode", results, status);
                    if (!results.length || status !== 'OK') {
                        return;
                    }
                    if (self.form.address) {
                        return;
                    }
                    const result = results[0];
                    self.form.address = result.formatted_address;
                    self.form.lat = pos.lat;
                    self.form.lng = pos.lng;
                });
                this.marker = pos;
                this.center = pos;
                if (self.zoomChanged) {
                    return;
                }
                this.zoom = 15;
            },
            onZoomChanged(e) {
                console.log("On Zoom Changed", e);
                this.zoomChanged = true;
            },
            onAddressChange: _.debounce(function (e) {
                console.log("OnAddressChange", e);
                const self = this;
                const geocoder = new google.maps.Geocoder;
                geocoder.geocode({address: self.form.address}, (results, status) => {
                    console.log("Geocode From Address", results, status);
                    if (!results.length || status !== 'OK') {
                        return;
                    }
                    const result = results[0];
                    const location = result.geometry.location;
                    const pos = {
                        lat: location.lat(),
                        lng: location.lng(),
                    };
                    self.form.lat = pos.lat;
                    self.form.lng = pos.lng;
                    self.marker = pos;
                    self.center = pos;
                    if (self.zoomChanged) {
                        return;
                    }
                    self.zoom = 15;
                });
            }, 500),
            onMarkerMoved: _.debounce(function (e) {
                console.log('On Marker Moved', e);
                const pos = {
                    lat: e.latLng.lat(),
                    lng: e.latLng.lng(),
                };
                this.form.lat = pos.lat;
                this.form.lng = pos.lng;
                this.marker = pos;
                this.center = pos;
            }),
            load() {
                this.loading = true;
                const uri = laroute.route('api.billboard.show', {billboard: this.id});
                Slc.find(uri).then((billboard) => {
                    console.log(billboard);
                    this.loading = false;
                    this.form = new SlcForm({
                        id: billboard.id,
                        name: billboard.name,
                        description: billboard.description,
                        digital_driveby: billboard.digital_driveby,
                        address: billboard.address,
                        lat: billboard.lat,
                        lng: billboard.lng,
                    });
                });
            },
        }
    }
</script>