<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * @var string[][]
     */
    protected $categories = [
        [
            'name' => 'ATMs',
            'radar_category' => 'automated-teller-machine-atm',
            'google_type' => 'atm',
            'icon' => 'fa fa-credit-card'
        ],
        [
            'name' => 'Bars',
            'radar_category' => 'bar',
            'google_type' => 'bar',
            'icon' => 'fa fa-glass-cheers'
        ],
        [
            'name' => 'Book Stores',
            'radar_category' => 'book-store',
            'google_type' => 'book_store',
            'icon' => 'fa fa-book'
        ],
        [
            'name' => 'Bus Stations',
            'radar_category' => 'bus-station',
            'google_type' => 'bus_station',
            'icon' => 'fa fa-bus'
        ],
        [
            'name' => 'Cafes & Coffee Shops',
            'radar_category' => 'cafe,coffee-shop',
            'google_type' => 'cafe',
            'icon' => 'fa fa-coffee'
        ],
        [
            'name' => 'Car & Truck Rental',
            'radar_category' => 'car-rental,truck-rental',
            'google_type' => 'car_rental',
            'icon' => 'fa fa-car'
        ],
        [
            'name' => 'Fast Food Restaurants',
            'radar_category' => 'fast-food-restaurant',
            'google_type' => 'food',
            'icon' => 'fa fa-utensils'
        ],
        [
            'name' => 'Gas',
            'radar_category' => 'gas-station',
            'google_type' => 'gas_station',
            'icon' => 'fa fa-gas-pump'
        ],
        [
            'name' => 'Grocery Stores',
            'radar_category' => 'food-grocery',
            'google_type' => 'store',
            'icon' => 'fa fa-store'
        ],
        [
            'name' => 'Health Care Providers',
            'radar_category' => 'home-health-care-service',
            'google_type' => 'health',
            'icon' => 'fa fa-x-ray'
        ],
        [
            'name' => 'Hotels',
            'radar_category' => 'hotel',
            'google_type' => 'lodging',
            'icon' => 'fa fa-hotel'
        ],
        [
            'name' => 'Information',
            'radar_category' => 'information-technology-company',
            'google_type' => null,
            'icon' => 'fa fa-question-circle'
        ],
        [
            'name' => 'Limos',
            'radar_category' => 'limo-service',
            'google_type' => null,
            'icon' => 'fa fa-lemon'
        ],
        [
            'name' => 'Malls',
            'radar_category' => 'shopping-mall',
            'google_type' => 'shopping_mall',
            'icon' => 'fa fa-building'
        ],
        [
            'name' => 'Movie Theaters',
            'radar_category' => 'movie-theatre',
            'google_type' => 'movie_theater',
            'icon' => 'fa fa-film'
        ],
        [
            'name' => 'Parking',
            'radar_category' => 'parking',
            'google_type' => 'parking',
            'icon' => 'fa fa-parking'
        ],
        [
            'name' => 'Parks',
            'radar_category' => 'park',
            'google_type' => 'park',
            'icon' => 'fa fa-child'
        ],
        [
            'name' => 'Pharmacies',
            'radar_category' => 'pharmacy',
            'google_type' => 'pharmacy',
            'icon' => 'fa fa-prescription-bottle-alt'
        ],
        [
            'name' => 'Restaurants',
            'radar_category' => 'restaurant',
            'google_type' => 'restaurant',
            'icon' => 'fa fa-concierge-bell'
        ],
        [
            'name' => 'Shipping',
            'radar_category' => 'automotive-shipping-service',
            'google_type' => null,
            'icon' => 'fa fa-shipping-fast'
        ],
        [
            'name' => 'Shopping',
            'radar_category' => 'shopping-retail',
            'google_type' => null,
            'icon' => 'fa fa-shopping-cart'
        ],
        [
            'name' => 'Taxis',
            'radar_category' => 'taxi',
            'google_type' => 'taxi_stand',
            'icon' => 'fa fa-taxi'
        ],
        [
            'name' => 'Train Stations',
            'radar_category' => 'train-station',
            'google_type' => 'train_station',
            'icon' => 'fa fa-train'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categories as $category) {
            Category::query()->updateOrCreate([
//                'name' => $category['name'],
                'radar_category' => $category['radar_category']
            ], $category);
        }
    }
}
