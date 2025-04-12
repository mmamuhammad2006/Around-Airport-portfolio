<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FrontendTermsAndConditionsController extends Controller
{
    /**
     * @return View
     */
    public function __invoke()
    {
        return view('frontend.terms-and-conditions');
    }
}
