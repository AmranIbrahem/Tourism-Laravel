<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class Response
{
    public static function AuthSuccess($message, $data, $token): JsonResponse
    {
        return response()->json([
            "message" => $message,
            'data' => $data,
            'token' => $token
        ], 200);
    }
    public static function logout($success,$message,$stateCode): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message
        ], $stateCode);
    }

    public static function Message($message,$stateCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $stateCode);
    }

    public static function Message200($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ],200);
    }

    public static function Message201($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 201);
    }

    public static function Message400($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 400);
    }

    public static function Message401($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }

    public static function Message403($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 403);
    }
    public static function Message404($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 404);
    }

    public static function Message422($message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 422);
    }


    public static function NotAuthenticated(): JsonResponse
    {
        return response()->json([
            'message' => "User not authenticated!",
        ], 401);
    }


    public static function GuideNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Guide not found!",
        ], 404);
    }

    public static function CountryNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Country not found!",
        ], 404);
    }
    public static function CityNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "City not found!",
        ], 404);
    }

    public static function PreviousCityNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Previous city not found!",
        ], 404);
    }

    public static function AreaNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Area Not Found!",
        ], 404);
    }

    public static function TripNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Trip Not Found!",
        ], 404);
    }

    public static function UserNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "User Not Found!",
        ], 404);
    }

    public static function SubscriptionNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Subscription Not Found!",
        ], 404);
    }

    public static function AvailabilityNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "availability Not Found!",
        ], 404);
    }

    public static function TimeSlotNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "TimeSlot Not Found!",
        ], 404);
    }

    public static function ReportNotFound(): JsonResponse
    {
        return response()->json([
            'message' => "Report Not Found!",
        ], 404);
    }


    public static function NoGuidesFoundInThisCity(): JsonResponse
    {
        return response()->json([
            'message' => "No guides found in this city",
        ], 404);
    }

    public static function ReviewNotFoundOrNotAuthorized(): JsonResponse
    {
        return response()->json([
            'message' => "Review not found or you are not authorized to this review!",
        ], 404);
    }

    public static function SomethingIsWrong(): JsonResponse
    {
        return response()->json([
            'message' => "Something is wrong..!",
        ], 500);
    }

}
