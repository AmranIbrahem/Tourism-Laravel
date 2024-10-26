<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use App\Models\Guides\PrivateGuideBooking;
use App\Models\Guides\TimeSlots;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuideBookingController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// show All Bookings :
    public function showAllBookings()
    {
        $user = Auth::user();
        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide not found!", 404);
        }

        $bookings = PrivateGuideBooking::where('guide_id', $guide->id)
            ->where('bookingStatus', 'pending')
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message("No pending bookings found!", 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'userName' => $booking->user->FirstName . ' ' . $booking->user->LastName,
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
    /// showB ookings By Status :
    public function showBookingsByStatus($status)
    {
        $user = Auth::user();
        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide not found!", 404);
        }

        if (!in_array($status, ['confirmed', 'canceled'])) {
            return Response::Message("Invalid booking status!", 400);
        }

        $bookings = PrivateGuideBooking::where('guide_id', $guide->id)
            ->where('bookingStatus', $status)
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message("No bookings found with the selected status!", 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'userName' => $booking->user->FirstName . ' ' . $booking->user->LastName,
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
    /// show Pending Bookings By Date :
    public function showPendingBookingsByDate($date)
    {
        $user = Auth::user();
        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide not found!", 404);
        }

        $bookings = PrivateGuideBooking::where('guide_id', $guide->id)
            ->whereDate('bookingDate', $date)
            ->where('bookingStatus', 'pending')
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message("No pending bookings found on this date!", 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'userName' => $booking->user->FirstName . ' ' . $booking->user->LastName,
                'bookingDate' => $booking->bookingDate,
                'startDate' => $booking->startDate,
                'endDate' => $booking->endDate,
                'totalCost' => $booking->totalCost,
                'bookingStatus' => $booking->bookingStatus
            ];
        });

        return response()->json([
            'message' => 'Pending bookings retrieved successfully for the selected date!',
            'bookings' => $formattedBookings
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// show BookingsBy Status And Date :
    public function showBookingsByStatusAndDate( $date , $status)
    {
        $user = Auth::user();
        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide not found!", 404);
        }

        if (!in_array($status, ['confirmed', 'canceled'])) {
            return Response::Message("Invalid booking status!", 400);
        }

        $bookings = PrivateGuideBooking::where('guide_id', $guide->id)
            ->whereDate('bookingDate', $date)
            ->where('bookingStatus', $status)
            ->with('user')
            ->get();

        if ($bookings->isEmpty()) {
            return Response::Message("No bookings found with the selected status on this date!", 404);
        }

        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'idBooking' => $booking->id,
                'userName' => $booking->user->FirstName . ' ' . $booking->user->LastName,
                'bookingDate' => $booking->bookingDate,
                'startDate' => $booking->startDate,
                'endDate' => $booking->endDate,
                'totalCost' => $booking->totalCost,
                'bookingStatus' => $booking->bookingStatus
            ];
        });

        return response()->json([
            'message' => 'Bookings retrieved successfully for the selected date and status!',
            'bookings' => $formattedBookings
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// approve Booking :
    public function approveBooking(Request $request)
    {
        $user = Auth::user();
        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide not found!", 404);
        }

        $booking_id = $request->booking_id;
        $booking = PrivateGuideBooking::find($booking_id);

        if (!$booking || $booking->guide_id != $guide->id) {
            return Response::Message("Guide or booking not found!", 404);
        }

        if ($booking->bookingStatus !== 'pending') {
            return Response::Message("Booking is not pending!", 400);
        }

        $availability = GuideAvailability::where('guide_id', $guide->id)
            ->where('availableDate', $booking->bookingDate)
            ->first();

        if (!$availability) {
            return Response::Message("No availability for this guide on the booking date!", 400);
        }

        $timeSlot = TimeSlots::where('guide_availabilities_id', $availability->id)
            ->where('startTime', '<=', $booking->startDate)
            ->where('endTime', '>=', $booking->endDate)
            ->first();

        if (!$timeSlot) {
            return Response::Message("No matching time slot for the booking!", 400);
        }

        DB::transaction(function () use ($booking, $timeSlot, $availability) {
            $startTime = new \DateTime($timeSlot->startTime);
            $endTime = new \DateTime($timeSlot->endTime);
            $bookingStartTime = new \DateTime($booking->startDate);
            $bookingEndTime = new \DateTime($booking->endDate);

            PrivateGuideBooking::where('guide_id', $booking->guide_id)
                ->where('bookingDate', $booking->bookingDate)
                ->where('bookingStatus', 'pending')
                ->where(function ($query) use ($bookingStartTime, $bookingEndTime) {
                    $query->where(function ($q) use ($bookingStartTime, $bookingEndTime) {
                        $q->where('startDate', '>=', $bookingStartTime->format('H:i:s'))
                            ->where('startDate', '<=', $bookingEndTime->format('H:i:s'));
                    })
                        ->orWhere(function ($q) use ($bookingStartTime, $bookingEndTime) {
                            $q->where('endDate', '>=', $bookingStartTime->format('H:i:s'))
                                ->where('endDate', '<=', $bookingEndTime->format('H:i:s'));
                        });
                })
                ->update(['bookingStatus' => 'canceled']);

            if ($startTime < $bookingStartTime) {
                TimeSlots::create([
                    'guide_availabilities_id' => $availability->id,
                    'startTime' => $startTime->format('H:i:s'),
                    'endTime' => $bookingStartTime->format('H:i:s'),
                ]);
            }

            if ($endTime > $bookingEndTime) {
                TimeSlots::create([
                    'guide_availabilities_id' => $availability->id,
                    'startTime' => $bookingEndTime->format('H:i:s'),
                    'endTime' => $endTime->format('H:i:s'),
                ]);
            }

            $timeSlot->delete();
            $booking->update(['bookingStatus' => 'confirmed']);
        });
        return Response::Message("Booking approved and availability updated!", 200);
    }


}
