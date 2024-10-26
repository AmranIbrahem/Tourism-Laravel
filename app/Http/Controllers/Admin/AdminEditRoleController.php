<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddGuideRequest;
use App\Http\Requests\Admin\updateGuideCityRequest;
use App\Http\Responses\Response;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use App\Models\Guides\TimeSlots;
use App\Models\Places\City;
use App\Models\User\User;

class AdminEditRoleController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Add guide :
    public function AddGuide(AddGuideRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $city = City::where('CityName', $request->City)->first();

        if (!$user) {
            return Response::Message("User Not Found!", 404);
        }

        if (!$city) {
            return Response::Message("$request->City Not Found", 404);
        }

        if ($user->Role === 'guide' || $user->Role === 'admin' || $user->Role === 'owner') {
            return Response::Message("$user->FirstName cannot be added to the guides", 400);
        }

        $user->Role = "guide";

        $addGuideToCity = Guides::create([
            'user_id' => $user->id,
            'city_id' => $city->id,
            'CostPerHour' => $request->CostPerHour
        ]);

        if ($addGuideToCity && $user->save()) {
            $city->increment('CountOfGuides');

            return Response::Message("Added $user->FirstName to guides", 200);
        } else {
            return Response::Message("Something went wrong!", 500);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Delete guide :
    public function DeleteGuide($email_guide)
    {
        $user = User::where('email', $email_guide)->first();

        if (!$user) {
            return Response::Message("User Not Found!", 404);
        }

        if ($user->Role !== 'guide') {
            return Response::Message("$user->FirstName is not a guide", 400);
        }

        $guide = Guides::where('user_id', $user->id)->first();
        if (!$guide) {
            return Response::Message("Guide not found in cities table", 404);
        }

        $city = City::find($guide->city_id);

        $user->Role = "user";

        if ($user->save() && $guide->delete()) {
            $city->decrement('CountOfGuides');

            return Response::Message("Deleted $user->FirstName from guides", 200);
        } else {
            return Response::Message("Something went wrong!", 500);
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Edit City guide :
    public function updateGuideCity(updateGuideCityRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::Message("User Not Found!", 404);
        }

        if ($user->Role !== 'guide') {
            return Response::Message("$user->FirstName is not a guide!", 400);
        }

        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::Message("Guide Not Found!", 404);
        }

        $newCity = City::where('CityName', $request->city)->first();

        if (!$newCity) {
            return Response::Message("City Not Found!", 404);
        }

        $oldCity = City::find($guide->city_id);

        if (!$oldCity) {
            return Response::Message("Previous city not found!", 404);
        }

        $availabilities = GuideAvailability::where('guide_id', $guide->id)->get();

        foreach ($availabilities as $availability) {
            TimeSlots::where('guide_availabilities_id', $availability->id)->delete();
        }

        GuideAvailability::where('guide_id', $guide->id)->delete();

        $guide->city_id = $newCity->id;

        if ($guide->save()) {
            $oldCity->decrement('CountOfGuides');

            $newCity->increment('CountOfGuides');

            return response()->json([
                'message' => 'Guide city updated successfully, and all related availabilities and timeslots have been deleted!',
                'newCity' => $newCity->CityName
            ], 200);
        } else {
            return Response::Message("Failed to update guide city.", 500);
        }
    }



}
