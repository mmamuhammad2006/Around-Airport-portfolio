@extends('admin.partials.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            {{--            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>--}}
        </div>

        <!-- Content Row -->
        <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Categories
                                    (Total)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_categories }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Airlines
                                    (Total)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_airlines }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-route fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Airports (US)</div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div
                                            class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $total_airports }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-plane fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Places (US)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_places }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Content Column -->
            <div class="col-lg-12 mb-4">
                <div class="row">
{{--                    <div class="col-lg-4">--}}
{{--                        <div class="card card-header-actions shadow mb-4">--}}
{{--                            <div class="card-header py-3">--}}
{{--                                Generate Sitemap--}}
{{--                                <div>--}}
{{--                                    <button class="btn btn-light" title="View Log" data-toggle="modal"--}}
{{--                                            data-target="#sitemap-log-view-modal">--}}
{{--                                        <i class="fas fa-code-branch"></i>--}}
{{--                                        Log--}}
{{--                                    </button>--}}
{{--                                    <button onclick="runJob('{{ route("run", ["job" => "sitemap"]) }}')"--}}
{{--                                            class="btn btn-outline-info" title="Run">--}}
{{--                                        Run--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <h4 class="small font-weight-bold">Sitemap <span--}}
{{--                                        class="float-right sitemap-progress-bar-per">0%</span></h4>--}}
{{--                                <div class="progress mb-1">--}}
{{--                                    <div--}}
{{--                                        class="progress-bar progress-bar-striped progress-bar-animated bg-info sitemap-progress-bar"--}}
{{--                                        role="progressbar"--}}
{{--                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                </div>--}}
{{--                                <h4 class="small font-weight-bold mb-1">--}}
{{--                                    Status: <span class="font-weight-normal sitemap-status"></span>--}}
{{--                                </h4>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="col-lg-6">
                        <div class="card card-header-actions shadow mb-4">
                            <div class="card-header py-3">
                                Airlines
                                <div>
                                    <button class="btn btn-light" title="View Log" data-toggle="modal"
                                            data-target="#airlines-log-view-modal">
                                        <i class="fas fa-code-branch"></i>
                                        Log
                                    </button>
                                    <button onclick="runJob('{{ route("run", ["job" => "airlines"]) }}')"
                                            class="btn btn-outline-danger" title="Run">
                                        Run
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h4 class="small font-weight-bold">Airlines <span
                                        class="float-right airlines-progress-bar-per">0%</span></h4>
                                <div class="progress mb-1">
                                    <div
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-danger airlines-progress-bar"
                                        role="progressbar"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <h4 class="small font-weight-bold mb-1">
                                    Status: <span class="font-weight-normal airlines-status"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-header-actions shadow mb-4">
                            <div class="card-header py-3">
                                Airports
                                <div>
                                    <button class="btn btn-light" title="View Log" data-toggle="modal"
                                            data-target="#airports-log-view-modal">
                                        <i class="fas fa-code-branch"></i>
                                        Log
                                    </button>
                                    <button onclick="runJob('{{ route("run", ["job" => "airports"]) }}')"
                                            class="btn btn-outline-warning">
                                        Run
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h4 class="small font-weight-bold">Airports <span
                                        class="float-right airports-progress-bar-per">0%</span></h4>
                                <div class="progress mb-1">
                                    <div
                                        class="progress-bar progress-bar-striped progress-bar-animated bg-warning airports-progress-bar"
                                        role="progressbar"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <h4 class="small font-weight-bold mb-1">
                                    Status: <span class="font-weight-normal airports-status"></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-header-actions shadow mb-4">
                            <div class="card-header py-3">
                                Places
                                <div>
                                    <button class="btn btn-light" title="View Log" data-toggle="modal"
                                            data-target="#places-log-view-modal">
                                        <i class="fas fa-code-branch"></i>
                                        Log
                                    </button>
                                    <button class="btn btn-outline-primary"
                                            onclick="runJob('{{ route("run", ["job" => "places"]) }}')">
                                        Run
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4 class="small font-weight-bold">Places <span
                                                class="float-right places-progress-bar-per">0%</span></h4>
                                        <div class="progress mb-1">
                                            <div
                                                class="progress-bar progress-bar-striped progress-bar-animated places-progress-bar"
                                                role="progressbar" aria-valuenow="0"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <h4 class="small font-weight-bold mb-2">
                                            Status: <span class="font-weight-normal places-status"></span>
                                        </h4>

                                        <h4 class="small font-weight-bold">Duplicates <span
                                                class="float-right duplicates-progress-bar-per">0%</span></h4>
                                        <div class="progress mb-1">
                                            <div
                                                class="progress-bar progress-bar-striped progress-bar-animated duplicates-progress-bar"
                                                role="progressbar" aria-valuenow="0"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <h4 class="small font-weight-bold mb-2">
                                            Status: <span class="font-weight-normal duplicates-status"></span>
                                        </h4>
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <button class="btn btn-light" title="View Log" data-toggle="modal"
                                                data-target="#duplicates-log-view-modal">
                                            <i class="fas fa-code-branch"></i>
                                            Duplicates Log
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <form method="GET" action="{{ route("run", ["job" => "google-data"]) }}">
                            <div class="card card-header-actions shadow mb-4">
                                <div class="card-header py-3">
                                    Google Data
                                    <div>
                                        <button type="button" class="btn btn-light" title="View Log"
                                                data-toggle="modal"
                                                data-target="#google-data-log-view-modal">
                                            <i class="fas fa-code-branch"></i>
                                            Log
                                        </button>
                                        <button class="btn btn-outline-success" type="submit">
                                            Run
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <h4 class="small font-weight-bold">Google Data <span
                                                    class="float-right google-data-progress-bar-per">0%</span></h4>
                                            <div class="progress mb-1">
                                                <div
                                                    class="progress-bar bg-success progress-bar-striped progress-bar-animated google-data-progress-bar"
                                                    role="progressbar" aria-valuenow="0"
                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <h4 class="small font-weight-bold mb-1">
                                                Status: <span class="font-weight-normal google-data-status"></span>
                                            </h4>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <div class="form-row align-items-center">
                                                <div class="col-lg-4">
                                                    <small>How many airports you want to fetch data?</small>
                                                    <input type="text" class="form-control mb-2" placeholder="Limit"
                                                           id="limit" name="limit" value="100">
                                                </div>
                                                <div class="col-lg-4">
                                                    <small>Where you want to start fetch data?</small>
                                                    <input type="text" class="form-control mb-2" placeholder="Start"
                                                           id="start" name="start">
                                                </div>
                                                <div class="col-lg-4">
                                                    <small>Where you want to end fetch data?</small>
                                                    <input type="text" class="form-control mb-2" placeholder="End"
                                                           id="end" name="end">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Or --}}
                                        <div class="col-lg-12">
                                            <h4 class="small font-weight-bold">Or</h4>
                                        </div>
                                        <div class="col-lg-12">
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <div class="form-row align-items-center">
                                                <div class="col-lg-4">
                                                    <small>Search or select airport you want to fetch ?</small>
                                                    <airports-mutiselect></airports-mutiselect>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

    <!-- Sitemap Log View Modal -->
    <div class="modal fade" id="sitemap-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="sitemap-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sitemap-log-modal-label">Sitemap Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 sitemap-modal-body" style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Sitemap Log View Modal -->

    <!-- Airlines Log View Modal -->
    <div class="modal fade" id="airlines-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="airlines-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="airlines-log-modal-label">Airlines Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 airlines-modal-body" style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Airlines Log View Modal -->

    <!-- Airports Log View Modal -->
    <div class="modal fade" id="airports-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="airports-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="airports-log-modal-label">Airports Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 airports-modal-body" style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Airports Log View Modal -->

    <!-- Places Log View Modal -->
    <div class="modal fade" id="places-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="places-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="places-log-modal-label">Places Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 places-modal-body" style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Places Log View Modal -->

    <!-- Duplicates Log View Modal -->
    <div class="modal fade" id="duplicates-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="duplicates-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicates-log-modal-label">Duplicates Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 duplicates-modal-body"
                         style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Duplicates Log View Modal -->

    <!-- Google Data Log View Modal -->
    <div class="modal fade" id="google-data-log-view-modal" tabindex="-1" role="dialog"
         aria-labelledby="google-data-log-view-modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="google-data-log-modal-label">Duplicates Job Log</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container pt-3 pb-3 google-data-modal-body"
                         style="font-size: 11px; background: #f2f2f2;">
                        No Log found!
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- / Google Data Log View Modal -->
@endsection
@section('script')
    <script>
        function runJob(url) {
            $.ajax({
                type: 'GET',
                url: url,
                success: function (response) {
                    if (response.status === 200) {
                        console.log('Job Dispatch');
                    }
                }
            });
        }

        setInterval(function () {
            $.ajax({
                type: 'GET',
                url: '{{ route('jobs.statuses') }}',
                success: function (response) {
                    if (response.status === 200) {
                        $.each(response.statuses, function (index, status) {
                            let log = 'No Log found!';

                            if (status.output) {
                                log = status.output.message ? status.output.message : status.output.log;
                            }

                            $('.' + status.job + '-progress-bar-per').html(status.progress_percentage + '%');
                            $('.' + status.job + '-progress-bar').attr('aria-valuenow', status.progress_percentage).css('width', status.progress_percentage + '%');
                            $('.' + status.job + '-status').html(status.status);
                            $('.' + status.job + '-modal-body').html('<p style="margin-bottom: 0;"><strong>Started_at:</strong> ' + status.started_at + '</p>\n' +
                                '                        <p style="margin-bottom: 0;"><strong>Status: </strong> ' + status.status + '</p>' +
                                '<p style="margin-bottom: 0;"><strong>Finished_at:</strong> ' + status.finished_at + '</p>' +
                                '<p><strong>How mush time it take to finish?</strong> <span class="text-danger">' + status.how_much_time + '</span></p>' +
                                '                        <p><code>' + log + '</code></p>');
                        });
                    }
                }
            });
        }, 3000);
    </script>
@endsection
