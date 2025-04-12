@extends('frontend.layouts.default')
@section('title', "$place->name near $airport->name ($airport->code)")
@section('description',"$place->plug")
@section('keywords', 'around airports, airports')
@push('styles')
    <style>
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
@endpush
@section('schema')
    <script type="application/ld+json">
        {
          "@context": "https://schema.org/",
          "@type": "LocalBusiness",
          "name": "{{$place->name}}",
          "url": "{{ request()->url() }}",
          "image": "{{$place->bio_image}}",
          "address": "{{$airport->google_formatted_address}}"
          @if (!empty($place->google_rating))
            ,"aggregateRating": {
                "@type": "AggregateRating",
                "worstRating": "1",
                "bestRating": "5",
                "ratingValue": "{{$place->google_rating}}",
                "ratingCount": "{{count($place->google_reviews)}}"
            }
          @endif
        }
    </script>

{{--Example Code--}}
{{--<script type="application/ld+json">--}}
{{--{--}}
{{--  "@context" : "http://schema.org",--}}
{{--  "@type" : "Restaurant",--}}
{{--  "name" : "Up To No Good Tavern",--}}
{{--  "image" : "https://aroundairports.com/frontend/images/google-logo.png",--}}
{{--  "address" : {--}}
{{--    "@type" : "PostalAddress",--}}
{{--    "streetAddress" : "76 Market St",--}}
{{--    "addressLocality" : "Apalachicola",--}}
{{--    "addressRegion" : "FL",--}}
{{--    "addressCountry" : "USA",--}}
{{--    "postalCode" : "32320"--}}
{{--  },--}}
{{--  "url" : "https://aroundairports.com/AAF/bars",--}}
{{--  "aggregateRating" : {--}}
{{--    "@type" : "AggregateRating",--}}
{{--    "ratingValue" : "4.8",--}}
{{--    "bestRating" : "4.8",--}}
{{--    "worstRating" : "1",--}}
{{--    "ratingCount" : "5"--}}
{{--  }--}}
{{--}--}}
{{--</script>--}}
@endsection
@section('content')
    {{--    code="{{$airport->code}}"--}}
    {{--    category_slug="{{$category->slug}}"--}}
    {{--    slug="{{$place->slug}}"--}}
    <div class="container mt-3 mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('airports.management') }}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('airports.code.category', [$airport->code, $category->slug]) }}">
                        {{ $category->name }}
                    </a>
                </li>
                <li
                    class="breadcrumb-item active"
                    aria-current="page">{{ $place->name }}
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title font-weight-bold">
                            {{ $place->name }}
                            <a class="btn btn-primary btn-sm twitter-share-button"
                               target="_blank"
                               href="https://twitter.com/intent/tweet?original_referer={{ route('airports.business.detail', [$airport->code, $category->slug, $place->slug]) }}&source=tweetbutton&text={{ $place->name }}+near+{{ $airport->name }}&url={{ route('airports.business.detail', [$airport->code, $category->slug, $place->slug]) }}">
                                <i class="twitter-icon"></i> Tweet
                            </a>
                        </h2>
                        <div class="card-text">
                            <div
                                class="stars"
                                style="backgroundImage: url({{ asset('frontend/images/null_stars.svg') }});">
                                <div
                                    class="fill"
                                    style="width: {{ $place->getAverageRating() }};">
                                    <img
                                        class="text-muted float-left"
                                        src="{{ asset('frontend/images/star-full-img.svg') }}"/>
                                </div>
                            </div>
                            {{ $place->google_rating }} - {{ count($place->google_reviews) }} votes
                        </div>
                        <p class="card-text">
                            <b>Near:</b> {{ $place->airport->name }}
                            <br>
                            <b>Hours:</b>
                            @foreach($place->google_hours as $google_working_day)
                                <span>
                                @if($google_working_day['selected'])
                                        {{ $google_working_day['time'] }}
                                    @endif
                            </span>
                            @endforeach
                        </p>
                        <p class="card-text">
                            {{ $place->google_formatted_address }}
                        </p>

                        <h4 class="card-title font-weight-bold">Ratings</h4>
                        <ul class="reviews-list">
                            <li>
                                <img
                                    src="{{ asset('frontend/images/google-logo.png') }}"
                                    alt="Google"/>
                                Google
                                <div
                                    class="stars"
                                    style="backgroundImage: url({{ asset('frontend/images/null_stars.svg') }});">
                                    <div
                                        class="fill"
                                        style="width: {{ $place->getAverageRating() }};">
                                        <img
                                            class="text-muted float-left"
                                            src="{{ asset('frontend/images/star-full-img.svg') }}">
                                    </div>
                                </div>
                                {{ $place->google_rating }}
                            </li>
                        </ul>

                        <h4 class="card-title font-weight-bold">Reviews for {{ $place->name }}</h4>

                        @foreach($place->google_reviews as $google_review)
                            <div class="card bg-light mb-2">
                                <div class="card-body">
                                    <div class="media">
                                        <img
                                            class="mr-3"
                                            src="{{ $google_review['profile_photo_url'] }}"
                                            alt="{{ $google_review['author_name'] }}"
                                            style="width: 80px; height: 80px;"/>
                                        <div class="media-body">
                                            <h5 class="mt-0">{{ $google_review['author_name'] }}</h5>

                                            <div class="card-text">
                                                <div
                                                    class="stars"
                                                    style="backgroundImage: url({{ asset('frontend/images/null_stars.svg') }});">
                                                    <div
                                                        class="fill"
                                                        style="width: {{ $place->getReviewAverageRating($google_review['rating']) }};">
                                                        <img
                                                            class="text-muted float-left"
                                                            src="{{ asset('frontend/images/star-full-img.svg') }}">
                                                    </div>
                                                </div>
                                                <small class="text-muted float-right">
                                                    {{ $google_review['relative_time_description'] }}
                                                </small>
                                            </div>
                                            <p class="card-text pt-2 review-text">
                                                {{ $google_review['text'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card mt-3 mb-3">
                    <div class="card-body">
                        <h2 class="card-title font-weight-bold">
                            {{ $place->name }} Photos
                        </h2>

                        <div class="card-body">
                            <div class="row">
                                @foreach($place->google_photos as $photo)
                                    <div class="col-md-4 px-md-2 px-0">
                                        <div class="min-img-thumbnail-wrapper image-gallery">
                                            <a href="{{ $photo['photo_url'] }}">
                                                <img class="img-fluid img-thumbnail min-img-thumbnail" src="{{ $photo['photo_url'] }}" alt="{{ $place->name }}">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center font-weight-bold">{{ $place->name }}</h4>
                        <div class="responsive-map-container">
                            <!-- place the iframe code between here... -->
                            <iframe
                                id="my_address"
                                scrolling="no"
                                marginheight="1500"
                                marginwidth="1500"
                                src="https://maps.google.com/maps?q={{ $place->name }},{{ $place->google_formatted_address }}&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
                                width="600"
                                height="500"
                                frameborder="0">
                            </iframe>
                            <!-- ... and here -->
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="btn-group">
                                <a
                                    role="button"
                                    href="http://maps.apple.com/?q={{ $place->name }},{{ $place->google_formatted_address }}"
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
                            Distance: {{ $place->distance_text }}
                        </p>
                    </div>
                </div>

                @if($place->category)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">Nearby {{ $place->category->name }}</h6>
                            @foreach($place->near_by_places as $near_place)
                                <p class="card-text">
                                    {{ $near_place }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($place->google_hours) > 0)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">Hours</h6>
                            <table class="table">
                                <thead>
                                </thead>
                                <tbody>
                                @foreach($place->google_hours as $google_working_day)
                                    <tr class="{{ $google_working_day['class'] }}">
                                        <td>{{ $google_working_day['day'] }}</td>
                                        <td>{{ $google_working_day['time'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $('.image-gallery a').simpleLightbox();
    </script>
@endpush

