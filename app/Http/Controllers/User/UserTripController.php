<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\subscribeToTripRequest;
use App\Models\Admin\TripAreas;
use App\Models\Admin\Trips;
use App\Models\Places\Area;
use App\Models\User\Subscriptions;
use Illuminate\Support\Facades\Auth;

class UserTripController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Trip By Area :
    public function ShowTripByArea($area_id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $area = Area::find($area_id);
        if (!$area) {
            return response()->json(['message' => 'Area Not Found'], 404);
        }

        $areaTrips = TripAreas::where('area_id', $area_id)
            ->with('trip')
            ->get();

        if ($areaTrips->isEmpty()) {
            return response()->json(['message' => 'No trips found for this area'], 404);
        }

        return response()->json([
            'message' => 'Trips found for the specified area',
            'trips' => $areaTrips->map(function ($areaTrip) {
                return [
                    'trip_id' => $areaTrip->trip->id,
                    'AreaName' => $areaTrip->trip->AreaName,
                    'NumberOfPeople' => $areaTrip->trip->NumberOfPeople,
                    'Cost' => $areaTrip->trip->Cost,
                    'TripDetails' => $areaTrip->trip->TripDetails,
                    'TripHistory' => $areaTrip->trip->TripHistory,
                    'RegistrationStartDate' => $areaTrip->trip->RegistrationStartDate,
                    'RegistrationEndDate' => $areaTrip->trip->RegistrationEndDate,

                ];
            })
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///  Add Subscription :
    public function subscribeToTrip(subscribeToTripRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $trip_id=$request->trip_id;

        $trip = Trips::find($trip_id);
        if (!$trip) {
            return response()->json(['message' => 'Trip not found!'], 404);
        }

        if ($trip->Completed) {
            return response()->json(['message' => 'This trip is already completed!'], 400);
        }

        $currentDate = now();
        if ($currentDate->lt($trip->RegistrationStartDate) || $currentDate->gt($trip->RegistrationEndDate)) {
            return response()->json(['message' => 'Registration is closed for this trip!'], 400);
        }

        $existingSubscription = Subscriptions::where('trip_id', $trip_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubscription) {
            return response()->json(['message' => 'You are already subscribed to this trip!'], 400);
        }

        $subscription = Subscriptions::create([
            'trip_id' => $trip_id,
            'user_id' => $user->id,
            'GoStatus' => 'in_progress',
        ]);

        $trip->Pending += 1;
        $trip->save();

        return response()->json(['message' => 'Subscription added successfully!', 'subscription' => $subscription], 201);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Subscription :
    public function cancelSubscription( $trip_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $subscription = Subscriptions::where('trip_id', $trip_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found!'], 404);
        }

        if ($subscription->GoStatus == 'confirmed') {
            return response()->json(['message' => 'You cannot cancel a confirmed subscription!'], 400);
        }

        $subscription->delete();

        $trip = Trips::find($trip_id);
        $trip->Pending -= 1;
        $trip->save();

        return response()->json(['message' => 'Subscription canceled successfully!'], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get All User Subscriptions :
    public function getUserSubscriptions($Status = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        if ($Status) {
            $subscriptions = $user->subscriptions()
                ->whereIn('GoStatus', $Status)
                ->with('trip')
                ->get();
        } else {
            $subscriptions = $user->subscriptions()
                ->whereIn('GoStatus', ['in_progress', 'completed', 'canceled'])
                ->with('trip')
                ->get();
        }

        return response()->json([
            'message' => 'User subscriptions retrieved successfully!',
            'subscriptions' => $subscriptions->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'trip_id' => $subscription->trip_id,
                    'GoStatus' => $subscription->GoStatus,
                    'trip_details' => $subscription->trip->TripDetails ?? null,
                    'trip_area' => $subscription->trip->AreaName ?? null,
                ];
            })
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get All User Subscriptions In Progress :
    public function getUserSubscriptionsInProgress()
    {
        return $this->getUserSubscriptions(['in_progress']);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get All User Subscriptions  completed :
    public function getUserSubscriptionsCompleted()
    {
        return $this->getUserSubscriptions(['completed']);
    }


}
