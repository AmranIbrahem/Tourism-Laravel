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
            return Response::NotAuthenticated();
        }

        $trip = Trips::find($trip_id);

        if (!$trip) {
            return Response::TripNotFound();
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
            return Response::NotAuthenticated();
        }

        $subscription = Subscriptions::find($subscription_id);

        if (!$subscription) {
            return Response::SubscriptionNotFound();
        }

        $trip = Trips::find($subscription->trip_id);

        if (!$trip) {
            return Response::TripNotFound();
        }

        if ($subscription->GoStatus == 'completed') {
            return Response::Message400("This request has already been approved");
        }

        if ($trip->Completed) {
            return Response::Message400("Cannot approve request as the trip is already completed!");
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
        return Response::Message200("Request approved successfully!");
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
