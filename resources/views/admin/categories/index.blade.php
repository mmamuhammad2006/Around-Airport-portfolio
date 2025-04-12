@extends('admin.partials.app')
@section('title')
    Categories
@endsection
@section('style')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/admin/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">
            <i class="fa fa-list"></i> Categories
        </h1>

        <!-- DataTales Example -->
        <div class="card card-header-actions shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">List of all categories</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="categories-datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Radar Category</th>
                            <th>Google Type</th>
                            <th>Icon</th>
{{--                            <th>Action</th>--}}
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
@section('script')
    <!-- Page level plugins -->
    {{-- Custom paths for dataTable files are removed because now we are using cdn links for dataTable in admin.partials.app.blade.php --}}

    <script>
        let table;

        $(document).ready(function () {
            table = $('#categories-datatable').DataTable({
                pageLength: 100,
                processing: true,
                serverSide: true,
                ajax: '{{ route('categories.data.table') }}',
                columns: [
                    {data: 'id', searchable: false},
                    {data: 'name'},
                    {data: 'radar_category'},
                    {data: 'google_type'},
                    {data: 'icon', orderable: false, searchable: false},
                    // {data: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endsection
