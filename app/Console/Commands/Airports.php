<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Airports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airports:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Airports fetch from aviation edge.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!empty(config('aroundairports.airports.airports_api_key'))) {
            $airports = Http::get('https://aviation-edge.com/v2/public/airportDatabase?key=' . config('aroundairports.airports.airports_api_key') . '&codeIso2Country=' . config('aroundairports.airports.code_iso2_country') . '');
            $airports = $airports->object();

            foreach ($airports as $airport) {
                $city = City::query()->whereCode($airport->codeIataCity)->first();

                $airport = Airport::query()->updateOrCreate([
                        'code' => $airport->codeIataAirport,
                    ],
                    [
                        'code' => $airport->codeIataAirport,
                        'city_code' => $airport->codeIataCity,
                        'name' => $airport->nameAirport,
                        'latitude' => $airport->latitudeAirport,
                        'longitude' => $airport->longitudeAirport,
                        'country_code' => $airport->codeIso2Country,
                        'country_name' => $airport->nameCountry,
                        'city' => !is_null($city) ? $city->name : null,
                        'timezone' => $airport->timezone,
                        'data' => $airport,
                        'expire_at' => Carbon::now()->subDay(),
                    ]);

                $this->info('Update or Create Airport: ' . $airport->id);
            }
        } else {
            $this->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
