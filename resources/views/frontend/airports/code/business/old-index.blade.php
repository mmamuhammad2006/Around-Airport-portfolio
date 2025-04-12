@extends('frontend.layouts.default')
@section('title', "$place->name near $airport->name ($airport->code)")
@section('description',"$place->plug")
@section('keywords', 'around airports, airports')
@section('schema')
    <script type="application/ld+json">
    {
      "@context": "https://schema.org/",
      "@type": "LocalBusiness",
      "name": "{{$place->name}}",
      "url": "{{ request()->url() }}",
      "image": "{{$place->bio_image}}",
      "address": "{{$airport->google_formatted_address}}"

{{--      "aggregateRating": {--}}
{{--        "@type": "AggregateRating",--}}
{{--        "ratingValue": "{{$place->google_rating}}",--}}
{{--        "reviewCount": "{{count($place->google_reviews)}}",--}}
{{--        "bestRating": "{{collect($place->google_reviews)->max('rating')}}",--}}
{{--        "worstRating": "{{collect($place->google_reviews)->min('rating')}}"--}}
{{--      }--}}
    }
    </script>
@endsection
@section('content')
    <airports-code-business-detail
        code="{{$airport->code}}"
        category_slug="{{$category->slug}}"
        slug="{{$place->slug}}"
    ></airports-code-business-detail>
@endsection

