<?php

namespace App\Jobs;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use App\Services\DuplicatePlacesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\Trackable;

class DuplicatePlacesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $retryUntil = 7200;

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

        $duplicate_places_service = new DuplicatePlacesService();

        foreach ($airports as $key => $airport) {
            foreach ($categories as $category) {
                $places = Place::withoutTrashed()
                    ->where('airport_id', $airport->id)
                    ->where('category_id', $category->id)
                    ->whereNotNull('dup_google_place_id')
                    ->get();

                if ($places->isNotEmpty()) {
                    $duplicate_places_service->removeDuplicatePlaces($places, $airport, $category);
                }
            }

            $this->setProgressNow($key + 1);
        }

        $duplicate_places_service->setLog();

        $this->setOutput(['log' => $duplicate_places_service->log]);
    }
}
