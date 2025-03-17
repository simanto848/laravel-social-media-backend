<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function success($data, $message = "Success", $statusCode = JsonResponse::HTTP_OK) {
        return response()->json([
            'status' => true,
            'message' => $message,
            'error' => null,
            'data' => $data
        ], $statusCode);
    }

    public function error($error, $message="Data is invalid", $statusCode = JsonResponse::HTTP_BAD_REQUEST) {
        return response()->json([
            'status' => false,
            'message' => $message,
            'error' => $error,
            'data' => null
        ], $statusCode);
    }
}
