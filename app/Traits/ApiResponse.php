<?php

namespace App\Traits;

trait ApiResponse
{
    public function __construct()
    {

    }

    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'error' => $message,
            'code' => $code,
        ], $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse([
            'data' => $message,
        ], $code);
    }
}
