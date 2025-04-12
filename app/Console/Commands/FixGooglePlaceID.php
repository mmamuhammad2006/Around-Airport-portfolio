<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FixGooglePlaceID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-google:place-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix fetch place google place id from google.';

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
        $categories = Category::all();
        $airports = Airport::query()->limit(10)->get();

        foreach ($airports as $airport) {
            foreach ($categories as $category) {
                $places = Place::byTenNearestPlaces($airport, $category)->get();

                foreach ($places as $place) {
                    if (!is_null($place->dup_google_place_id)) {
                        $this->info('We have already dup_google_place_id, so we can simply update google_place_id for that place: ' . $place->id);

                        $search_place = Place::withoutTrashed()
                            ->where('id', '<>', $place->id)
                            ->where('airport_id', $place->airport_id)
                            ->where('category_id', $place->category_id)
                            ->where('google_place_id', $place->dup_google_place_id)->first();

                        if ($search_place) {
                            $place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);
                        } else {
                            $place->update([
                                'google_place_id' => $place->dup_google_place_id
                            ]);
                        }
                    } else {
                        $this->warn('We don\'t have dup_google_place_id, so we can simply fetch dup_google_place_id for that place from google: ' . $place->id);

                        if (!empty(config('aroundairports.google.api_key'))) {
                            $google_place = Http::get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=' . urlencode($place->name) . '&inputtype=textquery&fields=place_id&locationbias=circle:1000@' . $place->latitude . ',' . $place->longitude . '&key=' . config('aroundairports.google.api_key') . '');

                            if ($google_place['status'] === 'OK' && count($google_place['candidates']) > 0) {
                                $google_place_data = $google_place->object();

                                $this->info('Google gives data for Place: ' . $place->id);

                                $search_place = Place::withoutTrashed()
                                    ->where('airport_id', $place->airport_id)
                                    ->where('category_id', $place->category_id)
                                    ->where('google_place_id', $google_place_data->candidates[0]->place_id)->first();

                                if ($search_place) {
                                    $place->update(['google_place_id' => null, 'dup_google_place_id' => $google_place_data->candidates[0]->place_id, 'deleted_at' => Carbon::now()]);
                                } else {
                                    $place->update([
                                        'google_place_id' => $google_place_data->candidates[0]->place_id,
                                        'dup_google_place_id' => $google_place_data->candidates[0]->place_id
                                    ]);
                                }
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
            }
        }
    }
}
