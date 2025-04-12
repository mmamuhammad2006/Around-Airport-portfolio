<?php

namespace App\Services;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PlacesGoogleDataService
{
    /**
     * @var string
     */
    public $log;

    /**
     * @var int
     */
    public $places_google_data_count;

    /**
     * PlacesService constructor.
     */
    public function __construct()
    {
        $this->log = '';
        $this->places_google_data_count = 0;
    }

    /**
     * @param $places
     * @param $airport
     * @param $category
     */
    public function getPlacesGoogleData($places, $airport, $category)
    {
        foreach ($places as $place) {
            if (is_null($place->google_data)) {
                $place::getGoogleDataByPlaceID($place);

                $this->places_google_data_count++;
            } else {
                //
            }
        }
    }

    /**
     * Set log job output.
     */
    public function setLog()
    {
        $this->log .= 'Total Places: ' . $this->getTotalPlaces() .
            '<br> Places fetch Google Data: ' . $this->places_google_data_count;
    }

    /**
     * Get count of all places store
     * in database
     *
     * @return int
     */
    public function getTotalPlaces()
    {
        return Place::query()->count();
    }
}
