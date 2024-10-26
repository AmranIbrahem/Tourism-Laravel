<?php

namespace App\Http\Responses;

use App\Models\series;
use Illuminate\Http\JsonResponse;

class Response
{
    public static function AuthSuccess($message, $data, $token,$stateCode): JsonResponse
    {
        return response()->json([
            "message" => $message,
            'data' => $data,
            'token' => $token
        ], $stateCode);
    }
    public static function logout($succes,$message,$stateCode): JsonResponse
    {
        return response()->json([
            'success' => $succes,
            'message' => $message
        ], $stateCode);
    }

    public static function Message($message,$stateCode): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $stateCode);
    }



}
