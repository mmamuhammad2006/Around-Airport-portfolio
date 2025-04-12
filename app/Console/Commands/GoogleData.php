<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GoogleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:data
                            {--limit=100 : How many airports you want to fetch data}
                            {--start= : Where you want to start fetch data}
                            {--end= : Where you want to end fetch data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Google data for -- first airports';

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
        $limit = (int)$this->option('limit');
        $start = (int)$this->option('start');
        $end = (int)$this->option('end');

        $categories = Category::all();
        $airports = Airport::withoutTrashed()->when($start && $end, function ($query) use ($start, $end) {
            $query->whereBetween('id', [$start, $end]);
        })->limit($limit)->get();

        $this->info('Airports: ' . $airports->pluck('id')->toJson());

        foreach ($airports as $airport) {
            foreach ($categories as $category) {
                $ten_nearest_places = Place::query()->where('airport_id', $airport->id)
                    ->where('category_id', $category->id)
                    ->whereNotNull('distance_value')
                    ->whereNotNull('google_place_id')
                    ->orderBy('distance_value', 'ASC')
                    ->limit(10)
                    ->get();

                $this->error('Airport ID: ' . $airport->id . ' Ten nearest places: ' . $ten_nearest_places->pluck('id')->toJson());

                if ($ten_nearest_places->isNotEmpty()) {
                    foreach ($ten_nearest_places as $place) {
//                        || Carbon::parse($place->expire_at)->lessThan(Carbon::now())
                        if (is_null($place->google_data)) {
                            $this->info('Airport ID: ' . $airport->id . ' Fetch Google Data for Place: ' . $place->id);

                            $place::getGoogleDataByPlaceID($place);
                        } else {
                            $this->warn('Airport ID: ' . $airport->id . ' Google Data for Place is up to date: ' . $place->id);
                        }
                    }
                }
            }
        }
    }
}
