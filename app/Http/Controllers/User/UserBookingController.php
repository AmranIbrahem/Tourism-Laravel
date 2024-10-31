<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\createBookingRequest;
use App\Http\Responses\Response;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use App\Models\Guides\PrivateGuideBooking;
use App\Models\Guides\TimeSlots;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Create Booking :
    public function createBooking(createBookingRequest $request, $guide_id)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
        }

        $guide = Guides::find($guide_id);

        if (!$guide) {
            return Response::GuideNotFound();
        }

        $availability = GuideAvailability::where('guide_id', $guide_id)
            ->where('availableDate', $request->bookingDate)
            ->where('isAvailable', true)
            ->first();

        if (!$availability) {
            return Response::Message400("Guide not available on this date!");
        }

        $timeSlot = TimeSlots::where('guide_availabilities_id', $availability->id)
            ->where('startTime', '<=', $request->startDate)
            ->where('endTime', '>=', $request->endDate)
            ->first();

        if (!$timeSlot) {
            return Response::Message400("Guide not available during the selected time!");
        }

        $startDateTime = new \DateTime($request->startDate);
        $endDateTime = new \DateTime($request->endDate);
        $interval = $startDateTime->diff($endDateTime);

        $totalMinutes = ($interval->h * 60) + $interval->i;

        $costPerMinute = $guide->CostPerHour / 60;

        $totalCost = $totalMinutes * $costPerMinute;

        if ($totalMinutes <= 0) {
            return Response::Message400("Invalid booking duration!");
        }

        $booking = PrivateGuideBooking::create([
            'user_id' => $user->id,
            'guide_id' => $guide_id,
            'bookingDate' => $request->bookingDate,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'totalCost' => $totalCost,
            'bookingStatus' => 'pending',
        ]);

        return response()->json([
            'message' => 'Booking created successfully!',
            'booking' => $booking
        ], 201);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// show All User Bookings:
    public function showAllUserBookings()
    {
        $user = Auth::user();

        if (!$user) {
            return Response::NotAuthenticated();
        }

        $bookings = PrivateGuideBooking::where('user_id', $user->id)
            ->with('guide')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message404("No booking found");
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'guideName' => $booking->guide->FirstName . ' ' . $booking->guide->LastName,
                'bookingDate' => $booking->bookingDate,
                'startDate' => $booking->startDate,
                'endDate' => $booking->endDate,
                'totalCost' => $booking->totalCost,
                'bookingStatus' => $booking->bookingStatus
            ];
        });

        return response()->json([
            'message' => 'Bookings retrieved successfully!',
            'bookings' => $formattedBookings
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// show user Bookings By Status:
    public function showUserBookingsByStatus($status)
    {
        $user = Auth::user();

        if (!$user) {
            return Response::NotAuthenticated();
        }

        if (!in_array($status, ['pending', 'confirmed'])) {
            return Response::Message400("Invalid booking status!");
        }

        $bookings = PrivateGuideBooking::where('user_id', $user->id)
            ->where('bookingStatus', $status)
            ->with('guide')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message404("No bookings found with the selected status!");
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'guideName' => $booking->guide->FirstName . ' ' . $booking->guide->LastName,
                'bookingDate' => $booking->bookingDate,
                'startDate' => $booking->startDate,
                'endDate' => $booking->endDate,
                'totalCost' => $booking->totalCost,
                'bookingStatus' => $booking->bookingStatus
            ];
        });

        return response()->json([
            'message' => 'Bookings with status "' . $status . '" retrieved successfully!',
            'bookings' => $formattedBookings
        ], 200);
    }



}
