<?php

namespace App\Services;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlacesService
{
    /**
     * @var string
     */
    public $log;

    /**
     * @var int
     */
    public $new_places_count;

    /**
     * @var int
     */
    public $exception_counter;

    /**
     * PlacesService constructor.
     */
    public function __construct()
    {
        $this->log = '';
        $this->new_places_count = 0;
        $this->exception_counter = 0;
    }

    /**
     * @param array $places
     * @param $airport
     * @param $category
     */
    public function store(array $places, $airport, $category)
    {
        foreach ($places as $key => $place) {
            $search_place = Place::withTrashed()
                ->where('airport_id', $airport->id)
                ->where('category_id', $category->id)
                ->where('_id', $place->_id)
                ->first();

            if (is_null($search_place)) {
                try {
                    Place::query()->create([
                        'airport_id' => $airport->id,
                        'category_id' => $category->id,
                        '_id' => $place->_id,
                        'name' => $place->name,
                        'latitude' => $place->location->coordinates[1],
                        'longitude' => $place->location->coordinates[0],
                        'data' => $place,
                        'expire_at' => Carbon::now()->subDay()
                    ]);

                    $this->new_places_count++;
                } catch (\Exception $e) {
                    Log::error('Exception ' . $this->exception_counter++ . ' Airport ' . $airport->id . ' Category ' . $category->id . ', Search Place ' . $search_place ? $search_place->id : $search_place . ' Exception: ' . $e->getMessage());
                }
            }
        }

        // ToDo Discuss to verify check
        if ($this->new_places_count > 0) {
            $this->getDistanceForTopTenNearestPlaces(Place::byTenNearestPlaces($airport, $category)->get(), $airport);
        }
    }

    /**
     * Set log job output.
     */
    public function setLog()
    {
        $this->log .= 'Total Places: ' . $this->getTotalPlaces() .
            '<br> New Places: ' . $this->new_places_count .
            '<br> Exceptions: ' . $this->exception_counter;
    }

    /**
     * Get count of all places store
     * in database
     *
     * @return int
     */
    public function getTotalPlaces()
    {
        return Place::query()->count();
    }

    /**
     * @param $nearest_places
     * @param $airport
     */
    public function getDistanceForTopTenNearestPlaces($nearest_places, $airport)
    {
        $nearest_places = $nearest_places->whereNull('distance_value');

        foreach ($nearest_places as $nearest_place) {
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

        $nearest_places->each(function ($nearest_place) {
            $nearest_place->fresh();

            if (is_null($nearest_place->deleted_at)) {
                $nearest_place->update([
                    'updated_at' => Carbon::now()
                ]);
            }
        });
    }

    /**
     * @param $place
     */
    public function getGooglePlaceID($place)
    {
        if (!is_null($place->google_place_id)) {
            $place->update([
                'dup_google_place_id' => $place->google_place_id
            ]);
        } else {
            $google_place = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . urlencode($place->name) . '&inputtype=textquery&fields=name,place_id&locationbias=circle:1000@' . $place->latitude . ',' . $place->longitude . '&key=' . config('aroundairports.google.api_key') . '');

            if ($google_place['status'] === 'OK' && count($google_place['candidates']) > 0) {
                $google_place_data = $google_place->object();

                $search_place = Place::withoutTrashed()
                    ->where('airport_id', $place->airport_id)
                    ->where('category_id', $place->category_id)
                    ->where('google_place_id', $google_place_data->candidates[0]->place_id)->first();

                if ($search_place) {
                    $place->update(['google_place_id' => null, 'dup_google_place_id' => $google_place_data->candidates[0]->place_id, 'deleted_at' => Carbon::now()]);

                    $this->setSlugNull($place);
                } else {
                    $place->update([
                        'name' => $google_place_data->candidates[0]->name,
                        'google_place_id' => $google_place_data->candidates[0]->place_id,
                        'dup_google_place_id' => $google_place_data->candidates[0]->place_id
                    ]);
                }
            } else if ($google_place['status'] === 'ZERO_RESULTS') {
                $find_place = Place::withoutTrashed()->find($place->id);

                if ($find_place) {
                    $find_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);

                    $this->setSlugNull($find_place);
                }
            }
        }
    }

    /**
     * @param $place
     * @return int
     */
    public function setSlugNull($place)
    {
        return DB::table('places')->where('id', $place->id)->update(['slug' => null]);
    }
}
