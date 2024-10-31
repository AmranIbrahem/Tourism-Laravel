<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\addReviewGuideRequest;
use App\Http\Responses\Response;
use App\Models\Guides\Guides;
use App\Models\Guides\PrivateGuideBooking;
use App\Models\ReviewsUser\ReviewsGuides;
use Illuminate\Support\Facades\Auth;

class UserReviewsGuidesController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Add Review Guide :

    public function addReviewGuide(addReviewGuideRequest $request, $guideId)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();

        $approvedBooking = PrivateGuideBooking::where('user_id', $user->id)
            ->where('guide_id', $guideId)
            ->where('bookingStatus', 'confirmed')
            ->exists();

        if (!$approvedBooking) {
            return Response::Message400('You can only add a review if you have an approved booking with the guide!');
        }

        $existingReview = ReviewsGuides::where('user_id', $user->id)->where('guide_id', $guideId)->first();

        if ($existingReview) {
            return Response::Message400('You have already reviewed this guide!');
        }

        $review = ReviewsGuides::create([
            'user_id' => $user->id,
            'guide_id' => $guideId,
            'Rate' => $request->rate
        ]);

        $this->updateGuideAverageRating($guideId);

        return response()->json(['message' => 'Review added successfully!', 'review' => $review], 201);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Show User Reviews:
    public function showUserReviewsGuide()
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();

        $reviews = ReviewsGuides::where('user_id', $user->id)
            ->with(['guide.user' => function ($query) {
                $query->select('id', 'FirstName', 'LastName');
            }])
            ->get();

        if ($reviews->isEmpty()) {
            return Response::Message404('You have not added any reviews yet!');
        }

        $formattedReviews = $reviews->map(function ($review) {
            return [
                'review_id' => $review->id,
                'guide_id' => $review->guide->id,
                'guide_name' => $review->guide->user->FirstName . ' ' . $review->guide->user->LastName,
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
    public function updateReview(addReviewGuideRequest $request, $review_id)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();
        $newRate = $request->input('rate');

        $review = ReviewsGuides::where('id', $review_id)->where('user_id', $user->id)->first();

        if (!$review) {
            return Response::ReviewNotFoundOrNotAuthorized();
        }

        $review->Rate = $newRate;
        $review->save();

        $this->updateGuideAverageRating($review->guide_id);

        return response()->json(['message' => 'Review updated successfully!', 'review' => $review], 200);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Delete Review:
    public function deleteReview($review_id)
    {
        if (!Auth::check()) {
            return Response::NotAuthenticated();
        }

        $user = Auth::user();

        $review = ReviewsGuides::where('id', $review_id)->where('user_id', $user->id)->first();

        if (!$review) {
            return Response::ReviewNotFoundOrNotAuthorized();
        }

        $review->delete();

        $this->updateGuideAverageRating($review->guide_id);

        return Response::Message200('Review deleted successfully!');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Update Guide Average Rating :

    public function updateGuideAverageRating($guideId)
    {
        $averageRating = ReviewsGuides::where('guide_id', $guideId)->avg('rate');

        $guide = Guides::find($guideId);
        $guide->rate = $averageRating;
        $guide->save();
    }

}

