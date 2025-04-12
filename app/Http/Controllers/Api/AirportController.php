<?php

namespace App\Http\Controllers\Api;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\AirportResource;
use App\Http\Resources\PlaceCollection;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Pool;
use Yajra\DataTables\DataTables;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $airports = Airport::query()->where('country_code', 'US')->select(['id', 'slug', 'name', 'city', 'state', 'google_state_long_name', 'code']);

        if (request()->has('search') && !empty(request()->get('search'))) {
            $airports->where(function ($query) {
                $query->where('name', 'LIKE', '%' . request()->get('search') . '%')
                    ->orWhere('code', 'LIKE', '%' . request()->get('search') . '%')
                    ->orWhere('city', 'LIKE', '%' . request()->get('search') . '%')
                    ->orWhere('state', 'LIKE', '%' . request()->get('search') . '%')
                    ->orWhere('google_state_long_name', 'LIKE', '%' . request()->get('search') . '%');
            });
        }

        return ApiResponse::success('Airports retrieved successfully.', $airports->paginate(request()->length));

        return DataTables::of($airports)->editColumn('city', function ($airport) {
            return $airport->city . ', ' . ($airport->state ? $airport->state : $airport->google_state_long_name);
        })->addColumn('action', function ($airport) {
            return view('partials.airports._action', compact('airport'));
        })->rawColumns(['action' => 'action'])->make(true);
    }

    public function getAirports(){
        $query = Airport::query();
        return $query->paginate(500);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $airport = Airport::findByCode($code);
        $airport = new AirportResource($airport);

        return ApiResponse::success('Airport retrieved successfully.', $airport);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $airport_code
     * @param $category_slug
     * @return \App\Helpers\ApiResponse
     */
    public function getAroundPlaces($airport_code, $category_slug)
    {
        $airport = Airport::query()->where('code', $airport_code)->firstOrFail();
        $category = Category::query()->where('slug', $category_slug)->firstOrFail();

        if ($airport && $category) {
            if (!empty(config('aroundairports.radar.radar_test_secret_server')) && request()->has('page')) {
                $places = Place::withoutTrashed()->where('airport_id', $airport->id)
                    ->where('category_id', $category->id)
                    ->where(function ($query) {
                        $query->whereNull('distance_value')
                            ->orWhereNull('dup_google_place_id');
                    })->get()->take(9);

                if ($places->isNotEmpty()) {
                    $pool = Pool::create();

                    foreach ($places as $place) {
                        $pool->add(function () use ($category, $airport, $place) {

                            $this->getPlaceDistance($airport, $place);

                            if (is_null($place->distance_value) && !is_null($place->google_place_id)) {
                                $this->getPlaceDistanceFromGoogle($airport, $place);
                            }

                            $this->getPlaceGooglePlaceID($category, $airport, $place);

                        })->catch(function (\Exception $exception) {
                            Log::error('Error: ' . $exception->getMessage());
                        });
                    }

                    $pool->wait();
                }
            }

            $places = Place::query()->where('airport_id', $airport->id)
                ->where('category_id', $category->id)
                ->whereNotNull('distance_value')
                ->whereNotNull('google_place_id')
                ->orderBy('distance_value', 'ASC')
                ->paginate(9);

//            PlaceCollection::collection($places)
            return ApiResponse::success('Places retrieved successfully.', $places);
        }

        return ApiResponse::success('Places retrieved successfully.', []);
    }

    /**
     * @param $airport
     * @param $place
     */
    public function getPlaceDistance($airport, $place)
    {
        $distance_url = 'https://api.radar.io/v1/route/distance?origin=' . $airport->latitude . ',' . $airport->longitude . '&destination=' . $place->latitude . ',' . $place->longitude . '&modes=' . config('aroundairports.radar.modes') . '&units=' . config('aroundairports.radar.units') . '';

        $distance = Http::withHeaders([
            'Authorization' => config('aroundairports.radar.radar_test_secret_server')
        ])->get($distance_url);

        if ($distance->status() == 200) {
            $place->update([
                'distance_value' => $distance['routes']['car']['distance']['value'],
                'distance_text' => $distance['routes']['car']['distance']['text'],
                'all_travel_mode_distance' => $distance->object(),
            ]);
        }
    }

    /**
     * @param $airport
     * @param $place
     */
    public function getPlaceDistanceFromGoogle($airport, $place)
    {
        $distance = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=' . $airport->name . '&destinations=place_id:' . $place->google_place_id . '&key=' . config('aroundairports.google.api_key') . '');

        if (!empty($distance['rows'])) {
            $place->update([
                'distance_value' => $distance['rows'][0]['elements'][0]['distance']['value'],
                'distance_text' => $distance['rows'][0]['elements'][0]['distance']['text'],
                'all_travel_mode_distance' => $distance->object(),
            ]);
        }
    }

    /**
     * @param $category
     * @param $airport
     * @param $place
     */
    public function getPlaceGooglePlaceID($category, $airport, $place)
    {
        if (is_null($place->dup_google_place_id)) {
            if (!empty(config('aroundairports.google.api_key'))) {
                $google_place = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . urlencode($place->name) . '&inputtype=textquery&fields=place_id&locationbias=circle:1000@' . $place->latitude . ',' . $place->longitude . '&key=' . config('aroundairports.google.api_key') . '');

                if ($google_place['status'] === 'OK' && count($google_place['candidates']) > 0) {
                    $google_place_data = $google_place->object();

                    $search_place = Place::withoutTrashed()
                        ->where('airport_id', $airport->id)
                        ->where('category_id', $category->id)
                        ->where('google_place_id', $google_place_data->candidates[0]->place_id)
                        ->first();

                    if ($search_place) {
                        $place->update(['google_place_id' => null, 'dup_google_place_id' => $google_place_data->candidates[0]->place_id, 'deleted_at' => Carbon::now()]);
                    } else {
                        $place->update(['google_place_id' => $google_place_data->candidates[0]->place_id, 'dup_google_place_id' => $google_place_data->candidates[0]->place_id]);
                    }
                } else if ($google_place['status'] === 'ZERO_RESULTS') {
                    $find_place = Place::withoutTrashed()->find($place->id);

                    if ($find_place) {
                        $find_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);
                    }
                }
            }
        }
    }
}
