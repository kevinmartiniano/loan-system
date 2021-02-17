<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
