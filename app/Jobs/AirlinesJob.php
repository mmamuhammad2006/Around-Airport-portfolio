<?php

namespace App\Jobs;

use App\Models\Airline;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Imtigger\LaravelJobStatus\Trackable;

class AirlinesJob implements ShouldQueue
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
        $airlines = Http::get('https://aviation-edge.com/v2/public/airlineDatabase?key=' . config('aroundairports.airports.airports_api_key'));
        $airlines = $airlines->object();
        $new_airlines = collect();

        $this->setProgressMax(count($airlines));

        foreach ($airlines as $key => $airline) {
            $search_airline = Airline::query()->where('_id', $airline->airlineId)->first();

            if ($search_airline) {
                $search_airline->update([
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
            } else {
                $airline = Airline::query()->create([
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

                $new_airlines->push($airline);
            }

            $this->setProgressNow($key + 1);
        }

        $this->setOutput(['log' => 'Total Airlines: ' . count($airlines) . '<br> New Airlines: ' . $new_airlines->count()]);
    }
}
