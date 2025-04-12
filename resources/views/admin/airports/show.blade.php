@extends('admin.partials.app')
@section('title')
    {{ $airport->name }} ({{ $airport->code }})
@endsection
@section('style')
    <link href="{{ asset('assets/admin/json-viewer/jquery.json-viewer.css') }}" type="text/css" rel="stylesheet">
@endsection
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $airport->name }} ({{ $airport->code }}) - Categories</h1>

            <a role="button" href="{{ route('airports.index') }}" class="btn btn-sm btn-outline-danger float-right">Back to List</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                            <tr class="bg-light">
                                <th scope="col">Category</th>
                                <th scope="col">Airport Name</th>
                                <th scope="col">Airport Code</th>
                                <th scope="col">Total Places</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\Category::all() as $key=>$category)
                                <tr>
                                    <th scope="row"><i class="{{ $category->icon }}"></i> {{ $category->name }}</th>
                                    <td>{{ $airport->name }}</td>
                                    <td>{{ $airport->code }}</td>
                                    <td>{{ $airport->getTotalPlacesByCategory($category) }}</td>
                                    <td>
                                        @if($category->google_type)
                                            <button type="button" class="btn btn-sm btn-outline-danger mb-2" id="search-from-google-{{ $category->id }}"
                                                    onclick="searchPlaces('{{ route("airports.places") }}', 'google', '{{ $airport->code }}', '{{ $category->google_type }}', 'search-from-google-{{ $category->id }}')">
                                                <i class="fab fa-google"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-warning mb-2" id="search-from-google-by-name-{{ $category->id }}"
                                                    onclick="searchPlacesByName('{{ route("airports.places") }}', '{{ $airport->code }}', '{{ $category->slug }}', 'search-from-google-by-name-{{ $category->id }}')">
                                                <i class="fa fa-route"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-primary mb-2" id="search-{{ $category->id }}"
                                                onclick="searchPlaces('{{ route("airports.places") }}', 'radar', '{{ $airport->code }}', '{{ $category->radar_category }}', 'search-{{ $category->id }}')">
                                            <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Output
                            <small class="d-block output-counters" style="color: #858796;">
                                <strong>Total Places: </strong> 0
                                <strong>Duplicate Places: </strong> 0
                            </small>
                        </h6>

                        <a role="button" href="{{ route('airports.categories.places', $airport->code) }}" class="btn btn-sm btn-outline-success float-right"><i class="fab fa-google"></i> Fetch All</a>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body" id="output">
                        No Places found!
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@section('script')
    <script src="{{ asset('assets/admin/json-viewer/jquery.json-viewer.js') }}"></script>

    <script>
        function searchPlaces(url, type, airport ,category, btn_id) {
            $('#' + btn_id).html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                '                                                        <span class="sr-only">Loading...</span>\n' +
                '                                                    </div>')

            let data;
            if (type === 'radar') {
                data = { type: type, airport_code: airport, radar_category: category }
            } else {
                data = { type: type, airport_code: airport, google_type: category };
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function (response) {
                    if (response.status === 200) {
                        $('#output').jsonViewer(response.data.places);
                        $('.output-counters').html('<strong>Total Places: </strong> '+ response.data.total_places +'\n' +
                            '                                <strong>Duplicate Places: </strong> '+ response.data.duplicate_places +'');

                        if (type === 'radar') {
                            $('#' + btn_id).html('<i class="fa fa-paper-plane"></i>');
                        } else {
                            $('#' + btn_id).html('<i class="fab fa-google"></i>');
                        }
                    }
                }
            });
        }

        function searchPlacesByName(url, airport ,category, btn_id) {
            $('#' + btn_id).html('<div class="spinner-border spinner-border-sm" role="status">\n' +
                '                                                        <span class="sr-only">Loading...</span>\n' +
                '                                                    </div>')

            let data = { type: 'google', airport_code: airport, category_slug: category };

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function (response) {
                    if (response.status === 200) {
                        $('#output').jsonViewer(response.data.places);
                        $('.output-counters').html('<strong>Total Places: </strong> '+ response.data.total_places +'\n' +
                            '                                <strong>Duplicate Places: </strong> '+ response.data.duplicate_places +'');

                        $('#' + btn_id).html('<i class="fa fa-route"></i>');
                    }
                }
            });
        }
    </script>
@endsection
