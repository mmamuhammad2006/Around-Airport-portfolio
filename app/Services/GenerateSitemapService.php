<?php

namespace App\Services;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapService
{
    /**
     * @var string
     */
    public $log;

    /**
     * @var Sitemap
     */
    public $sitemap;

    /**
     * @var SitemapIndex
     */
    public $site_map_index;

    /**
     * @var \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    public $file_names;

    /**
     * PlacesService constructor.
     */
    public function __construct()
    {
        $this->log = '';
        $this->sitemap = Sitemap::create();
        $this->site_map_index = SitemapIndex::create();
        $this->file_names = collect();
    }

    /**
     * @param array $pages
     */
    public function addOtherPages(array $pages)
    {
        foreach ($pages as $page) {
            $this->sitemap->add(Url::create($page)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(1.0));
        }
    }

    /**
     * @param $airport
     */
    public function addAirport($airport)
    {
        $this->sitemap->add(Url::create('/' . $airport->code)
            ->setLastModificationDate(Carbon::parse($airport->updated_at))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
            ->setPriority(0.8));
    }

    /**
     * @param $airport
     * @param $categories
     */
    public function addAirportCategories($airport, $categories)
    {
        foreach ($categories as $category) {
            $this->sitemap->add(Url::create('/' . $airport->code . '/' . $category->slug)
                ->setLastModificationDate(Carbon::parse($category->updated_at))
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                ->setPriority(0.8));

            $this->addAirportCategoryPlaces($airport, $category);
        }
    }

    /**
     * @param $airport
     * @param $category
     */
    public function addAirportCategoryPlaces($airport, $category)
    {
        $places = Place::query()->where('airport_id', $airport->id)
            ->where('category_id', $category->id)
            ->get();

        foreach ($places as $place) {
            if ($place->airport) {
                $this->sitemap->add(Url::create('/' . $place->airport->code . '/' . $place->category->slug . '/' . $place->slug)
                    ->setLastModificationDate(Carbon::parse($place->updated_at))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(1.0));
            }
        }
    }

    /**
     * @param $airport
     */
    public function generateSiteMap($airport)
    {
        $path = public_path() . '/sitemaps';
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $file_name = 'airport-' . $airport->id . '-category-places-sitemap.xml';

        $this->file_names = $this->file_names->merge($file_name);

        $this->sitemap->writeToFile($path . '/' . $file_name);
    }

    /**
     * All sitemaps files to index in a
     * single sitemap.xml file.
     */
    public function indexSiteMapFiles()
    {
        foreach ($this->file_names as $file_name) {
            $this->site_map_index->add(config('app.url', 'https://aroundairports.com') . '/sitemaps/' . $file_name);
        }

        $this->site_map_index->writeToFile(public_path() . '/sitemap.xml');
    }

    /**
     * @param $count
     * @param $id
     *
     * Set log job output.
     */
    public function setLog($count, $id)
    {
        $this->log = 'Airports Completed: ' . $count .
            '<br> Last Completed Airport: ' . $id;
    }
}
