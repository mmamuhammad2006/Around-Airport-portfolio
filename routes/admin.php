<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'auth', 'namespace' => 'Admin'], function (){
    Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');

    Route::get('/run/{job?}', 'DashboardController@run')->name('run');
    Route::get('/jobs/statuses', 'DashboardController@getJobsStatuses')->name('jobs.statuses');

    Route::get('airports/data/table', 'AirportController@dataTable')->name('airports.data.table');
    Route::post('airports/places', 'AirportController@airportPlaces')->name('airports.places');
    Route::get('airports/{airport}/categories/places', 'AirportController@airportCategoriesPlaces')->name('airports.categories.places');
    Route::resource('airports', 'AirportController');
    Route::post('airports/google/api/key','AirportController@setGoogleApi')->name('airports.google.api.key');

    Route::get('categories/data/table', 'CategoryController@dataTable')->name('categories.data.table');
    Route::resource('categories', 'CategoryController');

    Route::get('airlines/data/table', 'AirlineController@dataTable')->name('airlines.data.table');
    Route::resource('airlines', 'AirlineController');

    Route::get('places/data/table', 'PlaceController@dataTable')->name('places.data.table');
    Route::resource('places', 'PlaceController');
});

Auth::routes();
