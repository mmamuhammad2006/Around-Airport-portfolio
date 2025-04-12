<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GoogleDistance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:distance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google distance for place.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categories = Category::all();
        $airports = Airport::query()->where('country_code', 'US')->get();

        foreach ($airports as $airport) {
            foreach ($categories as $category) {
                $places = Place::byTenNearestPlaces($airport, $category)->whereNull('distance_value')->whereNotNull('google_place_id')->get();

                $this->info('Airport ID: ' . $airport->id . ' Category ID: ' . $category->id . ' Ten Nearest Places: ' . $places->count());

                if ($places->count() > 0) {
                    foreach ($places as $place) {
                        $distance = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $airport->name . '&destinations=place_id:' . $place->google_place_id . '&key=' . config('aroundairports.google.api_key') . '');

                        try {
                            if (!empty($distance['rows'])) {
                                if (isset($distance['rows'][0]['elements'][0]['distance'])) {
                                    $place->update([
                                        'distance_value' => $distance['rows'][0]['elements'][0]['distance']['value'],
                                        'distance_text' => $distance['rows'][0]['elements'][0]['distance']['text'],
                                        'all_travel_mode_distance' => $distance->object(),
                                    ]);
                                }
                            }
                        } catch (\Exception $e) {
                            dd($distance->object());
                        }
                    }
                }
            }
        }
    }
}
