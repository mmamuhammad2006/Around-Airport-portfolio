<?php

namespace App\Http\Resources;

use App\Models\Airport;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightFlightsCollection extends JsonResource
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
            'arrival' => $this->arrival,
            'codeshared' => $this->codeshared,
            'departure' => $this->departure,
            'flight' => $this->flight,
            'arrival_airport' => $this->getAirport($this->arrival->iataCode),
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
