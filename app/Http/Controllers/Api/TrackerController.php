<?php

namespace App\Http\Controllers\Api;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\FlightFlightsCollection;
use App\Http\Resources\RouteFlightsCollection;
use App\Models\Airline;
use App\Models\Airport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TrackerController extends Controller
{
    /**
     * Track by Flight for searching.
     *
     * @param Request $request
     * @return \App\Helpers\ApiResponse
     */
    public function byFlight(Request $request)
    {
        $airline_name = explode(' - ', $request->airline_name);
        $flight_number = (int)$request->flight_number;

        $airline = Airline::query()->where('name', $airline_name[0])->firstOrFail();

        if (!empty($airline->iata_code)) {
            $flights= Http::get('http://aviation-edge.com/v2/public/timetable?key=' . config('aroundairports.airports.airports_api_key') . '&airline_iata=' . $airline->iata_code . '&flight_num=' . $flight_number . '');
        } else {
            $flights= Http::get('http://aviation-edge.com/v2/public/timetable?key=' . config('aroundairports.airports.airports_api_key') . '&airline_icao=' . $airline->icao_code . '&flight_num=' . $flight_number . '');
        }

        if (!$flights->offsetExists('error')) {
            $flights = $flights->object();

            $flights = collect($flights)->filter(function ($flight) use ($request) {
                return Carbon::parse($flight->arrival->scheduledTime)->isSameAs('Y-d-m', Carbon::parse($request->departure_date)->toDateString()) ||
                    Carbon::parse($flight->departure->scheduledTime)->isSameAs('Y-d-m', Carbon::parse($request->departure_date)->toDateString());
            });

            return ApiResponse::success('Flight retrieved successfully.', FlightFlightsCollection::collection($flights));
        }

        return ApiResponse::error('No Flight Found.');
    }

    /**
     * Track by Route for searching.
     *
     * @param Request $request
     * @return \App\Helpers\ApiResponse
     */
    public function byRoute(Request $request)
    {
        $flights = collect();
        $filter = ['type' => null, 'airports' => []];

        if ($request->has('departure_city_name') && !empty($request->get('departure_city_name'))) {
            $departure_city = explode(', ', $request->get('departure_city_name'));

            $filter['type'] = 'departure';
            $filter['airports'] = array_merge($filter['airports'], Airport::query()->where('city', $departure_city[0])->pluck('code')->toArray());
        }

        if ($filter['type'] == null) {
            $arrival_city = explode(', ', $request->get('arrival_city_name'));

            $filter['type'] = 'arrival';
            $filter['airports'] = array_merge($filter['airports'], Airport::query()->where('city', $arrival_city[0])->pluck('code')->toArray());
        }

        foreach ($filter['airports'] as $code) {
            $departure_flights = Http::get('http://aviation-edge.com/v2/public/timetable?key=' . config('aroundairports.airports.airports_api_key') . '&iataCode=' . $code . '&type=' . $filter['type'] . '');

            if (!$departure_flights->offsetExists('error')) {
                $flights->push($departure_flights->object());
            }
        }

        $flights = $flights->flatten();

        if ($filter['type'] == 'departure' && $request->has('arrival_city_name') && !empty($request->get('arrival_city_name'))) {
            $arrival_city = explode(', ', $request->get('arrival_city_name'));
            $arrival_airport_codes = Airport::query()->where('city', $arrival_city[0])->pluck('code')->toArray();

            $flights = $flights->filter(function ($flight) use ($arrival_airport_codes) {
                return in_array($flight->arrival->iataCode, $arrival_airport_codes);
            });
        }

        if ($request->has('time') && !empty($request->get('time'))) {
            $dates = $this->getTimeInterval($request->time);
            $start_date = $dates[0];
            $end_date = $dates[1];

            $flights = $flights->filter(function ($flight) use ($start_date, $end_date) {
                return Carbon::parse($flight->arrival->scheduledTime)->between($start_date, $end_date) ||
                    Carbon::parse($flight->departure->scheduledTime)->between($start_date, $end_date);
            });
        }

        if ($flights->isNotEmpty()) {
            return ApiResponse::success('Flights retrieved successfully.', RouteFlightsCollection::collection($flights));
        }

        return ApiResponse::error('No Flights Found.');
    }

    /**
     * @param $time
     * @return array
     */
    public function getTimeInterval($time)
    {
        switch ($time) {
            case 'all_day':
                $start_date = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 0, 0, 0);
                $end_date = Carbon::parse($start_date->toDateTimeString())->addDay(1)->subMinute();

                return [$start_date, $end_date];
            case 'morning':
                $start_date = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 0, 0, 0);
                $end_date = Carbon::parse($start_date->toDateTimeString())->addHours(12)->subMinute();

                return [$start_date, $end_date];
            case 'afternoon':
                $start_date = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 12, 0, 0);
                $end_date = Carbon::parse($start_date->toDateTimeString())->addHours(5)->subMinute();

                return [$start_date, $end_date];
            case 'evening':
                $start_date = Carbon::create(Carbon::now()->year, Carbon::now()->month, Carbon::now()->day, 17, 0, 0);
                $end_date = Carbon::parse($start_date->toDateTimeString())->addHours(7)->subMinute();

                return [$start_date, $end_date];
        }
    }

    /**
     * @param Request $request
     * @return \App\Helpers\ApiResponse
     */
    public function searchAirline(Request $request)
    {
        $airlines = Airline::query()
            ->where('name', 'LIKE', '%' . $request->airline . '%')
            ->orWhere('country', 'LIKE', '%' . $request->airline . '%')
            ->orWhere('iata_code', 'LIKE', '%' . $request->airline . '%')
            ->select('name', 'iata_code')
            ->get();

        return ApiResponse::success('Airlines retrieved successfully.', $airlines);
    }

    /**
     * @param Request $request
     * @return \App\Helpers\ApiResponse
     */
    public function searchCity(Request $request)
    {
        $cities = Airport::query()->where('country_code', 'US')
            ->where('city', 'LIKE', '%' . $request->city . '%')
            ->groupBy('city', 'google_state_short_name')
            ->select('city', 'google_state_long_name', 'google_state_short_name')
            ->get();

        return ApiResponse::success('Cities retrieved successfully.', $cities);
    }
}
