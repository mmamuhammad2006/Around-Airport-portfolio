<?php

namespace App\Jobs;

use App\Models\Place;
use App\Models\Airport;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Imtigger\LaravelJobStatus\Trackable;
use App\Services\PlacesGoogleDataService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlacesGoogleDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * @var int
     */
    public $qty_limit;

    /**
     * @var int
     */
    public $qty_start;

    /**
     * @var int
     */
    public $qty_end;

    /**
     * @var int
     */
    public $qty_airport_ids;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
//    public $retryUntil = 3600 * 5;

    /**
     * PlacesGoogleDataJob constructor.
     *
     * @param $limit
     * @param $start
     * @param $end
     */
    public function __construct($limit, $start, $end, $airport_ids)
    {
        $this->prepareStatus();
        $this->qty_limit = (int)$limit ? (int)$limit : 100;
        $this->qty_start = (int)$start;
        $this->qty_end = (int)$end;
        $this->qty_airport_ids = json_decode($airport_ids); //[1,2,3,4,5,6]
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $categories = Category::all();

        if(!empty($this->qty_airport_ids)) {
            $airports= Airport::withoutTrashed()->when($this->qty_airport_ids, function($query){
                $query->whereIn('id', $this->qty_airport_ids);
            })->get();

        } elseif($this->qty_start && $this->qty_limit){
            $airports = Airport::withoutTrashed()->when($this->qty_start && $this->qty_end, function ($query) {
                $query->whereBetween('id', [$this->qty_start, $this->qty_end]);
            })->limit($this->qty_limit)->get();

        }else{
            $this->setOutput(['log' => 'No valid parameters provided']);
            return;
        }
        Log::info('Airports fetch through DB:', $airports->toArray());

        if ($airports->isNotEmpty()) {
            $this->setProgressMax($airports->count());

            $places_google_data_service = new PlacesGoogleDataService();

            foreach ($airports as $key => $airport) {
                foreach ($categories as $category) {
                    $ten_nearest_places = Place::query()->where('airport_id', $airport->id)
                        ->where('category_id', $category->id)
                        ->whereNotNull('distance_value')
                        ->whereNotNull('google_place_id')
                        ->orderBy('distance_value', 'ASC')
                        ->limit(10)
                        ->get();
                    if ($ten_nearest_places->isNotEmpty()) {
                        $places_google_data_service->getPlacesGoogleData($ten_nearest_places, $airport, $category);
                    }
                }

                $this->setProgressNow($key + 1);
            }

            $places_google_data_service->setLog();

            $this->setOutput(['log' => $places_google_data_service->log]);
        } else {
            $this->setOutput(['log' => 'No airports found with these parameters: start, end']);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(30);
    }
}
