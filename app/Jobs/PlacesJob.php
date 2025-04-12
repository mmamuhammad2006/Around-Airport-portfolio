<?php

namespace App\Jobs;

use App\Models\Airport;
use App\Models\Category;
use App\Services\PlacesService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Imtigger\LaravelJobStatus\Trackable;

class PlacesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $retryUntil = 3600 * 10;

    /**
     * Job name for exception.
     */
    const JOB = 'PlacesJob';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->prepareStatus();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $categories = Category::all();
        $airports = Airport::query()->where('country_code', 'US')->get();
        $this->setProgressMax($airports->count());

        $places_service = new PlacesService();

        foreach ($airports as $key => $airport) {
            if (Carbon::parse($airport->job_date)->isPast()) {
                foreach ($categories as $category) {
                    $url = 'https://api.radar.io/v1/search/places?categories=' . $category->radar_category . '&near=' . $airport->latitude . ',' . $airport->longitude . '&radius=' . config('aroundairports.radar.radius') . '&limit=' . config('aroundairports.radar.limit') . '';

                    $places = Http::withHeaders([
                        'Authorization' => config('aroundairports.radar.radar_test_secret_server')
                    ])->get($url);

                    if (!empty($places['places'])) {
                        $places = $places->object();

                        $places_service->store($places->places, $airport, $category);
                    }
                }

                $airport->update([
                    'job_date' => Carbon::now()->addDays(3)
                ]);

                $this->setProgressNow($key + 1);
            } else {
                $this->setProgressNow($key + 1);
            }
        }

        $places_service->setLog();

        $this->setOutput(['log' => $places_service->log]);
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Request::logExceptionError($exception, static::JOB);
    }
}
