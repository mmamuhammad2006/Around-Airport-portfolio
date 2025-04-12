@extends('admin.partials.app')
@section('title')
    Airports
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
            <i class="fa fa-plane"></i> Airports
        </h1>

        @if(Session::has('success'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{Session::get('success')}}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- DataTales Example -->
        <div class="card card-header-actions shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">List of all airports</h6>

{{--                <div class="dropdown">--}}
{{--                    <button class="btn btn-sm btn-primary dropdown-toggle" id="dropdownMenuButton" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                        <i class="fa fa-filter"></i> With Hide--}}
{{--                    </button>--}}
{{--                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">--}}
{{--                        <a class="dropdown-item" href="javascript:void(0);">With Hide</a>--}}
{{--                        <a class="dropdown-item" href="javascript:void(0);">Only Hide</a>--}}
{{--                        <a class="dropdown-item" href="javascript:void(0);">Clear</a>--}}
{{--                    </div>--}}
{{--                </div>--}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="airports-datatable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Code</th>
                            <th>Action</th>
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
            table = $('#airports-datatable').DataTable({
                pageLength: 100,
                processing: true,
                serverSide: true,
                ajax: '{{ route('airports.data.table') }}',
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'city'},
                    {data: 'state'},
                    {data: 'code'},
                    {data: 'action', orderable: false, searchable: false}
                ]
            });
        });

        function deleteRecord(url, e) {
            e.preventDefault();
            swal({
                    title: "Are you sure?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                },
                function (willDelete) {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function (data, textStatus) {
                                if (data.status == 200 && data.message == "Airport hide successfully..!") {
                                    swal({
                                            title: "Done!",
                                            icon: "success"
                                        },
                                        function (ok) {
                                            if (ok) {
                                                table.draw();
                                            } else {
                                                table.draw();
                                            }
                                        });
                                }

                            },
                        });
                    }
                });
        }
    </script>
@endsection
