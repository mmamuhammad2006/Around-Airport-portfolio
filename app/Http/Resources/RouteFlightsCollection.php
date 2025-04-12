<?php

namespace App\Http\Resources;

use App\Models\Airport;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteFlightsCollection extends JsonResource
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
            'airline' => $this->airline,
            'flight' => $this->flight,
            'arrival' => $this->arrival,
            'arrival_airport' => $this->getAirport($this->arrival->iataCode),
            'departure' => $this->departure,
            'departure_airport' => $this->getAirport($this->departure->iataCode),
            'status' => $this->status,
            'type' => $this->type,
        ];
    }

    /**
     * @param $code
     * @return string
     */
    public function getAirport($code)
    {
        return Airport::withTrashed()->where('code', $code)->first();
    }
}
