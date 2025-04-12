<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{mix('frontend/css/app.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/main.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/fontawesome-5.14.0/css/all.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('frontend/css/default.css')}}" type="text/css">
    <link href="{{ asset('frontend/simpleLightbox/simpleLightbox.min.css') }}" rel="stylesheet"/>
    <link rel="canonical" href="{{url()->current()}}">
    @stack('styles')
    @yield('schema')
    @routes('')
    <!-- Scripts -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2152699661704943" crossorigin="anonymous"></script>
</head>
<body>
<div id="app">
    @include('frontend.layouts._partials.default-header')
    @if(config('services.adsense_ad_client'))
    <google-adsense
        data-ad-client="{{config('services.adsense_ad_client')}}"
        data-ad-slot="{{config('services.adsense_ad_slot')}}"></google-adsense>
    @endif
    <main role="main">
        @yield('content')
    </main>

    <!-- Scroll to Top Button-->
    <a
        class="scroll-to-top rounded"
        href="javascript:void(0);">
        <i class="fas fa-angle-up"></i>
    </a>

    @include('frontend.layouts._partials.default-footer')
    <loader :is-visible="isLoading"></loader>
</div>
<script src="{{mix('frontend/js/app.js')}}"></script>
<script src="{{ asset('frontend/simpleLightbox/simpleLightbox.min.js') }}"></script>
@stack('scripts')
</body>
</html>
