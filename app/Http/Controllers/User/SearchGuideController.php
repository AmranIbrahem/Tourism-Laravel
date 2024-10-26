<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Guides\GuideAvailability;
use App\Models\Guides\Guides;
use App\Models\Places\City;

class SearchGuideController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Search Guide By City :
    public function searchGuidesByCity( $city_name )
    {
        $city = City::where('CityName', $city_name)->first();

        if (!$city) {
            return response()->json(['message' => 'City not found!'], 404);
        }

        $guides = $city->guides()->with('user')->get();

        if ($guides->isEmpty()) {
            return response()->json(['message' => 'No guides found in this city'], 404);
        }

        $data = $guides->map(function ($guide) {
            return [
                'guide_id' => $guide->id,
                'guide_name' => $guide->user->FirstName . ' ' . $guide->user->LastName,
                'city' => $guide->city->CityName,
            ];
        });

        return response()->json([
            'message' => 'Guides retrieved successfully!',
            'data' => $data,
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// get Top 5 Guides By City :
    public function getTop5GuidesByCity( $city_name)
    {
        $city = City::where('CityName', $city_name)->first();

        if (!$city) {
            return response()->json(['message' => 'City not found!'], 404);
        }

        $topGuides = Guides::where('city_id', $city->id)
            ->orderBy('CostPerHour', 'asc')
            ->take(5)
            ->with('user')
            ->get();

        if ($topGuides->isEmpty()) {
            return response()->json(['message' => 'No guides found in this city'], 404);
        }

        $formattedGuides = $topGuides->map(function ($guide) {
            return [
                'idUser'=> $guide->user->id,
                'idGuide' => $guide->id,
                'name' => $guide->user->FirstName . ' ' . $guide->user->LastName,
                'CostPerHour' => $guide->CostPerHour,

            ];
        });

        return response()->json([
            'message' => 'Top 5 guides retrieved successfully!',
            'guides' => $formattedGuides
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// get Top 5 Guides By City :

    public function getTop5GuidesByCityRate($city_name)
    {
        $city = City::where('CityName', $city_name)->first();

        if (!$city) {
            return response()->json(['message' => 'City not found!'], 404);
        }

        $topGuides = Guides::where('city_id', $city->id)
            ->orderBy('rate', 'desc')
            ->take(5)
            ->with('user')
            ->get();

        if ($topGuides->isEmpty()) {
            return response()->json(['message' => 'No guides found in this city'], 404);
        }

        $formattedGuides = $topGuides->map(function ($guide) {
            return [
                'idUser' => $guide->user->id,
                'idGuide' => $guide->id,
                'name' => $guide->user->FirstName . ' ' . $guide->user->LastName,
                'CostPerHour' => $guide->CostPerHour,
                'rate' => $guide->rate,
            ];
        });

        return response()->json([
            'message' => 'Top 5 guides retrieved successfully!',
            'guides' => $formattedGuides
        ], 200);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// search Available Guides :
    public function searchAvailableGuides($city_name, $date, $start_time, $end_time)
    {
        $city = City::where('CityName', $city_name)->first();

        if (!$city) {
            return response()->json(['message' => 'City not found!'], 404);
        }

        $availableGuides = Guides::where('city_id', $city->id)
            ->whereHas('availabilities', function($query) use ($date, $start_time, $end_time) {
                $query->where('availableDate', $date)
                    ->where('isAvailable', true)
                    ->whereHas('timeSlots', function($slotQuery) use ($start_time, $end_time) {
                        $slotQuery->where('startTime', '<=', $start_time)
                            ->where('endTime', '>=', $end_time);
                    });
            })
            ->with('user')
            ->get();

        if ($availableGuides->isEmpty()) {
            return response()->json(['message' => 'No guides available in this city and time period'], 404);
        }

        $formattedGuides = $availableGuides->map(function ($guide) {
            return [
                'idUser' => $guide->user->id,
                'idGuide' => $guide->id,
                'name' => $guide->user->FirstName . ' ' . $guide->user->LastName,
                'CostPerHour' => $guide->CostPerHour,
            ];
        });

        return response()->json([
            'message' => 'Available guides retrieved successfully!',
            'date'=>$date,
            'guides' => $formattedGuides
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// search Available Guides By City And Date :
    public function searchAvailableGuidesByCityAndDate($city_name, $date)
    {
        $city = City::where('CityName', $city_name)->first();

        if (!$city) {
            return response()->json(['message' => 'City not found!'], 404);
        }

        $availableGuides = Guides::where('city_id', $city->id)
            ->whereHas('availabilities', function($query) use ($date) {
                $query->where('availableDate', $date)
                    ->where('isAvailable', true);
            })
            ->with('user')
            ->get();

        if ($availableGuides->isEmpty()) {
            return response()->json(['message' => 'No guides available in this city on the specified date'], 404);
        }

        $formattedGuides = $availableGuides->map(function ($guide) {
            return [
                'idUser' => $guide->user->id,
                'idGuide' => $guide->id,
                'name' => $guide->user->FirstName . ' ' . $guide->user->LastName,
                'CostPerHour' => $guide->CostPerHour,
            ];
        });

        return response()->json([
            'message' => 'Available guides retrieved successfully!',
            'date'=>$date,
            'guides' => $formattedGuides
        ], 200);
    }


    public function getAvailableTimes($guide_id, $date)
    {
        $guideAvailability = GuideAvailability::where('guide_id', $guide_id)
            ->where('availableDate', $date)
            ->with('timeSlots')
            ->first();

        if (!$guideAvailability) {
            return response()->json(['message' => 'No availability found for this guide on the specified date.'], 404);
        }

        $timeSlots = $guideAvailability->timeSlots;

        return response()->json([
            'message' => 'Available time slots retrieved successfully!',
            'timeSlots' => $timeSlots
        ], 200);
    }

}
