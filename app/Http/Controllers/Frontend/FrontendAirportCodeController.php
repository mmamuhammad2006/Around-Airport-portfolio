<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Category;

class FrontendAirportCodeController extends Controller
{
    public function __invoke($code)
    {
        $categories = Category::all();
        $airport = Airport::query()->where('code', $code)->firstOrFail();
        return view('frontend.airports.code.index', compact('airport', 'categories'));
    }
}
