<?php

namespace App\Traits;

trait ResponseTrait
{
    protected function success($data)
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    protected function error($message, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
} 