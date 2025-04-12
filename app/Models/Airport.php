<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Airport extends Model
{
    use HasSlug, SoftDeletes;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
        'google_data' => 'json',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    /**
     * Get the airport's bio image.
     *
     * @return mixed
     */
    public function getBioImage()
    {
        if (!is_null($this->google_data) && isset($this->google_data['photos'])) {
            return $this->google_data['photos'][0]['photo_url'];
        }
    }

    /**
     * @param $code
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function findByCode($code)
    {
        $airport = self::query()->where('code', $code)->firstOrFail();

        if (! empty(config('aroundairports.google.api_key')) && Carbon::parse($airport->expire_at)->lessThan(Carbon::now())) {
            $google_place = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode($airport->name) . '&location=' . $airport->latitude . ',' . $airport->longitude . '&key=' . config('aroundairports.google.api_key') . '');

            if ($google_place['status'] === 'OK') {
                $google_place_detail = Http::get('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $google_place['results'][0]['place_id'] . '&key=' . config('aroundairports.google.api_key') . '');
                if ($google_place_detail['status'] === 'OK') {
                    $google_place_detail = $google_place_detail->object();

                    if (isset($google_place_detail->result->photos)) {
                        $google_place_detail->result->photos = self::setPhotoUrl($google_place_detail->result->photos);
                    }

                    $airport->update([
//                        'name' => $google_place_detail->result->name,
                        'google_state_long_name' => static::setStateFromGoogle($google_place_detail->result->address_components)['long_name'],
                        'google_state_short_name' => static::setStateFromGoogle($google_place_detail->result->address_components)['short_name'],
                        'google_place_id' => $google_place_detail->result->place_id,
                        'google_latitude' => $google_place_detail->result->geometry->location->lat,
                        'google_longitude' => $google_place_detail->result->geometry->location->lng,
                        'google_rating' => isset($google_place_detail->result->rating) ? $google_place_detail->result->rating : null,
                        'google_formatted_address' => $google_place_detail->result->formatted_address,
                        'google_data' => isset($google_place_detail->result) ? $google_place_detail->result : null,
                        'expire_at' => Carbon::now()->addYear(),
                    ]);
                }
            }
        }

        return $airport;
    }

    /**
     * @param $google_address_components
     * @return array
     */
    public static function setStateFromGoogle($google_address_components)
    {
        return collect($google_address_components)->filter(function ($data) {
            return in_array('administrative_area_level_1', $data->types);
        })->flatMap(function ($state) {
            return ['long_name' => $state->long_name ? $state->long_name : '', 'short_name' => $state->short_name ? $state->short_name : ''];
        })->toArray();
    }

    /**
     * @param $photos
     * @return array
     */
    public static function setPhotoUrl($photos)
    {
        return collect($photos)->each(function ($photo) {
            $google_photo = Http::get('https://maps.googleapis.com/maps/api/place/photo?maxwidth=' . $photo->width . '&photoreference=' . $photo->photo_reference . '&key=' . config('aroundairports.google.api_key') . '');

            $photo->photo_url = 'https://' . $google_photo->effectiveUri()->getAuthority() . $google_photo->effectiveUri()->getPath();
        })->toArray();
    }

    /**
     * @param $category
     * @return int
     */
    public function getTotalPlacesByCategory($category)
    {
        return $this->places()->where('category_id', $category->id)->count();
    }
}
