<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a :href="airportUrl()">Home</a></li>
                <li
                    class="breadcrumb-item active"
                    aria-current="page">{{ airport.name }}
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 box-shadow">
                    <img
                        v-if="airport.bio_image != null"
                        class="card-img-top"
                        :src="airport.bio_image"
                        :alt="airport.name">
                    <img
                        v-else
                        class="card-img-top"
                        src="/frontend/images/default.png"
                        :alt="airport.name"
                        style="height: 500px;">
                    <div
                        v-if="airport.name"
                        class="card-body">
                        <h5
                            class="card-title">
                            {{ airport.name }} ({{ airport.code }})
                            <a class="btn btn-primary btn-sm twitter-share-button"
                               target="_blank"
                               :href="'https://twitter.com/intent/tweet?original_referer=' + getUrl() + '&source=tweetbutton&text=' + airport.name + '&url=' + getUrl()">
                                <i class="twitter-icon"></i> Tweet
                            </a>
                        </h5>

                        <div class="card-text">
                            <div class="stars" :style="{ 'backgroundImage': 'url(\'' + image + '\')' }">
                                <div class="fill" :style="{ 'width': percentageRating(airport.google_rating) }">
                                    <img class="text-muted float-left" src="/frontend/images/star-full-img.svg">
                                </div>
                            </div>
                        </div>

                        <p class="card-text">
                            Helping you quickly find places of interest around {{ airport.name }}. Choose a
                            category to explore places around the airport.
                        </p>

                        <p class="card-text">
                            <strong>City: </strong> {{ airport.city }}
                            <strong>State: </strong> {{ airport.state }}
                            <strong>Country: </strong> {{ airport.country }}
                        </p>

                        <p class="card-text">
                            <strong>Address: </strong> {{ airport.google_formatted_address }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a role="button" :href="'https://maps.google.com/maps?q=' + airport.name"
                                   target="_blank" class="btn btn-sm btn-outline-dark"><i
                                    class="fa fa-map-marker-alt"></i> Google Map</a>
                                <a role="button" :href="'http://maps.apple.com/?q=' + airport.name" target="_blank"
                                   class="btn btn-sm btn-outline-dark"><i class="fa fa-location-arrow"></i> Apple
                                    Map</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        code: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            airport: {},
            image: '/frontend/images/null_stars.svg'
        }
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        airportUrl() {
            return route('airports.management');
        },
        fetchData() {
            let _self = this;
            _self.$root.startLoading();
            axios.get(route('api.airports.show', {airport: this.code})).then(response => {
                if (response.data.status) {
                    _self.airport = response.data.data;
                }
                _self.$root.stopLoading();
            }).catch(error => {
                console.log(error);
                _self.$root.stopLoading();
            });
        },
        /**
         * Calculate percentage of rating.
         * @param value
         * @returns {string}
         */
        percentageRating(value) {
            if (!value) {
                return 0 + '%';
            }

            return (parseFloat(value) * 100) / parseFloat(5) + '%';
        },

        /**
         * Current URL
         * @returns {*}
         */
        getUrl() {
            return window.location.href;
        }
    },
}
</script>

<style scoped>
.stars {
    background: no-repeat;
    width: 56px;
    height: 16px;
    display: inline-table;
}

.fill {
    overflow: hidden;
}

.fill img {
    width: 71px;
    height: 16px;
    position: relative;
    bottom: 1px;
    filter: invert(54%) sepia(97%) saturate(1187%) hue-rotate(1deg) brightness(110%) contrast(105%);
    vertical-align: middle;
}

.twitter-icon {
    position: relative;
    top: 2px;
    display: inline-block;
    width: 14px;
    height: 14px;
    background: transparent 0 0 no-repeat;
    background-image: url('/frontend/images/twitter.svg');
}
</style>
