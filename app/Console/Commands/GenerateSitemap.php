<?php

namespace App\Console\Commands;

use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * @var array
     */
    protected $file_names = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

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
//        $categories = Category::query()->get();
//
        $path = public_path() . '/sitemaps';
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
//
//        $sitemap = App::make('sitemap');
//
//        $sitemap->add(config('app.url', 'https://aroundairports.com'), null, 1, 'monthly');
//        $sitemap->add(config('app.url', 'https://aroundairports.com') . '/privacy', null, 1, 'monthly');
//        $sitemap->add(config('app.url', 'https://aroundairports.com') . '/terms-and-conditions', null, 1, 'monthly');
//
//        Airport::query()->where('country_code', 'US')->where('id', '>', 372)->chunk(1200, function ($airports) use ($sitemap, $categories, $path) {
//            foreach ($airports as $airport) {
//                $sitemap->add(config('app.url', 'https://aroundairports.com') . '/' . $airport->code, $airport->updated_at, 0.8, 'monthly');
//
//                foreach ($categories as $category) {
//                    $sitemap->add(config('app.url', 'https://aroundairports.com') . '/' . $airport->code . '/' . $category->slug, $category->updated_at, 0.8, 'monthly');
//
//                    Place::query()->where('airport_id', $airport->id)
//                        ->where('category_id', $category->id)
//                        ->chunk(20, function ($places) use ($sitemap) {
//                            foreach ($places as $place) {
//                                if ($place->airport) {
//                                    $sitemap->add(config('app.url', 'https://aroundairports.com') . '/' . $place->airport->code . '/' . $place->category->slug . '/' . $place->slug, $place->updated_at, 1.0, 'monthly');
//                                }
//                            }
//                        });
//                }
//
//                $sitemap->store('xml', 'sitemaps/airport-' . $airport->id . '-category-places-sitemap');
//
//                $sitemap->addSitemap(config('app.url', 'https://aroundairports.com') . '/sitemaps/airport-' . $airport->id . '-category-places-sitemap.xml');
//
//                $sitemap->model->resetItems();
//
//                $this->info('Airport, Categories and Places sitemap completed successfully: ' . $airport->id);
//            }
//        });
//
//        $sitemap->store('sitemapindex', 'sitemap');




        // create new sitemap object
        $sitemap = App::make('sitemap');

        // get all products from db (or wherever you store them)
        $places = Place::query()->whereNull('deleted_at')->take(50000)->get(['id', 'slug', 'airport_id', 'category_id']);

        // counters
        $counter = 0;
        $sitemapCounter = 0;

        // add every product to multiple sitemaps with one sitemap index
        foreach ($places as $p) {
            if ($counter == 50000) {
                // generate new sitemap file
                $sitemap->store('xml', 'sitemaps/sitemap-' . $sitemapCounter);
                // add the file to the sitemaps array
                $sitemap->addSitemap(secure_url('sitemaps/sitemap-' . $sitemapCounter . '.xml'));
                // reset items array (clear memory)
                $sitemap->model->resetItems();
                // reset the counter
                $counter = 0;
                // count generated sitemap
                $sitemapCounter++;
            }

            // add product to items array
            if ($p->airport) {
                $sitemap->add(config('app.url', 'https://aroundairports.com') . '/' . $p->airport->code . '/' . $p->category->slug . '/' . $p->slug, $p->updated_at, 1.0, 'monthly');
                // count number of elements
                $counter++;
            }
        }

        // you need to check for unused items
        if (!empty($sitemap->model->getItems())) {
            // generate sitemap with last items
            $sitemap->store('xml', 'sitemaps/sitemap-' . $sitemapCounter);
            // add sitemap to sitemaps array
            $sitemap->addSitemap(secure_url('sitemaps/sitemap-' . $sitemapCounter . '.xml'));
            // reset items array
            $sitemap->model->resetItems();
        }

        // generate new sitemapindex that will contain all generated sitemaps above
        $sitemap->store('sitemapindex', 'sitemap');
    }
}
