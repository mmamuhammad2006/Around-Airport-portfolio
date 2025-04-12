<template>
    <div class="container mt-3 mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a :href="airportUrl()">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a
                        :href="airportCodeUrl()">
                        {{ category.name }}
                    </a>
                </li>
                <li
                    class="breadcrumb-item active"
                    aria-current="page">{{ business.name }}
                </li>
            </ol>
        </nav>

        <div
            v-if="business.name"
            class="row">
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title font-weight-bold">
                            {{ business.name }}
                            <a class="btn btn-primary btn-sm twitter-share-button"
                               target="_blank"
                               :href="`https://twitter.com/intent/tweet?original_referer=${getUrl()}&source=tweetbutton&text=${business.name}+near+${airport.name}&url=${getUrl()}`">
                                <i class="twitter-icon"></i> Tweet
                            </a>
                        </h2>
                        <div class="card-text">
                            <div
                                class="stars"
                                :style="{ 'backgroundImage': 'url(\'' + image + '\')' }">
                                <div
                                    class="fill"
                                    :style="{ 'width' : averageRating(business.google_rating) }">
                                    <img
                                        class="text-muted float-left"
                                        src="/frontend/images/star-full-img.svg"/>
                                </div>
                            </div>
                            {{ business.google_rating }} - {{ business.google_reviews.length }} votes
                        </div>
                        <p class="card-text">
                            <b>Near:</b> {{ business.airport.name }}
                            <br>
                            <b>Hours:</b> <span
                            v-for="(google_working_day, index) in business.google_working_days_opening_timing"
                            v-if="google_working_day.selected">{{ google_working_day.time }}</span>
                        </p>
                        <p class="card-text">
                            {{ business.google_formatted_address }}
                        </p>

                        <h4 class="card-title font-weight-bold">Ratings</h4>
                        <ul class="reviews-list">
                            <li>
                                <img
                                    src="/frontend/images/google-logo.png"
                                    alt="Google"/>
                                Google
                                <div
                                    class="stars"
                                    :style="{ 'backgroundImage': 'url(\'' + image + '\')' }">
                                    <div
                                        class="fill"
                                        :style="{ 'width' : averageRating(business.google_rating) }">
                                        <img
                                            class="text-muted float-left"
                                            src="/frontend/images/star-full-img.svg">
                                    </div>
                                </div>
                                {{ business.google_rating }}
                            </li>
                        </ul>

                        <h4 class="card-title font-weight-bold">Reviews for {{ business.name }}</h4>

                        <div
                            class="card bg-light mb-2"
                            v-for="google_review in business.google_reviews">
                            <div class="card-body">

                                <div class="media">
                                    <img
                                        class="mr-3"
                                        :src="google_review.profile_photo_url"
                                        :alt="google_review.author_name"
                                        style="width: 80px; height: 80px;"/>
                                    <div class="media-body">
                                        <h5 class="mt-0">{{ google_review.author_name }}</h5>

                                        <div class="card-text">
                                            <div
                                                class="stars"
                                                :style="{ 'backgroundImage': 'url(\'' + image + '\')' }">
                                                <div
                                                    class="fill"
                                                    :style="{ 'width': percentageRating(google_review.rating) }">
                                                    <img
                                                        class="text-muted float-left"
                                                        src="/frontend/images/star-full-img.svg">
                                                </div>
                                            </div>
                                            <small class="text-muted float-right">
                                                {{ google_review.relative_time_description }}
                                            </small>
                                        </div>
                                        <p class="card-text pt-2 review-text">
                                            {{ google_review.text }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3 mb-3">
                    <div class="card-body">
                        <h2 class="card-title font-weight-bold">
                            {{ business.name }} Photos
                        </h2>

                        <div class="card-body">
                            <div class="row">
                                <div
                                    class="col-md-4 px-md-2 px-0"
                                    v-for="(photo, i) in business.google_photos">
                                    <div class="min-img-thumbnail-wrapper">
                                        <img
                                            :src="photo.photo_url" :alt="business.name"
                                            class="img-fluid img-thumbnail min-img-thumbnail"
                                            :key="i"
                                            @click="index = i">
                                        <vue-gallery-slideshow
                                            :images="getImages"
                                            :index="index"
                                            @close="index = null"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center font-weight-bold">{{ business.name }}</h4>
                        <div class="responsive-map-container">
                            <!-- place the iframe code between here... -->
                            <iframe
                                id="my_address"
                                scrolling="no"
                                marginheight="1500"
                                marginwidth="1500"
                                :src="'https://maps.google.com/maps?q=' + business.name + ',' + business.google_formatted_address + '&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=&amp;output=embed'"
                                width="600"
                                height="500"
                                frameborder="0"/>
                            <!-- ... and here -->
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="btn-group">
                                <a
                                    role="button"
                                    :href="'http://maps.apple.com/?q=' + business.name + ',' + business.google_formatted_address"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-dark">
                                    <i class="fa fa-location-arrow"></i>
                                    Apple Map</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold">More Info</h6>
                        <p class="card-text">
                            Distance: {{ business.distance_text }}
                        </p>
                    </div>
                </div>

                <div class="card mt-3" v-if="business.category">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold">Nearby {{ business.category.name }}</h6>
                        <p
                            class="card-text"
                            v-for="(place, slug) in business.near_by_places">
                            <a @click="businessDetailUrl(place)">{{ place }}</a>
                        </p>
                    </div>
                </div>

                <div
                    class="card mt-3"
                    v-if="business.google_working_days_opening_timing.length > 0">
                    <div class="card-body">
                        <h6 class="card-title font-weight-bold">Hours</h6>
                        <table class="table">
                            <thead>
                            </thead>
                            <tbody>
                            <tr
                                v-for="(google_working_day, index) in business.google_working_days_opening_timing"
                                :class="google_working_day.class">
                                <td>{{ google_working_day.day }}</td>
                                <td>{{ google_working_day.time }}</td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import VueGallerySlideshow from 'vue-gallery-slideshow';

export default {
    props: {
        code: {
            type: String,
            required: true
        },
        category_slug: {
            type: String,
            required: true
        },
        slug: {
            type: String,
            required: true
        }

    },
    data() {
        return {
            airport: {},
            category: {},
            business: {},
            image: '/frontend/images/null_stars.svg',
            index: null
        }
    },
    components: {
        VueGallerySlideshow
    },
    computed: {
        /**
         * Get slider images.
         */
        getImages: function () {
            let _self = this;
            let images = [];

            if (this.business.google_photos.length > 0) {
                _.each(this.business.google_photos, function (image) {
                    images.push({url: image.photo_url, alt: _self.business.name});
                });
                return images;
            }
            return images;
        }
    },

    mounted() {
        this.asyncData(this);
    },
    methods: {
        airportUrl() {
            return route('airports.management');
        },
        businessDetailUrl(place) {
            window.location.href =
                route('airports.business.detail', {
                    code: this.code,
                    category: this.category_slug,
                    slug: place.slug
                });
        },
        /**
         * Fetch place
         * @param context
         * @returns {Promise<unknown>}
         */
        asyncData(context) {
            let _self = this;
            _self.$root.startLoading();
            return Promise.all([
                axios.get(route('api.airports.show', {airport: this.code})),
                axios.get(route('api.categories.show', {category: this.category_slug})),
                axios.get(route('api.places.show', {code: this.code, category: this.category_slug, slug: this.slug}))
            ]).then((response) => {
                _self.airport = response[0].data.data;
                _self.category = response[1].data.data;
                _self.business = response[2].data.data;
                _self.$root.stopLoading();

            }).catch(error => {
                _self.$root.stopLoading();
                console.log(error)
            });
        },
        /**
         * Category detail page.
         *
         * @returns {string}
         */
        airportCodeUrl() {
            return route('airports.code.category', {code: this.code, category: this.category_slug});
        },
        /**
         * Calculate average of ratings.
         * @param value
         * @returns {string}
         */
        averageRating(value) {
            if (!value) {
                return 0 + '%';
            }

            return (parseFloat(value) / parseFloat(5)) * 100 + '%';
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
    }
}
</script>

<style scoped>
.responsive-map-container {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 30px;
    height: 0;
    overflow: hidden;
}

.responsive-map-container iframe,
.responsive-map-container object,
.responsive-map-container embed {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.review-text {
    font-size: 13px !important;
}

.reviews-list {
    padding: 0;
}

.reviews-list li {
    list-style: none;
}

.table th, .table td {
    padding: 0.55rem !important;
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

.min-img-thumbnail {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;

}

.min-img-thumbnail-wrapper {
    height: 150px !important;
    margin: 8px 0;
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
