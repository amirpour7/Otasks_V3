<?php

namespace App\Traits;

trait ApiResponser
{
    protected function SuccessResponse(int $code, $data, string $message = null)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }
    protected function ErrorResponse(int $code, string $message, $data = null)
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }
}
