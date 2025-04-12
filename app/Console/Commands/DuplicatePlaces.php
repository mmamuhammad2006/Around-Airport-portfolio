<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;

class DuplicatePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicate:places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete duplicate places.';

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
        $airports = Airport::query()->where('country_code', 'US')
            ->where('id', '<', 101)
            ->get();

        foreach ($airports as $airport_key => $airport) {
            foreach ($categories as $category) {
                $places = Place::withoutTrashed()
                    ->where('airport_id', $airport->id)
                    ->where('category_id', $category->id)
                    ->whereNotNull('dup_google_place_id')
                    ->get();

                if ($places->isNotEmpty()) {
                    foreach ($places->groupBy('dup_google_place_id') as $places) {
                        if ($places->count() >= 2) {
                            $this->warn('Airport ID: ' . $airport->id . ', Category Name: ' . $category->name . ', Total Places Group By Name: ' . $places->pluck('id')->toJson());

                            $nearest_place = Place::withoutTrashed()->whereIn('id', $places->pluck('id')->toArray())
                                ->selectRaw('*, ( 3959 * acos( cos( radians(' . $airport->latitude . ') ) * cos( radians( latitude ) ) *
                                cos( radians( longitude ) - radians(' . $airport->longitude . ') ) + sin( radians(' . $airport->latitude . ') ) *
                                sin( radians( latitude ) ) ) ) AS distance')
                                ->orderBy('distance')
                                ->first();

                            $this->warn('Airport ID: ' . $airport->id . ', Category Name: ' . $category->name . ', Nearest Place: ' . $nearest_place->id);

                            if ($nearest_place) {
                                $places->filter(function ($place) use ($nearest_place) {
                                    return $nearest_place->id != $place->id;
                                })->each(function ($delete_place) {
                                    $this->error('Place successfully deleted: ' . $delete_place->id);

                                    $delete_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);
                                });

                                $this->info('Airport ID: ' . $airport->id . ', Category Name: ' . $category->name . ', Google Place ID for nearest Place: ' . $nearest_place->id);

                                $nearest_place->update([
                                    'google_place_id' => $nearest_place->dup_google_place_id
                                ]);
                            }
                        } else {
                            $places->each(function ($single_place) {
                                $this->info('Google Place ID for Place: ' . $single_place->id);

                                $single_place->update(['google_place_id' => $single_place->dup_google_place_id]);
                            });
                        }
                    }
                }
            }
        }
    }
}
