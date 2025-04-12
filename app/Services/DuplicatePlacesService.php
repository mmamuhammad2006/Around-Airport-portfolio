<?php

namespace App\Services;

use App\Models\Place;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DuplicatePlacesService
{
    /**
     * @var string
     */
    public $log;

    /**
     * @var int
     */
    public $dup_places_count;

    /**
     * PlacesService constructor.
     */
    public function __construct()
    {
        $this->log = '';
        $this->dup_places_count = 0;
    }

    /**
     * @param $places
     * @param $airport
     */
    public function removeDuplicatePlaces($places, $airport)
    {
        foreach ($places->groupBy('dup_google_place_id') as $places) {
            if ($places->count() >= 2) {
                $nearest_place = Place::withoutTrashed()->whereIn('id', $places->pluck('id')->toArray())
                    ->selectRaw('*, ( 3959 * acos( cos( radians(' . $airport->latitude . ') ) * cos( radians( latitude ) ) *
                                cos( radians( longitude ) - radians(' . $airport->longitude . ') ) + sin( radians(' . $airport->latitude . ') ) *
                                sin( radians( latitude ) ) ) ) AS distance')
                    ->orderBy('distance')
                    ->first();

                if ($nearest_place) {
                    $places->filter(function ($place) use ($nearest_place) {
                        return $nearest_place->id != $place->id;
                    })->each(function ($delete_place) {
                        $delete_place->update(['google_place_id' => null, 'deleted_at' => Carbon::now()]);
                    });

                    $nearest_place->update([
                        'google_place_id' => $nearest_place->dup_google_place_id
                    ]);
                }

                $this->dup_places_count++;
            } else {
                $places->each(function ($single_place) {
                    $single_place->update(['google_place_id' => $single_place->dup_google_place_id]);
                });
            }
        }
    }

    /**
     * Set log job output.
     */
    public function setLog()
    {
        $this->log .= 'Total Places: ' . $this->getTotalPlaces() .
            '<br> Duplicate Places: ' . $this->dup_places_count;
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
