<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Places extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'places:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Places fetch from aviation edge.';

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

        if (!empty(config('aroundairports.radar.radar_test_secret_server')) && !empty(config('aroundairports.google.api_key'))) {
            foreach ($airports as $airport) {
                foreach ($categories as $category) {
                    $url = 'https://api.radar.io/v1/search/places?categories=' . $category->radar_category . '&near=' . $airport->latitude . ',' . $airport->longitude . '&radius=' . config('aroundairports.radar.radius') . '&limit=' . config('aroundairports.radar.limit') . '';

                    $places = Http::withHeaders([
                        'Authorization' => config('aroundairports.radar.radar_test_secret_server')
                    ])->get($url);

                    if (!empty($places['places'])) {
                        $this->error('Airport ID: ' . $airport->id . ' Category Name: ' . $category->name . ' Total Places: ' . count($places['places']));

                        foreach ($places['places'] as $key => $place) {
                            $search_place = Place::withTrashed()
                                ->where('airport_id', $airport->id)
                                ->where('category_id', $category->id)
                                ->where('_id', $place['_id'])
                                ->first();

                            if (is_null($search_place)) {
                                $created_place = Place::query()->create([
                                    'airport_id' => $airport->id,
                                    'category_id' => $category->id,
                                    '_id' => $place['_id'],
                                    'name' => $place['name'],
                                    'latitude' => $place['location']['coordinates'][1],
                                    'longitude' => $place['location']['coordinates'][0],
                                    'data' => $place,
                                    'expire_at' => Carbon::now()->subDay()
                                ]);

                                $this->info('Airport ID: ' . $airport->id . ' Category ID: ' . $category->id . ' New Place ID: ' . $created_place->id);
                            }
                        }

                        $ten_nearest_places = Place::byTenNearestPlaces($airport, $category)->get();

                        foreach ($ten_nearest_places->whereNull('distance_value') as $nearest_place) {
                            $this->warn('Nearest Place ID to Fetch Distance Api Data: ' . $nearest_place->id);

                            $distance_url = 'https://api.radar.io/v1/route/distance?origin=' . $airport->latitude . ',' . $airport->longitude . '&destination=' . $nearest_place->latitude . ',' . $nearest_place->longitude . '&modes=' . config('aroundairports.radar.modes') . '&units=' . config('aroundairports.radar.units') . '';

                            $distance = Http::withHeaders([
                                'Authorization' => config('aroundairports.radar.radar_test_secret_server')
                            ])->get($distance_url);

                            if ($distance->status() == 200) {
                                $nearest_place->update([
                                    'distance_value' => $distance['routes']['car']['distance']['value'],
                                    'distance_text' => $distance['routes']['car']['distance']['text'],
                                    'all_travel_mode_distance' => $distance->object(),
                                ]);

                                $this->getGooglePlaceID($nearest_place);
                            }
                        }
                    }
                }
            }
        } else {
            $this->error('Please set Radar api RADAR_TEST_SECRET_SERVER, RADAR_TEST_SECRET_SERVER and GOOGLE_API_KEY keys in .env file.');
        }
    }

    public function getGooglePlaceID($place)
    {
        if (!is_null($place->google_place_id)) {
            $this->info('We have already google_place_id, so we can simply update dup_google_place_id for that place: ' . $place->id);

            $place->update([
                'dup_google_place_id' => $place->google_place_id
            ]);
        } else {
            $this->warn('We don\'t have google_place_id, so we can simply fetch google_place_id for that place from google: ' . $place->id);

            if (!empty(config('aroundairports.google.api_key'))) {
                $google_place = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . urlencode($place->name) . '&inputtype=textquery&fields=place_id&locationbias=circle:1000@' . $place->latitude . ',' . $place->longitude . '&key=' . config('aroundairports.google.api_key') . '');

                if ($google_place['status'] === 'OK' && count($google_place['candidates']) > 0) {
                    $google_place_data = $google_place->object();

                    $this->info('Google gives data for Place: ' . $place->id);

                    $search_place = Place::withoutTrashed()
                        ->where('airport_id', $place->airport_id)
                        ->where('category_id', $place->category_id)
                        ->where('google_place_id', $google_place_data->candidates[0]->place_id)->first();

                    if ($search_place) {
                        $place->update(['google_place_id' => null, 'dup_google_place_id' => $google_place_data->candidates[0]->place_id, 'deleted_at' => Carbon::now()]);
                    } else {
                        $place->update([
                            'google_place_id' => $google_place_data->candidates[0]->place_id,
                            'dup_google_place_id' => $google_place_data->candidates[0]->place_id
                        ]);
                    }
                } else if ($google_place['status'] === 'ZERO_RESULTS') {
                    $this->error('Google not gives data for Place, we can delete it: ' . $place->id);

                    $find_place = Place::withoutTrashed()->find($place->id);

                    if ($find_place) {
                        $find_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);

                        $this->error('Place successfully deleted: ' . $find_place->id);
                    }
                }
            } else {
                $this->error('Please set GOOGLE_API_KEY keys in .env file.');
            }
        }
    }
}
