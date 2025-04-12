<?php

namespace App\Console\Commands;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Cities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cities fetch from aviation edge.';

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
        if (! empty(config('aroundairports.airports.airports_api_key'))) {
            $cities = Http::get('https://aviation-edge.com/v2/public/cityDatabase?key=' . config('aroundairports.airports.airports_api_key') . '&codeIso2Country=' . config('aroundairports.airports.code_iso2_country') . '');
            $cities = $cities->object();

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
            $this->error('Please set Aviation Edge api AIRPORTS_API_KEY key in .env file.');
        }
    }
}
