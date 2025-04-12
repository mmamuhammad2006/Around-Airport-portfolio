<?php

namespace App\Http\Controllers\SiteMap;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Models\Category;
use App\Models\Place;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Spatie\Sitemap\SitemapIndex;

class SiteMapController extends Controller
{
    /**
     * @return void
     */
    public function bigSitemap()
    {
        $sitemap = App::make('sitemap');

        $categories = Category::query()->get();
        $airports = Airport::query()->where('country_code', 'US')->take(2)->get();
        $url = config('app.url', 'https://aroundairports.com');

        foreach ($airports as $airport) {
            $sitemap->add($url . '/' . $airport->code, $airport->updated_at, 0.8, 'monthly');

            foreach ($categories as $category) {
                $sitemap->add($url . '/' . $airport->code . '/' . $category->slug, $category->updated_at, 0.8, 'monthly');

                Place::query()->where('airport_id', $airport->id)
                    ->where('category_id', $category->id)
                    ->chunk(2000, function ($places) use ($sitemap, $url) {
                        foreach ($places as $place) {
                            if ($place->airport) {
                                $sitemap->add($url . '/' . $place->airport->code . '/' . $place->category->slug . '/' . $place->slug, $place->updated_at, 1.0, 'monthly');
                            }
                        }
                    });
            }

            $path = public_path() . '/sitemaps';
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $sitemap->store('xml', 'sitemaps/airport-' . $airport->id . '-category-places-sitemap');
            $sitemap->addSitemap($url . '/sitemaps/airport-' . $airport->id . '-category-places-sitemap.xml');
            $sitemap->model->resetItems();
        }

        $sitemap->store('sitemapindex', 'sitemap');
    }

    /**
     * @return string
     */
    public function sitemap()
    {
        $files = array_diff(scandir(public_path() . '/sitemaps'), array('.', '..'));
        $site_index = SitemapIndex::create();

        foreach ($files as $file_name) {
            $site_index->add(config('app.url', 'https://aroundairports.com') . '/sitemaps/' . $file_name);
        }

        $site_index->writeToFile(public_path() . '/sitemap.xml');
        return 'sitemap.xml created successfully..!';
    }
}
