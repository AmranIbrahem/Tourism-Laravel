<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Responses\Response;
use App\Models\Guides\PrivateGuideBooking;
use App\Models\User\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    /// Create Report :
    public function createReport(Request $request)
    {
        $user = Auth::user();
        $guideId = $request->input('guide_id');

        $bookingExists = PrivateGuideBooking::where('user_id', $user->id)
            ->where('guide_id', $guideId)
            ->where('bookingStatus', 'completed')
            ->exists();

        if (!$bookingExists) {
            return Response::Message403('You cannot submit a report without a completed booking with this guide.');
        }

        Report::create([
            'user_id' => $user->id,
            'guide_id' => $guideId,
            'report_text' => $request->input('report_text'),
            'status' => 'pending',
        ]);

        return Response::Message200('Report submitted successfully.');
    }



}
