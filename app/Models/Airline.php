<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Airline extends Model
{
    use HasSlug, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        '_id',
        'name',
        'iata_code',
        'icao_code',
        'country',
        'country_code',
        'size_airline',
        'call_sign',
        'status',
        'data',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json',
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
}
