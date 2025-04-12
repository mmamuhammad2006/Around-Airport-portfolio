@extends('admin.partials.app')
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
                {{ $airline->name }}
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-route fa-2x"></i>
                        <div class="ml-4">
                            <div class="small">{{ $airline->name }}</div>
                            <div class="text-xs text-muted">
                                Created {{ \Carbon\Carbon::parse($airline->created_at)->format('d/Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 small">
                        <div class="badge badge-light mr-3">
                            {{ $airline->iata_code }}
                        </div>
                    </div>
                </div>
                <hr />
                <a role="button" href="{{ route('airlines.index') }}" class="btn btn-sm btn-outline-danger float-right">Back to List</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
@section('script')

@endsection
