<template>
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a :href="airportUrl()">Home</a>
                </li>
                <li
                    v-if="airport.code"
                    class="breadcrumb-item">
                    <a :href="airportCodeUrl()">{{ airport.name }}</a>
                </li>
                <li
                    class="breadcrumb-item active"
                    aria-current="page">{{ category.name }}
                </li>
            </ol>
        </nav>

        <template v-if="airport.name">
            <div class="jumbotron">
                <h1
                    class="display-4 text-break"
                    style="font-size: 2.5rem;">
                    {{ category.name }} around {{ airport.name }}
                    <a class="btn btn-primary btn-sm twitter-share-button"
                       target="_blank"
                       :href="`https://twitter.com/intent/tweet?original_referer=${getUrl()}&source=tweetbutton&text=${category.twitter_name}+near+${airport.name}&url=${getUrl()}`">
                        <i class="twitter-icon"></i> Tweet
                    </a>
                </h1>
                <p class="lead">Helping you quickly find places of interest around {{ airport.name }}. Choose a
                    category to explore places around the airport.</p>
            </div>
        </template>

        <div
            v-if="places.data"
            class="row">
            <div class="col-lg-4 col-md-6" v-for="(place, index) in places.data">
                <div class="card mb-4 box-shadow">
                    <div class="card-image-wrapper overflow-hidden">
                        <img class="card-img-top bio-img" v-if="place.bio_image != null" :src="place.bio_image"
                             :alt="place.name">
                        <img class="card-img-top bio-img" v-if="place.bio_image == null"
                             src="/frontend/images/default.png"
                             :alt="place.name">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-0">
                            {{ place.name | readMore(18, '...') }}
                        </h5>
                        <small>{{ place.google_formatted_address }}</small>

                        <div class="card-text mt-1" v-if="place.google_rating">
                            <div class="stars" :style="{ 'backgroundImage': 'url(\'' + image + '\')' }">
                                <div class="fill" :style="{ 'width': percentageRating(place.google_rating) }">
                                    <img class="text-muted float-left" src="/frontend/images/star-full-img.svg">
                                </div>
                            </div>
                        </div>

                        <p class="card-text">
                            <strong>Distance:</strong> {{ place.distance_text }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a
                                    @click="businessDetailUrl(place)"
                                    class="btn btn-sm btn-outline-dark">View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-if="places.data"
            class="row">
            <div class="col-lg-12 text-center">
                <pagination
                    :data="places"
                    :limit="-1"
                    :show-disabled="false"
                    :align="`center`"
                    @pagination-change-page="vieMore">
                    <span slot="prev-nav">Previous</span>
                    <span slot="next-nav">View More {{ category.name }}</span>
                </pagination>
            </div>
        </div>
    </div>
</template>

<script>
import Pagination from 'laravel-vue-pagination';

export default {
    props: {
        code: {
            type: [String, Number],
            required: true
        },
        category_slug: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            airport: {},
            category: {},
            places: {},
            image: '/frontend/images/null_stars.svg'
        }
    },

    components: {
        pagination: Pagination
    },
    mounted() {
        this.asyncData(this);

    },
    methods: {
        airportUrl() {
            return route('airports.management');
        },
        businessDetailUrl(place) {
            console.log(place);
            window.location.href =
                route('airports.business.detail', {
                    code: this.code,
                    category: this.category.slug,
                    slug: place.slug
                });
        },
        /**
         * Fetch data
         * @param context
         * @returns {Promise<{places: *, category: *, airport: *}>}
         */
        asyncData(context) {
            let _self = this;
            _self.$root.startLoading();
            Promise.all([
                axios.get(route('api.airports.show', {airport: this.code})),
                axios.get(route('api.categories.show', {category: this.category_slug})),
                axios.get(route('api.airports.around.places', {airport: this.code, category: this.category_slug})),
            ]).then((response) => {
                _self.airport = response[0].data.data;
                _self.category = response[1].data.data;
                _self.places = response[2].data.data;
                _self.$root.stopLoading();
            }).catch(error => {
                console.log(error);
                _self.$root.stopLoading();
            });
        },
        /**
         * Category detail page.
         *
         * @returns {string}
         */
        airportCodeUrl() {
            return route('airports.code', {code: this.code});
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
         * Get Pages Data.
         */
        vieMore(page) {
            let _self = this;
            _self.$root.startLoading();
            page = typeof page === 'undefined' ? 1 : page;
            axios.get(route('api.airports.around.places', {
                airport: this.code,
                category: this.category_slug
            }), {'params': {page: page}}).then(response => {
                if (response.status) {
                    let data = _self.places.data.concat(response.data.data.data);
                    _self.places = response.data.data;
                    _self.places.data = data;
                    _self.places.prev_page_url = null;
                }

                _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();
                console.log(error);
            });
        },

        /**
         * Current URL
         * @returns {*}
         */
        getUrl() {
            return window.location.href;
        }
    },

    filters: {
        /**
         * Read More
         * @param text
         * @param length
         * @param suffix
         * @returns {string|boolean}
         */
        readMore: function (text, length, suffix) {
            if (!text) return false;

            return text.length > length ? text.substring(0, length) + suffix : text;
        }
    }
}
</script>

<style scoped>
strong {
    font-weight: 500;
}

.bio-img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}

.stars {
    background: no-repeat;
    width: 56px;
    height: 16px;
    display: inline-table;
}

.fill {
    overflow: hidden;
}

.card-image-wrapper {
    height: 190px;
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
