<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guide\addTimeSlotRequest;
use App\Http\Requests\Guide\editTimeSlotRequest;
use App\Http\Requests\Guide\showAvailableTimeSlotsRequest;
use App\Http\Responses\Response;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use App\Models\Guides\TimeSlots;
use Illuminate\Support\Facades\Auth;

class GuideTimeSlotsController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Time Slot :
    public function addTimeSlot(addTimeSlotRequest $request , $guide_availabilities_id)
    {
        $guide = Guides::where('user_id', Auth::id())->first();

        if (!$guide) {
            return Response::GuideNotFound();
        }

        $availability = GuideAvailability::where('id', $guide_availabilities_id)
            ->where('guide_id', $guide->id)
            ->first();

        if (!$availability) {
            return Response::AvailabilityNotFound();
        }

        if (strtotime($request->startTime) >= strtotime($request->endTime)) {
            return Response::Message400("Start time must be before end time");
        }

        $timeSlot = TimeSlots::create([
            'guide_availabilities_id' => $guide_availabilities_id,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime
        ]);

        if ($timeSlot) {
            return response()->json([
                'message' => 'Time slot added successfully!',
                'data' => $timeSlot
            ], 201);
        } else {
            return Response::SomethingIsWrong();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Edit Time Slot :
    public function editTimeSlot(editTimeSlotRequest $request, $time_slot_id)
    {
        $timeSlot = TimeSlots::find($time_slot_id);

        if (!$timeSlot) {
            return Response::TimeSlotNotFound();
        }

        $guideAvailability = GuideAvailability::find($timeSlot->guide_availabilities_id);

        if ($guideAvailability->guide->user_id !== Auth::id()) {
            return Response::NotAuthenticated();
        }

        if ($request->has('startTime')) {
            $timeSlot->startTime = $request->startTime;
        }

        if ($request->has('endTime')) {
            $timeSlot->endTime = $request->endTime;
        }

        if ($timeSlot->save()) {
            return response()->json([
                'message' => 'TimeSlot updated successfully!',
                'data' => $timeSlot
            ], 200);
        } else {
            return Response::SomethingIsWrong();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Time Slot :
    public function deleteTimeSlot($time_slot_id)
    {
        $timeSlot = TimeSlots::find($time_slot_id);

        if (!$timeSlot) {
            return Response::TimeSlotNotFound();
        }

        $guideAvailability = GuideAvailability::find($timeSlot->guide_availabilities_id);

        if ($guideAvailability && $guideAvailability->guide->user_id !== Auth::id()) {
            return Response::NotAuthenticated();
        }

        if ($timeSlot->delete()) {
            return Response::Message200("TimeSlot deleted successfully!");
        } else {
            return Response::SomethingIsWrong();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Time Slot :
    public function showAvailableTimeSlots($date)
    {
        $availabilities = GuideAvailability::where('availableDate', $date)
            ->where('isAvailable', true)
            ->with('timeSlots')
            ->get();

        if ($availabilities->isEmpty()) {
            return Response::AreaNotFound();
        }

        $data = [];
        foreach ($availabilities as $availability) {
            $data[] = [
                'guide_id' => $availability->guide_id,
                'availableDate' => $availability->availableDate,
                'CostPerHour' => $availability->CostPerHour,
                'timeSlots' => $availability->timeSlots->map(function ($timeSlot) {
                    return [
                        'startTime' => $timeSlot->startTime,
                        'endTime' => $timeSlot->endTime,
                    ];
                }),
            ];
        }

        return response()->json([
            'message' => 'Available time slots retrieved successfully!',
            'data' => $data,
        ], 200);
    }


}
