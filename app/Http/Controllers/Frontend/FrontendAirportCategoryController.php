<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Category;
use Illuminate\View\View;

class FrontendAirportCategoryController extends Controller
{
    /**
     * @param $airport
     * @param $category
     * @return View
     */
    public function __invoke($airport, $category)
    {
        $categories = Category::all();
        $airport = Airport::query()->where('code', $airport)->firstOrFail();
        $category = Category::query()->where('slug', $category)->firstOrFail();
        return view('frontend.airports.code.category.index', compact('category', 'airport', 'categories'));
    }
}
