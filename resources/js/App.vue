<script>
import Search from "./components/Search.vue";
import { Fetch } from "./utils/fetch/fetch";
import config from "./config/app.config";
export default {
    data() {
        return {
            isMapOpen: false,
            isMobile: false,
            height: "100vh",
            restaurantsLisData: [],
            keywordLocation: "Bang sue",
            config: config,
            apiKey: import.meta.env.VITE_GOOGLE_MAP_KEY,
            locationSelected: {
                lat: 13.828204,
                lng: 100.529692,
            },
            center: { lat: 51.093048, lng: 6.84212 },
        };
    },
    components: {
        Search,
    },
    methods: {
        async getRestaurant(keyword) {
            try {
                const res = await Fetch({
                    method: "GET",
                    path: "/api/restaurant/get",
                    params: { keyword: keyword },
                });
                if (res.code === 200) {
                    this.restaurantsLisData = res.result;
                }
            } catch (err) {
                console.log(err);
            }
        },
        updateKeywordLocation(newKeyword) {
            this.keywordLocation = newKeyword;
        },
        setHeight() {
            const { innerHeight } = window;
            this.isMobile = innerWidth < 768; // Set the breakpoint for mobile devices
            this.height = this.isMobile ? `${innerHeight * 0.65}px` : "80vh";
        },
    },
    mounted() {
        this.getRestaurant(this.keywordLocation);
        this.setHeight();
        window.addEventListener("resize", this.setHeight);
    },
    beforeDestroy() {
        window.removeEventListener("resize", this.setHeight);
    },
};
</script>
<template>
    <main
        class="main d-flex flex-column"
        :style="{ height: '100vh', overflow: 'hidden', position: 'relative' }"
    >
        <div
            v-if="isMapOpen && isMobile"
            :style="{
                width: '100%',
                height: '100%',
                position: 'absolute',
                backgroundColor: 'black',
                opacity: '0.5',
                top: '0',
                zIndex: '2',
            }"
        ></div>
        <div class="container-fluid">
            <div class="row p-4 h-100 gx-5">
                <div class="col-12 col-md-6">
                    <div class="row gy-3">
                        <div class="col-md-12">
                            <div class="row justify-content-center h-100">
                                <form class="col-12 mb-12 p-0 w-100">
                                    <div class="row bg-transparent gy-3">
                                        <div class="col-md-10 col-xs-12">
                                            <div
                                                class="autocomplete"
                                                :style="{
                                                    width: '100%',
                                                    height: '50px',
                                                }"
                                            >
                                                <Search
                                                    :updateKeywordLocation="
                                                        updateKeywordLocation
                                                    "
                                                    :keywordLocation="
                                                        keywordLocation
                                                    "
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-xs-12">
                                            <button
                                                :style="{
                                                    width: '100%',
                                                    height: '50px',
                                                }"
                                                type="button"
                                                class="btn btn-primary rounded-5"
                                                @click="
                                                    getRestaurant(
                                                        keywordLocation
                                                    )
                                                "
                                            >
                                                Search
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-12 p-2">
                            <div class="row align-items-end">
                                <div class="col-md-auto">
                                    <h1 :style="{ lineHeight: '20px' }">
                                        Restaurants
                                    </h1>
                                </div>
                                <div class="col-md-6">
                                    <span
                                        :style="{
                                            lineHeight: '10px',
                                            opacity: '0.7',
                                        }"
                                    >
                                        {{ restaurantsLisData?.length }} Result
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12"
                            :style="{
                                overflowY: 'auto',
                                height: height,
                                borderRadius: '15px',
                            }"
                        >
                            <div class="row gy-2">
                                <div
                                    :key="index"
                                    v-for="(
                                        restaurant, index
                                    ) in restaurantsLisData"
                                    class="col-md-12 bg-white rounded-5"
                                    @click="
                                        locationSelected = {
                                            lat: restaurant?.lat,
                                            lng: restaurant?.lng,
                                            name: restaurant?.name,
                                            placeId: restaurant?.placeId,
                                        }
                                    "
                                >
                                    <div class="restaurants-box row p-1">
                                        <div
                                            class="col-md-3 p-0 rounded-5"
                                            :style="{
                                                overflow: 'hidden',
                                                height: isMobile
                                                    ? '250px'
                                                    : '150px',
                                                borderRadius: '10px',
                                                textAlign: 'center',
                                                backgroundColor: 'gray',
                                            }"
                                        >
                                            <img
                                                :src="
                                                    restaurant?.photoReference !==
                                                    null
                                                        ? `${this.config.googlePlacePhotoApi}?maxwidth=400&photo_reference=${restaurant.photoReference}&key=${apiKey}`
                                                        : '../../3901287.jpg'
                                                "
                                                :style="{
                                                    height: '100%',
                                                    display: 'block',
                                                    margin: '0 auto',
                                                }"
                                            />
                                        </div>
                                        <div
                                            class="col-md-9 pt-2 pl-3 pb-2 pl-2"
                                        >
                                            <div class="row">
                                                <span
                                                    class="font-weight-bold"
                                                    :style="{
                                                        fontSize: '20px',
                                                        fontWeight: 'bold',
                                                    }"
                                                >
                                                    {{ restaurant?.name }}
                                                </span>
                                            </div>

                                            <div class="row">
                                                <span
                                                    class="font-weight-bold"
                                                    :style="{
                                                        fontSize: '18px',
                                                        opacity: '0.7',
                                                    }"
                                                >
                                                    {{ restaurant?.address }}
                                                </span>
                                            </div>
                                            <div class="row">
                                                <span
                                                    class="font-weight-bold"
                                                    :style="{
                                                        fontSize: '18px',
                                                        opacity: '0.7',
                                                    }"
                                                >
                                                    tel:{{ restaurant?.tel }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="col-12 col-md-6"
                    :style="
                        isMobile
                            ? {
                                  transition: 'ease-in 0.3s',
                                  position: 'absolute',
                                  height: '80%',
                                  width: '100%',
                                  bottom: isMapOpen ? '0px' : '-70%',
                                  left: '0',
                                  zIndex: '2',
                                  padding: '10px',
                              }
                            : {
                                  height: '100%',
                              }
                    "
                >
                    <div class="row justify-content-center">
                        <div class="col-3">
                            <button
                                v-if="isMobile"
                                :style="{
                                    marginBottom: '10px',
                                }"
                                type="button"
                                class="btn btn-primary rounded-5"
                                @click="isMapOpen = !isMapOpen"
                            >
                                {{ isMapOpen ? "Close Maps" : "Open Maps" }}
                            </button>
                        </div>
                    </div>

                    <div
                        class="row bg-white"
                        :style="{
                            height: '100%',
                            overflow: 'hidden',
                            borderRadius: '20px',
                            boxShadow: 'rgba(99, 99, 99, 0.2) 0px 2px 8px 0px',
                        }"
                    >
                        <GMapMap
                            :center="locationSelected"
                            :zoom="15"
                            map-type-id="terrain"
                            :style="{
                                height: '100%',
                            }"
                        >
                            <GMapMarker
                                :key="index"
                                v-for="(makerData, index) in restaurantsLisData"
                                :position="{
                                    lat: makerData?.lat,
                                    lng: makerData?.lng,
                                }"
                                :aria-label="makerData?.name"
                                :clickable="true"
                                :draggable="true"
                                @click="
                                    locationSelected = {
                                        lat: makerData?.lat,
                                        lng: makerData?.lng,
                                    }
                                "
                            />
                        </GMapMap>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<style scoped>
.main {
    height: 100vh;
    background-color: #eeeeee;
}
.restaurants-box {
    border-radius: 35px;
    transition: ease-in 0.2s;
}
.restaurants-box:hover {
    cursor: pointer;
    border-radius: 35px;
    transition: ease-in 0.2s;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
}
</style>
