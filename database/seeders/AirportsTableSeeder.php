<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Airport;
use App\Models\City;

class AirportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! empty(config('aroundairports.airports.airports_api_key'))) {
            $airports = Http::get('https://aviation-edge.com/v2/public/airportDatabase?key=' . config('aroundairports.airports.airports_api_key') . '&codeIso2Country=' . config('aroundairports.airports.code_iso2_country') . '');
            $airports = json_decode($airports);

            foreach ($airports as $airport) {
                $city = City::query()->whereCode($airport->codeIataCity)->first();

                Airport::query()->updateOrCreate([
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
                    'city' => ! is_null($city) ? $city->name : null,
                    'timezone' => $airport->timezone,
                    'data' => $airport,
                    'expire_at' => Carbon::now()->subDay(),
                ]);
            }
        } else {
            $this->command->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
