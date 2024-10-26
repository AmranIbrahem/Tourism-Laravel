<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\createBookingRequest;
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
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $guide = Guides::find($guide_id);

        if (!$guide) {
            return response()->json(['message' => 'Guide not found!'], 404);
        }

        $availability = GuideAvailability::where('guide_id', $guide_id)
            ->where('availableDate', $request->bookingDate)
            ->where('isAvailable', true)
            ->first();

        if (!$availability) {
            return response()->json(['message' => 'Guide not available on this date!'], 400);
        }

        $timeSlot = TimeSlots::where('guide_availabilities_id', $availability->id)
            ->where('startTime', '<=', $request->startDate)
            ->where('endTime', '>=', $request->endDate)
            ->first();

        if (!$timeSlot) {
            return response()->json(['message' => 'Guide not available during the selected time!'], 400);
        }

        $startDateTime = new \DateTime($request->startDate);
        $endDateTime = new \DateTime($request->endDate);
        $interval = $startDateTime->diff($endDateTime);

        $totalMinutes = ($interval->h * 60) + $interval->i;

        $costPerMinute = $guide->CostPerHour / 60;

        $totalCost = $totalMinutes * $costPerMinute;

        if ($totalMinutes <= 0) {
            return response()->json(['message' => 'Invalid booking duration!'], 400);
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
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $bookings = PrivateGuideBooking::where('user_id', $user->id)
            ->with('guide')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found!'], 404);
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
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        if (!in_array($status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Invalid booking status!'], 400);
        }

        $bookings = PrivateGuideBooking::where('user_id', $user->id)
            ->where('bookingStatus', $status)
            ->with('guide')
            ->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found with the selected status!'], 404);
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
