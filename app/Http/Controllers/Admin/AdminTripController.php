<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\addTripRequest;
use App\Http\Requests\Admin\updateTripRequest;
use App\Http\Responses\Response;
use App\Models\Admin\TripAreas;
use App\Models\Admin\Trips;
use App\Models\Places\Area;
use Illuminate\Support\Facades\Auth;

class AdminTripController extends Controller
{

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Trip :
    public function addTrip(addTripRequest $request)
    {
        $user = Auth::user();
        if (!$user || $user->Role !== 'admin') {
            return Response::NotAuthenticated();
        }

        $AreaNames = $request->input('AreaNames');

        $trip = Trips::create([
            'AreaName' => implode(', ', $AreaNames),
            'NumberOfPeople' => $request->input('NumberOfPeople'),
            'Cost' => $request->input('Cost'),
            'TripDetails' => $request->input('TripDetails'),
            'TripHistory' => $request->input('TripHistory'),
            'RegistrationStartDate' => $request->input('RegistrationStartDate'),
            'RegistrationEndDate' => $request->input('RegistrationEndDate'),
        ]);

        foreach ($AreaNames as $AreaName) {
            $area = Area::where('AreaName', $AreaName)->first();

            if ($area) {
                $this->addTripAreaToTrip($area->id, $trip->id);
                $this->incrementAreaTripCount($area->id);
            }
        }

        return response()->json(['message' => 'Trip and areas association added successfully!', 'trip' => $trip], 201);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Edit Trip :

    public function updateTrip(updateTripRequest $request, $trip_id)
    {
        $user = Auth::user();
        if (!$user || $user->Role !== 'admin') {
            return Response::NotAuthenticated();
        }

        $trip = Trips::find($trip_id);
        if (!$trip) {
            return Response::TripNotFound();
        }

        if ($request->has('NumberOfPeople')) {
            $trip->NumberOfPeople = $request->input('NumberOfPeople');
        }

        if ($request->has('Cost')) {
            $trip->Cost = $request->input('Cost');
        }

        if ($request->has('TripDetails')) {
            $trip->TripDetails = $request->input('TripDetails');
        }

        if ($request->has('TripHistory')) {
            $trip->TripHistory = $request->input('TripHistory');
        }

        if ($request->has('RegistrationStartDate')) {
            $trip->RegistrationStartDate = $request->input('RegistrationStartDate');
        }

        if ($request->has('RegistrationEndDate')) {
            $trip->RegistrationEndDate = $request->input('RegistrationEndDate');
        }

        $oldAreaNames = TripAreas::where('trip_id', $trip_id)
            ->join('areas', 'trip_areas.area_id', '=', 'areas.id')
            ->pluck('areas.AreaName')
            ->toArray();

        if ($request->has('AreaNames')) {
            $newAreaNames = $request->input('AreaNames');
            $trip->AreaName = implode(', ', $newAreaNames);

            TripAreas::where('trip_id', $trip_id)->delete();

            $removedAreas = array_diff($oldAreaNames, $newAreaNames);
            foreach ($removedAreas as $removedAreaName) {
                $removedArea = Area::where('AreaName', $removedAreaName)->first();
                if ($removedArea) {
                    $this->decrementAreaTripCount($removedArea->id);
                }
            }

            foreach ($newAreaNames as $AreaName) {
                $area = Area::where('AreaName', $AreaName)->first();
                if ($area) {
                    $this->addTripAreaToTrip($area->id, $trip->id);

                    if (!in_array($AreaName, $oldAreaNames)) {
                        $this->incrementAreaTripCount($area->id);
                    }
                }
            }
        }

        $trip->save();

        return response()->json(['message' => 'Trip updated successfully!', 'trip' => $trip], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Trip  :
    public function deleteTrip($trip_id)
    {
        $user = Auth::user();
        if (!$user || $user->Role !== 'admin') {
            return Response::NotAuthenticated();
        }

        $trip = Trips::find($trip_id);
        if (!$trip) {
            return Response::TripNotFound();
        }

        if ($trip->GoStatus !== 'completed') {
            $tripAreas = TripAreas::where('trip_id', $trip_id)->get();

            foreach ($tripAreas as $tripArea) {
                $this->decrementAreaTripCount($tripArea->area_id);
            }
        }

        if ($trip->delete()) {
            return Response::Message200("Trip deleted successfully!");
        } else {
            return Response::Message("Failed to delete Trip", 500);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Trip Area To Trip :
    public function addTripAreaToTrip($area_id, $trip_id)
    {
        TripAreas::create([
            'area_id' => $area_id,
            'trip_id' => $trip_id,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Increment Area Trip Count :
    public function incrementAreaTripCount($area_id)
    {
        $area = Area::find($area_id);

        if ($area) {
            $area->NumberExistingTrips += 1;
            $area->save();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// decrement Area Trip Count :
    public function decrementAreaTripCount($area_id)
    {
        $area = Area::find($area_id);
        if ($area && $area->NumberExistingTrips > 0) {
            $area->NumberExistingTrips -= 1;
            $area->save();
        }
    }

}
