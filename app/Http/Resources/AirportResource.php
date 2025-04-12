<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AirportResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'city' => $this->city,
            'state' => $this->state ? $this->state : $this->google_state_long_name,
            'state_long_name' => $this->google_state_long_name,
            'state_short_name' => $this->google_state_short_name,
            'country' => $this->country_name,
            'bio_image' => ! is_null($this->google_data) ? $this->getBioImage() : null,
            'google_rating' => $this->google_rating,
            'google_formatted_address' => $this->google_formatted_address,
        ];
    }
}
