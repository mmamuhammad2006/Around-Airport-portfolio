<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Illuminate\View\View;

class FrontendAirportBusinessController extends Controller
{
    /**
     * @param $airport
     * @param $category
     * @param $business
     * @return View
     */
    public function __invoke($airport, $category, $business)
    {
        $airport = Airport::query()->where('code', $airport)->firstOrFail();
        $category = Category::query()->where('slug', $category)->firstOrFail();
        $place = Place::findBySlug($airport, $category, $business);
        $categories = Category::all();

        return view('frontend.airports.code.business.index', compact('category', 'airport', 'place', 'categories'));
    }
}
