<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\addGuideToFavoritesRequest;
use App\Http\Responses\Response;
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
            return Response::NotAuthenticated();
        }

        $guide_id=$request->guide_id;

        $favorite = FavoriteGuide::where('user_id', $user->id)->where('guide_id', $guide_id)->first();

        if ($favorite) {
            return Response::Message400('Guide already in favorites!');
        }

        FavoriteGuide::create([
            'user_id' => $user->id,
            'guide_id' => $guide_id,
        ]);

        return Response::Message201('Guide added to favorites successfully!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Remove Guide From Favorite :
    public function removeGuideFromFavorites( $guide_id)
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
        }

        $favorite = FavoriteGuide::where('user_id', $user->id)->where('guide_id', $guide_id)->first();

        if (!$favorite) {
            return Response::Message404('Guide not found in favorites!');
        }

        $favorite->delete();

        return Response::Message200('Guide removed from favorites successfully!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// View Favorite Guide :
    public function viewFavoriteGuides()
    {
        $user = Auth::user();
        if (!$user) {
            return Response::NotAuthenticated();
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
