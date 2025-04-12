<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'Api', 'as' => 'api.'], function () {
    Route::resource('categories', 'CategoryController');

    Route::get('get-airports','AirportController@getAirports');
    Route::get('airports/{airport}/categories/{category}', 'AirportController@getAroundPlaces')->name('airports.around.places');
    Route::resource('airports', 'AirportController');

    Route::get('places/{code}/{category}/{slug}', 'PlaceController@show')->name('places.show');
    Route::resource('places', 'PlaceController')->except('show');

    Route::post('tracker/flight', 'TrackerController@byFlight')->name('tracker.flight');
    Route::post('tracker/route', 'TrackerController@byRoute')->name('tracker.route');
    Route::post('tracker/airline', 'TrackerController@searchAirline')->name('tracker.airline');
    Route::post('tracker/city', 'TrackerController@searchCity')->name('tracker.city');
});
