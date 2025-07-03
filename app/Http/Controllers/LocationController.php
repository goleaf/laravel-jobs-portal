<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\JsonResponse;

class LocationController extends AppBaseController
{
    /**
     * Get countries list.
     */
    public function getCountries(): JsonResponse
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return $this->sendResponse($countries, 'Countries retrieved successfully.');
    }

    /**
     * Get states list based on country.
     */
    public function getStates(GetStatesLocationRequest $request): JsonResponse
    {
        $countryId = $request->get('country_id');

        if (! $countryId) {
            return $this->sendError('Country ID is required', 400);
        }

        $states = State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->sendResponse($states, 'States retrieved successfully.');
    }

    /**
     * Get cities list based on state.
     */
    public function getCities(GetCitiesLocationRequest $request): JsonResponse
    {
        $stateId = $request->get('state_id');

        if (! $stateId) {
            return $this->sendError('State ID is required', 400);
        }

        $cities = City::where('state_id', $stateId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return $this->sendResponse($cities, 'Cities retrieved successfully.');
    }
}
