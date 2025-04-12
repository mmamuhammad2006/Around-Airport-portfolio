<?php

namespace App\Http\Controllers\Api;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  string  $code
     * @param  string  $category
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($code, $category, $slug)
    {
        $airport = Airport::query()->where('code', $code)->first();
        $category = Category::query()->where('slug', $category)->first();

        if ($airport && $category) {
            $place = Place::findBySlug($airport, $category, $slug);
            $place = new PlaceResource($place);

            if ($place) {
                return ApiResponse::success('Place retrieved successfully.', $place);
            }
        }

        return ApiResponse::error('Place not found.');
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
        //
    }
}
