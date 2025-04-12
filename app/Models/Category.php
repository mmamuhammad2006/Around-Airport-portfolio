<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug, SoftDeletes;

    /**
     * @var array
     */
    protected $appends = ['twitter_name'];

    /**
     * @var array
     */
    protected $guarded = [];

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
     * @return mixed|string|string[]
     */
    public function getTwitterNameAttribute()
    {
        return str_replace('&', '%26', $this->name);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function places()
    {
        return $this->hasMany(Place::class, 'category_id', 'id');
    }
}
