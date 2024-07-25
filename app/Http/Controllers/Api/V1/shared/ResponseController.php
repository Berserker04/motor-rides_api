<?php

namespace App\Http\Controllers\Api\V1\shared;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseController
{
    public static function jsonResponse($data, $message, $status)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ], $status);
    }

    public static function success($data = null, $message = 'success', $status = 200)
    {
        return ResponseController::jsonResponse($data, $message, $status);
    }

    public static function error($message = 'Internal server error', $status = 500, $data = null)
    {
        return ResponseController::jsonResponse($data, $message, $status);
    }
}
