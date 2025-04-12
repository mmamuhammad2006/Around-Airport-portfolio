<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;

class PlacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();
        $airports = Airport::query()->where('country_code', 'US')->get();

        if (config('app.env') === 'local') {
            $airports = Airport::all()->take(10);
        }

        if (!empty(config('aroundairports.radar.radar_test_secret_server')) && !empty(config('aroundairports.google.api_key'))) {
            foreach ($airports as $airport) {
                foreach ($categories as $category) {
                    $limit = config('aroundairports.radar.limit');

                    if (config('app.env') === 'local') {
                        $limit = config('aroundairports.radar.seeder_limit');
                    }

                    $url = 'https://api.radar.io/v1/search/places?categories=' . $category->radar_category . '&near=' . $airport->latitude . ',' . $airport->longitude . '&radius=' . config('aroundairports.radar.radius') . '&limit=' . $limit . '';

                    $places = Http::withHeaders([
                        'Authorization' => config('aroundairports.radar.radar_test_secret_server')
                    ])->get($url);

                    if (!empty($places['places'])) {

                        $this->command->error('Airport ID: ' . $airport->id . ' Category Name: ' . $category->name . ' Total Places: ' . count($places['places']));

                        foreach ($places['places'] as $key => $place) {
                            $search_place = Place::query()
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

                                $this->command->info('Airport ID: ' . $airport->id . ' Category ID: ' . $category->id . ' Place ID: ' . $created_place->id);
                            }
                        }

                        $ten_nearest_places = Place::byTenNearestPlaces($airport, $category)->get();

                        foreach ($ten_nearest_places as $nearest_place) {
                            $this->command->warn('Nearest Place ID to Fetch Distance Api Data: ' . $nearest_place->id);

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

//                                $google_place = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($nearest_place->name) . '&location=' . $nearest_place->latitude . ',' . $nearest_place->longitude . '&key=' . config('aroundairports.google.api_key') . '');
//
//                                if ($google_place['status'] === 'OK' && count($google_place['results']) > 0) {
//                                    $google_place_detail = Http::get('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $google_place['results'][0]['place_id'] . '&key=' . config('aroundairports.google.api_key') . '');
//
//                                    if ($google_place_detail['status'] === 'OK') {
//                                        $google_place_detail = $google_place_detail->object();
//
//                                        if (isset($google_place_detail->result->photos)) {
//                                            $google_place_detail->result->photos = $this->setPhotoUrl($google_place_detail->result->photos);
//                                        }
//
//                                        $search_place = Place::query()
//                                            ->where('airport_id', $airport->id)
//                                            ->where('category_id', $category->id)
//                                            ->where('google_place_id', $google_place_detail->result->place_id)->first();
//
//                                        if (is_null($search_place)) {
//                                            $nearest_place->update([
//                                                'google_place_id' => $google_place_detail->result->place_id,
//                                                'google_latitude' => $google_place_detail->result->geometry->location->lat,
//                                                'google_longitude' => $google_place_detail->result->geometry->location->lng,
//                                                'google_rating' => isset($google_place_detail->result->rating) ? $google_place_detail->result->rating : null,
//                                                'google_formatted_address' => $google_place_detail->result->formatted_address,
//                                                'google_data' => isset($google_place_detail->result) ? $google_place_detail->result : null,
//                                                'expire_at' => Carbon::now()->addDays(config('aroundairports.expire_days_for_places')),
//                                            ]);
//                                        }
//                                    }
//                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->command->error('Please set Radar api RADAR_TEST_SECRET_SERVER, RADAR_TEST_SECRET_SERVER and GOOGLE_API_KEY keys in .env file.');
        }
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
}
