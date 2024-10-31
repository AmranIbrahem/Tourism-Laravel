<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\addReviewAreaRequest;
use App\Http\Responses\Response;
use App\Models\Places\Area;
use App\Models\ReviewsUser\ReviewsAreas;
use Illuminate\Support\Facades\Auth;

class UserReviewsAreasController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Review Guide :
    public function addReviewArea(addReviewAreaRequest $request, $areaId)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }
        $user = Auth::user();

        $existingReview = ReviewsAreas::where('user_id', $user->id)->where('area_id', $areaId)->first();

        if ($existingReview) {
            return Response::Message400('You have already reviewed this Area!');
        }

        $review = ReviewsAreas::create([
            'user_id' => $user->id,
            'area_id' => $areaId,
            'rate' => $request->rate
        ]);

        $this->updateGuideAverageRating($areaId);

        return response()->json(['message' => 'Review added successfully!', 'review' => $review], 201);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show User Reviews:
    public function showUserReviewsArea()
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();

        $reviews = ReviewsAreas::where('user_id', $user->id)
            ->with(['area' => function ($query) {
                $query->select('id', 'AreaName');
            }])
            ->get();

        if ($reviews->isEmpty()) {
            return Response::Message404('You have not added any reviews yet!');
        }

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'review_id' => $review->id,
                'area_id' => $review->area->id,
                'area_name' => $review->area->AreaName,
                'rate' => $review->rate,
            ];
        });

        return response()->json([
            'message' => 'Reviews retrieved successfully!',
            'reviews' => $formattedReviews
        ], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Update Review:
    public function updateReviewArea(addReviewAreaRequest $request, $review_id)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();
        $newRate = $request->input('rate');

        $review = ReviewsAreas::where('id', $review_id)->where('user_id', $user->id)->first();

        if (!$review) {
            return Response::Message404('Review not found or you are not authorized to edit this review!');
        }

        $review->Rate = $newRate;
        $review->save();

        $this->updateGuideAverageRating($review->area_id);

        return response()->json(['message' => 'Review updated successfully!', 'review' => $review], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Review:
    public function deleteReviewArea($review_id)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();

        $review = ReviewsAreas::where('id', $review_id)->where('user_id', $user->id)->first();

        if (!$review) {
            return Response::Message404('Review not found or you are not authorized to delete this review!');
        }

        $review->delete();

        $this->updateGuideAverageRating($review->area_id);

        return Response::Message200('Review deleted successfully!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Update Guide Average Rating :

    public function updateGuideAverageRating($area_id)
    {
        $averageRating = ReviewsAreas::where('area_id', $area_id)->avg('rate');

        $area = Area::find($area_id);
        $area->rate = $averageRating;
        $area->save();
    }
}
