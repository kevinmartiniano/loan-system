<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Get(
 *     tags={"HealthCheck"},
 *     path="/api/healthcheck",
 *     @OA\Response(response="200", description="A route to healthcheck")
 * )
 */
class IndexController extends Controller
{
    public function index(): JsonResponse
    {
        $message = [
            "message" => "Success!",
        ];

        return response()->json($message);
    }
}
