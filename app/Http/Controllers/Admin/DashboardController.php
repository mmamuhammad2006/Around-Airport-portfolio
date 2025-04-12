<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AirlinesJob;
use App\Jobs\AirportsJob;
use App\Jobs\DuplicatePlacesJob;
use App\Jobs\GenerateSitemap;
use App\Jobs\PlacesGoogleDataJob;
use App\Jobs\PlacesJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Imtigger\LaravelJobStatus\JobStatus;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        $total_categories = DB::table('categories')->count();
        $total_airlines = DB::table('airlines')->count();
        $total_airports = DB::table('airports')->where('country_code', 'US')->count();
        $total_places = DB::table('places')->count();

        return view('admin.index', compact('total_categories', 'total_airlines', 'total_airports', 'total_places'));
    }

    /**
     * @param null $job
     * @return false|\Illuminate\Http\RedirectResponse
     */
    public function run($job = null)
    {
        if ($job) {
            switch ($job) {
                case 'sitemap':
                    $this->dispatch(new GenerateSitemap());
                    break;
                case 'airlines':
                    $this->dispatch(new AirlinesJob());
                    break;
                case 'airports':
                    $this->dispatch(new AirportsJob());
                    break;
                case 'places':
                    PlacesJob::withChain([
                        new DuplicatePlacesJob,
                    ])->dispatch();
                    break;
                case 'google-data':
                    $this->dispatch(new PlacesGoogleDataJob(request()->limit, request()->start, request()->end,request()->airport_ids));
                    return redirect()->back();
                default:
                    return false;
            }
        }

        return false;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJobsStatuses()
    {

        $jobs_statuses = JobStatus::query()->whereIn('id', function ($query) {
            $query->selectRaw('max(id) as id')
                ->from((new JobStatus())->getTable())
                ->whereIn('type', [GenerateSitemap::class, AirlinesJob::class, AirportsJob::class, PlacesJob::class, DuplicatePlacesJob::class, PlacesGoogleDataJob::class])
                ->groupBy('type');
        })->get();

        $statuses = collect();

        $jobs_statuses->each(function ($job_status) use ($statuses) {
            switch ($job_status->type) {
                case GenerateSitemap::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'sitemap',
                        'type' => GenerateSitemap::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                case AirlinesJob::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'airlines',
                        'type' => AirlinesJob::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                case AirportsJob::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'airports',
                        'type' => AirportsJob::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                case PlacesJob::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'places',
                        'type' => PlacesJob::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                case DuplicatePlacesJob::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'duplicates',
                        'type' => DuplicatePlacesJob::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                case PlacesGoogleDataJob::class:
                    $statuses->push([
                        'id' => $job_status->id,
                        'job' => 'google-data',
                        'type' => PlacesGoogleDataJob::class,
                        'progress_percentage' => $job_status->progress_percentage,
                        'is_ended' => $job_status->is_ended,
                        'is_executing' => $job_status->is_executing,
                        'is_failed' => $job_status->is_failed,
                        'is_finished' => $job_status->is_finished,
                        'is_queued' => $job_status->is_queued,
                        'is_retrying' => $job_status->is_retrying,
                        'status' => $job_status->status,
                        'output' => $job_status->output,
                        'started_at' => Carbon::parse($job_status->started_at)->format('D M d H:m:s Y'),
                        'finished_at' => $job_status->is_finished ? Carbon::parse($job_status->finished_at)->format('D M d H:m:s Y') : $job_status->is_finished,
                        'how_much_time' => $job_status->is_finished ? $this->getDiff($job_status->started_at, $job_status->finished_at) : $job_status->is_finished
                    ]);
                    break;
                default:
                    return false;
            }
        });

        return response()->json([
            'status' => 200,
            'statuses' => $statuses
        ]);
    }

    /**
     * @param $start_date
     * @param $finish_date
     * @return string
     */
    public function getDiff($start_date, $finish_date)
    {
        $diff = $start_date->diff($finish_date);

        return $diff->h . ':' . $diff->i . ':' . $diff->s;
    }
}
