<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected function ok(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        $body = ['status' => 'success', 'message' => $message];
        if ($data !== null) {
            $body['data'] = $data;
        }

        return response()->json($body, $code);
    }

    protected function fail(string $message, int $code = 400): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $code);
    }
}
