<?php

namespace App\Services;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GooglePlacesService
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
     * PlacesService constructor.
     */
    public function __construct()
    {
        $this->log = '';
        $this->new_places_count = 0;
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
                ->where('google_place_id', $place->place_id)
                ->first();

            if (is_null($search_place)) {
                if (isset($place->photos)) {
                    $place->photos = $this->setPhotoUrl($place->photos);
                }

                $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins='. $airport->name .'&destinations=place_id:'. $place->place_id .'&key='. config('aroundairports.google.api_key') .'';
                $distance = Http::get($url);

                if (!empty($distance['rows'])) {
                    $place->distance_text = $distance['rows'][0]['elements'][0]['distance']['text'];
                    $place->distance_value = $distance['rows'][0]['elements'][0]['distance']['value'];
                }

                Place::query()->create([
                    'airport_id' => $airport->id,
                    'category_id' => $category->id,
                    'google_place_id' => $place->place_id,
                    'dup_google_place_id' => $place->place_id,
                    '_id' => $place->place_id,
                    'name' => $place->name,
                    'latitude' => $place->geometry->location->lat,
                    'longitude' => $place->geometry->location->lng,
                    'google_latitude' => $place->geometry->location->lat,
                    'google_longitude' => $place->geometry->location->lng,
                    'google_rating' => isset($place->rating) ? $place->rating : null,
                    'google_data' => $place,
                    'data' => $place->geometry,
                    'expire_at' => Carbon::now()->subDay(),
                    'distance_text' => isset($place->distance_text) ? $place->distance_text : null,
                    'distance_value' => isset($place->distance_value) ? $place->distance_value : null,
                ]);

                $this->new_places_count++;
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
            '<br> New Places: ' . $this->new_places_count;
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
     * @param $photos
     * @return array
     */
    public function setPhotoUrl($photos)
    {
        return collect($photos)->each(function ($photo) {
            $google_photo = Http::get('https://maps.googleapis.com/maps/api/place/photo?maxwidth=' . $photo->width . '&photoreference=' . $photo->photo_reference . '&key=' . config('aroundairports.google.api_key') . '');

            $photo->photo_url = 'https://' . $google_photo->effectiveUri()->getAuthority() . $google_photo->effectiveUri()->getPath();
        })->toArray();
    }

    /**
     * @param $nearest_places
     * @param $airport
     */
    public function getDistanceForTopTenNearestPlaces($nearest_places, $airport)
    {
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
            }
        }
    }
}
