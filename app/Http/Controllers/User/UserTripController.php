<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\subscribeToTripRequest;
use App\Http\Responses\Response;
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
            return Response::NotAuthenticated();
        }

        $area = Area::find($area_id);
        if (!$area) {
            return Response::AreaNotFound();
        }

        $areaTrips = TripAreas::where('area_id', $area_id)
            ->with('trip')
            ->get();

        if ($areaTrips->isEmpty()) {
            return Response::Message404('No trips found for this area');
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
            return Response::NotAuthenticated();
        }

        $trip_id=$request->trip_id;

        $trip = Trips::find($trip_id);
        if (!$trip) {
            return Response::TripNotFound();
        }

        if ($trip->Completed) {
            return Response::Message400('This trip is already completed!');
        }

        $currentDate = now();
        if ($currentDate->lt($trip->RegistrationStartDate) || $currentDate->gt($trip->RegistrationEndDate)) {
            return Response::Message400('Registration is closed for this trip!');
        }

        $existingSubscription = Subscriptions::where('trip_id', $trip_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSubscription) {
            return Response::Message400('You are already subscribed to this trip!');
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
            return Response::NotAuthenticated();
        }

        $subscription = Subscriptions::where('trip_id', $trip_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$subscription) {
            return Response::SubscriptionNotFound();
        }

        if ($subscription->GoStatus == 'confirmed') {
            return Response::Message400('You cannot cancel a confirmed subscription!');
        }

        $subscription->delete();

        $trip = Trips::find($trip_id);
        $trip->Pending -= 1;
        $trip->save();

        return Response::Message200('Subscription canceled successfully!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Get All User Subscriptions :
    public function getUserSubscriptions($Status = null)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
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
