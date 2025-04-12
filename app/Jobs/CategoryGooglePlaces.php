<?php

namespace App\Jobs;

use App\Models\Airport;
use App\Models\Category;
use App\Services\GooglePlacesService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Imtigger\LaravelJobStatus\Trackable;

class CategoryGooglePlaces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * @var Airport
     */
    public $airport;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $retryUntil = 3600 * 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Airport $airport)
    {
        $this->prepareStatus();
        $this->airport = $airport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $categories = Category::all();
        $this->setProgressMax($categories->count());

        foreach ($categories as $key => $category) {

            if ($category->google_type) {
                $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $this->airport->latitude . ',' . $this->airport->longitude . '&radius='. config('aroundairports.google.radius') .'&type='. $category->google_type .'&key='. config('aroundairports.google.api_key') .'';
            } else {
                $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $this->airport->latitude . ',' . $this->airport->longitude . '&radius='. config('aroundairports.google.radius') .'&name='. $category->name .'&key='. config('aroundairports.google.api_key') .'';
            }

            $places = Http::get($url);

            if (!empty($places['results'])) {
                $fetch_places = $places->object();

                $google_places_service = new GooglePlacesService();
                $google_places_service->store($fetch_places->results, $this->airport, $category);
            }

            $this->setProgressNow($key + 1);
        }

        $this->setOutput(['Airport' => $this->airport->code, 'total' => $categories->count()]);
    }
}
