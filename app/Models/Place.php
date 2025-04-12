<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Carbon\Carbon;

class Place extends Model
{
    use HasSlug, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'slug',
        'airport_id',
        'category_id',
        '_id',
        'name',
        'latitude',
        'longitude',
        'distance_text',
        'distance_value',
        'all_travel_mode_distance',
        'google_place_id',
        'dup_google_place_id',
        'google_latitude',
        'google_longitude',
        'google_rating',
        'google_formatted_address',
        'data',
        'google_data',
        'expire_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var array
     */
    protected $appends = ['bio_image'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
        'all_travel_mode_distance' => 'json',
        'google_data' => 'json',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return "{$model->name} - '-near-' {$model->airport()->withTrashed()->first()->code} - 'airport'";
            })
            ->saveSlugsTo('slug');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getNearByPlacesAttribute()
    {
        return self::query()->where('id', '<>', $this->id)
            ->where('airport_id', $this->airport_id)
            ->where('category_id', $this->category_id)
            ->whereNotNull('distance_value')
            ->orderBy('distance_value', 'ASC')
            ->limit(5)
            ->pluck('name', 'slug');
    }

    /**
     * @return array|mixed
     */
    public function getGoogleReviewsAttribute()
    {
        if (!is_null($this->google_data) && isset($this->google_data['reviews'])) {
            return $this->google_data['reviews'];
        }

        return [];
    }

    /**
     * Get the place's hours.
     *
     * @return array
     */
    public function getGoogleHoursAttribute()
    {
        if (isset($this->google_data['opening_hours']) && isset($this->google_data['opening_hours']['weekday_text'])) {
            return collect($this->google_data['opening_hours']['weekday_text'])->map(function ($day_time) {
                $day_time = explode(': ', $day_time);

                return ['day' => $day_time[0], 'time' => $day_time[1], 'selected' => Carbon::now()->dayName === $day_time[0], 'class' => Carbon::now()->dayName === $day_time[0] ? 'font-weight-bold' : ''];
            })->toArray();
        }

        return [];
    }

    /**
     * Get the place's bio image.
     *
     * @return mixed
     */
    public function getBioImageAttribute()
    {
        if (!is_null($this->google_data) && isset($this->google_data['photos']) && isset($this->google_data['photos'][0])) {
            return $this->google_data['photos'][0]['photo_url'];
        }
    }

    /**
     * Get the place's google photos.
     *
     * @return array|mixed
     */
    public function getGooglePhotosAttribute()
    {
        if (!is_null($this->google_data) && isset($this->google_data['photos'])) {
            return $this->google_data['photos'];
        }

        return [];
    }

    /**
     * @param $airport
     * @param $category
     * @param $slug
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function findBySlug($airport, $category, $slug)
    {
        $place = self::query()
            ->where('airport_id', $airport->id)
            ->where('category_id', $category->id)
            ->where('slug', $slug)
            ->first();

        if (is_null($place->google_data) || Carbon::parse($place->expire_at)->lessThan(Carbon::now())) {
            self::getGoogleDataByPlaceID($place);
        }

        return $place;
    }

    /**
     * @param $query
     * @param $airport
     * @param $category
     * @return mixed
     */
    public function scopeByTenNearestPlaces($query, $airport, $category)
    {
        return $query->where('airport_id', $airport->id)
            ->where('category_id', $category->id)
            ->selectRaw('*, ( 3959 * acos( cos( radians(' . $airport->latitude . ') ) * cos( radians( latitude ) ) *
                                cos( radians( longitude ) - radians(' . $airport->longitude . ') ) + sin( radians(' . $airport->latitude . ') ) *
                                sin( radians( latitude ) ) ) ) AS distance')
            ->orderBy('distance')
            ->limit(10);
    }

    /**
     * @param $place
     */
    public static function getGoogleDataByPlaceID($place)
    {
        $search_place = self::query()->where('google_place_id', $place->google_place_id)->first();

        if ($search_place && Carbon::parse($search_place->expire_at)->greaterThan(Carbon::now())) {
            $place->update([
                'name' => $search_place->name,
                'google_latitude' => $search_place->google_latitude,
                'google_longitude' => $search_place->google_longitude,
                'google_rating' => $search_place->google_rating,
                'google_formatted_address' => $search_place->google_formatted_address,
                'google_data' => $search_place->google_data,
                'expire_at' => Carbon::now()->addDays(config('aroundairports.expire_days_for_places')),
            ]);
        } else {
            $apiKey = $place->airport->api_key ?? config('aroundairports.google.api_key');
            $google_place_detail = Http::get('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . $place->google_place_id . '&key=' . $apiKey . '');

            if ($google_place_detail['status'] === 'OK') {
                $google_place_detail = $google_place_detail->object();

                if (isset($google_place_detail->result->photos)) {
                    $google_place_detail->result->photos = self::setPhotoUrl($google_place_detail->result->photos,$place);
                }

                $place->update([
                    'name' => $google_place_detail->result->name,
                    'google_latitude' => $google_place_detail->result->geometry->location->lat,
                    'google_longitude' => $google_place_detail->result->geometry->location->lng,
                    'google_rating' => isset($google_place_detail->result->rating) ? $google_place_detail->result->rating : null,
                    'google_formatted_address' => $google_place_detail->result->formatted_address,
                    'google_data' => isset($google_place_detail->result) ? $google_place_detail->result : null,
                    'expire_at' => Carbon::now()->addDays(config('aroundairports.expire_days_for_places')),
                ]);
            }
        }
    }

    /**
     * @param $photos
     * @return array
     */
    public static function setPhotoUrl($photos,$place)
    {
        $apiKey = $place->airport->api_key ?? config('aroundairports.google.api_key');
        return collect($photos)->each(function ($photo) use ($apiKey) {
            $google_photo = Http::get('https://maps.googleapis.com/maps/api/place/photo?maxwidth=' . $photo->width . '&photoreference=' . $photo->photo_reference . '&key=' . $apiKey . '');

            $photo->photo_url = 'https://' . $google_photo->effectiveUri()->getAuthority() . $google_photo->effectiveUri()->getPath();
        })->toArray();
    }

    /**
     * @return string
     */
    public function getAverageRating()
    {
        if (empty($this->google_rating)) {
            return 0 . '%';
        }

        return (floatval($this->google_rating) / floatval(5)) * 100 . '%';
    }

    /**
     * @param $rating
     * @return string
     */
    public function getReviewAverageRating($rating)
    {
        if (empty($rating)) {
            return 0 . '%';
        }

        return (floatval($rating) / floatval(5)) * 100 . '%';
    }
}
