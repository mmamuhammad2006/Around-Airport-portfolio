<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Place;
use App\Models\Airport;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\PlacesService;
use Yajra\DataTables\DataTables;
use App\Jobs\CategoryGooglePlaces;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Services\GooglePlacesService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.airports.index');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $airport = Airport::query()->where('slug', $slug)->first();

        return view('admin.airports.show', compact('airport'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $airport = Airport::withTrashed()->findOrFail($id);

        if ($airport->deleted_at) {
            $airport->update([
                'deleted_at' => null
            ]);
        } else {
            $airport->delete();
        }

        return  response()->json([
            'status' => 200,
            'message' => 'Airport hide successfully..!'
        ]);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function dataTable()
    {
        $airports = Airport::withTrashed()->where('country_code', 'US')->select(['id', 'slug', 'name', 'city', 'state', 'google_state_long_name', 'code', 'deleted_at']);

        return DataTables::of($airports)->addColumn('action', function ($airport) {
            return view('admin.airports.partials._action', compact('airport'));
        })->rawColumns(['action' => 'action'])->make(true);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function airportPlaces(Request $request)
    {
        if ($request->type === 'radar') {
            $data = $this->getPlacesFromRadar($request);
        } else {
            $data = $this->getPlacesFromGoogle($request);
        }

        list($places, $total_places, $duplicate_places) = $data;

        return response()->json([
            'status' => 200,
            'data' => ['places' => $places->object(), 'total_places' => $total_places, 'duplicate_places' => $duplicate_places],
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getPlacesFromRadar(Request $request)
    {
        $airport = Airport::query()->where('code', $request->airport_code)->firstOrFail();
        $category = Category::query()->where('radar_category', $request->radar_category)->firstOrFail();

        $url = 'https://api.radar.io/v1/search/places?categories=' . $category->radar_category . '&near=' . $airport->latitude . ',' . $airport->longitude . '&radius=' . config('aroundairports.radar.radius') . '&limit=' . config('aroundairports.radar.limit') . '';

        $places = Http::withHeaders([
            'Authorization' => config('aroundairports.radar.radar_test_secret_server')
        ])->get($url);

        if (!empty($places['places'])) {
            $fetch_places = $places->object();

            $places_service = new PlacesService();
            $places_service->store($fetch_places->places, $airport, $category);
        }

        list($total_places, $duplicate_places) = $this->getPlacesCounters($airport, $category);

        return [$places, $total_places, $duplicate_places];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getPlacesFromGoogle(Request $request)
    {
        $airport = Airport::query()->where('code', $request->airport_code)->firstOrFail();

        if ($request->has('google_type')) {
            $category = Category::query()->where('google_type', $request->google_type)->firstOrFail();

            $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $airport->latitude . ',' . $airport->longitude . '&radius='. config('aroundairports.google.radius') .'&type='. $category->google_type .'&key='. config('aroundairports.google.api_key') .'';
        } else {
            $category = Category::query()->where('name', $request->category_slug)->firstOrFail();

            $url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $airport->latitude . ',' . $airport->longitude . '&radius='. config('aroundairports.google.radius') .'&name='. $category->name .'&key='. config('aroundairports.google.api_key') .'';
        }

        $places = Http::get($url);

        if (!empty($places['results'])) {
            $fetch_places = $places->object();

            $google_places_service = new GooglePlacesService();
            $google_places_service->store($fetch_places->results, $airport, $category);
        }

        list($total_places, $duplicate_places) = $this->getPlacesCounters($airport, $category);

        return [$places, $total_places, $duplicate_places];
    }

    /**
     * @param $airport
     * @param $category
     * @return array
     */
    public function getPlacesCounters($airport, $category)
    {
        $total_places = Place::withTrashed()->where('airport_id', $airport->id)
            ->where('category_id', $category->id)
            ->count();

        $duplicate_places = Place::onlyTrashed()->where('airport_id', $airport->id)
            ->where('category_id', $category->id)
            ->count();

        return [$total_places, $duplicate_places];
    }

    /**
     * @param $airport_code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function airportCategoriesPlaces($airport_code)
    {
        $airport = Airport::query()->where('code', $airport_code)->firstOrFail();

        $this->dispatch(new CategoryGooglePlaces($airport));

        return redirect()->back();
    }

    public function setGoogleApi(Request $request){

        Airport::where('id',$request->airport_id)->update(['api_key'=>Crypt::encryptString($request->google_api_key)]);
        Session::flash('success','Api key added successfully');
        return redirect()->back();
    }
}
