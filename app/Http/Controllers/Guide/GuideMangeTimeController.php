<?php

namespace App\Http\Controllers\Guide;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guide\addGuideAvailabilityRequest;
use App\Http\Requests\Guide\EditGuideAvailabilityRequest;
use App\Http\Responses\Response;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use Illuminate\Support\Facades\Auth;

class GuideMangeTimeController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Guide Availability :
    public function addGuideAvailability(addGuideAvailabilityRequest $request)
    {
        $guide = Guides::where('user_id', Auth::id())->first();

        if (!$guide) {
            return Response::Message("Guide Not Found", 404);
        }

        $existingAvailability = GuideAvailability::where('guide_id', $guide->id)
            ->where('availableDate', $request->availableDate)
            ->first();

        if ($existingAvailability) {
            return response()->json([
                'message' => 'This date is already available for the guide!',
            ], 400);
        }

        $availability = GuideAvailability::create([
            'guide_id' => $guide->id,
            'availableDate' => $request->availableDate,
        ]);

        if ($availability) {
            return response()->json([
                'message' => 'Availability added successfully!',
                'data' => $availability
            ], 201);
        } else {
            return response()->json(['message' => 'Failed to add availability.'], 500);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show Guide Availability :
    public function ShowAvailability()
    {
        $guide = Guides::where('user_id', Auth::id())->with('availabilities')->first();

        if (!$guide) {
            return response()->json(['message' => 'Guide Not Found.'], 404);
        }

        $availabilities = $guide->availabilities()->orderBy('availableDate', 'asc')->get();

        if ($availabilities->isEmpty()) {
            return response()->json(['message' => 'No availability found!'], 404);
        }

        return response()->json([
            'message' => 'Availability retrieved successfully!',
            'data' => $availabilities
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Edit Guide Availability :
    public function updateAvailability(EditGuideAvailabilityRequest $request, $availability_id)
    {
        $availability = GuideAvailability::find($availability_id);

        if (!$availability) {
            return response()->json(['message' => 'No availability found!'], 404);
        }

        $guide = Guides::where('user_id', Auth::id())->first();

        if (!$guide || $availability->guide_id != $guide->id) {
            return response()->json(['message' => 'You are not authorized to update this availability!'], 403);
        }

        if ($request->has('availableDate')) {
            $availability->availableDate = $request->availableDate;
            $availability->save();
        }

        if ($availability->save()) {
            return response()->json([
                'message' => 'Availability updated successfully!',
                'data' => $availability
            ], 200);
        } else {
            return response()->json(['message' => 'Failed to add availability.'], 500);
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Guide Availability :
    public function deleteAvailability($availability_id)
    {
        $availability = GuideAvailability::find($availability_id);

        if (!$availability) {
            return response()->json(['message' => 'No availability found!'], 404);
        }

        $guide = Guides::where('user_id', Auth::id())->first();

        if (!$guide || $availability->guide_id != $guide->id) {
            return response()->json(['message' => 'You are not authorized to update this availability!'], 403);
        }

        if ($availability->delete()) {
            return response()->json(['message' => 'Availability deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Failed to add availability.'], 500);
        }
    }

}
