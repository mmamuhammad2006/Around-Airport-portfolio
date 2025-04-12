<?php

namespace App\Console\Commands;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GooglePlaceID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:place-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch place google place id from google.';

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
        Place::withoutTrashed()->where('airport_id', '<', 101)->whereNotNull('distance_value')->whereNull('dup_google_place_id')->chunk(1000, function ($places) {
            foreach ($places as $place) {
                if (!is_null($place->google_place_id)) {
                    $this->info('We have already google_place_id, so we can simply update dup_google_place_id for that place: ' . $place->id);

                    $place->update([
                        'dup_google_place_id' => $place->google_place_id
                    ]);
                } else {
                    $this->warn('We don\'t have google_place_id, so we can simply fetch google_place_id for that place from google: ' . $place->id);

                    if (!empty(config('aroundairports.google.api_key'))) {
                        $google_place = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . urlencode($place->name) . '&inputtype=textquery&fields=place_id&locationbias=circle:1000@' . $place->latitude . ',' . $place->longitude . '&key=' . config('aroundairports.google.api_key') . '');

                        if ($google_place['status'] === 'OK' && count($google_place['candidates']) > 0) {
                            $google_place_data = $google_place->object();

                            $this->info('Google gives data for Place: ' . $place->id);

                            $place->update([
                                'dup_google_place_id' => $google_place_data->candidates[0]->place_id
                            ]);
                        } else if ($google_place['status'] === 'ZERO_RESULTS') {
                            $this->error('Google not gives data for Place, we can delete it: ' . $place->id);

                            $find_place = Place::withoutTrashed()->find($place->id);

                            if ($find_place) {
                                $this->error('Place successfully deleted: ' . $find_place->id);

                                $find_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);
                            }
                        }
                    } else {
                        $this->error('Please set GOOGLE_API_KEY keys in .env file.');
                    }

                }
            }
        });
    }
}
