<template>
    <div>
        <div class="container mt-4">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom" style="background: #f4f8fb;">
                            <ul class="nav nav-tabs card-header-tabs" id="cardTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold border-0 bg-transparent text-dark"
                                       :class="activeTab() === 'byFlight' ? 'active' : ''" id="home-tab"
                                       data-toggle="tab"
                                       href="#byFlight" role="tab"
                                       aria-controls="byFlight"
                                       aria-selected="true">By Flight</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold border-0 bg-transparent text-dark"
                                       :class="activeTab() === 'byRoute' ? 'active' : ''" id="profile-tab"
                                       data-toggle="tab"
                                       href="#byRoute" role="tab" aria-controls="byRoute"
                                       aria-selected="false">By Route</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body" style="background: #ff6d4b;">
                            <div class="tab-content" id="cardTabContent">
                                <div class="tab-pane" :class="activeTab() === 'byFlight' ? 'show active' : ''"
                                     id="byFlight" role="tabpanel" aria-labelledby="byFlight-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <form>
                                                <div class="row">
                                                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                                        <label>Airline<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                               placeholder="Enter name of code"
                                                               v-model="flightTrackerForm.airline_name"
                                                               @input="searchAirline(flightTrackerForm.airline_name)">
                                                        <div class="dropdown-menu search-airports"
                                                             v-if="searchAirlinesData.length > 0">
                                                            <h6 class="dropdown-header">Airlines</h6>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                               v-for="(airline, index) in searchAirlinesData"
                                                               @click="selectAirline('flight', airline.name + ' - ' + airline.iata_code)"><span
                                                                v-if="airline.iata_code">({{
                                                                    airline.iata_code
                                                                }})</span>
                                                                {{ airline.name | readMore(32, '...') }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="form-group  col-lg-4 col-md-4 col-sm-12">
                                                        <label>Flight Number<span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" placeholder=""
                                                               v-model="flightTrackerForm.flight_number"
                                                               maxlength="4">
                                                    </div>
                                                    <div class="form-group  col-lg-4 col-md-4 col-sm-12">
                                                        <label>Departing Date<span class="text-danger">*</span></label>
                                                        <vue-ctk-date-time-picker
                                                            :label="'Departing Date'"
                                                            v-model="flightTrackerForm.departure_date"
                                                            :onlyDate="true"
                                                            :format="'DD-MM-YYYY'"
                                                            :formatted="'ll'"
                                                            :noLabel="true"/>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <small class="float-left"><span
                                                            class="text-danger">Note: </span>All fields with (<span
                                                            class="text-danger">*</span>) is required.</small>
                                                        <button type="button" class="btn btn-warning float-right"
                                                                @click="trackByFlight()"><i
                                                            class="fa fa-search"></i> Track Flight
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="card border-light mb-3"
                                                 v-if="Object.keys(flightTrackDetail).length > 0">
                                                <div class="card-header"><i class="fa fa-plane"></i> Flight Detail</div>
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        Status: <label
                                                        class="badge badge-info">{{ flightTrackDetail.status }}</label>
                                                    </h5>

                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col" class="p-0 bg-transparent border-0">
                                                                <span class="airports-yellow-badge"> Departure</span>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 320px;">Airport:</td>
                                                            <td>
                                                                {{ flightTrackDetail.departure_airport.name }}
                                                                <span class="d-block">{{
                                                                        flightTrackDetail.departure_airport.city
                                                                    }}, {{
                                                                        flightTrackDetail.departure_airport.country_name
                                                                    }} ({{
                                                                        flightTrackDetail.departure.iataCode
                                                                    }})</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Scheduled Time:</td>
                                                            <td>{{
                                                                    new Date(flightTrackDetail.departure.scheduledTime) | dateFormat('hh:mm a, MMM D')
                                                                }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Left Gate Time:</td>
                                                            <td v-if="flightTrackDetail.departure.actualRunway">
                                                                {{
                                                                    new Date(flightTrackDetail.departure.actualRunway) | dateFormat('hh:mm a, MMM D')
                                                                }}
                                                            </td>
                                                            <td v-else>--</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Terminal - Gate:</td>
                                                            <td>Terminal {{ flightTrackDetail.departure.terminal }} - {{
                                                                    flightTrackDetail.departure.gate
                                                                }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col" class="p-0 bg-transparent border-0">
                                                                <span class="airports-yellow-badge">Arrival</span>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 320px;">Airport:</td>
                                                            <td>
                                                                {{ flightTrackDetail.arrival_airport.name }}
                                                                <span class="d-block">{{
                                                                        flightTrackDetail.arrival_airport.city
                                                                    }}, {{
                                                                        flightTrackDetail.arrival_airport.country_name
                                                                    }} ({{ flightTrackDetail.arrival.iataCode }})</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Scheduled Time:</td>
                                                            <td>{{
                                                                    new Date(flightTrackDetail.arrival.scheduledTime) | dateFormat('hh:mm a, MMM D')
                                                                }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Estimated Time:</td>
                                                            <td v-if="flightTrackDetail.arrival.estimatedTime">
                                                                {{
                                                                    new Date(flightTrackDetail.arrival.estimatedTime) | dateFormat('hh:mm a, MMM D')
                                                                }}
                                                            </td>
                                                            <td v-else>--</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Terminal - Gate:</td>
                                                            <td>Terminal {{ flightTrackDetail.arrival.terminal }} - {{
                                                                    flightTrackDetail.arrival.gate
                                                                }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Baggage Claim:</td>
                                                            <td>{{
                                                                    flightTrackDetail.arrival.baggage === null ? '-' : flightTrackDetail.arrival.baggage
                                                                }}
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col" class="p-0 bg-transparent border-0">
                                                            <span class="airports-yellow-badge">
                                                              Aircraft's Detail
                                                            </span>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 320px;">Flight:</td>
                                                            <td>{{ flightTrackDetail.airline.name }}
                                                                {{ flightTrackDetail.flight.number }}
                                                                ({{ flightTrackDetail.flight.iataNumber }})
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>

                                            <div class="card border-light mb-3"
                                                 v-if="Object.keys(flightTrackDetail).length === 0 && flightTrackNotFound">
                                                <div class="card-body"><h6>No Flight Founds.</h6></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" :class="activeTab() === 'byRoute' ? 'show active' : ''"
                                     id="byRoute" role="tabpanel" aria-labelledby="byRoute-tab">
                                    <div class="card">
                                        <div class="card-body">

                                            <form>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-check form-check-inline"
                                                             v-for="(time, index) in times">
                                                            <input class="form-check-input" type="radio" :id="index"
                                                                   name="time" :value="time.value"
                                                                   v-model="flightRouteTrackerForm.time">
                                                            <label class="form-check-label" :for="index">{{
                                                                    time.label
                                                                }}</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="row">
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label>Departure</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="New York, LA"
                                                               v-model="flightRouteTrackerForm.departure_city_name"
                                                               @input="searchCity('departure', flightRouteTrackerForm.departure_city_name)">
                                                        <div class="dropdown-menu search-airports"
                                                             v-if="searchCities.length > 0 && showDepartureCities">
                                                            <h6 class="dropdown-header">Departure</h6>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                               v-for="(city, index) in searchCities"
                                                               :class="flightRouteTrackerForm.arrival_city_name === city.city + ', ' + city.google_state_short_name ? 'disabled' : ''"
                                                               @click="selectCity('departure', city.google_state_short_name ? city.city + ', ' + city.google_state_short_name : city.city)">{{
                                                                    city.city
                                                                }} <span v-if="city.google_state_short_name">({{
                                                                        city.google_state_short_name
                                                                    }})</span></a>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                                        <label>Arrival</label>
                                                        <input type="text" class="form-control"
                                                               placeholder="Los Angeles, CA"
                                                               v-model="flightRouteTrackerForm.arrival_city_name"
                                                               @input="searchCity('arrival', flightRouteTrackerForm.arrival_city_name)">
                                                        <div class="dropdown-menu search-airports"
                                                             v-if="searchCities.length > 0 && showArrivalCities">
                                                            <h6 class="dropdown-header">Arrival</h6>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                               v-for="(city, index) in searchCities"
                                                               :class="flightRouteTrackerForm.departure_city_name === city.city + ', ' + city.google_state_short_name ? 'disabled' : ''"
                                                               @click="selectCity('arrival', city.google_state_short_name ? city.city + ', ' + city.google_state_short_name : city.city)">{{
                                                                    city.city
                                                                }} <span v-if="city.google_state_short_name">({{
                                                                        city.google_state_short_name
                                                                    }})</span></a>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="form-group col-12">
                                                        <small class="float-left"><span
                                                            class="text-danger">Note: </span>Only one is required
                                                            departure
                                                            or arrival.</small>
                                                        <button type="button" class="btn btn-warning float-right"
                                                                @click="trackByRoute()"><i
                                                            class="fa fa-search"></i> Track Flight
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="card border-light mb-3" v-if="flightRouteTrackDetail.length > 0"
                                                 style="font-size: 14px;">
                                                <div class="card-body">
                                                    <table class="table table-striped">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Flight#
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Airline
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                From
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                To
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Departure
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Arrival
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Status
                                                            </th>
                                                            <th scope="col" class="bg-transparent border-0">
                                                                Action
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="flight in flightRouteTrackDetail">
                                                            <td>{{ flight.flight.number }}</td>
                                                            <td style="width: 120px;">{{ flight.airline.name }}</td>
                                                            <td>
                                                                <p class="grid">
                                                                    {{ flight.departure_airport.name }},
                                                                    {{ flight.departure_airport.code }}
                                                                    <small>
                                                                        {{
                                                                            flight.departure_airport.city ? flight.departure_airport.city : ''
                                                                        }}{{
                                                                            flight.departure_airport.google_state_short_name ? ', ' + flight.departure_airport.google_state_short_name : ''
                                                                        }}
                                                                    </small>
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="grid">
                                                                    {{ flight.arrival_airport.name }},
                                                                    {{ flight.arrival_airport.code }}
                                                                    <small>
                                                                        {{
                                                                            flight.arrival_airport.city ? flight.arrival_airport.city : ''
                                                                        }}{{
                                                                            flight.arrival_airport.google_state_short_name ? ', ' + flight.arrival_airport.google_state_short_name : ''
                                                                        }}
                                                                    </small>
                                                                </p>
                                                            </td>
                                                            <td>{{
                                                                    new Date(flight.departure.scheduledTime) | dateFormat('hh:mm')
                                                                }}
                                                            </td>
                                                            <td>
                                                                {{
                                                                    new Date(flight.arrival.scheduledTime) | dateFormat('hh:mm')
                                                                }}
                                                                <i class="fa fa-arrow-right"></i>
                                                                {{
                                                                    new Date(flight.arrival.estimatedTime ? flight.arrival.estimatedTime : flight.arrival.scheduledTime) | dateFormat('hh:mm')
                                                                }}
                                                            </td>
                                                            <td>{{ flight.status }}</td>
                                                            <td>
                                                                <button
                                                                    class="btn btn-sm btn-outline-success"
                                                                    data-toggle="modal"
                                                                    data-target="#byRouteFlightDetailModal"
                                                                    @click="flightDetail = flight"><i
                                                                    class="fa fa-eye"></i></button>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="card border-light mb-3"
                                                 v-if="flightRouteTrackDetail.length === 0 && flightRouteTrackNotFound">
                                                <div class="card-body"><h6>No Flights Founds.</h6></div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-6 col-md-6 col-sm-4 w-50">
                    <label>
                        Show
                        <select class="form-control" v-model="query.length">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </label>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-8 w-50 text-right">
                    <label>
                        Search:
                        <input class="form-control" type="text" placeholder="Search..." v-model="query.search">
                    </label>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th v-for="column in columns" :key="column.name">
                                {{ column.label }}
                            </th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="airport in airports.data" :key="airport.id">
                            <!--              <td>{{ airport.id }}</td>-->
                            <td>{{ airport.name }}</td>
                            <td>{{ airport.city }}, {{
                                    airport.state ? airport.state : airport.google_state_long_name
                                }}
                            </td>
                            <td>{{ airport.code }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-dark" :href="showAirPort(airport)"><i
                                    class="fa fa-plane"></i> View
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 text-left" v-if="Object.keys(airports).length > 0">
                    Showing {{ airports.from }} to {{ airports.to }} of {{ airports.total }} entries
                </div>

                <div class="col-lg-6 d-none d-xl-block d-lg-block" v-if="Object.keys(airports).length > 0">
                    <pagination :data="airports" :limit="2" :show-disabled="true" :align="`right`"
                                @pagination-change-page="paginate">
                        <span slot="prev-nav">Previous</span>
                        <span slot="next-nav">Next</span>
                    </pagination>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 d-lg-none d-sm-block d-md-block"
                     v-if="Object.keys(airports).length > 0">
                    <pagination :data="airports" :limit="-1" :show-disabled="true" :align="`right`"
                                @pagination-change-page="paginate">
                        <span slot="prev-nav">Previous</span>
                        <span slot="next-nav">Next</span>
                    </pagination>
                </div>
            </div>

        </div>

        <!-- Detail Modal -->
        <div v-if="Object.keys(flightDetail).length > 0" class="modal fade" id="byRouteFlightDetailModal" tabindex="-1"
             role="dialog" aria-labelledby="byRouteFlightDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="byRouteFlightDetailModalLabel">({{ flightDetail.airline.iataCode }})
                            {{ flightDetail.airline.name }} {{ flightDetail.flight.number }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">From <i class="fa fa-plane-departure"></i></h5>
                                        <p class="card-text" style="display: grid;">
                                            {{ flightDetail.departure_airport.name }},
                                            {{ flightDetail.departure_airport.code }}
                                            <small>
                                                {{
                                                    flightDetail.departure_airport.city ? flightDetail.departure_airport.city : ''
                                                }}{{
                                                    flightDetail.departure_airport.google_state_short_name ? ', ' + flightDetail.departure_airport.google_state_short_name : ''
                                                }}
                                            </small>
                                            <strong>
                                                {{
                                                    new Date(flightDetail.departure.scheduledTime) | dateFormat('dddd DD-MMM-YYYY')
                                                }}
                                            </strong>
                                            <strong>
                                                {{
                                                    new Date(flightDetail.departure.estimatedTime) | dateFormat('HH:mmA')
                                                }}
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">To <i class="fa fa-plane-arrival"></i></h5>
                                        <p class="card-text" style="display: grid;">
                                            {{ flightDetail.arrival_airport.name }},
                                            {{ flightDetail.arrival_airport.code }}
                                            <small>
                                                {{
                                                    flightDetail.arrival_airport.city ? flightDetail.arrival_airport.city : ''
                                                }}{{
                                                    flightDetail.arrival_airport.google_state_short_name ? ', ' + flightDetail.arrival_airport.google_state_short_name : ''
                                                }}
                                            </small>
                                            <strong>
                                                {{
                                                    new Date(flightDetail.arrival.scheduledTime) | dateFormat('dddd DD-MMM-YYYY')
                                                }}
                                            </strong>
                                            <strong>
                                                {{
                                                    new Date(flightDetail.arrival.estimatedTime ? flightDetail.arrival.estimatedTime : flightDetail.arrival.scheduledTime) | dateFormat('HH:mmA')
                                                }}
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <table class="table table-borderless mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th colspan="2" scope="col" class="border-0">
                                            Departure Times
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Gate Departure</td>
                                        <td>Takeoff</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p :class="grid">
                                                {{ flightDetail.departure.gate ? flightDetail.departure.gate : '--' }}
                                                <small>Scheduled {{
                                                        new Date(flightDetail.departure.scheduledTime) | dateFormat('HH:mmA')
                                                    }}</small>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="grid">
                                                <strong>{{
                                                        new Date(flightDetail.departure.estimatedTime) | dateFormat('HH:mmA')
                                                    }}</strong>
                                                <small>Scheduled {{
                                                        new Date(flightDetail.departure.estimatedTime) | dateFormat('HH:mmA')
                                                    }}</small>
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table table-borderless mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th colspan="2" scope="col" class="border-0">
                                            Arrival Times
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Landing</td>
                                        <td>Gate Arrival</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="grid">
                                                <strong>{{
                                                        new Date(flightDetail.arrival.estimatedTime ? flightDetail.arrival.estimatedTime : flightDetail.arrival.scheduledTime) | dateFormat('HH:mmA')
                                                    }}</strong>
                                                <small>Scheduled {{
                                                        new Date(flightDetail.arrival.scheduledTime) | dateFormat('HH:mmA')
                                                    }}</small>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="grid">
                                                {{ flightDetail.arrival.gate ? flightDetail.arrival.gate : '--' }}
                                                <small>Scheduled {{
                                                        new Date(flightDetail.arrival.scheduledTime) | dateFormat('HH:mmA')
                                                    }}</small>
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Pagination from 'laravel-vue-pagination';
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';

export default {
    data() {
        return {
            airports: {},
            columns: [
                {label: 'Name', name: 'name'},
                {label: 'City, State', name: 'city'},
                {label: 'Code', name: 'code'},
            ],
            query: {
                search: '',
                length: 100
            },
            flightTrackerForm: {
                airline_name: this.getUrlParameter('airline_name') ? this.getUrlParameter('airline_name') : '',
                flight_number: this.getUrlParameter('flight_number') ? this.getUrlParameter('flight_number') : '',
                departure_date: this.getUrlParameter('departure_date') ? this.getUrlParameter('departure_date') : '',
                tab: 'byFlight'
            },
            flightTrackNotFound: false,
            flightTrackDetail: {},

            times: [
                {label: 'All day', value: 'all_day'},
                {label: 'Morning', value: 'morning'},
                {label: 'Afternoon', value: 'afternoon'},
                {label: 'Evening', value: 'evening'},
            ],
            flightRouteTrackerForm: {
                departure_city_name: this.getUrlParameter('departure_city_name') ? this.getUrlParameter('departure_city_name') : '',
                arrival_city_name: this.getUrlParameter('arrival_city_name') ? this.getUrlParameter('arrival_city_name') : '',
                time: this.getUrlParameter('time') ? this.getUrlParameter('time') : '',
                tab: 'byRoute'
            },
            flightRouteTrackNotFound: false,
            flightRouteTrackDetail: [],

            showDepartureCities: false,
            showArrivalCities: false,
            searchCities: [],
            searchAirlinesData: [],
            flightDetail: {}
        }
    },

    components: {
        Pagination,
        VueCtkDateTimePicker
    },
    mounted() {
        let _self = this;
        this.fetch();
        window.onpopstate = function () {
            if (this.getUrlParameter('tab') === null) {
                _self.flightTrackNotFound = false;
                _self.flightTrackDetail = {};
                _self.flightRouteTrackNotFound = false;
                _self.flightRouteTrackDetail = [];
            }


            if (this.getUrlParameter('tab') === 'byFlight') {
                _self.flightTrackNotFound = false;
                _self.flightTrackDetail = {};
                _self.flightTrackerForm = {
                    airline_name: this.getUrlParameter('airline_name'),
                    flight_number: this.getUrlParameter('flight_number'),
                    departure_date: this.getUrlParameter('departure_date')
                };

                if (_self.flightTrackerForm.airline_name !== null) {
                    _self.trackByFlight(true);
                }
            } else {
                _self.flightRouteTrackNotFound = false;
                _self.flightRouteTrackDetail = [];
                _self.flightRouteTrackerForm = {
                    departure_city_name: this.getUrlParameter('departure_city_name'),
                    arrival_city_name: this.getUrlParameter('arrival_city_name'),
                    time: this.getUrlParameter('time')
                };

                if (_self.flightRouteTrackerForm.departure_city_name !== null) {
                    _self.trackByRoute(true);
                }
            }
        };

        let submit = this.getUrlParameter('submit');

        if (submit) {
            setTimeout(function () {
                if (this.getUrlParameter('tab') === 'byFlight') {
                    _self.trackByFlight();
                } else {
                    _self.trackByRoute();
                }
            }, 300)
        }
    },

    methods: {
        showAirPort(airport) {
            return route('airports.code', {code: airport.code});
        },
        /**
         * Fetch airports
         */
        async fetch() {
            let _self = this;
            _self.$root.startLoading();
            await axios.get(route('api.airports.index'), {'params': this.query}).then(response => {
                if (response.data.status) {
                    _self.airports = response.data.data;
                }
                _self.$root.stopLoading();
            }).catch(error => {
                console.log(error);
                _self.$root.stopLoading();
            });
        },
        /**
         * Get Pages Data.
         */
        paginate(page) {
            this.$root.startLoading();

            if (typeof page === 'undefined') {
                page = 1;
            }

            this.query.page = page;
            this.$root.isLoading = true;
            let _self = this;
            axios.get(route('api.airports.index'), {'params': this.query}).then(response => {
                if (response.data.status) {
                    _self.airports = response.data.data;
                }

                _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();
                console.log(error);
            });
        },

        /**
         * Track by Flight for searching.
         */
        trackByFlight(push_state_history) {
            this.$root.startLoading();

            if (typeof push_state_history === 'undefined') {
                this.flightTrackerForm.submit = true;
                window.history.pushState(null, null, '?' + $.param(this.flightTrackerForm));
            }

            this.flightTrackNotFound = false;
            let _self = this;
            axios.post(route('api.tracker.flight'), this.flightTrackerForm).then(response => {
                if (response.data.status) {
                    if (response.data.data.length > 0) {
                        _self.flightTrackDetail = response.data.data[0];
                    }
                }

                _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();

                _self.flightTrackDetail = {};
                _self.flightTrackNotFound = true;

                console.log(error);
            });
        },

        /**
         * Track by Route for searching.
         */
        trackByRoute(push_state_history) {
            // this.$nuxt.$loading.start();

            if (typeof push_state_history === 'undefined') {
                this.flightRouteTrackerForm.submit = true;
                window.history.pushState(null, null, '?' + $.param(this.flightRouteTrackerForm));
            }

            this.flightRouteTrackNotFound = false;
            this.$root.startLoading();
            let _self = this;
            axios.post(route('api.tracker.route'), this.flightRouteTrackerForm).then(response => {
                if (response.data.status) {
                    _self.flightRouteTrackDetail = response.data.data;
                }

                _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();

                _self.flightRouteTrackDetail = [];
                _self.flightRouteTrackNotFound = true;

                console.log(error);
            });
        },

        /**
         * Search City form database.
         */
        searchCity(type, city) {
            if (city !== '' && city.length >= 3) {
                this.search(this, type, city);
            }
        },

        /**
         * Debounce Search
         */
        search: _.debounce((_self, type, city) => {
             _self.$root.startLoading();

            _self.showDepartureCities = false;
            _self.showArrivalCities = false;
            _self.searchCities = [];

            axios.post(route('api.tracker.city'), {'city': city}).then(response => {
                if (response.data.status) {
                    _self.searchCities = response.data.data;

                    if (type === 'departure') {
                        _self.showDepartureCities = true;
                    } else {
                        _self.showArrivalCities = true;
                    }
                }

                 _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();
                console.log(error);
            });
        }, 300),

        /**
         * City Name Auto Fill.
         */
        selectCity(type, city) {
            if (type === 'departure') {
                this.flightRouteTrackerForm.departure_city_name = city;
            } else {
                this.flightRouteTrackerForm.arrival_city_name = city;
            }

            this.searchCities = [];
        },

        /**
         * Search Airline form database.
         */
        searchAirline(airline) {
            // if (airline !== '' && airline.length >= 3) {
            this.searchAirlines(this, airline);
            // }
        },

        /**
         * Debounce Search
         */
        searchAirlines: _.debounce((_self, airline) => {
             _self.$root.startLoading();

            _self.searchAirlinesData = [];

            axios.post(route('api.tracker.ariline'), {'airline': airline}).then(response => {
                if (response.data.status) {
                    _self.searchAirlinesData = response.data.data;
                }
                 _self.$root.stopLoading();
            }).catch(error => {
                _self.$root.stopLoading();
                console.log(error);
            });
        }, 300),

        /**
         * Airline Name Auto Fill.
         */
        selectAirline(type, airline) {
            if (type === 'flight') {
                this.flightTrackerForm.airline_name = airline;
            } else {
                this.flightRouteTrackerForm.airline = airline;
            }

            this.searchAirlinesData = [];
        },

        /**
         * Get Active Tab.
         */
        activeTab() {
            return this.getUrlParameter('tab') ? this.getUrlParameter('tab') : 'byFlight';
        },
    },

    watch: {
        /**
         * Data show's limit change.
         */
        'query.length': function (newValue, oldValue) {
            if (newValue !== oldValue) {
                this.paginate();
            }
        },

        /**
         * Search airport.
         */
        'query.search': function (newValue, oldValue) {
            this.paginate();
        }
    },

    filters: {
        /**
         * Read More
         * @param text
         * @param length
         * @param suffix
         * @returns {string|boolean}
         */
        readMore: function (text, length, suffix) {
            if (!text) return false;
            return text.length > length ? text.substring(0, length) + suffix : text;
        }
    }
}
</script>

<style scoped>
/*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
p.grid {
    display: grid;
    margin-bottom: 0;
}

@media only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px) {

    /* Force table to not be like tables anymore */
    table, thead, tbody, th, td, tr {
        display: block;
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    tr {
        border: 1px solid #ccc;
    }

    td, th {
        padding: 6px;
        text-align: left;
    }

    td {
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee;
        position: relative;
        padding-left: 50%;
    }

    td:before {
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%;
        padding-right: 10px;
        white-space: nowrap;
    }

    /*
    Label the data
    */
    td:nth-of-type(1):before {
        content: "Name";
        font-weight: bold;
    }

    td:nth-of-type(2):before {
        content: "City, State";
        font-weight: bold;
    }

    td:nth-of-type(3):before {
        content: "Code";
        font-weight: bold;
    }

    td:nth-of-type(4):before {
        content: "Action";
        font-weight: bold;
    }
}

.airports-yellow-badge {
    display: inline-block;
    font-size: 1.2em;
    font-weight: bold;
    color: #000;
    line-height: 1.2 !important;
    padding-left: 10px;
    background-size: cover !important;
    background: url('/assets/images/yellow-swoosh.gif');
    background-repeat: no-repeat !important;
    background-position: right top !important;
    width: 200px;
    padding-bottom: 3px !important;
}

.search-airports {
    position: initial;
    display: block;
    width: 100%;
    overflow-y: scroll;
    height: 150px;
    max-height: 150px;
}

.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    border-bottom: 2px solid #67a6eb !important;
}
</style>
