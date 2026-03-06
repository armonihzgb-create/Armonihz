<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Send a standard success JSON response.
     *
     * @param mixed  $data
     * @param string|null $message
     * @param int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * Send a standard error JSON response.
     *
     * @param string $message
     * @param mixed  $errors
     * @param int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, $errors = null, int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
