<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\addAreaToFavoritesRequest;
use App\Http\Responses\Response;
use App\Models\FavoriteUser\FavoriteAreas;
use Illuminate\Support\Facades\Auth;

class UserFavoriteAreaController extends Controller
{

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Area To Favorite :
    public function addAreaToFavorites(addAreaToFavoritesRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
        }

        $area_id=$request->area_id;

        $favorite = FavoriteAreas::where('user_id', $user->id)->where('area_id', $area_id)->first();

        if ($favorite) {
            return response::Message400('Area already in favorites!');
        }

        FavoriteAreas::create([
            'user_id' => $user->id,
            'area_id' => $area_id,
        ]);

        return response::Message('Area added to favorites successfully!',201);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Remove Area From Favorite :
    public function removeAreaFromFavorites($area_id)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
        }

        $favorite = FavoriteAreas::where('user_id', $user->id)->where('area_id', $area_id)->first();

        if (!$favorite) {
            return response::Message404('Area Not Found in favorite');
        }

        $favorite->delete();
        return response::Message('Area removed from favorites successfully!',200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View Favorite Areas :
    public function viewFavoriteAreas()
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
        }

        $favorites = FavoriteAreas::where('user_id', $user->id)->with('area')->get();

        $favoriteAreas = $favorites->map(function($favorite) {
            return [
                'area_id' => $favorite->area->id,
                'AreaName' => $favorite->area->AreaName,
                'Details' => $favorite->area->Details,
            ];
        });

        return response()->json(['favorite_areas' => $favoriteAreas], 200);
    }




}
