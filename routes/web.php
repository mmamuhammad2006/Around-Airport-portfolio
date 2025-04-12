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

Route::group(['namespace' => 'SiteMap'], function (){
    Route::get('BigSitemap','SiteMapController@bigSitemap')->name('site-map.summary');
    Route::get('sitemap','SiteMapController@sitemap')->name('site-map.summary');
});


Route::group(['namespace' => 'Frontend' ], function (){
    Route::get('privacy', 'FrontendPrivacyController')->name('privacy.index');
    Route::get('terms-and-conditions', 'FrontendTermsAndConditionsController')->name('terms-and-conditions.index');
    Route::get('/', 'FrontendAirportsController')->name('airports.management');
    Route::get('/{code}','FrontendAirportCodeController')->name('airports.code');
    Route::get('/{code}/{category}','FrontendAirportCategoryController')->name('airports.code.category');
    Route::get('/{code}/{category}/{slug}','FrontendAirportBusinessController')->name('airports.business.detail');
});


