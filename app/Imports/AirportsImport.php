<?php

namespace App\Imports;

use App\Models\Airport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AirportsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $airports
     */
    public function collection(Collection $airports)
    {
        foreach ($airports as $airport) {
            $airport = $airport->toArray();

            if ($airport['iata']) {
                Airport::query()->updateOrCreate([
                    'code' => $airport['iata']
                ],
                [
                    'code' => $airport['iata'],
                    'name' => $airport['airport'],
                    'state' => $airport['region_name'],
                    'latitude' => $airport['latitude'],
                    'longitude' => $airport['longitude'],
                ]);
            }
        }
    }
}
