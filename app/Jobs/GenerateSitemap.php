<?php

namespace App\Jobs;

use App\Models\Airport;
use App\Models\Category;
use App\Services\GenerateSitemapService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Imtigger\LaravelJobStatus\Trackable;

class GenerateSitemap implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $retryUntil = 3600 * 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->prepareStatus();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $categories = Category::all();
        $airports = Airport::query()->where('country_code', 'US')->get();
        $this->setProgressMax($airports->count());

        $site_map_service = new GenerateSitemapService();
        $site_map_service->addOtherPages([config('app.url', 'https://aroundairports.com'), 'privacy', 'terms-and-conditions']);

        foreach ($airports as $key => $airport) {
            $site_map_service->addAirport($airport);

            $site_map_service->addAirportCategories($airport, $categories);

            $site_map_service->generateSiteMap($airport);

            $this->setProgressNow($key + 1);

            $site_map_service->setLog($key + 1, $airport->id);

            $this->setOutput(['log' => $site_map_service->log]);
        }

        $site_map_service->indexSiteMapFiles();
    }
}
