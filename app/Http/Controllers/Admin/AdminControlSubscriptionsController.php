<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Admin\Trips;
use App\Models\Places\Area;
use App\Models\User\Subscriptions;
use Illuminate\Support\Facades\Auth;

class AdminControlSubscriptionsController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View All Requests For Trip  :
    public function viewTripRequests($trip_id, $Status)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::Message("User not authenticated!", 401);
        }

        $trip = Trips::find($trip_id);

        if (!$trip) {
            return Response::Message("Trip not found!", 404);
        }

        $requests = Subscriptions::with('user:id,FirstName,LastName')
        ->where('trip_id', $trip_id)
            ->where('GoStatus', $Status)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($request) {
                $request->user_full_name = $request->user->FirstName . ' ' . $request->user->LastName;
                unset($request->user);
                return $request;
            });

        return response()->json(['requests' => $requests], 200);
    }



    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View All Requests (InProgress) For Trip  :
    public function viewTripRequestsInProgress($trip_id){
        return  $this->viewTripRequests($trip_id,'in_progress');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View All Requests (Completed) For Trip  :
    public function viewTripRequestsInCompleted($trip_id){
        return  $this->viewTripRequests($trip_id,'completed');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Approve Request To Subscription For Trip :
    public function approveRequest($subscription_id)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::Message("User not authenticated!", 401);
        }

        $subscription = Subscriptions::find($subscription_id);

        if (!$subscription) {
            return Response::Message("Subscription not found!", 404);
        }

        $trip = Trips::find($subscription->trip_id);

        if (!$trip) {
            return Response::Message("Trip not found!", 404);
        }

        if ($subscription->GoStatus == 'completed') {
            return Response::Message("This request has already been approved", 400);
        }

        if ($trip->Completed) {
            return Response::Message("Cannot approve request as the trip is already completed!", 400);
        }

        $subscription->GoStatus = 'completed';
        $subscription->save();

        $trip->Confirmed += 1;
        if ($trip->Pending > 0) {
            $trip->Pending -= 1;
        }
        $trip->save();

        $this->checkAndMarkTripCompletion($trip);

        if ($trip->Completed) {
            $this->decrementAreaTripCount($trip);
        }
        return Response::Message("Request approved successfully!", 200);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function checkAndMarkTripCompletion($trip)
    {
        if ($trip->Confirmed >= $trip->NumberOfPeople) {
            $trip->Completed = true;
            $trip->save();

            Subscriptions::where('trip_id', $trip->id)
                ->where('GoStatus', 'in_progress')
                ->update(['GoStatus' => 'canceled']);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    public function decrementAreaTripCount($trip)
    {
        $areas = Area::whereHas('trips', function($query) use ($trip) {
            $query->where('trips.id', $trip->id);
        })->get();

        foreach ($areas as $area) {
            if ($area->NumberExistingTrips > 0) {
                $area->NumberExistingTrips -= 1;
                $area->save();
            }
        }
    }



}
