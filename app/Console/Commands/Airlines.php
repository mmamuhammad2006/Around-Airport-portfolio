<?php

namespace App\Console\Commands;

use App\Models\Airline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Airlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airlines:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Airlines fetch from aviation edge.';

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

                $this->info('Update or Create Airline: ' . $airline->id);
            }
        } else {
            $this->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
