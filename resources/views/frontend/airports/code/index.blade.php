@extends('frontend.layouts.default')
@section('title',"Places of interest near $airport->name ($airport->code) in $airport->google_formatted_address aroundairports.com")
@section('description',"Making travel easier and more enjoyable by quickly helping you find locations of interest around major as well as regional airports in the United States and around the world.")
@section('keywords', 'around airports, airports')

@section('content')

    <div class="container mt-3 mb-5">
        <div class="row">
            <div class="col-lg-3 d-none d-lg-block">
                @include('frontend.airports.code._partials.airport-code-sidebar', ['categories' => $categories])
            </div>
            <div class="col-lg-9 col-sm-12">
                <airports-code-detail code="{{$airport->code}}"></airports-code-detail>
            </div>
        </div>
    </div>
@endsection

