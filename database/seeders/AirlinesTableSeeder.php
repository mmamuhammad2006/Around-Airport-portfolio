<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Airline;

class AirlinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!empty(config('aroundairports.airports.airports_api_key'))) {
            $airlines = Http::get('https://aviation-edge.com/v2/public/airlineDatabase?key=' . config('aroundairports.airports.airports_api_key'));
            $airlines = $airlines->object();

            foreach ($airlines as $airline) {
                $airline = Airline::query()->updateOrCreate([
                    '_id' => $airline->airlineId,
                ],
                [
                    '_id' => $airline->airlineId,
                    'name' => $airline->nameAirline,
                    'iata_code' => $airline->codeIataAirline,
                    'icao_code' => $airline->codeIcaoAirline,
                    'country' => $airline->nameCountry,
                    'country_code' => $airline->codeIso2Country,
                    'size_airline' => $airline->sizeAirline,
                    'call_sign' => $airline->callsign,
                    'status' => $airline->statusAirline,
                    'data' => $airline,
                ]);

                $this->command->info('Update or Create Airline: ' . $airline->id);
            }
        } else {
            $this->command->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
