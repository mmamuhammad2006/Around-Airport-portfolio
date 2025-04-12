<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! empty(config('aroundairports.airports.airports_api_key'))) {
            $cities = Http::get('https://aviation-edge.com/v2/public/cityDatabase?key=' . config('aroundairports.airports.airports_api_key') . '&codeIso2Country=' . config('aroundairports.airports.code_iso2_country') . '');
            $cities = json_decode($cities);

            foreach ($cities as $city) {
                City::query()->updateOrCreate([
                    'code' => $city->codeIataCity,
                ],
                [
                    'code' => $city->codeIataCity,
                    'name' => $city->nameCity,
                    'latitude' => $city->latitudeCity,
                    'longitude' => $city->longitudeCity,
                    'timezone' => $city->timezone,
                    'data' => serialize($city),
                    'expire_at' => Carbon::now()->addDays(30),
                ]);
            }
        } else {
            $this->command->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
