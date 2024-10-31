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
            return Response::UserNotFound();
        }

        if (!$city) {
            return Response::Message404("$request->City Not Found");
        }

        if ($user->Role === 'guide' || $user->Role === 'admin' || $user->Role === 'owner') {
            return Response::Message400("$user->FirstName cannot be added to the guides");
        }

        $user->Role = "guide";

        $addGuideToCity = Guides::create([
            'user_id' => $user->id,
            'city_id' => $city->id,
            'CostPerHour' => $request->CostPerHour
        ]);

        if ($addGuideToCity && $user->save()) {
            $city->increment('CountOfGuides');

            return Response::Message200("Added $user->FirstName to guides");
        } else {
            return Response::SomethingIsWrong();
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Delete guide :
    public function DeleteGuide($email_guide)
    {
        $user = User::where('email', $email_guide)->first();

        if (!$user) {
            return Response::UserNotFound();
        }

        if ($user->Role !== 'guide') {
            return Response::Message400("$user->FirstName is not a guide");
        }

        $guide = Guides::where('user_id', $user->id)->first();
        if (!$guide) {
            return Response::Message404("Guide not found in cities table");
        }

        $city = City::find($guide->city_id);

        $user->Role = "user";

        if ($user->save() && $guide->delete()) {
            $city->decrement('CountOfGuides');

            return Response::Message200("Deleted $user->FirstName from guides");
        } else {
            return Response::SomethingIsWrong();
        }
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Edit City guide :
    public function updateGuideCity(updateGuideCityRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::UserNotFound();
        }

        if ($user->Role !== 'guide') {
            return Response::Message400("$user->FirstName is not a guide!");
        }

        $guide = Guides::where('user_id', $user->id)->first();

        if (!$guide) {
            return Response::GuideNotFound();
        }

        $newCity = City::where('CityName', $request->city)->first();

        if (!$newCity) {
            return Response::CityNotFound();
        }

        $oldCity = City::find($guide->city_id);

        if (!$oldCity) {
            return Response::PreviousCityNotFound();
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
