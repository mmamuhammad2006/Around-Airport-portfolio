<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlaceCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
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
            'google_rating' => $this->google_rating ? $this->google_rating : 0,
        ];
    }
}
