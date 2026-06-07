<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function refresh()
    {
        try {

            $newToken = auth('admin')->refresh();

            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token cannot be refreshed'
            ], 401);
        }
    }
    public function doctorRefresh()
{
    try {

        $newToken = auth('api')->refresh();

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Token cannot be refreshed'
        ], 401);
    }
}
}
