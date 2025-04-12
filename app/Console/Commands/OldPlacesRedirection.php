<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\OldPlace;
use Illuminate\Console\Command;

class OldPlacesRedirection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:places-redirection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Old Places redirection.';

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
        $server = config('app.url', 'https://aroundairports.com');
        $places = OldPlace::query()->groupBy('category', 'iata')->get();

        $file = fopen(public_path('redirection.txt'), 'w');
        fwrite($file, '');

        foreach ($places as $place) {
            $airport = Airport::query()->where('code', $place->iata)->first();
            $category = Category::query()->where('slug', $place->category)->first();

            if ($airport && is_null($category)) {
                $category = $this->getCategory($place->category);

                $redirection = "rewrite ^/". $place->iata ."/". $place->category ."$ ". $server ."/". $airport->code ."/". $category ." permanent;\n";
                fwrite($file, $redirection);
            }
        }

        fclose($file);
    }

    /**
     * @param $category
     * @return string
     */
    public function getCategory($category)
    {
        switch ($category) {
            case 'coffee':
                return 'cafes-coffee-shops';
            case 'fastfood':
                return 'fast-food-restaurants';
            case 'rentalcars':
                return 'car-truck-rental';
            case 'buses':
                return 'bus-stations';
            case 'groceries':
                return 'grocery-stores';
            case 'healthcare':
                return 'health-care-providers';
            case 'theaters':
                return 'movie-theaters';
            case 'shop':
                return 'shopping';
            case 'trains':
                return 'train-stations';
            case 'bookstores':
                return 'book-stores';
            default:
                return $category;
        }
    }
}
