<?php

namespace App\Console\Commands;

use App\Imports\AirportsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportAirports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airports:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import airports from CSV';

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
        $file_name = 'airports_' . time() . '.csv';
        Storage::disk('local')->put('/public/' . $file_name, file_get_contents('https://raw.githubusercontent.com/ip2location/ip2location-iata-icao/master/iata-icao.csv'));

        if (Storage::disk('local')->exists('/public/' . $file_name)) {
            return Excel::import(new AirportsImport, Storage::path('/public/' . $file_name)) ? 1 : 0;
        }
    }
}
