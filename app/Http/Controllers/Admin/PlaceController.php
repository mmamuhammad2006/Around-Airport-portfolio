<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.places.index');
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
        $place = Place::query()->where('slug', $slug)->first();

        return view('admin.places.show', compact('place'));
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

    /**
     * @return mixed
     * @throws \Exception
     */
    public function dataTable()
    {
        $places = Place::query()->select(['id', 'slug', 'name', 'airport_id', 'category_id', 'distance_value', 'distance_text']);

        return DataTables::of($places)->editColumn('airport_id', function ($place) {
            return $place->airport->code;
        })->editColumn('distance_text', function ($place) {
            return $place->distance_text ? $place->distance_text : '-';
        })->editColumn('category_id', function ($place) {
            return $place->category->radar_category;
        })->addColumn('action', function ($place) {
            return view('admin.places.partials._action', compact('place'));
        })->rawColumns(['action' => 'action'])->make(true);
    }
}
