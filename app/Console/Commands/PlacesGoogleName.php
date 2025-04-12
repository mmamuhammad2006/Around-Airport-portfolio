<?php

namespace App\Console\Commands;

use App\Models\Place;
use Illuminate\Console\Command;

class PlacesGoogleName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'places:google-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All places name update from google data.';

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
        Place::withTrashed()->whereNotNull('google_place_id')->each(function ($place) {
            if ($place->google_data) {
                $this->info('Place name update from google data: ' . $place->id);

                $place->update([
                    'name' => $place->google_data['name']
                ]);
            }
        });
    }
}
