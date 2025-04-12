@extends('admin.partials.app')
@section('title')
    {{ $place->name }}
@endsection
@section('style')

@endsection
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                Show
            </h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                {{ $place->name }}
            </div>
            <div class="card-body">

            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@section('script')

@endsection
