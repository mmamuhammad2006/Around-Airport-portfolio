@extends('frontend.layouts.default')
@section('title',"$category->name near $airport->name ($airport->code) in $airport->google_formatted_address aroundairports.com")
@section('description',"Find the closest $category->name around or near $airport->name - $airport->code. Maps, phone numbers and directions to businesses around the airport in $airport->google_formatted_address")
@section('keywords', 'around airports, airports')
@section('schema')
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "{{$airport->latitude}}",
    "longitude": "{{$airport->longitude}}"
  },
  "address": "{{$airport->google_formatted_address}}",
  "name": "{{$category->name}}",
  "description": "{{"Find the closest $category->name around or near $airport->name - $airport->code. Maps, phone numbers and directions to businesses around the airport in $airport->google_formatted_address"}}",
    "url": "{{ request()->url() }}",
    "image": "{{'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($airport->name)}}"
}
</script>


@endsection
@section('content')

    <div class="container mt-3 mb-5">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                @include('frontend.airports.code._partials.airport-code-sidebar', ['categories' => $categories])
            </div>
            <div class="col-lg-9 col-sm-12">
                <airports-code-category-detail
                    code="{{$airport->code}}"
                    category_slug="{{$category->slug}}"
                ></airports-code-category-detail>
            </div>
        </div>
    </div>
@endsection

