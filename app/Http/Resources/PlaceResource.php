<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'bio_image' => ! is_null($this->google_data) ? $this->bio_image : null,
            'distance_text' => $this->distance_text,
            'near_by_places' => $this->near_by_places,
            'google_rating' => $this->google_rating,
            'google_formatted_address' => $this->google_formatted_address,
            'google_reviews' => $this->google_reviews,
            'google_working_days_opening_timing' => $this->google_hours,
            'google_photos' => $this->google_photos,

            'category' => $this->category,
            'airport' => $this->airport,
        ];
    }
}
