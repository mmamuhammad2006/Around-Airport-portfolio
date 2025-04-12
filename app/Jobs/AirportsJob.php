<?php

namespace App\Jobs;

use App\Models\Airport;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Imtigger\LaravelJobStatus\Trackable;

class AirportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $retryUntil = 3600 * 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->prepareStatus();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $airports = Http::get('https://aviation-edge.com/v2/public/airportDatabase?key=' . config('aroundairports.airports.airports_api_key') . '&codeIso2Country=' . config('aroundairports.airports.code_iso2_country') . '');
        $airports = $airports->object();
        $new_airports = collect();

        $this->setProgressMax(count($airports));

        foreach ($airports as $key => $airport) {
            $city = City::query()->whereCode($airport->codeIataCity)->first();

            $search_airport = Airport::withTrashed()->where('code', $airport->codeIataAirport)->first();

            if ($search_airport) {
                $search_airport->update([
                    'city_code' => $airport->codeIataCity,
//                    'name' => $airport->nameAirport,
                    'latitude' => $airport->latitudeAirport,
                    'longitude' => $airport->longitudeAirport,
                    'country_code' => $airport->codeIso2Country,
                    'country_name' => $airport->nameCountry,
                    'city' => !is_null($city) ? $city->name : null,
                    'timezone' => $airport->timezone,
                    'data' => $airport,
                    'expire_at' => Carbon::now()->subDay(),
                ]);
            } else {
                $airport = Airport::query()->create([
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

                $new_airports->push($airport);
            }

            $this->setProgressNow($key + 1);
        }

        $this->setOutput(['log' => 'Total Airports: ' . count($airports) . '<br> New Airports: ' . $new_airports->count()]);
    }
}
