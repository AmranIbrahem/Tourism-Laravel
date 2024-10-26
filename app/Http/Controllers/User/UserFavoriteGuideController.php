<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\addGuideToFavoritesRequest;
use App\Models\FavoriteUser\FavoriteGuide;
use Illuminate\Support\Facades\Auth;

class UserFavoriteGuideController extends Controller
{

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Guide To Favorite :
    public function addGuideToFavorites(addGuideToFavoritesRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $guide_id=$request->guide_id;

        $favorite = FavoriteGuide::where('user_id', $user->id)->where('guide_id', $guide_id)->first();

        if ($favorite) {
            return response()->json(['message' => 'Guide already in favorites!'], 400);
        }

        FavoriteGuide::create([
            'user_id' => $user->id,
            'guide_id' => $guide_id,
        ]);

        return response()->json(['message' => 'Guide added to favorites successfully!'], 201);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Remove Guide From Favorite :
    public function removeGuideFromFavorites( $guide_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $favorite = FavoriteGuide::where('user_id', $user->id)->where('guide_id', $guide_id)->first();

        if (!$favorite) {
            return response()->json(['message' => 'Guide not found in favorites!'], 404);
        }

        $favorite->delete();


        return response()->json(['message' => 'Guide removed from favorites successfully!'], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View Favorite Guide :
    public function viewFavoriteGuides()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated!'], 401);
        }

        $favorites = FavoriteGuide::where('user_id', $user->id)
            ->with(['guide.user' => function($query) {
                $query->select('id', 'FirstName', 'LastName');
            }])
            ->get();

        $favoriteGuides = $favorites->map(function($favorite) {
            return [
                'id_Guide'=>$favorite->guide->id,
                'id_Guide_User' => $favorite->guide->user->id,
                'GuideName' => $favorite->guide->user->FirstName . ' ' . $favorite->guide->user->LastName,
            ];
        });

        return response()->json(['favorite_guides' => $favoriteGuides], 200);
    }







}
