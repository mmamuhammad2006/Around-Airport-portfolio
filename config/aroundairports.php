<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Around Airports Basic Configuration File
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Aviation edge api parameter, setting api keys for authorization
     * to fetch airports.
     *
     * https://aviation-edge.com/developers/
     */
    'airports' => [
        'airports_api_key' => env('AIRPORTS_API_KEY') ? env('AIRPORTS_API_KEY') : '',
        'code_iso2_country' => 'US',
    ],

    /**
     * Radar api parameter, setting api keys for authorization
     * and set limit and area around to fetch places in
     * a given radius.
     *
     * https://radar.io/
     */
    'radar' => [
        'radar_test_secret_server' => env('RADAR_TEST_SECRET_SERVER') ? env('RADAR_TEST_SECRET_SERVER') : '',
        'radar_test_publishable_client' => env('RADAR_TEST_PUBLISHABLE_CLIENT') ? env('RADAR_TEST_PUBLISHABLE_CLIENT') : '',
        'radius' => 10000,
        'limit' => 100,
        'seeder_limit' => 5,
        'modes' => 'car',
        'units' => 'imperial',
    ],

    /**
     * Google api key for fetching place_id and
     * fetch images, reviews and rating etc.
     *
     * https://google.com/
     */
    'google' => [
        'api_key' => env('GOOGLE_API_KEY') ? env('GOOGLE_API_KEY') : '',
        'radius' => 3000
    ],

    /**
     * Expire days for airports to fetch from
     * Api's
     */
    'expire_days_for_airports' => 180,

    /**
     * Expire days for places to fetch from
     * Api's
     */
    'expire_days_for_places' => 6

];
