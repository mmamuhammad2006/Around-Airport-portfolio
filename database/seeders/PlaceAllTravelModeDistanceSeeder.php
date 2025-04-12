<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Place;

class PlaceAllTravelModeDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::withTrashed()->whereNotNull('all_travel_mode_distance')->select('id', 'airport_id', 'all_travel_mode_distance')->chunk(1000, function ($places) {
            if ($places) {
                foreach ($places as $place) {
                    $all_travel_mode_distance = unserialize($place->all_travel_mode_distance)->json();

                    $place->update([
                        'all_travel_mode_distance' => json_encode($all_travel_mode_distance)
                    ]);
                }
            }
        });
    }
}
