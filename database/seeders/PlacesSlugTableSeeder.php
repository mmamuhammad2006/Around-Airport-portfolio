<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Place;
use Carbon\Carbon;

class PlacesSlugTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::onlyTrashed()->update(['slug' => null]);

        Place::query()->chunk(1000, function ($places) {
            foreach ($places as $place) {
                $place->update([
                    'updated_at' => Carbon::now()
                ]);

                $this->command->info('Place: ' . $place->id . ' Place Slug Update: ' . $place->slug);
            }
        });
    }
}
